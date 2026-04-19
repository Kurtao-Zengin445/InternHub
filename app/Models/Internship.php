<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Internship extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_TERMINATED = 'terminated';

    protected $fillable = [
        'application_id',
        'supervisor_id',
        'company_supervisor_id',
        'start_date',
        'end_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(Supervisor::class, 'supervisor_id');
    }

    public function companySupervisor()
    {
        return $this->belongsTo(User::class, 'company_supervisor_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function studentModel()
    {
        return $this->hasOneThrough(
            Student::class,
            Application::class,
            'id',
            'user_id',
            'application_id',
            'student_id'
        );
    }

    public function student()
    {
        return $this->hasOneThrough(
            User::class,
            Application::class,
            'id',
            'id',
            'application_id',
            'student_id'
        );
    }

    public function getStudentIdAttribute(): ?int
    {
        return $this->application?->student_id;
    }

    public function getDurationDaysAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function getAttendancePercentageAttribute(): float
    {
        $totalDays = $this->attendances()->count();
        $presentDays = $this->attendances()->where('status', 'present')->count();

        return $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isOngoing(): bool
    {
        return $this->isActive();
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isTerminated(): bool
    {
        return $this->status === self::STATUS_TERMINATED;
    }

    public function canBeEvaluatedBy(User $user)
    {
        return ($user->isSupervisor() && $user->supervisor?->id === $this->supervisor_id)
            || ($user->isCompany() && $user->company?->id === $this->application?->program?->company_id);
    }

    public function attendancePercentage()
    {
        return $this->attendance_percentage;
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'Sedang Berlangsung',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_TERMINATED => 'Dihentikan',
            default => ucfirst((string) $this->status),
        };
    }

    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'bg-green-500',
            self::STATUS_COMPLETED => 'bg-blue-500',
            self::STATUS_TERMINATED => 'bg-red-500',
            default => 'bg-gray-500',
        };
    }

    public function getTotalDaysAttribute()
    {
        return $this->start_date->diffInWeekdays($this->end_date);
    }

    public function getPassedDaysAttribute()
    {
        if (now()->lte($this->start_date)) {
            return 0;
        }

        return min($this->total_days, $this->start_date->diffInWeekdays(min(now(), $this->end_date)));
    }

    public function getProgressPercentAttribute()
    {
        if ($this->total_days === 0) {
            return 0;
        }

        return min(100, round(($this->passed_days / $this->total_days) * 100));
    }

    public function hasBeenEvaluatedBy(User $user)
    {
        $type = $user->isSupervisor() ? 'supervisor' : 'company';

        return $this->evaluations()->where('evaluator_type', $type)->exists();
    }

    public function isTodayCheckIn()
    {
        return $this->attendances()->whereDate('date', today())->exists();
    }

    public function todayAttendance()
    {
        return $this->attendances()->whereDate('date', today())->first();
    }

    public function canBeCompleted()
    {
        return $this->isActive() && now()->gte($this->end_date);
    }

    public function getAverageScoreAttribute()
    {
        return $this->evaluations()->avg('final_score');
    }

    public function getGradeAttribute()
    {
        $score = $this->average_score;

        if (!$score) {
            return '-';
        }

        if ($score >= 85) {
            return 'A';
        }
        if ($score >= 75) {
            return 'B';
        }
        if ($score >= 65) {
            return 'C';
        }
        if ($score >= 55) {
            return 'D';
        }

        return 'E';
    }

    public function progressPercent()
    {
        return $this->progress_percent;
    }

    public function daysPassed()
    {
        return $this->passed_days;
    }

    public function totalDays()
    {
        return $this->total_days;
    }

    public function daysRemaining()
    {
        return max(0, $this->total_days - $this->passed_days);
    }

    public function statusLabel()
    {
        return $this->status_label;
    }

    public function statusBadgeClass()
    {
        return $this->status_badge_class;
    }

    public function averageScore()
    {
        return $this->average_score;
    }

    public function grade()
    {
        return $this->grade;
    }

    public function durationDays()
    {
        return $this->duration_days;
    }

    public function supervisorEvaluation()
    {
        return $this->evaluations()->where('evaluator_type', 'supervisor')->latest()->first();
    }

    public function companyEvaluation()
    {
        return $this->evaluations()->where('evaluator_type', 'company')->latest()->first();
    }

    public function finalScore()
    {
        $supervisorScore = $this->supervisorEvaluation()?->final_score ?? 0;
        $companyScore = $this->companyEvaluation()?->final_score ?? 0;

        if ($supervisorScore > 0 && $companyScore > 0) {
            return round(($supervisorScore + $companyScore) / 2, 2);
        }

        return $supervisorScore > 0 ? $supervisorScore : $companyScore;
    }
}

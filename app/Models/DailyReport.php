<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'internship_id',
        'report_date',
        'activity',
        'problems',
        'solutions',
        'photo',
        'status',
        'feedback',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'report_date'  => 'date',
            'submitted_at' => 'datetime',
        ];
    }

    // ─── Relationships ─────────────────────────────────────

    public function internship()
    {
        return $this->belongsTo(Internship::class);
    }

    public function student()
    {
        return $this->hasOneThrough(
            Student::class,
            Internship::class,
            'id',           // Foreign key on internships table
            'user_id',      // Foreign key on students table
            'internship_id', // Local key on daily_reports table
            'student_id'    // Local key on internships table
        );
    }

    // ─── Helper ────────────────────────────────────────────

    public function isDraft(): bool     { return $this->status === 'draft'; }
    public function isSubmitted(): bool { return $this->status === 'submitted'; }
    public function isApproved(): bool  { return $this->status === 'approved'; }
    public function needsRevision(): bool { return $this->status === 'revision'; }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'draft' => 'Draf',
            'submitted' => 'Dikirim',
            'approved' => 'Disetujui',
            'revision' => 'Perlu Revisi',
            'rejected' => 'Ditolak',
            default => ucfirst($this->status)
        };
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'draft' => 'bg-gray-500',
            'submitted' => 'bg-yellow-500',
            'approved' => 'bg-green-500',
            'revision' => 'bg-orange-500',
            'rejected' => 'bg-red-500',
            default => 'bg-gray-500'
        };
    }

    public function canBeEdited()
    {
        return $this->isDraft() || $this->needsRevision();
    }

    public function canBeReviewed()
    {
        return $this->isSubmitted();
    }

    public function getReportDateFormattedAttribute()
    {
        return $this->report_date->isoFormat('dddd, D MMMM Y');
    }

    public function getSubmittedAtFormattedAttribute()
    {
        return $this->submitted_at?->format('d M Y H:i') ?? '-';
    }

    public function isLate()
    {
        return $this->submitted_at && $this->submitted_at->gt($this->report_date->setTime(23, 59, 59));
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_STUDENT = 'student';
    public const ROLE_SCHOOL = 'school';
    public const ROLE_SUPERVISOR = 'supervisor';
    public const ROLE_COMPANY = 'company';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'google_id',
        'avatar',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isStudent(): bool
    {
        return $this->role === self::ROLE_STUDENT;
    }

    public function isSchool(): bool
    {
        return $this->role === self::ROLE_SCHOOL;
    }

    public function isSupervisor(): bool
    {
        return $this->role === self::ROLE_SUPERVISOR;
    }

    public function isCompany(): bool
    {
        return $this->role === self::ROLE_COMPANY;
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function schoolProfile()
    {
        return $this->hasOne(School::class);
    }

    public function supervisor()
    {
        return $this->hasOne(Supervisor::class);
    }

    public function company()
    {
        return $this->hasOne(Company::class, 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function internship()
    {
        return $this->hasOne(Internship::class, 'student_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'student_id');
    }

    public function supervisedInternships()
    {
        return $this->hasMany(Internship::class, 'supervisor_id');
    }

    public function companyInternships()
    {
        return $this->hasManyThrough(Internship::class, Company::class, 'user_id', 'company_id');
    }

    public function school()
    {
        return $this->hasOneThrough(School::class, Student::class, 'user_id', 'id', 'id', 'school_id');
    }

    public function hasCompletedRoleProfile(): bool
    {
        return match ($this->role) {
            self::ROLE_STUDENT => $this->student()->exists(),
            self::ROLE_SCHOOL => $this->schoolProfile()->exists(),
            self::ROLE_SUPERVISOR => $this->supervisor()->exists(),
            self::ROLE_COMPANY => $this->company()->exists(),
            self::ROLE_ADMIN => true,
            default => true,
        };
    }

    public function hasPendingGoogleRegistration(): bool
    {
        return !empty($this->google_id) && !$this->hasCompletedRoleProfile();
    }

    public function profilePhotoUrl(): ?string
    {
        if (!$this->avatar) {
            return null;
        }

        if (Str::startsWith($this->avatar, ['http://', 'https://'])) {
            return $this->avatar;
        }

        return Storage::disk('public')->url($this->avatar);
    }

    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn (string $part) => Str::upper(Str::substr($part, 0, 1)))
            ->implode('');
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'Administrator',
            self::ROLE_STUDENT => 'Siswa Magang',
            self::ROLE_SUPERVISOR => 'Pembimbing Sekolah',
            self::ROLE_COMPANY => 'Perusahaan',
            self::ROLE_SCHOOL => 'Sekolah',
            default => ucfirst((string) $this->role),
        };
    }

    public function getRoleBadgeClassAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'bg-red-500',
            self::ROLE_STUDENT => 'bg-blue-500',
            self::ROLE_SUPERVISOR => 'bg-green-500',
            self::ROLE_COMPANY => 'bg-purple-500',
            self::ROLE_SCHOOL => 'bg-yellow-500',
            default => 'bg-gray-500',
        };
    }

    public function hasActiveInternship(): bool
    {
        return $this->student && $this->student->activeInternship() !== null;
    }

    public function hasPendingApplications(): bool
    {
        return $this->applications()->where('status', 'pending')->exists();
    }

    public function canApplyForPrograms(): bool
    {
        return $this->isStudent() && !$this->hasActiveInternship();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function getSchoolNameAttribute(): string
    {
        return $this->school?->name ?? '-';
    }

    public function getMajorAttribute(): string
    {
        return $this->student?->major ?? '-';
    }
}

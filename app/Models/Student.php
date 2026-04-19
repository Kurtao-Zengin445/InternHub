<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'school_id',
        'nis',
        'class',
        'major',
        'gender',
        'birth_date',
        'birth_place',
        'phone',
        'address',
        'photo',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'student_id', 'user_id');
    }

    public function internships()
    {
        return $this->hasManyThrough(
            Internship::class,
            Application::class,
            'student_id',
            'application_id',
            'user_id',
            'id'
        );
    }

    public function activeInternship()
    {
        return $this->internships()
            ->where('internships.status', 'active')
            ->where('internships.start_date', '<=', now())
            ->where('internships.end_date', '>=', now())
            ->latest('internships.created_at')
            ->first();
    }

    public function getNameAttribute(): string
    {
        return $this->user?->name ?? 'Siswa';
    }

    public function getEmailAttribute(): ?string
    {
        return $this->user?->email;
    }

    public function getSchoolNameAttribute()
    {
        return $this->school?->name ?? '-';
    }

    public function getMajorNameAttribute()
    {
        return $this->major ?? '-';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'address',
        'phone',
        'email',
        'contact_person',
        'contact_person_phone',
        'description',
        'industry',
        'logo',
        'website',
        'latitude',
        'longitude',
        'allowed_radius',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'allowed_radius' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function programs()
    {
        return $this->hasMany(InternshipProgram::class);
    }

    public function applications()
    {
        return $this->hasManyThrough(
            Application::class,
            InternshipProgram::class,
            'company_id',
            'internship_program_id',
            'id',
            'id'
        );
    }

    public function getActiveInternshipsCountAttribute(): int
    {
        return Internship::whereHas('application.program', function ($query) {
            $query->where('company_id', $this->id);
        })->where('status', 'active')->count();
    }

    public function getCompletedInternshipsCountAttribute(): int
    {
        return Internship::whereHas('application.program', function ($query) {
            $query->where('company_id', $this->id);
        })->where('status', 'completed')->count();
    }

    public function getStatusLabelAttribute(): string
    {
        return 'Aktif';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return 'bg-green-500';
    }
}

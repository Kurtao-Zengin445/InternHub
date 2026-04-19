<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternshipProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'title',
        'description',
        'requirements',
        'quota',
        'start_date',
        'end_date',
        'registration_start',
        'registration_end',
        'field',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date'         => 'date',
            'end_date'           => 'date',
            'registration_start' => 'date',
            'registration_end'   => 'date',
        ];
    }

    // ─── Relationships ─────────────────────────────────────

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function acceptedApplications()
    {
        return $this->hasMany(Application::class)->where('status', 'accepted');
    }

    // ─── Helper ────────────────────────────────────────────

    public function remainingQuota(): int
    {
        return $this->quota - $this->acceptedApplications()->count();
    }

    public function isOpen(): bool
    {
        return $this->status === 'open'
            && now()->between($this->registration_start, $this->registration_end);
    }
}
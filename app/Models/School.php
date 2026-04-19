<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'address',
        'phone',
        'email',
        'principal_name',
        'npsn',
        'school_type',
        'logo',
    ];

    // ─── Relationships ─────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function supervisors()
    {
        return $this->hasMany(Supervisor::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
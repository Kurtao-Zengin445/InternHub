<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supervisor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'school_id',
        'nip',
        'position',
        'phone',
        'photo',
    ];

    // ─── Relationships ─────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function internships()
    {
        return $this->hasMany(Internship::class);
    }

    public function evaluations()
    {
        return $this->hasManyThrough(
            Evaluation::class,
            Internship::class,
            'supervisor_id',  // FK di internships
            'internship_id'   // FK di evaluations
        );
    }
}
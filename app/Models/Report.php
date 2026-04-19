<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    const TYPE_DAILY = 'daily';
    const TYPE_WEEKLY = 'weekly';
    const TYPE_FINAL = 'final';

    protected $fillable = [
        'internship_id',
        'type',
        'title',
        'content',
        'report_date',
        'week_number',
        'file_path',
        'status',
        'feedback',
        'submitted_at',
        'reviewed_at',
    ];

    protected $casts = [
        'report_date' => 'date',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function internship()
    {
        return $this->belongsTo(Internship::class);
    }

    public function isDaily()
    {
        return $this->type === self::TYPE_DAILY;
    }

    public function isWeekly()
    {
        return $this->type === self::TYPE_WEEKLY;
    }

    public function isFinal()
    {
        return $this->type === self::TYPE_FINAL;
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function approve($feedback = null)
    {
        $this->update([
            'status' => 'approved',
            'feedback' => $feedback,
            'reviewed_at' => now(),
        ]);
    }

    public function reject($feedback)
    {
        $this->update([
            'status' => 'rejected',
            'feedback' => $feedback,
            'reviewed_at' => now(),
        ]);
    }
}

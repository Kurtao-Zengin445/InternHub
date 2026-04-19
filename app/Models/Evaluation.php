<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    const TYPE_SUPERVISOR = 'supervisor';
    const TYPE_COMPANY = 'company';
    const TYPE_SCHOOL = 'school';

    protected $fillable = [
        'internship_id',
        'evaluator_id',
        'evaluator_type',
        'discipline_score',
        'skill_score',
        'attitude_score',
        'knowledge_score',
        'communication_score',
        'final_score',
        'grade',
        'strengths',
        'improvements',
        'notes',
        'evaluated_at',
    ];

    protected $casts = [
        'evaluated_at' => 'datetime',
    ];

    public function internship()
    {
        return $this->belongsTo(Internship::class);
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    protected static function booted()
    {
        static::saving(function ($evaluation) {
            $evaluation->final_score = collect([
                $evaluation->attitude_score,
                $evaluation->knowledge_score,
                $evaluation->skill_score,
                $evaluation->discipline_score,
                $evaluation->communication_score,
            ])->average();
            
            $evaluation->grade = match (true) {
                $evaluation->final_score >= 85 => 'A',
                $evaluation->final_score >= 75 => 'B',
                $evaluation->final_score >= 65 => 'C',
                $evaluation->final_score >= 55 => 'D',
                default => 'E',
            };
        });
    }

    public function getGradeLetterAttribute()
    {
        return match (true) {
            $this->final_score >= 85 => 'A',
            $this->final_score >= 75 => 'B',
            $this->final_score >= 65 => 'C',
            $this->final_score >= 55 => 'D',
            default => 'E',
        };
    }

    public function isSupervisorEvaluation()
    {
        return $this->evaluator_type === self::TYPE_SUPERVISOR;
    }

    public function isCompanyEvaluation()
    {
        return $this->evaluator_type === self::TYPE_COMPANY;
    }

    public function isSchoolEvaluation()
    {
        return $this->evaluator_type === self::TYPE_SCHOOL;
    }

    public function getTypeLabelAttribute()
    {
        return match($this->evaluator_type) {
            self::TYPE_SUPERVISOR => 'Pembimbing Sekolah',
            self::TYPE_COMPANY => 'Pembimbing Perusahaan',
            self::TYPE_SCHOOL => 'Sekolah',
            default => 'Tidak Diketahui',
        };
    }

    public function getEvaluatedAtFormattedAttribute()
    {
        return $this->evaluated_at?->format('d M Y') ?? '-';
    }

    public function saveWithCalculation()
    {
        return $this->save();
    }
}


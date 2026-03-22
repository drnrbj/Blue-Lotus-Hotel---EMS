<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $fillable = [
        'employee_id',
        'evaluator_id',
        'period',
        'score',
        'remarks',
        'criteria',
        'status',
    ];

    protected $casts = [
        'score'    => 'decimal:2',
        'criteria' => 'array', // JSON — stores per-category scores
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function evaluator()
    {
        return $this->belongsTo(\App\Models\User::class, 'evaluator_id');
    }

    // Rating label based on score (0–100)
    public function getRatingLabelAttribute(): string
    {
        return match(true) {
            $this->score >= 90 => 'Outstanding',
            $this->score >= 75 => 'Exceeds Expectations',
            $this->score >= 60 => 'Meets Expectations',
            $this->score >= 45 => 'Needs Improvement',
            default            => 'Unsatisfactory',
        };
    }

    public function getRatingColorAttribute(): string
    {
        return match(true) {
            $this->score >= 90 => 'bg-emerald-100 text-emerald-700',
            $this->score >= 75 => 'bg-green-100 text-green-700',
            $this->score >= 60 => 'bg-blue-100 text-blue-700',
            $this->score >= 45 => 'bg-amber-100 text-amber-700',
            default            => 'bg-red-100 text-red-600',
        };
    }
}
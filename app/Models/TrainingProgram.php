<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingProgram extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'program_name',
        'department_id',
        'description',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function participants()
    {
        return $this->hasMany(TrainingParticipant::class, 'training_id');
    }

    public function getDurationAttribute(): string
    {
        $days = $this->start_date->diffInDays($this->end_date) + 1;
        return $days === 1 ? '1 day' : "{$days} days";
    }
}
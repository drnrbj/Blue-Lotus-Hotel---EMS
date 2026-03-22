<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingParticipant extends Model
{
    public $timestamps = false;

    protected $table = 'training_participants';

    protected $fillable = [
        'training_id',
        'employee_id',
        'status',
    ];

    public function training()
    {
        return $this->belongsTo(TrainingProgram::class, 'training_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'applicant_id',
        'interviewer_id',
        'schedule_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'schedule_date' => 'datetime',
    ];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

    public function interviewer()
    {
        return $this->belongsTo(\App\Models\User::class, 'interviewer_id');
    }
}
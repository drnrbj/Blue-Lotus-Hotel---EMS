<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    public $timestamps = false;

    protected $table = 'attendance';

    protected $fillable = [
        'employee_id',
        'date',
        'time_in',
        'time_out',
        'hours_worked',
        'status',
    ];

    protected $casts = [
        'date'         => 'date',
        'hours_worked' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
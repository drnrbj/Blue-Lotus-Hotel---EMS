<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'employee_id',
        'shift_start',
        'shift_end',
        'days',
        'effective_date',
        'status',
    ];

    protected $casts = [
        'effective_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getDaysArrayAttribute(): array
    {
        return explode(',', $this->days);
    }
}
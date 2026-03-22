<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $fillable = [
        'employee_id',
        'period_start',
        'period_end',
        'total_hours',
        'basic_pay',
        'gross_pay',
        'deductions',
        'net_pay',
        'status',
        'released_at',
        'released_by',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end'   => 'date',
        'total_hours'  => 'decimal:2',
        'basic_pay'    => 'decimal:2',
        'gross_pay'    => 'decimal:2',
        'deductions'   => 'decimal:2',
        'net_pay'      => 'decimal:2',
        'released_at'  => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function adjustments()
    {
        return $this->hasMany(PayrollAdjustment::class);
    }

    public function releasedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'released_by');
    }

    public function getPeriodLabelAttribute(): string
    {
        return $this->period_start->format('M d') . ' – ' . $this->period_end->format('M d, Y');
    }

    public function getTotalBonusesAttribute(): float
    {
        return (float) $this->adjustments->where('type', 'bonus')->sum('amount');
    }

    public function getTotalDeductionAdjustmentsAttribute(): float
    {
        return (float) $this->adjustments->where('type', 'deduction')->sum('amount');
    }
}
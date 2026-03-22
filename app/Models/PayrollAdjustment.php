<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollAdjustment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'payroll_id',
        'type',
        'amount',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }
}
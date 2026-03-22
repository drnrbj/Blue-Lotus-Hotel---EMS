<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_code',
        'first_name',
        'last_name',
        'department_id',
        'position',
        'date_started',
        'employment_type',
        'date_of_birth',
        'email',
        'phone_number',
        'address',
        'manager_id',
        'status',
        'last_working_day',
        'termination_reason',
        'terminated_at',
    ];

    protected $casts = [
        'date_started'    => 'date',
        'date_of_birth'   => 'date',
        'last_working_day'=> 'date',
        'terminated_at'   => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    // ── Accessors ──────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getInitialsAttribute(): string
    {
        return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
    }

    // ── Scopes ─────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }
}
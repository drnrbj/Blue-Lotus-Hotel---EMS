<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobPosting extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'job_title',
        'department_id',
        'slots',
        'posting_date',
        'deadline',
        'status',
        'description',
    ];

    protected $casts = [
        'posting_date' => 'date',
        'deadline'     => 'date',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function applicants()
    {
        return $this->hasMany(Applicant::class);
    }
}
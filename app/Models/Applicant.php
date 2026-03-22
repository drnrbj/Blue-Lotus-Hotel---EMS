<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'job_posting_id',
        'name',
        'email',
        'phone',
        'applied_position',
        'applied_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'applied_date' => 'date',
    ];

    public function jobPosting()
    {
        return $this->belongsTo(JobPosting::class);
    }

    public function interviews()
    {
        return $this->hasMany(Interview::class);
    }

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        return strtoupper(substr($words[0] ?? '', 0, 1) . substr($words[1] ?? '', 0, 1));
    }

    public static function avatarColor(int $id): string
    {
        $colors = ['bg-blue-500','bg-emerald-500','bg-violet-500','bg-amber-500','bg-rose-500','bg-teal-500','bg-sky-500','bg-indigo-500'];
        return $colors[$id % count($colors)];
    }
}
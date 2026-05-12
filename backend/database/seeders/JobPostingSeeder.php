<?php

namespace Database\Seeders;

use App\Models\JobPosting;
use App\Models\Applicant;
use App\Models\Interview;
use App\Models\User;
use Illuminate\Database\Seeder;

class JobPostingSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::where('role', 'Admin')->value('id') ?? 1;
        $hrId = User::where('role', 'HR')->value('id') ?? 2;

        // ── Job Postings ──────────────────────────────────────────────────
        $postings = [
            [
                'title' => 'Front Desk Receptionist',
                'department' => 'Front Office',
                'job_category' => 'Front Desk Agent',
                'description' => 'Handle guest check-ins, manage reservations, and provide excellent customer service at the front desk.',
                'slots' => 2,
                'status' => 'open',
                'created_by' => $adminId,
                'posted_date' => now()->subDays(10)->toDateString(),
                'deadline' => now()->addDays(20)->toDateString(),
            ],
            [
                'title' => 'Housekeeping Supervisor',
                'department' => 'Housekeeping',
                'job_category' => 'Housekeeping Supervisor',
                'description' => 'Oversee housekeeping staff, maintain cleanliness standards, and manage room assignments.',
                'slots' => 1,
                'status' => 'open',
                'created_by' => $adminId,
                'posted_date' => now()->subDays(15)->toDateString(),
                'deadline' => now()->addDays(15)->toDateString(),
            ],
            [
                'title' => 'Kitchen Assistant',
                'department' => 'Food & Beverage',
                'job_category' => 'Kitchen Steward',
                'description' => 'Assist in food preparation, maintain kitchen cleanliness, and support cooking operations.',
                'slots' => 3,
                'status' => 'open',
                'created_by' => $adminId,
                'posted_date' => now()->subDays(5)->toDateString(),
                'deadline' => now()->addDays(25)->toDateString(),
            ],
            [
                'title' => 'Maintenance Technician',
                'department' => 'Maintenance',
                'job_category' => 'Maintenance Technician',
                'description' => 'Perform routine maintenance, repairs, and ensure all hotel facilities are in working order.',
                'slots' => 1,
                'status' => 'open',
                'created_by' => $adminId,
                'posted_date' => now()->subDays(8)->toDateString(),
                'deadline' => now()->addDays(22)->toDateString(),
            ],
            [
                'title' => 'Sales Manager',
                'department' => 'Sales & Marketing',
                'job_category' => 'Sales Manager',
                'description' => 'Coordinate corporate accounts, drive revenue targets, and manage key client relationships.',
                'slots' => 1,
                'status' => 'open',
                'created_by' => $adminId,
                'posted_date' => now()->subDays(3)->toDateString(),
                'deadline' => now()->addDays(27)->toDateString(),
            ],
            [
                'title' => 'Bartender',
                'department' => 'Food & Beverage',
                'job_category' => 'Bartender',
                'description' => 'Prepare and serve beverages, interact with guests, and maintain bar cleanliness.',
                'slots' => 1,
                'status' => 'open',
                'created_by' => $adminId,
                'posted_date' => now()->subDays(4)->toDateString(),
                'deadline' => now()->addDays(26)->toDateString(),
            ],
            [
                'title' => 'HR Assistant',
                'department' => 'Administration',
                'job_category' => 'HR Officer',
                'description' => 'Assist HR in recruitment, onboarding, and employee records management.',
                'slots' => 1,
                'status' => 'open',
                'created_by' => $adminId,
                'posted_date' => now()->subDays(6)->toDateString(),
                'deadline' => now()->addDays(24)->toDateString(),
            ],
        ];

        foreach ($postings as $data) {
            JobPosting::updateOrCreate(
                ['title' => $data['title'], 'department' => $data['department']],
                $data
            );
        }

        // ── Applicants ────────────────────────────────────────────────────
        $applicants = [
            [
                'job_posting_id' => JobPosting::where('title', 'Front Desk Receptionist')->value('id'),
                'first_name' => 'April Bords',
                'last_name' => 'Nerosa',
                'email' => 'a.nerosa.545679@umindanao.edu.ph',
                'phone' => '09171231001',
                'pipeline_stage' => 'applied',
                'notes' => 'Good communication skills, for initial review.',
            ],
            [
                'job_posting_id' => JobPosting::where('title', 'Kitchen Assistant')->value('id'),
                'first_name' => 'Gabriel Joshua',
                'last_name' => 'Regidor',
                'email' => 'g.regidor.548909@umindanao.edu.ph',
                'phone' => '09171231002',
                'pipeline_stage' => 'interview_scheduled',
                'notes' => 'Scheduled for interview, promising background.',
            ],
            [
                'job_posting_id' => JobPosting::where('title', 'Sales Manager')->value('id'),
                'first_name' => 'Charish',
                'last_name' => 'Pulido',
                'email' => 'c.pulido.544201@umindanao.edu.ph',
                'phone' => '09171231003',
                'pipeline_stage' => 'reviewed',
                'notes' => 'Has sales experience, under evaluation.',
            ],
        ];

        foreach ($applicants as $data) {
            if (!$data['job_posting_id'])
                continue;
            Applicant::updateOrCreate(['email' => $data['email']], $data);
        }

        // ── Interviews ────────────────────────────────────────────────────
        $gabrielId = Applicant::where('email', 'g.regidor.548909@umindanao.edu.ph')->value('id');

        if ($gabrielId) {
            Interview::updateOrCreate(
                ['applicant_id' => $gabrielId],
                [
                    'applicant_id' => $gabrielId,
                    'interviewer_id' => $hrId,
                    'scheduled_at' => now()->addDays(1)->setTime(10, 0),
                    'status' => 'scheduled',
                    'feedback' => null,
                ]
            );
        }

        $this->command->info('✓ Job postings, applicants, and interviews seeded!');
    }
}
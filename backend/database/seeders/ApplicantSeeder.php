<?php
// database/seeders/ApplicantSeeder.php

namespace Database\Seeders;

use App\Models\Applicant;
use App\Models\JobPosting;
use Illuminate\Database\Seeder;

class ApplicantSeeder extends Seeder
{
    public function run(): void
    {
        // Get first job posting or create one
        $jobPosting = JobPosting::first();

        if (!$jobPosting) {
            $jobPosting = JobPosting::create([
                'title' => 'Front Desk Agent',
                'department' => 'Front Office',
                'job_category' => 'Staff',
                'description' => 'Responsible for greeting guests and checking them in.',
                'status' => 'open',
                'created_by' => 1,
            ]);
        }

        $applicants = [
            [
                'first_name' => 'Bai Fatima',
                'last_name' => 'Andong',
                'email' => 'b.andong.545438@umindanao.edu.ph',
                'phone' => '09100000001',
                'job_posting_id' => $jobPosting->id,
                'pipeline_stage' => 'hired',
                'notes' => '',
            ],
            [
                'first_name' => 'Robert Jhon',
                'last_name' => 'Aracena',
                'email' => 'r.aracena.545985@umindanao.edu.ph',
                'phone' => '09100000002',
                'job_posting_id' => $jobPosting->id,
                'pipeline_stage' => 'hired',
                'notes' => '',
            ],
            [
                'first_name' => 'Nino',
                'last_name' => 'Asan',
                'email' => 'n.asan.546681@umindanao.edu.ph',
                'phone' => '09100000003',
                'job_posting_id' => $jobPosting->id,
                'pipeline_stage' => 'interviewed',
                'notes' => '',
            ],
            [
                'first_name' => 'John Benedict',
                'last_name' => 'Bongcac',
                'email' => 'j.bongcac.543497@umindanao.edu.ph',
                'phone' => '09100000004',
                'job_posting_id' => $jobPosting->id,
                'pipeline_stage' => 'hired',
                'notes' => '',
            ],
            [
                'first_name' => 'Joana',
                'last_name' => 'Bravo',
                'email' => 'j.bravo.546336@umindanao.edu.ph',
                'phone' => '09100000005',
                'job_posting_id' => $jobPosting->id,
                'pipeline_stage' => 'interview_scheduled',
                'notes' => '',
            ],
            [
                'first_name' => 'Joanne Faith',
                'last_name' => 'Cabarde',
                'email' => 'j.cabarde.548077@umindanao.edu.ph',
                'phone' => '09100000006',
                'job_posting_id' => $jobPosting->id,
                'pipeline_stage' => 'interview_scheduled',
                'notes' => '',
            ],
        ];

        foreach ($applicants as $data) {
            Applicant::updateOrCreate(
                ['email' => $data['email']],
                $data
            );
        }

        $this->command->info('✓ ' . count($applicants) . ' applicant records seeded successfully!');
        $this->command->info('  - Hired applicants: ' . Applicant::where('pipeline_stage', 'hired')->count());
        $this->command->info('  - Interviewed: ' . Applicant::where('pipeline_stage', 'interviewed')->count());
        $this->command->info('  - Interview Scheduled: ' . Applicant::where('pipeline_stage', 'interview_scheduled')->count());
    }
}
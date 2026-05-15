<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    /**
     * Seeds realistic employees for Blue Lotus Hotel HR system.
     * Run with: php artisan db:seed --class=EmployeeSeeder
     *
     * Also creates matching User accounts for each employee so they can log in.
     */
    public function run(): void
    {
        $employees = [

            // ── HR DEPARTMENT (Morning Shift) ─────────────────────────────
            [
                'employee' => [
                    'role' => 'HR',
                    'status' => 'active',
                    'first_name' => 'Hazeljoy',
                    'last_name' => 'Hingpit',
                    'date_of_birth' => '1998-01-01',
                    'email' => 'h.hingpit.545666@umindanao.edu.ph',
                    'phone_number' => '09170000001',
                    'home_address' => 'Davao City',
                    'start_date' => '2024-01-01',
                    'department' => 'Human Resources',
                    'job_category' => 'HR Manager',
                    'shift_sched' => 'Morning',
                    'employment_type' => 'regular',
                    'basic_salary' => 35000,
                ],
                'password' => 'hr123',
            ],
            [
                'employee' => [
                    'role' => 'HR',
                    'status' => 'active',
                    'first_name' => 'Kiarra Mae',
                    'last_name' => 'Guradillo',
                    'date_of_birth' => '1999-01-01',
                    'email' => 'k.guradillo.548474@umindanao.edu.ph',
                    'phone_number' => '09170000002',
                    'home_address' => 'Davao City',
                    'start_date' => '2024-01-01',
                    'department' => 'Human Resources',
                    'job_category' => 'Recruitment Specialist',
                    'shift_sched' => 'Morning',
                    'employment_type' => 'regular',
                    'basic_salary' => 28000,
                ],
                'password' => 'hr123',
            ],

            // ── ADMIN / GENERAL MANAGEMENT (Morning Shift) ──────────────────────────
            [
                'employee' => [
                    'role' => 'Admin',
                    'status' => 'active',
                    'first_name' => 'Angelo',
                    'last_name' => 'Lozano',
                    'date_of_birth' => '1997-01-01',
                    'email' => 'a.lozano.547237@umindanao.edu.ph',
                    'phone_number' => '09170000003',
                    'home_address' => 'Davao City',
                    'start_date' => '2024-01-01',
                    'department' => 'Executive Office',
                    'job_category' => 'General Manager',
                    'shift_sched' => 'Morning',
                    'employment_type' => 'regular',
                    'basic_salary' => 80000,
                ],
                'password' => 'admin123',
            ],
            [
                'employee' => [
                    'role' => 'Admin',
                    'status' => 'active',
                    'first_name' => 'Mark Jade',
                    'last_name' => 'Palma',
                    'date_of_birth' => '1996-01-01',
                    'email' => 'm.palma.546616@umindanao.edu.ph',
                    'phone_number' => '09170000004',
                    'home_address' => 'Davao City',
                    'start_date' => '2024-01-01',
                    'department' => 'Executive Office',
                    'job_category' => 'Assistant General Manager',
                    'shift_sched' => 'Morning',
                    'employment_type' => 'regular',
                    'basic_salary' => 55000,
                ],
                'password' => 'admin123',
            ],

            // ── ACCOUNTING & FINANCE (Morning Shift) ─────────────────────
            [
                'employee' => [
                    'role' => 'Accountant',
                    'status' => 'active',
                    'first_name' => 'Princess Mae',
                    'last_name' => 'Planas',
                    'date_of_birth' => '1998-01-01',
                    'email' => 'p.planas.548892@umindanao.edu.ph',
                    'phone_number' => '09170000005',
                    'home_address' => 'Davao City',
                    'start_date' => '2024-01-01',
                    'department' => 'Accounting & Finance',
                    'job_category' => 'Accounting Staff',
                    'shift_sched' => 'Morning',
                    'employment_type' => 'regular',
                    'basic_salary' => 27000,
                ],
                'password' => 'acc123',
            ],
            [
                'employee' => [
                    'role' => 'Accountant',
                    'status' => 'active',
                    'first_name' => 'John Llorie',
                    'last_name' => 'Sarmiento',
                    'date_of_birth' => '1997-01-01',
                    'email' => 'j.sarmiento.545495@umindanao.edu.ph',
                    'phone_number' => '09170000006',
                    'home_address' => 'Davao City',
                    'start_date' => '2024-01-01',
                    'department' => 'Accounting & Finance',
                    'job_category' => 'Payroll Officer',
                    'shift_sched' => 'Morning',
                    'employment_type' => 'regular',
                    'basic_salary' => 28000,
                ],
                'password' => 'acc123',
            ],

            // ── FRONT OFFICE DEPARTMENT (Mixed Shifts - 24/7 operation) ─────────────────────
            [
                'employee' => [
                    'role' => 'Employee',
                    'status' => 'active',
                    'first_name' => 'Zander',
                    'last_name' => 'Duhaylungsod',
                    'middle_name' => null,
                    'date_of_birth' => '2000-01-01',
                    'email' => 'z.duhaylungsod.547209@umindanao.edu.ph',
                    'phone_number' => '09100000001',
                    'home_address' => 'Davao City',
                    'emergency_contact_name' => 'N/A',
                    'emergency_contact_number' => '09100000000',
                    'relationship' => 'N/A',
                    'start_date' => '2024-01-01',
                    'department' => 'Front Office',
                    'job_category' => 'Front Desk Agent',
                    'shift_sched' => 'Morning',
                    'employment_type' => 'regular',
                    'reporting_manager' => null,
                    'basic_salary' => 20000,
                ],
                'password' => 'Employee@1',
            ],
            [
                'employee' => [
                    'role' => 'Employee',
                    'status' => 'active',
                    'first_name' => 'Nicole',
                    'last_name' => 'Ednilan',
                    'middle_name' => null,
                    'date_of_birth' => '2000-01-01',
                    'email' => 'n.ednilan.549690@umindanao.edu.ph',
                    'phone_number' => '09100000002',
                    'home_address' => 'Davao City',
                    'emergency_contact_name' => 'N/A',
                    'emergency_contact_number' => '09100000000',
                    'relationship' => 'N/A',
                    'start_date' => '2024-01-01',
                    'department' => 'Front Office',
                    'job_category' => 'Guest Service Agent',
                    'shift_sched' => 'Afternoon',
                    'employment_type' => 'regular',
                    'reporting_manager' => null,
                    'basic_salary' => 20000,
                ],
                'password' => 'Employee@2',
            ],

            // ── HOUSEKEEPING DEPARTMENT (Morning & Afternoon Shifts) ─────────────────────
            [
                'employee' => [
                    'role' => 'Employee',
                    'status' => 'active',
                    'first_name' => 'DM Rashid',
                    'last_name' => 'Ferrer',
                    'middle_name' => null,
                    'date_of_birth' => '2000-01-01',
                    'email' => 'd.ferrer.545481@umindanao.edu.ph',
                    'phone_number' => '09100000003',
                    'home_address' => 'Davao City',
                    'emergency_contact_name' => 'N/A',
                    'emergency_contact_number' => '09100000000',
                    'relationship' => 'N/A',
                    'start_date' => '2024-01-01',
                    'department' => 'Housekeeping',
                    'job_category' => 'Room Attendant',
                    'shift_sched' => 'Morning',
                    'employment_type' => 'regular',
                    'reporting_manager' => null,
                    'basic_salary' => 17000,
                ],
                'password' => 'Employee@3',
            ],
            [
                'employee' => [
                    'role' => 'Employee',
                    'status' => 'active',
                    'first_name' => 'Karylle Mish',
                    'last_name' => 'Gellica',
                    'middle_name' => null,
                    'date_of_birth' => '2000-01-01',
                    'email' => 'k.gellica.544337@umindanao.edu.ph',
                    'phone_number' => '09100000004',
                    'home_address' => 'Davao City',
                    'emergency_contact_name' => 'N/A',
                    'emergency_contact_number' => '09100000000',
                    'relationship' => 'N/A',
                    'start_date' => '2024-01-01',
                    'department' => 'Housekeeping',
                    'job_category' => 'Housekeeping Supervisor',
                    'shift_sched' => 'Morning',
                    'employment_type' => 'regular',
                    'reporting_manager' => null,
                    'basic_salary' => 25000,
                ],
                'password' => 'Employee@4',
            ],

            // ── FOOD & BEVERAGE DEPARTMENT (All Shifts - 24/7 operation) ─────────────────────
            [
                'employee' => [
                    'role' => 'Employee',
                    'status' => 'active',
                    'first_name' => 'Aaron',
                    'last_name' => 'Jalapon',
                    'middle_name' => null,
                    'date_of_birth' => '2000-01-01',
                    'email' => 'a.jalapon.548769@umindanao.edu.ph',
                    'phone_number' => '09100000005',
                    'home_address' => 'Davao City',
                    'emergency_contact_name' => 'N/A',
                    'emergency_contact_number' => '09100000000',
                    'relationship' => 'N/A',
                    'start_date' => '2024-01-01',
                    'department' => 'Food & Beverage',
                    'job_category' => 'Restaurant Server',
                    'shift_sched' => 'Morning',
                    'employment_type' => 'regular',
                    'reporting_manager' => null,
                    'basic_salary' => 18000,
                ],
                'password' => 'Employee@5',
            ],
            [
                'employee' => [
                    'role' => 'Employee',
                    'status' => 'active',
                    'first_name' => 'Nazlah',
                    'last_name' => 'Nanding',
                    'middle_name' => null,
                    'date_of_birth' => '2000-01-01',
                    'email' => 'n.nanding.545627@umindanao.edu.ph',
                    'phone_number' => '09100000006',
                    'home_address' => 'Davao City',
                    'emergency_contact_name' => 'N/A',
                    'emergency_contact_number' => '09100000000',
                    'relationship' => 'N/A',
                    'start_date' => '2024-01-01',
                    'department' => 'Food & Beverage',
                    'job_category' => 'Line Cook',
                    'shift_sched' => 'Afternoon',
                    'employment_type' => 'regular',
                    'reporting_manager' => null,
                    'basic_salary' => 19000,
                ],
                'password' => 'Employee@6',
            ],

            // ── SALES & MARKETING (Morning Shift) ─────────────────────
            [
                'employee' => [
                    'role' => 'Employee',
                    'status' => 'active',
                    'first_name' => 'Jan Loren',
                    'last_name' => 'Odiong',
                    'middle_name' => null,
                    'date_of_birth' => '2000-01-01',
                    'email' => 'j.odiong.544579@umindanao.edu.ph',
                    'phone_number' => '09100000007',
                    'home_address' => 'Davao City',
                    'emergency_contact_name' => 'N/A',
                    'emergency_contact_number' => '09100000000',
                    'relationship' => 'N/A',
                    'start_date' => '2024-01-01',
                    'department' => 'Sales & Marketing',
                    'job_category' => 'Sales Coordinator',
                    'shift_sched' => 'Morning',
                    'employment_type' => 'regular',
                    'reporting_manager' => null,
                    'basic_salary' => 23000,
                ],
                'password' => 'Employee@7',
            ],

            // ── MAINTENANCE / ENGINEERING (Night Shift for overnight repairs) ─────────────────────
            [
                'employee' => [
                    'role' => 'Employee',
                    'status' => 'active',
                    'first_name' => 'Eduard Anthony',
                    'last_name' => 'Pechayco',
                    'middle_name' => null,
                    'date_of_birth' => '2000-01-01',
                    'email' => 'e.pechayco.546282@umindanao.edu.ph',
                    'phone_number' => '09100000008',
                    'home_address' => 'Davao City',
                    'emergency_contact_name' => 'N/A',
                    'emergency_contact_number' => '09100000000',
                    'relationship' => 'N/A',
                    'start_date' => '2024-01-01',
                    'department' => 'Engineering & Maintenance',
                    'job_category' => 'Maintenance Technician',
                    'shift_sched' => 'Night',
                    'employment_type' => 'regular',
                    'reporting_manager' => null,
                    'basic_salary' => 22000,
                ],
                'password' => 'Employee@8',
            ],

            // ── SECURITY DEPARTMENT (Night Shift for overnight coverage) ─────────────────────
            [
                'employee' => [
                    'role' => 'Employee',
                    'status' => 'active',
                    'first_name' => 'Julie Anne',
                    'last_name' => 'Pesana',
                    'middle_name' => null,
                    'date_of_birth' => '2000-01-01',
                    'email' => 'j.pesana.547304@umindanao.edu.ph',
                    'phone_number' => '09100000009',
                    'home_address' => 'Davao City',
                    'emergency_contact_name' => 'N/A',
                    'emergency_contact_number' => '09100000000',
                    'relationship' => 'N/A',
                    'start_date' => '2024-01-01',
                    'department' => 'Security',
                    'job_category' => 'Security Guard',
                    'shift_sched' => 'Night',
                    'employment_type' => 'regular',
                    'reporting_manager' => null,
                    'basic_salary' => 19000,
                ],
                'password' => 'Employee@9',
            ],
        ];

        $this->command->info('Seeding employees and user accounts...');

        foreach ($employees as $data) {

            // Random emergency contact data
            $emergencyNames = [
                'Maria Santos',
                'John Cruz',
                'Angela Reyes',
                'Michael Garcia',
                'Rose Dela Cruz',
                'Carlo Mendoza',
                'Anne Flores',
                'Joshua Ramos',
            ];

            $relationships = [
                'Mother',
                'Father',
                'Brother',
                'Sister',
                'Spouse',
                'Guardian',
                'Cousin',
            ];

            // Auto-fill missing emergency contact fields
            $data['employee']['emergency_contact_name'] =
                $data['employee']['emergency_contact_name']
                ?? $emergencyNames[array_rand($emergencyNames)];

            $data['employee']['emergency_contact_number'] =
                $data['employee']['emergency_contact_number']
                ?? '09' . rand(100000000, 999999999);

            $data['employee']['relationship'] =
                $data['employee']['relationship']
                ?? $relationships[array_rand($relationships)];

            // Create or update employee record
            $employee = Employee::updateOrCreate(
                ['email' => $data['employee']['email']],
                $data['employee']
            );

            // Create matching user account so they can log in
            User::updateOrCreate(
                ['email' => $data['employee']['email']],
                [
                    'name' => $data['employee']['first_name'] . ' ' . $data['employee']['last_name'],
                    'email' => $data['employee']['email'],
                    'password' => Hash::make($data['password']),
                    'role' => $data['employee']['role'],
                ]
            );
        }

        // Print login credentials table
        $this->command->info('');
        $this->command->info('✅ Employees and user accounts seeded successfully!');
        
        // Shift hours mapping for display
        $shiftHours = [
            'Morning' => '07:00-15:00',
            'Afternoon' => '15:00-23:00',
            'Night' => '23:00-07:00',
        ];
        
        $this->command->table(
            ['Name', 'Role', 'Department', 'Job Category', 'Shift', 'Hours', 'Email', 'Password'],
            collect($employees)->map(fn($d) => [
                $d['employee']['first_name'] . ' ' . $d['employee']['last_name'],
                $d['employee']['role'],
                $d['employee']['department'],
                $d['employee']['job_category'],
                $d['employee']['shift_sched'],
                $shiftHours[$d['employee']['shift_sched']],
                $d['employee']['email'],
                $d['password'],
            ])->toArray()
        );
        
        // Print shift summary
        $this->command->info('');
        $this->command->info('📋 Shift Assignment Summary:');
        $this->command->info('   🌅 Morning Shift (07:00-15:00): ' . collect($employees)->where('employee.shift_sched', 'Morning')->count() . ' employees');
        $this->command->info('   ☀️ Afternoon Shift (15:00-23:00): ' . collect($employees)->where('employee.shift_sched', 'Afternoon')->count() . ' employees');
        $this->command->info('   🌙 Night Shift (23:00-07:00): ' . collect($employees)->where('employee.shift_sched', 'Night')->count() . ' employees');
    }
}
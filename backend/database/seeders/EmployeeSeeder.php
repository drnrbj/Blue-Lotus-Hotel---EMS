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
        $employees = [];

        // ── HR DEPARTMENT (5 employees) ─────────────────────────────
        $hrEmployees = [
            [
                'first_name' => 'Hazeljoy',
                'last_name' => 'Hingpit',
                'email' => 'h.hingpit.545666@umindanao.edu.ph',
                'job_category' => 'HR Manager',
                'basic_salary' => 35000,
            ],
            [
                'first_name' => 'Kiarra Mae',
                'last_name' => 'Guradillo',
                'email' => 'k.guradillo.548474@umindanao.edu.ph',
                'job_category' => 'Recruitment Specialist',
                'basic_salary' => 28000,
            ],
            [
                'first_name' => 'Kevin John',
                'last_name' => 'Anga',
                'email' => 'k.anga.543642@umindanao.edu.ph',
                'job_category' => 'HR Coordinator',
                'basic_salary' => 27000,
            ],
            [
                'first_name' => 'Yosh',
                'last_name' => 'Batula',
                'email' => 'y.batula.544580@umindanao.edu.ph',
                'job_category' => 'HR Assistant',
                'basic_salary' => 25000,
            ],
            [
                'first_name' => 'Joevan',
                'last_name' => 'Capote',
                'email' => 'j.capote.545089@umindanao.edu.ph',
                'job_category' => 'Training Specialist',
                'basic_salary' => 27000,
            ],
        ];

        // ── ACCOUNTING & FINANCE DEPARTMENT (5 employees) ─────────────────────
        $accountants = [
            [
                'first_name' => 'Princess Mae',
                'last_name' => 'Planas',
                'email' => 'p.planas.548892@umindanao.edu.ph',
                'job_category' => 'Accounting Staff',
                'basic_salary' => 27000,
            ],
            [
                'first_name' => 'John Llorie',
                'last_name' => 'Sarmiento',
                'email' => 'j.sarmiento.545495@umindanao.edu.ph',
                'job_category' => 'Payroll Officer',
                'basic_salary' => 28000,
            ],
            [
                'first_name' => 'Mheil Andrei',
                'last_name' => 'Cenita',
                'email' => 'm.cenita.545045@umindanao.edu.ph',
                'job_category' => 'Accounts Payable',
                'basic_salary' => 26000,
            ],
            [
                'first_name' => 'Ryan Jay',
                'last_name' => 'Compuesto',
                'email' => 'r.compuesto.545237@umindanao.edu.ph',
                'job_category' => 'Accounts Receivable',
                'basic_salary' => 26000,
            ],
            [
                'first_name' => 'Fe Anne',
                'last_name' => 'Malasarte',
                'email' => 'f.malasarte.543849@umindanao.edu.ph',
                'job_category' => 'Senior Accountant',
                'basic_salary' => 35000,
            ],
        ];

        // ── ADMIN / GENERAL MANAGEMENT ──────────────────────────
        $adminEmployees = [
            [
                'role' => 'Admin',
                'first_name' => 'Angelo',
                'last_name' => 'Lozano',
                'email' => 'a.lozano.547237@umindanao.edu.ph',
                'job_category' => 'General Manager',
                'basic_salary' => 80000,
                'password' => 'admin123',
            ],
            [
                'role' => 'Admin',
                'first_name' => 'Mark Jade',
                'last_name' => 'Palma',
                'email' => 'm.palma.546616@umindanao.edu.ph',
                'job_category' => 'Assistant General Manager',
                'basic_salary' => 55000,
                'password' => 'admin123',
            ],
        ];

        // ── OTHER EMPLOYEES (Remaining staff) ─────────────────────
        $otherEmployees = [
            // Front Office
            [
                'first_name' => 'Zander',
                'last_name' => 'Duhaylungsod',
                'email' => 'z.duhaylungsod.547209@umindanao.edu.ph',
                'department' => 'Front Office',
                'job_category' => 'Front Desk Agent',
                'shift_sched' => 'Morning',
                'basic_salary' => 20000,
            ],
            [
                'first_name' => 'Nicole',
                'last_name' => 'Ednilan',
                'email' => 'n.ednilan.549690@umindanao.edu.ph',
                'department' => 'Front Office',
                'job_category' => 'Guest Service Agent',
                'shift_sched' => 'Afternoon',
                'basic_salary' => 20000,
            ],
            // Housekeeping
            [
                'first_name' => 'DM Rashid',
                'last_name' => 'Ferrer',
                'email' => 'd.ferrer.545481@umindanao.edu.ph',
                'department' => 'Housekeeping',
                'job_category' => 'Room Attendant',
                'shift_sched' => 'Morning',
                'basic_salary' => 17000,
            ],
            [
                'first_name' => 'Karylle Mish',
                'last_name' => 'Gellica',
                'email' => 'k.gellica.544337@umindanao.edu.ph',
                'department' => 'Housekeeping',
                'job_category' => 'Housekeeping Supervisor',
                'shift_sched' => 'Morning',
                'basic_salary' => 25000,
            ],
            // Food & Beverage
            [
                'first_name' => 'Aaron',
                'last_name' => 'Jalapon',
                'email' => 'a.jalapon.548769@umindanao.edu.ph',
                'department' => 'Food & Beverage',
                'job_category' => 'Restaurant Server',
                'shift_sched' => 'Morning',
                'basic_salary' => 18000,
            ],
            [
                'first_name' => 'Nazlah',
                'last_name' => 'Nanding',
                'email' => 'n.nanding.545627@umindanao.edu.ph',
                'department' => 'Food & Beverage',
                'job_category' => 'Line Cook',
                'shift_sched' => 'Afternoon',
                'basic_salary' => 19000,
            ],
            // Sales & Marketing
            [
                'first_name' => 'Jan Loren',
                'last_name' => 'Odiong',
                'email' => 'j.odiong.544579@umindanao.edu.ph',
                'department' => 'Sales & Marketing',
                'job_category' => 'Sales Coordinator',
                'shift_sched' => 'Morning',
                'basic_salary' => 23000,
            ],
            // Maintenance
            [
                'first_name' => 'Eduard Anthony',
                'last_name' => 'Pechayco',
                'email' => 'e.pechayco.546282@umindanao.edu.ph',
                'department' => 'Engineering & Maintenance',
                'job_category' => 'Maintenance Technician',
                'shift_sched' => 'Night',
                'basic_salary' => 22000,
            ],
            // Security
            [
                'first_name' => 'Julie Anne',
                'last_name' => 'Pesana',
                'email' => 'j.pesana.547304@umindanao.edu.ph',
                'department' => 'Security',
                'job_category' => 'Security Guard',
                'shift_sched' => 'Night',
                'basic_salary' => 19000,
            ],
            // Additional employees from your list
            [
                'first_name' => 'Fletcher',
                'last_name' => 'Malazarte',
                'email' => 'f.malazarte.545483@umindanao.edu.ph',
                'department' => 'Food & Beverage',
                'job_category' => 'Bartender',
                'shift_sched' => 'Afternoon',
                'basic_salary' => 20000,
            ],
            [
                'first_name' => 'Jan Vincent',
                'last_name' => 'Oclarit',
                'email' => 'j.oclarit.543717@umindanao.edu.ph',
                'department' => 'Front Office',
                'job_category' => 'Concierge',
                'shift_sched' => 'Morning',
                'basic_salary' => 21000,
            ],
        ];

        // Build HR employees array
        foreach ($hrEmployees as $hr) {
            $employees[] = [
                'employee' => [
                    'role' => 'HR',
                    'status' => 'active',
                    'first_name' => $hr['first_name'],
                    'last_name' => $hr['last_name'],
                    'date_of_birth' => '1998-01-01',
                    'email' => $hr['email'],
                    'phone_number' => '0917' . rand(1000000, 9999999),
                    'home_address' => 'Davao City',
                    'start_date' => '2024-01-01',
                    'department' => 'Human Resources',
                    'job_category' => $hr['job_category'],
                    'shift_sched' => 'Morning',
                    'employment_type' => 'regular',
                    'basic_salary' => $hr['basic_salary'],
                    'emergency_contact_name' => 'Emergency Contact',
                    'emergency_contact_number' => '09171234567',
                    'relationship' => 'Emergency Contact',
                ],
                'password' => 'hr123',
            ];
        }

        // Build Accountants array
        foreach ($accountants as $acc) {
            $employees[] = [
                'employee' => [
                    'role' => 'Accountant',
                    'status' => 'active',
                    'first_name' => $acc['first_name'],
                    'last_name' => $acc['last_name'],
                    'date_of_birth' => '1998-01-01',
                    'email' => $acc['email'],
                    'phone_number' => '0917' . rand(1000000, 9999999),
                    'home_address' => 'Davao City',
                    'start_date' => '2024-01-01',
                    'department' => 'Accounting & Finance',
                    'job_category' => $acc['job_category'],
                    'shift_sched' => 'Morning',
                    'employment_type' => 'regular',
                    'basic_salary' => $acc['basic_salary'],
                    'emergency_contact_name' => 'Emergency Contact',
                    'emergency_contact_number' => '09171234567',
                    'relationship' => 'Emergency Contact',
                ],
                'password' => 'acc123',
            ];
        }

        // Build Admin employees
        foreach ($adminEmployees as $admin) {
            $employees[] = [
                'employee' => [
                    'role' => $admin['role'],
                    'status' => 'active',
                    'first_name' => $admin['first_name'],
                    'last_name' => $admin['last_name'],
                    'date_of_birth' => '1997-01-01',
                    'email' => $admin['email'],
                    'phone_number' => '0917' . rand(1000000, 9999999),
                    'home_address' => 'Davao City',
                    'start_date' => '2024-01-01',
                    'department' => 'Executive Office',
                    'job_category' => $admin['job_category'],
                    'shift_sched' => 'Morning',
                    'employment_type' => 'regular',
                    'basic_salary' => $admin['basic_salary'],
                    'emergency_contact_name' => 'Emergency Contact',
                    'emergency_contact_number' => '09171234567',
                    'relationship' => 'Emergency Contact',
                ],
                'password' => $admin['password'],
            ];
        }

        // Build Other Employees
        foreach ($otherEmployees as $emp) {
            $employees[] = [
                'employee' => [
                    'role' => 'Employee',
                    'status' => 'active',
                    'first_name' => $emp['first_name'],
                    'last_name' => $emp['last_name'],
                    'date_of_birth' => '2000-01-01',
                    'email' => $emp['email'],
                    'phone_number' => '0910' . rand(1000000, 9999999),
                    'home_address' => 'Davao City',
                    'start_date' => '2024-01-01',
                    'department' => $emp['department'],
                    'job_category' => $emp['job_category'],
                    'shift_sched' => $emp['shift_sched'],
                    'employment_type' => 'regular',
                    'basic_salary' => $emp['basic_salary'],
                    'emergency_contact_name' => 'Emergency Contact',
                    'emergency_contact_number' => '09171234567',
                    'relationship' => 'Emergency Contact',
                ],
                'password' => 'employee123',
            ];
        }

        $this->command->info('Seeding employees and user accounts...');

        $employeeCounter = 1;  // For E001, E002, etc.
        $adminCounter = 1;     // For A001, A002, etc.
        $seededEmployees = [];

        foreach ($employees as $data) {
            // Random emergency contact data
            $emergencyNames = [
                'Maria Santos', 'John Cruz', 'Angela Reyes', 'Michael Garcia',
                'Rose Dela Cruz', 'Carlo Mendoza', 'Anne Flores', 'Joshua Ramos',
            ];

            $relationships = ['Mother', 'Father', 'Brother', 'Sister', 'Spouse', 'Guardian', 'Cousin'];

            // Set random emergency contact data
            $data['employee']['emergency_contact_name'] = $emergencyNames[array_rand($emergencyNames)];
            $data['employee']['emergency_contact_number'] = '09' . rand(100000000, 999999999);
            $data['employee']['relationship'] = $relationships[array_rand($relationships)];
            
            // Set custom IDs based on role
            if ($data['employee']['role'] === 'Admin') {
                $adminCode = 'A' . str_pad($adminCounter, 3, '0', STR_PAD_LEFT);
                $data['employee']['admin_code'] = $adminCode;
                $displayCode = $adminCode;
                $adminCounter++;
            } else {
                $employeeCode = 'E' . str_pad($employeeCounter, 3, '0', STR_PAD_LEFT);
                $data['employee']['employee_code'] = $employeeCode;
                $displayCode = $employeeCode;
                $employeeCounter++;
            }

            // Store for display
            $seededEmployees[] = [
                'code' => $displayCode,
                'employee' => $data['employee'],
                'password' => $data['password'],
            ];

            // Create or update employee record
            $employee = Employee::updateOrCreate(
                ['email' => $data['employee']['email']],
                $data['employee']
            );

            // Create matching user account
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
            ['Custom ID', 'Name', 'Role', 'Department', 'Job Category', 'Shift', 'Hours', 'Email', 'Password'],
            collect($seededEmployees)->map(fn($item) => [
                $item['code'],
                $item['employee']['first_name'] . ' ' . $item['employee']['last_name'],
                $item['employee']['role'],
                $item['employee']['department'],
                $item['employee']['job_category'],
                $item['employee']['shift_sched'],
                $shiftHours[$item['employee']['shift_sched']],
                $item['employee']['email'],
                $item['password'],
            ])->toArray()
        );
        
        // Print summary statistics
        $this->command->info('');
        $this->command->info('📊 Employee Summary:');
        $this->command->info('   👔 HR Employees: 5');
        $this->command->info('   💰 Accountants: 5');
        $this->command->info('   👥 Regular Employees: ' . ($employeeCounter - 1));
        $this->command->info('   👑 Admin: ' . ($adminCounter - 1));
        $this->command->info('   📦 TOTAL: ' . (($employeeCounter - 1) + ($adminCounter - 1)) . ' employees');
        
        $this->command->info('');
        $this->command->info('📋 Shift Assignment Summary:');
        $this->command->info('   🌅 Morning Shift (07:00-15:00): ' . collect($seededEmployees)->where('employee.shift_sched', 'Morning')->count() . ' employees');
        $this->command->info('   ☀️ Afternoon Shift (15:00-23:00): ' . collect($seededEmployees)->where('employee.shift_sched', 'Afternoon')->count() . ' employees');
        $this->command->info('   🌙 Night Shift (23:00-07:00): ' . collect($seededEmployees)->where('employee.shift_sched', 'Night')->count() . ' employees');
    }
}
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

            // ── HR ─────────────────────────────
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
                    'department' => 'Administration',
                    'job_category' => 'HR Officer',
                    'employment_type' => 'regular',
                    'basic_salary' => 25000,
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
                    'department' => 'Administration',
                    'job_category' => 'HR Officer',
                    'employment_type' => 'regular',
                    'basic_salary' => 25000,
                ],
                'password' => 'hr123',
            ],

            // ── ADMIN ──────────────────────────
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
                    'department' => 'Administration',
                    'job_category' => 'General Manager',
                    'employment_type' => 'regular',
                    'basic_salary' => 60000,
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
                    'department' => 'Administration',
                    'job_category' => 'Department Manager',
                    'employment_type' => 'regular',
                    'basic_salary' => 42000,
                ],
                'password' => 'admin123',
            ],

            // ── ACCOUNTANT ─────────────────────
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
                    'department' => 'Administration',
                    'job_category' => 'Accounting Staff',
                    'employment_type' => 'regular',
                    'basic_salary' => 24000,
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
                    'department' => 'Administration',
                    'job_category' => 'Payroll Officer',
                    'employment_type' => 'regular',
                    'basic_salary' => 26000,
                ],
                'password' => 'acc123',
            ],



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
                    'department' => 'Administration',
                    'job_category' => 'staff',
                    'employment_type' => 'regular',
                    'reporting_manager' => null,
                    'basic_salary' => 18000,
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
                    'department' => 'Administration',
                    'job_category' => 'staff',
                    'employment_type' => 'regular',
                    'reporting_manager' => null,
                    'basic_salary' => 18000,
                ],
                'password' => 'Employee@2',
            ],

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
                    'department' => 'Administration',
                    'job_category' => 'staff',
                    'employment_type' => 'regular',
                    'reporting_manager' => null,
                    'basic_salary' => 18000,
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
                    'department' => 'Administration',
                    'job_category' => 'staff',
                    'employment_type' => 'regular',
                    'reporting_manager' => null,
                    'basic_salary' => 18000,
                ],
                'password' => 'Employee@4',
            ],

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
                    'department' => 'Administration',
                    'job_category' => 'staff',
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
                    'department' => 'Administration',
                    'job_category' => 'staff',
                    'employment_type' => 'regular',
                    'reporting_manager' => null,
                    'basic_salary' => 18000,
                ],
                'password' => 'Employee@6',
            ],

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
                    'department' => 'Administration',
                    'job_category' => 'staff',
                    'employment_type' => 'regular',
                    'reporting_manager' => null,
                    'basic_salary' => 18000,
                ],
                'password' => 'Employee@7',
            ],

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
                    'department' => 'Administration',
                    'job_category' => 'staff',
                    'employment_type' => 'regular',
                    'reporting_manager' => null,
                    'basic_salary' => 18000,
                ],
                'password' => 'Employee@8',
            ],

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
                    'department' => 'Administration',
                    'job_category' => 'staff',
                    'employment_type' => 'regular',
                    'reporting_manager' => null,
                    'basic_salary' => 18000,
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

        foreach ($employees as $data) {
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
        $this->command->table(
            ['Name', 'Role', 'Department', 'Email', 'Password'],
            collect($employees)->map(fn($d) => [
                $d['employee']['first_name'] . ' ' . $d['employee']['last_name'],
                $d['employee']['role'],
                $d['employee']['department'],
                $d['employee']['email'],
                $d['password'],
            ])->toArray()
        );
    }
}
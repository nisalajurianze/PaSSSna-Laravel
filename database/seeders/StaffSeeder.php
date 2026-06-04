<?php

namespace Database\Seeders;

use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        $staffMembers = [
            [
                'first_name' => 'Gordon',
                'last_name' => 'Ramsay',
                'email' => 'chef.gordon@passsna.com',
                'phone' => '+1 (555) 111-2233',
                'role' => 'chef',
                'status' => 'active',
                'salary' => 4500.00,
                'hire_date' => now()->subYears(3),
                'address' => '123 Chef Street, Kitchen City',
                'emergency_contact' => 'Emergency Gordon',
                'emergency_phone' => '+1 (555) 111-2244',
                'password' => Hash::make('password123'),
            ],
            [
                'first_name' => 'Maria',
                'last_name' => 'Garcia',
                'email' => 'maria.manager@passsna.com',
                'phone' => '+1 (555) 222-3344',
                'role' => 'manager',
                'status' => 'active',
                'salary' => 5500.00,
                'hire_date' => now()->subYears(5),
                'address' => '456 Manager Ave, Business City',
                'emergency_contact' => 'Emergency Maria',
                'emergency_phone' => '+1 (555) 222-3355',
                'password' => Hash::make('password123'),
            ],
            [
                'first_name' => 'James',
                'last_name' => 'Wilson',
                'email' => 'james.waiter@passsna.com',
                'phone' => '+1 (555) 333-4455',
                'role' => 'waiter',
                'status' => 'active',
                'salary' => 1800.00,
                'hire_date' => now()->subYears(1),
                'address' => '789 Service Road, Hospitality City',
                'emergency_contact' => 'Emergency James',
                'emergency_phone' => '+1 (555) 333-4466',
                'password' => Hash::make('password123'),
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'email' => 'sarah.waiter@passsna.com',
                'phone' => '+1 (555) 444-5566',
                'role' => 'waiter',
                'status' => 'active',
                'salary' => 1800.00,
                'hire_date' => now()->subMonths(6),
                'address' => '321 Waitress Lane, Service City',
                'emergency_contact' => 'Emergency Sarah',
                'emergency_phone' => '+1 (555) 444-5577',
                'password' => Hash::make('password123'),
            ],
            [
                'first_name' => 'Mike',
                'last_name' => 'Brown',
                'email' => 'mike.bartender@passsna.com',
                'phone' => '+1 (555) 555-6677',
                'role' => 'bartender',
                'status' => 'active',
                'salary' => 2200.00,
                'hire_date' => now()->subYears(2),
                'address' => '654 Bar Street, Drink City',
                'emergency_contact' => 'Emergency Mike',
                'emergency_phone' => '+1 (555) 555-6688',
                'password' => Hash::make('password123'),
            ],
            [
                'first_name' => 'Anna',
                'last_name' => 'Lee',
                'email' => 'anna.cleaner@passsna.com',
                'phone' => '+1 (555) 666-7788',
                'role' => 'admin',
                'status' => 'active',
                'salary' => 1600.00,
                'hire_date' => now()->subYears(4),
                'address' => '987 Clean Street, Hygiene City',
                'emergency_contact' => 'Emergency Anna',
                'emergency_phone' => '+1 (555) 666-7799',
                'password' => Hash::make('password123'),
            ],
            [
                'first_name' => 'Carlos',
                'last_name' => 'Rodriguez',
                'email' => 'carlos.chef@passsna.com',
                'phone' => '+1 (555) 777-8899',
                'role' => 'chef',
                'status' => 'on_leave',
                'salary' => 3500.00,
                'hire_date' => now()->subYears(2),
                'address' => '147 Kitchen Road, Food City',
                'emergency_contact' => 'Emergency Carlos',
                'emergency_phone' => '+1 (555) 777-8800',
                'password' => Hash::make('password123'),
            ],
            [
                'first_name' => 'Emily',
                'last_name' => 'Davis',
                'email' => 'emily.hostess@passsna.com',
                'phone' => '+1 (555) 888-9900',
                'role' => 'host',
                'status' => 'active',
                'salary' => 1700.00,
                'hire_date' => now()->subMonths(3),
                'address' => '258 Reception Street, Welcome City',
                'emergency_contact' => 'Emergency Emily',
                'emergency_phone' => '+1 (555) 888-9911',
                'password' => Hash::make('password123'),
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Miller',
                'email' => 'david.cashier@passsna.com',
                'phone' => '+1 (555) 999-0011',
                'role' => 'cashier',
                'status' => 'active',
                'salary' => 1900.00,
                'hire_date' => now()->subYears(1),
                'address' => '369 Cash Street, Finance City',
                'emergency_contact' => 'Emergency David',
                'emergency_phone' => '+1 (555) 999-0022',
                'password' => Hash::make('password123'),
            ],
            [
                'first_name' => 'Robert',
                'last_name' => 'Taylor',
                'email' => 'robert.delivery@passsna.com',
                'phone' => '+1 (555) 000-1122',
                'role' => 'delivery_boy',
                'status' => 'active',
                'salary' => 1500.00,
                'hire_date' => now()->subMonths(8),
                'address' => '741 Wash Street, Clean City',
                'emergency_contact' => 'Emergency Robert',
                'emergency_phone' => '+1 (555) 000-1133',
                'password' => Hash::make('password123'),
            ],
        ];

        foreach ($staffMembers as $staff) {
            $staff['employee_id'] = 'EMP' . strtoupper(substr($staff['first_name'], 0, 3)) . str_pad(rand(1, 999), 4, '0', STR_PAD_LEFT);
            Staff::updateOrCreate(
                ['email' => $staff['email']],
                $staff
            );
        }

        // Create more sample staff
        $roles = ['chef', 'waiter', 'bartender', 'admin', 'manager', 'host', 'cashier', 'delivery_boy'];
        $statuses = ['active', 'on_leave', 'inactive'];

        $firstNames = ['John', 'Jane', 'Alex', 'Lisa', 'Tom', 'Emma', 'Sam', 'Olivia', 'Chris', 'Sophia'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez'];

        for ($i = 0; $i < 10; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $role = $roles[array_rand($roles)];
            $status = $statuses[array_rand($statuses)];
            $email = strtolower($firstName . '.' . $lastName . rand(1, 99) . '@passsna.com');

            $employeeId = 'EMP' . strtoupper(substr($firstName, 0, 3)) . str_pad(rand(1, 999), 4, '0', STR_PAD_LEFT);

            // Keep generating until we get a unique employee_id
            while (Staff::where('employee_id', $employeeId)->exists()) {
                $employeeId = 'EMP' . strtoupper(substr($firstName, 0, 3)) . str_pad(rand(1, 999), 4, '0', STR_PAD_LEFT);
            }

            Staff::updateOrCreate(
                ['email' => $email],
                [
                    'employee_id' => $employeeId,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'phone' => '+1 (555) ' . rand(100, 999) . '-' . rand(1000, 9999),
                    'role' => $role,
                    'status' => $status,
                    'salary' => rand(1500, 5000) + 0.00,
                    'hire_date' => now()->subMonths(rand(1, 24)),
                    'address' => rand(100, 999) . ' Random Street, City ' . chr(rand(65, 90)),
                    'emergency_contact' => 'Emergency ' . $firstName,
                    'emergency_phone' => '+1 (555) ' . rand(100, 999) . '-' . rand(1000, 9999),
                    'password' => Hash::make('password123'),
                ]
            );
        }
    }
}

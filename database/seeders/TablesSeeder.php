<?php

namespace Database\Seeders;

use App\Models\Table;
use Illuminate\Database\Seeder;

class TablesSeeder extends Seeder
{
    public function run(): void
    {
        $tables = [
            ['table_number' => 1, 'capacity' => 2, 'status' => 'available', 'location' => 'Window', 'is_active' => true],
            ['table_number' => 2, 'capacity' => 2, 'status' => 'available', 'location' => 'Window', 'is_active' => true],
            ['table_number' => 3, 'capacity' => 4, 'status' => 'available', 'location' => 'Center', 'is_active' => true],
            ['table_number' => 4, 'capacity' => 4, 'status' => 'available', 'location' => 'Center', 'is_active' => true],
            ['table_number' => 5, 'capacity' => 4, 'status' => 'available', 'location' => 'Garden', 'is_active' => true],
            ['table_number' => 6, 'capacity' => 6, 'status' => 'available', 'location' => 'Garden', 'is_active' => true],
            ['table_number' => 7, 'capacity' => 6, 'status' => 'available', 'location' => 'VIP', 'is_active' => true],
            ['table_number' => 8, 'capacity' => 8, 'status' => 'available', 'location' => 'VIP', 'is_active' => true],
            ['table_number' => 9, 'capacity' => 2, 'status' => 'available', 'location' => 'Bar', 'is_active' => true],
            ['table_number' => 10, 'capacity' => 2, 'status' => 'available', 'location' => 'Bar', 'is_active' => true],
            ['table_number' => 11, 'capacity' => 4, 'status' => 'available', 'location' => 'Patio', 'is_active' => true],
            ['table_number' => 12, 'capacity' => 4, 'status' => 'available', 'location' => 'Patio', 'is_active' => true],
            ['table_number' => 13, 'capacity' => 6, 'status' => 'reserved', 'location' => 'Center', 'is_active' => true],
            ['table_number' => 14, 'capacity' => 8, 'status' => 'occupied', 'location' => 'VIP', 'is_active' => true],
            ['table_number' => 15, 'capacity' => 10, 'status' => 'available', 'location' => 'Private Room', 'is_active' => true],
            ['table_number' => 16, 'capacity' => 10, 'status' => 'available', 'location' => 'Private Room', 'is_active' => true],
            ['table_number' => 17, 'capacity' => 12, 'status' => 'available', 'location' => 'Banquet', 'is_active' => true],
            ['table_number' => 18, 'capacity' => 12, 'status' => 'available', 'location' => 'Banquet', 'is_active' => true],
            ['table_number' => 19, 'capacity' => 4, 'status' => 'maintenance', 'location' => 'Center', 'is_active' => false],
            ['table_number' => 20, 'capacity' => 4, 'status' => 'available', 'location' => 'Window', 'is_active' => true],
        ];

        foreach ($tables as $table) {
            Table::firstOrCreate(
                ['table_number' => $table['table_number']],
                $table
            );
        }
    }
}

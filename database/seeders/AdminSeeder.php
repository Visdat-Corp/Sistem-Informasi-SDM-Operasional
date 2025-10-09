<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin accounts
        $admins = [
            [
                'nama_admin' => 'Super Admin',
                'email' => 'admin@visdat.com',
                'password' => Hash::make('admin123'),
            ],
            [
                'nama_admin' => 'Admin HR',
                'email' => 'hr@visdat.com',
                'password' => Hash::make('hr123'),
            ],
            [
                'nama_admin' => 'Admin IT',
                'email' => 'it@visdat.com',
                'password' => Hash::make('it123'),
            ],
        ];

        foreach ($admins as $admin) {
            Admin::create($admin);
        }

        $this->command->info('Admin accounts created successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('1. Super Admin - Email: admin@visdat.com, Password: admin123');
        $this->command->info('2. Admin HR - Email: hr@visdat.com, Password: hr123');
        $this->command->info('3. Admin IT - Email: it@visdat.com, Password: it123');
    }
}

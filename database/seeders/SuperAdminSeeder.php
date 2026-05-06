<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create/Update first super admin
        User::updateOrCreate(
            ['email' => 'maemmangga001@gmail.com'],
            [
                'name' => 'Super Admin 1',
                'password' => Hash::make('Kepong333!'),
                'role' => 'super_admin',
            ]
        );

        // Create/Update second super admin
        User::updateOrCreate(
            ['email' => 'ryan001@gmail.com'],
            [
                'name' => 'Ryan - Super Admin',
                'password' => Hash::make('Kepong333!'),
                'role' => 'super_admin',
            ]
        );

        $this->command->info('✅ Super Admins created successfully!');
        $this->command->info('');
        $this->command->info('📧 Super Admin 1: maemmangga001@gmail.com');
        $this->command->info('📧 Super Admin 2: ryan001@gmail.com');
        $this->command->info('🔑 Password: Kepong333!');
    }
}

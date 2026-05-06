<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(KnowledgeBaseSeeder::class);

        $adminUsers = [
            [
                'name' => 'oktavian',
                'email' => 'maemmangga01@gmail.com',
                'password' => 'Kepong333!',
                'role' => 'admin',
            ],
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@larrynfood.com',
                'password' => 'password',
                'role' => 'super_admin',
            ],
            [
                'name' => 'Super Admin 1',
                'email' => 'maemmangga001@gmail.com',
                'password' => 'password',
                'role' => 'super_admin',
            ],
            [
                'name' => 'ryan',
                'email' => 'ryan@gmail.com',
                'password' => 'password',
                'role' => 'admin',
            ],
            [
                'name' => 'okta',
                'email' => 'oktavian@gmail.com',
                'password' => 'password',
                'role' => 'admin',
            ],
            [
                'name' => 'Ryan - Super Admin',
                'email' => 'ryan001@gmail.com',
                'password' => 'password',
                'role' => 'super_admin',
            ],
        ];

        foreach ($adminUsers as $adminUser) {
            User::updateOrCreate(
                ['email' => $adminUser['email']],
                $adminUser
            );
        }
    }
}

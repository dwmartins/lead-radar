<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AddAdminUserSeeder extends Seeder
{
    /**
     * Execute os seedings do banco de dados.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@example.com')],
            [
                'name' => env('ADMIN_NAME', 'Support'),
                'last_name' => env('ADMIN_LAST_NAME', 'Test'),
                'email' => env('ADMIN_EMAIL', 'admin@example.com'),
                'password' => env('ADMIN_PASSWORD', '27072707'),
                'role' => User::ROLE_ADMIN
            ]
        );

        $this->command->info('✅ Admin: '. env('ADMIN_EMAIL', 'admin@example.com') .' / '. env('ADMIN_PASSWORD', '27072707') .'');
    }
}

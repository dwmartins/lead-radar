<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Seeder;

class AddUserSeeder extends Seeder
{
    /**
     * Execute os seedings do banco de dados.
     */
    public function run(): void
    {
        $plan = Plan::where('is_active', true)->first();

        User::firstOrCreate(
            ['email' => 'carlos@example.com'],
            [
                'name'      => 'Carlos',
                'last_name' => 'Alberto',
                'password'  => '27072707',
                'role'      => User::ROLE_USER,
                'plan_id'   => $plan ? $plan->id : null
            ]
        );

        $this->command->info('✅ Usuário: carlos@example.com / 27072707');
    }
}

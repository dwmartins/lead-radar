<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class AddPlansSeeder extends Seeder
{
    /**
     * Execute os seedings do banco de dados.
     */
    public function run(): void
    {
        $plans = [
            ['name' => 'Básico',       'monthly_leads_limit' => 100,  'price' => 49.90,  'is_active' => true],
            ['name' => 'Profissional', 'monthly_leads_limit' => 500,  'price' => 149.90, 'is_active' => true],
            ['name' => 'Empresarial',  'monthly_leads_limit' => 2000, 'price' => 399.90, 'is_active' => true],
        ];

        foreach ($plans as $plan) {
            Plan::firstOrCreate(['name' => $plan['name']], $plan);
        }

        $this->command->info('✅ Planos criados: Básico, Profissional, Empresarial');
    }
}
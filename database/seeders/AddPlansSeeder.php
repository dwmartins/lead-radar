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
            ['name' => 'Basic',        'monthly_search_limit' => 20,  'price' => 49.90,  'is_active' => true],
            ['name' => 'Professional', 'monthly_search_limit' => 40,  'price' => 149.90, 'is_active' => true],
            ['name' => 'Enterprise',   'monthly_search_limit' => 60, 'price'  => 399.90, 'is_active' => true],
        ];

        foreach ($plans as $plan) {
            Plan::firstOrCreate(['name' => $plan['name']], $plan);
        }
    }
}
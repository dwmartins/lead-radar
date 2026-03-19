<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\PlanPrice;
use Illuminate\Database\Seeder;

class AddPlansSeeder extends Seeder
{
    /**
     * Execute os seedings do banco de dados.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'monthly_search_limit' => 20,
                'is_active' => true,
                'prices' => [
                    ['price' => 49.90, 'currency' => 'BRL'],
                    ['price' => 20.90,  'currency' => 'USD'],
                ],
            ],
            [
                'name' => 'Professional',
                'monthly_search_limit' => 40,
                'is_active' => true,
                'prices' => [
                    ['price' => 89.90, 'currency' => 'BRL'],
                    ['price' => 49.90,  'currency' => 'USD'],
                ],
            ],
            [
                'name' => 'Enterprise',
                'monthly_search_limit' => 60,
                'is_active' => true,
                'prices' => [
                    ['price' => 120.90, 'currency' => 'BRL'],
                    ['price' => 89.90,  'currency' => 'USD'],
                ],
            ],
        ];

        foreach ($plans as $planData) {
            $plan = Plan::firstOrCreate(
                ['name' => $planData['name']],
                [
                    'monthly_search_limit' => $planData['monthly_search_limit'],
                    'is_active' => $planData['is_active'],
                ]
            );

            foreach ($planData['prices'] as $priceData) {
                PlanPrice::updateOrCreate(
                    [
                        'plan_id'  => $plan->id,
                        'currency' => $priceData['currency'],
                    ],
                    [
                        'price'     => $priceData['price'],
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
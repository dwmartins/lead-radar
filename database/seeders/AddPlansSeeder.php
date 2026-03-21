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
                    [
                        'price' => 49.90,
                        'currency' => 'BRL',
                        'type' => 'recurring',
                        'interval' => 'month',
                        'interval_count' => 1,
                    ],
                    [
                        'price' => 20.90,
                        'currency' => 'USD',
                        'type' => 'recurring',
                        'interval' => 'month',
                        'interval_count' => 1,
                    ],
                ],
            ],
            [
                'name' => 'Professional',
                'monthly_search_limit' => 40,
                'is_active' => true,
                'prices' => [
                    [
                        'price' => 89.90,
                        'currency' => 'BRL',
                        'type' => 'recurring',
                        'interval' => 'month',
                        'interval_count' => 1,
                    ],
                    [
                        'price' => 49.90,
                        'currency' => 'USD',
                        'type' => 'recurring',
                        'interval' => 'month',
                        'interval_count' => 1,
                    ],
                ],
            ],
            [
                'name' => 'Enterprise',
                'monthly_search_limit' => 60,
                'is_active' => true,
                'prices' => [
                    [
                        'price' => 120.90,
                        'currency' => 'BRL',
                        'type' => 'recurring',
                        'interval' => 'month',
                        'interval_count' => 1,
                    ],
                    [
                        'price' => 89.90,
                        'currency' => 'USD',
                        'type' => 'recurring',
                        'interval' => 'month',
                        'interval_count' => 1,
                    ],
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
                        'plan_id'        => $plan->id,
                        'currency'       => $priceData['currency'],
                        'type'           => $priceData['type'],
                        'interval'       => $priceData['interval'],
                        'interval_count' => $priceData['interval_count'],
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
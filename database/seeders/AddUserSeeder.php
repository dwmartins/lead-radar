<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\PlanPrice;
use App\Models\Subscription;
use App\Models\Transaction;
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

        /** @var User $user */
        $user = User::firstOrCreate(
            ['email' => 'carlos@example.com'],
            [
                'name'      => 'Carlos',
                'last_name' => 'Alberto',
                'password'  => '27072707',
                'role'      => User::ROLE_USER,
            ]
        );

        if ($plan && !$user->hasActiveSubscription()) {

            $planPrice = $plan->prices()
                ->where('currency', 'BRL')
                ->where('is_active', true)
                ->first();

            $subscription = $user->subscriptions()->create([
                'plan_id'       => $plan->id,
                'started_at'    => now(),
                'expires_at'    => now()->addMonth(),
                'status'        => Subscription::STATUS_ACTIVE,
                'billing_cycle' => Subscription::BILLING_MONTHLY
            ]);

            if ($planPrice) {
                Transaction::create([
                    'subscription_id'         => $subscription->id,
                    'plan_price_id'           => $planPrice->id,
                    'amount'                  => $planPrice->price,
                    'currency'                => $planPrice->currency,
                    'status'                  => Transaction::STATUS_PAID,
                    'payment_gateway'         => Transaction::GATEWAY_SEED,
                    'payment_method'          => Transaction::METHOD_CREDIT_CARD,
                    'gateway_transaction_id'  => 'seed_' . uniqid(),
                    'paid_at'                 => now(),
                    'expires_at'              => $subscription->expires_at,
                    'meta'                    => [
                        'source' => 'seeder'
                    ],
                ]);
            }
        }
    }
}
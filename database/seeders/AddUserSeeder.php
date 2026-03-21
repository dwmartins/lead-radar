<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\PlanPrice;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
                'password'  => Hash::make('27072707'),
                'role'      => User::ROLE_USER,
            ]
        );

        if ($plan && !$user->hasActiveSubscription()) {

            $planPrice = $plan->prices()
                ->where('currency', 'BRL')
                ->where('type', PlanPrice::TYPE_RECURRING)
                ->where('interval', 'month')
                ->where('interval_count', 1)
                ->where('is_active', true)
                ->first();

            $subscription = $user->subscriptions()->create([
                'plan_id'       => $plan->id,
                'status'        => Subscription::STATUS_ACTIVE,
                'billing_cycle' => Subscription::BILLING_MONTHLY,
                'expires_at'    => now()->addMonth(),
                'is_manual'     => true,
                'note'          => 'Criado via seeder',
            ]);

            if ($planPrice) {
                Transaction::create([
                    'subscription_id'        => $subscription->id,
                    'plan_price_id'          => $planPrice->id,
                    'amount'                 => $planPrice->price,
                    'currency'               => $planPrice->currency,
                    'status'                 => Transaction::STATUS_PAID,
                    'payment_gateway'        => Transaction::GATEWAY_MANUAL,
                    'payment_method'         => Transaction::METHOD_PIX,
                    'gateway_transaction_id' => 'seed_' . uniqid(),
                    'paid_at'                => now(),
                    'meta'                   => [
                        'source' => 'seeder'
                    ],
                ]);
            }
        }
    }
}
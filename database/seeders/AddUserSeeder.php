<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Subscription;
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
            $user->subscriptions()->create([
                'plan_id'         => $plan->id,
                'started_at'      => now(),
                'amount'          => $plan->price,
                'expires_at'      => now()->addMonth(),
                'status'          => Subscription::STATUS_ACTIVE,
                'payment_gateway' => Subscription::GATEWAY_SEED,
                'payment_method'  => Subscription::METHOD_PIX,
                'billing_cycle'   => Subscription::BILLING_MONTHLY
            ]);
        }
    }
}

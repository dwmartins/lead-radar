<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\User;
use App\Models\Plan;
use App\Models\PlanPrice;
use App\Models\Transaction;
use Carbon\Carbon;
use Exception;

class SubscriptionService
{
    /**
     * Cria uma nova assinatura para um usuário manualmente
     *
     * @param User $user
     * @param PlanPrice $planPrice
     * @param string $expires_at
     * @param string $status
     * @param string $note
     * @return Subscription
     * 
     * @throws Exception
     */
    public static function createManual(
        User $user,
        PlanPrice $planPrice,
        ?string $expires_at = null,
        ?string $status = null,
        ?string $note = null,
        string $payment_status,
        ?string $payment_method,
        ?string $payment_paid_at,
    ): Subscription
    {
        // Se não vier expires_at, calcula automaticamente
        if (!$expires_at) {
            $expires_at = now()->addMonths($planPrice->interval_count);
        } else {
            $expires_at = Carbon::parse($expires_at);
        }

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $planPrice->plan_id,
            'status' => $status ?? Subscription::STATUS_ACTIVE,
            'billing_cycle' => $planPrice->interval, 

            'expires_at' => $expires_at,
            'is_manual' => true,
            'note' => $note
        ]);

        Transaction::create([
            'subscription_id'        => $subscription->id,
            'plan_price_id'          => $planPrice->id,
            'amount'                 => $planPrice->price,
            'currency'               => $planPrice->currency,
            'status'                 => $payment_status,
            'payment_gateway'        => Transaction::GATEWAY_MANUAL,
            'payment_method'         => $payment_method ?? null,
            'gateway_transaction_id' => 'manual_' . uniqid(),
            'paid_at'                => $payment_paid_at ?? null,
            'meta'                   => [
                'source' => 'manual payment'
            ]
        ]);

        return $subscription;
    }
}
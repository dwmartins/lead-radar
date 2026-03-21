<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\User;
use App\Models\Plan;
use App\Models\PlanPrice;
use Carbon\Carbon;
use Exception;

class SubscriptionService
{
    /**
     * Cria uma nova assinatura para um usuário
     *
     * @param User $user
     * @param Plan $plan
     * @param string $status
     * @return Subscription
     * @throws Exception
     */
    public static function createManual(User $user, Plan $plan, string $status = Subscription::STATUS_ACTIVE)
    {
        
    }
}
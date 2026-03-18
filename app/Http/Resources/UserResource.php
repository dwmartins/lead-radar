<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $subscription = $this->relationLoaded('activeSubscription')
            ? $this->activeSubscription
            : $this->getActiveSubscription();

        $plan = $subscription?->plan;
        
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'last_name'         => $this->last_name,
            'full_name'         => $this->full_name,
            'email'             => $this->email,
            'phone'             => $this->phone,
            'role'              => $this->role,
            'account_status'    => $this->account_status,
            'avatar_url'        => $this->avatar_url,
            'email_verified_at' => $this->email_verified_at,

            'plan' => $plan ? [
                'id'                   => $plan->id,
                'name'                 => $plan->name,
                'monthly_search_limit' => $plan->monthly_search_limit,
                'price'                => $plan->price,
                'formatted_price'      => $plan->formatted_price,
            ] : null,

            'monthly_statistics' => $this->when(
                $request->boolean('with_monthly_statistics'),
                fn () => [
                    'leads_captured' => $this->leadsCapturedThisMonth(),
                    'searches_used'  => $this->searchesUsedThisMonth(),
                    'search_limit'   => $this->monthlySearchLimit(),
                    'remaining'      => $this->remainingSearches(),
                    'percent'        => $this->monthlySearchLimit() > 0
                        ? round(($this->searchesUsedThisMonth() / $this->monthlySearchLimit()) * 100, 1)
                        : 0,
                ]
            ),
            'created_at' => $this->created_at,
        ];
    }
}

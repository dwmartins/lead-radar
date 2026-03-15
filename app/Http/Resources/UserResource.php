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
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'last_name'  => $this->last_name,
            'full_name'  => $this->full_name,
            'email'      => $this->email,
            'role'       => $this->role,
            'avatar_url' => $this->avatar_url,
            'plan'       => $this->whenLoaded('plan', fn () => [
                'id'                   => $this->plan->id,
                'name'                 => $this->plan->name,
                'monthly_search_limit' => $this->plan->monthly_search_limit,
                'price'                => $this->plan->price,
                'formatted_price'      => $this->plan->formatted_price,
            ]),
            'stats' => $this->when(!$this->isAdmin(), fn () => [
                'leads_captured'  => $this->leadsCapturedThisMonth(),
                'searches_used'  => $this->searchesUsedThisMonth(),
                'search_limit' => $this->monthlySearchLimit(),
                'remaining'   => $this->remainingSearches(),
                'percent'     => $this->monthlySearchLimit() > 0
                    ? round(($this->searchesUsedThisMonth() / $this->monthlySearchLimit()) * 100, 1)
                    : 0,
            ]),
            'created_at' => $this->created_at,
        ];
    }
}

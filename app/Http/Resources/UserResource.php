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
                'id'                  => $this->plan->id,
                'name'                => $this->plan->name,
                'monthly_leads_limit' => $this->plan->monthly_leads_limit,
                'price'               => $this->plan->price,
                'formatted_price'     => $this->plan->formatted_price,
            ]),
            'stats' => $this->when(!$this->isAdmin(), fn () => [
                'leads_used'  => $this->leadsUsedThisMonth(),
                'leads_limit' => $this->monthlyLeadsLimit(),
                'remaining'   => $this->remainingLeads(),
                'percent'     => $this->monthlyLeadsLimit() > 0
                    ? round(($this->leadsUsedThisMonth() / $this->monthlyLeadsLimit()) * 100, 1)
                    : 0,
            ]),
            'created_at' => $this->created_at,
        ];
    }
}

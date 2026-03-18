<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Lead;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Retorna os dados da dashboard do admin
     */
    public function AdminDashboard(Request $request)
    {
        $now = now();
        $request->merge(['with_monthly_statistics' => true]);

        $top_users = User::where('role', User::ROLE_USER)
            ->with(['activeSubscription.plan'])
            ->withCount(['leads as leads_this_month' => fn ($q) =>
                $q->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)])
            ->orderByDesc('leads_this_month')
            ->limit(10)
            ->get();

        return response()->json([
            'stats' => [
                'total_users'      => User::where('role', 'user')->count(),
                'total_leads'      => Lead::count(),
                'leads_this_month' => Lead::whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->count(),
                'active_plans'     => Plan::where('is_active', true)->count(),
            ],
            'top_users' => UserResource::collection($top_users),
        ]);
    }
}

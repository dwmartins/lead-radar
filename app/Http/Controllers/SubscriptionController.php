<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscriptionRequest;
use App\Models\Plan;
use App\Models\PlanPrice;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Obtém uma lista pagina de usuários.
     * 
     * Filtros disponíveis:
     * - id: Id da subscription
     * - status: Status da subscription
     * - plan_id: filtra por Plano
     * - expires_at: Data de expiração
     * - user_id: Id do usuário
     * 
     * Paginação:
     * - perPage: Número de itens port página (padrão: 7).
     * - page: numero da página (padrão: 1);
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->query('perPage', 7);
        $page    = $request->query('page', 1);

        $filters = $request->only([
            'id',
            'status',
            'plan_id',
            'expires_at',
            'user_id'
        ]);

        $query = Subscription::with([
            'plan',
            'user:id,name,last_name,email'
        ]);

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }

        $query->orderBy('id', 'desc');

        $subscriptions = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $subscriptions->items(),
            'pagination' => [
                'current_page' => $subscriptions->currentPage(),
                'last_page' => $subscriptions->lastPage(),
                'per_page' => $subscriptions->perPage(),
                'total' => $subscriptions->total(),
            ]
        ]);
    }

    /**
     * Registra uma nova subscription manual
     * 
     * @param SubscriptionRequest
     */
    public function store(SubscriptionRequest $request): JsonResponse
    {
        $data = $request->validated();

        $planPrice = PlanPrice::with('plan')
            ->where('id', $data['plan_price_id'])
            ->where('is_active', true)
            ->first();

        if(!$planPrice) {
            return response()->json([
                'message' => 'Preço do plano não encontrado.'
            ], 404);
        }

        /** @var User|null $user */
        $user = User::where('id', $data['user_id'])
            ->where('account_status', true)
            ->first();

        if(!$user) {
            return response()->json([
                'message' => 'Usuário não encontrado.'
            ], 404);
        }

        if($user->hasActiveSubscription()) {
            return response()->json([
                'message' => 'Esse usuário já possui assinatura ativa.'
            ], 403);
        }

        try {
            $subscription = SubscriptionService::createManual(
                user: $user,
                planPrice: $planPrice,
                expires_at: $data['expires_at'] ?? null,
                status: $data['status'] ?? null,
                note: $data['note'] ?? null,
                payment_status: $data['payment_status'],
                payment_method: $data['payment_method'],
                payment_paid_at: $data['payment_paid_at'],
            );

            return response()->json([
                'message' => 'Assinatura criada com sucesso.',
                'subscription' => $subscription
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 403);
        }

        $subscription = Subscription::create($data);

        return response()->json([
            'messages' => 'Assinatura criada com sucesso.',
            'subscription' => $subscription
        ]);
    }
}

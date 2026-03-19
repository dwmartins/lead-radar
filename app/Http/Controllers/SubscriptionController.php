<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
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
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Obtém uma lista pagina de usuários.
     * 
     * Filtros disponíveis:
     * - keyword: termo de pesquisa aplicado aos campos de nome, last_name e e-mail.
     * - account_status: filtrar por status da conta (0 ou 1).
     * - plan_id: filtra por Plano
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

        $filters = $request([
            'keyword',
            'account_status',
            'plan_id'
        ]);

        $query = User::whereRole(User::ROLE_USER)
            ->with('plan')
            ->withCount('leads');
        
        if(isset($filters['keyword']) && !empty($filters['keyword'])) {
            $search = $filters['keyword'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhereRaw("CONCAT(name, ' ', last_name) LIKE ?", ["%$search%"]);
            });
        }

        if(isset($filters['account_status']) && $filters['account_status'] !== '') {
            $query->whereAccountStatus((int) $filters['account_status']);
        }

        if (!empty($filters['plan_id'])) {
            $query->whereHas('plan', function ($q) use ($filters) {
                $q->where('plan_id', (int) $filters['plan_id']);
            });
        }

        $query->orderBy('created_at', 'desc');

        $users = $query->paginate($perPage, ['*'], 'page', $page);
        
        return response()->json([
            'data' => $users->items(),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ]
        ]);
    }

    /**
     * Registra um novo usuário.
     * 
     * @param UserRequest
     */
    public function store(UserRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = new User($data);
        $user->account_status = $data['account_status'];
        $user->role = User::ROLE_USER;
        $user->password = $data['password'];
        $user->plan_id = $data['plan_id'] ?? null;
        $user->save();

        return response()->json([
            'message' => __('messages.user_successfully_registered'),
            'data' => $user
        ], 201);
    }

    /**
     * Atualiza um usuário existente.
     * 
     * @param UserRequest
     */
    public function update(UserRequest $request): JsonResponse
    {
        $data = $request->validated();

        /** @var User|null $user */
        $user = User::find($data['id']);

        if(!$user) {
            return response()->json([
                'message' => __('messages.user_not_found')
            ], 404);
        }

        $user->fill($data);
        $user->account_status = $data['account_status'];
        $user->plan_id = $data['plan_id'];

        if(!empty($data['password'])) {
            $user->password = $data['password'];
        }

        $user->save();

        return response()->json([
            'message' => __('messages.user_updated_successfully'),
            'data' => $user
        ], 201);
    }

    /**
     * Exclui um usuário existente.
     * 
     * @param int $id do usuário.
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        /** @var User|null $user */
        $user = User::find((int) $id);

        if(!$user) {
            return response()->json([
                'message' => __('messages.user_not_found')
            ], 404);
        }

        $user->deleteAvatar();
        $user->delete();

        return response()->json([
            'message' => __('messages.user_successfully_deleted')
        ], 200);
    }

    /**
     * Retorna um usuário pelo id
     */
    public function getById(Request $request, $id): JsonResponse
    {
        $user = User::with(['plan'])->find($id);

        if(!$user) {
            return response()->json([
                'message' => __('messages.user_not_found')
            ], 404);
        }

        return response()->json($user);
    }
}

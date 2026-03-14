<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Realiza a autenticação.
     * 
     * Campos esperados:
     * - email (string)
     * - password (string)
     * - remember_me (boolean, opcional)
     * 
     * @param LoginRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $throttleKey = Str::lower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'message' => __('auth.throttle', ['throttle' => $seconds])
            ], 429);
        }

        $credentials = $request->only('email', 'password');

        if(!Auth::attempt($credentials, $request->boolean('remember_me'))) {
            RateLimiter::hit($throttleKey);

            return response()->json([
                'message' => __('auth.failed')
            ], 401);
        }

        RateLimiter::clear($throttleKey);

        /** @var User $user */
        $user = Auth::user();

        if(!$user->isActive()) {
            $this->forceLogout($request);

            return response()->json([
                'message' => __('auth.inactive_user')
            ]);
        }

        $request->session()->regenerate();
        $user->updateLastLogin();

        return response()->json([
            'message' => __('auth.login_successful'),
            'user' => $user
        ]);
    }

    /**
     * Valida se o token Sanctum é válido e retorna o usuário autenticado.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validate(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if(!$user || !$user->isActive()) {
            $this->forceLogout($request);

            return response()->json([
                'message' => __('auth.invalid_session'),
                'is_valid' => false,
                'force_logout' => true
            ], 401);
        }

        return response()->json([
            'message'  => __('auth.authenticated'),
            'is_valid' => true,
            'user'     => $user
        ]);
    }

    /**
     * Realiza o logout
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        // Revoga tokens (mobile / API)
        if ($user && method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }

        if ($request->hasSession()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
        
        return response()->json([
            'message' => __('auth.logout_successful'),
            'force_logout' => true
        ]);
    }

    /**
     * Força o logout
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function forceLogout(Request $request): void
    {
        /** @var User $user */
        $user = $request->user();

        // Revoga tokens (mobile / API)
        if ($user && method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }

        if ($request->hasSession()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
    }
}

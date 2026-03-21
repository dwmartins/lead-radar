<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    /**
     * Retorna todos os planos e suas features;
     * 
     * @param Request
     */
    public function index(Request $request): JsonResponse
    {
        $plans = Plan::with('features', 'prices')->get();
        return response()->json($plans);
    }
}

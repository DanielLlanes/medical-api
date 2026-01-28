<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Landlord\BaseLandlordController;
use App\Models\Landlord\Plan;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanController extends BaseLandlordController
{
    use ApiResponseTrait;

    public function index(): JsonResponse
    {
        return response()->json([]);
    }

    public function store(Request $request): JsonResponse
    {
        return response()->json([]);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json([]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        return response()->json([]);
    }

    public function destroy(int $id): JsonResponse
    {
        return response()->json(null, 204);
    }
    public function getActivePlans(): JsonResponse
    {
        // Traemos solo lo necesario para la landing (ID, nombre, precios, slug, características)
        $plans = Plan::where('is_active', true)->get();

        return $this->sendResponse(
            $plans,
            'Tarifario actualizado recuperado.',
            200 // Cambiado a 200: Es una consulta exitosa, no una creación.
        );
    }
}

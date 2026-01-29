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
}

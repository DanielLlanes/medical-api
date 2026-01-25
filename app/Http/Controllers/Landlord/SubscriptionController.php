<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Landlord\BaseLandlordController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends BaseLandlordController
{
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
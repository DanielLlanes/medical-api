<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/debug-cluster', function () {
    return response()->json([
        'instancia' => env('APP_INSTANCE'),
        'timestamp' => now()->toDateTimeString(),
        'puerto_minio' => env('AWS_ENDPOINT')
    ]);
});
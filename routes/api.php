<?php

use App\Http\Controllers\Api\AuthTokenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthTokenController::class, 'store']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthTokenController::class, 'me']);
        Route::post('/logout', [AuthTokenController::class, 'destroy']);
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


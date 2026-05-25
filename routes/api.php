<?php

use App\Http\Controllers\Api\AuthTokenController;
use App\Http\Controllers\Api\BackupController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\QuranDatabaseController;
use App\Http\Controllers\Api\SubscriptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthTokenController::class, 'register']);
    Route::post('/login', [AuthTokenController::class, 'store']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthTokenController::class, 'me']);
        Route::post('/logout', [AuthTokenController::class, 'destroy']);
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->prefix('profile')->group(function () {
    Route::get('/', [ProfileController::class, 'show']);
    Route::put('/', [ProfileController::class, 'update']);

    Route::get('/settings', [ProfileController::class, 'settings']);
    Route::put('/settings', [ProfileController::class, 'updateSettings']);
});

Route::middleware('auth:sanctum')->prefix('subscription')->group(function () {
    Route::get('/', [SubscriptionController::class, 'show']);
    Route::post('/activate', [SubscriptionController::class, 'activate']);
    Route::post('/cancel', [SubscriptionController::class, 'cancel']);
});

Route::middleware(['auth:sanctum', 'subscription.active'])->prefix('backups')->group(function () {
    Route::get('/', [BackupController::class, 'index']);
    Route::post('/', [BackupController::class, 'store']);
    Route::post('/{backup}/restore', [BackupController::class, 'restore']);
});

Route::middleware('auth:sanctum')->prefix('quran')->group(function () {
    Route::get('/db/download', [QuranDatabaseController::class, 'download']);
});

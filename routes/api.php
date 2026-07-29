<?php

use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TokenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.')->group(function () {
    Route::post('/auth/token', [TokenController::class, 'store'])->name('auth.token');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', fn (Request $request) => $request->user()->only(['id', 'name', 'email', 'role']))->name('user');

        Route::apiResource('products', ProductController::class);
        Route::apiResource('customers', CustomerController::class);
        Route::apiResource('orders', OrderController::class)->only(['index', 'store', 'show']);
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    });
});

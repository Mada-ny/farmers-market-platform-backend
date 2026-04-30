<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\FarmerController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\RepaymentController;
use App\Http\Controllers\Api\V1\TransactionController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Auth (public)
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('logout', [AuthController::class, 'logout'])
            ->middleware('auth:sanctum');
    });

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // Users — Admin manages supervisors, Supervisor manages operators
        Route::apiResource('users', UserController::class)
            ->middleware('role:admin,supervisor');

        // Categories & Products — Admin + Supervisor write; all roles read
        Route::get('categories', [CategoryController::class, 'index'])->middleware('role:admin,supervisor,operator');
        Route::post('categories', [CategoryController::class, 'store'])->middleware('role:admin,supervisor');
        Route::get('categories/{category}', [CategoryController::class, 'show'])->middleware('role:admin,supervisor,operator');
        Route::put('categories/{category}', [CategoryController::class, 'update'])->middleware('role:admin,supervisor');
        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->middleware('role:admin,supervisor');

        Route::get('products', [ProductController::class, 'index'])->middleware('role:admin,supervisor,operator');
        Route::post('products', [ProductController::class, 'store'])->middleware('role:admin,supervisor');
        Route::get('products/{product}', [ProductController::class, 'show'])->middleware('role:admin,supervisor,operator');
        Route::put('products/{product}', [ProductController::class, 'update'])->middleware('role:admin,supervisor');
        Route::delete('products/{product}', [ProductController::class, 'destroy'])->middleware('role:admin,supervisor');

        // Farmers — Operator only
        Route::apiResource('farmers', FarmerController::class)
            ->middleware('role:operator');
        Route::get('farmers/{farmer}/debts', [FarmerController::class, 'debts'])
            ->middleware('role:operator,supervisor');

        // Transactions — Operator writes, Operator+Supervisor reads
        Route::get('transactions', [TransactionController::class, 'index'])->middleware('role:operator,supervisor');
        Route::post('transactions', [TransactionController::class, 'store'])->middleware('role:operator');
        Route::get('transactions/{transaction}', [TransactionController::class, 'show'])->middleware('role:operator,supervisor');

        // Repayments — Operator writes, Operator+Supervisor reads
        Route::get('repayments', [RepaymentController::class, 'index'])->middleware('role:operator,supervisor');
        Route::post('repayments', [RepaymentController::class, 'store'])->middleware('role:operator');
        Route::get('repayments/{repayment}', [RepaymentController::class, 'show'])->middleware('role:operator,supervisor');
    });
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public product routes (no auth required)
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/search', [ProductController::class, 'search']);
    Route::get('/{id}', [ProductController::class, 'show']);
});

// Protected routes
Route::middleware('auth:api')->group(function () {
    
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [UserController::class, 'me']);
    
    // User management (admin only)
    Route::middleware('admin')->prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });
    
    // Product admin routes (require auth + admin)
    Route::prefix('products')->middleware('admin')->group(function () {
        Route::post('/', [ProductController::class, 'store']);
        Route::put('/{id}', [ProductController::class, 'update']);
        Route::delete('/{id}', [ProductController::class, 'destroy']);
        Route::put('/{id}/stock', [ProductController::class, 'updateStock']);
        Route::get('/statistics/overview', [ProductController::class, 'statistics']);
    });
    
    // Transaction routes
    Route::prefix('transactions')->group(function () {
        // User transaction routes
        Route::post('/', [TransactionController::class, 'store']);
        Route::get('/my-transactions', [TransactionController::class, 'myTransactions']);
        Route::get('/{id}', [TransactionController::class, 'show']);
        Route::put('/{id}/cancel', [TransactionController::class, 'cancel']); // cancel by user/admin

        // Admin only transaction routes
        Route::middleware('admin')->group(function () {
            Route::get('/', [TransactionController::class, 'index']);
            Route::put('/{id}/status', [TransactionController::class, 'update']); // update status
            Route::get('/statistics/overview', [TransactionController::class, 'statistics']);
            Route::delete('/{id}', [TransactionController::class, 'destroy']); // delete by admin
        });
    });
    
    // Example endpoint
    Route::get('/province', function () {
        return response()->json([
            'status' => 'success',
            'data' => ['Jawa Barat', 'Jawa Tengah', 'Jawa Timur']
        ]);
    });
});

// Fallback for 404
Route::fallback(function () {
    return response()->json([
        'status' => 'error',
        'message' => 'Route not found'
    ], 404);
});

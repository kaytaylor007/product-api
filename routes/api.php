<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\RetailChainController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/password/forgot', [AuthController::class,'forgotPassword']);
Route::post('/password/reset', [AuthController::class,'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'profile']);

    Route::get('/chains', [RetailChainController::class, 'index']);
    Route::get('/products/gtin/{gtin}', [ProductController::class,'showByGtin']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::post('/products', [ProductController::class,'store']);
    Route::put('/products/{id}', [ProductController::class,'update']);
    Route::get('/products', [ProductController::class,'index']);
    Route::get('/products/{productId}/prices', [PriceController::class,'listForProduct']);
    Route::post('/prices', [PriceController::class,'store']);

    Route::get('/prices/pending', [PriceController::class, 'pendingList'])->middleware('role:moderator,admin');
    Route::post('/prices/{id}/approve', [PriceController::class, 'approve'])->middleware('role:moderator,admin');
    Route::post('/prices/{id}/reject', [PriceController::class, 'reject'])->middleware('role:moderator,admin');
});

// <?php

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

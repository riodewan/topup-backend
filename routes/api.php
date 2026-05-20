<?php

use App\Http\Controllers\API\Admin\GameController as AdminGameController;
use App\Http\Controllers\API\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\API\Admin\ProductController as AdminProductController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\GameController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — KurenStore Backend
|--------------------------------------------------------------------------
*/

// ─── Health Check ─────────────────────────────────────────────────────────────
Route::get('/ping', fn () => response()->json(['success' => true, 'message' => 'API Running ✓']));

// ─── Authentication ────────────────────────────────────────────────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // ─── Orders (User History) ─────────────────────────────────────────────────
    Route::get('/orders', [OrderController::class, 'index']); // GET /api/orders (Wajib Login)
});

// ─── Orders (Public/Guest Checkout & Details) ───────────────────────────────
Route::prefix('orders')->group(function () {
    Route::post('/',             [OrderController::class, 'store']);   // POST   /api/orders
    Route::get('/{id}',          [OrderController::class, 'show']);    // GET    /api/orders/{id}
    Route::patch('/{id}/cancel', [OrderController::class, 'cancel']); // PATCH  /api/orders/{id}/cancel
});

// ─── Public: Games ────────────────────────────────────────────────────────────
Route::prefix('games')->group(function () {
    Route::get('/',       [GameController::class, 'index']);   // GET /api/games
    Route::get('/{slug}', [GameController::class, 'show']);    // GET /api/games/{slug}
});

// ─── Public: Products ─────────────────────────────────────────────────────────
Route::get('/products', [ProductController::class, 'index']);  // GET /api/products?game_id=X

// ─── Admin Panel ──────────────────────────────────────────────────────────────
Route::middleware(['auth:sanctum', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Games CRUD
        Route::apiResource('games', AdminGameController::class);

        // Products CRUD
        Route::apiResource('products', AdminProductController::class);

        // Orders (read + status update)
        Route::get('/orders',                   [AdminOrderController::class, 'index']);        // GET   /api/admin/orders
        Route::get('/orders/{id}',              [AdminOrderController::class, 'show']);         // GET   /api/admin/orders/{id}
        Route::patch('/orders/{id}/status',     [AdminOrderController::class, 'updateStatus']); // PATCH /api/admin/orders/{id}/status
    });
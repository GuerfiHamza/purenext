<?php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\RawMaterialController;
use App\Http\Controllers\Api\RecipeController;
use App\Http\Controllers\Api\RecipePackagingController;
use App\Http\Controllers\Api\ProductionSimulatorController;
use App\Http\Controllers\Api\ProductionRunController;
use App\Http\Controllers\Api\FinishedGoodController;
use App\Http\Controllers\Api\SalesOrderController;
use App\Http\Controllers\Api\StockMovementController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RecipeIngredientController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\NotificationController;

use Illuminate\Support\Facades\Route;
// Routes publiques
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::post('notifications/{notification}/read', [NotificationController::class, 'markRead']);
    Route::post('notifications/read-all', [NotificationController::class, 'markAllRead']);
    Route::delete('notifications/{notification}', [NotificationController::class, 'destroy']);
Route::post('push-token', function (\Illuminate\Http\Request $request) {
    $request->validate(['token' => 'required|string']);
Route::get('/production/suggest-lot-number', [ProductionRunController::class, 'suggestLotNumber']);
    \App\Models\PushToken::updateOrCreate(
        ['user_id' => auth()->id()],
        ['token' => $request->token, 'platform' => 'android']
    );

    return response()->json(['message' => 'Token saved.']);
});
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'me']);
    Route::get('dashboard', [DashboardController::class, 'index']);

    // ─── Marques ─────────────────────────────────────────────
    Route::apiResource('brands', BrandController::class);

    // ─── Fournisseurs ─────────────────────────────────────────
    Route::apiResource('suppliers', SupplierController::class);
    Route::get('dashboard/charts', [DashboardController::class, 'charts']);
    // ─── Matières Premières ───────────────────────────────────
    Route::apiResource('raw-materials', RawMaterialController::class);
    Route::post('raw-materials/{rawMaterial}/adjust-stock', [RawMaterialController::class, 'adjustStock']);
    Route::apiResource('recipe-ingredients', RecipeIngredientController::class);

    // ─── Recettes ─────────────────────────────────────────────
    Route::apiResource('recipes', RecipeController::class);
    Route::get('recipes/{recipe}/packaging-options', [RecipeController::class, 'packagingOptions']);

    // ─── Packaging ────────────────────────────────────────────
    Route::apiResource('recipe-packaging', RecipePackagingController::class);

    // ─── Simulateur ───────────────────────────────────────────
    Route::post('production/simulate', [ProductionSimulatorController::class, 'simulate']);

    // ─── Productions ──────────────────────────────────────────
    Route::post('production/launch', [ProductionRunController::class, 'launch']);
    Route::post('production/{productionRun}/complete', [ProductionRunController::class, 'complete']);
    Route::apiResource('production', ProductionRunController::class);

    // ─── Produits Finis ───────────────────────────────────────
    Route::apiResource('finished-goods', FinishedGoodController::class);

    // ─── Ventes ───────────────────────────────────────────────
    Route::post('sales-orders/{salesOrder}/deliver', [SalesOrderController::class, 'deliver']);
    Route::apiResource('sales-orders', SalesOrderController::class);

    // ─── Mouvements Stock (lecture seule) ─────────────────────
    Route::get('stock-movements', [StockMovementController::class, 'index']);
    Route::get('stock-movements/{stockMovement}', [StockMovementController::class, 'show']);

    // ─── Routes réservées par rôle ────────────────────────────
    Route::middleware('role:gerant')->group(function () {
        Route::get('settings', [SettingsController::class, 'index']);
        Route::post('settings', [SettingsController::class, 'update']);
        Route::delete('brands/{brand}', [BrandController::class, 'destroy']);
        Route::delete('suppliers/{supplier}', [SupplierController::class, 'destroy']);
        Route::delete('raw-materials/{rawMaterial}', [RawMaterialController::class, 'destroy']);
        Route::delete('recipes/{recipe}', [RecipeController::class, 'destroy']);
        Route::get('users', [UserController::class, 'index']);
        Route::post('users', [UserController::class, 'store']);
        Route::delete('users/{user}', [UserController::class, 'destroy']);
    });
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\HistoryController;
use Illuminate\Http\Request;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', static function (Request $request) {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Вы вышли']);
    });

    Route::post('/purchase', [PurchaseController::class, 'purchase']);
    Route::post('/rental', [RentalController::class, 'rent']);
    Route::post('/rental/extend', [RentalController::class, 'extend']);

    Route::get('/purchase/status/{id}', [StatusController::class, 'purchaseStatus']);
    Route::get('/rental/status/{id}', [StatusController::class, 'rentalStatus']);

    Route::get('/history', [HistoryController::class, 'index']);
});

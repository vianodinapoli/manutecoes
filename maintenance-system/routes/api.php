<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\MachineController;
use App\Http\Controllers\Api\RequisitionController;

// Autenticação
Route::post('/login', [AuthController::class, 'login']);

// Rotas protegidas
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn(Request $request) => $request->user());
    Route::post('/logout', [AuthController::class, 'logout']);

    // Stock
    Route::get('/stock', [StockController::class, 'index']);
    Route::get('/stock/{id}', [StockController::class, 'show']);

    // Máquinas
    Route::get('/machines', [MachineController::class, 'index']);
    Route::get('/machines/{id}', [MachineController::class, 'show']);

    // Requisições
    Route::get('/requisitions', [RequisitionController::class, 'index']);
    Route::get('/requisitions/{id}', [RequisitionController::class, 'show']);
});
<?php
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\DischargeApiController;

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

Route::post('/login', [AuthApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/operations', [DischargeApiController::class, 'operations']);
    Route::get('/operations/{operation}', [DischargeApiController::class, 'operation']);
    Route::post('/discharges', [DischargeApiController::class, 'store']);
});

Route::middleware('auth:sanctum')->get('/discharges/{discharge}', [\App\Http\Controllers\Api\DischargeShowApiController::class, 'show']);

Route::middleware('auth:sanctum')->get('/operations/{operation}/discharges', [\App\Http\Controllers\Api\DischargeListApiController::class, 'index']);

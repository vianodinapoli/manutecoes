<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\StockItemController;
use App\Http\Controllers\MaterialPurchaseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\FuelController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\RequisitionController;
use App\Http\Controllers\DischargeController;
use App\Http\Controllers\MovimentoArmazemController;
use App\Http\Controllers\ViaturaController;
use App\Http\Controllers\Admin\BackupController;

// ── Página inicial ──────────────────────────────────────────
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : view('auth/login');
});

// ── Dashboard ───────────────────────────────────────────────
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'permission:acesso dashboard'])
    ->name('dashboard');

// ── Rotas protegidas ────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Perfil (sem restrição — todos os utilizadores)
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Download de ficheiros de manutenção (sem restrição)
    Route::get('/download-file/{id}', function ($id) {
        $file = \App\Models\MaintenanceFile::findOrFail($id);
        return response()->file(storage_path('app/public/' . $file->filepath));
    })->name('file.download');

    // ── Equipamentos / Máquinas ─────────────────────────────
    Route::middleware('permission:acesso equipamentos')->group(function () {
        Route::resource('machines', MachineController::class);
        Route::get('exportar-maquinas/export', [MachineController::class, 'export'])->name('machines.export');
        Route::get('maintenances/create-from-machine/{machine}', [MaintenanceController::class, 'createFromMachine'])
            ->name('maintenances.createFromMachine');
    });

    // ── Manutenções ─────────────────────────────────────────
    Route::middleware('permission:acesso manutencoes')->group(function () {
        Route::resource('maintenances', MaintenanceController::class);
    });

    // ── Stock ───────────────────────────────────────────────
    Route::middleware('permission:acesso stock')->group(function () {
        Route::resource('stock-items', StockItemController::class);
        Route::get('exportar-inventario', [StockItemController::class, 'export'])->name('stock-items.export');
    });

    // ── Movimentos de Armazém ───────────────────────────────
    Route::middleware('permission:acesso movimentos')->group(function () {
        Route::get('/movimentos/pdf',            [MovimentoArmazemController::class, 'exportPdf']) ->name('movimentos.pdf');
        Route::get('/movimentos',                [MovimentoArmazemController::class, 'index'])     ->name('movimentos.index');
        Route::post('/movimentos',               [MovimentoArmazemController::class, 'store'])     ->name('movimentos.store');
        Route::put('/movimentos/{movimento}',    [MovimentoArmazemController::class, 'update'])    ->name('movimentos.update');
        Route::delete('/movimentos/{movimento}', [MovimentoArmazemController::class, 'destroy'])   ->name('movimentos.destroy');
        Route::get('/api/produtos/{produto}/stock', [MovimentoArmazemController::class, 'stockAtual'])->name('api.produto.stock');
    });

    // ── Viaturas ────────────────────────────────────────────
    Route::middleware('permission:acesso viaturas')->group(function () {
        Route::get('/viaturas/export', [ViaturaController::class, 'export'])->name('viaturas.export');
        Route::delete('/viaturas/documento/{documento}', [ViaturaController::class, 'destroyDocumento'])->name('viaturas.documento.destroy');
        Route::resource('viaturas', ViaturaController::class)->only(['index', 'store', 'update', 'destroy']);
    });

    // ── Pedidos / Requisições ───────────────────────────────
    Route::middleware('permission:acesso pedidos')->group(function () {
        Route::patch('compras/items/{item}/status', [MaterialPurchaseController::class, 'updateItemStatus'])->name('compras.items.status');
        Route::resource('compras', MaterialPurchaseController::class);
        Route::patch('compras/{compra}/status', [MaterialPurchaseController::class, 'updateStatus'])->name('compras.status');
        Route::resource('suppliers', SupplierController::class);
        Route::get('/requisicoes',              [RequisitionController::class, 'index'])   ->name('requisicoes.index');
        Route::get('/requisicoes/novo',         [RequisitionController::class, 'create'])  ->name('requisicoes.create');
        Route::post('/requisicoes/store',       [RequisitionController::class, 'store'])   ->name('requisicoes.store');
        Route::get('/requisicoes/{id}/json',    [RequisitionController::class, 'showJson'])->name('requisicoes.json');
        Route::get('/requisicoes/{id}/pdf',     [RequisitionController::class, 'gerarPdf'])->name('requisicoes.pdf');
        Route::delete('/requisicoes/{requisicao}', [RequisitionController::class, 'destroy'])->name('requisicoes.destroy');
        Route::get('/requisicoes/{id}/edit',    [RequisitionController::class, 'edit'])    ->name('requisicoes.edit');
        Route::put('/requisicoes/{id}',         [RequisitionController::class, 'update'])  ->name('requisicoes.update');
    });

    // ── Combustível ─────────────────────────────────────────
    Route::middleware('permission:acesso combustivel')->group(function () {
        Route::get('/combustivel',                [FuelController::class, 'index'])          ->name('fuel.index');
        Route::post('/combustivel',               [FuelController::class, 'store'])          ->name('fuel.store');
        Route::post('/combustivel/entrada',       [FuelController::class, 'storeEntry'])     ->name('fuel.storeEntry');
        Route::post('/combustivel/store',         [FuelController::class, 'store'])          ->name('fuel.store.alt');
        Route::post('/combustivel/entry',         [FuelController::class, 'storeEntry'])     ->name('fuel.entry.store');
        Route::post('/combustivel/tanks/store',   [FuelController::class, 'storeTank'])      ->name('fuel.tanks.store');
        Route::post('/combustivel/fuel-settlement',[FuelController::class, 'storeSettlement'])->name('fuel.settlement.store');
        Route::get('/fuel-log/{id}/edit',         [FuelController::class, 'editLog'])        ->name('fuel.log.edit');
        Route::put('/fuel-log/{id}',              [FuelController::class, 'updateLog'])      ->name('fuel.log.update');
        Route::delete('/fuel-log/{id}',           [FuelController::class, 'destroyLog'])     ->name('fuel.log.destroy');
        Route::get('/fuel-entry/{id}/edit',       [FuelController::class, 'editEntry'])      ->name('fuel.entry.edit');
        Route::put('/fuel-entry/{id}',            [FuelController::class, 'updateEntry'])    ->name('fuel.entry.update');
        Route::delete('/fuel-entry/{id}',         [FuelController::class, 'destroyEntry'])   ->name('fuel.entry.destroy');
        Route::get('/fuel-log/{id}/json',         [FuelController::class, 'getLogJson'])     ->name('fuel.log.json');
        Route::get('/fuel-entry/{id}/json',       [FuelController::class, 'getEntryJson'])   ->name('fuel.entry.json');
    });

    // ── Caixa e Bancos ──────────────────────────────────────
    Route::middleware('permission:acesso caixa')->group(function () {
        Route::get('/caixa-bancos', fn() => view('caixa-bancos.index'))->name('caixa.index');
    });

    // ── Discharges ──────────────────────────────────────────
    Route::middleware('permission:acesso discharges')->group(function () {
        Route::resource('discharges', DischargeController::class);
        Route::patch('discharges/{discharge}/confirm', [DischargeController::class, 'confirm'])->name('discharges.confirm');
    });

    // ── Administração (apenas super-admin) ──────────────────
    Route::middleware('role:super-admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users',               [UserController::class, 'index'])       ->name('users.index');
        Route::post('/users',              [UserController::class, 'store'])       ->name('users.store');
        Route::put('/users/{user}',        [UserController::class, 'update'])      ->name('users.update');
        Route::post('/users/{user}/toggle',[UserController::class, 'toggleAdmin']) ->name('users.toggle');
        Route::delete('/users/{user}',     [UserController::class, 'destroy'])     ->name('users.destroy');

        // Backup
    Route::get('/backup',          [BackupController::class, 'index'])   ->name('backup.index');
    Route::get('/backup/download', [BackupController::class, 'download'])->name('backup.download');
    Route::post('/backup/restore', [BackupController::class, 'restore']) ->name('backup.restore');
    });

});

require __DIR__.'/auth.php';
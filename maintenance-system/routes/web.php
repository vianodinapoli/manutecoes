<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// 1. IMPORTAR OS SEUS CONTROLLERS AQUI
use App\Http\Controllers\MachineController; 
use App\Http\Controllers\MaintenanceController; 
use App\Http\Controllers\StockItemController; 
use App\Http\Controllers\MaterialPurchaseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController;


// -------------------------------------------------------------
// ROTAS BASE DO BREEZE
// -------------------------------------------------------------

// Rota de Boas-vindas (Página Inicial)
// Se quiser usar o dashboard como página inicial para utilizadores logados, pode mudar o 'welcome'
Route::get('/', function () {
    // Redireciona utilizadores logados para o Dashboard, caso contrário mostra o 'welcome'
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('auth/login');
});

// Dashboard (Protegido e Verificado)
// Substitui a rota antiga:
// Route::get('/dashboard', function () { return view('dashboard'); })->...

// Por esta nova:
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// -------------------------------------------------------------
// ROTAS PROTEGIDAS DA APLICAÇÃO (Necessita de Login)
// -------------------------------------------------------------

Route::middleware('auth')->group(function () {
    
    // Rotas de Perfil (do Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // ==========================================================
    // SEUS ROTAS DE RECURSOS (ADICIONADAS AGORA)
    // ==========================================================
    
    // 1. Máquinas
    Route::resource('machines', MachineController::class);

    // 2. Stock
    Route::resource('stock-items', StockItemController::class);

    // 3. Manutenções
    Route::resource('maintenances', MaintenanceController::class);
    
    // 4. Rota especial de Manutenção (Se ela ainda existir)
    Route::get('maintenances/create-from-machine/{machine}', [MaintenanceController::class, 'createFromMachine'])
        ->name('maintenances.createFromMachine');


Route::resource('compras', MaterialPurchaseController::class);
// Rota customizada para o select de status
Route::patch('compras/{compra}/status', [MaterialPurchaseController::class, 'updateStatus'])->name('compras.status');


/// Mude para 'exportar-inventario' para garantir que não colide com o resource
Route::get('exportar-inventario', [StockItemController::class, 'export'])->name('stock-items.export');

// Mantenha o resource abaixo
Route::resource('stock-items', StockItemController::class);


// Rota de exportação de máquinas
Route::get('exportar-maquinas/export', [App\Http\Controllers\MachineController::class, 'export'])->name('machines.export');

// Seu resource atual
Route::resource('machines', MachineController::class);



Route::middleware(['auth', 'role:super-admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users/{user}/toggle', [UserController::class, 'toggleAdmin'])->name('users.toggle');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

});

// Rotas de Autenticação do Breeze (Login, Register, Logout, etc.)
require __DIR__.'/auth.php';
<?php
use App\Http\Controllers\MachineController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\StockItemController; // <--- PASSO 1: Importar o Controller
use Illuminate\Support\Facades\Route;


// ROTA ALTERADA: Redireciona a rota raiz ("/") para a lista de máquinas
Route::get('/', [MachineController::class, 'index'])->name('root');


Route::resource('machines', MachineController::class);

Route::resource('maintenances', MaintenanceController::class);

// <--- PASSO 2: Adicionar a Rota de Recurso para Stock Items
// Esta linha cria automaticamente as rotas 'stock-items.index', 'stock-items.create', etc.
Route::resource('stock-items', StockItemController::class); 


// Rotas específicas
Route::get('/machines/{machine}/maintenances/create', [MaintenanceController::class, 'createFromMachine'])
    ->name('machines.maintenances.create');

// NOTA: A rota 'maintenances.createFromMachine' é redundante (verifique a necessidade).
// Route::get('/maintenances/create/{machine}', [MaintenanceController::class, 'createFromMachine'])
//     ->name('maintenances.createFromMachine');
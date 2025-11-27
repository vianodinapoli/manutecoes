<?php
use App\Http\Controllers\MachineController;
use App\Http\Controllers\MaintenanceController;
use Illuminate\Support\Facades\Route;


Route::resource('machines', MachineController::class);

Route::resource('maintenances', MaintenanceController::class);


Route::get('/', function () {
    return view('welcome');
});

Route::get('/machines/{machine}/maintenances/create', [MaintenanceController::class, 'createFromMachine'])
    ->name('machines.maintenances.create');

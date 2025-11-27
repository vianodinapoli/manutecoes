<?php
use App\Http\Controllers\MachineController;
use Illuminate\Support\Facades\Route;


Route::resource('machines', MachineController::class);

Route::get('/', function () {
    return view('welcome');
});

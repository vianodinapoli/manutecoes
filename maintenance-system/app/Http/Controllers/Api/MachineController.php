<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Machine;

class MachineController extends Controller
{
    public function index()
    {
        return response()->json(Machine::with('maintenances')->get());
    }

    public function show($id)
    {
        $machine = Machine::with('maintenances')->findOrFail($id);
        return response()->json($machine);
    }
}

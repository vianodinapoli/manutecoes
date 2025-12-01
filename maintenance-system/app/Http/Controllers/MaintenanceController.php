<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\Machine;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{

public function index()
{
    // Pega todas as máquinas com status 'em manutenção'
    $machinesInMaintenance = \App\Models\Machine::where('status', 'em manutenção')->get();

    return view('maintenances.index', [
        'machines' => $machinesInMaintenance
    ]);
}


    public function create()
    {
        $machines = Machine::all();
        return view('maintenances.form', [
            'maintenance' => new Maintenance(),
            'machines' => $machines,
            'selectedMachine' => null, // Para criação normal
        ]);
    }

    public function edit(Maintenance $maintenance)
    {
        $machines = Machine::all();
        return view('maintenances.form', [
            'maintenance' => $maintenance,
            'machines' => $machines,
            'selectedMachine' => $maintenance->machine_id,
        ]);

        
    }

    public function createFromMachine(Machine $machine)
{
    // Apenas prepara um novo objeto Maintenance sem salvar
    $maintenance = new Maintenance([
        'status' => 'Em manutenção',
        'scheduled_date' => now(),
        'machine_id' => $machine->id,
    ]);

    // Redireciona para o formulário já com máquina pré-selecionada
    return view('maintenances.form', [
        'maintenance' => $maintenance,
        'machines' => Machine::all(),
        'selectedMachine' => $machine->id,
    ])->with('success', 'Preencha os detalhes da manutenção antes de salvar.');
}


public function store(Request $request)
{
    $maintenance = Maintenance::create($request->all());

    // Atualiza o status da máquina com o status da manutenção
    $maintenance->machine->update([
        'status' => $maintenance->status
    ]);

    return redirect()->route('machines.show', $maintenance->machine_id)
                     ->with('success', 'Manutenção criada com sucesso!');
}

public function update(Request $request, Maintenance $maintenance)
{
    $maintenance->update($request->all());

    // Atualiza o status da máquina
    $maintenance->machine->update([
        'status' => $maintenance->status
    ]);

    return redirect()->route('machines.show', $maintenance->machine_id)
                     ->with('success', 'Manutenção atualizada com sucesso!');
}


// app/Http/Controllers/MaintenanceController.php

// ... (outros métodos)

    /**
     * Eliminar um registo de manutenção (APAGAR).
     */
    public function destroy(Maintenance $maintenance)
    {
        // Guardamos o ID da máquina para redirecionar para a página correta
        $machineId = $maintenance->machine_id; 
        
        // Nome da manutenção para a mensagem de sucesso
        $maintenanceId = $maintenance->id; 
        
        $maintenance->delete();

        // Redirecionamos o utilizador de volta para a página de detalhes da máquina
        return redirect()->route('machines.show', $machineId)
                         ->with('success', 'Registo de manutenção ID ' . $maintenanceId . ' eliminado com sucesso!');
    }




}

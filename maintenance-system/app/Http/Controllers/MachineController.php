<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use Illuminate\Http\Request;

class MachineController extends Controller
{
    /**
     * Mostrar a lista de todas as máquinas.
     */
    public function index()
    {
        // Puxa todas as máquinas da base de dados, ordenadas por nome
        $machines = Machine::orderBy('name')->get(); 
        
        return view('machines.index', compact('machines'));
    }

    /**
     * Mostrar o formulário para criar uma nova máquina.
     */
    public function create()
    {
        return view('machines.create');
    }

    /**
     * Guardar uma nova máquina na base de dados.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
        'numero_interno' => 'required|string|max:50|unique:machines,numero_interno',
        'tipo_equipamento' => 'required|string|max:100',
        'marca' => 'nullable|string|max:100',
        'modelo' => 'nullable|string|max:100',
        'localizacao' => 'required|string|max:100',
        'operador' => 'nullable|string|max:100',
        'status' => 'required|in:Operacional,Avariada,Em Manutenção,Desativada',
        'observacoes' => 'nullable|string',
    ]);
        
        // 2. Criação do Registo
        Machine::create($validatedData);

// 3. Redirecionamento (CORREÇÃO DA LINHA 50)
    return redirect()->route('machines.index')
                     ->with('success', 'Máquina "' . $validatedData['numero_interno'] . '" adicionada com sucesso!'); // <-- CORRIGIDO
    }

    

    /**
     * Mostrar os detalhes de uma máquina específica.
     */
    public function show(Machine $machine)

    
    {
     
        // O Laravel resolve automaticamente a máquina pelo ID (Route Model Binding)
        
        // Aqui, carregamos também as manutenções associadas à máquina
        // Nota: O método 'maintenances' foi definido no seu modelo Machine.php
        $maintenances = $machine->maintenances()->orderBy('created_at', 'desc')->get();
        
        return view('machines.show', compact('machine', 'maintenances'));
    }

    /**
     * Mostrar o formulário para editar uma máquina existente.
     */
    public function edit(Machine $machine)
    {
        // O Laravel resolve automaticamente a máquina pelo ID
        return view('machines.edit', compact('machine'));
    }

    /**
     * Atualizar a máquina na base de dados.
     */
    public function update(Request $request, Machine $machine)
    {
        // 1. Validação de Dados (ignoramos o próprio serial_number atual para evitar erro de UNIQUE)
        $validatedData = $request->validate([
        // Regra unique ignorando o ID da máquina atual:
        'numero_interno' => 'required|string|max:50|unique:machines,numero_interno,' . $machine->id,
        
        'tipo_equipamento' => 'required|string|max:100',
        'marca' => 'nullable|string|max:100',
        'modelo' => 'nullable|string|max:100',
        'localizacao' => 'required|string|max:100',
        'operador' => 'nullable|string|max:100',
        'status' => 'required|in:Operacional,Avariada,Em Manutenção,Desativada',
        'observacoes' => 'nullable|string',
    ]);
        
        // 2. Atualização do Registo
        $machine->update($validatedData);

        // 3. Redirecionamento
     // 3. Redirecionamento (CORREÇÃO DA LINHA 105)
        return redirect()->route('machines.index')
                         ->with('success', 'Máquina "' . $validatedData['numero_interno'] . '" atualizada com sucesso!'); // <-- CORRIGIDO
    }

    /**
     * Eliminar uma máquina.
     */
    public function destroy(Machine $machine)
    {
        $machineName = $machine->name;
        
        // Graças ao onDelete('cascade') na migration, 
        // todas as manutenções associadas serão eliminadas automaticamente!
        $machine->delete();

        return redirect()->route('machines.index')
                         ->with('success', 'Máquina "' . $machineName . '" e todos os seus registos de manutenção eliminados com sucesso!');
    }
}
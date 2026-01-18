<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockMovement;
use App\Models\StockItem; 
use App\Models\Machine;
use App\Models\Maintenance;
use App\Models\MaintenanceFile; // Certifica-te que este model existe
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MaintenanceController extends Controller
{
    /**
     * Lista todas as manutenções.
     */
    public function index()
    {
        $maintenances = Maintenance::with('machine')->latest()->get();
        return view('maintenances.index', compact('maintenances'));
    }

    /**
     * Formulário de criação genérico.
     */
    public function create()
    {
        return view('maintenances.form', [
            'maintenance' => new Maintenance(),
            'machines' => Machine::all(),
            'items' => StockItem::all(),
            'selectedMachine' => null,
        ]);
    }

    /**
     * Formulário de edição.
     */
    public function edit(Maintenance $maintenance)
    {
        return view('maintenances.form', [
            'maintenance' => $maintenance,
            'machines' => Machine::all(),
            'items' => StockItem::all(),
            'selectedMachine' => $maintenance->machine_id,
        ]);
    }

    /**
     * Formulário de criação a partir de uma máquina específica.
     */
    public function createFromMachine(Machine $machine)
    {
        $maintenance = new Maintenance([
            'status' => 'em_manutencao',
            'scheduled_date' => now(),
            'machine_id' => $machine->id,
        ]);

        return view('maintenances.form', [
            'maintenance'     => $maintenance,
            'machines'        => Machine::all(),
            'items'           => StockItem::all(),
            'selectedMachine' => $machine->id,
            'currentMachine'  => $machine,
        ])->with('info', 'Preencha os detalhes da manutenção antes de salvar.');
    }

    /**
     * 💾 Gravar Nova Manutenção (Com Baixa de Stock)
     */
    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            // 1. Criar a Manutenção
            $maintenance = Maintenance::create($request->all());

            // 2. Processar itens de stock
            if ($request->has('items')) {
                foreach ($request->items as $itemData) {
                    if (!empty($itemData['id']) && $itemData['quantity'] > 0) {
                        
                        // Regista o movimento no histórico
                        StockMovement::create([
                            'maintenance_id' => $maintenance->id,
                            'machine_id'     => $request->machine_id,
                            'stock_item_id'  => $itemData['id'], 
                            'quantity'       => $itemData['quantity'],
                        ]);

                        // Baixa automática no stock real
                        $stockItem = StockItem::find($itemData['id']);
                        if ($stockItem) {
                            $stockItem->decrement('quantidade', $itemData['quantity']);
                        }
                    }
                }
            }

            // 3. Status da Máquina
            $this->updateMachineStatus($maintenance);

            return response()->json([
                'message' => 'Manutenção e stock processados com sucesso!',
                'redirect_url' => route('maintenances.index')
            ]);
        });
    }

    /**
     * ✏️ Atualizar Manutenção Existente
     */
    public function update(Request $request, Maintenance $maintenance)
    {
        $validatedData = $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'failure_description' => 'required|string|max:1000',
            'status' => 'required|in:pendente,em_manutencao,concluida,cancelada',            
            'work_sheet_ref' => 'nullable|string|max:255',
            'hours_kms' => 'nullable|integer',
            'technician_notes' => 'nullable|string',
            'total_cost' => 'nullable|numeric|min:0',
            'end_date' => 'nullable|date',
            'scheduled_date' => 'nullable|date',
            'maintenance_files' => 'nullable|array',
            'maintenance_files.*' => 'file|max:10240|mimes:pdf,jpg,jpeg,png,zip,doc,docx', 
        ]);
        
        try {
            DB::beginTransaction();

            $maintenance->update($validatedData);
            $this->updateMachineStatus($maintenance);
            
            if ($request->hasFile('maintenance_files')) {
                $this->handleFileUploads($request, $maintenance);
            }

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Manutenção atualizada com sucesso!',
                'redirect_url' => route('machines.show', $maintenance->machine_id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro no update: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erro ao atualizar.'], 500);
        }
    }

    /**
     * Métodos Auxiliares
     */
    protected function updateMachineStatus(Maintenance $maintenance)
    {
        $machine = $maintenance->machine;
        $machineStatus = match ($maintenance->status) {
            'em_manutencao', 'pendente' => 'Em Manutenção',
            'concluida' => 'Operacional',
            default => $machine->status,
        };
        $machine->update(['status' => $machineStatus]);
    }

    protected function handleFileUploads(Request $request, Maintenance $maintenance)
    {
        foreach ($request->file('maintenance_files') as $file) {
            $path = $file->store('maintenances/' . $maintenance->id, 'public'); 

            MaintenanceFile::create([
                'maintenance_id' => $maintenance->id,
                'filename' => $file->getClientOriginalName(),
                'filepath' => $path,
                'mime_type' => $file->getMimeType(),
                'filesize' => $file->getSize(),
            ]);
        }
    }

    public function show(Maintenance $maintenance)
    {
        $exchangeRate = 70.00; 
        $maintenance->load(['machine', 'files']); 
        $maintenance->status = strtolower($maintenance->status);
        return view('maintenances.show', compact('maintenance', 'exchangeRate'));
    }

    public function destroy(Maintenance $maintenance)
    {
        $machineId = $maintenance->machine_id; 
        $maintenance->delete();
        return redirect()->route('machines.show', $machineId)
                         ->with('success', 'Eliminado com sucesso!');
    }
}
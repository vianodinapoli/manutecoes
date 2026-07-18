<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockMovement;
use App\Models\StockItem; 
use App\Models\Machine;
use App\Models\Maintenance;
use App\Models\MaintenanceFile;
use App\Models\Activity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MaintenanceController extends Controller
{
    public function index()
    {
        $maintenances = Maintenance::with('machine')->latest()->get();
        return view('maintenances.index', compact('maintenances'));
    }

    public function create()
    {
        return view('maintenances.form', [
            'maintenance' => new Maintenance(),
            'machines' => Machine::all(),
            'items' => StockItem::all(),
            'selectedMachine' => null,
        ]);
    }

    public function edit(Maintenance $maintenance)
    {
        return view('maintenances.form', [
            'maintenance' => $maintenance,
            'machines' => Machine::all(),
            'items' => StockItem::all(),
            'selectedMachine' => $maintenance->machine_id,
        ]);
    }

    public function createFromMachine(Machine $machine)
    {
        $maintenance = new Maintenance([
            'status' => 'Em Manutenção',
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
     * 💾 Gravar Nova Manutenção (Com Baixa de Stock e Peças)
     */
    public function store(Request $request)
{
    return DB::transaction(function () use ($request) {
        // 1. Criar a Manutenção
        $maintenance = Maintenance::create($request->except(['items', 'maintenance_files']));

        // 2. Registar Actividade (apenas UMA vez, com referência)
        Activity::create([
            'type'        => 'maintenance',
            'description' => 'Manutenção registada para ' . $maintenance->machine->nome,
            'reference'   => 'MNT-' . str_pad($maintenance->id, 4, '0', STR_PAD_LEFT) . ' · ' . $maintenance->machine->numero_interno,
            'user_name'   => auth()->user()->name,
            'status'      => 'pendente',
        ]);

        // 3. Processar itens de stock
        if ($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $itemData) {
                $itemId   = $itemData['id'] ?? $itemData['stock_item_id'] ?? null;
                $quantity = $itemData['quantity'] ?? 0;

                if ($itemId && $quantity > 0) {
                    StockMovement::create([
                        'maintenance_id' => $maintenance->id,
                        'machine_id'     => $maintenance->machine_id,
                        'stock_item_id'  => $itemId,
                        'quantity'       => $quantity,
                    ]);

                    $stockItem = StockItem::find($itemId);
                    if ($stockItem) {
                        $stockItem->decrement('quantidade', $quantity);
                    }
                }
            }
        }

        // 4. Processar Ficheiros
        if ($request->hasFile('maintenance_files')) {
            $this->handleFileUploads($request, $maintenance);
        }

        $this->updateMachineStatus($maintenance);

        return response()->json([
            'success'      => true,
            'message'      => 'Manutenção e stock processados com sucesso!',
            'redirect_url' => route('maintenances.index')
        ]);
    });
}

    public function update(Request $request, Maintenance $maintenance)
    {
        // Nota: Se quiser que o Update também adicione peças novas, 
        // a lógica do foreach do store deve ser replicada aqui.
        try {
            DB::beginTransaction();

            $maintenance->update($request->except(['maintenance_files', 'items']));
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

    protected function updateMachineStatus(Maintenance $maintenance)
    {
        $machine = $maintenance->machine;
        if($machine) {
            $machineStatus = match ($maintenance->status) {
                'em_manutencao', 'pendente' => 'Em Manutenção',
                'concluida' => 'Operacional',
                default => $machine->status,
            };
            $machine->update(['status' => $machineStatus]);
        }
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

    /**
     * 👀 Visualizar Detalhes (Carrega as peças usadas)
     */
    public function show(Maintenance $maintenance)
    {
        $exchangeRate = 70.00; 
        
        // O with/load garante que as peças e os nomes dos artigos apareçam na view
        $maintenance->load(['machine', 'files', 'movements.stockItem']); 
        
        $maintenance->status = strtolower($maintenance->status);
        
        return view('maintenances.show', compact('maintenance', 'exchangeRate'));
    }
public function destroy(Maintenance $maintenance)
{
    $maintenance->delete();
    return response()->json([
        'success' => true,
        'message' => 'Manutenção eliminada com sucesso.',
        'redirect_url' => route('maintenances.index')
    ]);
}
}
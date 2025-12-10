<?php

// app/Http/Controllers/MaintenanceController.php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\Machine;
use App\Models\MaintenanceFile; // <-- NOVO: Importar o modelo de ficheiros
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;       // <-- NOVO: Para usar transações
use Illuminate\Support\Facades\Storage;  // <-- NOVO: Para guardar ficheiros
use Illuminate\Routing\Controller; 

class MaintenanceController extends Controller
{
    // ... (Métodos index, create, edit, show, destroy, createFromMachine permanecem inalterados) ...
    
    public function index()
    {
       // Usamos 'with('machine')' para carregar a máquina associada a cada manutenção de forma eficiente
        $maintenances = Maintenance::with('machine')->latest()->get();

        return view('maintenances.index', compact('maintenances'));
    }
    

    public function create()
    {
        $machines = Machine::all();
        return view('maintenances.form', [
            'maintenance' => new Maintenance(),
            'machines' => $machines,
            'selectedMachine' => null, 
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
        $maintenance = new Maintenance([
            'status' => 'Em manutenção',
            'scheduled_date' => now(),
            'machine_id' => $machine->id,
        ]);

        return view('maintenances.form', [
            'maintenance' => $maintenance,
            'machines' => Machine::all(),
            'selectedMachine' => $machine->id,
            'currentMachine' => $machine,
        ])->with('info', 'Preencha os detalhes da manutenção antes de salvar.');
    }


    // =========================================================================
    // 💾 Método STORE (Criar) - Adaptado para AJAX e Gestão de Ficheiros
    // =========================================================================
    public function store(Request $request)
    {
        // 1. Validação
        // Nota: A validação dos campos de texto deve ser mais robusta aqui!
        $validatedData = $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'failure_description' => 'required|string|max:1000',
            'nome_motorista' => 'required|string|max:255', // <-- ESTE CAMPO DEVE ESTAR AQUI
             'data_entrada' => 'required|date',         // <-- ESTE CAMPO DEVE ESTAR AQUI
             'horas_trabalho' => 'required|numeric|min:0', // <-- ESTE
            'scheduled_date' => 'nullable|date',


'status' => 'required|in:pendente,em_manutencao,concluida,cancelada', // CORREÇÃO: Usando sublinhado e sem acento            
            // Opcionais
            'work_sheet_ref' => 'nullable|string|max:255',
            'hours_kms' => 'nullable|integer',
            'technician_notes' => 'nullable|string',
            'total_cost' => 'nullable|numeric|min:0',
            'end_date' => 'nullable|date',
            
            // Validação dos Ficheiros (Chave: 'maintenance_files.*')
            'maintenance_files' => 'nullable|array',
            'maintenance_files.*' => 'file|max:10240|mimes:pdf,jpg,jpeg,png,zip,doc,docx', 
        ]);

        try {
            // Inicia uma Transação de Base de Dados
            DB::beginTransaction();

            // 2. Criação da Manutenção
            $maintenance = Maintenance::create($validatedData);

            // 3. Gestão e atualização do Status da Máquina
            $this->updateMachineStatus($maintenance);

            // 4. Guardar os Ficheiros
            if ($request->hasFile('maintenance_files')) {
                $this->handleFileUploads($request, $maintenance);
            }

            // Confirma a transação
            DB::commit();

            // Resposta de Sucesso JSON (necessária para o Frontend AJAX)
            return response()->json([
                'success' => true,
                'message' => 'Manutenção criada e ficheiros guardados com sucesso!',
                'redirect_url' => route('machines.show', $maintenance->machine_id)
            ], 201);

        } catch (\Exception $e) {
            // Desfaz a transação se algo falhar (incluindo o upload)
            DB::rollBack();
            
            // Resposta de Erro JSON
            \Log::error("Erro ao guardar manutenção (store): " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor ao criar manutenção.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // =========================================================================
    // ✏️ Método UPDATE (Editar) - Adaptado para AJAX e Gestão de Ficheiros
    // =========================================================================
    public function update(Request $request, Maintenance $maintenance)
    {
        // 1. Validação
        $validatedData = $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'failure_description' => 'required|string|max:1000',
            'status' => 'required|in:pendente,em_manutencao,concluida,cancelada',            
            // Opcionais
            'work_sheet_ref' => 'nullable|string|max:255',
            'hours_kms' => 'nullable|integer',
            'technician_notes' => 'nullable|string',
            'total_cost' => 'nullable|numeric|min:0',
            'end_date' => 'nullable|date',
                        'scheduled_date' => 'nullable|date',

            // Validação dos Ficheiros (Chave: 'maintenance_files.*')
            'maintenance_files' => 'nullable|array',
            'maintenance_files.*' => 'file|max:10240|mimes:pdf,jpg,jpeg,png,zip,doc,docx', 
        ]);
        
        try {
            DB::beginTransaction();

            // 2. Atualização da Manutenção
            $maintenance->update($validatedData);

            // 3. Gestão e atualização do Status da Máquina
            $this->updateMachineStatus($maintenance);
            
            // 4. Guardar Novos Ficheiros
            // NOTA: Os ficheiros antigos permanecem. O AJAX só envia NOVOS ficheiros.
            if ($request->hasFile('maintenance_files')) {
                $this->handleFileUploads($request, $maintenance);
            }

            DB::commit();
            
            // Resposta de Sucesso JSON (necessária para o Frontend AJAX)
            return response()->json([
                'success' => true,
                'message' => 'Manutenção atualizada e novos ficheiros guardados com sucesso!',
                'redirect_url' => route('machines.show', $maintenance->machine_id)
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Resposta de Erro JSON
            \Log::error("Erro ao guardar manutenção (update): " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor ao atualizar manutenção.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // =========================================================================
    // Métodos Auxiliares
    // =========================================================================

    /**
     * Lógica para mapear o status da manutenção para o status da máquina e atualizar.
     */
    protected function updateMachineStatus(Maintenance $maintenance)
    {
        $machine = $maintenance->machine;
        
        $machineStatus = match ($maintenance->status) {
            'Pendente', 'Em Progresso' => 'Em Manutenção',
            'Concluída' => 'Operacional', // Se concluída, a máquina está apta para uso
            'Cancelada' => $machine->status, // Manter o status anterior
            default => $machine->status,
        };

        // Adicione aqui qualquer lógica que defina o status 'Avariada' se necessário
        
        $machine->update([
            'status' => $machineStatus
        ]);
    }

    /**
     * Lógica para guardar ficheiros no storage e na base de dados.
     */
    protected function handleFileUploads(Request $request, Maintenance $maintenance)
    {
        // Itera sobre cada ficheiro no array 'maintenance_files'
        foreach ($request->file('maintenance_files') as $file) {
            
            // Define a pasta de destino (ex: 'maintenances/1/')
            $folderPath = 'maintenances/' . $maintenance->id;
            
            // Guarda o ficheiro no disco 'public'. O nome do ficheiro é hashed.
            $path = $file->store($folderPath, 'public'); 

            // Cria o registo na base de dados
            MaintenanceFile::create([
                'maintenance_id' => $maintenance->id,
                'filename' => $file->getClientOriginalName(),
                'filepath' => $path,
                'mime_type' => $file->getMimeType(),
                'filesize' => $file->getSize(),
            ]);
        }
    }
    
    // ... (Métodos show e destroy permanecem inalterados) ...
    
    /**
     * Mostrar os detalhes de um registo de manutenção específico.
     */
    public function show(Maintenance $maintenance)
    {
       // 1. Define a taxa de câmbio (Ajuste este valor conforme a taxa atual)
    $exchangeRate = 70.00; // Exemplo: 1 Euro = 70 Meticais Moçambicanos (MZN)

    // 2. Garante que as relações são carregadas
    $maintenance->load(['machine', 'files']); 

    // 3. Normaliza o status para minúsculas antes de passar para o Blade (para a lógica de badges)
    $maintenance->status = strtolower($maintenance->status);

    // 4. PASSA A VARIÁVEL $exchangeRate para a view
    return view('maintenances.show', compact('maintenance', 'exchangeRate'));
    }

    /**
     * Eliminar um registo de manutenção (APAGAR).
     */
    public function destroy(Maintenance $maintenance)
    {
        // Antes de apagar, o Laravel irá apagar automaticamente os registos de 
        // MaintenanceFile devido ao 'onDelete('cascade')' na migração.

        $machineId = $maintenance->machine_id; 
        $maintenanceId = $maintenance->id; 
        
        $maintenance->delete();

        return redirect()->route('machines.show', $machineId)
                         ->with('success', 'Registo de manutenção ID ' . $maintenanceId . ' eliminado com sucesso!');
    }
}
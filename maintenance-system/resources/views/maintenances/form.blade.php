@extends('layouts.app')

@section('content')
<div class="container mt-4">
    
    @php
        // Define a máquina atual. Se estiver em edit, usa a relação. Se em createFromMachine, usa a variável passada.
        $currentMachine = $maintenance->machine ?? $currentMachine ?? null;
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ $maintenance->id ? '✏️ Editar Manutenção #' . $maintenance->id : '🛠️ Criar Nova Manutenção' }}</h1>
        <a href="{{ $currentMachine ? route('machines.show', $currentMachine->id) : route('maintenances.index') }}" class="btn btn-secondary">
            ⬅️ Voltar
        </a>
    </div>
    
    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">Por favor, corrija os erros no formulário.</div>
    @endif
    
    <form action="{{ $maintenance->id ? route('maintenances.update', $maintenance->id) : route('maintenances.store') }}" method="POST">
        @csrf
        @if($maintenance->id)
            @method('PUT')
        @endif

        {{-- =============================================== --}}
        <h2>Dados da Máquina Selecionada</h2>
        {{-- =============================================== --}}
        
        @if($currentMachine)
            <div class="card bg-light p-3 mb-4 border-primary">
                <div class="row">
                    <div class="col-md-4">
                        <p><strong>Nº Interno:</strong> {{ $currentMachine->numero_interno }}</p>
                        <p><strong>Tipo:</strong> {{ $currentMachine->tipo_equipamento }}</p>
                        <p><strong>Marca:</strong> {{ $currentMachine->marca }}</p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Modelo:</strong> {{ $currentMachine->modelo }}</p>
                        <p><strong>Localização:</strong> {{ $currentMachine->localizacao }}</p>
                        <p><strong>Status:</strong> <span class="badge bg-warning text-dark">{{ $currentMachine->status }}</span></p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Data Automática:</strong> {{ now()->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
            </div>
            <input type="hidden" name="machine_id" value="{{ $currentMachine->id }}">
        @else
            <div class="mb-3">
                <label for="machine_id" class="form-label">Máquina</label>
                <select name="machine_id" id="machine_id" class="form-select @error('machine_id') is-invalid @enderror" required>
                    <option value="">-- Selecione uma Máquina --</option>
                    @foreach($machines as $machine)
                        <option value="{{ $machine->id }}" 
                            {{ old('machine_id', $maintenance->machine_id ?? $selectedMachine) == $machine->id ? 'selected' : '' }}>
                            {{ $machine->numero_interno }} ({{ $machine->tipo_equipamento }})
                        </option>
                    @endforeach
                </select>
                @error('machine_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        @endif
        
        <hr>
        
        {{-- =============================================== --}}
        <h2>Detalhes da Intervenção</h2>
        {{-- =============================================== --}}

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="work_sheet_ref" class="form-label">Folha de Obra / Ref.</label>
                    <input type="text" name="work_sheet_ref" id="work_sheet_ref" class="form-control" 
                           value="{{ old('work_sheet_ref', $maintenance->work_sheet_ref ?? '') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="hours_kms" class="form-label">Nº de Horas / KMS</label>
                    <input type="number" name="hours_kms" id="hours_kms" class="form-control" 
                           value="{{ old('hours_kms', $maintenance->hours_kms ?? '') }}">
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="failure_description" class="form-label">Descrição da Falha (Ocorrência)</label>
            <textarea name="failure_description" id="failure_description" class="form-control @error('failure_description') is-invalid @enderror" rows="3" required>{{ old('failure_description', $maintenance->failure_description) }}</textarea>
            @error('failure_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        
        <div class="mb-3">
            <label for="status" class="form-label">Status da Manutenção</label>
            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                @php $currentStatus = old('status', $maintenance->status); @endphp
                <option value="Pendente" {{ $currentStatus == 'Pendente' ? 'selected' : '' }}>Pendente</option>
                <option value="Em Progresso" {{ $currentStatus == 'Em Progresso' ? 'selected' : '' }}>Em Progresso</option>
                <option value="Concluída" {{ $currentStatus == 'Concluída' ? 'selected' : '' }}>Concluída</option>
                <option value="Cancelada" {{ $currentStatus == 'Cancelada' ? 'selected' : '' }}>Cancelada</option>
            </select>
            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <hr>
        
        {{-- =============================================== --}}
        <h2>Custos e Material (Itens de Serviço)</h2>
        {{-- =============================================== --}}
        
        <div class="mb-3">
            <label for="technician_notes" class="form-label">Descrição do Material / Serviço / Notas do Técnico</label>
            <textarea name="technician_notes" id="technician_notes" class="form-control" rows="4">{{ old('technician_notes', $maintenance->technician_notes ?? '') }}</textarea>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="total_cost" class="form-label">Custo Total (A ser acumulado)</label>
                    <input type="number" step="0.01" name="total_cost" id="total_cost" class="form-control" 
                           value="{{ old('total_cost', $maintenance->total_cost ?? 0) }}" placeholder="0.00">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="end_date" class="form-label">Data de Conclusão (Opcional)</label>
                    <input type="datetime-local" name="end_date" id="end_date" class="form-control"
                           value="{{ old('end_date', $maintenance->end_date ? $maintenance->end_date->format('Y-m-d\TH:i') : '') }}">
                </div>
            </div>
        </div>

        <div class="card p-4 shadow-sm">
    <h2 class="card-title mb-3">📎 Anexar Ficheiros</h2>
    <div id="dropZone" class="drop-zone border-dashed rounded-lg p-5 text-center">
        <p class="mb-2">Arraste e solte ficheiros aqui ou <label for="fileInput" class="text-primary cursor-pointer hover:underline">clique para selecionar</label>.</p>
        <input type="file" id="fileInput" multiple style="display: none;">
        <p class="small text-muted" id="fileStatus">Nenhum ficheiro selecionado.</p>
    </div>
    <div id="fileList" class="mt-3">
        <!-- Ficheiros anexados serão listados aqui -->
    </div>
</div>

<!-- LÓGICA DE SCRIPTS NECESSÁRIOS -->
<!-- Certifique-se de que o jQuery e o Bootstrap JS estão carregados na sua página principal -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

<style>
    /* Estilos do Drop Zone */
    .drop-zone {
        border: 2px dashed #ccc;
        background-color: #f8f9fa;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .drop-zone.drag-over {
        border-color: #007bff; /* Cor primária do Bootstrap */
        background-color: #e9ecef;
    }
    .cursor-pointer {
        cursor: pointer;
    }
    .file-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 12px;
        margin-bottom: 4px;
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 4px;
    }
</style>

<script>
    // Inicializa a lógica de Drag & Drop
    $(document).ready(function() {
        // --- Lógica de Drag and Drop para Ficheiros ---
        const dropZone = $('#dropZone');
        const fileInput = $('#fileInput');
        const fileList = $('#fileList');
        const fileStatus = $('#fileStatus');
        let attachedFiles = []; // Array para guardar os ficheiros selecionados

        // Previne o comportamento padrão do navegador (abrir o ficheiro) em todo o documento
        $(document).on('dragover dragenter', function(e) {
            e.preventDefault();
            e.stopPropagation();
        });
        $(document).on('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
        });

        // Lidar com Drag Over/Enter (Mudar estilo)
        dropZone.on('dragover dragenter', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.addClass('drag-over');
        });

        // Lidar com Drag Leave (Remover estilo)
        dropZone.on('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            // Verifica se o rato saiu da zona de drop (para evitar flicker)
            if (e.originalEvent.relatedTarget === null || !$.contains(this, e.originalEvent.relatedTarget)) {
                dropZone.removeClass('drag-over');
            }
        });

        // Lidar com Drop
        dropZone.on('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.removeClass('drag-over');
            
            const files = e.originalEvent.dataTransfer.files;
            handleFiles(files);
        });

        // Lidar com seleção via Input (clique)
        fileInput.on('change', function() {
            handleFiles(this.files);
        });

        // Lidar com clique na área (atribui o evento ao label via HTML, mas este é um fallback)
        dropZone.on('click', function(e) {
            // Previne que o clique dispare duas vezes se clicar no label
            if (e.target.tagName !== 'LABEL') {
                fileInput.trigger('click');
            }
        });

        // Função principal para processar ficheiros
        function handleFiles(files) {
            for (let i = 0; i < files.length; i++) {
                // Adiciona os ficheiros à lista, evitando duplicados pelo nome (simples)
                const file = files[i];
                if (!attachedFiles.some(f => f.name === file.name)) {
                    attachedFiles.push(file);
                }
            }
            updateFileList();
        }
        
        // Função global para remover um ficheiro (chamada pelo botão)
        window.removeFile = function(fileName) {
            attachedFiles = attachedFiles.filter(file => file.name !== fileName);
            updateFileList();
        }

        // Função para atualizar a lista de ficheiros na UI
        function updateFileList() {
            fileList.empty(); // Limpa a lista atual

            if (attachedFiles.length === 0) {
                fileStatus.text('Nenhum ficheiro selecionado.');
            } else {
                fileStatus.text(`${attachedFiles.length} ficheiro(s) pronto(s) para upload.`);
            }
            
            attachedFiles.forEach(file => {
                const fileSize = (file.size / 1024 / 1024).toFixed(2); // Tamanho em MB
                const fileItem = `
                    <div class="file-item">
                        <span>
                            📄 ${file.name} 
                            <span class="text-muted small">(${fileSize} MB)</span>
                        </span>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile('${file.name}')">
                            Remover
                        </button>
                    </div>
                `;
                fileList.append(fileItem);
            });
        }
    });
</script>

        <button type="submit" class="btn btn-success btn-lg mt-3">
            {{ $maintenance->id ? '✅ Atualizar Manutenção' : '💾 Criar Manutenção' }}
        </button>
    </form>
</div>
@endsection
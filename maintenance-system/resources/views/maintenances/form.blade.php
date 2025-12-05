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
    
    {{-- As mensagens de erro de validação do Laravel 
         serão injetadas aqui pelo JS na resposta do AJAX 422 --}}
    
    {{-- Adicionamos o ID maintenanceForm para ser usado no JavaScript --}}
    <form action="{{ $maintenance->id ? route('maintenances.update', $maintenance->id) : route('maintenances.store') }}" 
          method="POST" 
          id="maintenanceForm">
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
                {{-- Aqui não precisamos do @error, pois o tratamento de erro é feito via AJAX/JS --}}
                <select name="machine_id" id="machine_id" class="form-select" required>
                    <option value="">-- Selecione uma Máquina --</option>
                    @foreach($machines as $machine)
                        <option value="{{ $machine->id }}" 
                            {{ old('machine_id', $maintenance->machine_id ?? $selectedMachine) == $machine->id ? 'selected' : '' }}>
                            {{ $machine->numero_interno }} ({{ $machine->tipo_equipamento }})
                        </option>
                    @endforeach
                </select>
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
            {{-- Aqui também removemos a classe @error, confiando no JS para tratamento visual --}}
            <textarea name="failure_description" id="failure_description" class="form-control" rows="3" required>{{ old('failure_description', $maintenance->failure_description) }}</textarea>
        </div>
        
        <div class="mb-3">
            <label for="status" class="form-label">Status da Manutenção</label>
            <select name="status" id="status" class="form-select" required>
                @php $currentStatus = old('status', $maintenance->status); @endphp
                <option value="Pendente" {{ $currentStatus == 'Pendente' ? 'selected' : '' }}>Pendente</option>
                <option value="Em Progresso" {{ $currentStatus == 'Em Progresso' ? 'selected' : '' }}>Em Progresso</option>
                <option value="Concluída" {{ $currentStatus == 'Concluída' ? 'selected' : '' }}>Concluída</option>
                <option value="Cancelada" {{ $currentStatus == 'Cancelada' ? 'selected' : '' }}>Cancelada</option>
            </select>
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
                </div>
            
            {{-- Se for edição, pode listar os ficheiros existentes aqui (opcional) --}}
            @if($maintenance->id && $maintenance->files->isNotEmpty())
                <h4 class="mt-4">Ficheiros Existentes:</h4>
                <div class="list-group">
                    @foreach($maintenance->files as $file)
                        <a href="{{ $file->url }}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            📄 {{ $file->filename }} 
                            <span class="badge bg-secondary rounded-pill">{{ round($file->filesize / 1024 / 1024, 2) }} MB</span>
                            {{-- Para apagar ficheiros existentes, precisará de lógica AJAX adicional --}}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- O BOTÃO AGORA É TYPE="BUTTON" COM ID PARA SER GERIDO PELO JAVASCRIPT --}}
        <button type="button" id="submitButton" class="btn btn-success btn-lg mt-3">
            {{ $maintenance->id ? '✅ Atualizar Manutenção' : '💾 Criar Manutenção' }}
        </button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>

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
    /* Estilo para campos com erro de validação (adicionado pelo JS) */
    .is-invalid {
        border-color: #dc3545 !important;
    }
</style>

<script>
    $(document).ready(function() {
        // --- Variáveis e Configuração ---
        const dropZone = $('#dropZone');
        const fileInput = $('#fileInput');
        const fileList = $('#fileList');
        const fileStatus = $('#fileStatus');
        const submitButton = $('#submitButton');
        let attachedFiles = []; // Array para guardar os ficheiros selecionados

        // --- Funções de Drop Zone ---
        $(document).on('dragover dragenter', function(e) { e.preventDefault(); e.stopPropagation(); });
        $(document).on('drop', function(e) { e.preventDefault(); e.stopPropagation(); });

        dropZone.on('dragover dragenter', function(e) { e.preventDefault(); e.stopPropagation(); dropZone.addClass('drag-over'); });
        dropZone.on('dragleave', function(e) { 
            e.preventDefault(); e.stopPropagation(); 
            if (e.originalEvent.relatedTarget === null || !$.contains(this, e.originalEvent.relatedTarget)) {
                dropZone.removeClass('drag-over');
            }
        });

        dropZone.on('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.removeClass('drag-over');
            handleFiles(e.originalEvent.dataTransfer.files);
        });

        fileInput.on('change', function() {
            handleFiles(this.files);
        });

        dropZone.on('click', function(e) {
            if (e.target.tagName !== 'LABEL' && e.target.tagName !== 'INPUT') {
                fileInput.trigger('click');
            }
        });

        function handleFiles(files) {
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                if (!attachedFiles.some(f => f.name === file.name && f.size === file.size)) {
                    attachedFiles.push(file);
                }
            }
            updateFileList();
        }
        
        window.removeFile = function(fileName, fileSize) {
            attachedFiles = attachedFiles.filter(file => !(file.name === fileName && file.size === parseInt(fileSize)));
            updateFileList();
        }

        function updateFileList() {
            fileList.empty(); 
            if (attachedFiles.length === 0) {
                fileStatus.text('Nenhum ficheiro selecionado.');
            } else {
                fileStatus.text(`${attachedFiles.length} ficheiro(s) pronto(s) para upload.`);
            }
            
            attachedFiles.forEach(file => {
                const fileSizeMB = (file.size / 1024 / 1024).toFixed(2);
                const fileItem = `
                    <div class="file-item">
                        <span>
                            📄 ${file.name} 
                            <span class="text-muted small">(${fileSizeMB} MB)</span>
                        </span>
                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                onclick="removeFile('${file.name}', '${file.size}')">
                            Remover
                        </button>
                    </div>
                `;
                fileList.append(fileItem);
            });
        }

        // ==========================================================
        // Lógica de Submissão AJAX
        // ==========================================================
        submitButton.on('click', function(e) {
            e.preventDefault(); 
            
            const btn = $(this);
            const originalText = btn.text();

            // 1. Limpar erros e marcar o botão
            $('.alert-danger').remove();
            $('.is-invalid').removeClass('is-invalid');
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> A Guardar...');

            // 2. Criação do FormData
            const formData = new FormData($('#maintenanceForm')[0]);

            // 3. Adiciona os ficheiros
            attachedFiles.forEach((file) => {
                formData.append('maintenance_files[]', file);
            });
            
            // Determina o método HTTP correto (Laravel usa POST com _method=PUT para updates)
            const formMethod = $('#maintenanceForm input[name="_method"]').length > 0 ? 'POST' : 'POST';

            // 4. Envio AJAX
            $.ajax({
                url: $('#maintenanceForm').attr('action'),
                method: formMethod,
                data: formData,
                processData: false, 
                contentType: false, 
                
                success: function(response) {
                    alert(response.message || 'Operação realizada com sucesso!');
                    window.location.href = response.redirect_url;
                },
                error: function(xhr) {
                    // Reabilita o botão
                    btn.prop('disabled', false).text(originalText);

                    // Tratar erros de validação (status 422)
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let errorHtml = '<div class="alert alert-danger mt-3"><strong>⚠️ Erros de Validação:</strong><ul>';
                        $.each(errors, function(key, value) {
                            errorHtml += '<li>' + value[0] + '</li>';
                            
                            // Tenta encontrar o campo e marcar como inválido
                            const fieldName = key.replace(/\..*/, ''); 
                            $(`[name="${fieldName}"], [name="${fieldName}[]"]`).addClass('is-invalid');
                            
                            // Se for um erro de ficheiro, realça o drop zone
                            if(fieldName === 'maintenance_files') {
                                $('#dropZone').addClass('is-invalid');
                            }
                        });
                        errorHtml += '</ul></div>';
                        $('.container').prepend(errorHtml);
                        $('html, body').animate({ scrollTop: 0 }, 'slow');
                        
                    } else {
                         // Erros de servidor (500)
                        alert('Ocorreu um erro inesperado: ' + (xhr.responseJSON.message || xhr.statusText));
                    }
                }
            });
        });
    });
</script>
@endsection
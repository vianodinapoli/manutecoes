<x-app-layout> 
    <div class="container mt-4">
        @php
            $currentMachine = $maintenance->machine ?? $currentMachine ?? null;
            $currentStatus = old('status', $maintenance->status ?? 'pendente');
        @endphp

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">{{ $maintenance->id ? '✏️ Editar Manutenção #' . $maintenance->id : '🛠️ Criar Nova Manutenção' }}</h3>
            <a href="{{ $currentMachine ? route('machines.show', $currentMachine->id) : route('maintenances.index') }}" class="btn btn-outline-secondary">
                ⬅️ Voltar
            </a>
        </div>
        
        <form action="{{ $maintenance->id ? route('maintenances.update', $maintenance->id) : route('maintenances.store') }}" 
              method="POST" 
              id="maintenanceForm"
              enctype="multipart/form-data">
            @csrf
            @if($maintenance->id)
                @method('PUT')
            @endif

            {{-- Secção: Dados da Máquina --}}
            <div class="card shadow-sm mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">ℹ️ Máquina Selecionada</h5>
                </div>
                <div class="card-body bg-light">
                    @if($currentMachine)
                        <div class="row">
                            <div class="col-md-4">
                                <p><strong>Nº Interno:</strong> {{ $currentMachine->numero_interno }}</p>
                                <p><strong>Tipo:</strong> {{ $currentMachine->tipo_equipamento }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Marca/Modelo:</strong> {{ $currentMachine->marca }} {{ $currentMachine->modelo }}</p>
                                <p><strong>Localização:</strong> {{ $currentMachine->localizacao }}</p>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <span class="badge bg-warning text-dark px-3 py-2 fs-6">{{ $currentMachine->status }}</span>
                            </div>
                        </div>
                        <input type="hidden" name="machine_id" value="{{ $currentMachine->id }}">
                    @else
                        <div class="mb-3">
                            <label for="machine_id" class="form-label fw-bold">Selecionar Máquina <span class="text-danger">*</span></label>
                            <select name="machine_id" id="machine_id" class="form-select select2" required>
                                <option value="">-- Selecione uma Máquina --</option>
                                @foreach($machines as $machine)
                                    <option value="{{ $machine->id }}" {{ old('machine_id', $maintenance->machine_id ?? $selectedMachine) == $machine->id ? 'selected' : '' }}>
                                        {{ $machine->numero_interno }} - {{ $machine->tipo_equipamento }} ({{ $machine->marca }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Secção: Detalhes da Intervenção --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">🛠️ Detalhes da Intervenção</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-bold">Nome do Motorista / Operador <span class="text-danger">*</span></label>
                            <input type="text" name="nome_motorista" class="form-control" value="{{ old('nome_motorista', $maintenance->nome_motorista ?? '') }}" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-bold">Data de Entrada <span class="text-danger">*</span></label>
                            <input type="date" name="data_entrada" class="form-control" value="{{ old('data_entrada', optional($maintenance->data_entrada ?? now())->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-bold">Status da Manutenção</label>
                            <select name="status" class="form-select bg-light fw-bold" required>
                                <option value="pendente" {{ $currentStatus == 'pendente' ? 'selected' : '' }}>🟡 Pendente</option>
                                <option value="em_manutencao" {{ $currentStatus == 'em_manutencao' ? 'selected' : '' }}>🟠 Em Manutenção</option>
                                <option value="concluida" {{ $currentStatus == 'concluida' ? 'selected' : '' }}>🟢 Concluída</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Descrição da Falha <span class="text-danger">*</span></label>
                            <textarea name="failure_description" class="form-control" rows="3" required>{{ old('failure_description', $maintenance->failure_description ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Secção: Dados Operacionais --}}
<div class="card shadow-sm mb-4 border-secondary">
    <div class="card-header bg-secondary text-white">
        <h5 class="mb-0">📋 Dados Operacionais e Referências</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            {{-- Folha de Obra / Ref --}}
            <div class="col-12 col-md-4">
                <label class="form-label fw-bold text-muted">Folha de Obra / Ref.</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-hash"></i></span>
                    <input type="text" name="work_sheet_ref" 
                           class="form-control" 
                           placeholder="Ex: FO-2024-001"
                           value="{{ old('work_sheet_ref', $maintenance->work_sheet_ref ?? '') }}">
                </div>
            </div>

            {{-- Horas/KMS na Entrada --}}
            <div class="col-12 col-md-4">
                <label class="form-label fw-bold text-muted">Horas/KMS na Entrada</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-speedometer2"></i></span>
                    <input type="number" name="hours_kms" 
                           class="form-control" 
                           placeholder="0"
                           value="{{ old('hours_kms', $maintenance->hours_kms ?? '') }}">
                </div>
            </div>

            {{-- Total de Horas Trabalhadas --}}
            <div class="col-12 col-md-4">
                <label class="form-label fw-bold text-muted">Horas de Mão-de-Obra</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-clock-history"></i></span>
                    <input type="number" step="0.01" name="horas_trabalho" 
                           class="form-control" 
                           placeholder="0.00"
                           value="{{ old('horas_trabalho', $maintenance->horas_trabalho ?? '0.00') }}">
                    <span class="input-group-text">h</span>
                </div>
            </div>
        </div>
    </div>
</div>

            {{-- Secção: Peças do Stock --}}
            <div class="card shadow-sm mb-4 border-info">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">📦 Peças e Consumíveis do Stock</h5>
                    <button type="button" class="btn btn-sm btn-light fw-bold" id="addItem">+ Adicionar Peça</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Artigo / Peça</th>
                                    <th width="180">Stock Atual</th>
                                    <th width="180">Qtd. a Retirar</th>
                                    <th width="50"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <tr class="item-row">
                                    <td class="p-3">
                                        <select name="items[0][id]" class="form-select item-select">
                                            <option value="">-- Selecione a Peça --</option>
                                            @foreach($items as $item)
                                                <option value="{{ $item->id }}" data-stock="{{ $item->quantidade }}">
                                                    [{{ $item->referencia }}] {{ $item->nome }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="p-3 text-center">
                                        <input type="text" class="form-control stock-display text-center fw-bold" readonly value="0">
                                    </td>
                                    <td class="p-3">
                                        <input type="number" name="items[0][quantity]" class="form-control text-center" step="0.01" min="0">
                                    </td>
                                    <td class="p-3 text-center">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-item">×</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- NOVO: Secção de Custos e Prazos + Anexos --}}
            <div class="row g-4 mb-4">
                <div class="col-12 col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title fw-bold mb-3">💰 Custos e Prazos</h5>
                            <div class="mb-3">
                                <label class="form-label">Custo Total Previsto (MT)</label>
                                <input type="number" step="0.01" name="total_cost" class="form-control form-control-lg" value="{{ old('total_cost', $maintenance->total_cost ?? 0) }}">
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Data de Conclusão Efetiva</label>
                                <input type="datetime-local" name="end_date" class="form-control" value="{{ old('end_date', isset($maintenance->end_date) ? $maintenance->end_date->format('Y-m-d\TH:i') : '') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title fw-bold mb-3">📎 Anexos (Fotos/Docs)</h5>
                            <div id="dropZone" class="border-dashed rounded p-4 text-center bg-light transition-all" style="cursor: pointer; border: 2px dashed #ccc;">
                                <p class="mb-1">Arraste ficheiros ou <strong>clique aqui</strong></p>
                                <input type="file" id="fileInput" multiple class="d-none">
                                <small class="text-muted d-block" id="fileStatus">0 ficheiros selecionados</small>
                            </div>
                            <div id="fileList" class="mt-3 small"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-5">
                <button type="button" id="submitButton" class="btn btn-success btn-lg px-5 shadow">
                    {{ $maintenance->id ? '✅ Atualizar Manutenção' : '💾 Criar Manutenção' }}
                </button>
            </div>
        </form>
    </div>

    <style>
        .border-dashed { border-style: dashed !important; }
        .drop-active { background-color: #e3f2fd !important; border-color: #0d6efd !important; }
        .transition-all { transition: all 0.3s ease; }
    </style>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        $(document).ready(function() {
            let attachedFiles = [];
            let itemIndex = 1;

            // --- Lógica de Peças ---
            $(document).on('change', '.item-select', function() {
                const stock = $(this).find(':selected').data('stock') || 0;
                $(this).closest('tr').find('.stock-display').val(stock);
            });

            $('#addItem').on('click', function() {
                let newRow = $('.item-row').first().clone();
                newRow.find('select').attr('name', `items[${itemIndex}][id]`).val('');
                newRow.find('input[type="number"]').attr('name', `items[${itemIndex}][quantity]`).val('');
                newRow.find('.stock-display').val('0');
                $('#itemsBody').append(newRow);
                itemIndex++;
            });

            $(document).on('click', '.remove-item', function() {
                if ($('.item-row').length > 1) $(this).closest('tr').remove();
            });

            // --- Lógica de Ficheiros (Drag & Drop) ---
            const dropZone = $('#dropZone');
            const fileInput = $('#fileInput');

            dropZone.on('click', () => fileInput.click());

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(name => {
                dropZone.on(name, (e) => { e.preventDefault(); e.stopPropagation(); });
            });

            dropZone.on('dragover', () => dropZone.addClass('drop-active'));
            dropZone.on('dragleave drop', () => dropZone.removeClass('drop-active'));

            dropZone.on('drop', (e) => handleFiles(e.originalEvent.dataTransfer.files));
            fileInput.on('change', function() { handleFiles(this.files); });

            function handleFiles(files) {
                for (let file of files) { attachedFiles.push(file); }
                updateFileList();
            }

            function updateFileList() {
                const list = $('#fileList');
                list.empty();
                attachedFiles.forEach((file, index) => {
                    list.append(`<div class="d-flex justify-content-between align-items-center mb-1 p-2 bg-white border rounded">
                        <span>📄 ${file.name}</span>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile(${index})">×</button>
                    </div>`);
                });
                $('#fileStatus').text(attachedFiles.length + ' ficheiro(s) selecionado(s)');
            }

            window.removeFile = (index) => { attachedFiles.splice(index, 1); updateFileList(); };

            // --- Submissão AJAX ---
            $('#submitButton').on('click', function() {
                const btn = $(this);
                const form = $('#maintenanceForm');
                
                if(!form[0].checkValidity()) { form[0].reportValidity(); return; }

                const formData = new FormData(form[0]);
                attachedFiles.forEach(file => formData.append('maintenance_files[]', file));

                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> A Guardar...');

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        alert(res.message);
                        window.location.href = res.redirect_url;
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).text('Tentar Novamente');
                        alert(xhr.responseJSON?.message || 'Erro ao guardar.');
                    }
                });
            });
        });
    </script>
</x-app-layout>
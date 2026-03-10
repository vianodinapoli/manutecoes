<x-app-layout>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    .page-wrap{max-width:900px;margin:0 auto;padding:32px 24px 48px}
    .section{background:#fff;border:1px solid #e2e8f0;border-radius:12px;margin-bottom:16px;overflow:hidden}
    .section-head{display:flex;align-items:center;gap:10px;padding:12px 20px;background:#f8fafc;border-bottom:1px solid #e2e8f0}
    .section-head .s-num{font-size:.6rem;font-weight:800;letter-spacing:1.5px;color:#94a3b8;text-transform:uppercase;background:#e2e8f0;border-radius:4px;padding:2px 7px}
    .section-head .s-title{font-size:.8rem;font-weight:700;color:#334155;letter-spacing:.3px}
    .section-body{padding:20px 24px}
    .field-lbl{font-size:.6rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#94a3b8;display:block;margin-bottom:5px}
    .field-val{font-size:.83rem;font-weight:600;color:#1e293b}
    .form-control,.form-select{font-size:.83rem;border:1px solid #e2e8f0;border-radius:8px;color:#1e293b;background:#fff;transition:border-color .15s,box-shadow .15s}
    .form-control:focus,.form-select:focus{border-color:#64748b;box-shadow:0 0 0 3px rgba(100,116,139,.1);background:#fff;outline:none}
    .form-control:disabled,.form-control[readonly]{background:#f8fafc;color:#94a3b8;cursor:not-allowed}
    .input-group-text{font-size:.8rem;background:#f8fafc;border:1px solid #e2e8f0;color:#94a3b8}
    .form-text{font-size:.68rem;color:#94a3b8;margin-top:5px}
    .invalid-feedback{font-size:.7rem;color:#dc2626;margin-top:4px}
    .top-btn{display:inline-flex;align-items:center;gap:6px;padding:7px 16px;border-radius:8px;font-size:.78rem;font-weight:600;text-decoration:none;border:1px solid #e2e8f0;background:#fff;color:#475569;transition:background .15s,border-color .15s;cursor:pointer}
    .top-btn:hover{background:#f8fafc;border-color:#cbd5e1;color:#334155}
    .top-btn.primary{background:#1e293b;border-color:#1e293b;color:#fff}
    .top-btn.primary:hover{background:#334155;border-color:#334155;color:#fff}
    .error-box{background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:12px 16px;font-size:.78rem;color:#dc2626}

    /* máquina info */
    .machine-info-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px 24px}
    .machine-badge{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:6px;font-size:.68rem;font-weight:700;background:#f1f5f9;color:#475569;border:1px solid #e2e8f0}

    /* tabela de peças */
    .parts-table{width:100%;border-collapse:collapse}
    .parts-table thead th{font-size:.6rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#94a3b8;padding:8px 14px;background:#f8fafc;border-bottom:1px solid #e2e8f0;text-align:left}
    .parts-table tbody td{padding:8px 14px;border-bottom:1px solid #f1f5f9;vertical-align:middle}
    .parts-table tbody tr:last-child td{border-bottom:none}
    .btn-add-part{display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:7px;font-size:.72rem;font-weight:700;border:1px dashed #cbd5e1;background:#fff;color:#475569;cursor:pointer;transition:all .15s}
    .btn-add-part:hover{background:#f8fafc;border-color:#94a3b8}
    .btn-remove{width:26px;height:26px;border-radius:6px;border:1px solid #fecaca;background:#fff;color:#ef4444;font-size:.9rem;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;transition:background .15s}
    .btn-remove:hover{background:#fef2f2}

    /* drop zone */
    .drop-zone{border:1px dashed #cbd5e1;border-radius:10px;padding:24px;text-align:center;background:#f8fafc;cursor:pointer;transition:all .2s}
    .drop-zone:hover,.drop-zone.drop-active{border-color:#64748b;background:#f1f5f9}
    .drop-zone p{font-size:.8rem;color:#94a3b8;margin:0}
    .file-item{display:flex;align-items:center;justify-content:space-between;padding:6px 10px;border:1px solid #e2e8f0;border-radius:7px;font-size:.78rem;color:#475569;background:#fff;margin-top:6px}
</style>

<div class="page-wrap">

    @php
        $currentMachine = $maintenance->machine ?? $currentMachine ?? null;
        $currentStatus  = old('status', $maintenance->status ?? 'pendente');
        $isEdit         = (bool)($maintenance->id ?? false);
    @endphp

    {{-- CABEÇALHO --}}
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <div style="font-size:.6rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">
                {{ $isEdit ? 'Edição de Intervenção Técnica' : 'Nova Intervenção Técnica' }}
            </div>
            <h4 class="fw-bold mb-1" style="color:#1e293b;font-size:1.25rem;">
                @if($isEdit)
                    #{{ str_pad($maintenance->id, 4, '0', STR_PAD_LEFT) }}
                    <span style="color:#cbd5e1;font-weight:300;margin:0 6px;">·</span>
                    <span style="color:#475569;font-weight:600;font-size:1rem;">{{ $currentMachine->numero_interno ?? '' }}</span>
                @else
                    Criar Manutenção
                @endif
            </h4>
        </div>
        <a href="{{ $currentMachine ? route('machines.show', $currentMachine->id) : route('maintenances.index') }}"
           class="top-btn">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>

    {{-- ERROS --}}
    @if($errors->any())
    <div class="error-box mb-4">
        <div class="d-flex align-items-center gap-2 mb-1">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <strong>Por favor corrija os erros de validação.</strong>
        </div>
        <ul class="mb-0 ps-3 mt-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ $isEdit ? route('maintenances.update', $maintenance->id) : route('maintenances.store') }}"
          method="POST" id="maintenanceForm" enctype="multipart/form-data">
        @csrf
        @if($isEdit) @method('PUT') @endif

        {{-- 01 — MÁQUINA --}}
        <div class="section">
            <div class="section-head">
                <span class="s-num">01</span>
                <span class="s-title">Máquina Selecionada</span>
            </div>
            <div class="section-body">
                @if($currentMachine)
                    <input type="hidden" name="machine_id" value="{{ $currentMachine->id }}">
                    <div class="machine-info-grid">
                        <div>
                            <div class="field-lbl">Nº Interno</div>
                            <div class="field-val">{{ $currentMachine->numero_interno }}</div>
                        </div>
                        <div>
                            <div class="field-lbl">Tipo de Equipamento</div>
                            <div class="field-val">{{ $currentMachine->tipo_equipamento }}</div>
                        </div>
                        <div>
                            <div class="field-lbl">Estado</div>
                            <span class="machine-badge mt-1">{{ $currentMachine->status }}</span>
                        </div>
                        <div>
                            <div class="field-lbl">Marca / Modelo</div>
                            <div class="field-val">{{ $currentMachine->marca }} {{ $currentMachine->modelo }}</div>
                        </div>
                        <div>
                            <div class="field-lbl">Localização</div>
                            <div class="field-val">{{ $currentMachine->localizacao }}</div>
                        </div>
                    </div>
                @else
                    <label class="field-lbl" for="machine_id">
                        Selecionar Máquina <span style="color:#ef4444;">*</span>
                    </label>
                    <select name="machine_id" id="machine_id" class="form-select" required>
                        <option value="">— Selecione uma máquina —</option>
                        @foreach($machines as $machine)
                        <option value="{{ $machine->id }}"
                            {{ old('machine_id', $maintenance->machine_id ?? $selectedMachine ?? '') == $machine->id ? 'selected' : '' }}>
                            {{ $machine->numero_interno }} — {{ $machine->tipo_equipamento }} ({{ $machine->marca }})
                        </option>
                        @endforeach
                    </select>
                @endif
            </div>
        </div>

        {{-- 02 — DETALHES DA INTERVENÇÃO --}}
        <div class="section">
            <div class="section-head">
                <span class="s-num">02</span>
                <span class="s-title">Detalhes da Intervenção</span>
            </div>
            <div class="section-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="field-lbl" for="nome_motorista">
                            Motorista / Operador <span style="color:#ef4444;">*</span>
                        </label>
                        <input type="text" id="nome_motorista" name="nome_motorista"
                               class="form-control @error('nome_motorista') is-invalid @enderror"
                               value="{{ old('nome_motorista', $maintenance->nome_motorista ?? '') }}" required>
                        @error('nome_motorista')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="field-lbl" for="data_entrada">
                            Data de Entrada <span style="color:#ef4444;">*</span>
                        </label>
                        <input type="date" id="data_entrada" name="data_entrada"
                               class="form-control @error('data_entrada') is-invalid @enderror"
                               value="{{ old('data_entrada', optional($maintenance->data_entrada ?? now())->format('Y-m-d')) }}" required>
                        @error('data_entrada')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="field-lbl" for="status">Estado</label>
                        <select id="status" name="status" class="form-select" required>
                            <option value="pendente"      {{ $currentStatus == 'pendente'      ? 'selected' : '' }}>Pendente</option>
                            <option value="em_manutencao" {{ $currentStatus == 'em_manutencao' ? 'selected' : '' }}>Em Manutenção</option>
                            <option value="concluida"     {{ $currentStatus == 'concluida'     ? 'selected' : '' }}>Concluída</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="field-lbl" for="failure_description">
                            Descrição da Falha <span style="color:#ef4444;">*</span>
                        </label>
                        <textarea id="failure_description" name="failure_description" rows="3"
                                  class="form-control @error('failure_description') is-invalid @enderror"
                                  required>{{ old('failure_description', $maintenance->failure_description ?? '') }}</textarea>
                        @error('failure_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- 03 — DADOS OPERACIONAIS --}}
        <div class="section">
            <div class="section-head">
                <span class="s-num">03</span>
                <span class="s-title">Dados Operacionais e Referências</span>
            </div>
            <div class="section-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="field-lbl">Folha de Obra / Ref.</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-hash"></i></span>
                            <input type="text" name="work_sheet_ref"
                                   class="form-control"
                                   placeholder="Ex: FO-2024-001"
                                   value="{{ old('work_sheet_ref', $maintenance->work_sheet_ref ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="field-lbl">Horas / KMS na Entrada</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-speedometer2"></i></span>
                            <input type="number" name="hours_kms"
                                   class="form-control"
                                   placeholder="0"
                                   value="{{ old('hours_kms', $maintenance->hours_kms ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="field-lbl">Horas de Mão-de-Obra</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-clock-history"></i></span>
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

        {{-- 04 — PEÇAS DO STOCK --}}
        <div class="section">
            <div class="section-head">
                <span class="s-num">04</span>
                <span class="s-title">Peças e Consumíveis do Stock</span>
                <button type="button" class="btn-add-part ms-auto" id="addItem">
                    <i class="bi bi-plus"></i> Adicionar Peça
                </button>
            </div>
            <table class="parts-table">
                <thead>
                    <tr>
                        <th>Artigo / Peça</th>
                        <th style="width:160px;text-align:center;">Stock Actual</th>
                        <th style="width:160px;">Qtd. a Retirar</th>
                        <th style="width:44px;"></th>
                    </tr>
                </thead>
                <tbody id="itemsBody">
                    <tr class="item-row">
                        <td>
                            <select name="items[0][id]" class="form-select item-select" style="font-size:.8rem;">
                                <option value="">— Selecione a peça —</option>
                                @foreach($items as $item)
                                <option value="{{ $item->id }}" data-stock="{{ $item->quantidade }}">
                                    [{{ $item->referencia }}] {{ $item->nome }}
                                </option>
                                @endforeach
                            </select>
                        </td>
                        <td style="text-align:center;">
                            <input type="text" class="form-control stock-display text-center fw-bold"
                                   style="font-size:.8rem;" readonly value="0">
                        </td>
                        <td>
                            <input type="number" name="items[0][quantity]"
                                   class="form-control text-center" style="font-size:.8rem;" step="0.01" min="0">
                        </td>
                        <td style="text-align:center;">
                            <button type="button" class="btn-remove remove-item">×</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- 05 — CUSTOS + ANEXOS --}}
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <div class="section mb-0 h-100">
                    <div class="section-head">
                        <span class="s-num">05</span>
                        <span class="s-title">Custos e Prazos</span>
                    </div>
                    <div class="section-body">
                        <div class="mb-3">
                            <label class="field-lbl" for="total_cost">Custo Total Previsto (€)</label>
                            <input type="number" step="0.01" id="total_cost" name="total_cost"
                                   class="form-control"
                                   value="{{ old('total_cost', $maintenance->total_cost ?? 0) }}">
                        </div>
                        <div>
                            <label class="field-lbl" for="end_date">Data de Conclusão Efectiva</label>
                            <input type="datetime-local" id="end_date" name="end_date"
                                   class="form-control"
                                   value="{{ old('end_date', isset($maintenance->end_date) ? $maintenance->end_date->format('Y-m-d\TH:i') : '') }}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="section mb-0 h-100">
                    <div class="section-head">
                        <span class="s-num">06</span>
                        <span class="s-title">Anexos</span>
                        <span style="margin-left:auto;font-size:.65rem;color:#94a3b8;" id="fileStatus">0 ficheiros</span>
                    </div>
                    <div class="section-body">
                        <div class="drop-zone" id="dropZone">
                            <i class="bi bi-cloud-upload" style="font-size:1.4rem;color:#cbd5e1;display:block;margin-bottom:6px;"></i>
                            <p>Arraste ficheiros ou <strong style="color:#475569;">clique aqui</strong></p>
                            <input type="file" id="fileInput" multiple class="d-none">
                        </div>
                        <div id="fileList"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ACÇÕES --}}
        <div class="d-flex justify-content-between align-items-center mt-2"
             style="padding-top:16px;border-top:1px solid #f1f5f9;">
            <a href="{{ $currentMachine ? route('machines.show', $currentMachine->id) : route('maintenances.index') }}"
               class="top-btn">
                <i class="bi bi-x"></i> Cancelar
            </a>
            <button type="button" id="submitButton" class="top-btn primary px-5">
                <i class="bi bi-check-lg"></i>
                {{ $isEdit ? 'Actualizar Manutenção' : 'Criar Manutenção' }}
            </button>
        </div>

    </form>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

$(document).ready(function () {
    let attachedFiles = [];
    let itemIndex = 1;

    // --- Peças ---
    $(document).on('change', '.item-select', function () {
        const stock = $(this).find(':selected').data('stock') || 0;
        $(this).closest('tr').find('.stock-display').val(stock);
    });

    $('#addItem').on('click', function () {
        let newRow = $('.item-row').first().clone();
        newRow.find('select').attr('name', `items[${itemIndex}][id]`).val('');
        newRow.find('input[type="number"]').attr('name', `items[${itemIndex}][quantity]`).val('');
        newRow.find('.stock-display').val('0');
        $('#itemsBody').append(newRow);
        itemIndex++;
    });

    $(document).on('click', '.remove-item', function () {
        if ($('.item-row').length > 1) $(this).closest('tr').remove();
    });

    // --- Ficheiros ---
    const dropZone = $('#dropZone');
    const fileInput = $('#fileInput');

    dropZone.on('click', () => fileInput.click());
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(n => {
        dropZone.on(n, e => { e.preventDefault(); e.stopPropagation(); });
    });
    dropZone.on('dragover', () => dropZone.addClass('drop-active'));
    dropZone.on('dragleave drop', () => dropZone.removeClass('drop-active'));
    dropZone.on('drop', e => handleFiles(e.originalEvent.dataTransfer.files));
    fileInput.on('change', function () { handleFiles(this.files); });

    function handleFiles(files) {
        for (let f of files) attachedFiles.push(f);
        renderFiles();
    }

    function renderFiles() {
        const list = $('#fileList');
        list.empty();
        attachedFiles.forEach((f, i) => {
            list.append(`<div class="file-item">
                <span><i class="bi bi-file-earmark me-2"></i>${f.name}</span>
                <button type="button" class="btn-remove" onclick="removeFile(${i})">×</button>
            </div>`);
        });
        $('#fileStatus').text(attachedFiles.length + ' ficheiro(s)');
    }

    window.removeFile = i => { attachedFiles.splice(i, 1); renderFiles(); };

    // --- Submissão ---
    $('#submitButton').on('click', function () {
        const btn = $(this);
        const form = $('#maintenanceForm');
        if (!form[0].checkValidity()) { form[0].reportValidity(); return; }

        const formData = new FormData(form[0]);
        attachedFiles.forEach(f => formData.append('maintenance_files[]', f));

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>A Guardar...');

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: res => { window.location.href = res.redirect_url; },
            error: xhr => {
                btn.prop('disabled', false).html('<i class="bi bi-check-lg"></i> Tentar Novamente');
                alert(xhr.responseJSON?.message || 'Erro ao guardar.');
            }
        });
    });
});
</script>
</x-app-layout>
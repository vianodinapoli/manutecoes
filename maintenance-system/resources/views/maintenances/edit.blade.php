<x-app-layout>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    .page-wrap{max-width:760px;margin:0 auto;padding:32px 24px 48px}
    .section{background:#fff;border:1px solid #e2e8f0;border-radius:12px;margin-bottom:16px;overflow:hidden}
    .section-head{display:flex;align-items:center;gap:10px;padding:12px 20px;background:#f8fafc;border-bottom:1px solid #e2e8f0}
    .section-head .s-num{font-size:.6rem;font-weight:800;letter-spacing:1.5px;color:#94a3b8;text-transform:uppercase;background:#e2e8f0;border-radius:4px;padding:2px 7px}
    .section-head .s-title{font-size:.8rem;font-weight:700;color:#334155;letter-spacing:.3px}
    .section-body{padding:20px 24px}
    .field-lbl{font-size:.6rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#94a3b8;display:block;margin-bottom:5px}
    .form-control,.form-select{font-size:.83rem;border:1px solid #e2e8f0;border-radius:8px;color:#1e293b;background:#fff;transition:border-color .15s,box-shadow .15s}
    .form-control:focus,.form-select:focus{border-color:#64748b;box-shadow:0 0 0 3px rgba(100,116,139,.1);background:#fff}
    .form-control:disabled,.form-control[readonly]{background:#f8fafc;color:#94a3b8;cursor:not-allowed}
    .form-text{font-size:.68rem;color:#94a3b8;margin-top:5px}
    .top-btn{display:inline-flex;align-items:center;gap:6px;padding:7px 16px;border-radius:8px;font-size:.78rem;font-weight:600;text-decoration:none;border:1px solid #e2e8f0;background:#fff;color:#475569;transition:background .15s,border-color .15s;cursor:pointer}
    .top-btn:hover{background:#f8fafc;border-color:#cbd5e1;color:#334155}
    .top-btn.primary{background:#1e293b;border-color:#1e293b;color:#fff}
    .top-btn.primary:hover{background:#334155;border-color:#334155;color:#fff}
    .inner-divider{height:1px;background:#f1f5f9;margin:4px 0 20px}
    .error-box{background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:12px 16px;font-size:.78rem;color:#dc2626;margin-bottom:16px}
    .invalid-feedback{font-size:.7rem;color:#dc2626;margin-top:4px;}
</style>

<div class="page-wrap">

    {{-- CABEÇALHO --}}
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <div style="font-size:.6rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">
                Edição de Intervenção Técnica
            </div>
            <h4 class="fw-bold mb-1" style="color:#1e293b;font-size:1.25rem;">
                #{{ str_pad($maintenance->id, 4, '0', STR_PAD_LEFT) }}
                <span style="color:#cbd5e1;font-weight:300;margin:0 6px;">·</span>
                <span style="color:#475569;font-weight:600;font-size:1rem;">{{ $machine->name }}</span>
            </h4>
            <div style="font-size:.72rem;color:#94a3b8;">
                Nº Série: {{ $machine->serial_number }}
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('machines.show', $machine->id) }}" class="top-btn">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    {{-- ERROS --}}
    @if($errors->any())
    <div class="error-box mb-4">
        <div class="d-flex align-items-center gap-2 mb-1">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <strong>Por favor corrija os erros de validação abaixo.</strong>
        </div>
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('maintenances.update', $maintenance->id) }}">
        @csrf
        @method('PUT')

        {{-- 01 — DESCRIÇÃO DA AVARIA (só leitura) --}}
        <div class="section">
            <div class="section-head">
                <span class="s-num">01</span>
                <span class="s-title">Descrição da Avaria</span>
                <span style="margin-left:auto;font-size:.65rem;color:#94a3b8;background:#f1f5f9;border-radius:4px;padding:2px 8px;font-weight:600;">
                    <i class="bi bi-lock-fill me-1"></i>Não editável
                </span>
            </div>
            <div class="section-body">
                <label class="field-lbl">Ocorrência registada</label>
                <textarea class="form-control" rows="3" readonly disabled
                          style="resize:none;">{{ $maintenance->failure_description }}</textarea>
                <div class="form-text">A descrição inicial da avaria não pode ser alterada após o registo.</div>
            </div>
        </div>

        {{-- 02 — ESTADO --}}
        <div class="section">
            <div class="section-head">
                <span class="s-num">02</span>
                <span class="s-title">Estado da Manutenção</span>
            </div>
            <div class="section-body">
                <label class="field-lbl" for="status">Estado actual</label>
                <select id="status" name="status"
                        class="form-select @error('status') is-invalid @enderror" required>
                    <option value="">Selecione o estado...</option>
                    @foreach($statuses as $status)
                    <option value="{{ $status }}"
                        {{ old('status', $maintenance->status) == $status ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                    </option>
                    @endforeach
                </select>
                @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- 03 — DATAS --}}
        <div class="section">
            <div class="section-head">
                <span class="s-num">03</span>
                <span class="s-title">Datas de Intervenção</span>
            </div>
            <div class="section-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="field-lbl" for="scheduled_date">Data Agendada</label>
                        <input type="datetime-local" id="scheduled_date" name="scheduled_date"
                               class="form-control @error('scheduled_date') is-invalid @enderror"
                               value="{{ old('scheduled_date', optional($maintenance->scheduled_date)->format('Y-m-d\TH:i')) }}">
                        @error('scheduled_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="field-lbl" for="start_date">Início Real</label>
                        <input type="datetime-local" id="start_date" name="start_date"
                               class="form-control @error('start_date') is-invalid @enderror"
                               value="{{ old('start_date', optional($maintenance->start_date)->format('Y-m-d\TH:i')) }}">
                        @error('start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="field-lbl" for="end_date">Data de Conclusão</label>
                        <input type="datetime-local" id="end_date" name="end_date"
                               class="form-control @error('end_date') is-invalid @enderror"
                               value="{{ old('end_date', optional($maintenance->end_date)->format('Y-m-d\TH:i')) }}">
                        @error('end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- 04 — NOTAS DO TÉCNICO --}}
        <div class="section">
            <div class="section-head">
                <span class="s-num">04</span>
                <span class="s-title">Notas do Técnico</span>
            </div>
            <div class="section-body">
                <label class="field-lbl" for="technician_notes">Resumo da intervenção</label>
                <textarea id="technician_notes" name="technician_notes" rows="5"
                          class="form-control @error('technician_notes') is-invalid @enderror"
                          placeholder="Descreva as acções realizadas, peças substituídas, observações relevantes..."
                          style="resize:vertical;">{{ old('technician_notes', $maintenance->technician_notes) }}</textarea>
                @error('technician_notes')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- ACÇÕES --}}
        <div class="d-flex justify-content-between align-items-center mt-2"
             style="padding-top:16px;border-top:1px solid #f1f5f9;">
            <a href="{{ route('machines.show', $machine->id) }}" class="top-btn">
                <i class="bi bi-x"></i> Cancelar
            </a>
            <button type="submit" class="top-btn primary px-4">
                <i class="bi bi-check-lg"></i> Guardar Alterações
            </button>
        </div>

    </form>
</div>
</x-app-layout>
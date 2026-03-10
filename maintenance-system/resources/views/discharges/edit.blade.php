{{--
    View parcial — carregada via fetch no modalPrincipal do index
    NÃO usa x-app-layout
--}}
@if ($errors->any())
<div class="alert alert-danger py-2 small mx-1 mb-3">
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form action="{{ route('discharges.update', $discharge->id) }}" method="POST" id="formEditar">
    @csrf
    @method('PUT')

    {{-- Identificação --}}
    <h6 class="fw-bold text-warning border-bottom pb-2 mb-3"><i class="bi bi-card-list me-1"></i> Identificação</h6>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="small fw-bold text-muted">Data</label>
            <input type="date" name="data" class="form-control @error('data') is-invalid @enderror"
                   value="{{ old('data', $discharge->data->format('Y-m-d')) }}" required>
            @error('data')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
            <label class="small fw-bold text-muted">Número de Guia</label>
            <input type="text" name="numero_guia" class="form-control @error('numero_guia') is-invalid @enderror"
                   value="{{ old('numero_guia', $discharge->numero_guia) }}" required>
            @error('numero_guia')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
            <label class="small fw-bold text-muted">Hora de Saída do Porto</label>
            <input type="time" name="hora_saida_porto" class="form-control"
                   value="{{ old('hora_saida_porto', $discharge->hora_saida_porto) }}">
        </div>
    </div>

    {{-- Transporte --}}
    <h6 class="fw-bold text-warning border-bottom pb-2 mb-3"><i class="bi bi-truck me-1"></i> Transporte</h6>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="small fw-bold text-muted">Motorista</label>
            <input type="text" name="motorista" class="form-control @error('motorista') is-invalid @enderror"
                   value="{{ old('motorista', $discharge->motorista) }}" required>
            @error('motorista')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
            <label class="small fw-bold text-muted">Matrícula</label>
            <input type="text" name="matricula" class="form-control @error('matricula') is-invalid @enderror"
                   value="{{ old('matricula', $discharge->matricula) }}" required>
            @error('matricula')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
            <label class="small fw-bold text-muted">Transportadora</label>
            <input type="text" name="transportadora" class="form-control @error('transportadora') is-invalid @enderror"
                   value="{{ old('transportadora', $discharge->transportadora) }}" required>
            @error('transportadora')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    {{-- Carga --}}
    <h6 class="fw-bold text-warning border-bottom pb-2 mb-3"><i class="bi bi-box-seam me-1"></i> Carga — Nitrato de Amónio</h6>

    {{-- Alta --}}
    <div class="card border-0 mb-3" style="background:#e8f0fe;border-left:4px solid #1a56db !important;border-radius:8px;">
        <div class="card-body py-3 px-4">
            <div class="d-flex align-items-center mb-2">
                <span class="badge me-2" style="background:#1a56db;">ALTA</span>
                <span class="small text-muted">Sacos de alta densidade</span>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="small fw-bold text-muted">Nº Sacos Alta</label>
                    <div class="input-group">
                        <button type="button" class="btn btn-outline-primary btn-sm px-3" onclick="editSetAlta(24)">24</button>
                        <button type="button" class="btn btn-outline-primary btn-sm px-3" onclick="editSetAlta(36)">36</button>
                        <input type="number" name="sacos_alta" id="e_sacos_alta" class="form-control text-center"
                               value="{{ old('sacos_alta', $discharge->sacos_alta ?? 0) }}" min="0" oninput="editCalc()" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold text-muted">Peso por Saco (kg)</label>
                    <input type="number" name="peso_saco_alta" id="e_peso_saco_alta" class="form-control"
                           value="{{ old('peso_saco_alta', $discharge->peso_saco_alta ?? 1200) }}" step="0.01" min="0" oninput="editCalc()">
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold text-muted">Subtotal Alta</label>
                    <div class="input-group">
                        <input type="text" id="e_subtotal_alta" class="form-control bg-white fw-bold text-primary" readonly>
                        <span class="input-group-text small">kg</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Baixa --}}
    <div class="card border-0 mb-3" style="background:#fde8e8;border-left:4px solid #dc3545 !important;border-radius:8px;">
        <div class="card-body py-3 px-4">
            <div class="d-flex align-items-center mb-2">
                <span class="badge bg-danger me-2">BAIXA</span>
                <span class="small text-muted">Sacos de baixa densidade</span>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="small fw-bold text-muted">Nº Sacos Baixa</label>
                    <div class="input-group">
                        <button type="button" class="btn btn-outline-danger btn-sm px-3" onclick="editSetBaixa(24)">24</button>
                        <button type="button" class="btn btn-outline-danger btn-sm px-3" onclick="editSetBaixa(36)">36</button>
                        <input type="number" name="sacos_baixa" id="e_sacos_baixa" class="form-control text-center"
                               value="{{ old('sacos_baixa', $discharge->sacos_baixa ?? 0) }}" min="0" oninput="editCalc()" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold text-muted">Peso por Saco (kg)</label>
                    <input type="number" name="peso_saco_baixa" id="e_peso_saco_baixa" class="form-control"
                           value="{{ old('peso_saco_baixa', $discharge->peso_saco_baixa ?? 1000) }}" step="0.01" min="0" oninput="editCalc()">
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold text-muted">Subtotal Baixa</label>
                    <div class="input-group">
                        <input type="text" id="e_subtotal_baixa" class="form-control bg-white fw-bold text-danger" readonly>
                        <span class="input-group-text small">kg</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Totais --}}
    <div class="card border-0 mb-4" style="background:#f0fdf4;border-left:4px solid #198754 !important;border-radius:8px;">
        <div class="card-body py-3 px-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-6">
                    <label class="small fw-bold text-muted">Total de Sacos</label>
                    <input type="text" id="e_total_sacos" class="form-control bg-white fw-bold text-success" readonly>
                </div>
                <div class="col-md-6">
                    <label class="small fw-bold text-muted">Peso Estimado Total</label>
                    <div class="input-group">
                        <input type="text" id="e_total_peso" class="form-control bg-white fw-bold fs-5 text-success" readonly>
                        <span class="input-group-text fw-bold">kg</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-2">
        <button type="button" class="btn btn-light border px-4"
                onclick="bootstrap.Modal.getInstance(document.getElementById('modalPrincipal')).hide()">
            Cancelar
        </button>
        <button type="submit" class="btn btn-warning px-5 fw-bold">
            <i class="bi bi-check-circle me-1"></i>Guardar Alterações
        </button>
    </div>
</form>

<script>
function editSetAlta(n)  { document.getElementById('e_sacos_alta').value  = n; editCalc(); }
function editSetBaixa(n) { document.getElementById('e_sacos_baixa').value = n; editCalc(); }
function editCalc() {
    const sA = parseFloat(document.getElementById('e_sacos_alta').value)      || 0;
    const pA = parseFloat(document.getElementById('e_peso_saco_alta').value)  || 0;
    const sB = parseFloat(document.getElementById('e_sacos_baixa').value)     || 0;
    const pB = parseFloat(document.getElementById('e_peso_saco_baixa').value) || 0;
    document.getElementById('e_subtotal_alta').value  = (sA * pA).toLocaleString('pt-MZ');
    document.getElementById('e_subtotal_baixa').value = (sB * pB).toLocaleString('pt-MZ');
    document.getElementById('e_total_sacos').value    = sA + sB;
    document.getElementById('e_total_peso').value     = (sA * pA + sB * pB).toLocaleString('pt-MZ');
}
// Calcular ao carregar com valores existentes
editCalc();
</script>
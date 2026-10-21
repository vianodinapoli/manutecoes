{{--
    View parcial — carregada via fetch no modalPrincipal do index
    NÃO usa x-app-layout
    Recebe: $discharge, $operation, $items (saldo sem contar este camião), $actuais [id produto => qtd]
--}}
@php
    $palette = [
        ['bg' => '#e8f0fe', 'bd' => '#1a56db', 'tx' => '#1a56db'],
        ['bg' => '#fde8e8', 'bd' => '#dc3545', 'tx' => '#dc3545'],
        ['bg' => '#e6f4ec', 'bd' => '#198754', 'tx' => '#198754'],
        ['bg' => '#fff3cd', 'bd' => '#fd7e14', 'tx' => '#b35c00'],
        ['bg' => '#f3e8ff', 'bd' => '#6f42c1', 'tx' => '#6f42c1'],
        ['bg' => '#e0f7fc', 'bd' => '#0aa2c0', 'tx' => '#0aa2c0'],
    ];
    $fmt = fn ($n) => rtrim(rtrim(number_format($n, 3, ',', '.'), '0'), ',');
@endphp

@if ($errors->any())
<div class="alert alert-danger py-2 small mx-1 mb-3">
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form action="{{ route('discharges.update', $discharge->id) }}" method="POST" id="formEditar">
    @csrf
    @method('PUT')

    <div class="alert py-2 mb-3 border-0" style="border-radius:8px;background:#fff8e6;color:#856404;font-size:.78rem;">
        <i class="bi bi-ship me-1"></i>Operação <strong>{{ $operation->navio }}</strong> · {{ $operation->empresa }}
    </div>

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

    {{-- Carga: uma linha por produto do plano --}}
    <h6 class="fw-bold text-warning border-bottom pb-2 mb-3"><i class="bi bi-box-seam me-1"></i> Carga — definida no plano da operação</h6>

    @foreach($items as $i => $it)
    @php
        $c      = $palette[$i % count($palette)];
        $kgUn   = $it->unidade === 'tonelada' ? 1000 : (float) $it->peso_unitario_kg;
        $saldo  = round((float) $it->quantidade - (float) ($it->registado ?? 0), 3); // sem contar este camião
        $isSaco = $it->unidade === 'saco';
        $valor  = old('items.'.$it->id, $actuais[$it->id] ?? 0);
    @endphp
    <div class="card border-0 mb-3 ed-item" data-kg="{{ $kgUn }}" data-saldo="{{ $saldo }}" data-saco="{{ $isSaco ? 1 : 0 }}"
         style="background:{{ $c['bg'] }};border-left:4px solid {{ $c['bd'] }} !important;border-radius:8px;">
        <div class="card-body py-3 px-4">
            <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                <div>
                    <span class="badge me-2" style="background:{{ $c['bd'] }};">{{ mb_strtoupper($it->nome) }}</span>
                    <span class="small text-muted">
                        @if($isSaco) {{ $fmt($kgUn) }} kg por saco @else a granel (toneladas) @endif
                    </span>
                </div>
                <div class="small text-muted">
                    Plano <b>{{ $fmt($it->quantidade) }}</b> ·
                    Outros camiões <b>{{ $fmt($it->registado ?? 0) }}</b> ·
                    <span class="fw-bold" style="color:{{ $c['tx'] }};">Máx. neste camião {{ $fmt($saldo) }} {{ $it->unidade_label }}</span>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="small fw-bold text-muted">{{ $isSaco ? 'Nº de sacos' : 'Toneladas' }}</label>
                    <div class="input-group">
                        @if($isSaco)
                            <button type="button" class="btn btn-outline-secondary btn-sm px-3" onclick="edSet(this, 24)">24</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm px-3" onclick="edSet(this, 36)">36</button>
                        @endif
                        <input type="number" name="items[{{ $it->id }}]" class="form-control text-center ed-qtd @error('items.'.$it->id) is-invalid @enderror"
                               value="{{ $valor }}" min="0" max="{{ $saldo }}" step="{{ $isSaco ? 1 : 0.001 }}" oninput="edCalc()">
                    </div>
                    <div class="ed-aviso small text-danger fw-bold mt-1" style="display:none;"></div>
                </div>
                <div class="col-md-6">
                    <label class="small fw-bold text-muted">Subtotal</label>
                    <div class="input-group">
                        <input type="text" class="form-control bg-white fw-bold ed-sub" style="color:{{ $c['tx'] }};" readonly>
                        <span class="input-group-text small">kg</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    {{-- Totais --}}
    <div class="card border-0 mb-4" style="background:#f0fdf4;border-left:4px solid #198754 !important;border-radius:8px;">
        <div class="card-body py-3 px-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-6">
                    <label class="small fw-bold text-muted">Total de Sacos</label>
                    <input type="text" id="ed_total_sacos" class="form-control bg-white fw-bold text-success" readonly>
                </div>
                <div class="col-md-6">
                    <label class="small fw-bold text-muted">Peso Estimado Total</label>
                    <div class="input-group">
                        <input type="text" id="ed_total_peso" class="form-control bg-white fw-bold fs-5 text-success" readonly>
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
        <button type="submit" class="btn btn-warning px-5 fw-bold" id="edSubmit">
            <i class="bi bi-check-circle me-1"></i>Guardar Alterações
        </button>
    </div>
</form>

<script>
function edSet(btn, n) {
    btn.closest('.input-group').querySelector('.ed-qtd').value = n;
    edCalc();
}
function edCalc() {
    let sacos = 0, kg = 0, excede = false;
    document.querySelectorAll('#formEditar .ed-item').forEach(card => {
        const inp   = card.querySelector('.ed-qtd');
        const q     = parseFloat(inp.value) || 0;
        const kgUn  = parseFloat(card.dataset.kg) || 0;
        const saldo = parseFloat(card.dataset.saldo) || 0;
        const aviso = card.querySelector('.ed-aviso');

        card.querySelector('.ed-sub').value = (q * kgUn).toLocaleString('pt-MZ');
        kg += q * kgUn;
        if (card.dataset.saco === '1') sacos += q;

        if (q > saldo + 0.0005) {
            aviso.textContent = 'Ultrapassa o plano. Máximo para este camião: ' + saldo.toLocaleString('pt-MZ');
            aviso.style.display = 'block';
            excede = true;
        } else {
            aviso.style.display = 'none';
        }
    });
    document.getElementById('ed_total_sacos').value = sacos;
    document.getElementById('ed_total_peso').value  = kg.toLocaleString('pt-MZ');
    document.getElementById('edSubmit').disabled = excede || kg <= 0;
}
edCalc();
</script>
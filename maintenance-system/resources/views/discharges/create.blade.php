{{--
    View parcial — carregada via fetch no modalPrincipal do index
    NÃO usa x-app-layout
    Recebe: $operation, $items (produtos do plano com ->registado e ->saldo)
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

<form action="{{ route('discharges.store') }}" method="POST" id="formCriar">
    @csrf
    <input type="hidden" name="operation_id" value="{{ $operation->id }}">

    <div class="alert py-2 mb-3 border-0" style="border-radius:8px;background:#e8f4fd;color:#1a56db;font-size:.78rem;">
        <i class="bi bi-ship me-1"></i>Operação <strong>{{ $operation->navio }}</strong>
        · {{ $operation->empresa }}
        @if($operation->bl) · BL {{ $operation->bl }} @endif
    </div>

    {{-- Identificação --}}
    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3"><i class="bi bi-card-list me-1"></i> Identificação</h6>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="small fw-bold text-muted">Data</label>
            <input type="date" name="data" class="form-control @error('data') is-invalid @enderror"
                   value="{{ old('data', date('Y-m-d')) }}" required>
            @error('data')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
            <label class="small fw-bold text-muted">Número de Guia</label>
            <input type="text" name="numero_guia" class="form-control @error('numero_guia') is-invalid @enderror"
                   value="{{ old('numero_guia') }}" placeholder="Ex: GU-2026-001" required>
            @error('numero_guia')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
            <label class="small fw-bold text-muted">Hora de Saída do Porto</label>
            <input type="time" name="hora_saida_porto" class="form-control" value="{{ old('hora_saida_porto') }}">
        </div>
    </div>

    {{-- Transporte --}}
    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3"><i class="bi bi-truck me-1"></i> Transporte</h6>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="small fw-bold text-muted">Motorista</label>
            <input type="text" name="motorista" class="form-control @error('motorista') is-invalid @enderror"
                   value="{{ old('motorista') }}" placeholder="Nome completo" required>
            @error('motorista')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
            <label class="small fw-bold text-muted">Matrícula</label>
            <input type="text" name="matricula" class="form-control @error('matricula') is-invalid @enderror"
                   value="{{ old('matricula') }}" placeholder="Ex: MZB-1234-A" required>
            @error('matricula')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
            @php
                $lista_cr  = collect($operation->transportadoras ?? []);
                $valTp_cr  = trim((string) old('transportadora', ''));
                $naLista_cr = $valTp_cr !== '' && $lista_cr->contains(fn ($t) => mb_strtolower($t['nome']) === mb_strtolower($valTp_cr));
            @endphp
            <label class="small fw-bold text-muted">Transportadora</label>

            @if($lista_cr->isNotEmpty())
                {{-- Lista da operação + opção "Outra" --}}
                <select id="cr_transp_sel" class="form-select @error('transportadora') is-invalid @enderror"
                        onchange="crTranspChange()" required>
                    <option value="">Seleccione…</option>
                    @foreach($lista_cr as $t)
                        <option value="{{ $t['nome'] }}"
                                @selected($naLista_cr && mb_strtolower($valTp_cr) === mb_strtolower($t['nome']))>
                            {{ !empty($t['extra']) ? '⚠ ' : '' }}{{ $t['nome'] }}
                        </option>
                    @endforeach
                    <option value="__nova__" @selected($valTp_cr !== '' && ! $naLista_cr)>＋ Outra (não está na lista)…</option>
                </select>

                <input type="text" id="cr_transp_nova" class="form-control mt-2"
                       style="display:none;border:2px solid #fd7e14;background:#fff4e6;"
                       placeholder="Nome da transportadora" maxlength="100"
                       value="{{ $valTp_cr !== '' && ! $naLista_cr ? $valTp_cr : '' }}"
                       oninput="crTranspSync()">

                <div id="cr_transp_aviso" class="mt-1"
                     style="display:none;background:#fd7e14;color:#fff;border-radius:6px;padding:4px 8px;font-size:.72rem;font-weight:600;">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>Fora da lista desta operação. Será acrescentada e ficará a laranja.
                </div>

                <input type="hidden" name="transportadora" id="cr_transp_val" value="{{ $valTp_cr }}">
            @else
                {{-- Operação sem lista: campo livre --}}
                <input type="text" name="transportadora" class="form-control @error('transportadora') is-invalid @enderror"
                       value="{{ $valTp_cr }}" placeholder="Nome da empresa" required>
            @endif
            @error('transportadora')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        {{-- Tipo de atrelado (definido no porto) --}}
        <div class="col-md-4">
            @php $atr_cr = old('tipo_atrelado', null); @endphp
            <label class="small fw-bold text-muted">Tipo de atrelado</label>
            <div class="btn-group w-100" role="group">
                <input type="radio" class="btn-check" name="tipo_atrelado" id="cr_atr_normal" value="normal"
                       autocomplete="off" required onchange="crCalc()" {{ $atr_cr === 'normal' ? 'checked' : '' }}>
                <label class="btn btn-outline-primary" for="cr_atr_normal">Normal <small>(até 24)</small></label>

                <input type="radio" class="btn-check" name="tipo_atrelado" id="cr_atr_link" value="link"
                       autocomplete="off" onchange="crCalc()" {{ $atr_cr === 'link' ? 'checked' : '' }}>
                <label class="btn btn-outline-danger" for="cr_atr_link">Link <small>(mais de 24)</small></label>
            </div>
            @error('tipo_atrelado')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            <div id="cr_atr_aviso" class="mt-1"
                 style="display:none;background:#fd7e14;color:#fff;border-radius:6px;padding:4px 8px;font-size:.72rem;font-weight:600;"></div>
        </div>
    </div>

    {{-- Carga: uma linha por produto do plano --}}
    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3"><i class="bi bi-box-seam me-1"></i> Carga — definida no plano da operação</h6>

    @foreach($items as $i => $it)
    @php
        $c      = $palette[$i % count($palette)];
        $kgUn   = $it->unidade === 'tonelada' ? 1000 : (float) $it->peso_unitario_kg;
        $saldo  = round((float) $it->quantidade - (float) ($it->registado ?? 0), 3);
        $isSaco = $it->unidade === 'saco';
    @endphp
    <div class="card border-0 mb-3 cr-item" data-kg="{{ $kgUn }}" data-saldo="{{ $saldo }}" data-saco="{{ $isSaco ? 1 : 0 }}"
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
                    No porto <b>{{ $fmt($it->registado ?? 0) }}</b> ·
                    <span class="fw-bold" style="color:{{ $saldo > 0 ? $c['tx'] : '#dc3545' }};">Saldo {{ $fmt($saldo) }} {{ $it->unidade_label }}</span>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="small fw-bold text-muted">{{ $isSaco ? 'Nº de sacos' : 'Toneladas' }}</label>
                    <div class="input-group">
                        @if($isSaco)
                            <button type="button" class="btn btn-outline-secondary btn-sm px-3" onclick="crSet(this, 24)">24</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm px-3" onclick="crSet(this, 36)">36</button>
                        @endif
                        <input type="number" name="items[{{ $it->id }}]" class="form-control text-center cr-qtd @error('items.'.$it->id) is-invalid @enderror"
                               value="{{ old('items.'.$it->id, 0) }}" min="0" max="{{ $saldo }}" step="{{ $isSaco ? 1 : 0.001 }}"
                               oninput="crCalc()" {{ $saldo <= 0 ? 'readonly' : '' }}>
                    </div>
                    <div class="cr-aviso small text-danger fw-bold mt-1" style="display:none;"></div>
                </div>
                <div class="col-md-6">
                    <label class="small fw-bold text-muted">Subtotal</label>
                    <div class="input-group">
                        <input type="text" class="form-control bg-white fw-bold cr-sub" style="color:{{ $c['tx'] }};" readonly placeholder="0">
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
                    <input type="text" id="cr_total_sacos" class="form-control bg-white fw-bold text-success" readonly placeholder="0">
                </div>
                <div class="col-md-6">
                    <label class="small fw-bold text-muted">Peso Estimado Total</label>
                    <div class="input-group">
                        <input type="text" id="cr_total_peso" class="form-control bg-white fw-bold fs-5 text-success" readonly placeholder="0">
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
        <button type="submit" class="btn btn-primary px-5 fw-bold" id="crSubmit">
            <i class="bi bi-check-circle me-1"></i>Registar Descarga
        </button>
    </div>
</form>

<script>
function crSet(btn, n) {
    btn.closest('.input-group').querySelector('.cr-qtd').value = n;
    crCalc();
}
function crCalc() {
    let sacos = 0, kg = 0, excede = false;
    document.querySelectorAll('#formCriar .cr-item').forEach(card => {
        const inp   = card.querySelector('.cr-qtd');
        const q     = parseFloat(inp.value) || 0;
        const kgUn  = parseFloat(card.dataset.kg) || 0;
        const saldo = parseFloat(card.dataset.saldo) || 0;
        const aviso = card.querySelector('.cr-aviso');

        card.querySelector('.cr-sub').value = (q * kgUn).toLocaleString('pt-MZ');
        kg += q * kgUn;
        if (card.dataset.saco === '1') sacos += q;

        if (q > saldo + 0.0005) {
            aviso.textContent = 'Ultrapassa o plano. Saldo disponível: ' + saldo.toLocaleString('pt-MZ');
            aviso.style.display = 'block';
            excede = true;
        } else {
            aviso.style.display = 'none';
        }
    });
    document.getElementById('cr_total_sacos').value = sacos;
    crAtreladoAviso(sacos);
    document.getElementById('cr_total_peso').value  = kg.toLocaleString('pt-MZ');
    document.getElementById('crSubmit').disabled = excede || kg <= 0;
}
crCalc();

function crTranspChange() {
    const sel = document.getElementById('cr_transp_sel');
    if (!sel) return;
    const nova  = document.getElementById('cr_transp_nova');
    const aviso = document.getElementById('cr_transp_aviso');
    const val   = document.getElementById('cr_transp_val');
    const outra = sel.value === '__nova__';

    nova.style.display  = outra ? 'block' : 'none';
    aviso.style.display = outra ? 'block' : 'none';
    nova.required       = outra;
    val.value           = outra ? nova.value.trim() : sel.value;
}
function crTranspSync() {
    document.getElementById('cr_transp_val').value =
        document.getElementById('cr_transp_nova').value.trim();
}

function crAtreladoAviso(sacos) {
    const sel = document.querySelector('input[name=tipo_atrelado]:checked');
    const av  = document.getElementById('cr_atr_aviso');
    if (!sel || sacos <= 0) { av.style.display = 'none'; return; }

    const normal = sel.value === 'normal';
    const incons = (normal && sacos > 24) || (!normal && sacos <= 24);
    av.style.display = incons ? 'block' : 'none';
    av.textContent = normal
        ? '⚠ ' + sacos + ' sacos é mais do que um atrelado normal (24). Confirme o tipo.'
        : '⚠ ' + sacos + ' sacos não passa de 24. É mesmo um atrelado link?';
}
crTranspChange();
</script>
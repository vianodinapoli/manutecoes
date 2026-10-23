{{--
    View parcial — carregada via fetch no modalPrincipal do index
    NÃO usa x-app-layout
    Recebe: $discharge (com items.operationItem, operation, registeredBy, confirmedBy)
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
    $fmt = fn ($n) => rtrim(rtrim(number_format((float) $n, 3, ',', '.'), '0'), ',');
    $statusClass = match($discharge->status) {
        'confirmed'  => 'success',
        'in_transit' => 'info',
        default      => 'warning',
    };
    $hasDivergence = $discharge->hasDivergence();
    $arquivada     = $discharge->operation?->isArchived();
@endphp

{{-- Badges de estado --}}
<div class="d-flex align-items-center gap-2 mb-4 flex-wrap">
    <span class="badge bg-{{ $statusClass }} bg-opacity-10 text-{{ $statusClass }} border border-{{ $statusClass }} border-opacity-25 px-3 py-2" style="font-size:.78rem;">
        {{ $discharge->status_label }}
    </span>
    @if($hasDivergence)
    <span class="badge" style="background:#fff3cd;color:#856404;border:1px solid #ffc107;font-size:.72rem;">
        <i class="bi bi-exclamation-triangle-fill me-1"></i>Divergência entre o porto e a balança
    </span>
    @endif
    @if($discharge->operation)
    <span class="badge bg-light text-dark border" style="font-size:.72rem;">
        <i class="bi bi-ship me-1"></i>{{ $discharge->operation->navio }}
    </span>
    @endif
</div>

<div class="row g-4">
    {{-- Identificação --}}
    <div class="col-12">
        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3"><i class="bi bi-card-list me-1"></i> Identificação</h6>
        <div class="row g-3">
            <div class="col-md-3">
                <div class="small text-muted fw-bold mb-1">Data</div>
                <div class="fw-semibold">{{ $discharge->data->format('d/m/Y') }}</div>
            </div>
            <div class="col-md-3">
                <div class="small text-muted fw-bold mb-1">Número de Guia</div>
                <div class="fw-bold text-primary">{{ $discharge->numero_guia }}</div>
            </div>
            <div class="col-md-3">
                <div class="small text-muted fw-bold mb-1">Hora Saída Porto</div>
                <div class="fw-semibold">{{ $discharge->hora_saida_porto ?? '—' }}</div>
            </div>
            <div class="col-md-3">
                <div class="small text-muted fw-bold mb-1">Registado por</div>
                <div class="fw-semibold">{{ $discharge->registeredBy->name ?? '—' }}</div>
            </div>
        </div>
    </div>

    {{-- Transporte --}}
    <div class="col-12">
        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3"><i class="bi bi-truck me-1"></i> Transporte</h6>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="small text-muted fw-bold mb-1">Motorista</div>
                <div class="fw-semibold">{{ $discharge->motorista }}</div>
            </div>
            <div class="col-md-4">
                <div class="small text-muted fw-bold mb-1">Matrícula</div>
                <span class="badge bg-dark bg-opacity-75" style="font-size:.75rem;letter-spacing:1px;">{{ $discharge->matricula }}</span>
            </div>
            <div class="col-md-4">
                <div class="small text-muted fw-bold mb-1">Tipo de atrelado</div>
                @if($discharge->tipo_atrelado)
                    <span class="badge {{ $discharge->tipo_atrelado === 'link' ? 'bg-danger' : 'bg-primary' }}" style="font-size:.75rem;">
                        {{ strtoupper($discharge->tipo_atrelado) }}
                    </span>
                    @if($discharge->atreladoInconsistente())
                        <span style="display:inline-block;background:#fd7e14;color:#fff;border-radius:20px;font-size:.64rem;font-weight:700;padding:2px 9px;margin-left:4px;">
                            ⚠ não bate com os {{ $discharge->total_sacos }} sacos
                        </span>
                    @endif
                @else
                    <span class="text-muted">Não definido</span>
                @endif
            </div>
            <div class="col-md-4">
                <div class="small text-muted fw-bold mb-1">Transportadora</div>
                <div class="fw-semibold">{{ $discharge->transportadora }}</div>
                @if($discharge->operation?->isTransportadoraExtra($discharge->transportadora))
                    <span style="display:inline-flex;align-items:center;gap:4px;background:#fd7e14;color:#fff;border-radius:20px;
                                 font-size:.64rem;font-weight:700;padding:2px 9px;margin-top:4px;box-shadow:0 0 0 3px rgba(253,126,20,.22);">
                        <i class="bi bi-exclamation-triangle-fill"></i>Fora da lista da operação
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Carga no Porto --}}
    <div class="col-12">
        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3"><i class="bi bi-box-seam me-1"></i> Carga Registada no Porto</h6>
        <div class="row g-3">
            @foreach($discharge->items as $i => $it)
            @php
                $op    = $it->operationItem;
                $c     = $palette[$i % count($palette)];
                $kgUn  = $op->unidade === 'tonelada' ? 1000 : (float) $op->peso_unitario_kg;
            @endphp
            <div class="col-md-4">
                <div class="card border-0 h-100" style="background:{{ $c['bg'] }};border-left:4px solid {{ $c['bd'] }} !important;border-radius:8px;">
                    <div class="card-body py-3 px-4">
                        <div class="small fw-bold mb-2"><span class="badge" style="background:{{ $c['bd'] }};">{{ mb_strtoupper($op->nome) }}</span></div>
                        <div class="fw-bold" style="font-size:1.4rem;color:{{ $c['tx'] }};">{{ $fmt($it->quantidade) }} {{ $op->unidade_label }}</div>
                        @if($op->unidade === 'saco')
                            <div class="small text-muted">× {{ $fmt($kgUn) }} kg</div>
                        @endif
                        <div class="fw-bold mt-1" style="color:{{ $c['tx'] }};">= {{ number_format($it->quantidade * $kgUn, 0, ',', '.') }} kg</div>
                        @if($it->quantidade_confirmada !== null)
                            @php $dif = abs($it->quantidade_confirmada - $it->quantidade) > 0.0005; @endphp
                            <div class="small mt-2 {{ $dif ? 'text-danger fw-bold' : 'text-success' }}">
                                <i class="bi bi-speedometer2 me-1"></i>Balança: {{ $fmt($it->quantidade_confirmada) }} {{ $op->unidade_label }}
                                @if($dif) <i class="bi bi-exclamation-triangle-fill text-warning ms-1"></i> @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach

            <div class="col-md-4">
                <div class="card border-0 h-100" style="background:#f0fdf4;border-left:4px solid #198754 !important;border-radius:8px;">
                    <div class="card-body py-3 px-4">
                        <div class="small text-muted fw-bold mb-2">TOTAL ESTIMADO</div>
                        <div class="fw-bold" style="font-size:1.4rem;color:#198754;">{{ $discharge->total_sacos }} sacos</div>
                        <div class="fw-bold text-success mt-1" style="font-size:1.1rem;">
                            {{ number_format($discharge->peso_total_estimado, 0, ',', '.') }} kg
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Confirmação na Balança --}}
    @if($discharge->status === 'confirmed')
    <div class="col-12">
        <h6 class="fw-bold text-success border-bottom pb-2 mb-3"><i class="bi bi-speedometer2 me-1"></i> Confirmação na Balança</h6>
        <div class="row g-3">
            <div class="col-md-3">
                <div class="small text-muted fw-bold mb-1">Hora Chegada</div>
                <div class="fw-semibold">{{ $discharge->hora_chegada_balanca ?? '—' }}</div>
            </div>
            <div class="col-md-3">
                <div class="small text-muted fw-bold mb-1">Tempo de Transporte</div>
                <div class="fw-semibold">
                    @if($discharge->tempo_transporte)
                        <i class="bi bi-clock me-1 text-muted"></i>{{ $discharge->tempo_transporte }} min
                    @else —
                    @endif
                </div>
            </div>
            <div class="col-md-3">
                <div class="small text-muted fw-bold mb-1">Peso Bruto</div>
                <div class="fw-semibold">{{ number_format($discharge->peso_bruto, 0, ',', '.') }} kg</div>
            </div>
            <div class="col-md-3">
                <div class="small text-muted fw-bold mb-1">Tara</div>
                <div class="fw-semibold">{{ number_format($discharge->tara, 0, ',', '.') }} kg</div>
            </div>
            <div class="col-md-3">
                <div class="small text-muted fw-bold mb-1">Peso Líquido</div>
                <div class="fw-bold text-success" style="font-size:1.1rem;">{{ number_format($discharge->peso_liquido, 0, ',', '.') }} kg</div>
            </div>
            <div class="col-md-3">
                <div class="small text-muted fw-bold mb-1">Sacos Confirmados</div>
                <div class="fw-semibold {{ $hasDivergence ? 'text-danger' : 'text-success' }}">
                    {{ $discharge->sacos_confirmados }}
                    @if($hasDivergence)
                        <i class="bi bi-exclamation-triangle-fill ms-1 text-warning"></i>
                    @endif
                </div>
            </div>
            <div class="col-md-3">
                <div class="small text-muted fw-bold mb-1">Confirmado por</div>
                <div class="fw-semibold">{{ $discharge->confirmedBy->name ?? '—' }}</div>
            </div>
            @if($discharge->observacoes)
            <div class="col-12">
                <div class="small text-muted fw-bold mb-1">Observações</div>
                <div class="p-3 rounded" style="background:#f8f9fa;font-size:.85rem;">{{ $discharge->observacoes }}</div>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- Rodapé com botões --}}
<div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
    <button type="button" class="btn btn-light border px-4"
            onclick="bootstrap.Modal.getInstance(document.getElementById('modalPrincipal')).hide()">
        <i class="bi bi-x me-1"></i>Fechar
    </button>
    <div class="d-flex gap-2">
        @unless($arquivada)
        <button type="button" class="btn btn-warning px-4 fw-bold"
                onclick="abrirModal('{{ route('discharges.edit', $discharge->id) }}', 'Editar Descarga #{{ $discharge->id }}', 'warning')">
            <i class="bi bi-pencil me-1"></i>Editar
        </button>
        @endunless
        <button type="button" class="btn btn-outline-dark px-4" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>Imprimir
        </button>
    </div>
</div>
@php
    $fmt   = fn ($n) => rtrim(rtrim(number_format((float) $n, 3, ',', '.'), '0'), ',');
    $kg    = fn ($n) => $n === null ? '—' : number_format((float) $n, 0, ',', '.') . ' kg';
    $hora  = fn ($h) => $h ? substr((string) $h, 0, 5) : '—';
    $quando = fn ($dt) => $dt ? \Carbon\Carbon::parse($dt)->format('d/m H:i') : null;

    $estados = [
        'pending'    => ['Pendente', 'secondary'],
        'in_transit' => ['Em trânsito', 'info'],
        'weighing'   => ['A pesar', 'warning'],
        'confirmed'  => ['Confirmado', 'success'],
    ];
    [$estadoTxt, $estadoCor] = $estados[$discharge->status] ?? [$discharge->status, 'secondary'];

    $divergencia = $discharge->hasDivergence();
    $tempo = $discharge->tempo_transporte;
@endphp

<div style="font-size:.8rem;">

    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <div class="fw-bold" style="font-size:.95rem;">Guia {{ $discharge->numero_guia }}</div>
            <div class="text-muted" style="font-size:.72rem;">
                {{ $discharge->operation?->navio }} · {{ \Carbon\Carbon::parse($discharge->data)->format('d/m/Y') }}
            </div>
        </div>
        <div class="d-flex gap-1 flex-wrap justify-content-end">
            <span class="badge bg-{{ $estadoCor }}">{{ $estadoTxt }}</span>
            @if($divergencia)
                <span class="badge bg-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>Divergência</span>
            @endif
        </div>
    </div>

    <div class="row g-2 mb-3">
        <div class="col-6"><div class="text-muted" style="font-size:.68rem;">Motorista</div><b>{{ $discharge->motorista }}</b></div>
        <div class="col-6"><div class="text-muted" style="font-size:.68rem;">Matrícula</div><b>{{ $discharge->matricula }}</b></div>
        <div class="col-6"><div class="text-muted" style="font-size:.68rem;">Transportadora</div><b>{{ $discharge->transportadora }}</b></div>
        <div class="col-6"><div class="text-muted" style="font-size:.68rem;">Atrelado</div><b>{{ $discharge->tipo_atrelado === 'link' ? 'Link' : ($discharge->tipo_atrelado === 'normal' ? 'Normal' : '—') }}</b></div>
        <div class="col-6"><div class="text-muted" style="font-size:.68rem;">Saída do porto</div><b>{{ $hora($discharge->hora_saida_porto) }}</b></div>
        <div class="col-6"><div class="text-muted" style="font-size:.68rem;">Chegada à balança</div><b>{{ $hora($discharge->hora_chegada_balanca) }}</b></div>
        @if($tempo)
            <div class="col-6"><div class="text-muted" style="font-size:.68rem;">Tempo de transporte</div><b>{{ intdiv((int) $tempo, 60) }}h{{ str_pad((int) $tempo % 60, 2, '0', STR_PAD_LEFT) }}</b></div>
        @endif
    </div>

    <div class="fw-bold mb-1" style="font-size:.72rem;text-transform:uppercase;color:#64748b;">Produtos</div>
    <table class="table table-sm align-middle mb-3" style="font-size:.76rem;">
        <thead class="table-light">
            <tr><th>Produto</th><th class="text-end">Porto</th><th class="text-end">Balança</th></tr>
        </thead>
        <tbody>
        @foreach($discharge->items as $i)
            @php
                $conf = $i->quantidade_confirmada;
                $dif  = $conf !== null && abs((float) $conf - (float) $i->quantidade) > 0.0005;
                $un   = $i->operationItem->unidade_label ?? '';
            @endphp
            <tr>
                <td>{{ $i->operationItem->nome ?? '—' }}</td>
                <td class="text-end">{{ $fmt($i->quantidade) }} {{ $un }}</td>
                <td class="text-end {{ $dif ? 'text-danger fw-bold' : '' }}">
                    {{ $conf !== null ? $fmt($conf) . ' ' . $un : '—' }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="fw-bold mb-1" style="font-size:.72rem;text-transform:uppercase;color:#64748b;">Pesagem</div>
    <div class="row g-2 mb-3">
        <div class="col-4">
            <div class="text-muted" style="font-size:.68rem;">Cheio (bruto)</div>
            <b>{{ $kg($discharge->peso_bruto) }}</b>
            @if($quando($discharge->peso_bruto_em))<div class="text-muted" style="font-size:.65rem;">{{ $quando($discharge->peso_bruto_em) }}</div>@endif
        </div>
        <div class="col-4">
            <div class="text-muted" style="font-size:.68rem;">Vazio (tara)</div>
            <b>{{ $kg($discharge->tara) }}</b>
            @if($quando($discharge->tara_em))<div class="text-muted" style="font-size:.65rem;">{{ $quando($discharge->tara_em) }}</div>@endif
        </div>
        <div class="col-4">
            <div class="text-muted" style="font-size:.68rem;">Líquido</div>
            <b class="text-success">{{ $kg($discharge->peso_liquido) }}</b>
        </div>
    </div>

    @if($discharge->observacoes)
        <div class="mb-3 p-2 rounded" style="background:#f8fafc;font-size:.74rem;">
            <i class="bi bi-chat-left-text me-1"></i>{{ $discharge->observacoes }}
        </div>
    @endif

    @if($discharge->signature?->imagem)
        <div class="mb-3">
            <div class="text-muted" style="font-size:.68rem;">Assinatura do motorista</div>
            <img src="{{ $discharge->signature->imagem }}" alt="Assinatura"
                 style="max-width:220px;max-height:90px;border:1px solid #e9ecef;border-radius:6px;background:#fff;">
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center text-muted" style="font-size:.68rem;">
        <span>
            Registado por {{ $discharge->registeredBy?->name ?? '—' }}
            @if($discharge->confirmedBy) · Confirmado por {{ $discharge->confirmedBy->name }} @endif
        </span>
        <a href="{{ route('discharges.ticket', $discharge) }}" target="_blank" class="btn btn-outline-secondary btn-sm" style="font-size:.7rem;">
            <i class="bi bi-printer me-1"></i>Ticket
        </a>
    </div>
</div>

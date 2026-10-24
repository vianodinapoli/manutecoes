{{--
    Armazém da operação: stock por produto + histórico de saídas de carga.
    Incluir em operations/show.blade.php, por exemplo logo depois dos KPIs:

        @include('operations._saidas', ['operation' => $operation])
--}}
@php
    $fmtS   = fn ($n) => rtrim(rtrim(number_format((float) $n, 3, ',', '.'), '0'), ',');
    $stockS = \App\Models\SaidaCarga::stockOperacao($operation);
    $saidas = \App\Models\SaidaCarga::carregadas()
                ->where('operation_id', $operation->id)
                ->with('discharge')
                ->orderByDesc('carregado_em')
                ->get();
    $nomeS  = $operation->items->pluck('nome', 'id');
@endphp

<div class="op-head no-print" style="background:#fff;border:1px solid #e9ecef;border-radius:10px;padding:14px 18px;margin-bottom:14px;">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div style="font-size:.72rem;font-weight:700;letter-spacing:.9px;text-transform:uppercase;color:#64748b;">
            <i class="bi bi-box-seam me-1"></i>Armazém desta operação
        </div>
        @if(Route::has('saidas.index'))
            <a href="{{ route('saidas.index') }}" class="btn btn-sm btn-outline-success fw-bold" style="font-size:.7rem;border-radius:7px;">
                <i class="bi bi-box-arrow-up me-1"></i>Saída de carga
            </a>
        @endif
    </div>

    {{-- Stock por produto --}}
    <div class="table-responsive">
        <table class="table table-sm mb-3" style="font-size:.76rem;">
            <thead>
                <tr style="font-size:.58rem;text-transform:uppercase;letter-spacing:.8px;color:#868e96;">
                    <th>Produto</th>
                    <th class="text-end">Recebido</th>
                    <th class="text-end">Saiu</th>
                    <th class="text-end">Em armazém</th>
                </tr>
            </thead>
            <tbody>
            @foreach($stockS as $r)
                <tr>
                    <td class="fw-bold">{{ $r->item->nome }}</td>
                    <td class="text-end">{{ $fmtS($r->recebido) }} {{ $r->item->unidade_label }}</td>
                    <td class="text-end" style="color:#dc3545;">{{ $fmtS($r->saiu) }}</td>
                    <td class="text-end fw-bold" style="color:{{ $r->stock > 0 ? '#198754' : ($r->stock < 0 ? '#dc3545' : '#64748b') }};">
                        {{ $fmtS($r->stock) }} {{ $r->item->unidade_label }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{-- Histórico de saídas --}}
    <div style="font-size:.66rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">
        Histórico de saídas ({{ $saidas->count() }})
    </div>
    <div class="table-responsive">
        <table class="table table-sm mb-0" style="font-size:.74rem;">
            <thead>
                <tr style="font-size:.58rem;text-transform:uppercase;letter-spacing:.8px;color:#868e96;">
                    <th>Saída</th><th>Cliente</th><th>Matrícula</th><th>Atrelado</th>
                    <th>Produto</th><th class="text-end">Qtd</th><th>Descarga</th><th>Outros dados</th>
                </tr>
            </thead>
            <tbody>
            @forelse($saidas as $s)
                <tr>
                    <td>{{ $s->carregado_em?->format('d/m/Y H:i') }}</td>
                    <td>{{ $s->empresa }} · {{ $s->nome_completo }}</td>
                    <td>
                        @if($s->matricula)
                            <span style="display:inline-block;background:#1e293b;color:#fff;border-radius:3px;font-size:.58rem;letter-spacing:1px;padding:1px 6px;font-weight:600;">{{ $s->matricula }}</span>
                        @else — @endif
                    </td>
                    <td>{{ $s->tipo_atrelado ? strtoupper($s->tipo_atrelado) : '—' }}</td>
                    <td>{{ $nomeS[$s->operation_item_id] ?? '—' }}</td>
                    <td class="text-end fw-bold">{{ $fmtS($s->quantidade) }}</td>
                    <td>#{{ $s->discharge_id }} · Guia {{ $s->discharge->numero_guia ?? '—' }}</td>
                    <td>
                        @foreach(($s->metadata ?? []) as $k => $v)
                            <span style="display:inline-block;background:#f1f3f5;color:#475569;border-radius:4px;font-size:.6rem;padding:1px 6px;margin-right:2px;"><b>{{ $k }}:</b> {{ $v }}</span>
                        @endforeach
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-3">Ainda não saiu carga desta operação.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
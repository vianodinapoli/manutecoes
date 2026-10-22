<x-app-layout>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    .page-title{font-size:1.05rem;font-weight:700;color:#1e293b;margin:0}
    .page-sub{font-size:.72rem;color:#94a3b8;margin:2px 0 0}
    .box{background:#fff;border:1px solid #e9ecef;border-radius:10px;padding:16px 18px;margin-bottom:14px}
    .op-ref{font-size:.74rem;color:#94a3b8}
    .op-ref b{color:#475569;font-weight:600}
    .meta-chip{display:inline-block;background:#f1f3f5;color:#475569;border-radius:4px;
               font-size:.62rem;padding:1px 7px;margin:2px 3px 0 0}
    /* ── Stickers de transportadoras ── */
    .tp-label{font-size:.6rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#94a3b8;margin-right:6px}
    .tp-chip{display:inline-flex;align-items:center;gap:4px;background:#eef2ff;color:#3b4cca;border:1px solid #d5dcff;
             border-radius:20px;font-size:.64rem;font-weight:600;padding:2px 9px;margin:2px 3px 0 0;white-space:nowrap}
    .tp-chip.extra{background:#fd7e14;color:#fff;border-color:#fd7e14;box-shadow:0 0 0 3px rgba(253,126,20,.22)}
    #planTable th{font-size:.6rem;font-weight:700;letter-spacing:.9px;text-transform:uppercase;color:#868e96;
                  border-bottom:1px solid #e9ecef;padding:8px 10px}
    #planTable td{font-size:.8rem;padding:8px 10px;vertical-align:middle}
    #planTable tfoot td{font-weight:800;border-top:2px solid #e9ecef}
</style>

<div class="container-fluid py-3 px-4">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <a href="{{ route('operations.index') }}" class="text-decoration-none" style="font-size:.7rem;color:#94a3b8;">
                <i class="bi bi-arrow-left me-1"></i>Operações
            </a>
            <div class="page-title mt-1">
                <i class="bi bi-ship me-2" style="color:#0d6efd;"></i>{{ $operation->navio }}
                <span class="badge ms-2" style="background:#fff3cd;color:#856404;font-size:.6rem;vertical-align:middle;">PLANEADA</span>
            </div>
            <div class="page-sub">{{ $operation->empresa }} — defina o plano e inicie a descarga</div>
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary btn-sm fw-bold" style="border-radius:7px;font-size:.75rem;"
                    onclick='abrirOperacaoModal(@json($operation->modalPayload()))'>
                <i class="bi bi-pencil me-1"></i>Editar plano
            </button>

            @if(auth()->user()->hasRole('super-admin'))
            <form action="{{ route('operations.destroy', $operation) }}" method="POST" class="m-0">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger btn-sm fw-bold" style="border-radius:7px;font-size:.75rem;"
                        onclick="return confirm('Eliminar esta operação planeada?')">
                    <i class="bi bi-trash3 me-1"></i>Eliminar
                </button>
            </form>
            @endif

            <form action="{{ route('operations.start', $operation) }}" method="POST" class="m-0">
                @csrf @method('PATCH')
                <button class="btn btn-success btn-sm fw-bold shadow-sm" style="border-radius:7px;font-size:.75rem;"
                        onclick="return confirm('Iniciar a descarga? Depois de iniciada, o plano fica bloqueado.')">
                    <i class="bi bi-play-circle me-1"></i>Iniciar descarga
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success py-2" style="font-size:.78rem;border-radius:8px;">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger py-2" style="font-size:.78rem;border-radius:8px;">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif

    <div class="box">
        <div class="op-ref">
            BL: <b>{{ $operation->bl ?: '—' }}</b> &nbsp;·&nbsp;
            Factura: <b>{{ $operation->factura ?: '—' }}</b> &nbsp;·&nbsp;
            Empresa: <b>{{ $operation->empresa }}</b>
            @if($operation->data_chegada) &nbsp;·&nbsp; Chegada: <b>{{ $operation->data_chegada->format('d/m/Y') }}</b> @endif
        </div>
        @foreach(($operation->metadata ?? []) as $k => $v)
            <span class="meta-chip"><b>{{ $k }}:</b> {{ $v }}</span>
        @endforeach
        @if(!empty($operation->transportadoras))
        <div class="mt-2">
            <span class="tp-label">Transportadoras</span>
            @foreach($operation->transportadoras as $t)
                <span class="tp-chip {{ !empty($t['extra']) ? 'extra' : '' }}"
                      @if(!empty($t['extra'])) title="Fora da lista prevista (acrescentada ao lançar um camião)" @endif>
                    @if(!empty($t['extra']))<i class="bi bi-exclamation-triangle-fill"></i>@endif{{ $t['nome'] }}
                </span>
            @endforeach
        </div>
        @endif
        @if($operation->observacoes)
            <div class="mt-2" style="font-size:.74rem;color:#64748b;"><i class="bi bi-chat-left-text me-1"></i>{{ $operation->observacoes }}</div>
        @endif
    </div>

    <div class="box">
        <div class="fw-bold mb-2" style="font-size:.85rem;color:#1e293b;">
            <i class="bi bi-clipboard-check me-1" style="color:#1a56db;"></i>Plano da descarga
        </div>

        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0" id="planTable">
                <thead>
                    <tr>
                        <th>Produto / tipo</th>
                        <th>Unidade</th>
                        <th class="text-end">Peso por saco (kg)</th>
                        <th class="text-end">Quantidade esperada</th>
                        <th class="text-end">Peso esperado (t)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($operation->items as $it)
                    <tr>
                        <td class="fw-bold">{{ $it->nome }}</td>
                        <td>{{ $it->unidade === 'tonelada' ? 'Tonelada' : 'Saco' }}</td>
                        <td class="text-end">{{ $it->unidade === 'saco' ? rtrim(rtrim(number_format($it->peso_unitario_kg, 3, ',', '.'), '0'), ',') : '—' }}</td>
                        <td class="text-end">{{ rtrim(rtrim(number_format($it->quantidade, 3, ',', '.'), '0'), ',') }} {{ $it->unidade_label }}</td>
                        <td class="text-end">{{ number_format($it->peso_esperado_kg / 1000, 3, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end">Total esperado</td>
                        <td class="text-end" style="color:#1a56db;">{{ number_format((float) $operation->quantidade_esperada, 3, ',', '.') }} t</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-3" style="font-size:.7rem;color:#94a3b8;">
            <i class="bi bi-lock me-1"></i>Depois de iniciar a descarga, o plano fica bloqueado e só o super-admin o pode alterar (com motivo registado).
        </div>
    </div>
</div>

@include('operations._modal')

</x-app-layout>
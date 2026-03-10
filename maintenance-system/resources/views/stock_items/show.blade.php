<x-app-layout>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    .page-wrap{max-width:900px;margin:0 auto;padding:32px 24px 48px}
    .section{background:#fff;border:1px solid #e2e8f0;border-radius:12px;margin-bottom:16px;overflow:hidden}
    .section-head{display:flex;align-items:center;gap:10px;padding:12px 20px;background:#f8fafc;border-bottom:1px solid #e2e8f0}
    .section-head .s-num{font-size:.6rem;font-weight:800;letter-spacing:1.5px;color:#94a3b8;text-transform:uppercase;background:#e2e8f0;border-radius:4px;padding:2px 7px}
    .section-head .s-title{font-size:.8rem;font-weight:700;color:#334155;letter-spacing:.3px}
    .section-body{padding:20px 24px}
    .field-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px 24px}
    .field-grid.col-2{grid-template-columns:repeat(2,1fr)}
    .field-lbl{font-size:.6rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#94a3b8;margin-bottom:3px}
    .field-val{font-size:.83rem;font-weight:600;color:#1e293b}
    .top-btn{display:inline-flex;align-items:center;gap:6px;padding:7px 16px;border-radius:8px;font-size:.78rem;font-weight:600;text-decoration:none;border:1px solid #e2e8f0;background:#fff;color:#475569;transition:all .15s;cursor:pointer}
    .top-btn:hover{background:#f8fafc;border-color:#cbd5e1;color:#334155}
    .top-btn.warn{background:#fff;border-color:#fde68a;color:#92400e}
    .top-btn.warn:hover{background:#fefce8}
    .top-btn.danger{background:#fff;border-color:#fecaca;color:#dc2626}
    .top-btn.danger:hover{background:#fef2f2}
    .qty-badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:6px;font-size:.75rem;font-weight:800;border:1px solid}
    .qty-badge.ok {background:#f0fdf4;color:#16a34a;border-color:#bbf7d0}
    .qty-badge.low{background:#fef2f2;color:#dc2626;border-color:#fecaca}
    .estado-badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:6px;font-size:.7rem;font-weight:700;border:1px solid}
    .estado-badge.novo          {background:#f0f9ff;color:#0369a1;border-color:#bae6fd}
    .estado-badge.recondicionado{background:#fefce8;color:#92400e;border-color:#fde68a}
    .estado-badge.usado         {background:#f8fafc;color:#475569;border-color:#e2e8f0}
    .meta-chip{padding:10px 14px;border:1px solid #e2e8f0;border-radius:9px;background:#f8fafc}
    .meta-chip .meta-key{font-size:.58rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#94a3b8;margin-bottom:3px}
    .meta-chip .meta-val{font-size:.82rem;font-weight:600;color:#1e293b}
</style>

<div class="page-wrap">

    @php
        $qtyClass    = $stockItem->quantidade <= 5 ? 'low' : 'ok';
        $estadoClass = match($stockItem->estado ?? '') {
            'Novo'           => 'novo',
            'Recondicionado' => 'recondicionado',
            'Usado'          => 'usado',
            default          => 'usado',
        };
    @endphp

    {{-- CABEÇALHO --}}
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <div style="font-size:.6rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">
                Detalhe de Item de Stock
            </div>
            <h4 class="fw-bold mb-1" style="color:#1e293b;font-size:1.25rem;">
                {{ $stockItem->nome ?? $stockItem->referencia }}
                <span style="color:#cbd5e1;font-weight:300;margin:0 6px;">·</span>
                <span style="color:#475569;font-weight:500;font-size:1rem;">{{ $stockItem->referencia }}</span>
            </h4>
            <div style="font-size:.72rem;color:#94a3b8;">
                Criado em {{ $stockItem->created_at->format('d/m/Y H:i') }}
            </div>
        </div>
        <span class="qty-badge {{ $qtyClass }}" style="font-size:.85rem;padding:5px 14px;">
            {{ $stockItem->quantidade }} un.
        </span>
    </div>

    {{-- ACÇÕES --}}
    <div class="d-flex gap-2 mb-4">
        <a href="{{ route('stock-items.index') }}" class="top-btn">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
        <a href="{{ route('stock-items.edit', $stockItem->id) }}" class="top-btn warn">
            <i class="bi bi-pencil"></i> Editar
        </a>
        <form action="{{ route('stock-items.destroy', $stockItem->id) }}" method="POST"
              onsubmit="return confirm('Eliminar este item de stock? Esta acção é irreversível.')">
            @csrf @method('DELETE')
            <button type="submit" class="top-btn danger">
                <i class="bi bi-trash"></i> Eliminar
            </button>
        </form>
    </div>

    {{-- FLASH --}}
    @if(session('success'))
    <div class="d-flex align-items-center gap-2 mb-3 px-3 py-2 rounded"
         style="background:#f0fdf4;border:1px solid #bbf7d0;font-size:.8rem;color:#166534;">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
    @endif

    {{-- 01 — IDENTIFICAÇÃO --}}
    <div class="section">
        <div class="section-head">
            <span class="s-num">01</span>
            <span class="s-title">Identificação</span>
        </div>
        <div class="section-body">
            <div class="field-grid">
                <div>
                    <div class="field-lbl">Referência</div>
                    <div class="field-val" style="color:#1a56db;">{{ $stockItem->referencia }}</div>
                </div>
                <div>
                    <div class="field-lbl">Marca / Fabricante</div>
                    <div class="field-val">{{ $stockItem->marca_fabricante ?? '—' }}</div>
                </div>
                <div>
                    <div class="field-lbl">Modelo</div>
                    <div class="field-val">{{ $stockItem->modelo ?? '—' }}</div>
                </div>
                <div>
                    <div class="field-lbl">Categoria</div>
                    <div class="field-val">{{ $stockItem->categoria ?? '—' }}</div>
                </div>
                <div>
                    <div class="field-lbl">Sistema da Máquina</div>
                    <div class="field-val">{{ $stockItem->sistema_maquina ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- 02 — STOCK E LOCALIZAÇÃO --}}
    <div class="section">
        <div class="section-head">
            <span class="s-num">02</span>
            <span class="s-title">Controlo de Stock e Localização</span>
        </div>
        <div class="section-body">
            <div class="field-grid">
                <div>
                    <div class="field-lbl">Quantidade em Stock</div>
                    <span class="qty-badge {{ $qtyClass }} mt-1">
                        {{ $stockItem->quantidade }} un.
                    </span>
                </div>
                <div>
                    <div class="field-lbl">Estado</div>
                    <span class="estado-badge {{ $estadoClass }} mt-1">
                        {{ $stockItem->estado ?? '—' }}
                    </span>
                </div>
                <div>
                    <div class="field-lbl">Nº de Armazém</div>
                    <div class="field-val">{{ $stockItem->numero_armazem }}</div>
                </div>
                <div>
                    <div class="field-lbl">Secção</div>
                    <div class="field-val">{{ $stockItem->seccao_armazem ?? '—' }}</div>
                </div>
                <div>
                    <div class="field-lbl">Criado em</div>
                    <div class="field-val">{{ $stockItem->created_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- 03 — METADATA --}}
    <div class="section">
        <div class="section-head">
            <span class="s-num">03</span>
            <span class="s-title">Campos Personalizados</span>
            @if($stockItem->metadata && is_array($stockItem->metadata))
            <span style="margin-left:auto;font-size:.68rem;color:#94a3b8;font-weight:600;">
                {{ count($stockItem->metadata) }} campo(s)
            </span>
            @endif
        </div>
        <div class="section-body">
            @if($stockItem->metadata && is_array($stockItem->metadata) && count($stockItem->metadata) > 0)
            <div class="row g-2">
                @foreach($stockItem->metadata as $key => $value)
                <div class="col-md-3 col-sm-4 col-6">
                    <div class="meta-chip">
                        <div class="meta-key">{{ $key }}</div>
                        <div class="meta-val">{{ $value ?? '—' }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div style="text-align:center;color:#94a3b8;font-size:.8rem;padding:16px 0;">
                <i class="bi bi-inbox" style="font-size:1.2rem;display:block;margin-bottom:6px;opacity:.35;"></i>
                Nenhum campo personalizado adicionado.
            </div>
            @endif
        </div>
    </div>

    {{-- RODAPÉ --}}
    <div class="d-flex justify-content-between align-items-center mt-3"
         style="padding-top:16px;border-top:1px solid #f1f5f9;">
        <a href="{{ route('stock-items.index') }}" class="top-btn">
            <i class="bi bi-arrow-left"></i> Voltar à Lista
        </a>
        <span style="font-size:.65rem;color:#cbd5e1;">
            {{ $stockItem->referencia }} · {{ $stockItem->created_at->format('d/m/Y') }}
        </span>
    </div>

</div>
</x-app-layout>
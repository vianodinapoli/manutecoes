<x-app-layout>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    .page-wrap{padding:32px 24px 48px}

    /* ── KPI cards ── */
    .kpi-card{background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:16px 20px;display:flex;align-items:center;gap:14px;box-shadow:0 1px 4px rgba(0,0,0,.04)}
    .kpi-card.dark{border-left:4px solid #1e293b}
    .kpi-card.red {border-left:4px solid #dc2626}
    .kpi-icon{width:36px;height:36px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0}
    .kpi-icon.dark{background:#f1f5f9;color:#1e293b}
    .kpi-icon.red {background:#fef2f2;color:#dc2626}
    .kpi-label{font-size:.6rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#94a3b8;margin-bottom:2px}
    .kpi-value{font-size:1.4rem;font-weight:800;color:#1e293b;line-height:1}

    /* ── Search + botões ── */
    .search-input{font-size:.82rem;border:1px solid #e2e8f0;border-radius:8px;padding:7px 12px 7px 36px;color:#1e293b;background:#fff;width:100%;max-width:380px;transition:border-color .15s,box-shadow .15s}
    .search-input:focus{outline:none;border-color:#64748b;box-shadow:0 0 0 3px rgba(100,116,139,.1)}
    .search-wrap{position:relative;display:inline-block}
    .search-wrap i{position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:.8rem;pointer-events:none}

    .top-btn{display:inline-flex;align-items:center;gap:6px;padding:7px 16px;border-radius:8px;font-size:.78rem;font-weight:600;text-decoration:none;border:1px solid #e2e8f0;background:#fff;color:#475569;transition:all .15s;cursor:pointer}
    .top-btn:hover{background:#f8fafc;border-color:#cbd5e1;color:#334155}
    .top-btn.primary{background:#1e293b;border-color:#1e293b;color:#fff}
    .top-btn.primary:hover{background:#334155;color:#fff}
    .top-btn.success{background:#fff;border-color:#bbf7d0;color:#16a34a}
    .top-btn.success:hover{background:#f0fdf4}
    .top-btn.danger-outline{background:#fff;border-color:#fecaca;color:#dc2626}
    .top-btn.danger-outline:hover{background:#fef2f2}

    /* ── Article cards ── */
    .article-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;transition:border-color .2s,box-shadow .2s,transform .2s;height:100%;display:flex;flex-direction:column}
    .article-card:hover{border-color:#94a3b8;box-shadow:0 6px 20px rgba(0,0,0,.07);transform:translateY(-3px)}
    .article-head{padding:12px 14px;background:#f8fafc;border-bottom:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:flex-start;gap:8px}
    .article-name{font-size:.72rem;font-weight:800;letter-spacing:.3px;text-transform:uppercase;color:#334155;margin:0;line-height:1.3}
    .article-name-lbl{font-size:.55rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#94a3b8;margin-bottom:2px}
    .qty-pill{display:inline-flex;align-items:center;justify-content:center;min-width:26px;height:20px;border-radius:5px;font-size:.68rem;font-weight:800;flex-shrink:0;padding:0 6px}
    .qty-pill.ok {background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0}
    .qty-pill.low{background:#fef2f2;color:#dc2626;border:1px solid #fecaca}

    /* ── Item rows ── */
    .item-row{padding:10px 14px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;gap:8px}
    .item-row:last-child{border-bottom:none}
    .item-lbl{font-size:.58rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#94a3b8;margin-bottom:2px}
    .item-val{font-size:.78rem;font-weight:600;color:#334155;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:130px}
    .item-brand{font-size:.68rem;color:#94a3b8}

    /* ── Qty badge ── */
    .qty-badge{display:inline-flex;align-items:center;padding:2px 8px;border-radius:5px;font-size:.7rem;font-weight:700;white-space:nowrap}
    .qty-badge.ok {background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0}
    .qty-badge.low{background:#fef2f2;color:#dc2626;border:1px solid #fecaca}

    /* ── Action icons ── */
    .item-actions{display:flex;gap:6px;justify-content:flex-end;padding:4px 14px 10px;flex-shrink:0}
    .action-icon{width:26px;height:26px;border-radius:7px;border:1px solid #e2e8f0;background:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:.78rem;color:#475569;text-decoration:none;transition:all .15s;cursor:pointer}
    .action-icon:hover{background:#f8fafc;border-color:#94a3b8;color:#1e293b}
    .action-icon.danger:hover{background:#fef2f2;border-color:#fecaca;color:#dc2626}

    /* ── Empty state ── */
    .empty-state{text-align:center;padding:48px 24px;color:#94a3b8}
    .empty-state i{font-size:2rem;display:block;margin-bottom:10px;opacity:.3}
</style>

<div class="page-wrap">

    {{-- CABEÇALHO --}}
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <div style="font-size:.6rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">Armazém</div>
            <h4 class="fw-bold mb-0" style="color:#1e293b;font-size:1.25rem;">Mapa de Stock</h4>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <div class="search-wrap">
                <i class="bi bi-search"></i>
                <input type="text" id="globalSearch" class="search-input"
                       placeholder="Pesquisar artigo, referência, marca...">
            </div>
            <a href="{{ route('stock-items.export', ['type' => 'excel']) }}" class="top-btn success">
                <i class="bi bi-file-earmark-spreadsheet"></i> Excel
            </a>
            <a href="{{ route('stock-items.export', ['type' => 'pdf']) }}" class="top-btn danger-outline">
                <i class="bi bi-file-earmark-pdf"></i> PDF
            </a>
            <a href="{{ route('stock-items.create') }}" class="top-btn primary">
                <i class="bi bi-plus-lg"></i> Novo Artigo
            </a>
        </div>
    </div>

    {{-- KPI CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="kpi-card dark">
                <div class="kpi-icon dark"><i class="bi bi-box-seam"></i></div>
                <div>
                    <div class="kpi-label">Total Referências</div>
                    <div class="kpi-value">{{ $stockItems->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card red">
                <div class="kpi-icon red"><i class="bi bi-exclamation-triangle"></i></div>
                <div>
                    <div class="kpi-label">Stock Crítico (≤ 5)</div>
                    <div class="kpi-value">{{ $stockItems->where('quantidade', '<=', 5)->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- GRID DE ARTIGOS --}}
    @php
        $grouped = $stockItems->groupBy(fn($item) => $item->nome ?: 'Sem Nome');
    @endphp

    @if($grouped->isEmpty())
    <div class="empty-state">
        <i class="bi bi-inbox"></i>
        <div style="font-size:.85rem;">Ainda não há artigos registados no stock.</div>
        <a href="{{ route('stock-items.create') }}" class="top-btn primary mt-3 mx-auto">
            <i class="bi bi-plus-lg"></i> Adicionar primeiro artigo
        </a>
    </div>
    @else
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3" id="stockGrid">
        @foreach($grouped as $nomeArtigo => $itens)
        @php
            $totalQty   = $itens->sum('quantidade');
            $searchData = strtolower($nomeArtigo.' '.$itens->pluck('referencia')->implode(' ').' '.$itens->pluck('marca_fabricante')->implode(' '));
        @endphp
        <div class="col article-card-wrapper" data-search="{{ $searchData }}">
            <div class="article-card">

                {{-- Cabeçalho do artigo --}}
                <div class="article-head">
                    <div style="flex:1;overflow:hidden;">
                        <div class="article-name-lbl">Artigo / Peça</div>
                        <div class="article-name text-truncate" title="{{ $nomeArtigo }}">{{ $nomeArtigo }}</div>
                    </div>
                    <span class="qty-pill {{ $totalQty <= 5 ? 'low' : 'ok' }}">{{ $totalQty }}</span>
                </div>

                {{-- Variações --}}
                <div style="flex:1;">
                    @foreach($itens as $item)
                    <div class="item-row">
                        <div style="overflow:hidden;flex:1;">
                            <div class="item-lbl">Ref / Marca</div>
                            <div class="item-val" title="{{ $item->referencia }}">{{ $item->referencia }}</div>
                            <div class="item-brand">{{ $item->marca_fabricante }}</div>
                        </div>
                        <div class="text-end" style="flex-shrink:0;">
                            <div class="item-lbl">Qtd</div>
                            <span class="qty-badge {{ $item->quantidade <= 5 ? 'low' : 'ok' }}">
                                {{ $item->quantidade }}
                            </span>
                        </div>
                    </div>
                    <div class="item-actions">
                        <a href="{{ route('stock-items.edit', $item->id) }}" class="action-icon" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <a href="{{ route('stock-items.show', $item->id) }}" class="action-icon" title="Ver">
                            <i class="bi bi-eye"></i>
                        </a>
                        @can('gerir utilizadores')
                        <button type="button"
                                class="action-icon danger btn-delete"
                                title="Eliminar"
                                data-action="{{ route('stock-items.destroy', $item->id) }}"
                                data-label="{{ $item->referencia }}">
                            <i class="bi bi-trash"></i>
                        </button>
                        @endcan
                    </div>
                    @endforeach
                </div>

            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>

{{-- MODAL CONFIRMAÇÃO ELIMINAR --}}
<div class="confirm-overlay" id="confirmOverlay">
    <div class="confirm-box">
        <div class="confirm-icon"><i class="bi bi-trash3-fill"></i></div>
        <div class="confirm-title">Eliminar variação?</div>
        <div class="confirm-msg">Esta acção é irreversível. A referência será removida permanentemente do stock.</div>
        <div class="confirm-id" id="confirmLabel">—</div>
        <div class="confirm-actions">
            <button class="confirm-cancel" onclick="closeConfirm()">
                <i class="bi bi-x"></i> Cancelar
            </button>
            <button class="confirm-ok" id="confirmOkBtn">
                <i class="bi bi-trash3"></i> Eliminar
            </button>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<style>
    .confirm-overlay{position:fixed;inset:0;background:rgba(15,23,42,.45);z-index:9999;display:flex;align-items:center;justify-content:center;opacity:0;pointer-events:none;transition:opacity .2s}
    .confirm-overlay.open{opacity:1;pointer-events:all}
    .confirm-box{background:#fff;border-radius:14px;padding:28px 28px 22px;max-width:380px;width:calc(100% - 32px);box-shadow:0 20px 60px rgba(0,0,0,.18);transform:translateY(8px) scale(.98);transition:transform .2s;border-top:4px solid #dc2626}
    .confirm-overlay.open .confirm-box{transform:none}
    .confirm-icon{width:44px;height:44px;background:#fef2f2;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:#dc2626;margin-bottom:14px}
    .confirm-title{font-size:.95rem;font-weight:700;color:#1e293b;margin-bottom:6px}
    .confirm-msg{font-size:.8rem;color:#64748b;line-height:1.5;margin-bottom:12px}
    .confirm-id{font-size:.78rem;font-weight:700;color:#1e293b;background:#f1f5f9;border:1px solid #e2e8f0;border-radius:6px;padding:4px 10px;display:inline-block;margin-bottom:18px}
    .confirm-actions{display:flex;gap:8px;justify-content:flex-end}
    .confirm-cancel{padding:7px 18px;border-radius:8px;font-size:.78rem;font-weight:600;border:1px solid #e2e8f0;background:#fff;color:#475569;cursor:pointer;transition:all .15s}
    .confirm-cancel:hover{background:#f8fafc;border-color:#cbd5e1}
    .confirm-ok{padding:7px 18px;border-radius:8px;font-size:.78rem;font-weight:600;border:none;background:#dc2626;color:#fff;cursor:pointer;transition:all .15s}
    .confirm-ok:hover{background:#b91c1c}
</style>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function () {
    $("#globalSearch").on("keyup", function () {
        var v = $(this).val().toLowerCase();
        $(".article-card-wrapper").each(function () {
            $(this).toggle($(this).data("search").indexOf(v) > -1);
        });
    });

    // Modal de eliminação
    $(document).on('click', '.btn-delete', function () {
        var action = $(this).data('action');
        var label  = $(this).data('label');
        $('#confirmLabel').text(label);
        $('#deleteForm').attr('action', action);
        $('#confirmOverlay').addClass('open');
    });

    $('#confirmOkBtn').on('click', function () {
        $('#deleteForm').submit();
    });

    $('#confirmOverlay').on('click', function (e) {
        if (e.target === this) closeConfirm();
    });

    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') closeConfirm();
    });
});

function closeConfirm() {
    $('#confirmOverlay').removeClass('open');
}
</script>
</x-app-layout>
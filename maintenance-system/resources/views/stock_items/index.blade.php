<x-app-layout>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    .page-wrap { padding: 32px 24px 48px; }

    /* ── KPI cards ── */
    .kpi-card { background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 16px 20px; display: flex; align-items: center; gap: 14px; box-shadow: 0 1px 4px rgba(0,0,0,.04); }
    .kpi-card.accent-dark   { border-left: 4px solid #1e293b; }
    .kpi-card.accent-blue   { border-left: 4px solid #3b82f6; }
    .kpi-card.accent-amber  { border-left: 4px solid #f59e0b; }
    .kpi-card.accent-red    { border-left: 4px solid #dc2626; }
    .kpi-icon { width: 36px; height: 36px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
    .kpi-icon.dark   { background: #f1f5f9; color: #1e293b; }
    .kpi-icon.blue   { background: #eff6ff; color: #3b82f6; }
    .kpi-icon.amber  { background: #fffbeb; color: #d97706; }
    .kpi-icon.red    { background: #fef2f2; color: #dc2626; }
    .kpi-label { font-size: .6rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px; }
    .kpi-value { font-size: 1.4rem; font-weight: 800; color: #1e293b; line-height: 1; }
    .kpi-hint  { font-size: .65rem; color: #94a3b8; margin-top: 3px; }

    /* ── Toolbar ── */
    .search-input { font-size: .82rem; border: 1px solid #e2e8f0; border-radius: 8px; padding: 7px 12px 7px 36px; color: #1e293b; background: #fff; width: 100%; transition: border-color .15s, box-shadow .15s; }
    .search-input:focus { outline: none; border-color: #64748b; box-shadow: 0 0 0 3px rgba(100,116,139,.1); }
    .search-wrap { position: relative; flex: 1; min-width: 180px; max-width: 340px; }
    .search-wrap i { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: .8rem; pointer-events: none; }
    .filter-select { font-size: .78rem; border: 1px solid #e2e8f0; border-radius: 8px; padding: 7px 28px 7px 10px; color: #475569; background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2394a3b8'/%3E%3C/svg%3E") no-repeat right 10px center; appearance: none; cursor: pointer; transition: border-color .15s; }
    .filter-select:focus { outline: none; border-color: #94a3b8; }
    .top-btn { display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 8px; font-size: .78rem; font-weight: 600; text-decoration: none; border: 1px solid #e2e8f0; background: #fff; color: #475569; transition: all .15s; cursor: pointer; white-space: nowrap; }
    .top-btn:hover { background: #f8fafc; border-color: #cbd5e1; color: #334155; }
    .top-btn.primary { background: #1e293b; border-color: #1e293b; color: #fff; }
    .top-btn.primary:hover { background: #334155; color: #fff; }
    .top-btn.success { background: #fff; border-color: #bbf7d0; color: #16a34a; }
    .top-btn.success:hover { background: #f0fdf4; }
    .top-btn.danger-outline { background: #fff; border-color: #fecaca; color: #dc2626; }
    .top-btn.danger-outline:hover { background: #fef2f2; }
    .filter-chip { display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px; border-radius: 20px; font-size: .72rem; font-weight: 600; border: 1px solid #e2e8f0; background: #fff; color: #64748b; cursor: pointer; transition: all .15s; white-space: nowrap; }
    .filter-chip:hover { border-color: #94a3b8; }
    .filter-chip.active { background: #fef2f2; border-color: #fca5a5; color: #dc2626; }

    /* ── Category accordion ── */
    .cat-section { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.04); }
    .cat-header { display: flex; align-items: center; gap: 10px; padding: 13px 16px; cursor: pointer; background: #fff; border-bottom: 1px solid #f1f5f9; transition: background .1s; user-select: none; }
    .cat-header:hover { background: #fafafa; }
    .cat-emoji { width: 34px; height: 34px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
    .cat-title { font-size: .8rem; font-weight: 700; color: #1e293b; flex: 1; letter-spacing: .2px; }
    .cat-count { font-size: .68rem; background: #f1f5f9; color: #64748b; padding: 2px 9px; border-radius: 20px; font-weight: 600; }
    .cat-critical-badge { font-size: .65rem; background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; padding: 2px 8px; border-radius: 20px; font-weight: 700; }
    .cat-toggle-icon { font-size: .65rem; color: #94a3b8; transition: transform .2s; }
    .cat-toggle-icon.collapsed { transform: rotate(-90deg); }
    .cat-body { display: grid; grid-template-columns: repeat(3, 1fr); }

    /* ── Item card ── */
    .item-card { padding: 13px 15px; border-bottom: 1px solid #f8fafc; border-right: 1px solid #f8fafc; transition: background .1s; }
    .item-card:hover { background: #fafafa; }
    .item-card:nth-child(3n) { border-right: none; }
    .item-ref-label { font-size: .55rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #94a3b8; margin-bottom: 3px; }
    .item-name-text { font-size: .8rem; font-weight: 700; color: #1e293b; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .item-ref-val { font-size: .68rem; color: #64748b; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .item-brand-val { font-size: .65rem; color: #94a3b8; }
    .item-footer { display: flex; align-items: center; justify-content: space-between; margin-top: 9px; }
    .stock-badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 9px; border-radius: 20px; font-size: .67rem; font-weight: 700; white-space: nowrap; }
    .stock-badge.critico { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .stock-badge.baixo   { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .stock-badge.normal  { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
    .item-qty-num { font-size: .85rem; font-weight: 800; color: #1e293b; }
    .item-actions-row { display: flex; gap: 5px; opacity: 0; transition: opacity .15s; }
    .item-card:hover .item-actions-row { opacity: 1; }
    .act-btn { width: 26px; height: 26px; border-radius: 6px; border: 1px solid #e2e8f0; background: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: .72rem; color: #64748b; text-decoration: none; cursor: pointer; transition: all .15s; }
    .act-btn:hover { background: #f8fafc; border-color: #94a3b8; color: #1e293b; }
    .act-btn.danger:hover { background: #fef2f2; border-color: #fecaca; color: #dc2626; }

    /* ── Legend ── */
    .legend-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; display: inline-block; }

    /* ── Empty state ── */
    .empty-state { text-align: center; padding: 48px 24px; color: #94a3b8; }
    .empty-state i { font-size: 2rem; display: block; margin-bottom: 10px; opacity: .3; }

    /* ── Delete confirm modal ── */
    .confirm-overlay { position: fixed; inset: 0; background: rgba(15,23,42,.45); z-index: 9999; display: flex; align-items: center; justify-content: center; opacity: 0; pointer-events: none; transition: opacity .2s; }
    .confirm-overlay.open { opacity: 1; pointer-events: all; }
    .confirm-box { background: #fff; border-radius: 14px; padding: 28px 28px 22px; max-width: 380px; width: calc(100% - 32px); box-shadow: 0 20px 60px rgba(0,0,0,.18); transform: translateY(8px) scale(.98); transition: transform .2s; border-top: 4px solid #dc2626; }
    .confirm-overlay.open .confirm-box { transform: none; }
    .confirm-icon { width: 44px; height: 44px; background: #fef2f2; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: #dc2626; margin-bottom: 14px; }
    .confirm-title { font-size: .95rem; font-weight: 700; color: #1e293b; margin-bottom: 6px; }
    .confirm-msg { font-size: .8rem; color: #64748b; line-height: 1.5; margin-bottom: 12px; }
    .confirm-id { font-size: .78rem; font-weight: 700; color: #1e293b; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 6px; padding: 4px 10px; display: inline-block; margin-bottom: 18px; }
    .confirm-actions { display: flex; gap: 8px; justify-content: flex-end; }
    .confirm-cancel { padding: 7px 18px; border-radius: 8px; font-size: .78rem; font-weight: 600; border: 1px solid #e2e8f0; background: #fff; color: #475569; cursor: pointer; transition: all .15s; }
    .confirm-cancel:hover { background: #f8fafc; border-color: #cbd5e1; }
    .confirm-ok { padding: 7px 18px; border-radius: 8px; font-size: .78rem; font-weight: 600; border: none; background: #dc2626; color: #fff; cursor: pointer; transition: all .15s; }
    .confirm-ok:hover { background: #b91c1c; }

    /* ── Responsive ── */
    @media (max-width: 768px) {
        .cat-body { grid-template-columns: repeat(2, 1fr); }
        .item-card:nth-child(3n) { border-right: 1px solid #f8fafc; }
        .item-card:nth-child(2n) { border-right: none; }
    }
    @media (max-width: 480px) {
        .cat-body { grid-template-columns: 1fr; }
        .item-card { border-right: none !important; }
    }
</style>

<div class="page-wrap">

    {{-- ══ CABEÇALHO ══ --}}
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
        <div>
            <div style="font-size:.6rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">Armazém · Beira</div>
            <h4 class="fw-bold mb-0" style="color:#1e293b;font-size:1.25rem;">Mapa de Stock</h4>
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
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

   @php
    $totalRefs    = $stockItems->count();
    $totalCritico = $stockItems->where('quantidade', '<=', 5)->count();
    $totalBaixo   = $stockItems->whereBetween('quantidade', [6, 15])->count();
    $totalNormal  = $stockItems->where('quantidade', '>', 15)->count();

    // Agrupamento por categoria. Ajuste o campo conforme o seu modelo:
    // $item->artigo | $item->categoria | ou outro campo de agrupamento.
    $grouped    = $stockItems->groupBy(fn($i) => $i->artigo ?? $i->categoria ?? 'Geral');
    $categorias = $grouped->keys();
@endphp

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="kpi-card accent-dark">
                <div class="kpi-icon dark"><i class="bi bi-box-seam"></i></div>
                <div>
                    <div class="kpi-label">Total Referências</div>
                    <div class="kpi-value">{{ $totalRefs }}</div>
                    <div class="kpi-hint">{{ $categorias->count() }} categoria{{ $categorias->count() != 1 ? 's' : '' }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card accent-blue">
                <div class="kpi-icon blue"><i class="bi bi-check-circle"></i></div>
                <div>
                    <div class="kpi-label">Stock Normal</div>
                    <div class="kpi-value">{{ $totalNormal }}</div>
                    <div class="kpi-hint">Qtd &gt; 15</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card accent-amber">
                <div class="kpi-icon amber"><i class="bi bi-exclamation-circle"></i></div>
                <div>
                    <div class="kpi-label">Stock Baixo</div>
                    <div class="kpi-value">{{ $totalBaixo }}</div>
                    <div class="kpi-hint">Qtd 6 – 15</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card accent-red">
                <div class="kpi-icon red"><i class="bi bi-exclamation-triangle"></i></div>
                <div>
                    <div class="kpi-label">Stock Crítico</div>
                    <div class="kpi-value">{{ $totalCritico }}</div>
                    <div class="kpi-hint">Qtd ≤ 5</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ BARRA DE FILTROS ══ --}}
    <div class="d-flex gap-2 align-items-center flex-wrap mb-4 p-3"
         style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;">
        <div class="search-wrap">
            <i class="bi bi-search"></i>
            <input type="text" id="globalSearch" class="search-input"
                   placeholder="Pesquisar artigo, referência, marca...">
        </div>

        <select id="filterCategoria" class="filter-select">
            <option value="">Todas as categorias</option>
            @foreach($categorias as $cat)
                <option value="{{ $cat }}">{{ $cat }}</option>
            @endforeach
        </select>

        <select id="filterStatus" class="filter-select">
            <option value="">Todos os estados</option>
            <option value="critico">Crítico (≤ 5)</option>
            <option value="baixo">Baixo (6–15)</option>
            <option value="normal">Normal (&gt; 15)</option>
        </select>

        <button class="filter-chip" id="chipCritico" onclick="toggleCritico()">
            <i class="bi bi-circle-fill" style="font-size:.45rem;color:#dc2626;"></i>
            Só críticos
        </button>

        <div class="d-flex gap-3 align-items-center ms-auto flex-wrap">
            <span style="display:flex;align-items:center;gap:5px;font-size:.7rem;color:#64748b;">
                <span class="legend-dot" style="background:#16a34a;"></span> Normal
            </span>
            <span style="display:flex;align-items:center;gap:5px;font-size:.7rem;color:#64748b;">
                <span class="legend-dot" style="background:#d97706;"></span> Baixo
            </span>
            <span style="display:flex;align-items:center;gap:5px;font-size:.7rem;color:#64748b;">
                <span class="legend-dot" style="background:#dc2626;"></span> Crítico
            </span>
        </div>
    </div>

    {{-- ══ CATEGORIAS + ITENS ══ --}}
    @if($grouped->isEmpty())
    <div class="empty-state">
        <i class="bi bi-inbox"></i>
        <div style="font-size:.85rem;">Ainda não há artigos registados no stock.</div>
        <a href="{{ route('stock-items.create') }}" class="top-btn primary mt-3 mx-auto">
            <i class="bi bi-plus-lg"></i> Adicionar primeiro artigo
        </a>
    </div>
    @else
    <div id="stockGrid" class="d-flex flex-column gap-3">

        @foreach($grouped as $categoria => $itens)
        @php
            $catCritico = $itens->where('quantidade', '<=', 5)->count();
            $catTotal   = $itens->count();
            $searchStr  = strtolower(
                $categoria . ' ' .
                $itens->pluck('nome')->implode(' ') . ' ' .
                $itens->pluck('referencia')->implode(' ') . ' ' .
                $itens->pluck('marca_fabricante')->implode(' ')
            );
            $nomeLower = strtolower($categoria);
            $catIcon = match(true) {
                str_contains($nomeLower, 'filtro')    => '🔵',
                str_contains($nomeLower, 'lubri')     => '🛢',
                str_contains($nomeLower, 'viatura')   => '🚗',
                str_contains($nomeLower, 'oficina')   => '🔧',
                str_contains($nomeLower, 'eléctric')  => '⚡',
                str_contains($nomeLower, 'electr')    => '⚡',
                str_contains($nomeLower, 'peça')      => '🔩',
                str_contains($nomeLower, 'combustív') => '⛽',
                str_contains($nomeLower, 'material')  => '📦',
                default => '📦',
            };
            $catBg = match(true) {
                str_contains($nomeLower, 'filtro')    => '#eff6ff',
                str_contains($nomeLower, 'lubri')     => '#f0fdf4',
                str_contains($nomeLower, 'viatura')   => '#fef9ee',
                str_contains($nomeLower, 'eléctric'),
                str_contains($nomeLower, 'electr')    => '#fefce8',
                str_contains($nomeLower, 'peça')      => '#fef2f2',
                default => '#f8fafc',
            };
        @endphp

        <div class="cat-section" data-search="{{ $searchStr }}" data-categoria="{{ $categoria }}">

            <div class="cat-header" onclick="toggleCat(this)">
                <div class="cat-emoji" style="background:{{ $catBg }};">{{ $catIcon }}</div>
                <div class="cat-title">{{ $categoria }}</div>
                @if($catCritico > 0)
                    <span class="cat-critical-badge">
                        <i class="bi bi-exclamation-triangle-fill me-1" style="font-size:.55rem;"></i>
                        {{ $catCritico }} crítico{{ $catCritico > 1 ? 's' : '' }}
                    </span>
                @endif
                <span class="cat-count">{{ $catTotal }} {{ $catTotal == 1 ? 'item' : 'itens' }}</span>
                <i class="bi bi-chevron-down cat-toggle-icon"></i>
            </div>

            <div class="cat-body">
                @foreach($itens as $item)
                @php
                    $qty = (int) ($item->quantidade ?? 0);
                    if ($qty <= 5)       { $stockClass = 'critico'; $stockLabel = 'Crítico'; $stockIcon = 'bi-exclamation-triangle-fill'; }
                    elseif ($qty <= 15)  { $stockClass = 'baixo';   $stockLabel = 'Baixo';   $stockIcon = 'bi-dash-circle-fill'; }
                    else                 { $stockClass = 'normal';  $stockLabel = 'Normal';  $stockIcon = 'bi-check-circle-fill'; }
                @endphp
                <div class="item-card" data-qty="{{ $qty }}" data-status="{{ $stockClass }}">
                    <div class="item-ref-label">Ref / Marca</div>
                    <div class="item-name-text" title="{{ $item->nome }}">{{ $item->nome ?? '—' }}</div>
                    <div class="item-ref-val"   title="{{ $item->referencia }}">{{ $item->referencia ?? '—' }}</div>
                    <div class="item-brand-val">{{ $item->marca_fabricante ?? '' }}</div>

                    <div class="item-footer">
                        <div class="d-flex align-items-center gap-2">
                            <span class="stock-badge {{ $stockClass }}">
                                <i class="bi {{ $stockIcon }}" style="font-size:.55rem;"></i>
                                {{ $stockLabel }}
                            </span>
                            <span class="item-qty-num">
                                {{ $qty }}<span style="font-size:.6rem;font-weight:500;color:#94a3b8;margin-left:2px;">un</span>
                            </span>
                        </div>
                        <div class="item-actions-row">
                            <a href="{{ route('stock-items.edit', $item->id) }}" class="act-btn" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="{{ route('stock-items.show', $item->id) }}" class="act-btn" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            @can('gerir utilizadores')
                            <button type="button" class="act-btn danger btn-delete" title="Eliminar"
                                    data-action="{{ route('stock-items.destroy', $item->id) }}"
                                    data-label="{{ $item->referencia }}">
                                <i class="bi bi-trash"></i>
                            </button>
                            @endcan
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

        </div>
        @endforeach

    </div>
    @endif

</div>

{{-- ══ MODAL CONFIRMAÇÃO ELIMINAR ══ --}}
<div class="confirm-overlay" id="confirmOverlay">
    <div class="confirm-box">
        <div class="confirm-icon"><i class="bi bi-trash3-fill"></i></div>
        <div class="confirm-title">Eliminar referência?</div>
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

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
var criticoOnly = false;

function toggleCat(header) {
    var body = header.nextElementSibling;
    var icon = header.querySelector('.cat-toggle-icon');
    var isOpen = body.style.display !== 'none';
    body.style.display = isOpen ? 'none' : 'grid';
    icon.classList.toggle('collapsed', isOpen);
}

function toggleCritico() {
    criticoOnly = !criticoOnly;
    $('#chipCritico').toggleClass('active', criticoOnly);
    applyFilters();
}

function applyFilters() {
    var search    = $('#globalSearch').val().toLowerCase().trim();
    var categoria = $('#filterCategoria').val();
    var status    = $('#filterStatus').val();

    $('.cat-section').each(function () {
        var $section   = $(this);
        var catName    = $section.data('categoria');
        var catSearch  = ($section.data('search') || '').toString();

        var matchCat    = !categoria || catName === categoria;
        var matchSearch = !search || catSearch.indexOf(search) > -1;

        if (!matchCat || !matchSearch) {
            $section.hide();
            return;
        }

        var hasVisible = false;
        $section.find('.item-card').each(function () {
            var itemStatus = $(this).data('status');
            var show = (!status || itemStatus === status) && (!criticoOnly || itemStatus === 'critico');
            $(this).toggle(show);
            if (show) hasVisible = true;
        });

        $section.toggle(hasVisible);
    });
}

$(document).ready(function () {
    $('#globalSearch').on('keyup', applyFilters);
    $('#filterCategoria').on('change', applyFilters);
    $('#filterStatus').on('change', applyFilters);

    $(document).on('click', '.btn-delete', function () {
        $('#confirmLabel').text($(this).data('label'));
        $('#deleteForm').attr('action', $(this).data('action'));
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
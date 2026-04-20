<x-app-layout>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    /* ── Layout ── */
    .page-wrap { padding: 32px 24px 48px; }

    .page-eyebrow { font-size: .6rem; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: #94a3b8; margin-bottom: 4px; }
    .page-title   { font-size: 1.2rem; font-weight: 700; color: #1e293b; }

    /* ── Toolbar ── */
    .toolbar       { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 20px; }
    .toolbar-right { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
    .search-wrap   { position: relative; display: inline-block; }
    .search-wrap i { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: .8rem; pointer-events: none; }
    .search-input  { font-size: .8rem; border: 1px solid #e2e8f0; border-radius: 8px; padding: 7px 12px 7px 32px; color: #1e293b; background: #fff; width: 280px; transition: border-color .15s, box-shadow .15s; }
    .search-input:focus { outline: none; border-color: #64748b; box-shadow: 0 0 0 3px rgba(100,116,139,.1); }

    .top-btn { display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 8px; font-size: .76rem; font-weight: 600; text-decoration: none; border: 1px solid #e2e8f0; background: #fff; color: #475569; transition: all .15s; cursor: pointer; white-space: nowrap; }
    .top-btn:hover { background: #f8fafc; border-color: #cbd5e1; color: #334155; }
    .top-btn.primary { background: #1e293b; border-color: #1e293b; color: #fff; }
    .top-btn.primary:hover { background: #334155; color: #fff; }
    .top-btn.success { border-color: #bbf7d0; color: #16a34a; }
    .top-btn.success:hover { background: #f0fdf4; }
    .top-btn.danger  { border-color: #fecaca; color: #dc2626; }
    .top-btn.danger:hover  { background: #fef2f2; }

    /* ── KPI cards ── */
    .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-bottom: 24px; }
    .kpi-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 13px 16px; display: flex; align-items: center; gap: 12px; }
    .kpi-card.kpi-dark { border-left: 3px solid #1e293b; }
    .kpi-card.kpi-red  { border-left: 3px solid #dc2626; }
    .kpi-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: .9rem; flex-shrink: 0; }
    .kpi-icon.dark { background: #f1f5f9; color: #1e293b; }
    .kpi-icon.red  { background: #fef2f2; color: #dc2626; }
    .kpi-label { font-size: .58rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px; }
    .kpi-value { font-size: 1.3rem; font-weight: 800; color: #1e293b; line-height: 1; }

    /* ── Tabela ── */
    .table-wrap  { border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; }
    .stock-table { width: 100%; border-collapse: collapse; font-size: .8rem; }

    .stock-table thead tr { background: #f8fafc; }
    .stock-table thead th {
        padding: 9px 12px;
        text-align: left;
        font-size: .58rem;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: #94a3b8;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }
    .stock-table th.tc, .stock-table td.tc { text-align: center; }

    /* ── Linha de grupo (artigo) ── */
    .group-row { cursor: pointer; background: #f8fafc; user-select: none; }
    .group-row:hover { background: #f1f5f9; }
    .group-row td { padding: 10px 12px; border-bottom: 1px solid #e2e8f0; color: #1e293b; font-weight: 600; font-size: .8rem; }

    .group-name { display: flex; align-items: center; gap: 8px; }

    /* Chevron */
    .chevron-wrap { width: 20px; height: 20px; border-radius: 5px; border: 1px solid #e2e8f0; background: #fff; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; transition: background .15s; }
    .chevron-wrap i { font-size: .7rem; color: #64748b; transition: transform .2s; }
    .group-row.open .chevron-wrap i { transform: rotate(90deg); }

    .ref-count { font-size: .65rem; font-weight: 600; color: #64748b; background: #fff; border: 1px solid #e2e8f0; border-radius: 4px; padding: 1px 7px; }
    .brands-preview { font-size: .72rem; font-weight: 400; color: #94a3b8; }

    /* ── Linhas de variacao ── */
    .var-row { display: none; }
    .var-row.visible { display: table-row; }
    .var-row td { padding: 8px 12px; border-bottom: 1px solid #f1f5f9; color: #334155; background: #fff; }
    .var-row.last-of-group td { border-bottom: 1px solid #e2e8f0; }

    .td-ref  { font-weight: 600; color: #1e293b; max-width: 130px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .td-muted{ font-size: .75rem; color: #64748b; max-width: 110px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

    /* ── Badges ── */
    .qty-badge { display: inline-flex; align-items: center; padding: 2px 9px; border-radius: 4px; font-size: .7rem; font-weight: 700; white-space: nowrap; }
    .qty-badge.ok  { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
    .qty-badge.low { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }

    .total-badge { display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 4px; font-size: .67rem; font-weight: 700; }
    .total-badge.ok  { background: #f0fdf4; color: #16a34a; }
    .total-badge.low { background: #fef2f2; color: #dc2626; }

    /* ── Accoes ── */
    .item-actions { display: flex; gap: 4px; justify-content: flex-end; }
    .action-icon {
        width: 24px; height: 24px; border-radius: 6px;
        border: 1px solid #e2e8f0; background: #fff;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: .75rem; color: #475569;
        text-decoration: none; cursor: pointer; transition: all .15s;
    }
    .action-icon:hover        { background: #f8fafc; border-color: #94a3b8; color: #1e293b; }
    .action-icon.danger:hover { background: #fef2f2; border-color: #fecaca; color: #dc2626; }

    /* ── Empty / No results ── */
    .empty-state  { text-align: center; padding: 56px 24px; color: #94a3b8; }
    .empty-state i{ font-size: 2.2rem; display: block; margin-bottom: 12px; opacity: .3; }
    .empty-state p{ font-size: .85rem; margin-bottom: 16px; }
    .no-results-row td { padding: 28px; text-align: center; font-size: .8rem; color: #94a3b8; }
</style>

<div class="page-wrap">

    {{-- TOOLBAR --}}
    <div class="toolbar">
        <div>
            <div class="page-eyebrow">Armazem</div>
            <div class="page-title">Mapa de Stock</div>
        </div>
        <div class="toolbar-right">
            <div class="search-wrap">
                <i class="bi bi-search"></i>
                <input type="text" id="globalSearch" class="search-input"
                       placeholder="Pesquisar artigo, referencia, marca...">
            </div>
            <a href="{{ route('stock-items.export', ['type' => 'excel']) }}" class="top-btn success">
                <i class="bi bi-file-earmark-spreadsheet"></i> Excel
            </a>
            <a href="{{ route('stock-items.export', ['type' => 'pdf']) }}" class="top-btn danger">
                <i class="bi bi-file-earmark-pdf"></i> PDF
            </a>
            <a href="{{ route('stock-items.create') }}" class="top-btn primary">
                <i class="bi bi-plus-lg"></i> Novo Artigo
            </a>
        </div>
    </div>

    {{-- KPI CARDS --}}
    @php
        $grouped = $stockItems->groupBy(fn($item) => $item->nome ?: 'Sem Nome');
    @endphp
    <div class="kpi-grid">
        <div class="kpi-card kpi-dark">
            <div class="kpi-icon dark"><i class="bi bi-box-seam"></i></div>
            <div>
                <div class="kpi-label">Total Referencias</div>
                <div class="kpi-value">{{ $stockItems->count() }}</div>
            </div>
        </div>
        <div class="kpi-card kpi-dark">
            <div class="kpi-icon dark"><i class="bi bi-collection"></i></div>
            <div>
                <div class="kpi-label">Total Artigos</div>
                <div class="kpi-value">{{ $grouped->count() }}</div>
            </div>
        </div>
        <div class="kpi-card kpi-red">
            <div class="kpi-icon red"><i class="bi bi-exclamation-triangle"></i></div>
            <div>
                <div class="kpi-label">Stock Critico (&le; 5)</div>
                <div class="kpi-value">{{ $stockItems->where('quantidade', '<=', 5)->count() }}</div>
            </div>
        </div>
    </div>

    {{-- TABELA ACCORDION --}}
    @if($grouped->isEmpty())
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <p>Ainda nao ha artigos registados no stock.</p>
            <a href="{{ route('stock-items.create') }}" class="top-btn primary">
                <i class="bi bi-plus-lg"></i> Adicionar primeiro artigo
            </a>
        </div>
    @else
    <div class="table-wrap">
        <table class="stock-table">
            <thead>
                <tr>
                    <th style="width: 36px;"></th>
                    <th>Artigo / Peca</th>
                    <th>Referencia</th>
                    <th>Marca / Fabricante</th>
                    <th>Localizacao</th>
                    <th>Unidade</th>
                    <th class="tc" style="width: 76px;">Qtd</th>
                    <th class="tc" style="width: 86px;">Acoes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grouped as $nomeArtigo => $itens)
                @php
                    $totalQty    = $itens->sum('quantidade');
                    $rowCount    = $itens->count();
                    $groupId     = 'g-' . Str::slug($nomeArtigo) . '-' . $loop->index;
                    $marcas      = $itens->pluck('marca_fabricante')->filter()->unique()->implode(', ');
                    $searchData  = strtolower(
                        $nomeArtigo . ' ' .
                        $itens->pluck('referencia')->implode(' ') . ' ' .
                        $itens->pluck('marca_fabricante')->implode(' ') . ' ' .
                        $itens->pluck('localizacao')->implode(' ')
                    );
                @endphp

                {{-- Linha do grupo (artigo) --}}
                <tr class="group-row"
                    data-group="{{ $groupId }}"
                    data-search="{{ $searchData }}">
                    <td class="tc">
                        <div class="chevron-wrap">
                            <i class="bi bi-chevron-right"></i>
                        </div>
                    </td>
                    <td>
                        <div class="group-name">
                            {{ $nomeArtigo }}
                            <span class="ref-count">{{ $rowCount }} ref.</span>
                        </div>
                    </td>
                    <td colspan="4" class="brands-preview">{{ $marcas ?: '—' }}</td>
                    <td class="tc">
                        <span class="total-badge {{ $totalQty <= 5 ? 'low' : 'ok' }}">
                            {{ $totalQty }}
                        </span>
                    </td>
                    <td></td>
                </tr>

                {{-- Linhas de variacao --}}
                @foreach($itens as $idx => $item)
                <tr class="var-row {{ $idx === $rowCount - 1 ? 'last-of-group' : '' }}"
                    data-group="{{ $groupId }}">
                    <td></td>
                    <td></td>
                    <td class="td-ref" title="{{ $item->referencia }}">{{ $item->referencia }}</td>
                    <td class="td-muted" title="{{ $item->marca_fabricante }}">{{ $item->marca_fabricante ?? '—' }}</td>
                    <td class="td-muted">{{ $item->localizacao ?? '—' }}</td>
                    <td class="td-muted">{{ $item->unidade ?? 'un' }}</td>
                    <td class="tc">
                        <span class="qty-badge {{ $item->quantidade <= 5 ? 'low' : 'ok' }}">
                            {{ $item->quantidade }}
                        </span>
                    </td>
                    <td class="tc">
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
                    </td>
                </tr>
                @endforeach

                @endforeach

                {{-- Sem resultados na pesquisa --}}
                <tr id="noResultsRow" style="display:none;">
                    <td colspan="8" class="no-results-row">Nenhum artigo encontrado para a pesquisa.</td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

</div>

{{-- MODAL CONFIRMACAO ELIMINAR --}}
<div class="confirm-overlay" id="confirmOverlay">
    <div class="confirm-box">
        <div class="confirm-icon"><i class="bi bi-trash3-fill"></i></div>
        <div class="confirm-title">Eliminar variacao?</div>
        <div class="confirm-msg">Esta accao e irreversivel. A referencia sera removida permanentemente do stock.</div>
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
    .confirm-overlay { position: fixed; inset: 0; background: rgba(15,23,42,.45); z-index: 9999; display: flex; align-items: center; justify-content: center; opacity: 0; pointer-events: none; transition: opacity .2s; }
    .confirm-overlay.open { opacity: 1; pointer-events: all; }
    .confirm-box { background: #fff; border-radius: 14px; padding: 28px 28px 22px; max-width: 380px; width: calc(100% - 32px); box-shadow: 0 20px 60px rgba(0,0,0,.18); transform: translateY(8px) scale(.98); transition: transform .2s; border-top: 4px solid #dc2626; }
    .confirm-overlay.open .confirm-box { transform: none; }
    .confirm-icon  { width: 44px; height: 44px; background: #fef2f2; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: #dc2626; margin-bottom: 14px; }
    .confirm-title { font-size: .95rem; font-weight: 700; color: #1e293b; margin-bottom: 6px; }
    .confirm-msg   { font-size: .8rem; color: #64748b; line-height: 1.5; margin-bottom: 12px; }
    .confirm-id    { font-size: .78rem; font-weight: 700; color: #1e293b; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 6px; padding: 4px 10px; display: inline-block; margin-bottom: 18px; }
    .confirm-actions { display: flex; gap: 8px; justify-content: flex-end; }
    .confirm-cancel  { padding: 7px 18px; border-radius: 8px; font-size: .78rem; font-weight: 600; border: 1px solid #e2e8f0; background: #fff; color: #475569; cursor: pointer; transition: all .15s; }
    .confirm-cancel:hover { background: #f8fafc; }
    .confirm-ok { padding: 7px 18px; border-radius: 8px; font-size: .78rem; font-weight: 600; border: none; background: #dc2626; color: #fff; cursor: pointer; transition: all .15s; }
    .confirm-ok:hover { background: #b91c1c; }
</style>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(function () {

    /* ── Toggle grupo (accordion) ── */
    $(document).on('click', '.group-row', function () {
        var g    = $(this).data('group');
        var open = $(this).toggleClass('open').hasClass('open');
        $('.var-row[data-group="' + g + '"]').toggleClass('visible', open);
    });

    /* ── Pesquisa global ── */
    $('#globalSearch').on('keyup', function () {
        var v = $(this).val().toLowerCase().trim();
        var any = false;

        $('.group-row').each(function () {
            var g     = $(this).data('group');
            var match = !v || ($(this).data('search') + '').indexOf(v) > -1;

            $(this).toggle(match);

            // Mantém variações visíveis apenas se o grupo estiver aberto
            var open = $(this).hasClass('open');
            $('.var-row[data-group="' + g + '"]').toggle(match && open);

            if (match) any = true;
        });

        $('#noResultsRow').toggle(!any);
    });

    /* ── Modal eliminar ── */
    $(document).on('click', '.btn-delete', function (e) {
        e.stopPropagation(); // evita toggle do grupo
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
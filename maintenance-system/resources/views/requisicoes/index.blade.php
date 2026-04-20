<x-app-layout>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    .table-card{background:#fff;border-radius:14px;border:1px solid #e9ecef;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.04)}
    #reqTable thead tr{background:#f8f9fa}
    #reqTable thead th{font-size:.67rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#868e96;border-bottom:1px solid #e9ecef;padding:8px 12px;white-space:nowrap}
    #reqTable tbody td{padding:9px 12px;vertical-align:middle;border-bottom:1px solid #f1f3f5;font-size:.82rem}
    #reqTable tbody tr:hover{background:#f8f9ff}
    #reqTable tbody tr:last-child td{border-bottom:none}
    .kpi-card{background:#fff;border-radius:14px;border:1px solid #e9ecef;padding:16px 20px;box-shadow:0 2px 8px rgba(0,0,0,.04);transition:transform .15s,box-shadow .15s;height:100%;position:relative;overflow:hidden}
    .kpi-card:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,0,0,.08)}
    .kpi-card::before{content:'';position:absolute;left:0;top:0;bottom:0;width:4px;border-radius:14px 0 0 14px}
    .kpi-card.blue::before{background:#1a56db}.kpi-card.green::before{background:#198754}
    .kpi-card.purple::before{background:#6f42c1}.kpi-card.gold::before{background:#fd7e14}
    .kpi-label{font-size:.65rem;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:#adb5bd;margin-bottom:6px}
    .kpi-value{font-size:1.7rem;font-weight:800;line-height:1;margin-bottom:2px}
    .kpi-value-sm{font-size:1.1rem;font-weight:800;line-height:1;margin-bottom:2px;padding-top:4px}
    .kpi-sub{font-size:.72rem;color:#adb5bd}
    .kpi-icon{position:absolute;right:16px;top:50%;transform:translateY(-50%);font-size:2rem;opacity:.1}
    .action-btn{width:28px;height:28px;border-radius:7px;display:inline-flex;align-items:center;justify-content:center;font-size:.72rem;border:1px solid;transition:all .15s;text-decoration:none;cursor:pointer;background:transparent}
    div.dataTables_wrapper div.dataTables_filter input{border-radius:8px;border:1px solid #dee2e6;padding:6px 12px;font-size:.82rem}
    div.dataTables_wrapper div.dataTables_length select{border-radius:8px;border:1px solid #dee2e6;padding:4px 8px;font-size:.82rem}
    .filter-panel{background:#fff;border-radius:14px;border:1px solid #e9ecef;padding:20px 24px;margin-bottom:24px;box-shadow:0 2px 8px rgba(0,0,0,.04)}
    .filter-title{font-size:.72rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#adb5bd;margin-bottom:14px;display:flex;align-items:center;gap:8px}
    .filter-group{display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end}
    .filter-item{display:flex;flex-direction:column;gap:5px}
    .filter-item label{font-size:.7rem;font-weight:600;letter-spacing:.8px;text-transform:uppercase;color:#6c757d}
    .filter-item select,.filter-item input{border:1px solid #dee2e6;border-radius:8px;padding:7px 12px;font-size:.82rem;color:#343a40;background:#f8f9fa;outline:none;transition:border-color .2s,box-shadow .2s;min-width:150px}
    .filter-item select:focus,.filter-item input:focus{border-color:#0d6efd;box-shadow:0 0 0 3px rgba(13,110,253,.1);background:#fff}
    .btn-filter{padding:8px 18px;border-radius:8px;font-size:.8rem;font-weight:600;cursor:pointer;border:none;display:inline-flex;align-items:center;gap:6px;transition:all .2s}
    .btn-filter-apply{background:#0d6efd;color:#fff}.btn-filter-apply:hover{background:#0b5ed7}
    .btn-filter-clear{background:#f1f5f9;color:#495057;border:1px solid #dee2e6}.btn-filter-clear:hover{background:#e9ecef}

    /* ── Botão extrato PDF ── */
    .btn-filter-pdf{background:#1a7a4a;color:#fff;border:none}.btn-filter-pdf:hover{background:#155f3a}
    .btn-filter-pdf:disabled{background:#adb5bd;cursor:not-allowed}
    #extractBar{display:none;margin-top:14px;padding:12px 16px;background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1px solid #bbf7d0;border-radius:10px;align-items:center;gap:12px;flex-wrap:wrap}
    #extractBar.visible{display:flex}
    #extractBar .ext-info{font-size:.78rem;color:#166534;font-weight:600;flex:1}
    #extractBar .ext-info span{font-weight:800}

    .active-filters{display:flex;flex-wrap:wrap;gap:6px;margin-top:12px}
    .filter-tag{display:inline-flex;align-items:center;gap:5px;background:#e7f1ff;color:#0d6efd;border:1px solid #b6d0ff;border-radius:20px;padding:3px 10px;font-size:.72rem;font-weight:500}
    .filter-tag .remove-tag{cursor:pointer;opacity:.6;font-size:.8rem}.filter-tag .remove-tag:hover{opacity:1}

    /* ── Toast ── */
    .toast-success{position:fixed;top:24px;right:24px;z-index:99999;background:#fff;border-radius:12px;padding:16px 20px;display:flex;align-items:center;gap:12px;box-shadow:0 8px 32px rgba(0,0,0,.12);border-left:4px solid #16a34a;min-width:300px;transform:translateX(120%);transition:transform 0.35s cubic-bezier(.34,1.56,.64,1)}
    .toast-success.show{transform:translateX(0)}
    .toast-icon{width:36px;height:36px;background:#f0fdf4;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#16a34a;font-size:1rem;flex-shrink:0}
    .toast-text{flex:1}
    .toast-title{font-size:.82rem;font-weight:700;color:#1e293b;margin-bottom:2px}
    .toast-sub{font-size:.74rem;color:#94a3b8}
    .toast-close{background:none;border:none;color:#94a3b8;cursor:pointer;font-size:1rem;padding:0;line-height:1}
    .toast-close:hover{color:#475569}

    /* ── Modal confirmação ── */
    .confirm-overlay{position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:9999;display:flex;align-items:center;justify-content:center;opacity:0;pointer-events:none;transition:opacity .25s}
    .confirm-overlay.open{opacity:1;pointer-events:all}
    .confirm-box{background:#fff;border-radius:20px;max-width:400px;width:calc(100% - 32px);box-shadow:0 24px 64px rgba(0,0,0,.18);transform:scale(.93) translateY(10px);transition:transform .25s cubic-bezier(.34,1.56,.64,1);overflow:hidden}
    .confirm-overlay.open .confirm-box{transform:scale(1) translateY(0)}
    .confirm-header{background:#fef2f2;padding:28px 28px 20px;text-align:center;border-bottom:1px solid #fecaca}
    .confirm-icon{width:56px;height:56px;background:#fee2e2;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:#dc2626;margin:0 auto 14px}
    .confirm-title{font-size:1.05rem;font-weight:700;color:#1e293b;margin-bottom:6px}
    .confirm-sub{font-size:.82rem;color:#94a3b8;line-height:1.6}
    .confirm-body{padding:20px 28px 24px}
    .confirm-warning{display:flex;align-items:center;gap:8px;background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:10px 14px;font-size:.78rem;color:#92400e;margin-bottom:20px}
    .confirm-actions{display:flex;gap:10px}
    .confirm-actions button{flex:1;padding:11px;border-radius:10px;font-size:.82rem;font-weight:600;border:none;cursor:pointer;transition:all .15s;display:flex;align-items:center;justify-content:center;gap:6px}
    .btn-cancel-confirm{background:#f1f5f9;color:#475569}.btn-cancel-confirm:hover{background:#e2e8f0}
    .btn-delete-confirm{background:#dc2626;color:#fff;box-shadow:0 2px 8px rgba(220,38,38,.3)}.btn-delete-confirm:hover{background:#b91c1c}
    .btn-delete-confirm:disabled{background:#f87171;cursor:not-allowed;box-shadow:none}
</style>

<div class="container-fluid py-4 px-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-file-earmark-text text-primary me-2"></i>Histórico de Requisições</h4>
            <p class="text-muted small mb-0">Consulta e gestão de todas as requisições de compra</p>
        </div>
        <a href="{{ route('requisicoes.create') }}" class="btn btn-primary btn-sm fw-bold px-3 shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Nova Requisição
        </a>
    </div>

    {{-- KPI CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="kpi-card blue">
                <div class="kpi-label">Total Requisições</div>
                <div class="kpi-value" style="color:#1a56db;">{{ $requisicoes->count() }}</div>
                <div class="kpi-sub">registadas</div>
                <i class="bi bi-file-earmark-text kpi-icon" style="color:#1a56db;"></i>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card purple">
                <div class="kpi-label">Total de Itens</div>
                <div class="kpi-value" style="color:#6f42c1;">{{ $requisicoes->sum(fn($r) => $r->items->count()) }}</div>
                <div class="kpi-sub">em todas as requisições</div>
                <i class="bi bi-box-seam kpi-icon" style="color:#6f42c1;"></i>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card green">
                <div class="kpi-label">Total Líquido</div>
                <div class="kpi-value-sm" style="color:#198754;">
                    {{ number_format($requisicoes->sum('total_liquid'), 2, ',', '.') }} MT
                </div>
                <div class="kpi-sub">sem IVA</div>
                <i class="bi bi-cash-stack kpi-icon" style="color:#198754;"></i>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card gold">
                <div class="kpi-label">Total Geral</div>
                <div class="kpi-value-sm" style="color:#fd7e14;">
                    {{ number_format($requisicoes->sum('total_final'), 2, ',', '.') }} MT
                </div>
                <div class="kpi-sub">com IVA (16%)</div>
                <i class="bi bi-receipt kpi-icon" style="color:#fd7e14;"></i>
            </div>
        </div>
    </div>

    {{-- FILTROS --}}
    <div class="filter-panel">
        <div class="filter-title"><i class="bi bi-funnel-fill"></i> Filtros de Pesquisa</div>
        <div class="filter-group">
            <div class="filter-item"><label>Data Início</label><input type="date" id="filterDateFrom"></div>
            <div class="filter-item"><label>Data Fim</label><input type="date" id="filterDateTo"></div>
            <div class="filter-item">
                <label>Fornecedor</label>
                <select id="filterFornecedor">
                    <option value="">Todos</option>
                    @foreach($requisicoes->pluck('supplier.name')->unique()->sort() as $nome)
                    <option value="{{ strtolower($nome) }}">{{ $nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex gap-2 align-items-end">
                <button class="btn-filter btn-filter-apply" onclick="applyFilters()"><i class="bi bi-search"></i> Filtrar</button>
                <button class="btn-filter btn-filter-clear" onclick="clearFilters()"><i class="bi bi-x-lg"></i> Limpar</button>
            </div>
        </div>

        {{-- BARRA DE EXTRATO — aparece após filtrar --}}
        <div id="extractBar">
            <div class="ext-info">
                <i class="bi bi-check-circle-fill me-1" style="color:#16a34a;"></i>
                Filtro activo: <span id="extractCount">0</span> requisição(ões) encontrada(s)
                <span id="extractFornecedorLabel" style="color:#15803d;"></span>
            </div>
            <button class="btn-filter btn-filter-pdf" id="btnExtratoPDF" onclick="gerarExtratoPDF()">
                <i class="bi bi-file-earmark-arrow-down"></i> Exportar Extrato PDF
            </button>
        </div>

        <div class="active-filters" id="activeTags"></div>
    </div>

    {{-- TABELA --}}
    <div class="table-card">
        <div class="p-3">
            <table class="table table-hover align-middle mb-0" id="reqTable" style="width:100%">
                <thead>
                    <tr>
                        <th>Nº / DATA</th>
                        <th>FORNECEDOR</th>
                        <th class="text-center">ITENS</th>
                        <th class="text-end">TOTAL LÍQUIDO</th>
                        <th class="text-end">IVA (16%)</th>
                        <th class="text-end">TOTAL GERAL</th>
                        <th class="text-center">PDF</th>
                        <th class="text-center">AÇÕES</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($requisicoes as $req)
                <tr data-data="{{ $req->date->format('Y-m-d') }}"
                    data-fornecedor="{{ strtolower($req->supplier->name) }}"
                    data-fornecedor-nome="{{ $req->supplier->name }}"
                    data-req-id="{{ $req->id }}"
                    data-data-fmt="{{ $req->date->format('d/m/Y') }}"
                    data-liquid="{{ number_format($req->total_liquid, 2, ',', '.') }}"
                    data-iva="{{ number_format($req->tax_amount, 2, ',', '.') }}"
                    data-total="{{ number_format($req->total_final, 2, ',', '.') }}">
                    <td>
                        <span class="fw-bold text-dark" style="font-size:.8rem;">#{{ $req->id }}</span><br>
                        <span class="text-muted" style="font-size:.72rem;">{{ $req->date->format('d/m/Y') }}</span>
                    </td>
                    <td>
                        <span class="fw-bold text-primary" style="font-size:.78rem;">{{ $req->supplier->name }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-secondary bg-opacity-10 text-secondary fw-semibold" style="font-size:.68rem;border-radius:6px;">
                            {{ $req->items->count() }} item(ns)
                        </span>
                    </td>
                    <td class="text-end" style="font-size:.78rem;">
                        {{ number_format($req->total_liquid, 2, ',', '.') }} MT
                    </td>
                    <td class="text-end text-muted" style="font-size:.75rem;">
                        {{ number_format($req->tax_amount, 2, ',', '.') }} MT
                    </td>
                    <td class="text-end">
                        <span class="fw-bold" style="font-size:.82rem;">{{ number_format($req->total_final, 2, ',', '.') }} MT</span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('requisicoes.pdf', $req->id) }}" target="_blank"
                           class="action-btn text-danger border-danger border-opacity-25" title="Abrir PDF">
                            <i class="bi bi-file-pdf"></i>
                        </a>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <!-- <button class="action-btn text-dark border-dark border-opacity-25"
                                    onclick="visualizarReq({{ $req->id }})" title="Visualizar / Imprimir">
                                <i class="bi bi-printer"></i>
                            </button> -->
                            <button class="action-btn text-danger border-danger border-opacity-25 btn-cancelar"
                                    data-id="{{ $req->id }}"
                                    data-label="#{{ $req->id }} — {{ $req->supplier->name }}"
                                    title="Cancelar">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- TOAST --}}
<div class="toast-success" id="toastSuccess">
    <div class="toast-icon"><i class="bi bi-check-lg"></i></div>
    <div class="toast-text">
        <div class="toast-title">Requisição cancelada</div>
        <div class="toast-sub">O registo foi removido com sucesso.</div>
    </div>
    <button class="toast-close" onclick="closeToast()"><i class="bi bi-x-lg"></i></button>
</div>

{{-- MODAL CONFIRMAÇÃO --}}
<div class="confirm-overlay" id="confirmOverlay">
    <div class="confirm-box">
        <div class="confirm-header">
            <div class="confirm-icon"><i class="bi bi-x-circle-fill"></i></div>
            <div class="confirm-title">Cancelar requisição?</div>
            <div class="confirm-sub">Tens a certeza que queres cancelar <strong id="confirmLabel"></strong>?</div>
        </div>
        <div class="confirm-body">
            <div class="confirm-warning">
                <i class="bi bi-exclamation-triangle-fill"></i>
                Esta acção é irreversível e não pode ser desfeita.
            </div>
            <div class="confirm-actions">
                <button class="btn-cancel-confirm" onclick="closeConfirm()">
                    <i class="bi bi-x-lg"></i> Cancelar
                </button>
                <button class="btn-delete-confirm" id="confirmOkBtn">
                    <i class="bi bi-x-circle"></i> Confirmar
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
let dtTable;
let _deleteId = null;

$(document).ready(function(){
    dtTable = $('#reqTable').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json' },
        order: [[0, 'desc']],
        columnDefs: [{ orderable: false, targets: [6, 7] }],
        pageLength: 15,
        lengthMenu: [5, 10, 15, 25, 50],
    });

    @if(session('deleted'))
        showToast();
    @endif
});

// ── Toast ──
function showToast() {
    var t = document.getElementById('toastSuccess');
    t.classList.add('show');
    setTimeout(closeToast, 4000);
}
function closeToast() {
    document.getElementById('toastSuccess').classList.remove('show');
}

// ── Modal ──
$(document).on('click', '.btn-cancelar', function() {
    _deleteId = $(this).data('id');
    $('#confirmLabel').text($(this).data('label'));
    $('#confirmOkBtn').prop('disabled', false)
        .html('<i class="bi bi-x-circle"></i> Confirmar');
    $('#confirmOverlay').addClass('open');
});

$('#confirmOkBtn').on('click', function() {
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> A cancelar...';

    fetch(`/requisicoes/${_deleteId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(res => {
        if (!res.ok) throw new Error('Erro HTTP: ' + res.status);
        return res.json();
    })
    .then(data => {
        closeConfirm();
        if (data.success) {
            showToast();
            setTimeout(() => window.location.reload(), 1800);
        } else {
            alert('Erro ao cancelar. Tenta novamente.');
        }
    })
    .catch(err => {
        closeConfirm();
        console.error(err);
        alert('Erro de ligação. Tenta novamente.');
    });
});

$('#confirmOverlay').on('click', function(e) {
    if (e.target === this) closeConfirm();
});

$(document).on('keydown', function(e) {
    if (e.key === 'Escape') closeConfirm();
});

function closeConfirm() {
    $('#confirmOverlay').removeClass('open');
}

// ── Filtros ──
function applyFilters() {
    const df = $('#filterDateFrom').val();
    const dt = $('#filterDateTo').val();
    const fr = $('#filterFornecedor').val().toLowerCase();

    let visibleCount = 0;

    $('#reqTable tbody tr').each(function() {
        const r = $(this);
        let show = true;
        if (df && r.data('data') < df) show = false;
        if (dt && r.data('data') > dt) show = false;
        if (fr && !r.data('fornecedor').includes(fr)) show = false;
        r.toggle(show);
        if (show) visibleCount++;
    });

    dtTable.draw();
    renderTags(df, dt, fr);
    updateExtractBar(visibleCount, df, dt, fr);
}

function clearFilters() {
    $('#filterDateFrom, #filterDateTo').val('');
    $('#filterFornecedor').val('');
    $('#reqTable tbody tr').show();
    $('#activeTags').html('');
    dtTable.draw();
    $('#extractBar').removeClass('visible');
}

function renderTags(df, dt, fr) {
    const labels = { df: `De: ${df}`, dt: `Até: ${dt}`, fr: `Fornecedor: ${fr}` };
    const vals   = { df, dt, fr };
    let html = '';
    for (const [k, v] of Object.entries(vals)) {
        if (v) html += `<span class="filter-tag">${labels[k]} <span class="remove-tag" onclick="removeTag('${k}')">✕</span></span>`;
    }
    $('#activeTags').html(html);
}

function removeTag(key) {
    const map = { df: 'filterDateFrom', dt: 'filterDateTo', fr: 'filterFornecedor' };
    $(`#${map[key]}`).val('');
    applyFilters();
}

function updateExtractBar(count, df, dt, fr) {
    const bar = $('#extractBar');
    $('#extractCount').text(count);

    // Texto descritivo do filtro activo
    let label = '';
    if (fr) {
        const nome = $('#filterFornecedor option:selected').text();
        label = ` — ${nome}`;
    }
    if (df || dt) {
        const range = [df ? df.split('-').reverse().join('/') : '...', dt ? dt.split('-').reverse().join('/') : '...'].join(' a ');
        label += ` [${range}]`;
    }
    $('#extractFornecedorLabel').text(label);

    if (count > 0) {
        bar.addClass('visible');
    } else {
        bar.removeClass('visible');
    }
}

// ── Geração do Extrato PDF (janela de impressão) ──
function gerarExtratoPDF() {
    // Recolhe apenas as linhas visíveis
    const rows = [];
    $('#reqTable tbody tr:visible').each(function() {
        const r = $(this);
        rows.push({
            id:           r.data('req-id'),
            data:         r.data('data-fmt'),
            fornecedor:   r.data('fornecedor-nome'),
            liquid:       r.data('liquid'),
            iva:          r.data('iva'),
            total:        r.data('total'),
        });
    });

    if (!rows.length) return;

    // Cabeçalho do filtro para o PDF
    const df  = $('#filterDateFrom').val();
    const dt  = $('#filterDateTo').val();
    const frNome = $('#filterFornecedor option:selected').text() !== 'Todos'
                    ? $('#filterFornecedor option:selected').text()
                    : 'Todos os Fornecedores';

    const periodoStr = df || dt
        ? `${df ? df.split('-').reverse().join('/') : '—'} a ${dt ? dt.split('-').reverse().join('/') : '—'}`
        : 'Todo o período';

    // Totais do extrato
    const sumLiquid = rows.reduce((a, r) => a + parseFloat(r.liquid.replace(/\./g,'').replace(',','.')), 0);
    const sumIva    = rows.reduce((a, r) => a + parseFloat(r.iva.replace(/\./g,'').replace(',','.')), 0);
    const sumTotal  = rows.reduce((a, r) => a + parseFloat(r.total.replace(/\./g,'').replace(',','.')), 0);

    const fmt = n => n.toLocaleString('pt-PT', {minimumFractionDigits:2, maximumFractionDigits:2}) + ' MT';

    const dataHoje = new Date().toLocaleDateString('pt-PT', {day:'2-digit',month:'2-digit',year:'numeric'});
    const horaAgora = new Date().toLocaleTimeString('pt-PT', {hour:'2-digit',minute:'2-digit'});

    // Linhas da tabela
    const linhas = rows.map((r, i) => `
        <tr>
            <td>${i + 1}</td>
            <td><strong>#${r.id}</strong></td>
            <td>${r.data}</td>
            <td>${r.fornecedor}</td>
            <td class="num">${r.liquid} MT</td>
            <td class="num">${r.iva} MT</td>
            <td class="num total-col">${r.total} MT</td>
        </tr>
    `).join('');

    const html = `<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<title>Extrato de Requisições</title>
<style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:DejaVu Sans,Arial,sans-serif;font-size:12px;color:#222;background:#fff;padding:30px}

    /* ── Cabeçalho ── */
    .header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;border-bottom:3px solid #c60a1a;padding-bottom:16px}
    .company-block{display:flex;flex-direction:column;align-items:flex-start;gap:8px}
    .company-logo{width:90px;height:auto}
    .company-info{display:flex;flex-direction:column}
    .company-name{font-size:13px;font-weight:bold;color:#c60a1a;line-height:1.3}
    .company-detail{font-size:9px;color:#555;margin-top:3px;line-height:1.6}
    .doc-block{text-align:right}
    .doc-block h1{font-size:18px;font-weight:bold;color:#c60a1a}
    .doc-block .doc-sub{font-size:11px;color:#888;margin-top:4px}

    /* ── Resumo filtro ── */
    .filter-summary{display:flex;gap:0;margin-bottom:20px;border:1px solid #e8edf2;border-radius:4px;overflow:hidden}
    .fs-item{flex:1;padding:10px 14px;background:#fdf2f2;border-right:1px solid #e8edf2}
    .fs-item:last-child{border-right:none}
    .fs-label{font-size:8px;font-weight:bold;letter-spacing:1px;text-transform:uppercase;color:#888;margin-bottom:3px}
    .fs-value{font-size:11px;font-weight:bold;color:#222}

    /* ── Tabela ── */
    table{width:100%;border-collapse:collapse;margin-bottom:16px}
    thead tr{background:#c60a1a}
    thead th{padding:8px 10px;font-size:9.5px;font-weight:bold;letter-spacing:.8px;text-transform:uppercase;color:#fff;text-align:left;white-space:nowrap}
    thead th.num{text-align:right}
    tbody tr{border-bottom:1px solid #e8edf2}
    tbody tr:nth-child(even){background:#fdf5f5}
    tbody td{padding:8px 10px;font-size:11px;color:#334155}
    tbody td.num{text-align:right;font-variant-numeric:tabular-nums}
    tbody td.total-col{font-weight:bold;color:#1e293b}

    /* ── Totais ── */
    .totals-wrap{width:280px;margin-left:auto;margin-bottom:24px}
    .totals-wrap table{margin-bottom:0}
    .totals-wrap td{padding:5px 10px;font-size:11px}
    .totals-wrap td.lbl{color:#666}
    .totals-wrap td.val{text-align:right;font-weight:bold}
    .totals-wrap tr.grand{background:#c60a1a;color:#fff}
    .totals-wrap tr.grand td{padding:8px 10px;font-size:13px}
    .totals-wrap tr.grand td.lbl{color:#fff}

    /* ── Rodapé ── */
    .page-footer{margin-top:32px;border-top:2px solid #c60a1a;padding-top:14px;display:flex;justify-content:space-between;align-items:flex-start}
    .footer-left .footer-label{font-size:8px;text-transform:uppercase;letter-spacing:.5px;color:#64748b;margin-bottom:2px}
    .footer-left .footer-val{font-size:10px;color:#334155}
    .footer-right{font-size:9px;color:#94a3b8;text-align:right}

    @media print{
        body{padding:20px}
        @page{margin:12mm;size:A4 landscape}
    }
</style>
</head>
<body>

<!-- CABEÇALHO -->
<div class="header">
    <div class="company-block">
        <img src="/images/bymozelogo.png" class="company-logo" alt="Logo">
        <div class="company-info">
            <div class="company-name">Fábrica de Explosivos de Moçambique</div>
            <div class="company-detail">
                Contribuinte Nº 400019029<br>
                Av. Samora Machel Nº — Parcela 10<br>
                Telef. +258 21 745 86/03 &nbsp;|&nbsp; FAX. +258 21 745 802
            </div>
        </div>
    </div>
    <div class="doc-block">
        <h1>EXTRATO DE REQUISIÇÕES</h1>
        <div class="doc-sub">Emitido em ${dataHoje} às ${horaAgora}</div>
    </div>
</div>

<!-- RESUMO DO FILTRO -->
<div class="filter-summary">
    <div class="fs-item">
        <div class="fs-label">Fornecedor</div>
        <div class="fs-value">${frNome}</div>
    </div>
    <div class="fs-item">
        <div class="fs-label">Período</div>
        <div class="fs-value">${periodoStr}</div>
    </div>
    <div class="fs-item">
        <div class="fs-label">Nº de Requisições</div>
        <div class="fs-value">${rows.length}</div>
    </div>
</div>

<!-- TABELA -->
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Nº Req.</th>
            <th>Data</th>
            <th>Fornecedor</th>
            <th class="num">Total Líquido</th>
            <th class="num">IVA (16%)</th>
            <th class="num">Total Geral</th>
        </tr>
    </thead>
    <tbody>${linhas}</tbody>
</table>

<!-- TOTAIS -->
<div class="totals-wrap">
    <table>
       
        <tr class="grand">
            <td class="lbl">TOTAL GERAL</td>
            <td class="val">${fmt(sumTotal)}</td>
        </tr>
    </table>
</div>

<!-- RODAPÉ -->
<div class="page-footer">
    <div class="footer-left">
        <div class="footer-label">Documento gerado por</div>
        <div class="footer-val">{{ auth()->user()->name }} &nbsp;|&nbsp; {{ auth()->user()->email }}</div>
    </div>
    <div class="footer-right">
        Documento gerado automaticamente pelo sistema de gestão.<br>
        Não requer assinatura.
    </div>
</div>

<script>
    window.onload = function() { window.print(); }
<\/script>
</body>
</html>`;

    const win = window.open('', '_blank', 'width=1100,height=750');
    win.document.write(html);
    win.document.close();
}

function visualizarReq(id) {
    window.open(`/requisicoes/${id}/pdf`, '_blank');
}
</script>

</x-app-layout>
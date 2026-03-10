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
    .btn-filter-clear{background:#f1f3f5;color:#495057;border:1px solid #dee2e6}.btn-filter-clear:hover{background:#e9ecef}
    .active-filters{display:flex;flex-wrap:wrap;gap:6px;margin-top:12px}
    .filter-tag{display:inline-flex;align-items:center;gap:5px;background:#e7f1ff;color:#0d6efd;border:1px solid #b6d0ff;border-radius:20px;padding:3px 10px;font-size:.72rem;font-weight:500}
    .filter-tag .remove-tag{cursor:pointer;opacity:.6;font-size:.8rem}.filter-tag .remove-tag:hover{opacity:1}
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

    {{-- TOAST --}}
    @if(session('success'))
    <div class="position-fixed top-0 end-0 p-3" style="z-index:9999;">
        <div id="successToast" class="toast show border-0"
             style="border-radius:10px;min-width:260px;overflow:hidden;
                    background:linear-gradient(135deg,rgba(25,135,84,.92),rgba(32,201,151,.92));
                    backdrop-filter:blur(16px);border:1px solid rgba(255,255,255,.25)!important;
                    box-shadow:0 8px 32px rgba(25,135,84,.3);">
            <div class="d-flex align-items-center gap-3 px-3 py-3">
                <i class="bi bi-check-circle-fill text-white" style="font-size:1.4rem;"></i>
                <div style="font-size:.78rem;color:rgba(255,255,255,.95);line-height:1.4;">{{ session('success') }}</div>
                <button type="button" class="btn-close btn-close-white opacity-75 ms-auto" data-bs-dismiss="toast" style="font-size:.55rem;"></button>
            </div>
            <div style="height:2px;background:rgba(255,255,255,.15);overflow:hidden;">
                <div id="toastProgress" style="height:100%;width:100%;background:rgba(255,255,255,.6);transition:width 3.5s linear;"></div>
            </div>
        </div>
    </div>
    @endif

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
                    data-fornecedor="{{ strtolower($req->supplier->name) }}">
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
                            <button class="action-btn text-dark border-dark border-opacity-25"
                                    onclick="visualizarReq({{ $req->id }})" title="Visualizar / Imprimir">
                                <i class="bi bi-printer"></i>
                            </button>
                            <button class="action-btn text-danger border-danger border-opacity-25"
                                    onclick="cancelarReq({{ $req->id }})" title="Cancelar">
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

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
let dtTable;
$(document).ready(function(){
    dtTable = $('#reqTable').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json' },
        order: [[0, 'desc']],
        columnDefs: [{ orderable: false, targets: [6, 7] }],
        pageLength: 15,
        lengthMenu: [5, 10, 15, 25, 50],
    });

    @if(session('success'))
    const toastEl = document.getElementById('successToast');
    new bootstrap.Toast(toastEl, { delay: 3500 }).show();
    setTimeout(()=>{ document.getElementById('toastProgress').style.width='0%'; }, 50);
    toastEl.style.opacity='0'; toastEl.style.transform='translateX(60px) scale(0.95)';
    toastEl.style.transition='all 0.5s cubic-bezier(0.34,1.56,0.64,1)';
    setTimeout(()=>{ toastEl.style.opacity='1'; toastEl.style.transform='translateX(0) scale(1)'; }, 50);
    @endif
});

function applyFilters() {
    const df = $('#filterDateFrom').val();
    const dt = $('#filterDateTo').val();
    const fr = $('#filterFornecedor').val().toLowerCase();

    $('#reqTable tbody tr').each(function() {
        const r = $(this);
        let show = true;
        if (df && r.data('data') < df) show = false;
        if (dt && r.data('data') > dt) show = false;
        if (fr && !r.data('fornecedor').includes(fr)) show = false;
        r.toggle(show);
    });
    dtTable.draw();
    renderTags(df, dt, fr);
}

function clearFilters() {
    $('#filterDateFrom, #filterDateTo').val('');
    $('#filterFornecedor').val('');
    $('#reqTable tbody tr').show();
    $('#activeTags').html('');
    dtTable.draw();
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

function visualizarReq(id) {
    window.open(`/requisicoes/${id}/pdf`, '_blank');
}

function cancelarReq(id) {
    if (confirm('Tens a certeza que queres cancelar esta requisição?')) {
        fetch(`/requisicoes/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
            }
        }).then(() => window.location.reload());
    }
}
</script>

</x-app-layout>
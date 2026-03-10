<x-app-layout>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<style>
    .kpi-card{background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:16px 20px;display:flex;align-items:center;gap:14px;box-shadow:0 1px 4px rgba(0,0,0,.04)}
    .kpi-card.dark  {border-left:4px solid #1e293b}
    .kpi-card.green {border-left:4px solid #16a34a}
    .kpi-card.red   {border-left:4px solid #dc2626}
    .kpi-card.grey  {border-left:4px solid #94a3b8}
    .kpi-icon{width:36px;height:36px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0}
    .kpi-icon.dark  {background:#f1f5f9;color:#1e293b}
    .kpi-icon.green {background:#f0fdf4;color:#16a34a}
    .kpi-icon.red   {background:#fef2f2;color:#dc2626}
    .kpi-icon.grey  {background:#f8fafc;color:#94a3b8}
    .kpi-label{font-size:.6rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#94a3b8;margin-bottom:2px}
    .kpi-value{font-size:1.4rem;font-weight:800;color:#1e293b;line-height:1}

    /* ── Filter panel ── */
    .filter-panel{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:14px 20px;margin-bottom:14px;box-shadow:0 1px 4px rgba(0,0,0,.04)}
    .filter-title{font-size:.6rem;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;color:#94a3b8;margin-bottom:10px;display:flex;align-items:center;gap:6px}
    .filter-group{display:flex;flex-wrap:wrap;gap:8px;align-items:center}
    .filter-select{font-size:.78rem;border:1px solid #e2e8f0;border-radius:8px;padding:5px 10px;color:#334155;background:#fff;cursor:pointer;transition:border-color .15s}
    .filter-select:focus{outline:none;border-color:#64748b}
    .filter-input{font-size:.78rem;border:1px solid #e2e8f0;border-radius:8px;padding:5px 10px;color:#334155;background:#fff;transition:border-color .15s;min-width:180px}
    .filter-input:focus{outline:none;border-color:#64748b;box-shadow:0 0 0 3px rgba(100,116,139,.1)}
    .btn-filter{display:inline-flex;align-items:center;gap:5px;padding:5px 14px;border-radius:8px;font-size:.75rem;font-weight:600;border:1px solid #1e293b;background:#1e293b;color:#fff;cursor:pointer;transition:all .15s}
    .btn-filter:hover{background:#334155}
    .btn-filter-clear{display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:8px;font-size:.75rem;font-weight:600;border:1px solid #e2e8f0;background:#fff;color:#475569;cursor:pointer;transition:all .15s}
    .btn-filter-clear:hover{background:#f8fafc;border-color:#cbd5e1}
    .filter-tags{display:flex;flex-wrap:wrap;gap:6px;margin-top:8px}
    .filter-tag{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:6px;font-size:.68rem;font-weight:600;background:#f1f5f9;color:#334155;border:1px solid #e2e8f0}
    .filter-tag button{background:none;border:none;padding:0;color:#94a3b8;cursor:pointer;font-size:.75rem;line-height:1;display:flex;align-items:center}
    .filter-tag button:hover{color:#ef4444}
    #filtroAtivo{font-size:.7rem;color:#94a3b8;margin-left:auto}

    /* ── Table card ── */
    .table-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.04)}
    #machinesTable thead th{font-size:.65rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#94a3b8;background:#f8fafc;border-bottom:1px solid #e2e8f0;padding:10px 14px;white-space:nowrap}
    #machinesTable tbody td{font-size:.82rem;color:#334155;padding:9px 14px;vertical-align:middle;border-bottom:1px solid #f1f5f9}
    #machinesTable tbody tr:last-child td{border-bottom:none}
    #machinesTable tbody tr:hover{background:#f8fafc}

    .status-badge{display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:6px;font-size:.68rem;font-weight:700;border:1px solid}
    .status-badge.operacional{background:#f0fdf4;color:#166534;border-color:#bbf7d0}
    .status-badge.manutencao {background:#f0f9ff;color:#0369a1;border-color:#bae6fd}
    .status-badge.avariada   {background:#fef2f2;color:#dc2626;border-color:#fecaca}
    .status-badge.desativada {background:#f8fafc;color:#475569;border-color:#e2e8f0}

    .action-btn{width:28px;height:28px;border-radius:7px;border:1px solid #e2e8f0;background:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:.8rem;color:#475569;text-decoration:none;transition:all .15s;cursor:pointer}
    .action-btn:hover{background:#f8fafc;border-color:#94a3b8;color:#1e293b}
    .action-btn.danger:hover{background:#fef2f2;border-color:#fecaca;color:#dc2626}

    .top-btn{display:inline-flex;align-items:center;gap:6px;padding:7px 16px;border-radius:8px;font-size:.78rem;font-weight:600;text-decoration:none;border:1px solid #e2e8f0;background:#fff;color:#475569;transition:all .15s;cursor:pointer}
    .top-btn:hover{background:#f8fafc;border-color:#cbd5e1;color:#334155}
    .top-btn.primary{background:#1e293b;border-color:#1e293b;color:#fff}
    .top-btn.primary:hover{background:#334155;color:#fff}
    .top-btn.success{background:#fff;border-color:#bbf7d0;color:#16a34a}
    .top-btn.success:hover{background:#f0fdf4}
    .top-btn.danger-outline{background:#fff;border-color:#fecaca;color:#dc2626}
    .top-btn.danger-outline:hover{background:#fef2f2}

    .dataTables_wrapper{padding:12px 16px 16px}
    .dataTables_wrapper .dataTables_filter{text-align:right}
    .dataTables_wrapper .dataTables_filter input{font-size:.8rem;border:1px solid #e2e8f0;border-radius:8px;padding:5px 10px;color:#334155}
    .dataTables_wrapper .dataTables_filter input:focus{outline:none;border-color:#64748b;box-shadow:0 0 0 3px rgba(100,116,139,.1)}
    .dataTables_wrapper .dataTables_length select{font-size:.8rem;border:1px solid #e2e8f0;border-radius:8px;padding:4px 8px;color:#334155}
    .dataTables_wrapper .dataTables_info,.dataTables_wrapper .dataTables_paginate{font-size:.75rem;color:#94a3b8;margin-top:12px}
    .dataTables_wrapper .paginate_button{border-radius:6px!important;font-size:.75rem!important}
    .dataTables_wrapper .row{margin:0}
    .dataTables_wrapper .dataTables_length,.dataTables_wrapper .dataTables_filter{padding:0 0 10px}

    /* ── Modal confirmação ── */
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

    /* ── Print ── */
    @media print {
        .no-print{display:none!important}
        .table-card{border:none!important;box-shadow:none!important}
        #machinesTable thead th{background:#f8fafc!important;-webkit-print-color-adjust:exact;print-color-adjust:exact}
        .print-header{display:block!important}
        body{font-size:11pt}
    }
    .print-header{display:none;margin-bottom:16px}
    .print-header h5{font-size:13pt;font-weight:700;color:#1e293b;margin-bottom:2px}
    .print-header p{font-size:8pt;color:#64748b}
</style>

<div class="container-fluid py-4 px-4">

    {{-- CABEÇALHO --}}
    <div class="d-flex justify-content-between align-items-start mb-4 no-print">
        <div>
            <div style="font-size:.6rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">Gestão de Frota</div>
            <h4 class="fw-bold mb-0" style="color:#1e293b;font-size:1.25rem;">Equipamentos e Máquinas</h4>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('machines.export', ['type' => 'excel']) }}" class="top-btn success">
                <i class="bi bi-file-earmark-spreadsheet"></i> Excel
            </a>
            <a href="{{ route('machines.export', ['type' => 'pdf']) }}" class="top-btn danger-outline">
                <i class="bi bi-file-earmark-pdf"></i> PDF
            </a>
            <button onclick="window.print()" class="top-btn">
                <i class="bi bi-printer"></i> Imprimir
            </button>
            <a href="{{ route('machines.create') }}" class="top-btn primary">
                <i class="bi bi-plus-lg"></i> Adicionar
            </a>
        </div>
    </div>

    {{-- KPI CARDS --}}
    <div class="row g-3 mb-4 no-print">
        <div class="col-md-3">
            <div class="kpi-card dark">
                <div class="kpi-icon dark"><i class="bi bi-cpu"></i></div>
                <div>
                    <div class="kpi-label">Total</div>
                    <div class="kpi-value">{{ \App\Models\Machine::count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card green">
                <div class="kpi-icon green"><i class="bi bi-check-circle"></i></div>
                <div>
                    <div class="kpi-label">Operacionais</div>
                    <div class="kpi-value">{{ \App\Models\Machine::where('status','Operacional')->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card red">
                <div class="kpi-icon red"><i class="bi bi-exclamation-triangle"></i></div>
                <div>
                    <div class="kpi-label">Avariadas</div>
                    <div class="kpi-value">{{ \App\Models\Machine::where('status','Avariada')->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card grey">
                <div class="kpi-icon grey"><i class="bi bi-dash-circle"></i></div>
                <div>
                    <div class="kpi-label">Desativadas</div>
                    <div class="kpi-value">{{ \App\Models\Machine::where('status','Desativada')->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTROS --}}
    <div class="filter-panel no-print">
        <div class="filter-title">
            <i class="bi bi-funnel"></i> Filtros
            <span id="filtroAtivo"></span>
        </div>
        <div class="filter-group">
            <select id="filtroEstado" class="filter-select">
                <option value="">Todos os estados</option>
                <option value="Operacional">Operacional</option>
                <option value="Em Manutenção">Em Manutenção</option>
                <option value="Avariada">Avariada</option>
                <option value="Desativada">Desativada</option>
            </select>

            <select id="filtroTipo" class="filter-select">
                <option value="">Todos os tipos</option>
                @foreach($machines->pluck('tipo_equipamento')->unique()->sort() as $tipo)
                <option value="{{ $tipo }}">{{ Str::limit($tipo, 40) }}</option>
                @endforeach
            </select>

            <input type="text" id="filtroLocalizacao" class="filter-input"
                   placeholder="Filtrar por localização...">

            <button class="btn-filter" onclick="applyFilters()">
                <i class="bi bi-check-lg"></i> Aplicar
            </button>
            <button class="btn-filter-clear" onclick="clearFilters()">
                <i class="bi bi-x"></i> Limpar
            </button>
        </div>
        <div class="filter-tags" id="filterTags"></div>
    </div>

    {{-- FLASH --}}
    @if(session('success'))
    <div class="d-flex align-items-center gap-2 mb-3 px-3 py-2 rounded no-print"
         style="background:#f0fdf4;border:1px solid #bbf7d0;font-size:.8rem;color:#166534;">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
    @endif

    {{-- CABEÇALHO DE IMPRESSÃO --}}
    <div class="print-header">
        <h5>Listagem de Equipamentos e Máquinas</h5>
        <p>FEM OFICINAS · Extraído em {{ now()->format('d/m/Y H:i') }} · <span id="printFilterLabel"></span></p>
    </div>

    {{-- TABELA --}}
    @if($machines->isEmpty())
    <div class="table-card p-5 text-center no-print" style="color:#94a3b8;">
        <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.35;"></i>
        <div style="font-size:.85rem;">Ainda não há máquinas registadas.</div>
        <a href="{{ route('machines.create') }}" class="top-btn primary mt-3 mx-auto">
            <i class="bi bi-plus-lg"></i> Adicionar primeira máquina
        </a>
    </div>
    @else
    <div class="table-card">
        <div class="table-responsive">
            <table id="machinesTable" class="table mb-0">
                <thead>
                    <tr>
                        <th>Nº Interno</th>
                        <th>Tipo de Equipamento</th>
                        <th>Marca / Modelo</th>
                        <th>Chassi</th>
                        <th>Matrícula</th>
                        <th>Localização</th>
                        <th>Estado</th>
                        <th class="no-print">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($machines as $machine)
                    @php
                        $statusKey = match($machine->status) {
                            'Operacional'   => 'operacional',
                            'Em Manutenção' => 'manutencao',
                            'Avariada'      => 'avariada',
                            'Desativada'    => 'desativada',
                            default         => 'desativada',
                        };
                        $statusIcon = match($machine->status) {
                            'Operacional'   => 'check-circle-fill',
                            'Em Manutenção' => 'gear-wide-connected',
                            'Avariada'      => 'exclamation-triangle-fill',
                            'Desativada'    => 'dash-circle',
                            default         => 'dash-circle',
                        };
                    @endphp
                    <tr
                        data-status="{{ $machine->status }}"
                        data-tipo="{{ $machine->tipo_equipamento }}"
                        data-localizacao="{{ strtolower($machine->localizacao) }}">
                        <td><span class="fw-semibold">{{ $machine->numero_interno }}</span></td>
                        <td>{{ Str::limit($machine->tipo_equipamento, 30) }}</td>
                        <td>{{ Str::limit($machine->marca, 15) }} / {{ Str::limit($machine->modelo, 20) }}</td>
                        <td style="color:#94a3b8;">{{ $machine->nr_chassi ?? '—' }}</td>
                        <td style="color:#94a3b8;">{{ $machine->matricula ?? '—' }}</td>
                        <td>{{ $machine->localizacao }}</td>
                        <td>
                            <span class="status-badge {{ $statusKey }}">
                                <i class="bi bi-{{ $statusIcon }}"></i>
                                {{ $machine->status }}
                            </span>
                        </td>
                        <td class="no-print">
                            <div class="d-flex gap-1">
                                <a href="{{ route('machines.show', $machine->id) }}"
                                   class="action-btn" title="Ver detalhes">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('machines.edit', $machine->id) }}"
                                   class="action-btn" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button"
                                        class="action-btn danger btn-delete"
                                        title="Eliminar"
                                        data-action="{{ route('machines.destroy', $machine->id) }}"
                                        data-label="{{ $machine->numero_interno }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>

{{-- MODAL CONFIRMAÇÃO ELIMINAR --}}
<div class="confirm-overlay" id="confirmOverlay">
    <div class="confirm-box">
        <div class="confirm-icon"><i class="bi bi-trash3-fill"></i></div>
        <div class="confirm-title">Eliminar equipamento?</div>
        <div class="confirm-msg">Esta acção é irreversível. O equipamento e todo o seu histórico serão removidos permanentemente.</div>
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

{{-- Form oculto único reutilizável --}}
<form id="deleteForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
var dtTable;
var activeFilters = { estado: '', tipo: '', localizacao: '' };

$(document).ready(function () {
    dtTable = $('#machinesTable').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json' },
        pageLength: 10,
        order: [[0, 'asc']],
        columnDefs: [{ orderable: false, targets: -1 }]
    });

    // Filtro personalizado
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        var row = $(dtTable.row(dataIndex).node());
        var estado      = row.data('status') || '';
        var tipo        = row.data('tipo') || '';
        var localizacao = (row.data('localizacao') || '').toLowerCase();

        if (activeFilters.estado      && estado !== activeFilters.estado) return false;
        if (activeFilters.tipo        && tipo   !== activeFilters.tipo)   return false;
        if (activeFilters.localizacao && !localizacao.includes(activeFilters.localizacao.toLowerCase())) return false;
        return true;
    });

    dtTable.columns.adjust();
    $(window).on('resize', function () { dtTable.columns.adjust(); });
});

function applyFilters() {
    activeFilters.estado      = $('#filtroEstado').val();
    activeFilters.tipo        = $('#filtroTipo').val();
    activeFilters.localizacao = $('#filtroLocalizacao').val().trim();
    dtTable.draw();
    renderTags();
    updatePrintLabel();
}

function clearFilters() {
    activeFilters = { estado: '', tipo: '', localizacao: '' };
    $('#filtroEstado').val('');
    $('#filtroTipo').val('');
    $('#filtroLocalizacao').val('');
    dtTable.draw();
    renderTags();
    updatePrintLabel();
}

function removeFilter(key) {
    activeFilters[key] = '';
    if (key === 'estado')      $('#filtroEstado').val('');
    if (key === 'tipo')        $('#filtroTipo').val('');
    if (key === 'localizacao') $('#filtroLocalizacao').val('');
    dtTable.draw();
    renderTags();
    updatePrintLabel();
}

function renderTags() {
    var container = $('#filterTags');
    container.empty();
    var labels = { estado: 'Estado', tipo: 'Tipo', localizacao: 'Localização' };
    var hasAny = false;
    $.each(activeFilters, function(key, val) {
        if (val) {
            hasAny = true;
            container.append(
                '<span class="filter-tag">' +
                '<span>' + labels[key] + ': <strong>' + val + '</strong></span>' +
                '<button onclick="removeFilter(\'' + key + '\')" title="Remover">' +
                '<i class="bi bi-x"></i></button></span>'
            );
        }
    });
    var count = dtTable ? dtTable.rows({ filter: 'applied' }).count() : '—';
    $('#filtroAtivo').text(hasAny ? count + ' resultado(s)' : '');
}

// ── Modal de eliminação ──
$(document).on('click', '.btn-delete', function() {
    var action = $(this).data('action');
    var label  = $(this).data('label');
    $('#confirmLabel').text(label);
    $('#deleteForm').attr('action', action);
    $('#confirmOverlay').addClass('open');
});

$('#confirmOkBtn').on('click', function() {
    $('#deleteForm').submit();
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


function updatePrintLabel() {
    var parts = [];
    if (activeFilters.estado)      parts.push('Estado: ' + activeFilters.estado);
    if (activeFilters.tipo)        parts.push('Tipo: ' + activeFilters.tipo);
    if (activeFilters.localizacao) parts.push('Localização: ' + activeFilters.localizacao);
    $('#printFilterLabel').text(parts.length ? 'Filtros: ' + parts.join(' · ') : 'Sem filtros aplicados');
}
</script>
</x-app-layout>
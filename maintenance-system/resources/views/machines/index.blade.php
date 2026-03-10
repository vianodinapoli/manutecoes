<x-app-layout>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<style>
    /* ── KPI Cards ── */
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

    /* ── Table card ── */
    .table-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.04)}
    #machinesTable thead th{font-size:.65rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#94a3b8;background:#f8fafc;border-bottom:1px solid #e2e8f0;padding:10px 14px;white-space:nowrap}
    #machinesTable tbody td{font-size:.82rem;color:#334155;padding:9px 14px;vertical-align:middle;border-bottom:1px solid #f1f5f9}
    #machinesTable tbody tr:last-child td{border-bottom:none}
    #machinesTable tbody tr:hover{background:#f8fafc}

    /* ── Status badges ── */
    .status-badge{display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:6px;font-size:.68rem;font-weight:700;border:1px solid}
    .status-badge.operacional{background:#f0fdf4;color:#166534;border-color:#bbf7d0}
    .status-badge.manutencao {background:#f0f9ff;color:#0369a1;border-color:#bae6fd}
    .status-badge.avariada   {background:#fef2f2;color:#dc2626;border-color:#fecaca}
    .status-badge.desativada {background:#f8fafc;color:#475569;border-color:#e2e8f0}

    /* ── Action buttons ── */
    .action-btn{width:28px;height:28px;border-radius:7px;border:1px solid #e2e8f0;background:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:.8rem;color:#475569;text-decoration:none;transition:all .15s;cursor:pointer}
    .action-btn:hover{background:#f8fafc;border-color:#94a3b8;color:#1e293b}
    .action-btn.danger:hover{background:#fef2f2;border-color:#fecaca;color:#dc2626}

    /* ── Top buttons ── */
    .top-btn{display:inline-flex;align-items:center;gap:6px;padding:7px 16px;border-radius:8px;font-size:.78rem;font-weight:600;text-decoration:none;border:1px solid #e2e8f0;background:#fff;color:#475569;transition:all .15s;cursor:pointer}
    .top-btn:hover{background:#f8fafc;border-color:#cbd5e1;color:#334155}
    .top-btn.primary{background:#1e293b;border-color:#1e293b;color:#fff}
    .top-btn.primary:hover{background:#334155;color:#fff}
    .top-btn.success{background:#fff;border-color:#bbf7d0;color:#16a34a}
    .top-btn.success:hover{background:#f0fdf4}
    .top-btn.danger-outline{background:#fff;border-color:#fecaca;color:#dc2626}
    .top-btn.danger-outline:hover{background:#fef2f2}

    /* ── DataTables overrides ── */
    .dataTables_wrapper{padding:12px 16px 16px}
    .dataTables_wrapper .dataTables_filter{text-align:right}
    .dataTables_wrapper .dataTables_filter input{font-size:.8rem;border:1px solid #e2e8f0;border-radius:8px;padding:5px 10px;color:#334155}
    .dataTables_wrapper .dataTables_filter input:focus{outline:none;border-color:#64748b;box-shadow:0 0 0 3px rgba(100,116,139,.1)}
    .dataTables_wrapper .dataTables_length select{font-size:.8rem;border:1px solid #e2e8f0;border-radius:8px;padding:4px 8px;color:#334155}
    .dataTables_wrapper .dataTables_info,.dataTables_wrapper .dataTables_paginate{font-size:.75rem;color:#94a3b8;margin-top:12px}
    .dataTables_wrapper .paginate_button{border-radius:6px!important;font-size:.75rem!important}
    .dataTables_wrapper .row{margin:0}
    .dataTables_wrapper .dataTables_length,.dataTables_wrapper .dataTables_filter{padding:0 0 10px}
</style>

<div class="container-fluid py-4 px-4">

    {{-- CABEÇALHO --}}
    <div class="d-flex justify-content-between align-items-start mb-4">
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
            <a href="{{ route('machines.create') }}" class="top-btn primary">
                <i class="bi bi-plus-lg"></i> Adicionar
            </a>
        </div>
    </div>

    {{-- KPI CARDS --}}
    <div class="row g-3 mb-4">
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

    {{-- FLASH --}}
    @if(session('success'))
    <div class="d-flex align-items-center gap-2 mb-3 px-3 py-2 rounded"
         style="background:#f0fdf4;border:1px solid #bbf7d0;font-size:.8rem;color:#166534;">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
    @endif

    {{-- TABELA --}}
    @if($machines->isEmpty())
    <div class="table-card p-5 text-center" style="color:#94a3b8;">
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
                        <th>Ações</th>
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
                    <tr>
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
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('machines.show', $machine->id) }}"
                                   class="action-btn" title="Ver detalhes">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('machines.edit', $machine->id) }}"
                                   class="action-btn" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('machines.destroy', $machine->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Eliminar a máquina {{ $machine->numero_interno }}? Esta acção não pode ser desfeita.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="action-btn danger" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
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

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function () {
    var table = $('#machinesTable').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json' },
        pageLength: 10,
        order: [[0, 'asc']],
        columnDefs: [{ orderable: false, targets: -1 }]
    });
    table.columns.adjust();
    $(window).on('resize', function () { table.columns.adjust(); });
});
</script>
</x-app-layout>
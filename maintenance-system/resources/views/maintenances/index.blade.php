<x-app-layout>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    .filter-panel{background:#fff;border-radius:14px;border:1px solid #e9ecef;padding:20px 24px;margin-bottom:24px;box-shadow:0 2px 8px rgba(0,0,0,.04)}
    .filter-title{font-size:.72rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#adb5bd;margin-bottom:14px;display:flex;align-items:center;gap:8px}
    .filter-group{display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end}
    .filter-item{display:flex;flex-direction:column;gap:5px}
    .filter-item label{font-size:.7rem;font-weight:600;letter-spacing:.8px;text-transform:uppercase;color:#6c757d}
    .filter-item input,.filter-item select{border:1px solid #dee2e6;border-radius:8px;padding:7px 12px;font-size:.82rem;color:#343a40;background:#f8f9fa;outline:none;transition:border-color .2s,box-shadow .2s;min-width:150px}
    .filter-item input:focus,.filter-item select:focus{border-color:#0d6efd;box-shadow:0 0 0 3px rgba(13,110,253,.1);background:#fff}
    .btn-filter{padding:8px 18px;border-radius:8px;font-size:.8rem;font-weight:600;cursor:pointer;border:none;display:inline-flex;align-items:center;gap:6px;transition:all .2s}
    .btn-filter-apply{background:#0d6efd;color:#fff}.btn-filter-apply:hover{background:#0b5ed7}
    .btn-filter-clear{background:#f1f3f5;color:#495057;border:1px solid #dee2e6}.btn-filter-clear:hover{background:#e9ecef}
    .table-card{background:#fff;border-radius:14px;border:1px solid #e9ecef;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.04)}
    #maintenanceTable thead tr{background:#f8f9fa}
    #maintenanceTable thead th{font-size:.67rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#868e96;border-bottom:1px solid #e9ecef;padding:8px 12px;white-space:nowrap}
    #maintenanceTable tbody td{padding:9px 12px;vertical-align:middle;border-bottom:1px solid #f1f3f5;font-size:.82rem}
    #maintenanceTable tbody tr:hover{background:#f8f9ff}
    #maintenanceTable tbody tr:last-child td{border-bottom:none}
    .kpi-card{background:#fff;border-radius:14px;border:1px solid #e9ecef;padding:16px 20px;box-shadow:0 2px 8px rgba(0,0,0,.04);transition:transform .15s,box-shadow .15s;height:100%;position:relative;overflow:hidden}
    .kpi-card:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,0,0,.08)}
    .kpi-card::before{content:'';position:absolute;left:0;top:0;bottom:0;width:4px;border-radius:14px 0 0 14px}
    .kpi-card.dark::before{background:#212529}.kpi-card.yellow::before{background:#ffc107}
    .kpi-card.teal::before{background:#0dcaf0}.kpi-card.green::before{background:#198754}
    .kpi-label{font-size:.65rem;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:#adb5bd;margin-bottom:6px}
    .kpi-value{font-size:1.7rem;font-weight:800;line-height:1;margin-bottom:2px}
    .kpi-sub{font-size:.72rem;color:#adb5bd}
    .kpi-icon{position:absolute;right:16px;top:50%;transform:translateY(-50%);font-size:2rem;opacity:.1}
    .status-badge{display:inline-flex;align-items:center;gap:5px;padding:3px 9px;border-radius:20px;font-size:.68rem;font-weight:600;white-space:nowrap}
    .action-btn{width:28px;height:28px;border-radius:7px;display:inline-flex;align-items:center;justify-content:center;font-size:.72rem;border:1px solid;transition:all .15s;text-decoration:none;cursor:pointer;background:transparent}
    .action-btn:hover{opacity:.75}
    div.dataTables_wrapper div.dataTables_filter input{border-radius:8px;border:1px solid #dee2e6;padding:6px 12px;font-size:.82rem}
    div.dataTables_wrapper div.dataTables_length select{border-radius:8px;border:1px solid #dee2e6;padding:4px 8px;font-size:.82rem}
    #maintenanceTable tbody tr.odd td.dataTables_empty,
    #maintenanceTable tbody tr.even td.dataTables_empty { visibility: hidden; }

    /* ── Toast de sucesso ── */
    .toast-success {
        position: fixed;
        top: 24px;
        right: 24px;
        z-index: 99999;
        background: #fff;
        border-radius: 12px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 8px 32px rgba(0,0,0,.12);
        border-left: 4px solid #16a34a;
        min-width: 300px;
        transform: translateX(120%);
        transition: transform 0.35s cubic-bezier(.34,1.56,.64,1);
    }
    .toast-success.show { transform: translateX(0); }
    .toast-icon {
        width: 36px; height: 36px;
        background: #f0fdf4;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: #16a34a; font-size: 1rem; flex-shrink: 0;
    }
    .toast-text { flex: 1; }
    .toast-title { font-size: .82rem; font-weight: 700; color: #1e293b; margin-bottom: 2px; }
    .toast-sub   { font-size: .74rem; color: #94a3b8; }
    .toast-close { background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 1rem; padding: 0; line-height: 1; }
    .toast-close:hover { color: #475569; }

    /* ── Modal de confirmação ── */
    .confirm-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;display:flex;align-items:center;justify-content:center;opacity:0;visibility:hidden;transition:all .25s}
    .confirm-overlay.show{opacity:1;visibility:visible}
    .confirm-box{background:#fff;border-radius:20px;padding:0;max-width:400px;width:90%;box-shadow:0 24px 64px rgba(0,0,0,.18);transform:scale(.93) translateY(10px);transition:transform .25s cubic-bezier(.34,1.56,.64,1);overflow:hidden}
    .confirm-overlay.show .confirm-box{transform:scale(1) translateY(0)}
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
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-tools text-primary me-2"></i>Manutenções Ativas</h4>
            <p class="text-muted small mb-0">Pendentes e em andamento — rastreio em tempo real</p>
        </div>
        <a href="{{ route('machines.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-gear me-1"></i> Ver Equipamentos
        </a>
    </div>

    {{-- KPI CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="kpi-card dark">
                <div class="kpi-label">Total Ativas</div>
                <div class="kpi-value">{{ \App\Models\Maintenance::whereIn('status', ['Pendente', 'Em_manutencao'])->count() }}</div>
                <div class="kpi-sub">pendente + em curso</div>
                <i class="bi bi-tools kpi-icon"></i>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card yellow">
                <div class="kpi-label">Pendentes</div>
                <div class="kpi-value" style="color:#ffc107;">{{ \App\Models\Maintenance::where('status', 'pendente')->count() }}</div>
                <div class="kpi-sub">aguardam intervenção</div>
                <i class="bi bi-clock-history kpi-icon" style="color:#ffc107;"></i>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card teal">
                <div class="kpi-label">Em Andamento</div>
                <div class="kpi-value" style="color:#0dcaf0;">{{ \App\Models\Maintenance::where('status', 'em_manutencao')->count() }}</div>
                <div class="kpi-sub">em curso agora</div>
                <i class="bi bi-gear-wide-connected kpi-icon" style="color:#0dcaf0;"></i>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card green">
                <div class="kpi-label">Concluídas</div>
                <div class="kpi-value" style="color:#198754;">{{ \App\Models\Maintenance::where('status', 'concluida')->count() }}</div>
                <div class="kpi-sub">total histórico</div>
                <i class="bi bi-check2-all kpi-icon" style="color:#198754;"></i>
            </div>
        </div>
    </div>

    {{-- ALERTA VAZIO --}}
    @if($maintenances->isEmpty())
    <div class="alert border-0 mb-4 py-2"
         style="border-radius:10px;background:#e8f4fd;color:#1a56db;border-left:4px solid #1a56db !important;">
        <i class="bi bi-info-circle me-2"></i>Não há registos de manutenção activos no sistema.
    </div>
    @endif

    {{-- FILTROS --}}
    <div class="filter-panel">
        <div class="filter-title"><i class="bi bi-funnel-fill"></i> Filtro por Período e Estado</div>
        <div class="filter-group">
            <div class="filter-item">
                <label>Data Inicial</label>
                <input type="date" id="min-date">
            </div>
            <div class="filter-item">
                <label>Data Final</label>
                <input type="date" id="max-date">
            </div>
            <div class="filter-item">
                <label>Estado</label>
                <select id="filterStatus">
                    <option value="">Todos</option>
                    <option value="pendente">Pendente</option>
                    <option value="em_manutencao">Em Manutenção</option>
                </select>
            </div>
            <div class="d-flex gap-2 align-items-end">
                <button class="btn-filter btn-filter-apply" onclick="applyFilters()">
                    <i class="bi bi-search"></i> Filtrar
                </button>
                <button class="btn-filter btn-filter-clear" onclick="clearFilters()">
                    <i class="bi bi-x-lg"></i> Limpar
                </button>
            </div>
        </div>
    </div>

    {{-- TABELA --}}
    <div class="table-card">
        <div class="p-3">
            <table class="table table-hover align-middle mb-0" id="maintenanceTable" style="width:100%">
                <thead>
                    <tr>
                        <th># ID</th>
                        <th>Nº INTERNO</th>
                        <th class="text-center">ESTADO</th>
                        <th>AVARIA REPORTADA</th>
                        <th>DATA ENTRADA</th>
                        <th class="text-center">AÇÕES</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($maintenances as $maintenance)
                @php
                    $statusLower   = strtolower($maintenance->status);
                    $statusDisplay = ucfirst(str_replace('_', ' ', $statusLower));
                    [$statusClass, $statusIcon] = match($statusLower) {
                        'pendente'      => ['bg-warning bg-opacity-10 text-warning', 'clock-history'],
                        'em_manutencao' => ['bg-info bg-opacity-10 text-info', 'gear-wide-connected'],
                        default         => ['bg-secondary bg-opacity-10 text-secondary', 'dash-circle'],
                    };
                    $dataEntrada = optional($maintenance->data_entrada)->format('Y-m-d') ?? '';
                    $dataDisplay = optional($maintenance->data_entrada)->format('d/m/Y') ?? 'N/A';
                @endphp
                <tr data-data="{{ $dataEntrada }}" data-status="{{ $statusLower }}">
                    <td>
                        <a href="{{ route('maintenances.show', $maintenance->id) }}"
                           class="fw-bold text-primary text-decoration-none" style="font-size:.8rem;">
                            #{{ $maintenance->id }}
                        </a>
                    </td>
                    <td>
                        @if($maintenance->machine)
                        <a href="{{ route('machines.show', $maintenance->machine_id) }}"
                           class="fw-semibold text-dark text-decoration-none" style="font-size:.78rem;">
                            {{ $maintenance->machine->numero_interno }}
                        </a>
                        @else
                        <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="status-badge {{ $statusClass }}">
                            <i class="bi bi-{{ $statusIcon }}"></i> {{ $statusDisplay }}
                        </span>
                    </td>
                    <td>
                        <span style="font-size:.78rem;">{{ Str::limit($maintenance->failure_description, 55) }}</span>
                    </td>
                    <td>
                        <span style="font-size:.78rem;">{{ $dataDisplay }}</span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('maintenances.show', $maintenance->id) }}"
                               class="action-btn text-info border-info border-opacity-25" title="Detalhes">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('maintenances.edit', $maintenance->id) }}"
                               class="action-btn text-warning border-warning border-opacity-25" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button"
                                    class="action-btn text-danger border-danger border-opacity-25"
                                    title="Apagar"
                                    onclick="confirmDelete('{{ route('maintenances.destroy', $maintenance->id) }}', '#{{ $maintenance->id }}')">
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
</div>

{{-- TOAST DE SUCESSO --}}
<div class="toast-success" id="toastSuccess">
    <div class="toast-icon"><i class="bi bi-check-lg"></i></div>
    <div class="toast-text">
        <div class="toast-title">Manutenção eliminada</div>
        <div class="toast-sub">O registo foi removido com sucesso.</div>
    </div>
    <button class="toast-close" onclick="closeToast()"><i class="bi bi-x-lg"></i></button>
</div>

{{-- MODAL DE CONFIRMAÇÃO --}}
<div class="confirm-overlay" id="deleteModal">
    <div class="confirm-box">
        <div class="confirm-header">
            <div class="confirm-icon"><i class="bi bi-trash3"></i></div>
            <div class="confirm-title">Apagar manutenção?</div>
            <div class="confirm-sub">Tens a certeza que queres eliminar o registo <strong id="deleteTarget"></strong>?</div>
        </div>
        <div class="confirm-body">
            <div class="confirm-warning">
                <i class="bi bi-exclamation-triangle-fill"></i>
                Esta acção é irreversível e não pode ser desfeita.
            </div>
            <div class="confirm-actions">
                <button class="btn-cancel-confirm" onclick="closeModal()">
                    <i class="bi bi-x-lg"></i> Cancelar
                </button>
                <button class="btn-delete-confirm" id="btnConfirmDelete">
                    <i class="bi bi-trash3"></i> Apagar
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

// ── Toast ──
function showToast() {
    const toast = document.getElementById('toastSuccess');
    toast.classList.add('show');
    setTimeout(closeToast, 4000);
}

function closeToast() {
    document.getElementById('toastSuccess').classList.remove('show');
}

// ── Modal ──
function confirmDelete(url, label) {
    window._deleteUrl = url;
    document.getElementById('deleteTarget').textContent = label;
    document.getElementById('deleteModal').classList.add('show');
}

function closeModal() {
    const btn = document.getElementById('btnConfirmDelete');
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-trash3"></i> Apagar';
    document.getElementById('deleteModal').classList.remove('show');
}

document.getElementById('btnConfirmDelete').addEventListener('click', function () {
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> A apagar...';

    fetch(window._deleteUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-HTTP-Method-Override': 'DELETE'
        },
        body: JSON.stringify({ _method: 'DELETE' })
    })
    .then(res => res.json())
    .then(data => {
        closeModal();
        if (data.success) {
            showToast();
            // Aguarda o toast aparecer e depois recarrega
            setTimeout(() => { window.location.href = data.redirect_url; }, 1800);
        } else {
            alert('Erro ao apagar. Tenta novamente.');
        }
    })
    .catch(() => {
        closeModal();
        alert('Erro de ligação. Tenta novamente.');
    });
});

document.getElementById('deleteModal').addEventListener('click', function (e) {
    if (e.target === this) closeModal();
});

// ── DataTables ──
$.fn.dataTable.ext.search.push(function(settings, data) {
    if (settings.nTable.id !== 'maintenanceTable') return true;
    const min = $('#min-date').val();
    const max = $('#max-date').val();
    if (!min && !max) return true;
    const raw = data[4];
    if (!raw || raw === 'N/A') return false;
    const parts = raw.split('/');
    if (parts.length !== 3) return false;
    const rowDate = new Date(parts[2], parts[1] - 1, parts[0]);
    const minDate = min ? new Date(min) : null;
    const maxDate = max ? new Date(max) : null;
    if (minDate && rowDate < minDate) return false;
    if (maxDate && rowDate > maxDate) return false;
    return true;
});

$(document).ready(function() {
    dtTable = $('#maintenanceTable').DataTable({
        destroy: true,
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json' },
        order: [[4, 'desc']],
        columnDefs: [{ orderable: false, targets: [5] }],
        pageLength: 15,
    });

    $('#min-date, #max-date').on('change', function() { dtTable.draw(); });
});

function applyFilters() {
    const st = $('#filterStatus').val();
    $('#maintenanceTable tbody tr').each(function() {
        $(this).toggle(!st || $(this).data('status') === st);
    });
    dtTable.draw();
}

function clearFilters() {
    $('#min-date, #max-date').val('');
    $('#filterStatus').val('');
    $('#maintenanceTable tbody tr').show();
    dtTable.draw();
}
</script>

</x-app-layout>
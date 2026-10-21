<x-app-layout>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    .page-title{font-size:1.05rem;font-weight:700;color:#1e293b;margin:0}
    .page-sub{font-size:.72rem;color:#94a3b8;margin:2px 0 0}
    .table-card{background:#fff;border:1px solid #e9ecef;border-radius:10px;overflow:hidden}
    #aTable thead th{font-size:.6rem;font-weight:700;letter-spacing:.9px;text-transform:uppercase;
                     color:#868e96;border-bottom:1px solid #e9ecef;padding:8px 10px;white-space:nowrap}
    #aTable tbody td{padding:7px 10px;vertical-align:middle;font-size:.78rem}
    .meta-chip{display:inline-block;background:#f1f3f5;color:#475569;border-radius:4px;
               font-size:.6rem;padding:1px 6px;margin:1px 2px 1px 0}
</style>

<div class="container-fluid py-3 px-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <div class="page-title"><i class="bi bi-archive me-2" style="color:#64748b;"></i>Arquivo de Operações</div>
            <div class="page-sub">Operações de descarga concluídas</div>
        </div>
        <a href="{{ route('operations.index') }}" class="btn btn-outline-secondary btn-sm fw-bold" style="border-radius:7px;font-size:.75rem;">
            <i class="bi bi-arrow-left me-1"></i>Operações activas
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success py-2" style="font-size:.78rem;border-radius:8px;">{{ session('success') }}</div>
    @endif

    <div class="table-card">
        <div class="p-2 pt-3">
            <table class="table table-hover align-middle mb-0" id="aTable" style="width:100%">
                <thead>
                    <tr>
                        <th>NAVIO</th>
                        <th>EMPRESA</th>
                        <th>BL</th>
                        <th>FACTURA</th>
                        <th class="text-end">ESPERADO (t)</th>
                        <th class="text-end">RECEBIDO (t)</th>
                        <th class="text-center">CAMIÕES</th>
                        <th>METADATA</th>
                        <th>ARQUIVADO</th>
                        <th class="text-center" style="width:90px">AÇÕES</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($operations as $op)
                    @php $rec = round(((float) $op->discharges_sum_peso_liquido) / 1000, 3); @endphp
                    <tr>
                        <td class="fw-bold">{{ $op->navio }}</td>
                        <td>{{ $op->empresa }}</td>
                        <td>{{ $op->bl ?: '—' }}</td>
                        <td>{{ $op->factura ?: '—' }}</td>
                        <td class="text-end">{{ number_format($op->quantidade_esperada, 1, ',', '.') }}</td>
                        <td class="text-end fw-bold" style="color:{{ $rec >= (float) $op->quantidade_esperada ? '#198754' : '#dc3545' }};">
                            {{ number_format($rec, 1, ',', '.') }}
                        </td>
                        <td class="text-center">{{ $op->discharges_count }}</td>
                        <td>
                            @foreach(($op->metadata ?? []) as $k => $v)
                                <span class="meta-chip"><b>{{ $k }}:</b> {{ $v }}</span>
                            @endforeach
                        </td>
                        <td data-order="{{ $op->archived_at?->timestamp }}">
                            {{ $op->archived_at?->format('d/m/Y') }}
                            @if($op->archivedBy)<div style="font-size:.62rem;color:#94a3b8;">{{ $op->archivedBy->name }}</div>@endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('operations.show', $op) }}" class="btn btn-outline-primary btn-sm py-0 px-2" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form action="{{ route('operations.restore', $op) }}" method="POST" class="m-0">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-outline-warning btn-sm py-0 px-2" title="Reactivar"
                                            onclick="return confirm('Reactivar esta operação?')">
                                        <i class="bi bi-arrow-counterclockwise"></i>
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
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(function () {
        $('#aTable').DataTable({
            language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json' },
            order: [[8, 'desc']],
            columnDefs: [{ orderable: false, targets: [7, 9] }],
            pageLength: 15,
        });
    });
</script>

</x-app-layout>

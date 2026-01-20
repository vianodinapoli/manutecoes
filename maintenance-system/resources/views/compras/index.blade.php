<x-app-layout>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        .container { max-width: 100%; }
        .table thead th { font-weight: 700; cursor: pointer; }
        .badge-status { min-width: 120px; border-radius: 20px; padding: 5px 10px; }
        .btn-action { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; }
    </style>


  <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>🛒 Gestão de Compras de Materiais</h2>
        
        </div>

        

<div class="row g-2 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm border-0" style="background: #f8f9fa; border-left: 4px solid #ffc107 !important;">
            <div class="card-body py-2 px-3">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-warning small fw-bold text-uppercase">Pendentes</span>
                    <i class="bi bi-hourglass-split text-warning"></i>
                </div>
                <h4 class="fw-bold mb-0 text-dark">
                    {{ \App\Models\MaterialPurchase::where('status', 'Pendente')->count() }}
                </h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0" style="background: #f8f9fa; border-left: 4px solid #0dcaf0 !important;">
            <div class="card-body py-2 px-3">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-info small fw-bold text-uppercase">Em Processo</span>
                    <i class="bi bi-cart-dash text-info"></i>
                </div>
                <h4 class="fw-bold mb-0 text-info">
                    {{ \App\Models\MaterialPurchase::where('status', 'Em processo')->count() }}
                </h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0" style="background: #f8f9fa; border-left: 4px solid #198754 !important;">
            <div class="card-body py-2 px-3">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-success small fw-bold text-uppercase">Aprovados</span>
                    <i class="bi bi-check-all text-success"></i>
                </div>
                <h4 class="fw-bold mb-0 text-success">
                    {{ \App\Models\MaterialPurchase::where('status', 'Aprovado')->count() }}
                </h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0" style="background: #f8f9fa; border-left: 4px solid #dc3545 !important;">
            <div class="card-body py-2 px-3">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-danger small fw-bold text-uppercase">Rejeitados</span>
                    <i class="bi bi-x-circle text-danger"></i>
                </div>
                <h4 class="fw-bold mb-0 text-danger">
                    {{ \App\Models\MaterialPurchase::where('status', 'Rejeitado')->count() }}
                </h4>
            </div>
        </div>
    </div>
</div>
    <div class="container mt-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            
            <a href="{{ route('compras.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-lg"></i> Nova Solicitação
            </a>
        </div>
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($compras->isEmpty())
             <div class="alert alert-info shadow-sm">
                <i class="bi bi-info-circle me-2"></i> Não há registos de compras no sistema.
            </div>
        @else

        <div class="card p-3 mb-4 shadow-sm border-0 bg-light">
            <h6 class="card-title mb-3 text-muted"><i class="bi bi-filter-left"></i> Filtrar por Período</h6>
            <div class="row g-3">
                <div class="col-md-5">
                    <label for="min-date" class="form-label small">Data Inicial</label>
                    <input type="date" id="min-date" class="form-control form-control-sm">
                </div>
                <div class="col-md-5">
                    <label for="max-date" class="form-label small">Data Final</label>
                    <input type="date" id="max-date" class="form-control form-control-sm">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button id="clear-filters" class="btn btn-outline-secondary btn-sm w-100">Limpar</button>
                </div>
            </div>
        </div>

        <div class="table-responsive card shadow-sm p-3">
            <table id="comprasTable" class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Material</th>
                        <th>Qtd</th>
                        <th>Solicitante</th>
                        <th>Status</th>
                        <th>Data Pedido</th>
                        <th>Info Extra</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($compras as $compra)
                        @php
                            $badge_class = match($compra->status) {
                                'Pendente' => 'bg-warning text-dark',
                                'Em processo' => 'bg-info text-white',
                                'Aprovado' => 'bg-success text-white',
                                'Rejeitado' => 'bg-danger text-white',
                                default => 'bg-secondary text-white',
                            };
                        @endphp
                        
                        <tr>
                            <td><span class="text-muted">#</span>{{ $compra->id }}</td>
                            <td><strong>{{ $compra->item_name }}</strong></td>
                            <td>{{ $compra->quantity }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-person me-1"></i> {{ $compra->user->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <form action="{{ route('compras.status', $compra->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="form-select form-select-sm badge-status {{ $badge_class }}" style="border:none; cursor:pointer;">
                                        <option value="Pendente" {{ $compra->status == 'Pendente' ? 'selected' : '' }}>Pendente</option>
                                        <option value="Em processo" {{ $compra->status == 'Em processo' ? 'selected' : '' }}>Em processo</option>
                                        <option value="Aprovado" {{ $compra->status == 'Aprovado' ? 'selected' : '' }}>Aprovado</option>
                                        <option value="Rejeitado" {{ $compra->status == 'Rejeitado' ? 'selected' : '' }}>Rejeitado</option>
                                    </select>
                                </form>
                            </td>
                            <td>{{ $compra->created_at->format('d/m/Y') }}</td>
                            <td>
                                @if($compra->metadata)
                                    <div class="small">
                                        <span class="text-primary">{{ $compra->metadata['placa_veiculo'] ?? '' }}</span>
                                        <span class="text-muted ms-1">{{ isset($compra->metadata['urgencia']) ? '| ' . $compra->metadata['urgencia'] : '' }}</span>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('compras.show', $compra->id) }}" class="btn btn-sm btn-outline-info btn-action" title="Visualizar">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    <a href="{{ route('compras.edit', $compra->id) }}" class="btn btn-sm btn-outline-warning btn-action" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <form action="{{ route('compras.destroy', $compra->id) }}" method="POST" onsubmit="return confirm('ATENÇÃO: Deseja realmente eliminar esta solicitação?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger btn-action" title="Eliminar">
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
        @endif
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/dt-2.0.8/datatables.min.js"></script>

    <script>
        $(document).ready(function() {
            var table = $('#comprasTable').DataTable({
                language: { url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/pt-BR.json' },
                order: [[0, 'desc']],
                pageLength: 10,
                columnDefs: [
                    { orderable: false, targets: [4, 7] } // Desativa ordenação no Status e Ações
                ]
            });

            // Filtro de data
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                var min = $('#min-date').val();
                var max = $('#max-date').val();
                var dateArr = data[5].split('/'); 
                if(dateArr.length < 3) return true;
                
                var valDate = dateArr[2] + dateArr[1] + dateArr[0];
                var minDate = min ? min.replace(/-/g, '') : null;
                var maxDate = max ? max.replace(/-/g, '') : null;

                if ((!minDate && !maxDate) || 
                    (minDate && !maxDate && valDate >= minDate) || 
                    (!minDate && maxDate && valDate <= maxDate) || 
                    (minDate && maxDate && valDate >= minDate && valDate <= maxDate)) {
                    return true;
                }
                return false;
            });

            $('#min-date, #max-date').on('change', function() { table.draw(); });
            $('#clear-filters').on('click', function() {
                $('#min-date, #max-date').val('');
                table.draw();
            });
        });
    </script>
</x-app-layout>
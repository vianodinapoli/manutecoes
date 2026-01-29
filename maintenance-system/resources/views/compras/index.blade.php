<x-app-layout>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        .container { max-width: 100%; }
        .table thead th { font-weight: 700; cursor: pointer; }
        .badge-status { min-width: 135px; border-radius: 20px; padding: 5px 10px; border: none !important; }
        .btn-action { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; }
        .urgencia-badge { font-size: 0.8rem; padding: 3px 8px; border-radius: 5px; }
    </style>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>🛒 Gestão de Compras de Materiais</h2>
            <a href="{{ route('compras.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-lg"></i> Nova Solicitação
            </a>
        </div>

        {{-- Cards de Resumo --}}
        <div class="row g-2 mb-4 row-cols-1 row-cols-md-5">
            @php
                $resumo = [
                    ['Pendente', 'warning', 'hourglass-split'],
                    ['Em processo', 'info', 'cart-dash'],
                    ['Aprovado', 'success', 'check-all'],
                    ['Rejeitado', 'danger', 'x-circle'],
                    ['Finalizado', 'dark', 'check-circle-fill']
                ];
            @endphp
            @foreach($resumo as $item)
            <div class="col">
                <div class="card shadow-sm border-0 h-100" style="background: #f8f9fa; border-left: 4px solid {{ $item[1] == 'warning' ? '#ffc107' : ($item[1] == 'info' ? '#0dcaf0' : ($item[1] == 'success' ? '#198754' : ($item[1] == 'danger' ? '#dc3545' : '#212529'))) }} !important;">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex justify-content-between align-items-center text-{{ $item[1] }}">
                            <span class="small fw-bold text-uppercase">{{ $item[0] }}</span>
                            <i class="bi bi-{{ $item[2] }}"></i>
                        </div>
                        <h4 class="fw-bold mb-0 text-dark">
                            {{ \App\Models\MaterialPurchase::where('status', $item[0])->count() }}
                        </h4>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Filtro de Período --}}
        <div class="card p-3 mb-4 shadow-sm border-0 bg-light">
            <h6 class="card-title mb-3 text-muted small"><i class="bi bi-filter-left"></i> Filtrar por Período</h6>
            <div class="row g-3">
                <div class="col-md-5">
                    <input type="date" id="min-date" class="form-control form-control-sm">
                </div>
                <div class="col-md-5">
                    <input type="date" id="max-date" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <button id="clear-filters" class="btn btn-outline-secondary btn-sm w-100">Limpar</button>
                </div>
            </div>
        </div>

        {{-- Tabela Principal --}}
        <div class="table-responsive card shadow-sm p-3">
            <table id="comprasTable" class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Material</th>
                        <th>Urgência</th>
                        <th>Status</th>
                        <th>Doc/Proforma</th>
                        <th>Solicitante</th>
                        <th>Data Pedido</th>
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
                                'Finalizado' => 'bg-dark text-white',
                                default => 'bg-secondary text-white',
                            };

                            // Lógica de Urgência vinda do metadata
                            $urgencia = $compra->metadata['urgencia'] ?? 'Normal';
                            $urgencia_style = match($urgencia) {
                                'Alta', 'Urgente' => 'badge bg-danger',
                                'Média' => 'badge bg-warning text-dark',
                                default => 'badge bg-light text-dark border',
                            };
                        @endphp
                        <tr>
                            <td>#{{ $compra->id }}</td>
                            <td><strong>{{ $compra->item_name }}</strong></td>
                            
                        @php
    // Busca a urgência no metadata (padrão 'Normal' se estiver vazio)
    $urgencia = $compra->metadata['urgencia'] ?? 'Normal';

    // Define a cor da badge baseada no tipo
    $urgencia_class = match($urgencia) {
        'Crítica' => 'bg-danger text-white',      // Vermelho para Crítica
        'Alta'    => 'bg-warning text-dark',     // Amarelo/Laranja para Alta
        'Normal'  => 'bg-success text-white',    // Verde para Normal
        default   => 'bg-secondary text-white',  // Cinza para outros
    };
@endphp

<td>
    <span class="badge {{ $urgencia_class }} shadow-sm px-3 py-2" style="font-size: 0.75rem;">
        <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $urgencia }}
    </span>
</td>

                            {{-- Coluna Status com Permissões --}}
                            <td>
                                <form action="{{ route('compras.status', $compra->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="form-select form-select-sm badge-status {{ $badge_class }}">
                                        @if(auth()->user()->hasRole('super-admin'))
                                            <option value="Pendente" {{ $compra->status == 'Pendente' ? 'selected' : '' }}>Pendente</option>
                                            <option value="Em processo" {{ $compra->status == 'Em processo' ? 'selected' : '' }}>Em processo</option>
                                            <option value="Aprovado" {{ $compra->status == 'Aprovado' ? 'selected' : '' }}>Aprovado</option>
                                            <option value="Rejeitado" {{ $compra->status == 'Rejeitado' ? 'selected' : '' }}>Rejeitado</option>
                                            <option value="Finalizado" {{ $compra->status == 'Finalizado' ? 'selected' : '' }}>Finalizado</option>
                                        @else
                                            <option value="{{ $compra->status }}" selected readonly>{{ $compra->status }}</option>
                                            <option value="Em processo">Em processo</option>
                                            <option value="Finalizado">Finalizado</option>
                                        @endif
                                    </select>
                                </form>
                            </td>

                            {{-- Coluna Proforma --}}
                            <td class="text-center">
                                @if($compra->quotation_file)
                                    <a href="{{ asset('storage/' . $compra->quotation_file) }}" target="_blank" class="btn btn-sm btn-outline-primary shadow-sm">
                                        <i class="bi bi-file-earmark-pdf"></i> Ver Doc
                                    </a>
                                @else
                                    <span class="text-muted small italic">Nenhum</span>
                                @endif
                            </td>

                            <td>{{ $compra->user->name ?? 'N/A' }}</td>
                            <td>{{ $compra->created_at->format('d/m/Y') }}</td>
                            
                           <td class="text-center">
    <div class="d-flex justify-content-center gap-2">
        {{-- Botão Visualizar --}}
        <a href="{{ route('compras.show', $compra->id) }}" class="btn btn-sm btn-outline-info btn-action" title="Visualizar">
            <i class="bi bi-eye"></i>
        </a>

        {{-- Botão Editar --}}
        <a href="{{ route('compras.edit', $compra->id) }}" class="btn btn-sm btn-outline-warning btn-action" title="Editar">
            <i class="bi bi-pencil"></i>
        </a>

        {{-- Botão Apagar (Apenas para Super-Admin) --}}
        @if(auth()->user()->hasRole('super-admin'))
            <form action="{{ route('compras.destroy', $compra->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja eliminar esta solicitação?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger btn-action" title="Eliminar">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
        @endif
    </div>
</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/dt-2.0.8/datatables.min.js"></script>

    <script>
        $(document).ready(function() {
            var table = $('#comprasTable').DataTable({
                language: { url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/pt-BR.json' },
                order: [[0, 'desc']],
                pageLength: 10,
                columnDefs: [
                    { orderable: false, targets: [3, 4, 7] } // Desativa ordenação para Status, Doc e Ações
                ]
            });

            // Filtro de Data Personalizado
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                var min = $('#min-date').val();
                var max = $('#max-date').val();
                var dateStr = data[6]; // Índice 6 agora aponta para "Data Pedido"
                
                if(!dateStr) return true;
                
                var dateArr = dateStr.split('/');
                var valDate = dateArr[2] + dateArr[1] + dateArr[0]; // Formato YYYYMMDD
                
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
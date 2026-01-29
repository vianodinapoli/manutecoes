<x-app-layout>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        .container { max-width: 100%; }
        .table thead th { font-weight: 700; text-transform: uppercase; font-size: 0.75rem; }
        /* Estilo para o Select de Status */
        .badge-status { min-width: 140px; border-radius: 10px; padding: 4px 12px; font-weight: 600; font-size: 0.8rem; cursor: pointer; transition: all 0.3s; }
        .badge-status:hover { opacity: 0.85; transform: scale(1.02); }
        /* Botões de Ação Redondos */
        .btn-action { width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; border-radius: 10%; transition: 0.2s; }
        .btn-action:hover { transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    </style>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold"><i class="bi bi-cart4 text-primary me-2"></i>Gestão de Compras</h2>
            <a href="{{ route('compras.create') }}" class="btn btn-primary shadow-sm px-4">
                <i class="bi bi-plus-lg me-1"></i> Nova Solicitação
            </a>
        </div>

        {{-- Cards de Resumo --}}
        <div class="row g-3 mb-4 row-cols-1 row-cols-md-5">
            @php
                $resumo = [
                    ['Pendente', 'warning', 'hourglass-split', '#ffc107'],
                    ['Em processo', 'info', 'cart-dash', '#0dcaf0'],
                    ['Aprovado', 'success', 'check-all', '#198754'],
                    ['Rejeitado', 'danger', 'x-circle', '#dc3545'],
                    ['Finalizado', 'dark', 'check-circle-fill', '#212529']
                ];
            @endphp
            @foreach($resumo as $item)
            <div class="col">
                <div class="card shadow-sm border-0 h-100" style="border-left: 4px solid {{ $item[3] }} !important;">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex justify-content-between align-items-center text-{{ $item[1] }}">
                            <span class="small fw-bold text-uppercase">{{ $item[0] }}</span>
                            <i class="bi bi-{{ $item[2] }} fs-5"></i>
                        </div>
                        <h3 class="fw-bold mb-0 text-dark">
                            {{ \App\Models\MaterialPurchase::where('status', $item[0])->count() }}
                        </h3>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Filtro de Período --}}
        <div class="card p-3 mb-4 shadow-sm border-0 bg-white">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="small fw-bold text-muted mb-1"><i class="bi bi-calendar-event me-1"></i>De:</label>
                    <input type="date" id="min-date" class="form-control form-control-sm shadow-none">
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold text-muted mb-1"><i class="bi bi-calendar-check me-1"></i>Até:</label>
                    <input type="date" id="max-date" class="form-control form-control-sm shadow-none">
                </div>
                <div class="col-md-4">
                    <button id="clear-filters" class="btn btn-light border btn-sm w-100 fw-bold text-secondary">Limpar Filtros</button>
                </div>
            </div>
        </div>

        {{-- Tabela Principal --}}
        <div class="table-responsive card shadow-sm p-3 border-0">
            <table id="comprasTable" class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>ID</th>
                        <th>Material</th>
                        <th class="text-center">Urgência</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Documento</th>
                        <th>Solicitante</th>
                        <th>Data Pedido</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($compras as $compra)
                        @php
                            // Cores do Status (Select)
                            $status_color = match($compra->status) {
                                'Pendente' => 'bg-warning text-dark',
                                'Em processo' => 'bg-info text-white',
                                'Aprovado' => 'bg-success text-white',
                                'Rejeitado' => 'bg-danger text-white',
                                'Finalizado' => 'bg-dark text-white',
                                default => 'bg-secondary text-white',
                            };

                            // Cores da Urgência (Pílula Moderna)
                            $urgencia = $compra->metadata['urgencia'] ?? 'Normal';
                            $urgencia_style = match($urgencia) {
                                'Crítica' => 'background-color: #fce8e6; color: #d93025; border: 1px solid #f99f97;',
                                'Alta'    => 'background-color: #fef7e0; color: #b06000; border: 1px solid #ffe082;',
                                'Normal'  => 'background-color: #e6f4ea; color: #137333; border: 1px solid #c3e6cb;',
                                default   => 'background-color: #f1f3f4; color: #5f6368; border: 1px solid #dadce0;',
                            };
                        @endphp
                        <tr>
                            <td><span class="text-muted fw-bold">#{{ $compra->id }}</span></td>
                            <td><span class="fw-semibold text-dark">{{ $compra->item_name }}</span></td>
                            
                            {{-- Coluna Urgência --}}
                            <td class="text-center">
                                <span class="badge px-3 py-2 fw-bold" style="{{ $urgencia_style }} border-radius: 20px; font-size: 0.65rem; letter-spacing: 0.5px;">
                                    <i class="bi bi-circle-fill me-1" style="font-size: 0.4rem; vertical-align: middle;"></i> 
                                    {{ strtoupper($urgencia) }}
                                </span>
                            </td>

                            {{-- Coluna Status --}}
                            <td class="text-center">
                                <form action="{{ route('compras.status', $compra->id) }}" method="POST" class="m-0">
                                    @csrf @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="form-select form-select-sm badge-status {{ $status_color }} border-0 shadow-sm">
                                        @if(auth()->user()->hasRole('super-admin'))
                                            @foreach(['Pendente', 'Em processo', 'Aprovado', 'Rejeitado', 'Finalizado'] as $st)
                                                <option value="{{ $st }}" {{ $compra->status == $st ? 'selected' : '' }}>{{ $st }}</option>
                                            @endforeach
                                        @else
                                            <option value="{{ $compra->status }}" selected>{{ $compra->status }}</option>
                                            <option value="Em processo">Em processo</option>
                                            <option value="Finalizado">Finalizado</option>
                                        @endif
                                    </select>
                                </form>
                            </td>

                            {{-- Coluna Documento --}}
                            <td class="text-center">
                                @if($compra->quotation_file)
                                    <a href="{{ asset('storage/' . $compra->quotation_file) }}" target="_blank" class="btn btn-sm btn-outline-primary pill px-3 shadow-none" style="font-size: 0.7rem;">
                                        <i class="bi bi-file-earmark-text"></i> VER DOC
                                    </a>
                                @else
                                    <span class="text-muted small">---</span>
                                @endif
                            </td>

                            <td><span class="small fw-semibold">{{ $compra->user->name ?? 'N/A' }}</span></td>
                            <td><span class="small text-muted">{{ $compra->created_at->format('d/m/Y') }}</span></td>

                            {{-- Coluna Ações --}}
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('compras.show', $compra->id) }}" class="btn btn-action btn-outline-info" title="Visualizar"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('compras.edit', $compra->id) }}" class="btn btn-action btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>

                                    @if(auth()->user()->hasRole('super-admin'))
                                        <form action="{{ route('compras.destroy', $compra->id) }}" method="POST" class="m-0">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-action btn-outline-danger" title="Eliminar" onclick="return confirm('Eliminar permanentemente?')">
                                                <i class="bi bi-trash3"></i>
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

    {{-- Scripts Datatables --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/dt-2.0.8/datatables.min.js"></script>

    <script>
        $(document).ready(function() {
            var table = $('#comprasTable').DataTable({
                language: { url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/pt-BR.json' },
                order: [[0, 'desc']],
                pageLength: 10,
                columnDefs: [{ orderable: false, targets: [3, 4, 7] }]
            });

            // Filtro de Datas
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                var min = $('#min-date').val();
                var max = $('#max-date').val();
                var dateStr = data[6]; 
                if(!dateStr) return true;
                
                var dateArr = dateStr.split('/');
                var valDate = dateArr[2] + dateArr[1] + dateArr[0];
                var minDate = min ? min.replace(/-/g, '') : null;
                var maxDate = max ? max.replace(/-/g, '') : null;

                if ((!minDate && !maxDate) || (minDate && !maxDate && valDate >= minDate) || 
                    (!minDate && maxDate && valDate <= maxDate) || (minDate && maxDate && valDate >= minDate && valDate <= maxDate)) {
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
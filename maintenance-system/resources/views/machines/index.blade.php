<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Máquinas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

    <style>
        .dataTables_wrapper {
            width: 100%;
            font-size: 12px
        }

        /* Estilo para reduzir a altura das linhas */
        #machinesTable th, 
        #machinesTable td {
            padding-top: 0.35rem; 
            padding-bottom: 0.35rem;
            vertical-align: middle; 
        }

        /* Estilo para botões de ação com ícones (agora usando bi-) */
        .col-acao .btn {
            padding: 0.25rem 0.4rem; /* Padding reduzido */
            /* line-height: 1;  */
            /* display: flex !important; */
            font-size: 1rem; 
        }
        .col-acao .d-inline {
            margin-left: 0.25rem; 
        }
    </style>

</head>
<body >
    <x-app-layout>

    <div class="container-fluid mt-5 w-100">

        <div class="row g-2 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm border-0" style="background: #f8f9fa; border-left: 4px solid #212529 !important;">
            <div class="card-body py-2 px-3">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small fw-bold text-uppercase">Total</span>
                    <i class="bi bi-cpu text-dark"></i>
                </div>
                <h4 class="fw-bold mb-0">{{ \App\Models\Machine::count() }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0" style="background: #f8f9fa; border-left: 4px solid #198754 !important;">
            <div class="card-body py-2 px-3">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-success small fw-bold text-uppercase">Operacionais</span>
                    <i class="bi bi-check-circle text-success"></i>
                </div>
                <h4 class="fw-bold mb-0 text-success">{{ \App\Models\Machine::where('status', 'Operacional')->count() }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0" style="background: #f8f9fa; border-left: 4px solid #dc3545 !important;">
            <div class="card-body py-2 px-3">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-danger small fw-bold text-uppercase">Avariadas</span>
                    <i class="bi bi-exclamation-triangle text-danger"></i>
                </div>
                <h4 class="fw-bold mb-0 text-danger">{{ \App\Models\Machine::where('status', 'Avariada')->count() }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0" style="background: #f8f9fa; border-left: 4px solid #6c757d !important;">
            <div class="card-body py-2 px-3">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-secondary small fw-bold text-uppercase">Desativadas</span>
                    <i class="bi bi-dash-circle text-secondary"></i>
                </div>
                <h4 class="fw-bold mb-0 text-secondary">{{ \App\Models\Machine::where('status', 'Desativada')->count() }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="mb-3">
    <a href="{{ route('machines.export', ['type' => 'excel']) }}" class="btn btn-success btn-sm">
        <i class="bi bi-file-earmark-spreadsheet"></i> Exportar Excel
    </a>
    <a href="{{ route('machines.export', ['type' => 'pdf']) }}" class="btn btn-danger btn-sm">
        <i class="bi bi-file-earmark-pdf"></i> Exportar PDF
    </a>
</div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5>⚙️ Lista de Equipamentos e Máquinas</h5>
            
            <div class="btn-group">
               
                <a href="{{ route('machines.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Adicionar
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if ($machines->isEmpty())
            <div class="alert alert-info">
                Ainda não há máquinas registadas. Adicione uma nova Equipamento/máquina para começar!
            </div>
        @else

            <div class="table-responsive">
                <table id="machinesTable" class="table table-striped table-hover border">
                    <thead class="table-dark">
                        <tr>
                            <th>Nº Interno</th>
                            <th>Tipo de Equipamento</th>
                            <th>Marca / Modelo</th>
                            <th>Chassi</th>
                            <th>Matrícula</th>
                            <th>Localização</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($machines as $machine)
                            <tr>
                                <td><strong>{{ $machine->numero_interno }}</strong></td>
                                <td>{{ Str::limit($machine->tipo_equipamento, 30) }}</td>
                                <td>{{Str::limit( $machine->marca, 15) }} / {{ Str::limit( $machine->modelo, 20) }}</td>
                                <td>{{ $machine->nr_chassi ?? 'N/A' }}</td>
                                <td>{{ $machine->matricula ?? 'N/A' }}</td>
                                
                                <td>{{ $machine->localizacao }}</td>

                                <td>
                                    @php
                                        $badge_class = match($machine->status) {
                                            'Operacional' => 'bg-success',
                                            'Em Manutenção' => 'bg-info text-dark',
                                            'Avariada' => 'bg-danger',
                                            'Desativada' => 'bg-secondary',
                                            default => 'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badge_class }}">{{ $machine->status }}</span>
                                </td>

                                <td class="d-flex gap-1 col-acao">
                                    {{-- Botão Ver (Detalhes) - bi-eye-fill --}}
                                    <a href="{{ route('machines.show', $machine->id) }}" 
                                       class="btn btn-info" 
                                       title="Ver Detalhes">
                                       <i class="bi bi-eye-fill"></i>
                                    </a>
                                    
                                    {{-- Botão Editar - bi-pencil-square --}}
                                    <a href="{{ route('machines.edit', $machine->id) }}" 
                                       class="btn btn-dark" 
                                       title="Editar">
                                       <i class="bi bi-pencil-square"></i>
                                    </a>

                                    {{-- Formulário Apagar - bi-trash-fill --}}
                                    <form action="{{ route('machines.destroy', $machine->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-danger"
                                                title="Apagar"
                                                onclick="return confirm('Tem certeza que deseja eliminar a máquina {{ $machine->numero_interno }}? Esta ação não pode ser desfeita.')">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        @endif
    </div>
  

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inicializa o DataTables e guarda numa variável
            var table = $('#machinesTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    // { extend: 'copy', text: 'Copiar' },
                    // { extend: 'csv', text: 'CSV' },
                    // { extend: 'excel', text: 'Excel' },
                    // { extend: 'pdf', text: 'PDF' },
                    // { extend: 'print', text: 'Imprimir' }
                ],
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json"
                },
                pageLength: 10,
                order: [[0, "asc"]]
            });
            
            // Força o ajuste das colunas para garantir 100% de largura.
            table.columns.adjust();

            // Garante que a tabela ajusta a largura em caso de redimensionamento do navegador
            $(window).on('resize', function() {
                table.columns.adjust();
            });
        });
    </script>
</x-app-layout>
</body>
</html>
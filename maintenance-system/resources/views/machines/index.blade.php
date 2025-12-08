<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Máquinas</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables + Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

</head>
<body>

    <div class="container mt-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>⚙️ Lista de Equipamentos e Máquinas</h1>
            <a href="{{ route('maintenances.index') }}" class="btn btn-dark">
                Ver Manutenções
            </a>
            <br>
            {{-- NOVO BOTÃO DE REDIRECIONAMENTO --}}
            <a href="{{ route('stock-items.index') }}" class="btn btn-dark">
                📦 Inventário de Stock
            </a>

            <a href="{{ route('machines.create') }}" class="btn btn-primary">
                ➕ Adicionar Nova Máquina
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if ($machines->isEmpty())
            <div class="alert alert-info">
                Ainda não há máquinas registadas. Adicione uma nova máquina para começar!
            </div>
        @else

            <div class="table-responsive">
                <table id="machinesTable" class="table table-striped table-hover border" style="width:100%">
                    <thead class="table-dark">
                        <tr>
                            <th>Nº Interno</th>
                            <th>Tipo de Equipamento</th>
                            <th>Marca / Modelo</th>
                            <th>Localização</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($machines as $machine)
                            <tr>
                                <td><strong>{{ $machine->numero_interno }}</strong></td>
                                <td>{{ $machine->tipo_equipamento }}</td>
                                <td>{{ $machine->marca }} / {{ $machine->modelo }}</td>
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

                                <td>
                                    <a href="{{ route('machines.show', $machine->id) }}" class="btn btn-sm btn-outline-info me-1">Ver</a>
                                    <a href="{{ route('machines.edit', $machine->id) }}" class="btn btn-sm btn-outline-warning me-1">Editar</a>

                                    <form action="{{ route('machines.destroy', $machine->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Tem certeza que deseja eliminar a máquina {{ $machine->numero_interno }}? Esta ação não pode ser desfeita.')">
                                            Apagar
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

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- DataTables Buttons -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>

    <!-- Exportações -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#machinesTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'copy', text: 'Copiar' },
                    { extend: 'csv', text: 'CSV' },
                    { extend: 'excel', text: 'Excel' },
                    { extend: 'pdf', text: 'PDF' },
                    { extend: 'print', text: 'Imprimir' }
                ],
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json"
                },
                pageLength: 10,
                order: [[0, "asc"]]
            });
        });
    </script>

</body>
</html>

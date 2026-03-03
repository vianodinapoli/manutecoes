<x-app-layout>

    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-dark mb-0">
                <i class="bi bi-file-earmark-text text-primary me-2"></i>Histórico de Requisições
            </h4>
            <a href="{{ route('requisicoes.create') }}" class="btn btn-primary btn-sm fw-bold px-3 shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Nova Requisição
            </a>
        </div>

        <div class="card shadow-sm border-0 overflow-hidden">
            <div class="table-responsive p-3">
                <table class="table table-hover align-middle mb-0" id="reqTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="small fw-bold">Nº / DATA</th>
                            <th class="small fw-bold">FORNECEDOR</th>
                            <th class="small fw-bold">ITENS</th>
                            <th class="small fw-bold text-end">TOTAL LÍQUIDO</th>
                            <th class="small fw-bold text-end">IVA (16%)</th>
                            <th class="small fw-bold text-end">TOTAL GERAL</th>
                            <th class="small fw-bold text-center">PDF</th>
                            <th class="small fw-bold text-center">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requisicoes as $req)
                        <tr>
                            <td class="small">
                                <strong>#{{ $req->id }}</strong><br>
                                <span class="text-muted">{{ $req->date->format('d/m/Y') }}</span>
                            </td>
                            <td class="fw-bold text-primary">{{ $req->supplier->name }}</td>
                            <td class="small text-muted">{{ $req->items->count() }} item(ns)</td>
                            <td class="text-end small">{{ number_format($req->total_liquid, 2, ',', ' ') }} MT</td>
                            <td class="text-end small text-muted">{{ number_format($req->tax_amount, 2, ',', ' ') }} MT</td>
                            <td class="text-end fw-bold text-dark">{{ number_format($req->total_final, 2, ',', ' ') }} MT</td>
                            <td class="text-center">
                                <a href="{{ route('requisicoes.pdf', $req->id) }}" target="_blank" 
                                   class="btn btn-sm btn-outline-danger" title="Abrir PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-dark" onclick="visualizarReq({{ $req->id }})" title="Visualizar/Imprimir">
                                        <i class="bi bi-printer"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="cancelarReq({{ $req->id }})" title="Cancelar">
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

    {{-- jQuery + DataTables JS --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#reqTable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
                },
                order: [[0, 'desc']], // Ordena por Nº desc por padrão
                columnDefs: [
                    { orderable: false, targets: [6, 7] } // PDF e Ações não ordenáveis
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
            });
        });
    </script>

</x-app-layout>
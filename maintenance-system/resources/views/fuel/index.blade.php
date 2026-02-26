<x-app-layout>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        /* Estilos de Impressão */
        @media print {
            .btn, .modal, form, canvas, .dataTables_filter, .dataTables_length, .dataTables_info, .dataTables_paginate {
                display: none !important;
            }
            .container { width: 100%; max-width: 100%; margin: 0; padding: 0; }
            #print-footer { display: block !important; margin-top: 50px; text-align: center; }
            .table td, .table th { color: #000 !important; border: 1px solid #ddd !important; font-size: 10pt; }
        }

        #print-footer { display: none; }
        .signature-line { border-top: 1px solid #000; width: 250px; margin-top: 40px; display: inline-block; }
        .card-fuel { border-radius: 15px; border: none; transition: transform 0.2s; }
        
        /* Estilo dos Mini Tanques */
        .tank-visor { height: 45px; background: #eee; border-radius: 8px; border: 2px solid #ddd; overflow: hidden; position: relative; }
        .tank-level { height: 100%; transition: width 1s ease-in-out; background: linear-gradient(90deg, #00b09b, #96c93d); }
        .tank-text { position: absolute; width: 100%; text-align: center; top: 50%; transform: translateY(-50%); font-weight: 700; color: #111; font-size: 0.8rem; }
        
        .table-compact th { font-size: 0.7rem; text-transform: uppercase; background: #f8f9fa; }
        .table-compact td { font-size: 0.85rem; vertical-align: middle; }
        .bg-entrada { background-color: #e6f4ea; color: #1e7e34; }
        .bg-saida { background-color: #fce8e6; color: #d93025; }
    </style>

    <div class="container py-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-dark mb-0"><i class="bi bi-fuel-pump-fill text-primary me-2"></i>Gestão de Combustível Multi-Tanque</h4>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-success btn-sm shadow-sm fw-bold px-3" data-bs-toggle="modal" data-bs-target="#modalEntrada">
                    <i class="bi bi-plus-circle me-1"></i> Atestar Tanque
                </button>
                <button onclick="window.print()" class="btn btn-primary btn-sm shadow-sm fw-bold px-3">
                    <i class="bi bi-printer me-1"></i> Imprimir
                </button>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card card-fuel shadow-sm p-3 h-100 bg-white">
                    <h6 class="fw-bold text-muted mb-3 small text-uppercase"><i class="bi bi-moisture me-2"></i>Níveis de Stock</h6>
                    <div class="overflow-auto" style="max-height: 400px;">
                        @foreach($tanques as $tanque)
                        <div class="mb-3 p-2 border-start border-1 border-success rounded bg-light">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-bold small">{{ $tanque->nome }}</span>
                                <span class="small fw-bold">{{ number_format($tanque->percentagem, 1) }}%</span>
                            </div>
                            <div class="tank-visor">
                                <div class="tank-level" style="width: {{ $tanque->percentagem }}%; {{ $tanque->percentagem < 15 ? 'background: #dc3545;' : '' }}"></div>
                                <div class="tank-text">{{ number_format($tanque->stock_atual, 0, ',', '.') }}L / {{ number_format($tanque->capacidade, 0, ',', '.') }}L</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card card-fuel shadow-sm p-4 h-100">
                    <h6 class="fw-bold text-muted mb-4 small text-uppercase">Registrar Abastecimento (Saída)</h6>
                    <form action="{{ route('fuel.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="small fw-bold text-primary">Selecionar Tanque de Origem</label>
                                <select name="tank_id" class="form-select border-primary shadow-sm" required>
                                    <option value="" selected disabled>Escolha o tanque...</option>
                                    @foreach($tanques as $tanque)
                                        <option value="{{ $tanque->id }}">{{ $tanque->nome }} (Disp: {{ number_format($tanque->stock_atual, 0) }} L)</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small fw-bold">Data</label>
                                <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="small fw-bold">Viatura/Matrícula</label>
                                <input type="text" name="plate" class="form-control" placeholder="LDA-00-00" required>
                            </div>

                            <div class="col-md-4">
                                <label class="small fw-bold">Contador Inicial (L)</label>
                                <input type="number" name="start_counter" id="ci" class="form-control bg-light" oninput="calc()" required>
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold">Contador Final (L)</label>
                                <input type="number" name="end_counter" id="cf" class="form-control bg-light" oninput="calc()" required>
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold text-primary">Total Abastecido</label>
                                <div class="input-group">
                                    <input type="number" id="qty" name="quantity" class="form-control border-primary fw-bold text-primary bg-white" readonly>
                                    <span class="input-group-text bg-primary text-white border-primary">L</span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="small fw-bold">Operador de Bomba</label>
                                <input type="text" name="operator" class="form-control" placeholder="Nome do operador">
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold">Motorista</label>
                                <input type="text" name="driver" class="form-control" placeholder="Nome do motorista">
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold">Empresa/Departamento</label>
                                <input type="text" name="company" class="form-control" placeholder="Ex: Logística">
                            </div>

                            <div class="col-12 text-end mt-4">
                                <button type="submit" class="btn btn-primary px-5 fw-bold shadow rounded-pill">Confirmar Saída</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card card-fuel shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0 text-dark small text-uppercase">Movimentações Recentes</h6>
            </div>
            <div class="table-responsive p-3">
                <table id="fuelTable" class="table table-hover table-compact w-100">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Operação</th>
                            <th>Tanque</th>
                            <th>Identificação</th>
                            <th class="text-center">Contadores</th>
                            <th class="text-end">Qtd</th>
                            <th>Operador / Motorista</th>
                            <th class="text-center d-print-none">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($historico as $item)
                        <tr>
                            <td class="text-muted">{{ date('d/m/Y', strtotime($item->date)) }}</td>
                            <td>
                                <span class="badge {{ $item->tipo == 'ENTRADA' ? 'bg-entrada' : 'bg-saida' }} rounded-pill">
                                    {{ $item->tipo }}
                                </span>
                            </td>
                            <td class="fw-bold text-primary">{{ $item->tanque_nome ?? '---' }}</td>
                            <td>{{ $item->ident }} <br><small class="text-muted">{{ $item->company }}</small></td>
                            <td class="text-center small">
                                @if($item->start_counter !== null)
                                    {{ number_format($item->start_counter, 0) }} - {{ number_format($item->end_counter, 0) }}
                                @else
                                    ---
                                @endif
                            </td>
                            <td class="text-end fw-bold {{ $item->tipo == 'ENTRADA' ? 'text-success' : 'text-danger' }}">
                                {{ $item->tipo == 'ENTRADA' ? '+' : '-' }} {{ number_format($item->quantity, 0, ',', '.') }} L
                            </td>
                            <td>
                                <div class="small">
                                    <strong>Op:</strong> {{ $item->operator ?: '---' }}<br>
                                    <strong>Mot:</strong> {{ $item->driver ?: '---' }}
                                </div>
                            </td>
                            <td class="text-center d-print-none">
                                <button class="btn btn-light btn-sm rounded-circle"><i class="bi bi-pencil"></i></button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div id="print-footer">
            <div class="row">
                <div class="col-6">
                    <div class="signature-line"></div>
                    <p class="small fw-bold">Operador/Responsável</p>
                </div>
                <div class="col-6">
                    <div class="signature-line"></div>
                    <p class="small fw-bold">Visto/Administração</p>
                </div>
            </div>
            <p class="mt-4 small text-muted">Relatório gerado em {{ date('d/m/Y H:i') }} por {{ auth()->user()->name }}</p>
        </div>
    </div>

    <div class="modal fade" id="modalEntrada" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white border-0">
                    <h6 class="modal-title fw-bold"><i class="bi bi-truck me-2"></i>Entrada de Cisterna</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('fuel.storeEntry') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="small fw-bold">Tanque de Destino</label>
                            <select name="tank_id" class="form-select" required>
                                <option value="" selected disabled>Escolha o tanque...</option>
                                @foreach($tanques as $tanque)
                                    <option value="{{ $tanque->id }}">{{ $tanque->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="small fw-bold">Data do Atesto</label>
                                <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small fw-bold">Quantidade (L)</label>
                                <input type="number" name="quantity" class="form-control" placeholder="2000" required>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="small fw-bold">Fornecedor / Ref. Documento</label>
                            <input type="text" name="supplier" class="form-control" placeholder="Ex: Petromoc / Doc 123">
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="submit" class="btn btn-success fw-bold px-4">Confirmar Entrada</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#fuelTable').DataTable({
                "order": [[ 0, "desc" ]],
                "language": { "url": "https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json" },
                "pageLength": 15
            });
        });

        function calc() {
            const i = parseFloat(document.getElementById('ci').value) || 0;
            const f = parseFloat(document.getElementById('cf').value) || 0;
            const res = f - i;
            document.getElementById('qty').value = res > 0 ? res : 0;
        }
    </script>
</x-app-layout>
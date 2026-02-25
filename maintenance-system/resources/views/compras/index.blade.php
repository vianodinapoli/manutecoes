<x-app-layout>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        .container { max-width: 100%; }
        .table thead th { font-weight: 700; text-transform: uppercase; font-size: 0.75rem; color: #495057; }
        .badge-status { min-width: 145px; border-radius: 8px; padding: 5px 10px; font-weight: 600; font-size: 0.8rem; cursor: pointer; transition: 0.3s; border: none !important; }
        .badge-status:hover { opacity: 0.9; transform: translateY(-1px); }
        .btn-action { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; transition: 0.2s; border: 1px solid #dee2e6; background: #fff; }
        .btn-action:hover { background-color: #f8f9fa; transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        
        #modal-obs { word-break: break-word; white-space: pre-line; }

        /* Estilo para Assinatura Manuscrita */
        .signature-font { 
            font-family: 'Dancing Script', cursive; 
            font-size: 2.2rem; 
            color: #003d99; /* Azul caneta */
            padding: 0 30px;
            display: inline-block;
            line-height: 1;
        }

        /* CONFIGURAÇÃO DE IMPRESSÃO PARA A4 */
        @media print {
            /* Define tamanho A4 e margens da folha */
            @page { 
                size: A4; 
                margin: 2cm; 
            }

            /* Esconde tudo o que não é o modal */
            body * { visibility: hidden; }
            #modalDetalhes, #modalDetalhes * { visibility: visible; }
            
            /* Reposiciona o modal para preencher a folha A4 */
            #modalDetalhes { 
                position: absolute; 
                left: 0; 
                top: 0; 
                width: 100%; 
                margin: 0; 
                padding: 0; 
                background: #fff;
            }

            .modal-dialog {
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
            }

            .modal-content { 
                border: none !important; 
                box-shadow: none !important; 
            }

            /* Esconde botões e cabeçalhos do modal na impressão */
            .d-print-none, .btn-close, .modal-footer, .modal-header { 
                display: none !important; 
            }

            /* Garante que as cores e o azul da assinatura apareçam */
            .signature-font { 
                color: #003d99 !important; 
                -webkit-print-color-adjust: exact; 
                print-color-adjust: exact;
            }

            .table-light { 
                background-color: #f8f9fa !important; 
                -webkit-print-color-adjust: exact; 
            }
            
            .text-primary { color: #000 !important; }
        }
    </style>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark"><i class="bi bi-cart4 text-primary me-2"></i>Gestão de Compras</h2>
            <a href="{{ route('compras.create') }}" class="btn btn-primary shadow-sm px-4 fw-bold">
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
                        <div class="d-flex justify-content-between align-items-center text-{{ $item[1] }} mb-1">
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

        {{-- Filtros --}}
        <div class="card p-3 mb-4 shadow-sm border-0 bg-white">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="small fw-bold text-muted mb-1">Data Inicial:</label>
                    <input type="date" id="min-date" class="form-control form-control-sm border-light-subtle shadow-none">
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold text-muted mb-1">Data Final:</label>
                    <input type="date" id="max-date" class="form-control form-control-sm border-light-subtle shadow-none">
                </div>
                <div class="col-md-4">
                    <button id="clear-filters" class="btn btn-light border btn-sm w-100 fw-bold text-secondary">Limpar Filtros</button>
                </div>
            </div>
        </div>

        {{-- Tabela --}}
        <div class="table-responsive card shadow-sm p-3 border-0">
            <table id="comprasTable" class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>ID</th>
                        <th>Material (Resumo)</th>
                        <th class="text-center">Urgência</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Relação</th>
                        <th>Solicitante</th>
                        <th>Data Pedido</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($compras as $compra)
                        @php
                            $status_color = match($compra->status) {
                                'Pendente' => 'bg-warning text-dark',
                                'Em processo' => 'bg-info text-white',
                                'Aprovado' => 'bg-success text-white',
                                'Rejeitado' => 'bg-danger text-white',
                                'Finalizado' => 'bg-dark text-white',
                                default => 'bg-secondary text-white',
                            };

                            $urgencia = $compra->urgencia ?? 'Normal';
                            $urg_style = match($urgencia) {
                                'Crítica' => 'background-color: #fce8e6; color: #d93025; border: 1px solid #f99f97;',
                                'Alta'    => 'background-color: #fef7e0; color: #b06000; border: 1px solid #ffe082;',
                                'Normal'  => 'background-color: #e6f4ea; color: #137333; border: 1px solid #c3e6cb;',
                                default   => 'background-color: #f1f3f4; color: #5f6368; border: 1px solid #dadce0;',
                            };

                            $primeiroItem = $compra->items->first();
                            $totalItens = $compra->items->count();
                        @endphp
                        <tr>
                            <td class="text-muted fw-bold">#{{ $compra->id }}</td>
                            <td>
                                <div class="fw-semibold">
                                    {{ $primeiroItem ? $primeiroItem->item_name : 'Sem descrição' }}
                                    @if($totalItens > 1)
                                        <span class="badge bg-secondary ms-1" style="font-size: 0.6rem;">+{{ $totalItens - 1 }} itens</span>
                                    @endif
                                </div>
                                <small class="text-muted text-truncate d-inline-block" style="max-width: 150px;">
                                    {{ $compra->fornecedor ?? 'Fornecedor não indicado' }}
                                </small>
                            </td>
                            
                            <td class="text-center">
                                <span class="badge px-3 py-2 fw-bold" style="{{ $urg_style }} border-radius: 20px; font-size: 0.65rem; letter-spacing: 0.5px;">
                                    <i class="bi bi-circle-fill me-1" style="font-size: 0.4rem; vertical-align: middle;"></i> 
                                    {{ strtoupper($urgencia) }}
                                </span>
                            </td>

                            <td class="text-center">
                                <form action="{{ route('compras.status', $compra->id) }}" method="POST" class="m-0">
                                    @csrf @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="form-select form-select-sm badge-status {{ $status_color }} shadow-sm">
                                        @if(auth()->user()->hasRole('super-admin'))
                                            @foreach(['Pendente', 'Em processo', 'Aprovado', 'Rejeitado', 'Finalizado'] as $opt)
                                                <option value="{{ $opt }}" {{ $compra->status == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                            @endforeach
                                        @else
                                            <option value="{{ $compra->status }}" selected>{{ $compra->status }}</option>
                                            <option value="Em processo">Em processo</option>
                                            <option value="Finalizado">Finalizado</option>
                                        @endif
                                    </select>
                                </form>
                            </td>

                            <td class="text-center">
                                @if($compra->attachments->count() > 0 || $compra->items->count() > 0)
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-bold btn-show-details" 
                                            style="font-size: 0.65rem;"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalDetalhes"
                                            data-id="{{ $compra->id }}"
                                            data-solicitante="{{ $compra->user->name ?? 'N/A' }}"
                                            data-fornecedor="{{ $compra->fornecedor ?? 'N/A' }}"
                                            data-obs="{{ $compra->description ?? 'Sem observações' }}"
                                            data-itens='{!! str_replace("'", "&#39;", $compra->items->toJson()) !!}'
                                            data-anexos='{!! str_replace("'", "&#39;", $compra->attachments->toJson()) !!}'>
                                        <i class="bi bi-eye"></i> {{ $compra->attachments->count() }} Ver
                                    </button>
                                @else
                                    <span class="text-muted small">---</span>
                                @endif
                            </td>

                            <td><span class="small fw-semibold text-secondary">{{ $compra->user->name ?? 'N/A' }}</span></td>
                            <td><span class="small text-muted">{{ $compra->created_at->format('d/m/Y') }}</span></td>

                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    {{-- <a href="{{ route('compras.show', $compra->id) }}" class="btn btn-action text-info" title="Visualizar Detalhes">
                                        <i class="bi bi-eye"></i>
                                    </a> --}}

                                    @php
                                        $podeEditar = !in_array($compra->status, ['Finalizado', 'Rejeitado']) || auth()->user()->hasRole('super-admin');
                                    @endphp

                                    @if($podeEditar)
                                        <a href="{{ route('compras.edit', $compra->id) }}" class="btn btn-action text-warning" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif

                                    @if(auth()->user()->hasRole('super-admin'))
                                        <form action="{{ route('compras.destroy', $compra->id) }}" method="POST" class="m-0">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-action text-danger" title="Eliminar" onclick="return confirm('Apagar permanentemente?')">
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

    {{-- Estrutura do Modal Otimizada --}}
    <div class="modal fade" id="modalDetalhes" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light d-print-none">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-file-text me-2"></i>Ficha de Requisição</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4" id="printArea">
                    <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-4">
                        <div>
                            <h4 class="fw-bold text-primary mb-0">REQUISIÇÃO DE COMPRA</h4>
                            <span class="text-muted small">Nº Registro: #<span id="modal-id"></span></span>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold text-dark small text-uppercase">Data de Emissão</div>
                            <div id="modal-data-atual" class="text-secondary fw-semibold"></div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-6 border-end">
                            <label class="small text-muted d-block fw-bold text-uppercase mb-1">Solicitante</label>
                            <span id="modal-solicitante-nome" class="fw-bold text-dark"></span>
                        </div>
                        <div class="col-6 ps-4">
                            <label class="small text-muted d-block fw-bold text-uppercase mb-1">Fornecedor Sugerido</label>
                            <span id="modal-fornecedor" class="fw-bold text-dark"></span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="small text-muted d-block fw-bold text-uppercase mb-1">Justificação / Observações</label>
                        <div id="modal-obs" class="p-3 bg-light rounded border text-secondary small" style="min-height: 60px;"></div>
                    </div>

                    <h6 class="fw-bold border-bottom pb-2 text-primary d-flex align-items-center">
                        <i class="bi bi-box-seam me-2"></i> Itens da Solicitação
                    </h6>
                    <div class="table-responsive mb-5">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light text-center">
                                <tr class="small fw-bold">
                                    <th width="50%">MATERIAL / DESCRIÇÃO</th>
                                    <th>QTD</th>
                                    <th>DESTINO / OBRA</th>
                                </tr>
                            </thead>
                            <tbody id="modal-tabela-itens"></tbody>
                        </table>
                    </div>

                    <div class="row mt-5 pt-4">
                        <div class="col-6 offset-6 text-center">
                            <div class="mb-0">
                                <span id="modal-assinatura-digital" class="signature-font"></span>
                            </div>
                            <div class="border-top pt-1">
                                <span class="small fw-bold text-uppercase text-muted" style="font-size: 0.65rem;">Assinatura Digital do Solicitante</span>
                            </div>
                            <div class="text-muted mt-1" style="font-size: 0.55rem;">
                                Documento gerado eletronicamente via Sistema de Gestão em {{ now()->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 d-print-none">
                        <h6 class="fw-bold border-bottom pb-2 text-primary d-flex align-items-center">
                            <i class="bi bi-paperclip me-2"></i> Documentos Anexos
                        </h6>
                        <div id="modal-anexos-lista" class="d-flex flex-wrap gap-2 mt-2 py-2"></div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-0 d-print-none">
                    <button type="button" class="btn btn-secondary btn-sm px-4 fw-bold" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary btn-sm px-4 fw-bold shadow-sm" onclick="window.print();">
                        <i class="bi bi-printer me-2"></i> Imprimir Documento
                    </button>
                </div>
            </div>
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
                columnDefs: [{ orderable: false, targets: [3, 4, 7] }]
            });

            // Filtros de Data
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
                    (!minDate && maxDate && valDate <= maxDate) || (minDate && maxDate && valDate >= minDate && valDate <= maxDate)) return true;
                return false;
            });

            $('#min-date, #max-date').on('change', function() { table.draw(); });
            $('#clear-filters').on('click', function() { $('#min-date, #max-date').val(''); table.draw(); });

            // Delegação para o Modal
            $(document).on('click', '.btn-show-details', function() {
                const btn = $(this);
                
                try {
                    const id = btn.attr('data-id');
                    const solicitante = btn.attr('data-solicitante');
                    const fornecedor = btn.attr('data-fornecedor');
                    const obs = btn.attr('data-obs');
                    const itens = JSON.parse(btn.attr('data-itens') || '[]');
                    const anexos = JSON.parse(btn.attr('data-anexos') || '[]');

                    // Preencher campos de texto
                    $('#modal-id').text(id);
                    $('#modal-solicitante-nome').text(solicitante);
                    $('#modal-assinatura-digital').text(solicitante);
                    $('#modal-fornecedor').text(fornecedor);
                    $('#modal-obs').text(obs);
                    
                    // Data atual formatada
                    $('#modal-data-atual').text(new Date().toLocaleDateString('pt-BR'));

                    // Renderizar itens
                    let htmlItens = '';
                    itens.forEach(item => {
                        htmlItens += `
                            <tr class="small text-center">
                                <td class="ps-3 text-start">${item.item_name}</td>
                                <td class="fw-bold">${item.quantity}</td>
                                <td>${item.destino}</td>
                            </tr>`;
                    });
                    $('#modal-tabela-itens').html(htmlItens || '<tr><td colspan="3" class="text-center">Sem itens</td></tr>');

                    // Renderizar anexos
                    let htmlAnexos = '';
                    if(anexos.length > 0) {
                        anexos.forEach(doc => {
                            htmlAnexos += `
                                <a href="/storage/${doc.file_path}" target="_blank" class="btn btn-sm btn-outline-primary px-3 shadow-sm">
                                    <i class="bi bi-file-earmark-pdf"></i> ${doc.file_name}
                                </a>`;
                        });
                    } else {
                        htmlAnexos = '<span class="text-muted small italic">Nenhum documento anexado.</span>';
                    }
                    $('#modal-anexos-lista').html(htmlAnexos);

                } catch (e) {
                    console.error("Erro no Parse do Modal:", e);
                }
            });
        });
    </script>
</x-app-layout>
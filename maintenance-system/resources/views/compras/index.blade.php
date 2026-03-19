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
        .signature-font { font-family: 'Dancing Script', cursive; font-size: 2.2rem; color: #003d99; padding: 0 30px; display: inline-block; line-height: 1; }
        .btn-emitir-req { font-size: 0.7rem; padding: 4px 10px; border-radius: 20px; font-weight: 700; background: linear-gradient(135deg, #198754, #20c997); color: white; border: none; white-space: nowrap; transition: 0.2s; }
        .btn-emitir-req:hover { transform: translateY(-1px); box-shadow: 0 4px 10px rgba(25,135,84,0.3); color: white; }

        /* ── Toast ── */
        .toast-success{position:fixed;top:24px;right:24px;z-index:99999;background:#fff;border-radius:12px;padding:16px 20px;display:flex;align-items:center;gap:12px;box-shadow:0 8px 32px rgba(0,0,0,.12);border-left:4px solid #16a34a;min-width:300px;transform:translateX(120%);transition:transform 0.35s cubic-bezier(.34,1.56,.64,1)}
        .toast-success.show{transform:translateX(0)}
        .toast-icon{width:36px;height:36px;background:#f0fdf4;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#16a34a;font-size:1rem;flex-shrink:0}
        .toast-text{flex:1}
        .toast-title{font-size:.82rem;font-weight:700;color:#1e293b;margin-bottom:2px}
        .toast-sub{font-size:.74rem;color:#94a3b8}
        .toast-close{background:none;border:none;color:#94a3b8;cursor:pointer;font-size:1rem;padding:0;line-height:1}
        .toast-close:hover{color:#475569}

        /* ── Modal confirmação ── */
        .confirm-overlay{position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:9999;display:flex;align-items:center;justify-content:center;opacity:0;pointer-events:none;transition:opacity .25s}
        .confirm-overlay.open{opacity:1;pointer-events:all}
        .confirm-box{background:#fff;border-radius:20px;max-width:400px;width:calc(100% - 32px);box-shadow:0 24px 64px rgba(0,0,0,.18);transform:scale(.93) translateY(10px);transition:transform .25s cubic-bezier(.34,1.56,.64,1);overflow:hidden}
        .confirm-overlay.open .confirm-box{transform:scale(1) translateY(0)}
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

        @media print {
            @page { size: A4; margin: 2cm; }
            body * { visibility: hidden; }
            #modalDetalhes, #modalDetalhes * { visibility: visible; }
            #modalDetalhes { position: absolute; left: 0; top: 0; width: 100%; margin: 0; padding: 0; background: #fff; }
            .modal-dialog { max-width: 100% !important; width: 100% !important; margin: 0 !important; }
            .modal-content { border: none !important; box-shadow: none !important; }
            .d-print-none, .btn-close, .modal-footer, .modal-header { display: none !important; }
            .signature-font { color: #003d99 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .table-light { background-color: #f8f9fa !important; -webkit-print-color-adjust: exact; }
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

        {{-- Flash session (redirect tradicional) --}}
        @if(session('success'))
        <div class="d-flex align-items-center gap-2 mb-3 px-3 py-2 rounded"
             style="background:#f0fdf4;border:1px solid #bbf7d0;font-size:.8rem;color:#166534;">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
        @endif

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
                                'Pendente'    => 'bg-warning text-dark',
                                'Em processo' => 'bg-info text-white',
                                'Aprovado'    => 'bg-success text-white',
                                'Rejeitado'   => 'bg-danger text-white',
                                'Finalizado'  => 'bg-dark text-white',
                                default       => 'bg-secondary text-white',
                            };
                            $urgencia  = $compra->urgencia ?? 'Normal';
                            $urg_style = match($urgencia) {
                                'Crítica' => 'background-color: #fce8e6; color: #d93025; border: 1px solid #f99f97;',
                                'Alta'    => 'background-color: #fef7e0; color: #b06000; border: 1px solid #ffe082;',
                                'Normal'  => 'background-color: #e6f4ea; color: #137333; border: 1px solid #c3e6cb;',
                                default   => 'background-color: #f1f3f4; color: #5f6368; border: 1px solid #dadce0;',
                            };
                            $primeiroItem = $compra->items->first();
                            $totalItens   = $compra->items->count();
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
                                <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap">
                                    @if($compra->status === 'Aprovado')
                                        <a href="{{ route('requisicoes.create', ['compra_id' => $compra->id]) }}"
                                           class="btn btn-emitir-req" title="Emitir Requisição de Compra">
                                            <i class="bi bi-file-earmark-arrow-up me-1"></i> Emitir Req.
                                        </a>
                                    @endif
                                    @php
                                        $podeEditar = !in_array($compra->status, ['Finalizado', 'Rejeitado']) || auth()->user()->hasRole('super-admin');
                                    @endphp
                                    @if($podeEditar)
                                        <a href="{{ route('compras.edit', $compra->id) }}" class="btn btn-action text-warning" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                    @if(auth()->user()->hasRole('super-admin'))
                                        {{-- Form oculto — submetido pelo modal --}}
                                        <form id="deleteForm-{{ $compra->id }}"
                                              action="{{ route('compras.destroy', $compra->id) }}"
                                              method="POST" class="d-none">
                                            @csrf @method('DELETE')
                                        </form>
                                        <button type="button"
                                                class="btn btn-action text-danger btn-delete"
                                                title="Eliminar"
                                                data-form="deleteForm-{{ $compra->id }}"
                                                data-label="#{{ $compra->id }}">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- TOAST --}}
    {{-- Só aparece se vier da sessão após redirect --}}
    <div class="toast-success" id="toastSuccess">
        <div class="toast-icon"><i class="bi bi-check-lg"></i></div>
        <div class="toast-text">
            <div class="toast-title">Compra eliminada</div>
            <div class="toast-sub">O registo foi removido com sucesso.</div>
        </div>
        <button class="toast-close" onclick="closeToast()"><i class="bi bi-x-lg"></i></button>
    </div>

    {{-- MODAL CONFIRMAÇÃO ELIMINAR --}}
    <div class="confirm-overlay" id="confirmOverlay">
        <div class="confirm-box">
            <div class="confirm-header">
                <div class="confirm-icon"><i class="bi bi-trash3-fill"></i></div>
                <div class="confirm-title">Apagar solicitação?</div>
                <div class="confirm-sub">Tens a certeza que queres eliminar a compra <strong id="confirmLabel"></strong>?</div>
            </div>
            <div class="confirm-body">
                <div class="confirm-warning">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    Esta acção é irreversível. Todos os itens e anexos associados serão removidos.
                </div>
                <div class="confirm-actions">
                    <button class="btn-cancel-confirm" onclick="closeConfirm()">
                        <i class="bi bi-x-lg"></i> Cancelar
                    </button>
                    <button class="btn-delete-confirm" id="confirmOkBtn">
                        <i class="bi bi-trash3"></i> Apagar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Detalhes --}}
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
                            {{-- <div class="mb-0">
                                <span id="modal-assinatura-digital" class="signature-font"></span>
                            </div> --}}
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

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/dt-2.0.8/datatables.min.js"></script>
    <script>
        var _deleteFormId = null;

        // ── Toast ──
        function showToast() {
            var t = document.getElementById('toastSuccess');
            t.classList.add('show');
            setTimeout(closeToast, 4000);
        }
        function closeToast() {
            document.getElementById('toastSuccess').classList.remove('show');
        }

        // Mostra toast se vier sessão de sucesso após redirect
        @if(session('deleted'))
            document.addEventListener('DOMContentLoaded', function() { showToast(); });
        @endif

        // ── Modal confirmação ──
        $(document).on('click', '.btn-delete', function() {
            _deleteFormId = $(this).data('form');
            $('#confirmLabel').text($(this).data('label'));
            $('#confirmOverlay').addClass('open');
        });

        $('#confirmOkBtn').on('click', function() {
            if (_deleteFormId) {
                $('#' + _deleteFormId).submit();
            }
        });

        $('#confirmOverlay').on('click', function(e) {
            if (e.target === this) closeConfirm();
        });

        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') closeConfirm();
        });

        function closeConfirm() {
            $('#confirmOverlay').removeClass('open');
        }

        // ── DataTables ──
        $(document).ready(function() {
            var table = $('#comprasTable').DataTable({
                language: { url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/pt-BR.json' },
                order: [[0, 'desc']],
                columnDefs: [{ orderable: false, targets: [3, 4, 7] }]
            });

            $.fn.dataTable.ext.search.push(function(settings, data) {
                var min = $('#min-date').val();
                var max = $('#max-date').val();
                var dateStr = data[6];
                if (!dateStr) return true;
                var p = dateStr.split('/');
                var valDate = p[2] + p[1] + p[0];
                var minD = min ? min.replace(/-/g, '') : null;
                var maxD = max ? max.replace(/-/g, '') : null;
                if (!minD && !maxD) return true;
                if (minD && !maxD) return valDate >= minD;
                if (!minD && maxD) return valDate <= maxD;
                return valDate >= minD && valDate <= maxD;
            });

            $('#min-date, #max-date').on('change', function() { table.draw(); });
            $('#clear-filters').on('click', function() { $('#min-date, #max-date').val(''); table.draw(); });

            $(document).on('click', '.btn-show-details', function() {
                const btn = $(this);
                try {
                    $('#modal-id').text(btn.attr('data-id'));
                    $('#modal-solicitante-nome').text(btn.attr('data-solicitante'));
                    $('#modal-assinatura-digital').text(btn.attr('data-solicitante'));
                    $('#modal-fornecedor').text(btn.attr('data-fornecedor'));
                    $('#modal-obs').text(btn.attr('data-obs'));
                    $('#modal-data-atual').text(new Date().toLocaleDateString('pt-BR'));

                    const itens  = JSON.parse(btn.attr('data-itens') || '[]');
                    const anexos = JSON.parse(btn.attr('data-anexos') || '[]');

                    let htmlItens = '';
                    itens.forEach(item => {
                        htmlItens += `<tr class="small text-center">
                            <td class="ps-3 text-start">${item.item_name}</td>
                            <td class="fw-bold">${item.quantity}</td>
                            <td>${item.destino}</td>
                        </tr>`;
                    });
                    $('#modal-tabela-itens').html(htmlItens || '<tr><td colspan="3" class="text-center">Sem itens</td></tr>');

                    let htmlAnexos = '';
                    if (anexos.length > 0) {
                        anexos.forEach(doc => {
                            htmlAnexos += `<a href="/storage/${doc.file_path}" target="_blank" class="btn btn-sm btn-outline-primary px-3 shadow-sm"><i class="bi bi-file-earmark-pdf"></i> ${doc.file_name}</a>`;
                        });
                    } else {
                        htmlAnexos = '<span class="text-muted small">Nenhum documento anexado.</span>';
                    }
                    $('#modal-anexos-lista').html(htmlAnexos);
                } catch (e) {
                    console.error('Erro no Parse do Modal:', e);
                }
            });
        });
    </script>
</x-app-layout>
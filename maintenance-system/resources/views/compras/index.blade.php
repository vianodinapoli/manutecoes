<x-app-layout>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&display=swap" rel="stylesheet">

<style>
    /* ── KPI Cards ── */
    .kpi-card{background:#fff;border-radius:14px;border:1px solid #e9ecef;padding:16px 20px;box-shadow:0 2px 8px rgba(0,0,0,.04);transition:transform .15s,box-shadow .15s;height:100%;position:relative;overflow:hidden}
    .kpi-card:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,0,0,.08)}
    .kpi-card::before{content:'';position:absolute;left:0;top:0;bottom:0;width:4px;border-radius:14px 0 0 14px}
    .kpi-card.yellow::before{background:#ffc107}
    .kpi-card.teal::before{background:#0dcaf0}
    .kpi-card.green::before{background:#198754}
    .kpi-card.red::before{background:#c60a1a}
    .kpi-card.dark::before{background:#212529}
    .kpi-label{font-size:.65rem;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:#adb5bd;margin-bottom:6px}
    .kpi-value{font-size:1.7rem;font-weight:800;line-height:1;margin-bottom:2px;color:#1e293b}
    .kpi-icon{position:absolute;right:16px;top:50%;transform:translateY(-50%);font-size:2rem;opacity:.08}

    /* ── Filter panel ── */
    .filter-panel{background:#fff;border-radius:14px;border:1px solid #e9ecef;padding:18px 22px;margin-bottom:20px;box-shadow:0 2px 8px rgba(0,0,0,.04)}
    .filter-label{font-size:.68rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#6c757d;margin-bottom:5px;display:block}
    .filter-panel .form-control{border-radius:8px;border:1px solid #dee2e6;font-size:.82rem;background:#f8f9fa}
    .filter-panel .form-control:focus{border-color:#c60a1a;box-shadow:0 0 0 3px rgba(198,10,26,.08);background:#fff}

    /* ── Table card ── */
    .table-card{background:#fff;border-radius:14px;border:1px solid #e9ecef;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.04)}
    #comprasTable thead tr{background:#f8f9fa}
    #comprasTable thead th{font-size:.67rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#868e96;border-bottom:1px solid #e9ecef;padding:8px 12px;white-space:nowrap}
    #comprasTable tbody td{padding:8px 12px;vertical-align:middle;border-bottom:1px solid #f1f3f5;font-size:.82rem}
    #comprasTable tbody tr:hover{background:#fdf5f5}
    #comprasTable tbody tr:last-child td{border-bottom:none}

    /* ── Urgência badges ── */
    .urg-badge{border-radius:20px;padding:3px 10px;font-size:.65rem;font-weight:700;letter-spacing:.5px;display:inline-flex;align-items:center;gap:4px}
    .urg-critica{background:#fce8e6;color:#c60a1a;border:1px solid #f8b8b8}
    .urg-alta{background:#fef7e0;color:#b06000;border:1px solid #ffe082}
    .urg-normal{background:#e6f4ea;color:#137333;border:1px solid #c3e6cb}

    /* ── Status select ── */
    .badge-status{min-width:130px;border-radius:8px;padding:5px 10px;font-weight:600;font-size:.75rem;cursor:pointer;border:none!important;transition:.2s}
    .badge-status:focus{outline:none;box-shadow:none}

    /* ── Action buttons ── */
    .action-btn{width:28px;height:28px;border-radius:7px;display:inline-flex;align-items:center;justify-content:center;font-size:.72rem;border:1px solid;transition:all .15s;text-decoration:none;cursor:pointer;background:transparent}

    /* ── Emitir req button ── */
    .btn-emitir-req{font-size:.7rem;padding:4px 10px;border-radius:20px;font-weight:700;background:#c60a1a;color:#fff;border:none;white-space:nowrap;transition:.2s;display:inline-flex;align-items:center;gap:4px}
    .btn-emitir-req:hover{background:#a30816;color:#fff;transform:translateY(-1px);box-shadow:0 4px 10px rgba(198,10,26,.3)}

    /* ── Toast ── */
    .toast-success{position:fixed;top:24px;right:24px;z-index:99999;background:#fff;border-radius:12px;padding:16px 20px;display:flex;align-items:center;gap:12px;box-shadow:0 8px 32px rgba(0,0,0,.12);border-left:4px solid #16a34a;min-width:300px;transform:translateX(120%);transition:transform 0.35s cubic-bezier(.34,1.56,.64,1)}
    .toast-success.show{transform:translateX(0)}
    .toast-icon{width:36px;height:36px;background:#f0fdf4;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#16a34a;font-size:1rem;flex-shrink:0}
    .toast-text{flex:1}
    .toast-title{font-size:.82rem;font-weight:700;color:#1e293b;margin-bottom:2px}
    .toast-sub{font-size:.74rem;color:#94a3b8}
    .toast-close{background:none;border:none;color:#94a3b8;cursor:pointer;font-size:1rem;padding:0;line-height:1}
    .toast-close:hover{color:#475569}

    /* ── Confirm modal ── */
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

    /* ── Modal detalhes ── */
    .modal-header-fem{background:#c60a1a;padding:16px 22px}
    .detail-label{font-size:.62rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#adb5bd;margin-bottom:3px}
    .detail-val{font-size:.85rem;font-weight:700;color:#1e293b}
    .obs-box{background:#f8f9fa;border:1px solid #e9ecef;border-radius:8px;padding:12px 16px;font-size:.82rem;color:#475569;min-height:52px;white-space:pre-line;word-break:break-word}
    #modal-tabela-itens td,#modal-tabela-itens th{font-size:.8rem}
    .signature-font{font-family:'Dancing Script',cursive;font-size:2.2rem;color:#003d99;display:inline-block;line-height:1}

    /* ── Print ── */
    @media print{
        @page{size:A4;margin:2cm}
        body *{visibility:hidden}
        #modalDetalhes,#modalDetalhes *{visibility:visible}
        #modalDetalhes{position:absolute;left:0;top:0;width:100%;margin:0;padding:0;background:#fff}
        .modal-dialog{max-width:100%!important;width:100%!important;margin:0!important}
        .modal-content{border:none!important;box-shadow:none!important}
        .d-print-none,.btn-close,.modal-footer,.modal-header-fem{display:none!important}
        .signature-font{color:#003d99!important;-webkit-print-color-adjust:exact;print-color-adjust:exact}
    }
</style>

<div class="container-fluid py-4 px-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-cart4 me-2" style="color:#c60a1a;"></i>Gestão de Compras
            </h4>
            <p class="text-muted small mb-0">Acompanhamento de todas as solicitações de materiais</p>
        </div>
        <a href="{{ route('compras.create') }}" class="btn btn-sm fw-bold px-4 shadow-sm"
           style="background:#c60a1a;color:#fff;border-radius:9px;">
            <i class="bi bi-plus-lg me-1"></i> Nova Solicitação
        </a>
    </div>

    {{-- KPI CARDS --}}
    @php
        $kpis = [
            ['Pendente',    'yellow', 'hourglass-split', '#ffc107'],
            ['Em processo', 'teal',   'cart-dash',       '#0dcaf0'],
            ['Aprovado',    'green',  'check-all',       '#198754'],
            ['Rejeitado',   'red',    'x-circle',        '#c60a1a'],
            ['Finalizado',  'dark',   'check-circle-fill','#212529'],
        ];
    @endphp
    <div class="row g-3 mb-4">
        @foreach($kpis as $k)
        <div class="col-6 col-md">
            <div class="kpi-card {{ $k[1] }}">
                <div class="kpi-label">{{ $k[0] }}</div>
                <div class="kpi-value">{{ \App\Models\MaterialPurchase::where('status', $k[0])->count() }}</div>
                <i class="bi bi-{{ $k[2] }} kpi-icon" style="color:{{ $k[3] }};"></i>
            </div>
        </div>
        @endforeach
    </div>

    {{-- FILTROS --}}
    <div class="filter-panel">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <span class="filter-label">Data Inicial</span>
                <input type="date" id="min-date" class="form-control form-control-sm">
            </div>
            <div class="col-md-4">
                <span class="filter-label">Data Final</span>
                <input type="date" id="max-date" class="form-control form-control-sm">
            </div>
            <div class="col-md-4">
                <button id="clear-filters" class="btn btn-sm fw-bold w-100"
                        style="background:#f1f3f5;color:#495057;border:1px solid #dee2e6;border-radius:8px;">
                    <i class="bi bi-x-lg me-1"></i> Limpar Filtros
                </button>
            </div>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="d-flex align-items-center gap-2 mb-3 px-3 py-2 rounded"
         style="background:#f0fdf4;border:1px solid #bbf7d0;font-size:.8rem;color:#166534;">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
    @endif

    {{-- TABELA --}}
    <div class="table-card">
        <div class="p-3">
            <table id="comprasTable" class="table table-hover align-middle mb-0 w-100">
                <thead>
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
                @foreach($compras as $compra)
                @php
                    $status_color = match($compra->status) {
                        'Pendente'    => 'bg-warning text-dark',
                        'Em processo' => 'bg-info text-white',
                        'Aprovado'    => 'bg-success text-white',
                        'Rejeitado'   => 'bg-danger text-white',
                        'Finalizado'  => 'bg-dark text-white',
                        default       => 'bg-secondary text-white',
                    };
                    $urgencia     = $compra->urgencia ?? 'Normal';
                    $urgClass     = match($urgencia) {
                        'Crítica' => 'urg-critica',
                        'Alta'    => 'urg-alta',
                        default   => 'urg-normal',
                    };
                    $primeiroItem = $compra->items->first();
                    $totalItens   = $compra->items->count();
                @endphp
                <tr>
                    <td>
                        <span class="fw-bold text-muted" style="font-size:.78rem;">#{{ $compra->id }}</span>
                    </td>
                    <td>
                        <div class="fw-semibold" style="font-size:.82rem;">
                            {{ $primeiroItem ? $primeiroItem->item_name : 'Sem descrição' }}
                            @if($totalItens > 1)
                            <span class="badge bg-secondary ms-1" style="font-size:.58rem;">+{{ $totalItens - 1 }}</span>
                            @endif
                        </div>
                        <div class="text-muted text-truncate" style="font-size:.72rem;max-width:160px;">
                            {{ $compra->fornecedor ?? 'Fornecedor não indicado' }}
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="urg-badge {{ $urgClass }}">
                            <i class="bi bi-circle-fill" style="font-size:.35rem;"></i>
                            {{ strtoupper($urgencia) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <form action="{{ route('compras.status', $compra->id) }}" method="POST" class="m-0">
                            @csrf @method('PATCH')
                            <select name="status" onchange="this.form.submit()"
                                    class="badge-status {{ $status_color }} shadow-sm">
                                @if(auth()->user()->hasRole('super-admin'))
                                    @foreach(['Pendente','Em processo','Aprovado','Rejeitado','Finalizado'] as $opt)
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
                                class="btn btn-sm btn-show-details"
                                style="font-size:.68rem;border:1px solid #fecaca;background:#fdf2f2;color:#c60a1a;border-radius:7px;padding:4px 10px;font-weight:600;"
                                data-bs-toggle="modal" data-bs-target="#modalDetalhes"
                                data-id="{{ $compra->id }}"
                                data-solicitante="{{ $compra->user->name ?? 'N/A' }}"
                                data-fornecedor="{{ $compra->fornecedor ?? 'N/A' }}"
                                data-obs="{{ $compra->description ?? 'Sem observações' }}"
                                data-itens='{!! str_replace("'", "&#39;", $compra->items->toJson()) !!}'
                                data-anexos='{!! str_replace("'", "&#39;", $compra->attachments->toJson()) !!}'>
                            <i class="bi bi-eye me-1"></i>{{ $compra->attachments->count() > 0 ? $compra->attachments->count().' ficheiro(s)' : 'Ver' }}
                        </button>
                        @else
                        <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="fw-semibold" style="font-size:.78rem;color:#334155;">
                            {{ $compra->user->name ?? 'N/A' }}
                        </span>
                    </td>
                    <td>
                        <span class="text-muted" style="font-size:.78rem;">
                            {{ $compra->created_at->format('d/m/Y') }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center align-items-center gap-1 flex-wrap">
                            @if($compra->status === 'Aprovado')
                            <a href="{{ route('requisicoes.create', ['compra_id' => $compra->id]) }}"
                               class="btn-emitir-req" title="Emitir Requisição de Compra">
                                <i class="bi bi-file-earmark-arrow-up"></i> Emitir Req.
                            </a>
                            @endif
                            @php $podeEditar = !in_array($compra->status, ['Finalizado','Rejeitado']) || auth()->user()->hasRole('super-admin'); @endphp
                            @if($podeEditar)
                            <a href="{{ route('compras.edit', $compra->id) }}"
                               class="action-btn text-warning border-warning border-opacity-25" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endif
                            @if(auth()->user()->hasRole('super-admin'))
                            <form id="deleteForm-{{ $compra->id }}"
                                  action="{{ route('compras.destroy', $compra->id) }}"
                                  method="POST" class="d-none">
                                @csrf @method('DELETE')
                            </form>
                            <button type="button"
                                    class="action-btn text-danger border-danger border-opacity-25 btn-delete"
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

</div>

{{-- TOAST --}}
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

{{-- MODAL DETALHES --}}
<div class="modal fade" id="modalDetalhes" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">

            {{-- Header vermelho FEM --}}
            <div class="modal-header-fem d-flex justify-content-between align-items-center d-print-none">
                <div>
                    <h6 class="text-white fw-bold mb-0"><i class="bi bi-file-earmark-text me-2"></i>Ficha de Requisição</h6>
                    <span class="text-white opacity-75" style="font-size:.7rem;">Nº <span id="modal-id-header"></span></span>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4" id="printArea">

                {{-- Cabeçalho de impressão (visível só no print) --}}
                <div class="d-none d-print-block border-bottom pb-3 mb-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h4 class="fw-bold mb-0" style="color:#c60a1a;">REQUISIÇÃO DE COMPRA</h4>
                            <p class="mb-0 text-muted small">Nº: #<span class="modal-id-print"></span></p>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold" style="color:#c60a1a;">Fábrica de Explosivos de Moçambique</div>
                            <small class="text-muted">Av. Samora Machel Nº — Parcela 10 | +258 21 745 86/03</small>
                        </div>
                    </div>
                </div>

                {{-- Info topo ── --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="detail-label">Nº Registo</div>
                        <div class="detail-val">#<span id="modal-id"></span></div>
                    </div>
                    <div class="col-md-3">
                        <div class="detail-label">Data de Emissão</div>
                        <div class="detail-val" id="modal-data-atual"></div>
                    </div>
                    <div class="col-md-3">
                        <div class="detail-label">Solicitante</div>
                        <div class="detail-val" id="modal-solicitante-nome"></div>
                    </div>
                    <div class="col-md-3">
                        <div class="detail-label">Fornecedor Sugerido</div>
                        <div class="detail-val" id="modal-fornecedor"></div>
                    </div>
                </div>

                {{-- Observações --}}
                <div class="mb-4">
                    <div class="detail-label mb-2">Justificação / Observações</div>
                    <div class="obs-box" id="modal-obs"></div>
                </div>

                {{-- Tabela itens --}}
                <div class="detail-label mb-2" style="color:#c60a1a;border-bottom:2px solid #c60a1a;padding-bottom:6px;">
                    <i class="bi bi-box-seam me-1"></i> Itens da Solicitação
                </div>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-sm">
                        <thead style="background:#c60a1a;">
                            <tr class="text-center text-white" style="font-size:.7rem;letter-spacing:.6px;text-transform:uppercase;">
                                <th class="fw-bold" style="width:50%;">Material / Descrição</th>
                                <th class="fw-bold">Qtd</th>
                                <th class="fw-bold">Destino / Obra</th>
                            </tr>
                        </thead>
                        <tbody id="modal-tabela-itens"></tbody>
                    </table>
                </div>

                {{-- Assinatura --}}
                <div class="row mt-4 pt-3">
                    <div class="col-6 offset-6 text-center">
                        <div class="border-top pt-2">
                            <span class="detail-label">Assinatura Digital do Solicitante</span>
                        </div>
                        <div class="text-muted mt-1" style="font-size:.58rem;">
                            Documento gerado eletronicamente via Sistema de Gestão em {{ now()->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>

                {{-- Anexos --}}
                <div class="mt-4 d-print-none">
                    <div class="detail-label mb-2" style="color:#c60a1a;border-bottom:2px solid #c60a1a;padding-bottom:6px;">
                        <i class="bi bi-paperclip me-1"></i> Documentos Anexos
                    </div>
                    <div id="modal-anexos-lista" class="d-flex flex-wrap gap-2 mt-2 py-2"></div>
                </div>

            </div>

            <div class="modal-footer border-0 bg-light d-print-none" style="border-radius:0 0 16px 16px;">
                <button type="button" class="btn btn-sm btn-light px-4 fw-bold" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-sm fw-bold px-4 shadow-sm" onclick="window.print();"
                        style="background:#c60a1a;color:#fff;border:none;border-radius:8px;">
                    <i class="bi bi-printer me-1"></i> Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/v/bs5/dt-2.0.8/datatables.min.js"></script>

<script>
var _deleteFormId = null;

function showToast(){ var t=document.getElementById('toastSuccess'); t.classList.add('show'); setTimeout(closeToast,4000); }
function closeToast(){ document.getElementById('toastSuccess').classList.remove('show'); }

@if(session('deleted'))
    document.addEventListener('DOMContentLoaded', function(){ showToast(); });
@endif

$(document).on('click', '.btn-delete', function(){
    _deleteFormId = $(this).data('form');
    $('#confirmLabel').text($(this).data('label'));
    $('#confirmOverlay').addClass('open');
});

$('#confirmOkBtn').on('click', function(){
    if (_deleteFormId) $('#' + _deleteFormId).submit();
});

$('#confirmOverlay').on('click', function(e){ if (e.target === this) closeConfirm(); });
$(document).on('keydown', function(e){ if (e.key === 'Escape') closeConfirm(); });
function closeConfirm(){ $('#confirmOverlay').removeClass('open'); }

$(document).ready(function(){
    var table = $('#comprasTable').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/pt-BR.json' },
        order: [[0,'desc']],
        columnDefs: [{ orderable: false, targets: [3,4,7] }]
    });

    $.fn.dataTable.ext.search.push(function(settings, data){
        var min = $('#min-date').val();
        var max = $('#max-date').val();
        var dateStr = data[6];
        if (!dateStr) return true;
        var p = dateStr.split('/');
        var valDate = p[2] + p[1] + p[0];
        var minD = min ? min.replace(/-/g,'') : null;
        var maxD = max ? max.replace(/-/g,'') : null;
        if (!minD && !maxD) return true;
        if (minD && !maxD) return valDate >= minD;
        if (!minD && maxD) return valDate <= maxD;
        return valDate >= minD && valDate <= maxD;
    });

    $('#min-date, #max-date').on('change', function(){ table.draw(); });
    $('#clear-filters').on('click', function(){ $('#min-date, #max-date').val(''); table.draw(); });

    $(document).on('click', '.btn-show-details', function(){
        var btn = $(this);
        try {
            var id = btn.attr('data-id');
            $('#modal-id').text(id);
            $('#modal-id-header').text(id);
            $('.modal-id-print').text(id);
            $('#modal-solicitante-nome').text(btn.attr('data-solicitante'));
            $('#modal-fornecedor').text(btn.attr('data-fornecedor'));
            $('#modal-obs').text(btn.attr('data-obs'));
            $('#modal-data-atual').text(new Date().toLocaleDateString('pt-BR'));

            var itens  = JSON.parse(btn.attr('data-itens')  || '[]');
            var anexos = JSON.parse(btn.attr('data-anexos') || '[]');

            var htmlItens = '';
            itens.forEach(function(item){
                htmlItens +=
                    '<tr class="text-center" style="font-size:.8rem;">' +
                        '<td class="text-start ps-3">' + item.item_name + '</td>' +
                        '<td class="fw-bold">' + item.quantity + '</td>' +
                        '<td>' + (item.destino || '—') + '</td>' +
                    '</tr>';
            });
            $('#modal-tabela-itens').html(htmlItens || '<tr><td colspan="3" class="text-center text-muted">Sem itens</td></tr>');

            var htmlAnexos = '';
            if (anexos.length > 0) {
                anexos.forEach(function(doc){
                    htmlAnexos +=
                        '<a href="/storage/' + doc.file_path + '" target="_blank" ' +
                        'class="btn btn-sm px-3" ' +
                        'style="background:#fdf2f2;border:1px solid #fecaca;color:#c60a1a;border-radius:8px;font-size:.75rem;font-weight:600;">' +
                        '<i class="bi bi-file-earmark-pdf me-1"></i>' + doc.file_name + '</a>';
                });
            } else {
                htmlAnexos = '<span class="text-muted small">Nenhum documento anexado.</span>';
            }
            $('#modal-anexos-lista').html(htmlAnexos);
        } catch(e){
            console.error('Erro no Parse do Modal:', e);
        }
    });
});
</script>

</x-app-layout>
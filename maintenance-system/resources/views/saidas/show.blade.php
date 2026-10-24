<x-app-layout>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

@php
    $fmt     = fn ($n) => rtrim(rtrim(number_format((float) $n, 3, ',', '.'), '0'), ',');
    $fechada = $saida->isFechada();
    $stCor   = $fechada ? '#64748b' : '#198754';
@endphp

<style>
    .page-title{font-size:1.05rem;font-weight:700;color:#1e293b;margin:0}
    .page-sub{font-size:.72rem;color:#94a3b8;margin:2px 0 0}
    .painel{background:#fff;border:1px solid #e9ecef;border-radius:10px;padding:14px 16px}
    .painel h6{font-size:.72rem;font-weight:700;letter-spacing:.9px;text-transform:uppercase;color:#64748b;margin:0 0 10px}
    .kpi{background:#fff;border:1px solid #e9ecef;border-radius:10px;padding:10px 14px}
    .kpi .v{font-size:1.25rem;font-weight:700;color:#1e293b;line-height:1.1}
    .kpi .l{font-size:.6rem;font-weight:700;letter-spacing:.9px;text-transform:uppercase;color:#94a3b8}
    #tabCam th{font-size:.58rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#868e96;
               border-bottom:1px solid #e9ecef;padding:6px 8px;white-space:nowrap}
    #tabCam td{font-size:.75rem;padding:6px 8px;vertical-align:middle;border-bottom:1px solid #f1f3f5}
    #tabCam tr:hover td{background:#fafbff}
    .mat-badge{display:inline-block;background:#1e293b;color:#fff;border-radius:3px;font-size:.62rem;
               letter-spacing:1px;padding:1px 6px;font-weight:600}
    .mat-badge.atr{background:#475569}
    .atr-chip{display:inline-block;font-size:.55rem;font-weight:700;padding:1px 6px;border-radius:3px}
    .atr-chip.normal{background:#e8f0fe;color:#1a56db}.atr-chip.link{background:#fde8e8;color:#dc3545}
    .meta-chip{display:inline-block;background:#f1f3f5;color:#475569;border-radius:4px;font-size:.6rem;padding:1px 6px;margin:1px 2px 0 0}
    .estado-pill{font-size:.6rem;font-weight:700;color:#fff;border-radius:20px;padding:2px 9px}
    .aviso-peso{font-size:.62rem;color:#b45309;background:#fff8e1;border:1px solid #ffe08a;border-radius:4px;padding:1px 5px;margin-top:2px}
    .prod-linha{display:flex;justify-content:space-between;align-items:center;font-size:.74rem;padding:5px 0;border-bottom:1px dashed #eef1f4}
    .prod-linha:last-child{border-bottom:0}
    .stock-chip{display:inline-block;border-radius:4px;font-size:.68rem;font-weight:700;padding:1px 8px;
                background:#e6f4ec;color:#198754;border:1px solid #b7dfc7}
    .stock-chip.zero{background:#fde8e8;color:#dc3545;border-color:#f8c4c4}
    .res-verif{border-radius:8px;padding:8px 10px;font-size:.76rem;margin-top:8px}
    .res-verif.ok{background:#e6f4ec;border:1px solid #b7dfc7;color:#146c43}
    .res-verif.info{background:#e8f0fe;border:1px solid #bcd0f7;color:#1a56db}
    .res-verif.no{background:#fde8e8;border:1px solid #f8c4c4;color:#b02a37}
    .res-verif.aviso{background:#fff8e1;border:1px solid #ffe08a;color:#7a5b00}
    .dica{font-size:.68rem;color:#94a3b8}
</style>

<div class="container-fluid py-3 px-4">

    {{-- Cabeçalho --}}
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
        <div>
            <a href="{{ route('saidas.index') }}" class="dica text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Todas as saídas</a>
            <div class="page-title mt-1">
                <i class="bi bi-box-arrow-up me-2" style="color:{{ $stCor }};"></i>{{ $saida->codigo }} · {{ $saida->cliente }}
                <span class="estado-pill ms-2" style="background:{{ $stCor }};">{{ $fechada ? 'Fechada' : 'Aberta' }}</span>
            </div>
            <div class="page-sub">
                {{ $saida->data?->format('d/m/Y') }}
                · Origem:
                @if($op) <b>{{ $op->navio }}</b> · {{ $op->empresa }} @else <span class="text-danger">sem operação definida</span> @endif
                @if($saida->observacoes) · {{ $saida->observacoes }} @endif
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('saidas.relatorio', $saida) }}" class="btn btn-sm btn-outline-secondary fw-bold" style="font-size:.72rem;border-radius:7px;">
                <i class="bi bi-printer me-1"></i>Relatório
            </a>
            <a href="{{ route('saidas.relatorio', [$saida, 'formato' => 'xlsx']) }}" class="btn btn-sm btn-outline-success fw-bold" style="font-size:.72rem;border-radius:7px;">
                <i class="bi bi-file-earmark-excel me-1"></i>Excel
            </a>
            @unless($fechada)
                <button type="button" class="btn btn-sm btn-outline-primary fw-bold" style="font-size:.72rem;border-radius:7px;" onclick="abrirModal('modalEditar')">
                    <i class="bi bi-pencil me-1"></i>Editar
                </button>
                <form action="{{ route('saidas.fechar', $saida) }}" method="POST" class="m-0"
                      onsubmit="return confirm('Fechar a saída? Deixa de aceitar carregamentos.')">
                    @csrf @method('PATCH')
                    <button class="btn btn-sm btn-dark fw-bold" style="font-size:.72rem;border-radius:7px;"><i class="bi bi-lock me-1"></i>Fechar</button>
                </form>
            @else
                <form action="{{ route('saidas.reabrir', $saida) }}" method="POST" class="m-0">
                    @csrf @method('PATCH')
                    <button class="btn btn-sm btn-outline-dark fw-bold" style="font-size:.72rem;border-radius:7px;"><i class="bi bi-unlock me-1"></i>Reabrir</button>
                </form>
            @endunless
            @if($kpi['carregados'] === 0)
                <form action="{{ route('saidas.destroy', $saida) }}" method="POST" class="m-0"
                      onsubmit="return confirm('Apagar esta saída e os camiões registados?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger" style="font-size:.72rem;border-radius:7px;" title="Apagar"><i class="bi bi-trash3"></i></button>
                </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success py-2" style="font-size:.78rem;border-radius:8px;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger py-2" style="font-size:.78rem;border-radius:8px;">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger py-2" style="font-size:.78rem;border-radius:8px;">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif
    @if(session('import_erros'))
        <div class="alert alert-warning py-2" style="font-size:.76rem;border-radius:8px;">
            <div class="fw-bold mb-1">Linhas ignoradas no ficheiro:</div>
            @foreach(session('import_erros') as $e)
                <div>Linha {{ $e['linha'] }}: {{ $e['msg'] }}</div>
            @endforeach
        </div>
    @endif

    {{-- KPIs --}}
    <div class="row g-2 mb-3">
        <div class="col-6 col-md-2"><div class="kpi"><div class="v">{{ $kpi['registados'] }}</div><div class="l">Registados</div></div></div>
        <div class="col-6 col-md-2"><div class="kpi"><div class="v" style="color:#198754;">{{ $kpi['carregados'] }}</div><div class="l">Carregados</div></div></div>
        <div class="col-6 col-md-2"><div class="kpi"><div class="v" style="color:#0d6efd;">{{ $kpi['em_carga'] }}</div><div class="l">Em carga</div></div></div>
        <div class="col-6 col-md-2"><div class="kpi"><div class="v" style="color:#d39e00;">{{ $kpi['pendentes'] }}</div><div class="l">Por carregar</div></div></div>
    </div>

    <div class="row g-3">

        {{-- ───────── ESQUERDA: camiões registados ───────── --}}
        <div class="col-lg-8">
            <div class="painel">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                    <h6 class="mb-0">Camiões registados</h6>
                    <form method="GET" action="{{ route('saidas.show', $saida) }}" class="d-flex gap-2">
                        <input type="text" name="q" value="{{ $q }}" class="form-control form-control-sm" style="width:170px;font-size:.72rem;" placeholder="Matrícula, motorista…">
                        <select name="estado" class="form-select form-select-sm" style="width:130px;font-size:.72rem;" onchange="this.form.submit()">
                            <option value="">Todos</option>
                            <option value="pendente" @selected($estado === 'pendente')>Por carregar</option>
                            <option value="em_carga" @selected($estado === 'em_carga')>Em carga</option>
                            <option value="carregada" @selected($estado === 'carregada')>Carregados</option>
                        </select>
                        <button class="btn btn-sm btn-dark" style="font-size:.72rem;"><i class="bi bi-search"></i></button>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm mb-0" id="tabCam">
                        <thead>
                            <tr>
                                <th>#</th><th>Matrícula</th><th>Atrelados</th><th>Empresa</th><th>Motorista</th>
                                <th>Outros dados</th><th>Produto</th><th class="text-end">Pedido</th>
                                <th class="text-end">Carregado</th><th>Pesos (kg)</th><th>Estado</th><th></th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($camioes as $c)
                            @php
                                $it  = $itens[$c->operation_item_id] ?? null;
                                $car = $c->status === 'carregada';
                                $emc = $c->status === 'em_carga';
                                $atr = $atrelados[$c->id] ?? collect();
                            @endphp
                            <tr>
                                <td class="fw-bold text-muted">{{ $c->id }}</td>
                                <td><span class="mat-badge">{{ $c->matricula }}</span></td>
                                <td>
                                    @if($c->tipo_atrelado)<span class="atr-chip {{ $c->tipo_atrelado }}">{{ strtoupper($c->tipo_atrelado) }}</span>@endif
                                    @foreach($atr as $a)<span class="mat-badge atr">{{ $a->matricula }}</span> @endforeach
                                    @if(! $c->tipo_atrelado && $atr->isEmpty()) — @endif
                                </td>
                                <td>{{ $c->empresa }}</td>
                                <td class="fw-semibold">{{ $c->nome_completo }}</td>
                                <td>
                                    @foreach(($c->metadata ?? []) as $k => $v)
                                        <span class="meta-chip"><b>{{ $k }}:</b> {{ $v }}</span>
                                    @endforeach
                                </td>
                                <td>{{ $it->nome ?? '—' }}</td>
                                <td class="text-end">{{ $fmt($c->quantidade_pedida ?? $c->quantidade) }}</td>
                                <td class="text-end fw-bold" style="color:#198754;">{{ $car ? $fmt($c->quantidade) : '—' }}</td>
                                <td style="white-space:nowrap;">
                                    @if($c->peso_vazio !== null)<div class="dica">Vazio: <b>{{ $fmt($c->peso_vazio) }}</b></div>@endif
                                    @if($c->peso_cheio !== null)<div class="dica">Cheio: <b>{{ $fmt($c->peso_cheio) }}</b></div>@endif
                                    @if($c->peso_liquido !== null)<div class="dica">Líquido: <b style="color:#198754;">{{ $fmt($c->peso_liquido) }}</b></div>@endif
                                    @if(isset($avisos[$c->id]))<div class="aviso-peso">⚠ {{ $avisos[$c->id] }}</div>@endif
                                    @if($c->peso_vazio === null) — @endif
                                </td>
                                <td>
                                    @if($car)
                                        <span class="estado-pill" style="background:#198754;">Carregado</span>
                                        <div class="dica">{{ $c->carregado_em?->format('d/m H:i') }}{{ $c->carregadoPor ? ' · ' . $c->carregadoPor->name : '' }}</div>
                                    @elseif($emc)
                                        <span class="estado-pill" style="background:#0d6efd;">Em carga</span>
                                        <div class="dica">{{ $c->ticket_numero }}</div>
                                    @else
                                        <span class="estado-pill" style="background:#d39e00;">Por carregar</span>
                                    @endif
                                </td>
                                <td class="text-end text-nowrap">
                                    @if($c->ticket_numero)
                                        <a href="{{ route('saidas.camioes.ticket', $c) }}" target="_blank" class="btn btn-sm btn-outline-secondary py-0" style="font-size:.62rem;" title="Ticket {{ $c->ticket_numero }}">
                                            <i class="bi bi-ticket-perforated"></i>
                                        </a>
                                    @endif
                                    @unless($fechada)
                                        @if($car)
                                            <form action="{{ route('saidas.camioes.devolver', $c) }}" method="POST" class="m-0 d-inline"
                                                  onsubmit="return confirm('Desfazer a 2ª pesagem e repor o stock?')">
                                                @csrf @method('PATCH')
                                                <button class="btn btn-sm btn-outline-secondary py-0" style="font-size:.62rem;">
                                                    <i class="bi bi-arrow-counterclockwise"></i> Devolver
                                                </button>
                                            </form>
                                        @elseif($emc)
                                            <button type="button" class="btn btn-sm btn-success py-0 fw-bold" style="font-size:.64rem;" onclick="abrirCheio({{ $c->id }})">
                                                <i class="bi bi-speedometer2 me-1"></i>2ª pesagem
                                            </button>
                                            <form action="{{ route('saidas.camioes.anular', $c) }}" method="POST" class="m-0 d-inline"
                                                  onsubmit="return confirm('Anular a 1ª pesagem? Apaga o peso vazio, os atrelados e o ticket.')">
                                                @csrf @method('PATCH')
                                                <button class="btn btn-sm text-danger p-0 ms-1" title="Anular 1ª pesagem"><i class="bi bi-x-circle"></i></button>
                                            </form>
                                        @else
                                            <button type="button" class="btn btn-sm btn-primary py-0 fw-bold" style="font-size:.64rem;" onclick="abrirVazio({{ $c->id }})">
                                                <i class="bi bi-speedometer2 me-1"></i>1ª pesagem
                                            </button>
                                            <form action="{{ route('saidas.camioes.destroy', $c) }}" method="POST" class="m-0 d-inline"
                                                  onsubmit="return confirm('Remover este camião da lista?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm text-danger p-0 ms-1" title="Remover"><i class="bi bi-trash3"></i></button>
                                            </form>
                                        @endif
                                    @endunless
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="12" class="text-center text-muted py-4" style="font-size:.78rem;">
                                @if($kpi['registados'] === 0)
                                    Ainda não há camiões registados. Faça o upload do Excel ou registe um à mão.
                                @else
                                    Nenhum camião corresponde ao filtro.
                                @endif
                            </td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ───────── DIREITA: verificar, registar, stock ───────── --}}
        <div class="col-lg-4">

            {{-- Verificar matrícula --}}
            <div class="painel mb-3">
                <h6>Verificar matrícula</h6>
                <div class="input-group input-group-sm">
                    <input type="text" id="verMat" class="form-control text-uppercase" placeholder="Matrícula do camião à porta" autocomplete="off">
                    <button type="button" class="btn btn-dark" onclick="verificar()"><i class="bi bi-search"></i></button>
                </div>
                <div id="verRes"></div>
                <div class="dica mt-2"><i class="bi bi-shield-check me-1"></i>Camião que não estiver na lista não carrega.</div>
            </div>

            @unless($fechada)
            {{-- Excel --}}
            <div class="painel mb-3">
                <h6>Registar camiões por Excel</h6>
                <a href="{{ route('saidas.modelo', $saida) }}" class="btn btn-sm btn-outline-success fw-bold w-100 mb-2" style="font-size:.72rem;border-radius:7px;">
                    <i class="bi bi-file-earmark-excel me-1"></i>Descarregar modelo
                </a>
                <form action="{{ route('saidas.importar', $saida) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="ficheiro" accept=".xlsx,.xls" class="form-control form-control-sm mb-2" required>
                    <button type="submit" class="btn btn-sm btn-success fw-bold w-100" style="font-size:.72rem;border-radius:7px;">
                        <i class="bi bi-upload me-1"></i>Carregar ficheiro
                    </button>
                </form>
                <div class="dica mt-2">Uma linha por camião. Linhas com erro são ignoradas e listadas; as restantes entram.</div>
            </div>

            {{-- Registar à mão --}}
            <div class="painel mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Registar um camião</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary fw-bold" style="font-size:.68rem;border-radius:7px;" onclick="toggleForm()">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>
                <div id="formCamiao" class="mt-3 {{ $errors->any() && old('matricula') !== null ? '' : 'd-none' }}">
                    <form action="{{ route('saidas.camioes.store', $saida) }}" method="POST">
                        @csrf
                        <div class="row g-2">
                            <div class="col-7">
                                <label class="small fw-bold text-muted">Matrícula *</label>
                                <input type="text" name="matricula" class="form-control form-control-sm text-uppercase" value="{{ old('matricula') }}" required>
                            </div>
                            <div class="col-5">
                                <label class="small fw-bold text-muted">Atrelado</label>
                                <select name="tipo_atrelado" class="form-select form-select-sm">
                                    <option value="">—</option>
                                    <option value="normal" @selected(old('tipo_atrelado') === 'normal')>Normal</option>
                                    <option value="link" @selected(old('tipo_atrelado') === 'link')>Link</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="small fw-bold text-muted">Nome *</label>
                                <input type="text" name="nome" class="form-control form-control-sm" value="{{ old('nome') }}" required>
                            </div>
                            <div class="col-6">
                                <label class="small fw-bold text-muted">Apelido</label>
                                <input type="text" name="apelido" class="form-control form-control-sm" value="{{ old('apelido') }}">
                            </div>
                            <div class="col-12">
                                <label class="small fw-bold text-muted">Empresa</label>
                                <input type="text" name="empresa" class="form-control form-control-sm" value="{{ old('empresa') }}" placeholder="{{ $saida->cliente }}">
                            </div>
                            <div class="col-7">
                                <label class="small fw-bold text-muted">Produto</label>
                                <select name="operation_item_id" class="form-select form-select-sm">
                                    <option value="">— escolher ao carregar —</option>
                                    @foreach($itens as $it)
                                        <option value="{{ $it->id }}" @selected(old('operation_item_id') == $it->id)>{{ $it->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-5">
                                <label class="small fw-bold text-muted">Quantidade *</label>
                                <input type="number" name="quantidade" class="form-control form-control-sm" step="0.001" min="0.001" value="{{ old('quantidade') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="small fw-bold text-muted">Outros dados</label>
                                <div id="metaBox"></div>
                                <button type="button" class="btn btn-sm btn-link p-0" style="font-size:.7rem;" onclick="addMeta()">
                                    <i class="bi bi-plus-circle me-1"></i>Acrescentar campo
                                </button>
                            </div>
                        </div>
                        <div class="text-end mt-2">
                            <button type="submit" class="btn btn-primary btn-sm fw-bold px-4" style="border-radius:7px;">
                                <i class="bi bi-check-lg me-1"></i>Registar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endunless

            {{-- Stock da operação --}}
            <div class="painel">
                <h6>Stock da operação{{ $op ? ' — ' . $op->navio : '' }}</h6>
                @forelse($stock as $r)
                    <div class="prod-linha">
                        <div>
                            <span class="fw-semibold">{{ $r->item->nome }}</span>
                            <div class="dica">recebido {{ $fmt($r->recebido) }} · saiu {{ $fmt($r->saiu) }}
                                @if(($nestaSaida[$r->item->id] ?? 0) > 0) · <b>nesta saída {{ $fmt($nestaSaida[$r->item->id]) }}</b>@endif
                            </div>
                        </div>
                        <span class="stock-chip {{ $r->stock <= 0 ? 'zero' : '' }}">{{ $fmt($r->stock) }} {{ $r->item->unidade_label }}</span>
                    </div>
                @empty
                    <div class="text-muted small">Sem operação de origem ou sem produtos.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- MODAL: 1ª pesagem (vazio) --}}
<div class="modal fade" id="modalVazio" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:14px;overflow:hidden;">
            <div class="modal-header bg-primary text-white border-0" style="padding:12px 20px;">
                <h5 class="modal-title fw-bold" style="font-size:.88rem;"><i class="bi bi-speedometer2 me-2"></i>1ª pesagem · camião vazio</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="pvErros" class="alert alert-danger py-2 small d-none"></div>
                <div class="mb-3" style="font-size:.78rem;">
                    <div class="small text-muted fw-bold">Camião</div>
                    <div id="pvCamiao" class="fw-semibold"></div>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold text-muted">Peso vazio (kg)</label>
                    <div class="input-group input-group-sm">
                        <input type="number" id="pvPeso" class="form-control" min="0" step="0.001" placeholder="Peso da balança">
                        <button type="button" class="btn btn-dark" onclick="lerBalanca('pvPeso', this)">
                            <i class="bi bi-speedometer2 me-1"></i>Ler balança
                        </button>
                    </div>
                </div>
                <div class="mb-1">
                    <label class="small fw-bold text-muted">Atrelados</label>
                    <div id="atrBox"></div>
                    <button type="button" class="btn btn-sm btn-link p-0" style="font-size:.7rem;" onclick="addAtrelado()">
                        <i class="bi bi-plus-circle me-1"></i>Acrescentar atrelado
                    </button>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0" style="padding:0 20px 16px;">
                <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="pvGuardar" class="btn btn-primary btn-sm px-3 fw-bold" onclick="enviarVazio(false)">
                    <i class="bi bi-check-lg me-1"></i>Guardar
                </button>
                <button type="button" id="pvImprimir" class="btn btn-outline-primary btn-sm px-3 fw-bold" onclick="enviarVazio(true)">
                    <i class="bi bi-ticket-perforated me-1"></i>Guardar e imprimir ticket
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: 2ª pesagem (cheio) + saída de carga --}}
<div class="modal fade" id="modalCheio" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:14px;overflow:hidden;">
            <div class="modal-header bg-success text-white border-0" style="padding:12px 20px;">
                <h5 class="modal-title fw-bold" style="font-size:.88rem;"><i class="bi bi-speedometer2 me-2"></i>2ª pesagem · camião cheio</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="chErros" class="alert alert-danger py-2 small d-none"></div>
                <div class="row g-2 mb-3" style="font-size:.78rem;">
                    <div class="col-7">
                        <div class="small text-muted fw-bold">Camião</div>
                        <div id="chCamiao" class="fw-semibold"></div>
                    </div>
                    <div class="col-5">
                        <div class="small text-muted fw-bold">Origem</div>
                        <div class="fw-semibold">{{ $op->navio ?? '—' }}</div>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="small fw-bold text-muted">Peso cheio (kg)</label>
                    <div class="input-group input-group-sm">
                        <input type="number" id="chPeso" class="form-control" min="0" step="0.001" placeholder="Peso da balança" oninput="chAtualizar(true)">
                        <button type="button" class="btn btn-dark" onclick="lerBalanca('chPeso', this)">
                            <i class="bi bi-speedometer2 me-1"></i>Ler balança
                        </button>
                    </div>
                    <div id="chLiquido" class="small mt-1"></div>
                </div>
                <div class="mb-2">
                    <label class="small fw-bold text-muted">Produto</label>
                    <select id="chProduto" class="form-select form-select-sm" onchange="chAtualizar()"></select>
                </div>
                <div class="mb-2">
                    <label class="small fw-bold text-muted">Quantidade a sair <span id="chUnidade" class="fw-normal"></span></label>
                    <input type="number" id="chQtd" class="form-control form-control-sm" min="0" step="0.001" oninput="chAtualizar(true)">
                    <div id="chDisp" class="small mt-1"></div>
                </div>
                <div id="chAviso"></div>
                <div class="mb-1 mt-2">
                    <label class="small fw-bold text-muted">Observações</label>
                    <textarea id="chObs" class="form-control form-control-sm" rows="2" placeholder="Opcional"></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0" style="padding:0 20px 16px;">
                <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="chConfirmar" class="btn btn-success btn-sm px-3 fw-bold" onclick="enviarCheio(false)">
                    <i class="bi bi-check-circle me-1"></i>Confirmar saída
                </button>
                <button type="button" id="chImprimir" class="btn btn-outline-success btn-sm px-3 fw-bold" onclick="enviarCheio(true)">
                    <i class="bi bi-ticket-perforated me-1"></i>Confirmar e imprimir ticket
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: editar saída --}}
@unless($fechada)
<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('saidas.update', $saida) }}" method="POST" class="modal-content border-0 shadow-lg" style="border-radius:14px;overflow:hidden;">
            @csrf @method('PUT')
            <div class="modal-header bg-primary text-white border-0" style="padding:12px 20px;">
                <h5 class="modal-title fw-bold" style="font-size:.88rem;"><i class="bi bi-pencil me-2"></i>Editar {{ $saida->codigo }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-2">
                    <label class="small fw-bold text-muted">Cliente / empresa</label>
                    <input type="text" name="cliente" class="form-control form-control-sm" value="{{ $saida->cliente }}" required>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-5">
                        <label class="small fw-bold text-muted">Data</label>
                        <input type="date" name="data" class="form-control form-control-sm" value="{{ $saida->data?->format('Y-m-d') }}" required>
                    </div>
                    <div class="col-7">
                        <label class="small fw-bold text-muted">Operação de origem</label>
                        <select name="operation_id" class="form-select form-select-sm" @disabled($kpi['carregados'] > 0)>
                            <option value="">— sem operação —</option>
                            @foreach($operationsAbertas as $o)
                                <option value="{{ $o->id }}" @selected($saida->operation_id == $o->id)>{{ $o->navio }} · {{ $o->empresa }}</option>
                            @endforeach
                        </select>
                        @if($kpi['carregados'] > 0)
                            <input type="hidden" name="operation_id" value="{{ $saida->operation_id }}">
                            <div class="dica">Bloqueada: já há camiões carregados.</div>
                        @endif
                    </div>
                </div>
                <div class="mb-1">
                    <label class="small fw-bold text-muted">Observações</label>
                    <textarea name="observacoes" class="form-control form-control-sm" rows="2">{{ $saida->observacoes }}</textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0" style="padding:0 20px 16px;">
                <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold"><i class="bi bi-check-lg me-1"></i>Guardar</button>
            </div>
        </form>
    </div>
</div>
@endunless

<script>
const CAMIOES = @json($camioesJs);
const ITENS   = @json($itensJs);
const URL_VAZIO = @json(route('saidas.camioes.vazio', ['camiao' => '__ID__']));
const URL_CHEIO = @json(route('saidas.camioes.cheio', ['camiao' => '__ID__']));
const CSRF = @json(csrf_token());
const FECHADA = @json($fechada);
const PESO_SACO = @json((float) config('saidas.peso_saco_kg'));
const TOLERANCIA = @json((float) config('saidas.tolerancia_pct'));
const BALANCA = @json(config('saidas.balanca'));

let pesCamId = null;
const fmtNum = n => Number(n).toLocaleString('pt-PT', { maximumFractionDigits: 3 });
const abrirModal = id => bootstrap.Modal.getOrCreateInstance(document.getElementById(id)).show();

/* ── Registar um camião à mão ── */
function toggleForm() { document.getElementById('formCamiao').classList.toggle('d-none'); }

function addMeta() {
    const box = document.getElementById('metaBox');
    const row = document.createElement('div');
    row.className = 'd-flex gap-2 mb-1';
    row.innerHTML = '<input type="text" name="meta_k[]" class="form-control form-control-sm" placeholder="Campo (ex.: Telefone)">' +
                    '<input type="text" name="meta_v[]" class="form-control form-control-sm" placeholder="Valor">';
    box.appendChild(row);
}

/* ── Verificar matrícula: só carrega quem estiver registado, uma vez por lista ── */
const normMat = s => (s || '').toUpperCase().replace(/\s+/g, '');

function verificar() {
    const v = normMat(document.getElementById('verMat').value);
    const out = document.getElementById('verRes');
    if (!v) { out.innerHTML = ''; return; }

    const achados = Object.values(CAMIOES).filter(c => normMat(c.matricula) === v);
    if (!achados.length) {
        out.innerHTML = '<div class="res-verif no"><b><i class="bi bi-x-octagon me-1"></i>NÃO REGISTADO</b><br>Este camião não está na lista desta saída. Não pode carregar.</div>';
        return;
    }
    const c = achados[0];
    if (c.status === 'pendente') {
        out.innerHTML = '<div class="res-verif ok"><b><i class="bi bi-check-circle me-1"></i>Registado</b> · ' + c.nome +
            ' · pedido ' + fmtNum(c.pedido) +
            (FECHADA ? '' : '<div class="mt-2"><button type="button" class="btn btn-sm btn-primary fw-bold py-0" onclick="abrirVazio(' + c.id + ')">1ª pesagem</button></div>') + '</div>';
    } else if (c.status === 'em_carga') {
        out.innerHTML = '<div class="res-verif info"><b><i class="bi bi-hourglass-split me-1"></i>Em carga</b> · ' + c.nome +
            ' · vazio ' + fmtNum(c.peso_vazio) + ' kg' +
            (FECHADA ? '' : '<div class="mt-2"><button type="button" class="btn btn-sm btn-success fw-bold py-0" onclick="abrirCheio(' + c.id + ')">2ª pesagem</button></div>') + '</div>';
    } else {
        out.innerHTML = '<div class="res-verif aviso"><b><i class="bi bi-exclamation-triangle me-1"></i>Já carregou</b> em ' + (c.carregado_em || '') +
            '.<br>Cada camião carrega uma vez por lista.</div>';
    }
}
document.getElementById('verMat').addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); verificar(); } });

/* ── Leitura da balança (Web Serial, Chrome/Edge) ── */
async function lerBalanca(inputId, btn) {
    if (!('serial' in navigator)) {
        alert('A leitura da balança só funciona no Chrome ou Edge. Escreva o peso à mão.');
        return;
    }
    const original = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>A ler…';
    try {
        const porta = (await navigator.serial.getPorts())[0] || await navigator.serial.requestPort();
        await porta.open({ baudRate: BALANCA.baud });

        const reader = porta.readable.getReader();
        const dec = new TextDecoder();
        let buf = '';
        const timer = setTimeout(() => reader.cancel().catch(() => {}), BALANCA.espera_ms);
        try {
            while (true) {
                const { value, done } = await reader.read();
                if (done) break;
                buf += dec.decode(value, { stream: true });
            }
        } finally {
            clearTimeout(timer);
            reader.releaseLock();
            await porta.close();
        }

        // última linha completa com um número
        const linhas = buf.split(/\r?\n/);
        if (!/[\r\n]$/.test(buf)) linhas.pop();
        const nums = linhas.map(l => l.match(/\d+(?:[.,]\d+)?/)).filter(Boolean);
        if (!nums.length) throw new Error('Sem leitura da balança.');

        const inp = document.getElementById(inputId);
        inp.value = parseFloat(nums[nums.length - 1][0].replace(',', '.'));
        inp.dispatchEvent(new Event('input'));
    } catch (e) {
        alert('Não foi possível ler a balança: ' + (e.message || e));
    } finally {
        btn.disabled = false;
        btn.innerHTML = original;
    }
}

/* ── Envio genérico (fetch). Se "imprimir", abre o ticket depois de gravar ── */
function enviar(url, fd, errosId, botoes, imprimir) {
    const erros = document.getElementById(errosId);
    erros.classList.add('d-none');

    const btns = botoes.map(id => document.getElementById(id));
    const originais = btns.map(b => b.innerHTML);
    const restaurar = () => btns.forEach((b, i) => { b.disabled = false; b.innerHTML = originais[i]; });
    btns.forEach(b => { b.disabled = true; });
    btns[imprimir ? 1 : 0].innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>A guardar...';

    // abre já a janela do ticket (o browser só permite com clique do utilizador)
    const janela = imprimir ? window.open('', '_blank') : null;

    fetch(url, {
        method: 'POST',
        body: fd,
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(async r => {
        let j = null;
        try { j = await r.json(); } catch (e) {}
        if (r.ok) {
            if (janela && j && j.ticket) janela.location = j.ticket + '?print=1';
            window.location.reload();
            return;
        }
        if (janela) janela.close();
        let msgs = ['Erro ' + r.status];
        if (j) msgs = j.errors ? Object.values(j.errors).flat() : [j.message || msgs[0]];
        erros.innerHTML = msgs.map(m => '<div>' + m + '</div>').join('');
        erros.classList.remove('d-none');
        restaurar();
    })
    .catch(() => {
        if (janela) janela.close();
        erros.textContent = 'Sem ligação ao servidor. Tente novamente.';
        erros.classList.remove('d-none');
        restaurar();
    });
}

/* ── 1ª pesagem (vazio) ── */
function abrirVazio(id) {
    const cam = CAMIOES[id];
    if (!cam || cam.status !== 'pendente') return;
    pesCamId = id;

    document.getElementById('pvErros').classList.add('d-none');
    document.getElementById('pvCamiao').textContent = cam.matricula + ' · ' + cam.nome;
    document.getElementById('pvPeso').value = '';

    document.getElementById('atrBox').innerHTML = '';
    addAtrelado();
    if (cam.atrelado === 'link') addAtrelado();

    abrirModal('modalVazio');
}

function addAtrelado() {
    const row = document.createElement('div');
    row.className = 'input-group input-group-sm mb-1';
    row.innerHTML = '<input type="text" class="form-control text-uppercase" placeholder="Matrícula do atrelado" autocomplete="off">' +
                    '<button type="button" class="btn btn-outline-secondary" onclick="this.parentElement.remove()"><i class="bi bi-x"></i></button>';
    document.getElementById('atrBox').appendChild(row);
}

function enviarVazio(imprimir) {
    const fd = new FormData();
    fd.append('_token', CSRF);
    fd.append('_method', 'PATCH');
    fd.append('peso_vazio', document.getElementById('pvPeso').value);
    document.querySelectorAll('#atrBox input').forEach(i => fd.append('atrelados[]', i.value));

    enviar(URL_VAZIO.replace('__ID__', pesCamId), fd, 'pvErros', ['pvGuardar', 'pvImprimir'], imprimir);
}

/* ── 2ª pesagem (cheio) ── */
function abrirCheio(id) {
    const cam = CAMIOES[id];
    if (!cam || cam.status !== 'em_carga') return;
    pesCamId = id;

    document.getElementById('chErros').classList.add('d-none');
    document.getElementById('chCamiao').textContent = cam.matricula + ' · ' + cam.nome + ' · vazio ' + fmtNum(cam.peso_vazio) + ' kg';
    document.getElementById('chPeso').value = '';
    document.getElementById('chObs').value = '';

    const sel = document.getElementById('chProduto');
    sel.innerHTML = '';
    ITENS.forEach(it => {
        const o = document.createElement('option');
        o.value = it.id;
        o.textContent = it.nome + ' — em armazém ' + fmtNum(it.disponivel) + ' ' + it.label;
        o.disabled = it.disponivel <= 0;
        sel.appendChild(o);
    });
    // produto previsto (se tiver stock) ou o primeiro com stock
    const previsto = ITENS.find(it => it.id == cam.item && it.disponivel > 0);
    const escolhido = previsto || ITENS.find(it => it.disponivel > 0);
    if (escolhido) sel.value = escolhido.id;

    document.getElementById('chQtd').value = Math.min(cam.pedido, escolhido ? escolhido.disponivel : 0) || '';
    chAtualizar(true);
    abrirModal('modalCheio');
}

function chItemActual() {
    return ITENS.find(i => i.id == document.getElementById('chProduto').value) || null;
}

function chAtualizar(manterQtd) {
    const cam = CAMIOES[pesCamId];
    const it = chItemActual();
    if (!cam || !it) return;

    const inp = document.getElementById('chQtd');
    inp.max  = it.disponivel;
    inp.step = it.unidade === 'saco' ? 1 : 0.001;
    document.getElementById('chUnidade').textContent = '(' + it.label + ')';
    if (!manterQtd) inp.value = Math.min(cam.pedido, it.disponivel) || '';

    const q = parseFloat(inp.value) || 0;
    const excede = q > it.disponivel;
    document.getElementById('chDisp').innerHTML =
        '<span style="color:' + ((it.disponivel > 0 && !excede) ? '#198754' : '#dc3545') + ';">' +
        'Em armazém nesta operação: <b>' + fmtNum(it.disponivel) + ' ' + it.label + '</b>' +
        (excede ? ' — a quantidade excede o stock' : '') + '</span>';

    // peso líquido e aviso de diferença
    const cheio = parseFloat(document.getElementById('chPeso').value) || 0;
    const liq = cheio - cam.peso_vazio;
    const boxLiq = document.getElementById('chLiquido');
    const boxAviso = document.getElementById('chAviso');
    boxAviso.innerHTML = '';

    if (!cheio) { boxLiq.innerHTML = ''; return; }
    if (liq <= 0) {
        boxLiq.innerHTML = '<span style="color:#dc3545;">O peso cheio tem de ser maior que o vazio (' + fmtNum(cam.peso_vazio) + ' kg).</span>';
        return;
    }
    boxLiq.innerHTML = 'Peso líquido: <b>' + fmtNum(liq) + ' kg</b>';

    if (it.unidade === 'saco' && q > 0 && PESO_SACO > 0) {
        const esperado = q * PESO_SACO;
        const dif = (liq - esperado) / esperado * 100;
        if (Math.abs(dif) > TOLERANCIA) {
            boxAviso.innerHTML = '<div class="res-verif aviso mt-1"><b><i class="bi bi-exclamation-triangle me-1"></i>Diferença grande</b><br>' +
                'Líquido ' + fmtNum(liq) + ' kg vs ' + fmtNum(esperado) + ' kg esperados (' + q + ' sacos × ' + PESO_SACO + ' kg): ' +
                (dif > 0 ? '+' : '') + dif.toFixed(1) + '%. Confirme os sacos antes de gravar.</div>';
        }
    }
}

function enviarCheio(imprimir) {
    const fd = new FormData();
    fd.append('_token', CSRF);
    fd.append('_method', 'PATCH');
    fd.append('peso_cheio', document.getElementById('chPeso').value);
    fd.append('operation_item_id', document.getElementById('chProduto').value);
    fd.append('quantidade', document.getElementById('chQtd').value);
    fd.append('observacoes', document.getElementById('chObs').value);

    enviar(URL_CHEIO.replace('__ID__', pesCamId), fd, 'chErros', ['chConfirmar', 'chImprimir'], imprimir);
}
</script>

</x-app-layout>

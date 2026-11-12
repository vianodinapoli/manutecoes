<x-app-layout>

    @include('components.skeleton-loading')
    @include('dashboard._estilos')

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        .ct-badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:.68rem; font-weight:600; white-space:nowrap; }
        .ct-badge.no_parque    { background:#eff6ff; color:#1d4ed8; }
        .ct-badge.em_transito  { background:#fffbeb; color:#b45309; }
        .ct-badge.descarregado { background:#ecfeff; color:#0e7490; }
        .ct-badge.carregado    { background:#f0fdf4; color:#15803d; }
        .ct-badge.vazio        { background:#f1f5f9; color:#475569; }
        .ct-badge.saiu         { background:#e2e8f0; color:#334155; }
        .ct-btn { background:#1e293b; color:#fff; border:0; border-radius:8px; padding:7px 16px; font-size:.78rem; font-weight:600; text-decoration:none; }
        .ct-btn:hover { background:#0f172a; color:#fff; }
        .ct-btn-sec { background:#fff; color:#64748b; border:1px solid #e2e8f0; border-radius:8px; padding:7px 16px; font-size:.78rem; font-weight:600; }
        .ct-btn-sec:hover { background:#f8fafc; color:#1e293b; }
        .ct-act { border:1px solid #e2e8f0; background:#fff; color:#64748b; border-radius:7px; padding:4px 9px; font-size:.75rem; }
        .ct-act:hover { background:#f8fafc; color:#1e293b; }
        .ct-act.danger:hover { background:#fef2f2; color:#dc2626; border-color:#fecaca; }
        .ct-filtro { font-size:.78rem; border:1px solid #e2e8f0; border-radius:8px; padding:5px 10px; color:#475569; background:#fff; }

        /* Modal */
        #modalContentor .modal-content { border:0; border-radius:14px; overflow:hidden; }
        #modalContentor .modal-header { border-bottom:1px solid #f1f5f9; padding:16px 20px; }
        #modalContentor .modal-footer { border-top:1px solid #f1f5f9; padding:14px 20px; }
        .ct-sec { font-size:.6rem; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:#94a3b8; margin:4px 0 10px; }
        .ct-lbl { font-size:.72rem; font-weight:600; color:#475569; margin-bottom:4px; }
        .ct-in { font-size:.85rem; border-radius:8px; border-color:#e2e8f0; }
        .ct-in:focus { border-color:#1e293b; box-shadow:0 0 0 3px rgba(30,41,59,.08); }
    </style>

<div class="container-fluid py-4 px-4">

    {{-- CABEÇALHO --}}
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <div style="font-size:.6rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">
                Painel de Controlo
            </div>
            <h4 class="fw-bold mb-1" style="color:#1e293b;font-size:1.3rem;">Contentores</h4>
            <p style="font-size:.75rem;color:#94a3b8;margin:0;">Gestão do parque: o que está no recinto, em trânsito e há quantos dias</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <button type="button" id="btnNovo" class="ct-btn"><i class="bi bi-plus-lg me-1"></i> Novo contentor</button>
            <div style="font-size:.72rem;color:#94a3b8;background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:6px 14px;">
                <i class="bi bi-calendar3 me-1"></i> {{ now()->format('d M, Y') }}
            </div>
        </div>
    </div>

    {{-- KPI CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="kpi-card blue">
                <div class="kpi-icon blue"><i class="bi bi-box-seam-fill"></i></div>
                <div>
                    <div class="kpi-label">No parque</div>
                    <div class="kpi-value">{{ $kpis['noParque'] }}</div>
                    <div style="font-size:.68rem;color:#94a3b8;margin-top:3px;">
                        {{ $kpis['total'] }} registados · {{ number_format($kpis['peso'], 1, ',', ' ') }} t
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card yellow">
                <div class="kpi-icon yellow"><i class="bi bi-truck"></i></div>
                <div>
                    <div class="kpi-label">Em trânsito</div>
                    <div class="kpi-value">{{ $kpis['emTransito'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card green">
                <div class="kpi-icon green"><i class="bi bi-check2-circle"></i></div>
                <div>
                    <div class="kpi-label">Carregados</div>
                    <div class="kpi-value">{{ $kpis['carregados'] }}</div>
                    <div style="font-size:.68rem;color:#94a3b8;margin-top:3px;">{{ $kpis['saiu'] }} já saíram</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card red">
                <div class="kpi-icon red"><i class="bi bi-hourglass-split"></i></div>
                <div>
                    <div class="kpi-label">Parados há demasiado tempo</div>
                    <div class="kpi-value">{{ $kpis['atrasados'] }}</div>
                    <div style="font-size:.68rem;color:#94a3b8;margin-top:3px;">mais de {{ $kpis['limite'] }} dias no parque</div>
                </div>
            </div>
        </div>
    </div>

    {{-- GRÁFICOS --}}
    <div class="row g-3 mb-3">
        <div class="col-lg-8">
            <div class="chart-card h-100">
                <div class="chart-card-head">
                    <div>
                        <div class="chart-card-title">Entradas por mês</div>
                        <div class="chart-card-sub">Últimos 12 meses</div>
                    </div>
                </div>
                <div class="chart-card-body">
                    <div style="height:280px;"><canvas id="mensalChart"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="chart-card h-100">
                <div class="chart-card-head">
                    <div>
                        <div class="chart-card-title">Contentores por estado</div>
                        <div class="chart-card-sub">Todos os registos</div>
                    </div>
                </div>
                <div class="chart-card-body">
                    <div style="height:280px;">
                        @if($porEstado->isEmpty())
                            <div class="chart-empty">Sem contentores registados</div>
                        @else
                            <canvas id="estadoChart"></canvas>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="chart-card">
                <div class="chart-card-head">
                    <div>
                        <div class="chart-card-title">Clientes com mais contentores no parque</div>
                        <div class="chart-card-sub">Top 8</div>
                    </div>
                </div>
                <div class="chart-card-body">
                    <div style="height:240px;">
                        @if($topClientes->isEmpty())
                            <div class="chart-empty">Sem contentores com cliente no parque</div>
                        @else
                            <canvas id="clientesChart"></canvas>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABELA --}}
    <div class="table-card">
        <div class="table-card-head justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <div class="table-card-icon"><i class="bi bi-list-stars"></i></div>
                <div>
                    <div style="font-size:.82rem;font-weight:700;color:#334155;">Lista de contentores</div>
                    <div style="font-size:.7rem;color:#94a3b8;">{{ $kpis['total'] }} registados</div>
                </div>
            </div>
            <select id="filtroEstado" class="ct-filtro">
                <option value="">Todos os estados</option>
                @foreach(\App\Models\Contentor::ESTADOS as $label)
                    <option value="{{ $label }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="table-responsive">
            <table id="tabelaContentores" class="table tabela-dash mb-0">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Cliente</th>
                        <th>Navio</th>
                        <th>BL / Selo</th>
                        <th>Localização</th>
                        <th class="text-end">Entrada</th>
                        <th>Dias no parque</th>
                        <th class="text-end"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contentores as $c)
                    @php
                        $d = $c->dias_no_parque;
                        $ativo = ! in_array($c->estado, ['em_transito', 'saiu']);
                        $dadosEdicao = [
                            'id'           => $c->id,
                            'numero'       => $c->numero,
                            'tipo'         => $c->tipo,
                            'estado'       => $c->estado,
                            'armador'      => $c->armador,
                            'cliente'      => $c->cliente,
                            'navio'        => $c->navio,
                            'bl'           => $c->bl,
                            'selo'         => $c->selo,
                            'peso_kg'      => $c->peso_kg,
                            'localizacao'  => $c->localizacao,
                            'data_entrada' => $c->data_entrada?->format('Y-m-d'),
                            'data_saida'   => $c->data_saida?->format('Y-m-d'),
                            'observacoes'  => $c->observacoes,
                        ];
                    @endphp
                    <tr>
                        <td class="fw-semibold" style="color:#1e293b;"><span class="mono">{{ $c->numero }}</span></td>
                        <td>{{ $c->tipo }}</td>
                        <td><span class="ct-badge {{ $c->estado }}">{{ \App\Models\Contentor::ESTADOS[$c->estado] ?? $c->estado }}</span></td>
                        <td>{{ $c->cliente ?: '—' }}</td>
                        <td>{{ $c->navio ?: '—' }}</td>
                        <td>
                            @if($c->bl)<span class="mono">{{ $c->bl }}</span>@endif
                            @if($c->selo)<span class="mono">{{ $c->selo }}</span>@endif
                            @if(! $c->bl && ! $c->selo)—@endif
                        </td>
                        <td>{{ $c->localizacao ?: '—' }}</td>
                        <td class="text-end" data-order="{{ $c->data_entrada?->timestamp ?? 0 }}">
                            {{ $c->data_entrada?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td data-order="{{ $d ?? -1 }}">
                            @if($d === null)
                                —
                            @else
                                <div class="d-flex align-items-center gap-2">
                                    <div class="prog flex-grow-1">
                                        <span class="{{ $ativo && $d > $kpis['limite'] ? 'over' : '' }}"
                                              style="width:{{ min(100, round($d / 14 * 100)) }}%"></span>
                                    </div>
                                    <span style="font-size:.72rem;color:#64748b;width:42px;">{{ $d }} d</span>
                                </div>
                            @endif
                        </td>
                        <td class="text-end text-nowrap">
                            <button type="button" class="ct-act btn-editar" title="Editar"
                                    data-c="{{ json_encode($dadosEdicao) }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('contentores.destroy', $c) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Apagar o contentor {{ $c->numero }}?')">
                                @csrf @method('DELETE')
                                <button class="ct-act danger" title="Apagar"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ═══════════ MODAL: CRIAR / EDITAR ═══════════ --}}
<div class="modal fade" id="modalContentor" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" id="formContentor" action="{{ route('contentores.store') }}">
                @csrf
                <input type="hidden" name="_method" id="campoMethod" value="PUT" disabled>
                <input type="hidden" name="_modal" id="campoModal" value="{{ old('_modal', 'novo') }}">
                <input type="hidden" name="_action_url" id="campoAction" value="{{ old('_action_url') }}">

                <div class="modal-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="table-card-icon"><i class="bi bi-box-seam-fill"></i></div>
                        <div>
                            <div id="modalTitulo" style="font-size:.9rem;font-weight:700;color:#334155;">Novo contentor</div>
                            <div style="font-size:.7rem;color:#94a3b8;">Número segundo a norma ISO 6346 (4 letras + 7 dígitos)</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <div class="modal-body">

                    @if($errors->any())
                        <div class="alert alert-danger" style="font-size:.82rem;border-radius:10px;">
                            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <div class="ct-sec">Identificação</div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <div class="ct-lbl">Número (ISO 6346)</div>
                            <input type="text" name="numero" maxlength="13" class="form-control ct-in text-uppercase"
                                   value="{{ old('numero') }}" placeholder="MSKU1234565" required>
                        </div>
                        <div class="col-md-4">
                            <div class="ct-lbl">Tipo</div>
                            <select name="tipo" class="form-select ct-in">
                                @foreach(\App\Models\Contentor::TIPOS as $t)
                                    <option value="{{ $t }}" @selected(old('tipo', '20GP') === $t)>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="ct-lbl">Estado</div>
                            <select name="estado" class="form-select ct-in">
                                @foreach(\App\Models\Contentor::ESTADOS as $k => $label)
                                    <option value="{{ $k }}" @selected(old('estado', 'no_parque') === $k)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="ct-sec">Origem e documentos</div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <div class="ct-lbl">Armador</div>
                            <input type="text" name="armador" class="form-control ct-in" value="{{ old('armador') }}">
                        </div>
                        <div class="col-md-4">
                            <div class="ct-lbl">Cliente</div>
                            <input type="text" name="cliente" class="form-control ct-in" value="{{ old('cliente') }}">
                        </div>
                        <div class="col-md-4">
                            <div class="ct-lbl">Navio</div>
                            <input type="text" name="navio" class="form-control ct-in" value="{{ old('navio') }}">
                        </div>
                        <div class="col-md-4">
                            <div class="ct-lbl">BL</div>
                            <input type="text" name="bl" class="form-control ct-in" value="{{ old('bl') }}">
                        </div>
                        <div class="col-md-4">
                            <div class="ct-lbl">Selo</div>
                            <input type="text" name="selo" class="form-control ct-in" value="{{ old('selo') }}">
                        </div>
                        <div class="col-md-4">
                            <div class="ct-lbl">Peso (kg)</div>
                            <input type="number" step="0.01" name="peso_kg" class="form-control ct-in" value="{{ old('peso_kg') }}">
                        </div>
                    </div>

                    <div class="ct-sec">Parque</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="ct-lbl">Localização no parque</div>
                            <input type="text" name="localizacao" class="form-control ct-in" placeholder="Ex.: Bloco A · Fila 3"
                                   value="{{ old('localizacao') }}">
                        </div>
                        <div class="col-md-4">
                            <div class="ct-lbl">Data de entrada</div>
                            <input type="date" name="data_entrada" class="form-control ct-in" value="{{ old('data_entrada', now()->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4">
                            <div class="ct-lbl">Data de saída</div>
                            <input type="date" name="data_saida" class="form-control ct-in" value="{{ old('data_saida') }}">
                        </div>
                        <div class="col-12">
                            <div class="ct-lbl">Observações</div>
                            <textarea name="observacoes" rows="3" class="form-control ct-in">{{ old('observacoes') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="ct-btn-sec" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="ct-btn"><i class="bi bi-check2 me-1"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function () {

    /* ── Modal criar / editar ── */
    const modalEl   = document.getElementById('modalContentor');
    const modal     = new bootstrap.Modal(modalEl);
    const form      = document.getElementById('formContentor');
    const urlStore  = @json(route('contentores.store'));
    const urlUpdate = @json(route('contentores.update', '__ID__'));
    const campos    = ['numero','tipo','estado','armador','cliente','navio','bl','selo','peso_kg','localizacao','data_entrada','data_saida','observacoes'];

    function preparar(editar, action) {
        form.action = action;
        document.getElementById('campoMethod').disabled = !editar;
        document.getElementById('campoModal').value  = editar ? 'editar' : 'novo';
        document.getElementById('campoAction').value = action;
        document.getElementById('modalTitulo').textContent = editar ? 'Editar contentor' : 'Novo contentor';
    }

    document.getElementById('btnNovo').addEventListener('click', function () {
        preparar(false, urlStore);
        campos.forEach(n => form.elements[n].value = '');
        form.elements['tipo'].value = '20GP';
        form.elements['estado'].value = 'no_parque';
        form.elements['data_entrada'].value = new Date().toISOString().slice(0, 10);
        modal.show();
    });

    document.querySelectorAll('.btn-editar').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const d = JSON.parse(btn.getAttribute('data-c'));
            preparar(true, urlUpdate.replace('__ID__', d.id));
            campos.forEach(n => form.elements[n].value = d[n] ?? '');
            modal.show();
        });
    });

    @if($errors->any() && old('_modal'))
        // Erro de validação: reabre a modal com os valores já preenchidos
        preparar(@json(old('_modal') === 'editar'), @json(old('_action_url') ?: route('contentores.store')));
        modal.show();
    @endif

    /* ── Tabela ── */
    const tabela = $('#tabelaContentores').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json' },
        pageLength: 10,
        order: [[7, 'desc']],
        columnDefs: [{ orderable: false, targets: 9 }],
        dom: '<"d-flex justify-content-between align-items-center mb-3"f>rt<"d-flex justify-content-between align-items-center mt-3"ip>'
    });

    $('#filtroEstado').on('change', function () {
        const v = this.value;
        tabela.column(2).search(v ? '^' + v + '$' : '', true, false).draw();
    });

    /* ── Gráficos ── */
    const cores = ['#1e293b','#c60a1a','#187fc4','#7ed953','#f59e0b','#8b3ecf','#342cc9','#707070'];
    const eixos = {
        x: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 }, color: '#94a3b8' } },
        y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 }, color: '#94a3b8', precision: 0 }, beginAtZero: true }
    };

    new Chart(document.getElementById('mensalChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($meses) !!},
            datasets: [{
                label: 'Entradas',
                data: {!! json_encode($entradasMensais) !!},
                borderColor: '#1e293b',
                backgroundColor: 'rgba(30,41,59,0.04)',
                borderWidth: 2, tension: 0.4, fill: true,
                pointRadius: 3, pointBackgroundColor: '#1e293b'
            }]
        },
        options: { maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: eixos }
    });

    const estadoEl = document.getElementById('estadoChart');
    if (estadoEl) {
        new Chart(estadoEl, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($porEstado->pluck('nome')->values()) !!},
                datasets: [{
                    data: {!! json_encode($porEstado->pluck('total')->values()) !!},
                    backgroundColor: cores, hoverOffset: 10, borderWidth: 0
                }]
            },
            options: {
                maintainAspectRatio: false, cutout: '72%',
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 9, font: { size: 10 }, color: '#64748b', padding: 12 } } }
            }
        });
    }

    const cliEl = document.getElementById('clientesChart');
    if (cliEl) {
        new Chart(cliEl, {
            type: 'bar',
            data: {
                labels: {!! json_encode($topClientes->keys()->values()) !!},
                datasets: [{
                    label: 'Contentores no parque',
                    data: {!! json_encode($topClientes->values()) !!},
                    backgroundColor: '#1e293b', borderRadius: 4
                }]
            },
            options: { maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: eixos }
        });
    }
});
</script>
@endpush
</x-app-layout>
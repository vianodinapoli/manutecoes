<x-app-layout>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-dark mb-0">
                <i class="bi bi-ship text-primary me-2"></i>Controlo de Descargas — Nitrato de Amónio
            </h4>
            <a href="{{ route('discharges.create') }}" class="btn btn-primary btn-sm fw-bold px-3 shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Registar Descarga (Porto)
            </a>
        </div>

        {{-- Toast Notification --}}
@if(session('success'))
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <div id="successToast" class="toast show border-0"
         style="border-radius: 10px;
                min-width: 260px;
                max-width: 290px;
                overflow: hidden;
                background: linear-gradient(135deg, rgba(25, 135, 84, 0.88), rgba(32, 201, 151, 0.88));
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border: 1px solid rgba(255,255,255,0.25) !important;
                box-shadow: 0 8px 32px rgba(25,135,84,0.35);">

        <div class="d-flex align-items-center gap-3 px-3 py-3">

            {{-- Ícone --}}
            <i class="bi bi-check-circle-fill text-white" style="font-size:1.4rem; min-width:22px;"></i>

            {{-- Texto --}}
            <div style="font-size:0.78rem; color:rgba(255,255,255,0.95); line-height:1.4;">
                {{ session('success') }}
            </div>

            {{-- Fechar --}}
            <button type="button" class="btn-close btn-close-white opacity-75 ms-auto"
                    data-bs-dismiss="toast" style="font-size:0.55rem;">
            </button>
        </div>

        {{-- Barra de progresso --}}
        <div style="height:2px; background:rgba(255,255,255,0.15); overflow:hidden;">
            <div id="toastProgress"
                 style="height:100%; width:100%;
                        background: rgba(255,255,255,0.6);
                        transition: width 3.5s linear;">
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toastEl = document.getElementById('successToast');
        const toast   = new bootstrap.Toast(toastEl, { delay: 3500 });
        toast.show();

        setTimeout(() => {
            document.getElementById('toastProgress').style.width = '0%';
        }, 50);

        toastEl.style.opacity    = '0';
        toastEl.style.transform  = 'translateX(60px) scale(0.95)';
        toastEl.style.transition = 'all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1)';
        setTimeout(() => {
            toastEl.style.opacity   = '1';
            toastEl.style.transform = 'translateX(0) scale(1)';
        }, 50);
    });
</script>
@endif

        {{-- Cards de Resumo --}}
        {{-- Cards de Resumo --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0dcaf0 !important;">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small fw-bold text-uppercase text-muted">Em Trânsito</div>
                        <h3 class="fw-bold mb-0 text-info">{{ $discharges->where('status','in_transit')->count() }}</h3>
                    </div>
                    <i class="bi bi-truck fs-2 text-info opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #198754 !important;">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small fw-bold text-uppercase text-muted">Descarregados</div>
                        <h3 class="fw-bold mb-0 text-success">{{ $discharges->where('status','confirmed')->count() }}</h3>
                    </div>
                    <i class="bi bi-check-circle-fill fs-2 text-success opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #1a56db !important;">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small fw-bold text-uppercase text-muted">Camiões &gt;24 sacos</div>
                        <h3 class="fw-bold mb-0" style="color:#1a56db;">{{ $discharges->where('total_sacos', '>', 24)->count() }}</h3>
                    </div>
                    <i class="bi bi-box-seam fs-2 opacity-50" style="color:#1a56db;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #dc3545 !important;">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small fw-bold text-uppercase text-muted">Camiões ≤24 sacos</div>
                        <h3 class="fw-bold mb-0 text-danger">{{ $discharges->where('total_sacos', '<=', 24)->count() }}</h3>
                    </div>
                    <i class="bi bi-box fs-2 text-danger opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Segunda linha de cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #6f42c1 !important;">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small fw-bold text-uppercase text-muted">Total Registos</div>
                        <h3 class="fw-bold mb-0 text-purple" style="color:#6f42c1;">{{ $discharges->count() }}</h3>
                    </div>
                    <i class="bi bi-clipboard-data fs-2 opacity-50" style="color:#6f42c1;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #198754 !important;">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small fw-bold text-uppercase text-muted">Peso Total</div>
                        <h3 class="fw-bold mb-0 text-success" style="font-size:1.1rem;">
                            {{ number_format($discharges->whereNotNull('peso_liquido')->sum('peso_liquido'), 0, ',', '.') }} kg
                        </h3>
                    </div>
                    <i class="bi bi-speedometer2 fs-2 text-success opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #1a56db !important;">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small fw-bold text-uppercase text-muted">Total Sacos Alta</div>
                        <h3 class="fw-bold mb-0" style="color:#1a56db;">{{ number_format($discharges->sum('sacos_alta'), 0, ',', '.') }}</h3>
                    </div>
                    <i class="bi bi-arrow-up-circle-fill fs-2 opacity-50" style="color:#1a56db;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #dc3545 !important;">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small fw-bold text-uppercase text-muted">Total Sacos Baixa</div>
                        <h3 class="fw-bold mb-0 text-danger">{{ number_format($discharges->sum('sacos_baixa'), 0, ',', '.') }}</h3>
                    </div>
                    <i class="bi bi-arrow-down-circle-fill fs-2 text-danger opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-dark mb-0">
        <i class="bi bi-ship text-primary me-2"></i>Controlo de Descargas — Nitrato de Amónio
    </h4>
    <div class="d-flex gap-2">
        <button onclick="imprimirRelatorio()" class="btn btn-outline-dark btn-sm fw-bold px-3 shadow-sm">
            <i class="bi bi-printer me-1"></i> Imprimir Relatório
        </button>
        <a href="{{ route('discharges.create') }}" class="btn btn-primary btn-sm fw-bold px-3 shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Registar Descarga (Porto)
        </a>
    </div>
</div>

        {{-- Tabela --}}
        <div class="card shadow-sm border-0">
            <div class="card-body p-3">
                <table class="table table-hover align-middle" id="dischargesTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="small fw-bold">Nº / DATA</th>
                            <th class="small fw-bold">GUIA</th>
                            <th class="small fw-bold">MOTORISTA</th>
                            <th class="small fw-bold">MATRÍCULA</th>
                            <th class="small fw-bold">TRANSPORTADORA</th>
                            <th class="small fw-bold text-center">SACOS</th>
                            <th class="small fw-bold text-center">PESO PORTO</th>
                            <th class="small fw-bold text-center">SAÍDA</th>
                            <th class="small fw-bold text-center">CHEGADA</th>
                            <th class="small fw-bold text-center">TEMPO</th>
                            <th class="small fw-bold text-center">ESTADO</th>
                            <th class="small fw-bold text-center">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($discharges as $discharge)
                        @php
                            $statusClass = match($discharge->status) {
                                'confirmed'  => 'bg-success text-white',
                                'in_transit' => 'bg-info text-dark',
                                default      => 'bg-warning text-dark',
                            };
                            $statusIcon = match($discharge->status) {
                                'confirmed'  => 'check-circle-fill',
                                'in_transit' => 'truck',
                                default      => 'hourglass-split',
                            };
                        @endphp
                        <tr>
                            <td class="small">
                                <strong>#{{ $discharge->id }}</strong><br>
                                <span class="text-muted">{{ $discharge->data->format('d/m/Y') }}</span>
                            </td>
                            <td class="small fw-bold text-primary">{{ $discharge->numero_guia }}</td>
                            <td class="small">{{ $discharge->motorista }}</td>
                            <td class="small"><span class="badge bg-dark">{{ $discharge->matricula }}</span></td>
                            <td class="small text-muted">{{ $discharge->transportadora }}</td>
                            <td class="text-center small">
                                <strong>{{ $discharge->numero_sacos }}</strong>
                                <span class="text-muted d-block" style="font-size:0.7rem;">{{ $discharge->tipo_saco }}kg/saco</span>
                                @if($discharge->sacos_confirmados && $discharge->sacos_confirmados != $discharge->numero_sacos)
                                    <span class="badge bg-danger" style="font-size:0.65rem;">Conf: {{ $discharge->sacos_confirmados }}</span>
                                @elseif($discharge->sacos_confirmados)
                                    <span class="badge bg-success" style="font-size:0.65rem;">✓ {{ $discharge->sacos_confirmados }}</span>
                                @endif
                            </td>
                            <td class="text-center small fw-bold">
                                {{ number_format($discharge->peso_total_porto, 0, ',', '.') }} kg
                                @if($discharge->peso_liquido)
                                    <span class="text-success d-block" style="font-size:0.7rem;">Líq: {{ number_format($discharge->peso_liquido, 0, ',', '.') }} kg</span>
                                @endif
                            </td>
                            <td class="text-center small">{{ $discharge->hora_saida_porto ?? '---' }}</td>
                            <td class="text-center small">{{ $discharge->hora_chegada_balanca ?? '---' }}</td>
                            <td class="text-center small">
                                @if($discharge->tempo_transporte)
                                    <span class="badge bg-info text-dark">{{ $discharge->tempo_transporte }} min</span>
                                @else
                                    <span class="text-muted">---</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $statusClass }} px-2 py-1" style="border-radius:20px; font-size:0.7rem;">
                                    <i class="bi bi-{{ $statusIcon }} me-1"></i>{{ $discharge->status_label }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    @if($discharge->status === 'pending' || $discharge->status === 'in_transit')
                                        <button class="btn btn-sm btn-success fw-bold"
                                                style="font-size:0.7rem; border-radius:20px; padding: 3px 10px;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalConfirm"
                                                data-id="{{ $discharge->id }}"
                                                data-guia="{{ $discharge->numero_guia }}"
                                                data-sacos="{{ $discharge->numero_sacos }}"
                                                data-saida="{{ $discharge->hora_saida_porto }}"
                                                title="Confirmar na Balança">
                                            <i class="bi bi-speedometer2 me-1"></i>Balança
                                        </button>
                                    @endif
                                    <a href="{{ route('discharges.show', $discharge->id) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       style="width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px;">
                                        <i class="bi bi-eye" style="font-size:0.75rem;"></i>
                                    </a>
                                    <a href="{{ route('discharges.edit', $discharge->id) }}"
                                       class="btn btn-sm btn-outline-warning"
                                       style="width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px;">
                                        <i class="bi bi-pencil" style="font-size:0.75rem;"></i>
                                    </a>
                                    @if(auth()->user()->hasRole('super-admin'))
                                    <form action="{{ route('discharges.destroy', $discharge->id) }}" method="POST" class="m-0">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                style="width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px;"
                                                onclick="return confirm('Eliminar este registo?')">
                                            <i class="bi bi-trash3" style="font-size:0.75rem;"></i>
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
    </div>

    {{-- Modal Confirmação Balança --}}
    <div class="modal fade" id="modalConfirm" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-speedometer2 me-2"></i>Confirmação na Balança
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formConfirm" method="POST">
                    @csrf @method('PATCH')
                    <div class="modal-body p-4">
                        <div class="alert alert-info py-2 small mb-3">
                            <i class="bi bi-info-circle me-1"></i>
                            Guia <strong id="confirm_guia"></strong> —
                            <strong id="confirm_sacos"></strong> sacos registados no porto
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="small fw-bold text-muted">Hora de Chegada</label>
                                <input type="time" name="hora_chegada_balanca" id="hora_chegada_balanca" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold text-muted">Tempo de Transporte</label>
                                <div class="input-group">
                                    <input type="text" id="transport_time_calc" class="form-control bg-light" readonly placeholder="Auto">
                                    <span class="input-group-text small">min</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold text-muted">Peso Bruto (kg)</label>
                                <input type="number" name="peso_bruto" id="peso_bruto" class="form-control" step="0.01" min="0" oninput="calcNetWeight()" required>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold text-muted">Tara (kg)</label>
                                <input type="number" name="tara" id="tara" class="form-control" step="0.01" min="0" oninput="calcNetWeight()" required>
                            </div>
                            <div class="col-12">
                                <label class="small fw-bold text-muted">Peso Líquido (kg)</label>
                                <input type="text" id="net_weight_calc" class="form-control bg-light fw-bold text-success" readonly placeholder="Calculado automaticamente">
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold text-muted">Sacos Confirmados</label>
                                <input type="number" name="sacos_confirmados" id="sacos_confirmados" class="form-control" min="1" required>
                                <div id="divergence_alert" class="text-danger small mt-1 d-none">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>Divergência detectada!
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="small fw-bold text-muted">Observações</label>
                                <textarea name="observacoes" class="form-control" rows="2" placeholder="Ex: saco rasgado, diferença de peso..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success btn-sm px-4 fw-bold">
                            <i class="bi bi-check-circle me-1"></i>Confirmar e Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#dischargesTable').DataTable({
                language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json' },
                order: [[0, 'desc']],
                columnDefs: [{ orderable: false, targets: [10, 11] }],
                pageLength: 15,
            });

            $(document).on('click', '[data-bs-target="#modalConfirm"]', function () {
                const id    = $(this).data('id');
                const guia  = $(this).data('guia');
                const sacos = $(this).data('sacos');
                const saida = $(this).data('saida');

                $('#confirm_guia').text(guia);
                $('#confirm_sacos').text(sacos);
                $('#sacos_confirmados').val(sacos);
                window._bagsFromPort  = sacos;
                window._departureTime = saida;

                $('#formConfirm').attr('action', `/discharges/${id}/confirm`);
                $('#hora_chegada_balanca, #peso_bruto, #tara, #net_weight_calc, #transport_time_calc').val('');
                $('#divergence_alert').addClass('d-none');
            });

            $('#hora_chegada_balanca').on('change', calcTransportTime);

            $(document).on('input', '#sacos_confirmados', function () {
                const confirmed = parseInt($(this).val()) || 0;
                const fromPort  = parseInt(window._bagsFromPort) || 0;
                if (confirmed !== fromPort && confirmed > 0) {
                    $('#divergence_alert').removeClass('d-none');
                } else {
                    $('#divergence_alert').addClass('d-none');
                }
            });
        });

        function calcTransportTime() {
            const departure = window._departureTime;
            const arrival   = $('#hora_chegada_balanca').val();
            if (!departure || !arrival) return;
            const [hD, mD] = departure.split(':').map(Number);
            const [hA, mA] = arrival.split(':').map(Number);
            const diff = (hA * 60 + mA) - (hD * 60 + mD);
            $('#transport_time_calc').val(diff >= 0 ? diff : '---');
        }

        function calcNetWeight() {
            const gross = parseFloat($('#peso_bruto').val()) || 0;
            const tare  = parseFloat($('#tara').val()) || 0;
            const net   = gross - tare;
            $('#net_weight_calc').val(net > 0 ? net.toFixed(2) : '---');
        }
    </script>




{{-- Área de Impressão do Relatório --}}
<div id="reportArea" style="display:none;">
    <style>
        @media print {
            body * { visibility: hidden; }
            #reportArea, #reportArea * { visibility: visible; }
            #reportArea { position: absolute; left: 0; top: 0; width: 100%; padding: 30px; }
        }
    </style>

    <div style="font-family: Arial, sans-serif; padding: 20px;">

        {{-- Cabeçalho --}}
        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom: 3px solid #198754; padding-bottom: 16px; margin-bottom: 24px;">
            <div>
                <h2 style="margin:0; color:#198754; font-size:1.4rem;">RELATÓRIO DE DESCARGAS</h2>
                <p style="margin:4px 0 0; color:#666; font-size:0.85rem;">Nitrato de Amónio — Controlo de Camiões</p>
            </div>
            <div style="text-align:right; color:#666; font-size:0.8rem;">
                <div><strong>Data de emissão:</strong> {{ now()->format('d/m/Y H:i') }}</div>
                <div><strong>Total de registos:</strong> {{ $discharges->count() }}</div>
            </div>
        </div>

        {{-- Cards de Resumo --}}
        <div style="display:grid; grid-template-columns: repeat(4, 1fr); gap:12px; margin-bottom:24px;">
            <div style="background:#f0fdf4; border-left:4px solid #198754; border-radius:8px; padding:12px;">
                <div style="font-size:0.65rem; color:#666; text-transform:uppercase; font-weight:bold;">Total Registos</div>
                <div style="font-size:1.5rem; font-weight:bold; color:#198754;">{{ $discharges->count() }}</div>
            </div>
            <div style="background:#fff7f0; border-left:4px solid #dc3545; border-radius:8px; padding:12px;">
                <div style="font-size:0.65rem; color:#666; text-transform:uppercase; font-weight:bold;">Camiões &gt;24 sacos</div>
                <div style="font-size:1.5rem; font-weight:bold; color:#dc3545;">{{ $discharges->where('total_sacos', '>', 24)->count() }}</div>
            </div>
            <div style="background:#f0f4ff; border-left:4px solid #1a56db; border-radius:8px; padding:12px;">
                <div style="font-size:0.65rem; color:#666; text-transform:uppercase; font-weight:bold;">Camiões ≤24 sacos</div>
                <div style="font-size:1.5rem; font-weight:bold; color:#1a56db;">{{ $discharges->where('total_sacos', '<=', 24)->count() }}</div>
            </div>
            <div style="background:#f5f0ff; border-left:4px solid #6f42c1; border-radius:8px; padding:12px;">
                <div style="font-size:0.65rem; color:#666; text-transform:uppercase; font-weight:bold;">Peso Total Líquido</div>
                <div style="font-size:1.1rem; font-weight:bold; color:#6f42c1;">{{ number_format($discharges->whereNotNull('peso_liquido')->sum('peso_liquido'), 0, ',', '.') }} kg</div>
            </div>
        </div>

        {{-- Sacos Alta vs Baixa --}}
        <div style="display:grid; grid-template-columns: repeat(2, 1fr); gap:12px; margin-bottom:24px;">
            <div style="background:#e8f0fe; border-left:4px solid #1a56db; border-radius:8px; padding:12px;">
                <div style="font-size:0.65rem; color:#666; text-transform:uppercase; font-weight:bold;">Total Sacos ALTA</div>
                <div style="font-size:1.5rem; font-weight:bold; color:#1a56db;">{{ number_format($discharges->sum('sacos_alta'), 0, ',', '.') }}</div>
                <div style="font-size:0.75rem; color:#666;">Peso total: {{ number_format($discharges->sum('total_alta'), 0, ',', '.') }} kg</div>
            </div>
            <div style="background:#fde8e8; border-left:4px solid #dc3545; border-radius:8px; padding:12px;">
                <div style="font-size:0.65rem; color:#666; text-transform:uppercase; font-weight:bold;">Total Sacos BAIXA</div>
                <div style="font-size:1.5rem; font-weight:bold; color:#dc3545;">{{ number_format($discharges->sum('sacos_baixa'), 0, ',', '.') }}</div>
                <div style="font-size:0.75rem; color:#666;">Peso total: {{ number_format($discharges->sum('total_baixa'), 0, ',', '.') }} kg</div>
            </div>
        </div>

        {{-- Tabela por Transportadora --}}
        <h3 style="font-size:0.9rem; color:#333; border-bottom:2px solid #eee; padding-bottom:8px; margin-bottom:12px;">
            RESUMO POR TRANSPORTADORA
        </h3>
        <table style="width:100%; border-collapse:collapse; margin-bottom:24px; font-size:0.8rem;">
            <thead>
                <tr style="background:#198754; color:white;">
                    <th style="padding:8px 12px; text-align:left;">Transportadora</th>
                    <th style="padding:8px 12px; text-align:center;">Nº Viagens</th>
                    <th style="padding:8px 12px; text-align:center;">Camiões &gt;24</th>
                    <th style="padding:8px 12px; text-align:center;">Camiões ≤24</th>
                    <th style="padding:8px 12px; text-align:center;">Total Sacos</th>
                    <th style="padding:8px 12px; text-align:right;">Peso Líquido</th>
                </tr>
            </thead>
            <tbody>
                @foreach($discharges->groupBy('transportadora') as $transportadora => $grupo)
                <tr style="border-bottom:1px solid #eee; {{ $loop->even ? 'background:#f9f9f9;' : '' }}">
                    <td style="padding:8px 12px; font-weight:bold;">{{ $transportadora }}</td>
                    <td style="padding:8px 12px; text-align:center;">{{ $grupo->count() }}</td>
                    <td style="padding:8px 12px; text-align:center; color:#dc3545; font-weight:bold;">{{ $grupo->where('total_sacos', '>', 24)->count() }}</td>
                    <td style="padding:8px 12px; text-align:center; color:#1a56db; font-weight:bold;">{{ $grupo->where('total_sacos', '<=', 24)->count() }}</td>
                    <td style="padding:8px 12px; text-align:center;">{{ number_format($grupo->sum('total_sacos'), 0, ',', '.') }}</td>
                    <td style="padding:8px 12px; text-align:right;">{{ number_format($grupo->whereNotNull('peso_liquido')->sum('peso_liquido'), 0, ',', '.') }} kg</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#f0fdf4; font-weight:bold; border-top:2px solid #198754;">
                    <td style="padding:8px 12px;">TOTAL GERAL</td>
                    <td style="padding:8px 12px; text-align:center;">{{ $discharges->count() }}</td>
                    <td style="padding:8px 12px; text-align:center; color:#dc3545;">{{ $discharges->where('total_sacos', '>', 24)->count() }}</td>
                    <td style="padding:8px 12px; text-align:center; color:#1a56db;">{{ $discharges->where('total_sacos', '<=', 24)->count() }}</td>
                    <td style="padding:8px 12px; text-align:center;">{{ number_format($discharges->sum('total_sacos'), 0, ',', '.') }}</td>
                    <td style="padding:8px 12px; text-align:right;">{{ number_format($discharges->whereNotNull('peso_liquido')->sum('peso_liquido'), 0, ',', '.') }} kg</td>
                </tr>
            </tfoot>
        </table>

        {{-- Tabela detalhada --}}
        <h3 style="font-size:0.9rem; color:#333; border-bottom:2px solid #eee; padding-bottom:8px; margin-bottom:12px;">
            DETALHE DOS REGISTOS
        </h3>
        <table style="width:100%; border-collapse:collapse; font-size:0.75rem;">
            <thead>
                <tr style="background:#1a56db; color:white;">
                    <th style="padding:7px 10px; text-align:left;">Nº</th>
                    <th style="padding:7px 10px; text-align:left;">Data</th>
                    <th style="padding:7px 10px; text-align:left;">Guia</th>
                    <th style="padding:7px 10px; text-align:left;">Motorista</th>
                    <th style="padding:7px 10px; text-align:left;">Transportadora</th>
                    <th style="padding:7px 10px; text-align:center;">Alta</th>
                    <th style="padding:7px 10px; text-align:center;">Baixa</th>
                    <th style="padding:7px 10px; text-align:center;">Total</th>
                    <th style="padding:7px 10px; text-align:right;">Peso Líq.</th>
                    <th style="padding:7px 10px; text-align:center;">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($discharges as $d)
                <tr style="border-bottom:1px solid #eee; {{ $loop->even ? 'background:#f9f9f9;' : '' }}">
                    <td style="padding:6px 10px; color:#666;">#{{ $d->id }}</td>
                    <td style="padding:6px 10px;">{{ $d->data->format('d/m/Y') }}</td>
                    <td style="padding:6px 10px; font-weight:bold; color:#1a56db;">{{ $d->numero_guia }}</td>
                    <td style="padding:6px 10px;">{{ $d->motorista }}</td>
                    <td style="padding:6px 10px;">{{ $d->transportadora }}</td>
                    <td style="padding:6px 10px; text-align:center; color:#1a56db; font-weight:bold;">{{ $d->sacos_alta }}</td>
                    <td style="padding:6px 10px; text-align:center; color:#dc3545; font-weight:bold;">{{ $d->sacos_baixa }}</td>
                    <td style="padding:6px 10px; text-align:center; font-weight:bold;">{{ $d->total_sacos }}</td>
                    <td style="padding:6px 10px; text-align:right;">{{ $d->peso_liquido ? number_format($d->peso_liquido, 0, ',', '.') . ' kg' : '---' }}</td>
                    <td style="padding:6px 10px; text-align:center;">{{ $d->status_label }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Rodapé --}}
        <div style="margin-top:30px; padding-top:12px; border-top:1px solid #eee; text-align:center; color:#999; font-size:0.7rem;">
            Documento gerado automaticamente pelo Sistema de Gestão em {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
</div>

<script>
    function imprimirRelatorio() {
        document.getElementById('reportArea').style.display = 'block';
        window.print();
        document.getElementById('reportArea').style.display = 'none';
    }
</script>
</x-app-layout>
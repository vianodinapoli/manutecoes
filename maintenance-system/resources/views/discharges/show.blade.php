<x-app-layout>
    <div class="container py-4" style="max-width: 860px;">

        @php
            $statusClass = match($discharge->status) {
                'confirmed'  => 'bg-success text-white',
                'in_transit' => 'bg-info text-dark',
                default      => 'bg-warning text-dark',
            };
        @endphp

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <i class="bi bi-ship text-primary me-2"></i>Descarga #{{ $discharge->id }}
            </h4>
            <div class="d-flex gap-2">
                <a href="{{ route('discharges.edit', $discharge->id) }}" class="btn btn-outline-warning btn-sm">
                    <i class="bi bi-pencil me-1"></i>Editar
                </a>
                <a href="{{ route('discharges.index') }}" class="btn btn-outline-secondary btn-sm">Voltar</a>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                <span class="fw-bold text-dark">Guia: {{ $discharge->numero_guia }}</span>
                <span class="badge {{ $statusClass }} px-3 py-2" style="border-radius:20px;">
                    {{ $discharge->status_label }}
                </span>
            </div>
            <div class="card-body p-4">

                {{-- Identificação --}}
                <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">Identificação</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <span class="small text-muted d-block">Data</span>
                        <strong>{{ $discharge->data->format('d/m/Y') }}</strong>
                    </div>
                    <div class="col-md-3">
                        <span class="small text-muted d-block">Motorista</span>
                        <strong>{{ $discharge->motorista }}</strong>
                    </div>
                    <div class="col-md-3">
                        <span class="small text-muted d-block">Matrícula</span>
                        <span class="badge bg-dark">{{ $discharge->matricula }}</span>
                    </div>
                    <div class="col-md-3">
                        <span class="small text-muted d-block">Transportadora</span>
                        <strong>{{ $discharge->transportadora }}</strong>
                    </div>
                </div>

                {{-- Carga Porto --}}
                <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                    <i class="bi bi-ship me-1"></i> Carga — Porto
                </h6>

                <div class="row g-3 mb-4">
                    {{-- Alta --}}
                    @if($discharge->sacos_alta > 0)
                    <div class="col-md-6">
                        <div class="card border-0 h-100" style="background:#e8f0fe; border-left: 4px solid #1a56db !important; border-radius:8px;">
                            <div class="card-body py-3 px-4">
                                <span class="badge mb-2" style="background:#1a56db;">ALTA</span>
                                <div class="row">
                                    <div class="col-6">
                                        <span class="small text-muted d-block">Nº Sacos</span>
                                        <strong class="text-primary fs-5">{{ $discharge->sacos_alta }}</strong>
                                    </div>
                                    <div class="col-6">
                                        <span class="small text-muted d-block">Peso/Saco</span>
                                        <strong>{{ number_format($discharge->peso_saco_alta, 0, ',', '.') }} kg</strong>
                                    </div>
                                    <div class="col-12 mt-2">
                                        <span class="small text-muted d-block">Subtotal Alta</span>
                                        <strong class="text-primary">{{ number_format($discharge->total_alta, 0, ',', '.') }} kg</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Baixa --}}
                    @if($discharge->sacos_baixa > 0)
                    <div class="col-md-6">
                        <div class="card border-0 h-100" style="background:#fde8e8; border-left: 4px solid #dc3545 !important; border-radius:8px;">
                            <div class="card-body py-3 px-4">
                                <span class="badge bg-danger mb-2">BAIXA</span>
                                <div class="row">
                                    <div class="col-6">
                                        <span class="small text-muted d-block">Nº Sacos</span>
                                        <strong class="text-danger fs-5">{{ $discharge->sacos_baixa }}</strong>
                                    </div>
                                    <div class="col-6">
                                        <span class="small text-muted d-block">Peso/Saco</span>
                                        <strong>{{ number_format($discharge->peso_saco_baixa, 0, ',', '.') }} kg</strong>
                                    </div>
                                    <div class="col-12 mt-2">
                                        <span class="small text-muted d-block">Subtotal Baixa</span>
                                        <strong class="text-danger">{{ number_format($discharge->total_baixa, 0, ',', '.') }} kg</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Total Porto --}}
                <div class="card border-0 mb-4" style="background:#f0fdf4; border-left: 4px solid #198754 !important; border-radius:8px;">
                    <div class="card-body py-3 px-4">
                        <div class="row">
                            <div class="col-md-6">
                                <span class="small text-muted d-block">Total de Sacos</span>
                                <strong class="text-success fs-5">{{ $discharge->total_sacos }}</strong>
                            </div>
                            <div class="col-md-6">
                                <span class="small text-muted d-block">Peso Estimado Total</span>
                                <strong class="text-success fs-5">{{ number_format($discharge->peso_total_estimado, 0, ',', '.') }} kg</strong>
                            </div>
                            <div class="col-md-6 mt-2">
                                <span class="small text-muted d-block">Hora Saída Porto</span>
                                <strong>{{ $discharge->hora_saida_porto ?? '---' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Confirmação Balança --}}
                <h6 class="fw-bold text-success border-bottom pb-2 mb-3">
                    <i class="bi bi-speedometer2 me-1"></i> Confirmação — Balança
                </h6>
                @if($discharge->hora_chegada_balanca)
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <span class="small text-muted d-block">Hora Chegada</span>
                            <strong>{{ $discharge->hora_chegada_balanca }}</strong>
                        </div>
                        <div class="col-md-3">
                            <span class="small text-muted d-block">Tempo Transporte</span>
                            <span class="badge bg-info text-dark">{{ $discharge->tempo_transporte }} min</span>
                        </div>
                        <div class="col-md-3">
                            <span class="small text-muted d-block">Peso Bruto</span>
                            <strong>{{ number_format($discharge->peso_bruto, 0, ',', '.') }} kg</strong>
                        </div>
                        <div class="col-md-3">
                            <span class="small text-muted d-block">Tara</span>
                            <strong>{{ number_format($discharge->tara, 0, ',', '.') }} kg</strong>
                        </div>
                        <div class="col-md-3">
                            <span class="small text-muted d-block">Peso Líquido</span>
                            <strong class="text-success fs-5">{{ number_format($discharge->peso_liquido, 0, ',', '.') }} kg</strong>
                        </div>
                        <div class="col-md-3">
                            <span class="small text-muted d-block">Sacos Confirmados</span>
                            @if($discharge->sacos_confirmados != $discharge->total_sacos)
                                <strong class="text-danger">{{ $discharge->sacos_confirmados }} ⚠️ Divergência</strong>
                            @else
                                <strong class="text-success">{{ $discharge->sacos_confirmados }} ✓</strong>
                            @endif
                        </div>
                    </div>
                    @if($discharge->observacoes)
                        <div class="alert alert-warning py-2 small">
                            <i class="bi bi-chat-left-text me-1"></i>
                            <strong>Observações:</strong> {{ $discharge->observacoes }}
                        </div>
                    @endif
                @else
                    <div class="alert alert-warning py-2 small">
                        <i class="bi bi-hourglass-split me-1"></i>Aguarda confirmação na balança.
                    </div>
                @endif

                <hr>
                <div class="row small text-muted">
                    <div class="col-md-6">
                        Registado por: <strong>{{ $discharge->registeredBy->name ?? '---' }}</strong>
                    </div>
                    <div class="col-md-6">
                        Confirmado por: <strong>{{ $discharge->confirmedBy->name ?? '---' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<x-app-layout>
    <div class="container py-4" style="max-width: 860px;">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-pencil me-2"></i>Editar Descarga #{{ $discharge->id }}
                </h5>
                <a href="{{ route('discharges.index') }}" class="btn btn-outline-dark btn-sm">Voltar</a>
            </div>
            <div class="card-body p-4">

                @if ($errors->any())
                    <div class="alert alert-danger py-2 small">
                        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <form action="{{ route('discharges.update', $discharge->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Identificação --}}
                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                        <i class="bi bi-card-list me-1"></i> Identificação
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="small fw-bold text-muted">Data</label>
                            <input type="date" name="data" class="form-control" value="{{ $discharge->data->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold text-muted">Número de Guia</label>
                            <input type="text" name="numero_guia" class="form-control" value="{{ $discharge->numero_guia }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold text-muted">Hora de Saída do Porto</label>
                            <input type="time" name="hora_saida_porto" class="form-control" value="{{ $discharge->hora_saida_porto }}">
                        </div>
                    </div>

                    {{-- Transporte --}}
                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                        <i class="bi bi-truck me-1"></i> Transporte
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="small fw-bold text-muted">Motorista</label>
                            <input type="text" name="motorista" class="form-control" value="{{ $discharge->motorista }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold text-muted">Matrícula</label>
                            <input type="text" name="matricula" class="form-control" value="{{ $discharge->matricula }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold text-muted">Transportadora</label>
                            <input type="text" name="transportadora" class="form-control" value="{{ $discharge->transportadora }}" required>
                        </div>
                    </div>

                    {{-- Carga --}}
                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                        <i class="bi bi-box-seam me-1"></i> Carga — Nitrato de Amónio
                    </h6>

                    {{-- Linha Alta --}}
                    <div class="card border-0 mb-3" style="background: #e8f0fe; border-left: 4px solid #1a56db !important; border-radius: 8px;">
                        <div class="card-body py-3 px-4">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge me-2" style="background:#1a56db;">ALTA</span>
                                <span class="small text-muted">Sacos de alta densidade</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="small fw-bold text-muted">Nº Sacos Alta</label>
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-primary btn-sm px-3" onclick="setHighBags(24)">24</button>
                                        <button type="button" class="btn btn-outline-primary btn-sm px-3" onclick="setHighBags(36)">36</button>
                                        <input type="number" name="sacos_alta" id="sacos_alta" class="form-control text-center" value="{{ $discharge->sacos_alta }}" min="0" oninput="calcAll()" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="small fw-bold text-muted">Peso por Saco (kg)</label>
                                    <input type="number" name="peso_saco_alta" id="peso_saco_alta" class="form-control" value="{{ $discharge->peso_saco_alta }}" step="0.01" min="0" oninput="calcAll()">
                                </div>
                                <div class="col-md-4">
                                    <label class="small fw-bold text-muted">Subtotal Alta</label>
                                    <div class="input-group">
                                        <input type="text" id="subtotal_alta" class="form-control bg-white fw-bold text-primary" readonly>
                                        <span class="input-group-text small">kg</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Linha Baixa --}}
                    <div class="card border-0 mb-3" style="background: #fde8e8; border-left: 4px solid #dc3545 !important; border-radius: 8px;">
                        <div class="card-body py-3 px-4">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-danger me-2">BAIXA</span>
                                <span class="small text-muted">Sacos de baixa densidade</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="small fw-bold text-muted">Nº Sacos Baixa</label>
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-danger btn-sm px-3" onclick="setLowBags(24)">24</button>
                                        <button type="button" class="btn btn-outline-danger btn-sm px-3" onclick="setLowBags(36)">36</button>
                                        <input type="number" name="sacos_baixa" id="sacos_baixa" class="form-control text-center" value="{{ $discharge->sacos_baixa }}" min="0" oninput="calcAll()" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="small fw-bold text-muted">Peso por Saco (kg)</label>
                                    <input type="number" name="peso_saco_baixa" id="peso_saco_baixa" class="form-control" value="{{ $discharge->peso_saco_baixa }}" step="0.01" min="0" oninput="calcAll()">
                                </div>
                                <div class="col-md-4">
                                    <label class="small fw-bold text-muted">Subtotal Baixa</label>
                                    <div class="input-group">
                                        <input type="text" id="subtotal_baixa" class="form-control bg-white fw-bold text-danger" readonly>
                                        <span class="input-group-text small">kg</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Total Geral --}}
                    <div class="card border-0 mb-4" style="background: #f0fdf4; border-left: 4px solid #198754 !important; border-radius: 8px;">
                        <div class="card-body py-3 px-4">
                            <div class="row g-3 align-items-center">
                                <div class="col-md-6">
                                    <label class="small fw-bold text-muted">Total de Sacos</label>
                                    <input type="text" id="total_sacos_display" class="form-control bg-white fw-bold text-success" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold text-muted">Peso Estimado Total</label>
                                    <div class="input-group">
                                        <input type="text" id="total_weight_display" class="form-control bg-white fw-bold fs-5 text-success" readonly>
                                        <span class="input-group-text fw-bold">kg</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('discharges.index') }}" class="btn btn-light border px-4">Cancelar</a>
                        <button type="submit" class="btn btn-warning px-5 fw-bold">
                            <i class="bi bi-check-circle me-1"></i>Guardar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function setHighBags(n) {
            document.getElementById('sacos_alta').value = n;
            calcAll();
        }

        function setLowBags(n) {
            document.getElementById('sacos_baixa').value = n;
            calcAll();
        }

        function calcAll() {
            const sacosAlta  = parseFloat(document.getElementById('sacos_alta').value) || 0;
            const pesoAlta   = parseFloat(document.getElementById('peso_saco_alta').value) || 0;
            const sacosBaixa = parseFloat(document.getElementById('sacos_baixa').value) || 0;
            const pesoBaixa  = parseFloat(document.getElementById('peso_saco_baixa').value) || 0;

            const subAlta    = sacosAlta * pesoAlta;
            const subBaixa   = sacosBaixa * pesoBaixa;
            const total      = subAlta + subBaixa;
            const totalSacos = sacosAlta + sacosBaixa;

            document.getElementById('subtotal_alta').value        = subAlta.toLocaleString('pt-MZ', {minimumFractionDigits: 0});
            document.getElementById('subtotal_baixa').value       = subBaixa.toLocaleString('pt-MZ', {minimumFractionDigits: 0});
            document.getElementById('total_sacos_display').value  = totalSacos;
            document.getElementById('total_weight_display').value = total.toLocaleString('pt-MZ', {minimumFractionDigits: 0});
        }

        calcAll();
    </script>
</x-app-layout>
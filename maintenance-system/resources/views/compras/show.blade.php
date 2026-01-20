<x-app-layout>
    <div class="container mt-5" style="max-width: 100%;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>📋 Detalhes da Solicitação #{{ $compra->id }}</h4>
            <div>
                <a href="{{ route('compras.index') }}" class="btn btn-outline-secondary">Voltar</a>
                <a href="{{ route('compras.edit', $compra->id) }}" class="btn btn-warning">Editar Dados</a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title text-primary border-bottom pb-2">Informações do Material</h5>
                        <div class="row mt-3">
                            <div class="col-sm-4 text-muted">Item:</div>
                            <div class="col-sm-8 font-weight-bold"><h5>{{ $compra->item_name }}</h5></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-4 text-muted">Quantidade:</div>
                            <div class="col-sm-8">{{ $compra->quantity }} unidades</div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-4 text-muted">Preço Estimado:</div>
                            <div class="col-sm-8 text-success font-weight-bold">
                                {{ $compra->price ? number_format($compra->price, 2, ',', '.') . ' MZN' : 'Não definido' }}
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12 text-muted mb-1">Descrição / Justificação:</div>
                            <div class="col-12 p-3 bg-light rounded border">
                                {{ $compra->metadata['descricao'] ?? 'Nenhuma descrição fornecida.' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0 mb-4">
                    @php
                        $status_color = match($compra->status) {
                            'Aprovado' => 'bg-success',
                            'Rejeitado' => 'bg-danger',
                            'Em processo' => 'bg-info',
                            default => 'bg-warning text-dark',
                        };
                    @endphp
                    <div class="card-header {{ $status_color }} text-white text-center font-weight-bold">
                        Status: {{ $compra->status }}
                    </div>
                    <div class="card-body">
                        <h6 class="text-muted border-bottom pb-2">Detalhes da Oficina</h6>
                        <p class="mb-1"><strong>Placa:</strong> {{ $compra->metadata['placa_veiculo'] ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Urgência:</strong> {{ $compra->metadata['urgencia'] ?? 'Normal' }}</p>
                        <hr>
                        <p class="mb-1 text-muted small">Criado em: {{ $compra->created_at->format('d/m/Y H:i') }}</p>
                        <p class="mb-0 text-muted small">Última atualização: {{ $compra->updated_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <div class="card-body">
    <h6 class="text-muted border-bottom pb-2">Detalhes da Oficina</h6>
    

<p class="mb-1"> <strong>Solicitante:</strong> {{ $compra->user->name ?? 'N/A' }}</p>
    <p class="mb-1"><strong>Placa:</strong> {{ $compra->metadata['placa_veiculo'] ?? 'N/A' }}</p>
    <p class="mb-1"><strong>Urgência:</strong> {{ $compra->metadata['urgencia'] ?? 'Normal' }}</p>
    <hr>
    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-3">Documento de Cotação</h6>
                        @if($compra->quotation_file)
                            <a href="{{ asset('storage/' . $compra->quotation_file) }}" target="_blank" class="btn btn-primary w-100">
                                📄 Abrir Ficheiro Anexo
                            </a>
                        @else
                            <div class="alert alert-light mb-0">Sem anexo disponível</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
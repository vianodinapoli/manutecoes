<x-app-layout>
    <style>
        .container { max-width: 100%; }
        .card { border-radius: 10px; border: none; }
        .card-header { font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        label { font-weight: 600; color: #4b5563; margin-bottom: 5px; }
    </style>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('compras.index') }}" class="btn btn-sm btn-outline-secondary">
                ⬅️ Cancelar e Voltar
            </a>
            <span class="badge bg-primary">Editando Solicitação #{{ $compra->id }}</span>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white p-3">
                <h5 class="mb-0">🛠️ Editar Solicitação de Material</h5>
            </div>
            
            <div class="card-body p-4">
                <form action="{{ route('compras.update', $compra->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label for="item_name" class="form-label">Material / Peça</label>
                            <input type="text" name="item_name" id="item_name" class="form-control form-control-lg" value="{{ $compra->item_name }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="quantity" class="form-label">Quantidade</label>
                            <input type="number" name="quantity" id="quantity" class="form-control form-control-lg" min="1" value="{{ $compra->quantity }}" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-4 p-3 bg-light rounded border">
                        <div class="col-12 border-bottom pb-2 mb-2">
                            <h6 class="text-primary mb-0 font-weight-bold">🛠️ Contexto da Solicitação</h6>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="placa" class="form-label">Placa do Veículo</label>
                            <input type="text" name="metadata[placa_veiculo]" id="placa" class="form-control" 
                                   value="{{ $compra->metadata['placa_veiculo'] ?? '' }}" placeholder="00-AA-00">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="urgencia" class="form-label">Nível de Urgência</label>
                            <select name="metadata[urgencia]" id="urgencia" class="form-select">
                                <option value="Normal" {{ ($compra->metadata['urgencia'] ?? '') == 'Normal' ? 'selected' : '' }}>Normal</option>
                                <option value="Alta" {{ ($compra->metadata['urgencia'] ?? '') == 'Alta' ? 'selected' : '' }}>Alta - Carro parado na box</option>
                                <option value="Crítica" {{ ($compra->metadata['urgencia'] ?? '') == 'Crítica' ? 'selected' : '' }}>Crítica - Aguardando peça para entrega</option>
                            </select>
                        </div>

                        <div class="col-12 mt-3">
                            <label for="description" class="form-label">Descrição / Justificação da Necessidade</label>
                            <textarea name="metadata[descricao]" id="description" rows="3" class="form-control" 
                                      placeholder="Descreva o motivo da compra...">{{ $compra->metadata['descricao'] ?? '' }}</textarea>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="price" class="form-label">Preço Estimado (MZN)</label>
                            <input type="number" name="price" id="price" step="0.01" class="form-control" value="{{ $compra->price }}">
                        </div>
                        <div class="col-md-6">
                            <label for="quotation" class="form-label">Substituir Cotação (Deixe vazio para manter)</label>
                            <input type="file" name="quotation" id="quotation" class="form-control">
                            @if($compra->quotation_file)
                                <div class="mt-2">
                                    <small class="text-success">✅ Ficheiro atual: <a href="{{ asset('storage/' . $compra->quotation_file) }}" target="_blank">Ver Anexo</a></small>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="border-top pt-4 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-5 shadow-sm font-weight-bold">
                            💾 Guardar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
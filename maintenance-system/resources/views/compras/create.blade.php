<x-app-layout>
    <style>
        /* Ajustado para 1000px para dar mais respiro aos campos lado a lado */
        .container { max-width: 100%; }
        .card { border-radius: 10px; border: none; }
        .card-header { font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        label { font-weight: 600; color: #4b5563; margin-bottom: 5px; }
        .form-control:focus, .form-select:focus { border-color: #3b82f6; box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25); }
    </style>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('compras.index') }}" class="btn btn-sm btn-outline-secondary">
                ⬅️ Voltar para a Lista
            </a>
            <span class="badge bg-secondary">Módulo de Oficina</span>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white p-3">
                <h5 class="mb-0">🛒 Nova Solicitação de Material</h5>
            </div>
            
            <div class="card-body p-4">
                <form action="{{ route('compras.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label for="item_name" class="form-label">Material / Peça</label>
                            <input type="text" name="item_name" id="item_name" class="form-control form-control-lg" placeholder="Ex: Kit Embraiagem, Óleo 5W30..." required>
                        </div>
                        <div class="col-md-4">
                            <label for="quantity" class="form-label">Quantidade</label>
                            <input type="number" name="quantity" id="quantity" class="form-control form-control-lg" min="1" value="1" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-4 p-3 bg-light rounded border">
                        <div class="col-12 border-bottom pb-2 mb-2">
                            <h6 class="text-primary mb-0 font-weight-bold">🛠️ Contexto da Solicitação</h6>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="placa" class="form-label">Placa do Veículo</label>
                            <input type="text" name="metadata[placa_veiculo]" id="placa" class="form-control" placeholder="00-AA-00">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="urgencia" class="form-label">Nível de Urgência</label>
                            <select name="metadata[urgencia]" id="urgencia" class="form-select">
                                <option value="Normal" selected>Normal</option>
                                <option value="Alta">Alta - Carro parado na box</option>
                                <option value="Crítica">Crítica - Aguardando peça para entrega</option>
                            </select>
                        </div>

                        <div class="col-12 mt-3">
                            <label for="description" class="form-label">Descrição / Justificação da Necessidade</label>
                            <textarea name="metadata[descricao]" id="description" rows="3" class="form-control" placeholder="Descreva o motivo da compra ou especificações técnicas adicionais..."></textarea>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="price" class="form-label">Preço Estimado (Opcional)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">€</span>
                                <input type="number" name="price" id="price" step="0.01" class="form-control" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="quotation" class="form-label">Anexar Cotação / Foto</label>
                            <input type="file" name="quotation_file" id="quotation" class="form-control">
                            <small class="text-muted italic">PDF, JPG, PNG (Máximo 2MB)</small>
                        </div>
                    </div>

                    <div class="border-top pt-4 d-flex justify-content-end">
                        <button type="reset" class="btn btn-light border me-2">Limpar</button>
                        <button type="submit" class="btn btn-primary px-5 shadow-sm font-weight-bold">
                            ✅ Registar Pedido
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
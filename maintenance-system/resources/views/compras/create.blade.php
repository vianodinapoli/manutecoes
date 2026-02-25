<x-app-layout>
    <style>
        .container { max-width: 100%; }
        .card { border-radius: 8px; border: none; }
        .item-row { 
            padding: 10px; 
            margin-bottom: 8px; 
            background: #fff; 
            border: 1px solid #dee2e6; 
            border-radius: 6px;
        }
        .form-label-sm { font-size: 0.85rem; margin-bottom: 2px; font-weight: 700; color: #4b5563; }
        .form-control-sm { padding: 0.4rem 0.6rem; }
        /* Estilo para os anexos múltiplos */
        .file-input-group { position: relative; margin-bottom: 8px; }
        .btn-remove-file { color: #dc3545; cursor: pointer; font-size: 0.8rem; text-decoration: underline; }
    </style>

    <div class="container mt-4">
        <form action="{{ route('compras.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-2">
                    <h6 class="mb-0">🛒 Requisição de Materiais</h6>
                    <button type="button" id="add-item" class="btn btn-sm btn-success">
                        <i class="fas fa-plus"></i> + Adicionar Artigo
                    </button>
                </div>
                
                <div class="card-body p-3">
                    <div class="row g-2 mb-1 d-none d-md-flex text-secondary">
                        <div class="col-md-5"><small class="form-label-sm">Material / Peça</small></div>
                        <div class="col-md-2"><small class="form-label-sm">Qtd.</small></div>
                        <div class="col-md-4"><small class="form-label-sm">Destino (Máquina, Stock ou Digite...)</small></div>
                        <div class="col-md-1"></div>
                    </div>

                    <div id="items-container">
                        <div class="item-row row g-2 align-items-center">
                            <div class="col-md-5">
                                <input type="text" name="items[0][name]" class="form-control form-control-sm" placeholder="Ex: Filtro de Ar" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="items[0][quantity]" class="form-control form-control-sm" min="1" value="1" required>
                            </div>
                            <div class="col-md-4">
                                <input list="destinos-list" name="items[0][destino]" class="form-control form-control-sm" placeholder="Onde aplicar?" required>
                            </div>
                            <div class="col-md-1 text-end">
                                <button type="button" class="btn btn-sm btn-outline-danger remove-item" disabled>×</button>
                            </div>
                        </div>
                    </div>

                    <datalist id="destinos-list">
                        <option value="Stock">
                        <option value="Máquina">
                        <option value="Oficina">
                    </datalist>

                    <div class="row g-3 mt-3 p-3 bg-light rounded border">
                        <div class="col-md-12">
                            <label for="description" class="form-label-sm">📝 Descrição / Justificação da Necessidade</label>
                            <textarea name="description" id="description" rows="3" class="form-control form-control-sm" placeholder="Descreva o motivo da compra..."></textarea>
                        </div>
                        
                        <div class="col-md-6 mt-3">
                            <label class="form-label-sm">Fornecedor Sugerido</label>
                            <input type="text" name="fornecedor" class="form-control form-control-sm" placeholder="Opcional">
                        </div>

                        <div class="col-md-4 mt-3">
        <label for="urgencia" class="form-label-sm">🚨 Nível de Urgência</label>
        <select name="urgencia" id="urgencia" class="form-select form-control-sm">
            <option value="Normal" selected>Normal</option>
            <option value="Alta">Alta (Urgente)</option>
            <option value="Crítica">Crítica (Carro Parado)</option>
        </select>
    </div>
                        
                        <div class="col-md-6 mt-3">
                            <label class="form-label-sm">📁 Anexar Cotações / Fotos</label>
                            <div id="file-inputs-container">
                                <div class="file-input-group">
                                    <input type="file" name="quotation_files[]" class="form-control form-control-sm">
                                </div>
                            </div>
                            <button type="button" id="add-file" class="btn btn-link btn-sm p-0 text-decoration-none">
                                ➕ Anexar outro ficheiro
                            </button>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-5 font-weight-bold shadow-sm">
                            ✅ Submeter Requisição (<span id="item-count">1</span>)
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        // --- LOGICA DE ARTIGOS ---
        let itemIndex = 1;
        const container = document.getElementById('items-container');
        const countSpan = document.getElementById('item-count');

        document.getElementById('add-item').addEventListener('click', () => {
            const html = `
                <div class="item-row row g-2 align-items-center">
                    <div class="col-md-5">
                        <input type="text" name="items[${itemIndex}][name]" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="items[${itemIndex}][quantity]" class="form-control form-control-sm" min="1" value="1" required>
                    </div>
                    <div class="col-md-4">
                        <input list="destinos-list" name="items[${itemIndex}][destino]" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-1 text-end">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-item">×</button>
                    </div>
                </div>`;
            container.insertAdjacentHTML('beforeend', html);
            itemIndex++;
            updateCount();
        });

        container.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-item')) {
                e.target.closest('.item-row').remove();
                updateCount();
            }
        });

        function updateCount() {
            countSpan.innerText = document.querySelectorAll('.item-row').length;
        }

        // --- NOVA LÓGICA DE ANEXOS MÚLTIPLOS ---
        const fileContainer = document.getElementById('file-inputs-container');
        
        document.getElementById('add-file').addEventListener('click', () => {
            const div = document.createElement('div');
            div.className = 'file-input-group d-flex align-items-center gap-2 mt-2';
            div.innerHTML = `
                <input type="file" name="quotation_files[]" class="form-control form-control-sm">
                <span class="btn-remove-file">Remover</span>
            `;
            fileContainer.appendChild(div);
        });

        fileContainer.addEventListener('click', (e) => {
            if (e.target.classList.contains('btn-remove-file')) {
                e.target.parentElement.remove();
            }
        });
    </script>
</x-app-layout>
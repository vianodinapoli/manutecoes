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
        .file-input-group { position: relative; margin-bottom: 8px; }
        .btn-remove-file { color: #dc3545; cursor: pointer; font-size: 0.8rem; text-decoration: underline; }
        .existing-file { font-size: 0.85rem; display: flex; align-items: center; gap: 10px; background: #e9ecef; padding: 5px 10px; border-radius: 4px; margin-bottom: 5px; }
    </style>

    <div class="container mt-4">
        {{-- Mudamos para a rota de update e adicionamos o método PUT --}}
        <form action="{{ route('compras.update', $compra->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-2">
                    <h6 class="mb-0">✏️ Editar Requisição #{{ $compra->id }}</h6>
                    <button type="button" id="add-item" class="btn btn-sm btn-success">
                        <i class="fas fa-plus"></i> + Adicionar Artigo
                    </button>
                </div>
                
                <div class="card-body p-3">
                    <div class="row g-2 mb-1 d-none d-md-flex text-secondary">
                        <div class="col-md-5"><small class="form-label-sm">Material / Peça</small></div>
                        <div class="col-md-2"><small class="form-label-sm">Qtd.</small></div>
                        <div class="col-md-4"><small class="form-label-sm">Destino</small></div>
                        <div class="col-md-1"></div>
                    </div>

                    <div id="items-container">
                        {{-- Loop para carregar itens existentes --}}
                        @foreach($compra->items as $index => $item)
                        <div class="item-row row g-2 align-items-center">
                            <div class="col-md-5">
                                <input type="text" name="items[{{ $index }}][name]" class="form-control form-control-sm" value="{{ $item->item_name }}" required>
                                {{-- Se usar IDs para atualizar registros existentes, inclua aqui --}}
                                <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="items[{{ $index }}][quantity]" class="form-control form-control-sm" min="1" value="{{ $item->quantity }}" required>
                            </div>
                            <div class="col-md-4">
                                <input list="destinos-list" name="items[{{ $index }}][destino]" class="form-control form-control-sm" value="{{ $item->destino }}" required>
                            </div>
                            <div class="col-md-1 text-end">
                                <button type="button" class="btn btn-sm btn-outline-danger remove-item" {{ $loop->first && count($compra->items) == 1 ? 'disabled' : '' }}>×</button>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <datalist id="destinos-list">
                        <option value="Stock">
                        <option value="Máquina">
                        <option value="Oficina">
                    </datalist>

                    <div class="row g-3 mt-3 p-3 bg-light rounded border">
                        <div class="col-md-12">
                            <label for="description" class="form-label-sm">📝 Descrição / Justificação</label>
                            <textarea name="description" id="description" rows="3" class="form-control form-control-sm">{{ $compra->description }}</textarea>
                        </div>
{{--                         
                        <div class="col-md-6 mt-3">
                            <label class="form-label-sm">Depratamento / Função</label>
                            <input type="text" name="fornecedor" class="form-control form-control-sm" value="{{ $compra->fornecedor }}">
                        </div> --}}

                        <div class="col-md-4 mt-3">
                            <label for="urgencia" class="form-label-sm">🚨 Nível de Urgência</label>
                            <select name="urgencia" id="urgencia" class="form-select form-control-sm">
                                <option value="Normal" {{ $compra->urgencia == 'Normal' ? 'selected' : '' }}>Normal</option>
                                <option value="Alta" {{ $compra->urgencia == 'Alta' ? 'selected' : '' }}>Alta (Urgente)</option>
                                <option value="Crítica" {{ $compra->urgencia == 'Crítica' ? 'selected' : '' }}>Crítica (Carro Parado)</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mt-3">
                            <label class="form-label-sm">📁 Anexos Atuais</label>
                            <div class="mb-2">
                                @forelse($compra->attachments as $file)
                                    <div class="existing-file">
                                        <i class="bi bi-file-earmark-check"></i> 
                                        <span class="text-truncate" style="max-width: 200px;">{{ $file->file_name }}</span>
                                        {{-- Link para remover o ficheiro se tiveres essa rota --}}
                                        <div class="form-check ms-auto">
                                            <input class="form-check-input" type="checkbox" name="delete_attachments[]" value="{{ $file->id }}" id="del{{ $file->id }}">
                                            <label class="form-check-label small text-danger" for="del{{ $file->id }}">Eliminar</label>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted small">Sem anexos.</p>
                                @endforelse
                            </div>

                            <label class="form-label-sm mt-2">➕ Adicionar Novos Ficheiros</label>
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

                    <div class="mt-4 d-flex justify-content-between">
                        <a href="{{ route('compras.index') }}" class="btn btn-light border px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-5 font-weight-bold shadow-sm">
                            💾 Atualizar Requisição (<span id="item-count">{{ count($compra->items) }}</span>)
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Iniciar o index com base no número de itens já existentes para não sobrescrever o array no POST
        let itemIndex = {{ count($compra->items) }};
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
                const rows = document.querySelectorAll('.item-row');
                if(rows.length > 1) {
                    e.target.closest('.item-row').remove();
                    updateCount();
                }
            }
        });

        function updateCount() {
            countSpan.innerText = document.querySelectorAll('.item-row').length;
        }

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
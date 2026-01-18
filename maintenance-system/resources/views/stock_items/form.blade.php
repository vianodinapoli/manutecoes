{{--
    Este partial é usado em create.blade.php e edit.blade.php (para Stock Items).
--}}
@php
    $stockItem = $stockItem ?? (object)[]; 
    $estados = ['Novo', 'Recondicionado', 'Usado'];
    $currentEstado = old('estado', $stockItem->estado ?? 'Novo');
@endphp

<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">ℹ️ Identificação e Localização</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            {{-- Nome do Artigo --}}
            <div class="col-12 col-md-8">
                <div class="mb-3">
                    <label for="nome" class="form-label font-weight-bold">Nome do Artigo / Peça <span class="text-danger">*</span></label>
                    <input type="text" id="nome" name="nome" class="form-control @error('nome') is-invalid @enderror" required 
                           placeholder="Ex: Filtro de Combustível Principal"
                           value="{{ old('nome', $stockItem->nome ?? null) }}">
                    @error('nome')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Referência --}}
            <div class="col-12 col-md-4">
                <div class="mb-3">
                    <label for="referencia" class="form-label font-weight-bold">Referência <span class="text-danger">*</span></label>
                    <input type="text" id="referencia" name="referencia" class="form-control @error('referencia') is-invalid @enderror" required 
                           value="{{ old('referencia', $stockItem->referencia ?? null) }}">
                    @error('referencia')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            {{-- Marca/Fabricante --}}
            <div class="col-12 col-md-6">
                <div class="mb-3">
                    <label for="marca_fabricante" class="form-label font-weight-bold">Marca/Fabricante</label>
                    <input type="text" id="marca_fabricante" name="marca_fabricante" class="form-control @error('marca_fabricante') is-invalid @enderror" 
                           value="{{ old('marca_fabricante', $stockItem->marca_fabricante ?? null) }}">
                </div>
            </div>
            
            {{-- Modelo --}}
            <div class="col-12 col-md-6">
                <div class="mb-3">
                    <label for="modelo" class="form-label font-weight-bold">Modelo</label>
                    <input type="text" id="modelo" name="modelo" class="form-control @error('modelo') is-invalid @enderror" 
                           value="{{ old('modelo', $stockItem->modelo ?? null) }}">
                </div>
            </div>

            {{-- Número de Armazém --}}
            <div class="col-12 col-sm-6 col-md-3">
                <div class="mb-3">
                    <label for="numero_armazem" class="form-label font-weight-bold">Número de Armazém <span class="text-danger">*</span></label>
                    <input type="text" id="numero_armazem" name="numero_armazem" class="form-control @error('numero_armazem') is-invalid @enderror" required 
                           value="{{ old('numero_armazem', $stockItem->numero_armazem ?? null) }}">
                </div>
            </div>
            
            {{-- Secção do Armazém --}}
            <div class="col-12 col-sm-6 col-md-3">
                <div class="mb-3">
                    <label for="seccao_armazem" class="form-label font-weight-bold">Secção do Armazém</label>
                    <input type="text" id="seccao_armazem" name="seccao_armazem" class="form-control @error('seccao_armazem') is-invalid @enderror" 
                           value="{{ old('seccao_armazem', $stockItem->seccao_armazem ?? null) }}">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">⚙️ Categorização e Stock</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            {{-- Categoria --}}
            <div class="col-12 col-md-6">
                <div class="mb-3">
                    <label for="categoria" class="form-label font-weight-bold">Categoria</label>
                    <input type="text" id="categoria" name="categoria" class="form-control @error('categoria') is-invalid @enderror" 
                           value="{{ old('categoria', $stockItem->categoria ?? null) }}">
                </div>
            </div>
            
            {{-- Sistema da Máquina --}}
            <div class="col-12 col-md-6">
                <div class="mb-3">
                    <label for="sistema_maquina" class="form-label font-weight-bold">Sistema da Máquina</label>
                    <input type="text" id="sistema_maquina" name="sistema_maquina" class="form-control @error('sistema_maquina') is-invalid @enderror" 
                           value="{{ old('sistema_maquina', $stockItem->sistema_maquina ?? null) }}">
                </div>
            </div>

            {{-- Estado (Dropdown) --}}
            <div class="col-12 col-md-6">
                <div class="mb-3">
                    <label for="estado" class="form-label font-weight-bold">Estado do Item <span class="text-danger">*</span></label>
                    <select id="estado" name="estado" class="form-select @error('estado') is-invalid @enderror" required>
                        @foreach($estados as $estado)
                            <option value="{{ $estado }}" {{ $currentEstado == $estado ? 'selected' : '' }}>
                                {{ $estado }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            {{-- Quantidade --}}
            <div class="col-12 col-md-6">
                <div class="mb-3">
                    <label for="quantidade" class="form-label font-weight-bold">Quantidade em Stock <span class="text-danger">*</span></label>
                    <input type="number" id="quantidade" name="quantidade" class="form-control @error('quantidade') is-invalid @enderror" required min="0"
                           value="{{ old('quantidade', $stockItem->quantidade ?? 0) }}">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-secondary text-white">
        <h5 class="mb-0">➕ Campos Personalizados (Metadata)</h5>
    </div>
    <div class="card-body">
        <p class="text-muted small">Adicione detalhes específicos como Voltagem, Cor ou Prazo de Entrega.</p>
        
        <div id="camposPersonalizados" class="mb-3">
            @if(isset($stockItem->metadata) && is_array($stockItem->metadata))
                @php $dynamicFieldCounter = 0; @endphp
                @foreach($stockItem->metadata as $key => $value)
                    @php $dynamicFieldCounter++; @endphp
                    <div class="row g-2 align-items-center mb-2 custom-field-row">
                        <div class="col-12 col-sm-4">
                            <input type="text" name="metadata_key[]" class="form-control" placeholder="Nome" value="{{ $key }}">
                        </div>
                        <div class="col-10 col-sm-7">
                            <input type="text" name="metadata_value[]" class="form-control" placeholder="Valor" value="{{ $value }}">
                        </div>
                        <div class="col-2 col-sm-1 text-end">
                            <button type="button" class="btn btn-danger btn-sm w-100" onclick="removerCampo(this)">
                                &times;
                            </button>
                        </div>
                    </div>
                @endforeach
            @else
                 @php $dynamicFieldCounter = 0; @endphp
            @endif
        </div>

        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="adicionarCampo()">
            + Adicionar Novo Campo
        </button>
    </div>
</div>

<script>
    let customFieldCounter = {{ $dynamicFieldCounter }};

    function adicionarCampo() {
        customFieldCounter++;
        const container = document.getElementById('camposPersonalizados');
        const newField = document.createElement('div');
        newField.className = 'row g-2 align-items-center mb-2 custom-field-row';
        newField.innerHTML = `
            <div class="col-12 col-sm-4">
                <input type="text" name="metadata_key[]" class="form-control" placeholder="Nome">
            </div>
            <div class="col-10 col-sm-7">
                <input type="text" name="metadata_value[]" class="form-control" placeholder="Valor">
            </div>
            <div class="col-2 col-sm-1 text-end">
                <button type="button" class="btn btn-danger btn-sm w-100" onclick="removerCampo(this)">
                    &times;
                </button>
            </div>
        `;
        container.appendChild(newField);
    }

    function removerCampo(button) {
        button.closest('.custom-field-row').remove();
    }
</script>
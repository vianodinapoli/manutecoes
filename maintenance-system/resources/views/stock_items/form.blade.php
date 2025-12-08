{{--
    Este partial é usado em create.blade.php e edit.blade.php (para Stock Items).
    Se estiver em modo de edição, a variável $stockItem existirá.
--}}
@php
    $stockItem = $stockItem ?? (object)[]; // Garante que $stockItem é um objeto vazio em 'create'
    $estados = ['Novo', 'Recondicionado', 'Usado'];
    $currentEstado = old('estado', $stockItem->estado ?? 'Novo');
@endphp

<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">ℹ️ Identificação e Localização</h5>
    </div>
    <div class="card-body">
        
        <div class="row">
            {{-- Referência --}}
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="referencia" class="form-label">Referência / Part Number <span class="text-danger">*</span></label>
                    <input type="text" id="referencia" name="referencia" class="form-control @error('referencia') is-invalid @enderror" required 
                           value="{{ old('referencia', $stockItem->referencia ?? null) }}">
                    @error('referencia')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            {{-- Número de Armazém --}}
            <div class="col-md-3">
                <div class="mb-3">
                    <label for="numero_armazem" class="form-label">Número de Armazém <span class="text-danger">*</span></label>
                    <input type="text" id="numero_armazem" name="numero_armazem" class="form-control @error('numero_armazem') is-invalid @enderror" required 
                           value="{{ old('numero_armazem', $stockItem->numero_armazem ?? null) }}">
                    @error('numero_armazem')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            {{-- Secção do Armazém --}}
            <div class="col-md-3">
                <div class="mb-3">
                    <label for="seccao_armazem" class="form-label">Secção do Armazém</label>
                    <input type="text" id="seccao_armazem" name="seccao_armazem" class="form-control @error('seccao_armazem') is-invalid @enderror" 
                           value="{{ old('seccao_armazem', $stockItem->seccao_armazem ?? null) }}">
                    @error('seccao_armazem')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Marca/Fabricante --}}
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="marca_fabricante" class="form-label">Marca/Fabricante</label>
                    <input type="text" id="marca_fabricante" name="marca_fabricante" class="form-control @error('marca_fabricante') is-invalid @enderror" 
                           value="{{ old('marca_fabricante', $stockItem->marca_fabricante ?? null) }}">
                    @error('marca_fabricante')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            {{-- Modelo --}}
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="modelo" class="form-label">Modelo</label>
                    <input type="text" id="modelo" name="modelo" class="form-control @error('modelo') is-invalid @enderror" 
                           value="{{ old('modelo', $stockItem->modelo ?? null) }}">
                    @error('modelo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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
        <div class="row">
            {{-- Categoria --}}
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="categoria" class="form-label">Categoria</label>
                    <input type="text" id="categoria" name="categoria" class="form-control @error('categoria') is-invalid @enderror" 
                           value="{{ old('categoria', $stockItem->categoria ?? null) }}">
                    @error('categoria')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            {{-- Sistema da Máquina --}}
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="sistema_maquina" class="form-label">Sistema da Máquina (Ex: Hidráulico, Motor)</label>
                    <input type="text" id="sistema_maquina" name="sistema_maquina" class="form-control @error('sistema_maquina') is-invalid @enderror" 
                           value="{{ old('sistema_maquina', $stockItem->sistema_maquina ?? null) }}">
                    @error('sistema_maquina')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Estado (Dropdown) --}}
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="estado" class="form-label">Estado do Item <span class="text-danger">*</span></label>
                    <select id="estado" name="estado" class="form-select @error('estado') is-invalid @enderror" required>
                        @foreach($estados as $estado)
                            <option value="{{ $estado }}" {{ $currentEstado == $estado ? 'selected' : '' }}>
                                {{ $estado }}
                            </option>
                        @endforeach
                    </select>
                    @error('estado')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            {{-- Quantidade --}}
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="quantidade" class="form-label">Quantidade em Stock <span class="text-danger">*</span></label>
                    <input type="number" id="quantidade" name="quantidade" class="form-control @error('quantidade') is-invalid @enderror" required min="0"
                           value="{{ old('quantidade', $stockItem->quantidade ?? 0) }}">
                    @error('quantidade')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>

<hr>

{{-- ================================================= --}}
{{-- SECÇÃO DE CAMPOS PERSONALIZADOS (Metadata) --}}
{{-- ================================================= --}}
<div class="card shadow-sm mb-4">
    <div class="card-header bg-secondary text-white">
        <h5 class="mb-0">➕ Campos Personalizados (Metadata)</h5>
    </div>
    <div class="card-body">
        <p>Use esta secção para adicionar quaisquer detalhes adicionais específicos do item (ex: Voltagem, Cor, Prazo de Entrega).</p>
        
        <div id="camposPersonalizados" class="mb-3">
            {{-- Campos existentes (se em modo de edição) --}}
            @if(isset($stockItem->metadata) && is_array($stockItem->metadata))
                @php $dynamicFieldCounter = 0; @endphp
                @foreach($stockItem->metadata as $key => $value)
                    @php $dynamicFieldCounter++; @endphp
                    <div class="row g-3 align-items-center mb-2 custom-field-row">
                        <div class="col-4">
                            <input type="text" name="metadata_key[]" class="form-control" placeholder="Nome do Campo" value="{{ $key }}">
                        </div>
                        <div class="col-7">
                            <input type="text" name="metadata_value[]" class="form-control" placeholder="Valor do Campo" value="{{ $value }}">
                        </div>
                        <div class="col-1 text-end">
                            <button type="button" class="btn btn-danger btn-sm" onclick="removerCampo(this)">
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
            + Adicionar Novo Campo Personalizado
        </button>
    </div>
</div>

{{-- Script para adicionar e remover campos dinâmicos --}}
<script>
    let customFieldCounter = {{ $dynamicFieldCounter }};

    function adicionarCampo() {
        customFieldCounter++;
        const container = document.getElementById('camposPersonalizados');
        
        const newField = document.createElement('div');
        newField.className = 'row g-3 align-items-center mb-2 custom-field-row';
        newField.innerHTML = `
            <div class="col-4">
                <input type="text" name="metadata_key[]" class="form-control" placeholder="Nome do Campo">
            </div>
            <div class="col-7">
                <input type="text" name="metadata_value[]" class="form-control" placeholder="Valor do Campo">
            </div>
            <div class="col-1 text-end">
                <button type="button" class="btn btn-danger btn-sm" onclick="removerCampo(this)">
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
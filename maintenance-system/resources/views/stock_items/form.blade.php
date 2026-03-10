{{--
    Partial usado em create.blade.php e edit.blade.php (Stock Items).
--}}
@php
    $stockItem     = $stockItem ?? (object)[];
    $estados       = ['Novo', 'Recondicionado', 'Usado'];
    $currentEstado = old('estado', $stockItem->estado ?? 'Novo');
@endphp

<style>
    .field-lbl{font-size:.6rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#94a3b8;display:block;margin-bottom:5px}
    .form-control,.form-select{font-size:.83rem;border:1px solid #e2e8f0;border-radius:8px;color:#1e293b;background:#fff;transition:border-color .15s,box-shadow .15s}
    .form-control:focus,.form-select:focus{border-color:#64748b;box-shadow:0 0 0 3px rgba(100,116,139,.1);outline:none}
    .invalid-feedback{font-size:.7rem;color:#dc2626;margin-top:4px}
    .form-section{margin-bottom:24px}
    .form-section-title{font-size:.65rem;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;color:#94a3b8;display:flex;align-items:center;gap:8px;margin-bottom:14px}
    .form-section-title::after{content:'';flex:1;height:1px;background:#e2e8f0}
    .meta-row{display:grid;grid-template-columns:1fr 1fr 28px;gap:8px;align-items:center;margin-bottom:8px}
    .btn-add-field{display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:7px;font-size:.72rem;font-weight:700;border:1px dashed #cbd5e1;background:#fff;color:#475569;cursor:pointer;transition:all .15s}
    .btn-add-field:hover{background:#f8fafc;border-color:#94a3b8}
    .btn-remove-field{width:28px;height:28px;border-radius:7px;border:1px solid #fecaca;background:#fff;color:#ef4444;font-size:.9rem;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;transition:background .15s;flex-shrink:0}
    .btn-remove-field:hover{background:#fef2f2}
</style>

{{-- ── SECÇÃO 1: IDENTIFICAÇÃO ── --}}
<div class="form-section">
    <div class="form-section-title">Identificação e Localização</div>
    <div class="row g-3">
        <div class="col-md-8">
            <label class="field-lbl" for="nome">Nome do Artigo / Peça <span style="color:#ef4444;">*</span></label>
            <input type="text" id="nome" name="nome"
                   class="form-control @error('nome') is-invalid @enderror"
                   placeholder="Ex: Filtro de Combustível Principal"
                   value="{{ old('nome', $stockItem->nome ?? '') }}" required>
            @error('nome')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
            <label class="field-lbl" for="referencia">Referência <span style="color:#ef4444;">*</span></label>
            <input type="text" id="referencia" name="referencia"
                   class="form-control @error('referencia') is-invalid @enderror"
                   value="{{ old('referencia', $stockItem->referencia ?? '') }}" required>
            @error('referencia')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="field-lbl" for="marca_fabricante">Marca / Fabricante</label>
            <input type="text" id="marca_fabricante" name="marca_fabricante"
                   class="form-control @error('marca_fabricante') is-invalid @enderror"
                   value="{{ old('marca_fabricante', $stockItem->marca_fabricante ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="field-lbl" for="modelo">Modelo</label>
            <input type="text" id="modelo" name="modelo"
                   class="form-control @error('modelo') is-invalid @enderror"
                   value="{{ old('modelo', $stockItem->modelo ?? '') }}">
        </div>
        <div class="col-md-3">
            <label class="field-lbl" for="numero_armazem">Nº de Armazém <span style="color:#ef4444;">*</span></label>
            <input type="text" id="numero_armazem" name="numero_armazem"
                   class="form-control @error('numero_armazem') is-invalid @enderror"
                   value="{{ old('numero_armazem', $stockItem->numero_armazem ?? '') }}" required>
            @error('numero_armazem')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
            <label class="field-lbl" for="seccao_armazem">Secção do Armazém</label>
            <input type="text" id="seccao_armazem" name="seccao_armazem"
                   class="form-control @error('seccao_armazem') is-invalid @enderror"
                   value="{{ old('seccao_armazem', $stockItem->seccao_armazem ?? '') }}">
        </div>
    </div>
</div>

{{-- ── SECÇÃO 2: CATEGORIZAÇÃO ── --}}
<div class="form-section">
    <div class="form-section-title">Categorização e Stock</div>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="field-lbl" for="categoria">Categoria</label>
            <input type="text" id="categoria" name="categoria"
                   class="form-control @error('categoria') is-invalid @enderror"
                   value="{{ old('categoria', $stockItem->categoria ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="field-lbl" for="sistema_maquina">Sistema da Máquina</label>
            <input type="text" id="sistema_maquina" name="sistema_maquina"
                   class="form-control @error('sistema_maquina') is-invalid @enderror"
                   value="{{ old('sistema_maquina', $stockItem->sistema_maquina ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="field-lbl" for="estado">Estado do Item <span style="color:#ef4444;">*</span></label>
            <select id="estado" name="estado"
                    class="form-select @error('estado') is-invalid @enderror" required>
                @foreach($estados as $estado)
                <option value="{{ $estado }}" {{ $currentEstado == $estado ? 'selected' : '' }}>
                    {{ $estado }}
                </option>
                @endforeach
            </select>
            @error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="field-lbl" for="quantidade">Quantidade em Stock <span style="color:#ef4444;">*</span></label>
            <input type="number" id="quantidade" name="quantidade" min="0"
                   class="form-control @error('quantidade') is-invalid @enderror"
                   value="{{ old('quantidade', $stockItem->quantidade ?? 0) }}" required>
            @error('quantidade')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

{{-- ── SECÇÃO 3: METADATA ── --}}
<div class="form-section mb-0">
    <div class="form-section-title">Campos Personalizados</div>
    <p style="font-size:.72rem;color:#94a3b8;margin-bottom:12px;">
        Adicione detalhes específicos como voltagem, cor, prazo de entrega, etc.
    </p>

    <div id="camposPersonalizados">
        @php $dynamicFieldCounter = 0; @endphp
        @if(isset($stockItem->metadata) && is_array($stockItem->metadata))
            @foreach($stockItem->metadata as $key => $value)
            @php $dynamicFieldCounter++; @endphp
            <div class="meta-row custom-field-row">
                <input type="text" name="metadata_key[]"
                       class="form-control" placeholder="Nome do campo"
                       value="{{ $key }}">
                <input type="text" name="metadata_value[]"
                       class="form-control" placeholder="Valor"
                       value="{{ $value }}">
                <button type="button" class="btn-remove-field" onclick="removerCampo(this)">×</button>
            </div>
            @endforeach
        @endif
    </div>

    <button type="button" class="btn-add-field mt-1" onclick="adicionarCampo()">
        <i class="bi bi-plus"></i> Adicionar campo
    </button>
</div>

<script>
    let customFieldCounter = {{ $dynamicFieldCounter }};

    function adicionarCampo() {
        customFieldCounter++;
        const container = document.getElementById('camposPersonalizados');
        const row = document.createElement('div');
        row.className = 'meta-row custom-field-row';
        row.innerHTML = `
            <input type="text" name="metadata_key[]" class="form-control" placeholder="Nome do campo">
            <input type="text" name="metadata_value[]" class="form-control" placeholder="Valor">
            <button type="button" class="btn-remove-field" onclick="removerCampo(this)">×</button>
        `;
        container.appendChild(row);
    }

    function removerCampo(btn) {
        btn.closest('.custom-field-row').remove();
    }
</script>
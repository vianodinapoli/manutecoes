@php
    $equipmentTypes = [
        'Viatura', 'Camião / Cisterna Água / Atrelado', 'Equipamento Perfuração', 'Grua',
        'Multifunções / Plataforma Elevatória / Empilhador', 'Torre Iluminação / Gerador / Compressor / Moto-Bomba',
        'Escavadora / Bulldozer / Pá Carregadora / Tractor / Retro-Escavadora / Cilindro / Dumper',
        'Martelo / Placa / Saltitão / Betoneira / Baileu', 'Motociclo', 'Imóvel',
    ];
    $currentType   = old('tipo_equipamento', $machine->tipo_equipamento ?? null);
    $currentStatus = old('status', $machine->status ?? 'Operacional');
@endphp

<style>
    .field-lbl{font-size:.6rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#94a3b8;display:block;margin-bottom:5px}
    .form-control,.form-select{font-size:.83rem;border:1px solid #e2e8f0;border-radius:8px;color:#1e293b;background:#fff;transition:border-color .15s,box-shadow .15s}
    .form-control:focus,.form-select:focus{border-color:#64748b;box-shadow:0 0 0 3px rgba(100,116,139,.1);outline:none}
    .input-group-text{font-size:.8rem;background:#f8fafc;border:1px solid #e2e8f0;color:#94a3b8}
    .invalid-feedback{font-size:.7rem;color:#dc2626;margin-top:4px}
    .form-section-divider{font-size:.65rem;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;color:#94a3b8;display:flex;align-items:center;gap:8px;margin:20px 0 14px}
    .form-section-divider::after{content:'';flex:1;height:1px;background:#e2e8f0}
</style>

<div class="row g-3">

    {{-- ── IDENTIFICAÇÃO ── --}}
    <div class="col-12">
        <div class="form-section-divider">Identificação</div>
    </div>

    <div class="col-md-4">
        <label class="field-lbl" for="numero_interno">
            Nº Interno (Activo) <span style="color:#ef4444;">*</span>
        </label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-hash"></i></span>
            <input type="text" id="numero_interno" name="numero_interno"
                   class="form-control @error('numero_interno') is-invalid @enderror"
                   placeholder="Ex: MC-001"
                   value="{{ old('numero_interno', $machine->numero_interno ?? '') }}" required>
        </div>
        @error('numero_interno')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-8">
        <label class="field-lbl" for="tipo_equipamento">
            Tipo de Equipamento <span style="color:#ef4444;">*</span>
        </label>
        <select id="tipo_equipamento" name="tipo_equipamento"
                class="form-select @error('tipo_equipamento') is-invalid @enderror" required>
            <option value="">— Selecione a categoria —</option>
            @foreach($equipmentTypes as $type)
            <option value="{{ $type }}" {{ $currentType == $type ? 'selected' : '' }}>{{ $type }}</option>
            @endforeach
        </select>
        @error('tipo_equipamento')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- ── DOCUMENTAÇÃO ── --}}
    <div class="col-12">
        <div class="form-section-divider">Documentação e Registo</div>
    </div>

    <div class="col-md-3">
        <label class="field-lbl" for="matricula">Matrícula</label>
        <input type="text" id="matricula" name="matricula"
               class="form-control"
               placeholder="AA-00-BB"
               value="{{ old('matricula', $machine->matricula ?? '') }}">
    </div>

    <div class="col-md-5">
        <label class="field-lbl" for="nr_chassi">Nº de Chassi</label>
        <input type="text" id="nr_chassi" name="nr_chassi"
               class="form-control"
               placeholder="VIN Number"
               value="{{ old('nr_chassi', $machine->nr_chassi ?? '') }}">
    </div>

    {{-- ── ESPECIFICAÇÕES ── --}}
    <div class="col-12">
        <div class="form-section-divider">Especificações Técnicas</div>
    </div>

    <div class="col-md-3">
        <label class="field-lbl" for="marca">Marca</label>
        <input type="text" id="marca" name="marca"
               class="form-control @error('marca') is-invalid @enderror"
               placeholder="Ex: Toyota"
               value="{{ old('marca', $machine->marca ?? '') }}">
        @error('marca')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="field-lbl" for="modelo">Modelo</label>
        <input type="text" id="modelo" name="modelo"
               class="form-control @error('modelo') is-invalid @enderror"
               placeholder="Ex: Hilux"
               value="{{ old('modelo', $machine->modelo ?? '') }}">
        @error('modelo')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- ── LOCALIZAÇÃO E OPERAÇÃO ── --}}
    <div class="col-12">
        <div class="form-section-divider">Localização e Operação</div>
    </div>

    <div class="col-md-5">
        <label class="field-lbl" for="localizacao">
            Localização Actual <span style="color:#ef4444;">*</span>
        </label>
        <input type="text" id="localizacao" name="localizacao"
               class="form-control @error('localizacao') is-invalid @enderror"
               value="{{ old('localizacao', $machine->localizacao ?? '') }}" required>
        @error('localizacao')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="field-lbl" for="operador">Responsável / Operador</label>
        <input type="text" id="operador" name="operador"
               class="form-control @error('operador') is-invalid @enderror"
               value="{{ old('operador', $machine->operador ?? '') }}">
        @error('operador')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="field-lbl" for="status">
            Estado Operacional <span style="color:#ef4444;">*</span>
        </label>
        <select id="status" name="status"
                class="form-select @error('status') is-invalid @enderror" required>
            <option value="Operacional" {{ $currentStatus == 'Operacional' ? 'selected' : '' }}>Operacional</option>
            <option value="Avariada"    {{ $currentStatus == 'Avariada'    ? 'selected' : '' }}>Avariada</option>
            <option value="Desativada"  {{ $currentStatus == 'Desativada'  ? 'selected' : '' }}>Desativada</option>
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- ── OBSERVAÇÕES ── --}}
    <div class="col-12">
        <div class="form-section-divider">Notas Adicionais</div>
    </div>

    <div class="col-12">
        <label class="field-lbl" for="observacoes">Observações</label>
        <textarea id="observacoes" name="observacoes" rows="3"
                  class="form-control @error('observacoes') is-invalid @enderror"
                  placeholder="Informação relevante sobre o estado ou histórico do equipamento..."
                  style="resize:vertical;">{{ old('observacoes', $machine->observacoes ?? '') }}</textarea>
        @error('observacoes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

</div>
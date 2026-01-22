@php
    $equipmentTypes = [
        'Viatura', 'Camião / Cisterna Água / Atrelado', 'Equipamento Perfuração', 'Grua',
        'Multifunções / Plataforma Elevatória / Empilhador', 'Torre Iluminação / Gerador / Compressor / Moto-Bomba',
        'Escavadora / Bulldozer / Pá Carregadora / Tractor / Retro-Escavadora / Cilindro / Dumper',
        'Martelo / Placa / Saltitão / Betoneira / Baileu', 'Motociclo', 'Imóvel',
    ];
    $currentType = old('tipo_equipamento', $machine->tipo_equipamento ?? null);
    $currentStatus = old('status', $machine->status ?? 'Operacional');
@endphp

<style>
    .form-section-title { font-size: 1.1rem; font-weight: 700; color: #0d6efd; display: flex; align-items: center; gap: 10px; }
    .card-form { border: none; border-radius: 12px; transition: all 0.3s ease; }
    .card-form:hover { box-shadow: 0 5px 15px rgba(0,0,0,0.08) !important; }
    .form-label { font-weight: 600; font-size: 0.9rem; color: #495057; }
    .form-control, .form-select { border-radius: 8px; padding: 10px 12px; border: 1px solid #dee2e6; }
    .form-control:focus, .form-select:focus { border-color: #0d6efd; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1); }
    .input-group-text { border-radius: 8px 0 0 8px; background-color: #f8f9fa; border-right: none; }
    .has-icon .form-control { border-radius: 0 8px 8px 0; }
</style>

<div class="row g-4">
    
    {{-- === COLUNA 1: IDENTIFICAÇÃO E DADOS REGISTRAIS === --}}
    <div class="col-md-6">
        <div class="card card-form shadow-sm p-4 h-100">
            <h5 class="form-section-title mb-4">
                <i class="bi bi-info-circle-fill"></i> Informações de Identificação
            </h5>
            
            <div class="mb-3">
                <label for="numero_interno" class="form-label">Número Interno (Ativo)</label>
                <div class="input-group has-icon">
                    <span class="input-group-text"><i class="bi bi-hash"></i></span>
                    <input type="text" id="numero_interno" name="numero_interno" 
                           class="form-control @error('numero_interno') is-invalid @enderror" 
                           required value="{{ old('numero_interno', $machine->numero_interno ?? null) }}"
                           placeholder="Ex: MC-001">
                </div>
                @error('numero_interno') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="mb-4">
                <label for="tipo_equipamento" class="form-label">Tipo de Equipamento</label>
                <select id="tipo_equipamento" name="tipo_equipamento" class="form-select @error('tipo_equipamento') is-invalid @enderror" required>
                    <option value="">Selecione a categoria...</option>
                    @foreach($equipmentTypes as $type)
                        <option value="{{ $type }}" {{ $currentType == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
                @error('tipo_equipamento') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <h5 class="form-section-title mb-4 mt-2">
                <i class="bi bi-card-text"></i> Documentação e Registro
            </h5>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="matricula" class="form-label">Matrícula</label>
                    <input type="text" class="form-control" id="matricula" name="matricula" 
                           value="{{ old('matricula', $machine->matricula ?? null) }}" placeholder="AA-00-BB">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="nr_chassi" class="form-label">Nº de Chassi</label>
                    <input type="text" class="form-control" id="nr_chassi" name="nr_chassi" 
                           value="{{ old('nr_chassi', $machine->nr_chassi ?? null) }}" placeholder="Vin Number">
                </div>
            </div>
        </div>
    </div>
    
    {{-- === COLUNA 2: ESPECIFICAÇÕES E STATUS === --}}
    <div class="col-md-6">
        <div class="card card-form shadow-sm p-4 h-100">
            <h5 class="form-section-title mb-4">
                <i class="bi bi-gear-wide-connected"></i> Especificações Técnicas
            </h5>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="marca" class="form-label">Marca</label>
                    <input type="text" id="marca" name="marca" class="form-control @error('marca') is-invalid @enderror" 
                           value="{{ old('marca', $machine->marca ?? null) }}" placeholder="Ex: Toyota">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="modelo" class="form-label">Modelo</label>
                    <input type="text" id="modelo" name="modelo" class="form-control @error('modelo') is-invalid @enderror" 
                           value="{{ old('modelo', $machine->modelo ?? null) }}" placeholder="Ex: Hilux">
                </div>
            </div>

            <h5 class="form-section-title mb-4 mt-2">
                <i class="bi bi-geo-alt-fill"></i> Localização e Operação
            </h5>
            
            <div class="mb-3">
                <label for="localizacao" class="form-label">Localização Atual</label>
                <input type="text" id="localizacao" name="localizacao" class="form-control @error('localizacao') is-invalid @enderror" 
                       required value="{{ old('localizacao', $machine->localizacao ?? null) }}">
            </div>
            
            <div class="row">
                <div class="col-md-7 mb-3">
                    <label for="operador" class="form-label">Responsável/Operador</label>
                    <input type="text" id="operador" name="operador" class="form-control @error('operador') is-invalid @enderror" 
                           value="{{ old('operador', $machine->operador ?? null) }}">
                </div>
                <div class="col-md-5 mb-3">
                    <label for="status" class="form-label">Status Operacional</label>
                    <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="Operacional" {{ $currentStatus == 'Operacional' ? 'selected' : '' }}>✅ Operacional</option>
                        <option value="Avariada" {{ $currentStatus == 'Avariada' ? 'selected' : '' }}>⚠️ Avariada</option>
                        <option value="Desativada" {{ $currentStatus == 'Desativada' ? 'selected' : '' }}>🚫 Desativada</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- === ÁREA DE OBSERVAÇÕES (Full Width) === --}}
    <div class="col-12">
        <div class="card card-form shadow-sm p-4">
            <h5 class="form-section-title mb-3">
                <i class="bi bi-chat-left-dots-fill"></i> Notas Adicionais
            </h5>
            <textarea id="observacoes" name="observacoes" rows="3" 
                      class="form-control @error('observacoes') is-invalid @enderror" 
                      placeholder="Alguma informação relevante sobre o estado ou histórico do equipamento...">{{ old('observacoes', $machine->observacoes ?? null) }}</textarea>
            @error('observacoes') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
</div>
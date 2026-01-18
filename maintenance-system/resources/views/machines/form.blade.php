{{--
    Este partial é usado em create.blade.php e edit.blade.php.
    A variável $machine deve ser passada, ou null na view de criação.
--}}
@php
    // Definição das categorias
    $equipmentTypes = [
        'Viatura',
        'Camião / Cisterna Água / Atrelado',
        'Equipamento Perfuração',
        'Grua',
        'Multifunções / Plataforma Elevatória / Empilhador',
        'Torre Iluminação / Gerador / Compressor / Moto-Bomba',
        'Escavadora / Bulldozer / Pá Carregadora / Tractor / Retro-Escavadora / Cilindro / Dumper',
        'Martelo / Placa / Saltitão / Betoneira / Baileu',
        'Motociclo',
        'Imóvel',
    ];
    // Pegando valores atuais ou antigos para preenchimento
    $currentType = old('tipo_equipamento', $machine->tipo_equipamento ?? null);
    $currentStatus = old('status', $machine->status ?? 'Operacional');
@endphp

<div class="row">
    
    {{-- === COLUNA 1: IDENTIFICAÇÃO E DADOS REGISTRAIS (CHASSI/MATRÍCULA) === --}}
    <div class="col-md-6">
        <h5 class="mb-3 text-primary">Informações Básicas</h5>
        <hr class="mt-0">
        
        {{-- 1. Número Interno (Ativo) --}}
        <div class="mb-3">
            <label for="numero_interno" class="form-label">Número Interno (Ativo):</label>
            <input type="text" id="numero_interno" name="numero_interno" class="form-control @error('numero_interno') is-invalid @enderror" required 
                   value="{{ old('numero_interno', $machine->numero_interno ?? null) }}">
            @error('numero_interno')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        {{-- 2. Tipo de Equipamento (Dropdown) --}}
        <div class="mb-3">
            <label for="tipo_equipamento" class="form-label">Tipo de Equipamento:</label>
            <select id="tipo_equipamento" name="tipo_equipamento" class="form-select @error('tipo_equipamento') is-invalid @enderror" required>
                <option value="">-- Selecione uma Categoria --</option>
                @foreach($equipmentTypes as $type)
                    <option value="{{ $type }}" {{ $currentType == $type ? 'selected' : '' }}>
                        {{ $type }}
                    </option>
                @endforeach
            </select>
            @error('tipo_equipamento')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <h5 class="mb-3 mt-4 text-primary">Dados de Registro</h5>
        <hr class="mt-0">
        
        {{-- 3. Matrícula (Opcional) --}}
        <div class="mb-3">
            <label for="matricula" class="form-label">Matrícula (Opcional):</label>
            <input 
                type="text" 
                class="form-control" 
                id="matricula" 
                name="matricula" 
                value="{{ old('matricula', $machine->matricula ?? null) }}" 
                placeholder="Ex: AB-12-CD"
                maxlength="50"
            >
            @error('matricula')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- 4. Número de Chassi (Opcional) --}}
        <div class="mb-3">
            <label for="nr_chassi" class="form-label">Nº de Chassi (Opcional):</label>
            <input 
                type="text" 
                class="form-control" 
                id="nr_chassi" 
                name="nr_chassi" 
                value="{{ old('nr_chassi', $machine->nr_chassi ?? null) }}" 
                placeholder="Ex: XYZ000012345"
                maxlength="100"
            >
            @error('nr_chassi')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

    </div>
    
    {{-- === COLUNA 2: ESPECIFICAÇÕES E STATUS OPERACIONAL === --}}
    <div class="col-md-6">
        <h5 class="mb-3 text-primary">Especificações Técnicas</h5>
        <hr class="mt-0">

        {{-- 5. Marca --}}
        <div class="mb-3">
            <label for="marca" class="form-label">Marca:</label>
            <input type="text" id="marca" name="marca" class="form-control @error('marca') is-invalid @enderror" 
                   value="{{ old('marca', $machine->marca ?? null) }}">
            @error('marca')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- 6. Modelo --}}
        <div class="mb-3">
            <label for="modelo" class="form-label">Modelo:</label>
            <input type="text" id="modelo" name="modelo" class="form-control @error('modelo') is-invalid @enderror" 
                   value="{{ old('modelo', $machine->modelo ?? null) }}">
            @error('modelo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <h5 class="mb-3 mt-4 text-primary">Localização e Status</h5>
        <hr class="mt-0">
        
        {{-- 7. Localização --}}
        <div class="mb-3">
            <label for="localizacao" class="form-label">Localização Atual:</label>
            <input type="text" id="localizacao" name="localizacao" class="form-control @error('localizacao') is-invalid @enderror" required 
                   value="{{ old('localizacao', $machine->localizacao ?? null) }}">
            @error('localizacao')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        {{-- 8. Operador/Responsável --}}
        <div class="mb-3">
            <label for="operador" class="form-label">Operador/Responsável:</label>
            <input type="text" id="operador" name="operador" class="form-control @error('operador') is-invalid @enderror" 
                   value="{{ old('operador', $machine->operador ?? null) }}">
            @error('operador')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        {{-- 9. Status Operacional --}}
        <div class="mb-3">
            <label for="status" class="form-label">Status Operacional:</label>
            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                <option value="Operacional" {{ $currentStatus == 'Operacional' ? 'selected' : '' }}>Operacional</option>
                <option value="Avariada" {{ $currentStatus == 'Avariada' ? 'selected' : '' }}>Avariada</option>
                <option value="Desativada" {{ $currentStatus == 'Desativada' ? 'selected' : '' }}>Desativada</option>
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

    </div>
</div> 

{{-- === ÁREA DE OBSERVAÇÕES (Full Width) === --}}
<h5 class="mb-3 mt-4 text-primary">Observações</h5>
<hr class="mt-0">
<div class="mb-3">
    <label for="observacoes" class="form-label">Observações/Descrição:</label>
    <textarea id="observacoes" name="observacoes" rows="4" class="form-control @error('observacoes') is-invalid @enderror">{{ old('observacoes', $machine->observacoes ?? null) }}</textarea>
    @error('observacoes')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
{{--
    Este partial é usado em create.blade.php e edit.blade.php.
    Se estiver em modo de edição, a variável $machine existirá.
    Usamos o operador '?? null' para evitar erros na view de criação.
--}}
@php
    // Definição das categorias baseadas no screenshot
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
    $currentType = old('tipo_equipamento', $machine->tipo_equipamento ?? null);
@endphp

<div class="row">
    <div class="col-md-6">
        
        <div class="mb-3">
            <label for="numero_interno" class="form-label">Número Interno (Ativo):</label>
            <input type="text" id="numero_interno" name="numero_interno" class="form-control @error('numero_interno') is-invalid @enderror" required 
                   value="{{ old('numero_interno', $machine->numero_interno ?? null) }}">
            @error('numero_interno')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        {{-- CAMPO ALTERADO: TIPO DE EQUIPAMENTO COM DROPDOWN --}}
        <div class="mb-3">
            <label for="tipo_equipamento" class="form-label">Tipo de Equipamento:</label>
            <select id="tipo_equipamento" name="tipo_equipamento" class="form-select @error('tipo_equipamento') is-invalid @enderror" required>
                <option value="">-- Selecione uma Categoria --</option>
                @foreach($equipmentTypes as $type)
                    {{-- Usa a descrição completa como valor e como texto visível --}}
                    <option value="{{ $type }}" {{ $currentType == $type ? 'selected' : '' }}>
                        {{ $type }}
                    </option>
                @endforeach
            </select>
            @error('tipo_equipamento')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        {{-- FIM DO CAMPO ALTERADO --}}
        
        <div class="mb-3">
            <label for="marca" class="form-label">Marca:</label>
            <input type="text" id="marca" name="marca" class="form-control @error('marca') is-invalid @enderror" 
                   value="{{ old('marca', $machine->marca ?? null) }}">
            @error('marca')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="modelo" class="form-label">Modelo:</label>
            <input type="text" id="modelo" name="modelo" class="form-control @error('modelo') is-invalid @enderror" 
                   value="{{ old('modelo', $machine->modelo ?? null) }}">
            @error('modelo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

    </div>
    
    <div class="col-md-6">
        
        <div class="mb-3">
            <label for="localizacao" class="form-label">Localização:</label>
            <input type="text" id="localizacao" name="localizacao" class="form-control @error('localizacao') is-invalid @enderror" required 
                   value="{{ old('localizacao', $machine->localizacao ?? null) }}">
            @error('localizacao')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-3">
            <label for="operador" class="form-label">Operador/Responsável:</label>
            <input type="text" id="operador" name="operador" class="form-control @error('operador') is-invalid @enderror" 
                   value="{{ old('operador', $machine->operador ?? null) }}">
            @error('operador')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-3">
            <label for="status" class="form-label">Status Operacional:</label>
            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                @php $currentStatus = old('status', $machine->status ?? 'Operacional'); @endphp
                <option value="Operacional" {{ $currentStatus == 'Operacional' ? 'selected' : '' }}>Operacional</option>
                <option value="Avariada" {{ $currentStatus == 'Avariada' ? 'selected' : '' }}>Avariada</option>
                {{-- <option value="Em Manutenção" {{ $currentStatus == 'Em Manutenção' ? 'selected' : '' }}>Em Manutenção</option> --}}
                <option value="Desativada" {{ $currentStatus == 'Desativada' ? 'selected' : '' }}>Desativada</option>
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

    </div>
</div> 
<div class="mb-3">
    <label for="observacoes" class="form-label">Observações/Descrição:</label>
    <textarea id="observacoes" name="observacoes" rows="4" class="form-control @error('observacoes') is-invalid @enderror">{{ old('observacoes', $machine->observacoes ?? null) }}</textarea>
    @error('observacoes')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
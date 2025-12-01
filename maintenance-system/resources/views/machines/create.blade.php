<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Máquina</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5"> 
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>➕ Adicionar Novo Equipamento / Máquina</h1>
        </div>

        <a href="{{ route('machines.index') }}" class="btn btn-secondary mb-3">
            ⬅️ Voltar à Lista
        </a>
    
        <form method="POST" action="{{ route('machines.store') }}">
            @csrf 
            
            @if ($errors->any())
                <div class="alert alert-danger">
                    Por favor, corrija os erros de validação abaixo.
                </div>
            @endif
            
            <div class="row">
                <div class="col-md-6">
                    
                    <div class="mb-3">
                        <label for="numero_interno" class="form-label">Número Interno (Ativo):</label>
                        <input type="text" id="numero_interno" name="numero_interno" class="form-control @error('numero_interno') is-invalid @enderror" required value="{{ old('numero_interno') }}">
                        @error('numero_interno')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="tipo_equipamento" class="form-label">Tipo de Equipamento:</label>
                        <input type="text" id="tipo_equipamento" name="tipo_equipamento" class="form-control @error('tipo_equipamento') is-invalid @enderror" required value="{{ old('tipo_equipamento') }}">
                        @error('tipo_equipamento')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="marca" class="form-label">Marca:</label>
                        <input type="text" id="marca" name="marca" class="form-control @error('marca') is-invalid @enderror" value="{{ old('marca') }}">
                        @error('marca')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="modelo" class="form-label">Modelo:</label>
                        <input type="text" id="modelo" name="modelo" class="form-control @error('modelo') is-invalid @enderror" value="{{ old('modelo') }}">
                        @error('modelo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
                
                <div class="col-md-6">
                    
                    <div class="mb-3">
                        <label for="localizacao" class="form-label">Localização:</label>
                        <input type="text" id="localizacao" name="localizacao" class="form-control @error('localizacao') is-invalid @enderror" required value="{{ old('localizacao') }}">
                        @error('localizacao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="operador" class="form-label">Operador/Responsável:</label>
                        <input type="text" id="operador" name="operador" class="form-control @error('operador') is-invalid @enderror" value="{{ old('operador') }}">
                        @error('operador')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status Operacional:</label>
                        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="Operacional" {{ old('status') == 'Operacional' ? 'selected' : '' }}>Operacional</option>
                            <option value="Avariada" {{ old('status') == 'Avariada' ? 'selected' : '' }}>Avariada</option>
                            <option value="Em Manutenção" {{ old('status') == 'Em Manutenção' ? 'selected' : '' }}>Em Manutenção</option>
                            <option value="Desativada" {{ old('status') == 'Desativada' ? 'selected' : '' }}>Desativada</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div> <div class="mb-3">
                <label for="observacoes" class="form-label">Observações/Descrição:</label>
                <textarea id="observacoes" name="observacoes" rows="4" class="form-control @error('observacoes') is-invalid @enderror">{{ old('observacoes') }}</textarea>
                @error('observacoes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-success btn-lg mt-3">
                💾 Guardar Máquina
            </button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
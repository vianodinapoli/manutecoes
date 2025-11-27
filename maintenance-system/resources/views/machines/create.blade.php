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
            <h1>➕ Adicionar Nova Máquina</h1>
        </div>

        <a href="{{ route('machines.index') }}" class="btn btn-secondary mb-3">
            ⬅️ Voltar à Lista
        </a>
    
        <form method="POST" action="{{ route('machines.store') }}">
            @csrf 
            
            @if ($errors->any())
                <div class="alert alert-danger">
                    Por favor, corrija os erros abaixo.
                </div>
            @endif

            <div class="mb-3">
                <label for="name" class="form-label">Nome da Máquina:</label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" required value="{{ old('name') }}">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="serial_number" class="form-label">Número de Série:</label>
                <input type="text" id="serial_number" name="serial_number" class="form-control @error('serial_number') is-invalid @enderror" required value="{{ old('serial_number') }}">
                @error('serial_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="chassi" class="form-label">Chassi</label>
                <input type="text" name="chassi" id="chassi" class="form-control @error('chassi') is-invalid @enderror" required value="{{ old('chassi') }}">
                 @error('chassi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="location" class="form-label">Localização:</label>
                <input type="text" id="location" name="location" class="form-control @error('location') is-invalid @enderror" required value="{{ old('location') }}">
                @error('location')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
    <label for="status">Status</label>
    <select name="status" id="status" class="form-control" required>
        <option value="operacional">Operacional</option>
        <option value="em manutenção">Em manutenção</option>
        <option value="avariada">Avariada</option>
        <option value="terreno">Terreno</option>
    </select>
</div>


            <div class="mb-3">
                <label for="description" class="form-label">Descrição:</label>
                <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-success">
                💾 Guardar Máquina
            </button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Máquina: {{ $machine->name }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5"> 
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>✏️ Editar Máquina: <span class="text-primary">{{ $machine->name }}</span></h1>
        </div>

        <div class="mb-4 d-flex gap-2">
            <a href="{{ route('machines.index') }}" class="btn btn-secondary">
                ⬅️ Voltar à Lista
            </a>
            <a href="{{ route('machines.show', $machine->id) }}" class="btn btn-info">
                👁️ Ver Detalhes
            </a>
        </div>
        
        <form method="POST" action="{{ route('machines.update', $machine->id) }}">
            @csrf 
            @method('PUT') @if ($errors->any())
                <div class="alert alert-danger">
                    Por favor, corrija os erros de validação abaixo.
                </div>
            @endif

            <div class="mb-3">
                <label for="name" class="form-label">Nome da Máquina:</label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" required value="{{ old('name', $machine->name) }}">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="serial_number" class="form-label">Número de Série:</label>
                <input type="text" id="serial_number" name="serial_number" class="form-control @error('serial_number') is-invalid @enderror" required value="{{ old('serial_number', $machine->serial_number) }}">
                @error('serial_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="location" class="form-label">Localização:</label>
                <input type="text" id="location" name="location" class="form-control @error('location') is-invalid @enderror" required value="{{ old('location', $machine->location) }}">
                @error('location')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Descrição:</label>
                <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror">{{ old('description', $machine->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <button type="submit" class="btn btn-success mt-3">
                ✅ Atualizar Máquina
            </button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
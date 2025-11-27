<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar Máquina: {{ $machine->name }}</title>
</head>
<body>
    <h1>✏️ Editar Máquina: {{ $machine->name }}</h1>

    <p>
        <a href="{{ route('machines.index') }}">Voltar à Lista</a> | 
        <a href="{{ route('machines.show', $machine->id) }}">Ver Detalhes</a>
    </p>
    
    <form method="POST" action="{{ route('machines.update', $machine->id) }}">
        @csrf 
        @method('PUT') <div>
            <label for="name">Nome da Máquina:</label>
            <input type="text" id="name" name="name" required value="{{ old('name', $machine->name) }}">
            @error('name') <div style="color: red;">{{ $message }}</div> @enderror
        </div>
        <br>
        
        <div>
            <label for="serial_number">Número de Série:</label>
            <input type="text" id="serial_number" name="serial_number" required value="{{ old('serial_number', $machine->serial_number) }}">
            @error('serial_number') <div style="color: red;">{{ $message }}</div> @enderror
        </div>
        <br>

        <div>
            <label for="location">Localização:</label>
            <input type="text" id="location" name="location" required value="{{ old('location', $machine->location) }}">
            @error('location') <div style="color: red;">{{ $message }}</div> @enderror
        </div>
        <br>

        <div>
            <label for="description">Descrição:</label>
            <textarea id="description" name="description">{{ old('description', $machine->description) }}</textarea>
            @error('description') <div style="color: red;">{{ $message }}</div> @enderror
        </div>
        <br>

        <button type="submit">Atualizar Máquina</button>
    </form>
</body>
</html>
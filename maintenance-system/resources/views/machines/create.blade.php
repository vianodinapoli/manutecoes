<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Máquina</title>
</head>
<body>
    <h1>➕ Adicionar Nova Máquina</h1>

    <a href="{{ route('machines.index') }}">Voltar à Lista</a>
    
    <form method="POST" action="{{ route('machines.store') }}">
        @csrf <div>
            <label for="name">Nome da Máquina:</label>
            <input type="text" id="name" name="name" required>
        </div>
        <br>
        
        <div>
            <label for="serial_number">Número de Série:</label>
            <input type="text" id="serial_number" name="serial_number" required>
        </div>
        <br>

        <div>
            <label for="location">Localização:</label>
            <input type="text" id="location" name="location" required>
        </div>
        <br>

        <div>
            <label for="description">Descrição:</label>
            <textarea id="description" name="description"></textarea>
        </div>
        <br>

        <button type="submit">Guardar Máquina</button>
    </form>
</body>
</html>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Gestão de Máquinas</title>
</head>
<body>
    <h1>🛠️ Lista de Máquinas</h1>

    <p>
        <a href="{{ route('machines.create') }}">Adicionar Nova Máquina</a>
    </p>

    @if ($machines->isEmpty())
        <p>Ainda não há máquinas registadas.</p>
    @else
        <table border="1" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Localização</th>
                    <th>Nº de Série</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($machines as $machine)
                    <tr>
                        <td>{{ $machine->id }}</td>
                        <td>{{ $machine->name }}</td>
                        <td>{{ $machine->location }}</td>
                        <td>{{ $machine->serial_number }}</td>
                        <td>
                            <a href="#">Ver Detalhes</a> | 
                            <a href="#">Editar</a> 
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Detalhes da Máquina: {{ $machine->name }}</title>
</head>
<body>
    <h1>Detalhes da Máquina: {{ $machine->name }}</h1>

    <p>
        <a href="{{ route('machines.index') }}">Voltar à Lista de Máquinas</a>
    </p>

    <p>
        <a href="{{ route('machines.edit', $machine->id) }}">✏️ Editar Máquina</a>
        | 
        <a href="#">➕ Nova Manutenção</a> 
    </p>

    ---

    <h2>Informação Básica</h2>
    <ul>
        <li>**Nome:** {{ $machine->name }}</li>
        <li>**Nº de Série:** {{ $machine->serial_number }}</li>
        <li>**Chassi:** {{ $machine->chassi }}</li>
        <li>**Localização:** {{ $machine->location }}</li>
        <li>**Registo:** {{ $machine->created_at->format('d/m/Y H:i') }}</li>
    </ul>

    <h3>Descrição Detalhada</h3>
    <p>{{ $machine->description ?? 'N/A' }}</p>
    
    ---

    <h2>Histórico de Manutenções ({{ $maintenances->count() }})</h2>

    @if ($maintenances->isEmpty())
        <p>Ainda não há registos de manutenção para esta máquina.</p>
    @else
        <table border="1" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Estado</th>
                    <th>Descrição da Avaria</th>
                    <th>Data de Criação</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($maintenances as $maintenance)
                    <tr>
                        <td>{{ $maintenance->id }}</td>
                        <td>**{{ $maintenance->status }}**</td>
                        <td>{{ \Illuminate\Support\Str::limit($maintenance->failure_description, 50) }}</td>
                        <td>{{ $maintenance->created_at->format('d/m/Y') }}</td>
                        <td>
                            <a href="#">Ver Detalhes</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
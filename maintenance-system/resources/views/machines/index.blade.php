<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Máquinas</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body >
    <div class="container-fluid mt-5" style="width: 90%; margin: 0 auto;">
 
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>🛠️ Lista de Máquinas</h1>
            
            <a href="{{ route('maintenances.index') }}" class="btn btn-info me-2">🛠 Todas as Manutenções</a>
            <a href="{{ route('machines.create') }}" class="btn btn-primary">
                ➕ Adicionar Nova Máquina
            </a>
        </div>
        
        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if ($machines->isEmpty())
            <div class="alert alert-info" role="alert">
                Ainda não há máquinas registadas.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Nº de Série</th>
                            <th>Chassi</th>
                            <th>Localização</th>
                            <th>Status</th>
                            <th>Descrição</th>
                            
                            <th>Status</th> 
                            
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($machines as $machine)
                            <tr>
                                <td>{{ $machine->id }}</td>
                                <td>{{ $machine->name }}</td>
                                <td>{{ $machine->serial_number }}</td>
                                <td>{{ $machine->chassi }}</td>
                                <td>{{ $machine->location }}</td>
                                  <td>{{ $machine->status }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($machine->description, 30) }}</td>
                                
                                <td>
                                    <span class="badge bg-success">{{ $machine->status }}</span>
                                </td>


                                <td>
                                    <a href="{{ route('machines.show', $machine->id) }}" class="btn btn-sm btn-info">Ver</a> 
                                    
                                    <a href="{{ route('machines.edit', $machine->id) }}" class="btn btn-sm btn-warning">Editar</a>
                                    
                                        <!-- Botão Apagar -->
<form action="{{ route('machines.destroy', $machine->id) }}" method="POST" class="d-inline"
      onsubmit="return confirm('Tens certeza que queres apagar esta máquina?');">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger">
        Apagar
    </button>

                                    <a href="{{ route('machines.maintenances.create', $machine->id) }}"  class="btn btn-sm btn-danger" 
                                       title="Criar Ordem de Manutenção">Manutenção</a>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
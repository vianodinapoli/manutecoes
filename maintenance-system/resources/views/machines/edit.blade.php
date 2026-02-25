<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Máquina: {{ $machine->numero_interno }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body>
    <x-app-layout>
    <div class="container mt-5"> 
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>✏️ Editar Máquina: <span class="text-primary">{{ $machine->numero_interno }}</span></h1>
        </div>

        <div class="mb-4 d-flex gap-2">
            <a href="{{ route('machines.index') }}" class="btn btn-secondary">
                ⬅️ Voltar à Lista
            </a>
            <a href="{{ route('machines.show', $machine->id) }}" class="btn btn-info">
                👁️ Ver Detalhes
            </a>
        </div>
        
        <div class="card shadow-sm p-4">
            <form method="POST" action="{{ route('machines.update', $machine->id) }}">
                @csrf 
                @method('PUT') 
                
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Por favor, corrija os erros de validação abaixo:</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                {{-- O partial 'machines.form' é incluído aqui, contendo todos os campos, incluindo o novo dropdown. --}}
                @include('machines.form') 
                
                <button type="submit" class="btn btn-success btn-lg mt-4 w-100">
                    ✅ Atualizar Máquina
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</x-app-layout>
</body>
</html>
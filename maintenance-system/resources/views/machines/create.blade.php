<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Máquina</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body>
    <x-app-layout>
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
            
            @include('machines.form', ['machine' => new \App\Models\Machine()])

            <button type="submit" class="btn btn-success btn-lg mt-3">
                💾 Guardar Máquina
            </button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</x-app-layout>
</body>
</html>
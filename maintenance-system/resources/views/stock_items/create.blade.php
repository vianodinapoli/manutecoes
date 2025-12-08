<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Novo Item de Stock</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5"> 
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>➕ Criar Novo Item de Stock</h1>
            <a href="{{ route('stock-items.index') }}" class="btn btn-secondary">
                ⬅️ Voltar à Lista
            </a>
        </div>

        {{-- Exibição de erros de validação --}}
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
        
        {{-- Formulário de Submissão --}}
        <form action="{{ route('stock-items.store') }}" method="POST">
            @csrf
            
            {{-- Inclui o partial do formulário (com todos os campos fixos e a secção de metadata dinâmica) --}}
            @include('stock_items.form') 
            
            <button type="submit" class="btn btn-success btn-lg mt-4 w-100">
                💾 Guardar Item de Stock
            </button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
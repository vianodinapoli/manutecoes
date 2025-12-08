<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Item de Stock: {{ $stockItem->referencia }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5"> 
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>✏️ Editar Item: <span class="text-primary">{{ $stockItem->referencia }}</span></h1>
        </div>

        {{-- Botões de Navegação --}}
        <div class="mb-4 d-flex gap-2">
            <a href="{{ route('stock-items.index') }}" class="btn btn-secondary">
                ⬅️ Voltar à Lista
            </a>
            <a href="{{ route('stock-items.show', $stockItem->id) }}" class="btn btn-info">
                👁️ Ver Detalhes
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
        
        {{-- Formulário de Submissão para Atualização --}}
        <form method="POST" action="{{ route('stock-items.update', $stockItem->id) }}">
            @csrf 
            @method('PUT') 
            
            {{-- Inclui o partial do formulário (que já carrega os dados de $stockItem) --}}
            @include('stock_items.form') 
            
            <button type="submit" class="btn btn-success btn-lg mt-4 w-100">
                ✅ Atualizar Item de Stock
            </button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
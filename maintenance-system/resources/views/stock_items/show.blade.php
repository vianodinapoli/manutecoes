<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Item de Stock: {{ $stockItem->referencia }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5"> 
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Detalhes do Item: <span class="text-primary">{{ $stockItem->referencia }}</span></h1>
        </div>

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        {{-- Botões de Ação --}}
        <div class="mb-4 d-flex gap-2">
            <a href="{{ route('stock-items.index') }}" class="btn btn-secondary">
                ⬅️ Voltar à Lista
            </a>
            <a href="{{ route('stock-items.edit', $stockItem->id) }}" class="btn btn-warning">
                ✏️ Editar Item
            </a>
            
            {{-- Formulário de Eliminação --}}
            <form action="{{ route('stock-items.destroy', $stockItem->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja eliminar este item de stock? Esta ação é irreversível!');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">🗑️ Eliminar</button>
            </form>
        </div>
        
        {{-- =============================================== --}}
        {{-- LINHA 1: IDENTIFICAÇÃO E STOCK --}}
        {{-- =============================================== --}}
        <div class="row">
            
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">ℹ️ Identificação</h5>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Referência:</strong> 
                            <span class="text-primary fs-5">{{ $stockItem->referencia }}</span>
                        </li>
                        <li class="list-group-item">
                            <strong>Marca/Fabricante:</strong> {{ $stockItem->marca_fabricante ?? 'N/A' }}
                        </li>
                        <li class="list-group-item">
                            <strong>Modelo:</strong> {{ $stockItem->modelo ?? 'N/A' }}
                        </li>
                        <li class="list-group-item">
                            <strong>Categoria:</strong> {{ $stockItem->categoria ?? 'N/A' }}
                        </li>
                        <li class="list-group-item">
                            <strong>Sistema da Máquina:</strong> {{ $stockItem->sistema_maquina ?? 'N/A' }}
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">📦 Controlo de Stock e Localização</h5>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Quantidade em Stock:</strong> 
                            @php
                                $qty_class = $stockItem->quantidade <= 5 ? 'bg-danger' : 'bg-success';
                            @endphp
                            <span class="badge {{ $qty_class }} fs-5">{{ $stockItem->quantidade }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Estado:</strong> 
                            @php
                                $estado_class = match($stockItem->estado) {
                                    'Novo' => 'bg-primary',
                                    'Recondicionado' => 'bg-warning text-dark',
                                    'Usado' => 'bg-secondary',
                                    default => 'bg-light text-dark',
                                };
                            @endphp
                            <span class="badge {{ $estado_class }}">{{ $stockItem->estado }}</span>
                        </li>
                        <li class="list-group-item">
                            <strong>Nº de Armazém:</strong> {{ $stockItem->numero_armazem }}
                        </li>
                        <li class="list-group-item">
                            <strong>Secção:</strong> {{ $stockItem->seccao_armazem ?? 'N/A' }}
                        </li>
                        <li class="list-group-item">
                            <strong>Criado em:</strong> {{ $stockItem->created_at->format('d/m/Y H:i') }}
                        </li>
                    </ul>
                </div>
            </div>
            
        </div> 

        {{-- =============================================== --}}
        {{-- LINHA 2: CAMPOS PERSONALIZADOS (METADATA) --}}
        {{-- =============================================== --}}
        <div class="row">
             <div class="col-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">➕ Campos Personalizados</h5>
                    </div>
                    <div class="card-body">
                        @if($stockItem->metadata && is_array($stockItem->metadata) && count($stockItem->metadata) > 0)
                            <div class="row">
                                @foreach($stockItem->metadata as $key => $value)
                                    <div class="col-md-4 mb-3">
                                        <div class="p-2 border rounded bg-light">
                                            <small class="text-muted d-block">{{ $key }}:</small>
                                            <strong>{{ $value ?? 'N/A' }}</strong>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info mb-0">
                                Nenhum campo personalizado foi adicionado a este item.
                            </div>
                        @endif
                    </div>
                </div>
             </div>
        </div>
        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
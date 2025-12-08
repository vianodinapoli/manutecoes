<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventário de Stock</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5"> 
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>📦 Inventário de Stock</h1>
            <a href="{{ route('stock-items.create') }}" class="btn btn-primary btn-lg">
                + Adicionar Novo Item
            </a>
        </div>

        {{-- Mensagens de sucesso --}}
        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                @if($stockItems->isEmpty())
                    <div class="alert alert-info mb-0">
                        Nenhum item de stock encontrado. Comece por adicionar um novo item!
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Ref.</th>
                                    <th>Localização</th>
                                    <th>Marca / Modelo</th>
                                    <th>Categoria</th>
                                    <th>Qt.</th>
                                    <th>Estado</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stockItems as $item)
                                    <tr>
                                        <td><strong>{{ $item->referencia }}</strong></td>
                                        <td>{{ $item->numero_armazem }} / {{ $item->seccao_armazem }}</td>
                                        <td>{{ $item->marca_fabricante }} / {{ $item->modelo }}</td>
                                        <td>{{ $item->categoria ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge {{ $item->quantidade <= 5 ? 'bg-danger' : 'bg-success' }}">
                                                {{ $item->quantidade }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $badge_class = match($item->estado) {
                                                    'Novo' => 'bg-primary',
                                                    'Recondicionado' => 'bg-warning text-dark',
                                                    'Usado' => 'bg-secondary',
                                                    default => 'bg-light text-dark',
                                                };
                                            @endphp
                                            <span class="badge {{ $badge_class }}">{{ $item->estado }}</span>
                                        </td>
                                        <td class="d-flex gap-1">
                                            <a href="{{ route('stock-items.show', $item->id) }}" class="btn btn-sm btn-info" title="Ver Detalhes">👁️</a>
                                            <a href="{{ route('stock-items.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Editar">✏️</a>
                                            
                                            <form action="{{ route('stock-items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja eliminar este item de stock?');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">🗑️</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Paginação --}}
                    <div class="mt-3">
                        {{ $stockItems->links() }}
                    </div>
                @endif
            </div>
        </div>
        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
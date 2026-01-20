<x-app-layout>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        .container-fluid { max-width: 1400px; }
        .card-article { 
            height: 100%; 
            border-radius: 12px; 
            border: 2px solid #e9ecef; 
            background: white;
            transition: all 0.2s;
        }
        .card-article:hover { border-color: #0d6efd; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        
        .article-header {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 10px 10px 0 0;
            border-bottom: 1px solid #dee2e6;
        }
        .article-title { font-size: 1.1rem; color: #0d6efd; font-weight: 700; margin: 0; }
        
        .item-row { 
            padding: 10px 0;
            border-bottom: 1px dashed #eee;
        }
        .item-row:last-child { border-bottom: none; }
        
        .item-label { font-size: 0.6rem; text-transform: uppercase; color: #adb5bd; font-weight: 800; margin-bottom: 0; }
        .item-value { font-size: 0.85rem; font-weight: 600; margin-bottom: 0; }
        
        .search-container { max-width: 600px; margin: 0 auto 30px auto; }
    </style>

    <div class="container-fluid mt-4">
        <div class="text-center mb-4">
            <h4 class="fw-bold">📦 Inventário por Artigo</h4>
            <p class="text-muted small">Cada card representa um artigo único e as suas variações de stock</p>
        </div>

        <div class="search-container">
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" id="globalSearch" class="form-control border-start-0" placeholder="Pesquisar nome do artigo, referência ou marca...">
                <a href="{{ route('stock-items.create') }}" class="btn btn-primary px-4">Novo</a>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4" id="stockGrid">
            @php
                // Agrupamos tudo pelo NOME DO ARTIGO para criar os cards
                $groupedByArticle = $stockItems->groupBy(function($item) {
                    return $item->nome_artigo ?? $item->marca_fabricante;
                });
            @endphp

            @foreach($groupedByArticle as $nomeArtigo => $itens)
                <div class="col article-card" data-search="{{ strtolower($nomeArtigo . ' ' . $itens->pluck('nome')->implode(' ')) }}">
                    <div class="card card-article shadow-sm">
                        <div class="article-header">
                            <h5 class="article-title text-truncate" title="{{ $nomeArtigo }}">
                                {{ $nomeArtigo }}
                            </h5>
                            <span class="badge bg-secondary small mt-1">{{ $itens->count() }} variações</span>
                        </div>
                        
                        <div class="card-body p-3">
                            @foreach($itens as $item)
                                <div class="item-row">
                                    <div class="row g-1 align-items-center">
                                        <div class="col-6">
                                            <p class="item-label">Referência</p>
                                            <p class="item-value text-dark">{{ $item->referencia }}</p>
                                        </div>
                                        <div class="col-6 text-end">
                                            <p class="item-label">Quantidade</p>
                                            <span class="badge {{ $item->quantidade <= 5 ? 'bg-danger' : 'bg-success' }}">
                                                {{ $item->quantidade }} UN
                                            </span>
                                        </div>
                                        <div class="col-7">
                                            <p class="item-label">Marca / Fabr.</p>
                                            <p class="item-value text-muted small">{{ $item->marca_fabricante }}</p>
                                        </div>
                                        <div class="col-5 text-end d-flex justify-content-end gap-2 mt-2">
                                            <a href="{{ route('stock-items.edit', $item->id) }}" class="text-warning"><i class="bi bi-pencil"></i></a>
                                            <a href="{{ route('stock-items.show', $item->id) }}" class="text-info"><i class="bi bi-eye"></i></a>
                                            <form action="{{ route('stock-items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apagar esta variação?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="border-0 bg-transparent text-danger p-0"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#globalSearch").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $(".article-card").each(function() {
                    var match = $(this).data("search").indexOf(value) > -1;
                    $(this).toggle(match);
                });
            });
        });
    </script>
</x-app-layout>
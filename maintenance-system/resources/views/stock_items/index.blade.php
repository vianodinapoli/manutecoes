<x-app-layout>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root { --primary-blue: #0d6efd; --danger-red: #dc3545; --light-bg: #f8f9fa; }
        .container-fluid { background-color: #f4f7f6; min-height: 100vh; padding: 2rem; }
        
        .stat-card { border: none; border-radius: 12px; transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-5px); }

        .search-wrapper { max-width: 1000px; margin: 0 auto 2rem auto; }
        .input-group-search { border-radius: 50px; overflow: hidden; background: white; border: 1px solid #ddd; }
        .input-group-search input { border: none; padding: 12px 20px; box-shadow: none !important; }

        .card-article { border: 1px solid #e0e0e0; border-radius: 10px; background: white; height: 100%; display: flex; flex-direction: column; }
        .article-header { background: var(--light-bg); padding: 12px; border-bottom: 1px solid #eee; border-radius: 10px 10px 0 0; }
        .article-title { font-size: 0.85rem; font-weight: 800; color: var(--primary-blue); text-transform: uppercase; margin: 0; }
        
        .item-row { padding: 10px; border-bottom: 1px solid #f1f1f1; }
        .item-row:last-child { border-bottom: none; }
        .label-mini { font-size: 0.6rem; color: #999; font-weight: 700; text-transform: uppercase; margin: 0; }
        .value-mini { font-size: 0.8rem; font-weight: 600; color: #333; margin: 0; }
        
        .badge-low { background-color: #fff2f2; color: #dc3545; border: 1px solid #ffc1c1; }
        .badge-ok { background-color: #f2fff5; color: #198754; border: 1px solid #c1ffcf; }

        /* Estilo para os botões de exportação */
        .btn-export-group .btn { border-radius: 8px; font-weight: 600; font-size: 0.85rem; }
    </style>

    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-6">
                <h3 class="fw-bold"><i class="bi bi-box-seam me-2"></i>Mapa de Stock</h3>
                <p class="text-muted">Gestão centralizada de artigos e variações</p>
            </div>
            <div class="col-md-3">
                <div class="card stat-card shadow-sm bg-white p-3">
                    <span class="label-mini text-primary">Total de Referências</span>
                    <h4 class="fw-bold mb-0">{{ $stockItems->count() }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card shadow-sm bg-danger text-white p-3">
                    <span class="label-mini text-white-50">Stock Crítico (≤ 5)</span>
                    <h4 class="fw-bold mb-0">{{ $stockItems->where('quantidade', '<=', 5)->count() }}</h4>
                </div>
            </div>
        </div>

        <div class="search-wrapper">
            <div class="row g-3 align-items-center">
                <div class="col-lg-6">
                    <div class="input-group input-group-search shadow-sm">
                        <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="globalSearch" class="form-control" placeholder="Pesquisar por nome, marca, referência...">
                    </div>
                </div>
                <div class="col-lg-6 d-flex justify-content-lg-end gap-2 btn-export-group">
                    <a href="{{ route('stock-items.export', ['type' => 'excel']) }}" class="btn btn-outline-success shadow-sm">
                        <i class="bi bi-file-earmark-spreadsheet me-1"></i> Excel
                    </a>
                    <a href="{{ route('stock-items.export', ['type' => 'pdf']) }}" class="btn btn-outline-danger shadow-sm">
                        <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                    </a>
                    <a href="{{ route('stock-items.create') }}" class="btn btn-primary shadow-sm px-4">
                        <i class="bi bi-plus-lg me-1"></i> Novo Artigo
                    </a>
                </div>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3" id="stockGrid">
            @php
                $grouped = $stockItems->groupBy(fn($item) => $item->nome ?: 'Sem Nome');
            @endphp

            @foreach($grouped as $nomeArtigo => $itens)
                @php
                    $totalQty = $itens->sum('quantidade');
                    $searchData = strtolower($nomeArtigo . ' ' . $itens->pluck('referencia')->implode(' ') . ' ' . $itens->pluck('marca_fabricante')->implode(' '));
                @endphp
                
                <div class="col article-card-wrapper" data-search="{{ $searchData }}">
                    <div class="card-article shadow-sm">
                        <div class="article-header d-flex justify-content-between align-items-center">
                            <div class="text-truncate" style="flex: 1;">
                                <p class="label-mini">Artigo / Peça</p>
                                <h6 class="article-title text-truncate" title="{{ $nomeArtigo }}">{{ $nomeArtigo }}</h6>
                            </div>
                            <span class="badge rounded-pill {{ $totalQty <= 5 ? 'bg-danger' : 'bg-primary' }} ms-2">
                                {{ $totalQty }}
                            </span>
                        </div>

                        <div class="card-body p-0">
                            @foreach($itens as $item)
                                <div class="item-row">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <div class="text-truncate" style="flex: 1;">
                                            <p class="label-mini">Ref / Marca</p>
                                            <p class="value-mini text-truncate">{{ $item->referencia }}</p>
                                            <small class="text-muted d-block text-truncate" style="font-size: 0.65rem;">{{ $item->marca_fabricante }}</small>
                                        </div>
                                        <div class="text-end ms-2">
                                            <p class="label-mini">Qtd</p>
                                            <span class="badge {{ $item->quantidade <= 5 ? 'badge-low' : 'badge-ok' }}">
                                                {{ $item->quantidade }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end gap-3 mt-2">
                                        <a href="{{ route('stock-items.edit', $item->id) }}" class="text-warning" title="Editar"><i class="bi bi-pencil-square"></i></a>
                                        <a href="{{ route('stock-items.show', $item->id) }}" class="text-info" title="Ver"><i class="bi bi-eye"></i></a>
                                        <form action="{{ route('stock-items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apagar variação?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="border-0 bg-transparent text-danger p-0"><i class="bi bi-trash"></i></button>
                                        </form>
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
                $(".article-card-wrapper").each(function() {
                    var content = $(this).data("search");
                    $(this).toggle(content.indexOf(value) > -1);
                });
            });
        });
    </script>
</x-app-layout>
<x-app-layout>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    .page-wrap{max-width:760px;margin:0 auto;padding:32px 24px 48px}
    .section{background:#fff;border:1px solid #e2e8f0;border-radius:12px;margin-bottom:16px;overflow:hidden}
    .section-head{display:flex;align-items:center;gap:10px;padding:12px 20px;background:#f8fafc;border-bottom:1px solid #e2e8f0}
    .section-head .s-num{font-size:.6rem;font-weight:800;letter-spacing:1.5px;color:#94a3b8;text-transform:uppercase;background:#e2e8f0;border-radius:4px;padding:2px 7px}
    .section-head .s-title{font-size:.8rem;font-weight:700;color:#334155;letter-spacing:.3px}
    .section-body{padding:20px 24px}
    .top-btn{display:inline-flex;align-items:center;gap:6px;padding:7px 16px;border-radius:8px;font-size:.78rem;font-weight:600;text-decoration:none;border:1px solid #e2e8f0;background:#fff;color:#475569;transition:all .15s;cursor:pointer}
    .top-btn:hover{background:#f8fafc;border-color:#cbd5e1;color:#334155}
    .top-btn.primary{background:#1e293b;border-color:#1e293b;color:#fff}
    .top-btn.primary:hover{background:#334155;color:#fff}
    .error-box{background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:12px 16px;font-size:.78rem;color:#dc2626;margin-bottom:16px}
</style>

<div class="page-wrap">

    {{-- CABEÇALHO --}}
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <div style="font-size:.6rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">
                Gestão de Stock
            </div>
            <h4 class="fw-bold mb-0" style="color:#1e293b;font-size:1.25rem;">
                Editar Item
                <span style="color:#cbd5e1;font-weight:300;margin:0 6px;">·</span>
                <span style="color:#475569;font-weight:500;font-size:1rem;">{{ $stockItem->referencia }}</span>
            </h4>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('stock-items.index') }}" class="top-btn">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
            <a href="{{ route('stock-items.show', $stockItem->id) }}" class="top-btn">
                <i class="bi bi-eye"></i> Ver Detalhes
            </a>
        </div>
    </div>

    {{-- ERROS --}}
    @if($errors->any())
    <div class="error-box">
        <div class="d-flex align-items-center gap-2 mb-1">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <strong>Por favor corrija os erros de validação.</strong>
        </div>
        <ul class="mb-0 ps-3 mt-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- FORMULÁRIO --}}
    <form method="POST" action="{{ route('stock-items.update', $stockItem->id) }}">
        @csrf
        @method('PUT')

        <div class="section">
            <div class="section-head">
                <span class="s-num">01</span>
                <span class="s-title">Dados do Item</span>
            </div>
            <div class="section-body">
                @include('stock_items.form')
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-2"
             style="padding-top:16px;border-top:1px solid #f1f5f9;">
            <a href="{{ route('stock-items.index') }}" class="top-btn">
                <i class="bi bi-x"></i> Cancelar
            </a>
            <button type="submit" class="top-btn primary px-5">
                <i class="bi bi-check-lg"></i> Actualizar Item
            </button>
        </div>

    </form>
</div>
</x-app-layout>
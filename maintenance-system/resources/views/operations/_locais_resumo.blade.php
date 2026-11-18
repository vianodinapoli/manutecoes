@php
    $locs = $operation->locations
        ->sortByDesc(fn ($l) => (int) $l->pivot->principal)
        ->values();
@endphp

<div class="locais-resumo">
    <div class="lr-title">Locais previstos para descarga</div>

    @if($locs->isEmpty())
        <div class="lr-empty">Sem locais definidos.</div>
    @else
        <div class="lr-list">
            @foreach($locs as $loc)
                <span class="lr-chip {{ $loc->pivot->principal ? 'lr-main' : '' }}">
                    <i class="bi {{ $loc->tipo === 'yard' ? 'bi-grid-3x3-gap' : 'bi-building' }}"></i>
                    {{ $loc->nome }}
                    @if($loc->pivot->principal)
                        <b>principal</b>
                    @endif
                </span>
            @endforeach
        </div>
    @endif
</div>

<style>
    .locais-resumo{margin-top:10px}
    .lr-title{font-size:.6rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#94a3b8;margin-bottom:6px}
    .lr-list{display:flex;flex-wrap:wrap;gap:6px}
    .lr-chip{display:inline-flex;align-items:center;gap:6px;border-radius:20px;padding:3px 11px;font-size:.7rem;
             font-weight:600;background:#f0f9f1;color:#2a6130;border:1px solid #bae0be}
    .lr-chip b{font-size:.56rem;text-transform:uppercase;letter-spacing:.5px;background:#3f9347;color:#fff;
               border-radius:10px;padding:1px 7px}
    .lr-main{border-color:#3f9347}
    .lr-empty{font-size:.75rem;color:#94a3b8}
</style>

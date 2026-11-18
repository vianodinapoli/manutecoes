@php
    $locations = \App\Models\Location::where('ativo', true)->orderBy('tipo')->orderBy('nome')->get();
@endphp

<div class="col-12">
    <label class="form-label op-lbl">Local de descarga (armazém / yard)</label>
    <div class="row g-2">
        @foreach($locations as $loc)
            <div class="col-md-4">
                <div class="loc-opt">
                    <label class="loc-name">
                        <input type="checkbox" name="locations[]" value="{{ $loc->id }}" class="loc-check">
                        <span>
                            <i class="bi {{ $loc->tipo === 'yard' ? 'bi-grid-3x3-gap' : 'bi-building' }} me-1"></i>{{ $loc->nome }}
                        </span>
                    </label>
                    <label class="loc-main-wrap" title="Local principal">
                        <input type="radio" name="location_principal" value="{{ $loc->id }}" class="loc-main" disabled>
                        <small>principal</small>
                    </label>
                </div>
            </div>
        @endforeach
        @if($locations->isEmpty())
            <div class="col-12"><div class="text-muted" style="font-size:.75rem;">Ainda não há locais criados.</div></div>
        @endif
    </div>
    <div style="font-size:.66rem;color:#94a3b8;margin-top:4px;">
        Marque os locais previstos e escolha o <b>principal</b> (vem pré-selecionado ao registar camiões).
    </div>
</div>

<style>
    .loc-opt{display:flex;align-items:center;justify-content:space-between;gap:8px;border:1px solid #e2e8f0;
             border-radius:8px;padding:8px 10px;font-size:.78rem;background:#fff}
    .loc-opt:has(.loc-check:checked){border-color:#3f9347;background:#f0f9f1}
    .loc-name{display:flex;align-items:center;gap:8px;margin:0;cursor:pointer;flex:1}
    .loc-main-wrap{display:flex;align-items:center;gap:4px;margin:0;color:#94a3b8;font-size:.62rem;cursor:pointer}
    .loc-main:disabled{opacity:.3}
    .loc-main:disabled + small{opacity:.4}
</style>

<script>
    function marcarPrincipalPorDefeito() {
        if (document.querySelector('.loc-main:checked')) return;
        const first = document.querySelector('.loc-check:checked');
        if (first) first.closest('.loc-opt').querySelector('.loc-main').checked = true;
    }

    function syncLoc(chk) {
        const radio = chk.closest('.loc-opt').querySelector('.loc-main');
        radio.disabled = !chk.checked;
        if (!chk.checked) radio.checked = false;
        marcarPrincipalPorDefeito();
    }

    function resetLocais() {
        document.querySelectorAll('.loc-check').forEach(c => c.checked = false);
        document.querySelectorAll('.loc-main').forEach(r => { r.checked = false; r.disabled = true; });
    }

    // op.locations = [{id, nome, principal}]
    function preencherLocais(op) {
        resetLocais();
        (op.locations || []).forEach(l => {
            const c = document.querySelector('.loc-check[value="' + l.id + '"]');
            if (!c) return;
            c.checked = true;
            const r = c.closest('.loc-opt').querySelector('.loc-main');
            r.disabled = false;
            if (l.principal) r.checked = true;
        });
        marcarPrincipalPorDefeito();
    }

    document.addEventListener('change', e => {
        if (e.target.classList && e.target.classList.contains('loc-check')) syncLoc(e.target);
    });
</script>

{{--
    Progresso da pesagem na balança (2 passos: peso cheio e peso vazio, em qualquer ordem).
    Colocar no TOPO do <form> de confirmação (o que tem os inputs name="peso_bruto" e name="tara"):

        @include('discharges._progresso-pesagem', ['discharge' => $discharge])

    Actualiza em tempo real à medida que se escreve cada peso.
--}}
<div class="pw-prog mb-3" data-bruto="{{ $discharge->peso_bruto !== null ? 1 : 0 }}"
     data-tara="{{ $discharge->tara !== null ? 1 : 0 }}"
     style="border:1px solid #e2e8f0;border-radius:10px;padding:10px 14px;background:#f8fafc;">
    <div class="d-flex justify-content-between align-items-baseline flex-wrap gap-1">
        <strong class="pw-titulo" style="font-size:.95rem;"></strong>
        <span class="pw-texto small text-muted"></span>
    </div>

    <div style="height:8px;background:#e2e8f0;border-radius:6px;overflow:hidden;margin:8px 0;">
        <div class="pw-fill" style="height:100%;width:0;border-radius:6px;transition:width .25s,background .25s;"></div>
    </div>

    <div class="d-flex gap-2 flex-wrap">
        <span class="pw-chip" data-chip="bruto"
              style="font-size:.75rem;font-weight:600;border-radius:20px;padding:2px 12px;border:1px solid #cbd5e1;"></span>
        <span class="pw-chip" data-chip="tara"
              style="font-size:.75rem;font-weight:600;border-radius:20px;padding:2px 12px;border:1px solid #cbd5e1;"></span>
    </div>
</div>

<script>
(function () {
    document.querySelectorAll('.pw-prog:not([data-init])').forEach(function (box) {
        box.dataset.init = '1';

        // fora de um <form> (ex.: vista só de leitura) não há inputs: usa só o que já está gravado
        var form  = box.closest('form');
        var inB   = form ? form.querySelector('[name="peso_bruto"]') : null;
        var inT   = form ? form.querySelector('[name="tara"]') : null;
        var jaB   = box.dataset.bruto === '1';
        var jaT   = box.dataset.tara === '1';

        var titulo = box.querySelector('.pw-titulo');
        var texto  = box.querySelector('.pw-texto');
        var fill   = box.querySelector('.pw-fill');
        var chipB  = box.querySelector('[data-chip="bruto"]');
        var chipT  = box.querySelector('[data-chip="tara"]');

        function tem(inp, ja) { return ja || (inp && inp.value.trim() !== ''); }

        function chip(el, nome, ok) {
            el.textContent   = (ok ? '✓ ' : '○ ') + nome;
            el.style.background  = ok ? '#e6f4ec' : '#fff';
            el.style.color       = ok ? '#198754' : '#64748b';
            el.style.borderColor = ok ? '#b7dfc7' : '#cbd5e1';
        }

        function render() {
            var b = tem(inB, jaB), t = tem(inT, jaT);
            var n = (b ? 1 : 0) + (t ? 1 : 0);

            titulo.textContent = 'Passo ' + n + ' de 2';
            fill.style.width   = (n * 50) + '%';
            fill.style.background = n === 2 ? '#198754' : (n === 1 ? '#fd7e14' : '#cbd5e1');

            if (n === 0)      texto.textContent = 'Registe pelo menos um peso (cheio ou vazio)';
            else if (n === 2) texto.textContent = 'Pesagem completa: ao guardar, a descarga fica confirmada';
            else if (b)       texto.textContent = 'Peso cheio registado. Falta pesar o camião vazio (ao voltar)';
            else              texto.textContent = 'Peso vazio registado. Falta pesar o camião cheio';

            chip(chipB, 'Cheio (bruto)', b);
            chip(chipT, 'Vazio (tara)', t);
        }

        [inB, inT].forEach(function (inp) { if (inp) inp.addEventListener('input', render); });
        render();
    });
})();
</script>

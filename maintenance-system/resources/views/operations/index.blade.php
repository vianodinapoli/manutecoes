<x-app-layout>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    /* ── Cabeçalho ───────────────────────────────────────── */
    .page-title{font-size:clamp(1rem,1.8vw,1.1rem);font-weight:700;color:#1e293b;margin:0}
    .page-sub{font-size:.74rem;color:#64748b;margin:2px 0 0}
    .btn-op{border-radius:8px;font-weight:600;font-size:.78rem;padding:.4rem .8rem;min-height:36px;
            display:inline-flex;align-items:center;justify-content:center;gap:.35rem}

    /* ── Cartões ─────────────────────────────────────────── */
    .op-card{position:relative;background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:14px 16px;
             height:100%;display:flex;flex-direction:column;
             transition:box-shadow .15s,transform .15s}
    .op-card:hover{box-shadow:0 8px 22px rgba(15,23,42,.09);transform:translateY(-1px)}
    .op-card.planned{border-style:dashed;background:#fbfcfe}
    .op-card.alert-warn{border:2px solid #f59e0b;background:#fffbeb}
    .op-card.alert-danger{border:2px solid #dc3545;background:#fef2f2}
    .op-navio{font-size:.92rem;font-weight:800;color:#1e293b;line-height:1.25;text-decoration:none;word-break:break-word}
    .op-navio:hover{color:#0d6efd}
    .op-empresa{font-size:.74rem;color:#64748b}
    .op-ref{font-size:.7rem;color:#64748b;display:flex;flex-wrap:wrap;gap:3px 12px}
    .op-ref b{color:#334155;font-weight:600}
    .op-ref i{color:#94a3b8;margin-right:3px}

    .op-bar{height:8px;border-radius:6px;background:#eef1f5;overflow:hidden;margin:10px 0 5px}
    .op-bar>div{height:100%;border-radius:6px;background:linear-gradient(90deg,#1a56db,#0dcaf0);transition:width .4s}
    .op-bar.done>div{background:#198754}
    .op-nums{display:flex;justify-content:space-between;gap:8px;flex-wrap:wrap;font-size:.72rem;color:#64748b}

    .op-chip{display:inline-flex;align-items:center;gap:4px;border-radius:20px;padding:3px 9px;
             font-size:.68rem;font-weight:600;white-space:nowrap}
    .chip-transit{background:#e0f7fc;color:#0a8aa5}
    .chip-ok{background:#e6f4ec;color:#157347}
    .chip-plan{background:#fff3cd;color:#7a5c00}
    .chip-warn{background:#fef3c7;color:#b45309}
    .chip-danger{background:#fee2e2;color:#b91c1c}
    .chip-danger.pulse{animation:pulse-red 1.4s infinite}
    @keyframes pulse-red{0%,100%{opacity:1}50%{opacity:.55}}

    .prod-chip{display:inline-block;background:#eef2ff;color:#3b4cca;border-radius:6px;
               font-size:.68rem;font-weight:600;padding:2px 7px;margin:3px 4px 0 0}

    .op-foot{display:flex;justify-content:space-between;align-items:center;gap:8px;
             margin-top:auto;padding-top:10px}
    .op-open{font-size:.76rem;font-weight:600;color:#0d6efd;display:inline-flex;align-items:center;gap:5px}
    .op-del{position:relative;z-index:2;margin:0}   /* acima do link que cobre o cartão */
    .op-del .btn{border-radius:8px;font-size:.74rem;font-weight:600;min-height:32px;padding:.25rem .6rem}

    /* ── Modal de confirmação ────────────────────────────── */
    .confirm-icon{width:54px;height:54px;border-radius:50%;background:#fee2e2;color:#dc3545;
                  display:flex;align-items:center;justify-content:center;font-size:1.5rem;margin:0 auto 12px}
    #confirmarTitulo{font-size:1rem;font-weight:700;color:#1e293b}
    #confirmarTexto{color:#64748b;font-size:.82rem;margin-bottom:0}

    /* ── Modais visíveis em qualquer ecrã, sem zoom ──────── */
    .modal .modal-dialog{margin:.5rem auto;max-width:min(var(--bs-modal-width,500px),calc(100vw - 1rem))}
    .modal .modal-content{max-height:calc(100vh - 1rem);max-height:calc(100dvh - 1rem);
                          display:flex;flex-direction:column;overflow:hidden}
    .modal .modal-content>form{display:flex;flex-direction:column;min-height:0;max-height:inherit;overflow:hidden}
    .modal .modal-header,.modal .modal-footer{flex-shrink:0}
    .modal .modal-body{overflow-y:auto;min-height:0;-webkit-overflow-scrolling:touch}
    @media (max-width:767.98px){
        /* 16px evita o zoom automático do iPhone/iPad ao tocar num campo */
        .modal input,.modal select,.modal textarea{font-size:16px !important}
        .modal .modal-footer{flex-wrap:wrap}
        .modal .modal-footer .btn{flex:1 1 140px}
    }
    @media (pointer:coarse){
        .modal .btn,.modal .form-control,.modal .form-select{min-height:42px}
    }
</style>

<div class="container-fluid py-3 px-3 px-md-4">

    {{-- Cabeçalho --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h1 class="page-title"><i class="bi bi-water me-2 text-primary"></i>Operações de Descarga</h1>
            <div class="page-sub">Uma operação por navio. Defina o plano antes de iniciar.</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('operations.archive') }}" class="btn btn-outline-secondary btn-op">
                <i class="bi bi-archive"></i>Arquivo
            </a>
            <button type="button" class="btn btn-primary btn-op shadow-sm" onclick="abrirOperacaoModal()">
                <i class="bi bi-plus-lg"></i>Nova Operação
            </button>
        </div>
    </div>

    {{-- Mensagens --}}
    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 py-2 js-auto-fechar" role="alert" style="font-size:.8rem;border-radius:10px;">
            <i class="bi bi-check-circle-fill"></i><div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center gap-2 py-2" role="alert" style="font-size:.8rem;border-radius:10px;">
            <i class="bi bi-exclamation-triangle-fill"></i><div>{{ session('error') }}</div>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger py-2" role="alert" style="font-size:.8rem;border-radius:10px;">
            <div class="d-flex align-items-center gap-2 fw-semibold mb-1">
                <i class="bi bi-exclamation-triangle-fill"></i>Não foi possível guardar. Verifique:
            </div>
            <ul class="mb-0 ps-4">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- Tempo real: verifica a cada 2s se houve alterações e só então recarrega a lista (rede de segurança: 60s) --}}
    <x-auto-refresh :segundos="60" :verificar="route('operations.sinal')" :verificar-segundos="2" />

    <div id="conteudo-atualizavel">
        <div class="row g-3">
            @forelse($operations as $op)
            @php
                $recebido = round(((float) $op->discharges_sum_peso_liquido) / 1000, 3);
                $esperado = (float) $op->quantidade_esperada;
                $pct = $esperado > 0 ? min(100, round($recebido / $esperado * 100, 1)) : 0;

                // Tempo do camião pendente mais antigo (porto -> balança)
                $desde = ($op->pendentes_count > 0 && $op->pendente_desde)
                    ? \Carbon\Carbon::parse($op->pendente_desde)
                    : null;
                $min = $desde ? (int) abs($desde->diffInMinutes(now())) : 0;
                $nivel = $desde ? ($min >= 80 ? 'danger' : ($min >= 60 ? 'warn' : '')) : '';

                $chegada = $op->data_chegada ? \Carbon\Carbon::parse($op->data_chegada)->format('d/m/Y') : null;
                $podeApagar = auth()->user()->hasRole('super-admin');
                $nCamioes = (int) $op->discharges_count;
            @endphp
            <div class="col-12 col-md-6 col-xl-4">
                <div class="op-card {{ $op->isPlanned() ? 'planned' : '' }} {{ $nivel ? 'alert-'.$nivel : '' }}"
                     @if($desde) data-desde="{{ $desde->timestamp * 1000 }}" @endif>

                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div class="min-w-0">
                            {{-- O link cobre o cartão inteiro (stretched-link) --}}
                            <a href="{{ route('operations.show', $op) }}" class="op-navio stretched-link">{{ $op->navio }}</a>
                            <div class="op-empresa">{{ $op->empresa }}</div>
                        </div>

                        @if($op->isPlanned())
                            <span class="op-chip chip-plan"><i class="bi bi-clipboard-check"></i>Planeada</span>
                        @elseif($op->pendentes_count > 0)
                            <span class="op-chip {{ $nivel ? 'chip-'.$nivel : 'chip-transit' }} {{ $nivel === 'danger' ? 'pulse' : '' }}" data-chip>
                                <i class="bi {{ $nivel ? 'bi-exclamation-triangle-fill' : 'bi-truck' }}"></i>
                                {{ $op->pendentes_count }} por confirmar
                                @if($desde)
                                    <i class="bi bi-clock ms-1"></i><span data-tempo>{{ intdiv($min, 60) }}h{{ str_pad($min % 60, 2, '0', STR_PAD_LEFT) }}</span>
                                @endif
                            </span>
                        @elseif($op->discharges_count > 0)
                            <span class="op-chip chip-ok"><i class="bi bi-check-circle-fill"></i>Tudo confirmado</span>
                        @else
                            <span class="op-chip chip-transit"><i class="bi bi-play-circle"></i>Em curso</span>
                        @endif
                    </div>

                    <div class="op-ref mt-2">
                        <span><i class="bi bi-file-earmark-text"></i>BL: <b>{{ $op->bl ?: '-' }}</b></span>
                        <span><i class="bi bi-receipt"></i>Factura: <b>{{ $op->factura ?: '-' }}</b></span>
                        @if($chegada)<span><i class="bi bi-calendar-event"></i><b>{{ $chegada }}</b></span>@endif
                    </div>

                    @if($op->items->isNotEmpty())
                    <div class="mt-2">
                        @foreach($op->items as $it)
                            <span class="prod-chip">
                                <i class="bi bi-box-seam me-1"></i>{{ $it->nome }}:
                                {{ rtrim(rtrim(number_format((float) $it->quantidade, 3, ',', '.'), '0'), ',') }} {{ $it->unidade_label }}
                                @if($it->unidade === 'saco' && $it->peso_unitario_kg)
                                    de {{ rtrim(rtrim(number_format((float) $it->peso_unitario_kg, 3, ',', '.'), '0'), ',') }} kg
                                @endif
                            </span>
                        @endforeach
                    </div>
                    @endif

                    <div class="op-bar {{ $pct >= 100 ? 'done' : '' }}" role="progressbar"
                         aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"
                         aria-label="Progresso da descarga de {{ $op->navio }}">
                        <div style="width:{{ $pct }}%"></div>
                    </div>
                    <div class="op-nums">
                        <span><b>{{ number_format($recebido, 1, ',', '.') }} t</b> de {{ number_format($esperado, 1, ',', '.') }} t</span>
                        <span>{{ number_format($pct, 1, ',', '.') }}% &nbsp;|&nbsp; {{ $nCamioes }} {{ $nCamioes === 1 ? 'camião' : 'camiões' }}</span>
                    </div>

                    <div class="op-foot">
                        <span class="op-open"><i class="bi bi-arrow-right-circle"></i>Abrir operação</span>

                        @if($podeApagar)
                        <form method="POST" action="{{ route('operations.destroy', $op) }}" class="op-del"
                              data-confirm
                              data-confirm-titulo="{{ $nCamioes > 0 ? 'Apagar operação com camiões?' : 'Apagar esta operação?' }}"
                              data-confirm-texto="{{ $nCamioes > 0
                                  ? 'ATENÇÃO: a operação do navio '.$op->navio.' tem '.$nCamioes.' '.($nCamioes === 1 ? 'camião registado' : 'camiões registados').'. Todos os camiões, pesagens e a carta nova serão apagados. Esta acção não pode ser desfeita.'
                                  : 'A operação do navio '.$op->navio.' será apagada. Esta acção não pode ser desfeita.' }}"
                              data-confirm-ok="Sim, apagar">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm" aria-label="Apagar a operação {{ $op->navio }}">
                                <i class="bi bi-trash3"></i><span class="ms-1">Apagar</span>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="text-center text-muted py-5">
                    <i class="bi bi-water" style="font-size:2.2rem;"></i>
                    <div class="mt-2 fw-semibold" style="font-size:.9rem;">Ainda não há operações.</div>
                    <div class="mb-3" style="font-size:.78rem;">Crie a primeira para começar a registar camiões.</div>
                    <button type="button" class="btn btn-primary btn-op" onclick="abrirOperacaoModal()">
                        <i class="bi bi-plus-lg"></i>Nova Operação
                    </button>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Modal de confirmação (apagar e acções semelhantes) --}}
<div class="modal fade" id="confirmarModal" tabindex="-1" aria-labelledby="confirmarTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="--bs-modal-width:400px;">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <div class="confirm-icon"><i class="bi bi-trash3"></i></div>
                <h2 class="h5" id="confirmarTitulo">Tem a certeza?</h2>
                <p id="confirmarTexto"></p>
                <div class="d-flex flex-wrap gap-2 justify-content-center mt-4">
                    <button type="button" class="btn btn-light border btn-op flex-fill" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-danger btn-op flex-fill" id="confirmarOk">
                        <i class="bi bi-trash3"></i><span>Sim, apagar</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@include('operations._modal')

<script>
    // ── Alertas de tempo (laranja a 1h00, vermelho a 1h20) ───────────
const LIMITE_AMARELO = 60;
const LIMITE_VERMELHO = 80;

    function atualizarAlertas() {
        document.querySelectorAll('.op-card[data-desde]').forEach(card => {
            const min = Math.floor((Date.now() - Number(card.dataset.desde)) / 60000);
            const chip = card.querySelector('[data-chip]');
            const tempo = card.querySelector('[data-tempo]');

            if (tempo) tempo.textContent = Math.floor(min / 60) + 'h' + String(min % 60).padStart(2, '0');

            card.classList.remove('alert-warn', 'alert-danger');
            if (chip) chip.classList.remove('chip-transit', 'chip-warn', 'chip-danger', 'pulse');

            if (min >= LIMITE_VERMELHO) {
                card.classList.add('alert-danger');
                if (chip) chip.classList.add('chip-danger', 'pulse');
            } else if (min >= LIMITE_AMARELO) {
                card.classList.add('alert-warn');
                if (chip) chip.classList.add('chip-warn');
            } else if (chip) {
                chip.classList.add('chip-transit');
            }
        });
    }
    atualizarAlertas();
    setInterval(atualizarAlertas, 30000);

    // Depois de cada actualização automática, recalcula tempos e cores
    document.addEventListener('auto-refresh:feito', atualizarAlertas);

    // ── Confirmação amigável (formulários com data-confirm) ──────────
    // Delegado ao documento: continua a funcionar depois de cada actualização da lista.
    (function () {
        const modalEl = document.getElementById('confirmarModal');
        const okBtn = document.getElementById('confirmarOk');
        let pendente = null; // { acao, token, metodo }

        function submeter(p) {
            const f = document.createElement('form');
            f.method = 'POST';
            f.action = p.acao;
            f.style.display = 'none';
            [['_token', p.token], ['_method', p.metodo]].forEach(([n, v]) => {
                if (!v) return;
                const i = document.createElement('input');
                i.type = 'hidden'; i.name = n; i.value = v;
                f.appendChild(i);
            });
            document.body.appendChild(f);
            f.submit();
        }

        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-confirm')) return;
            e.preventDefault();

            pendente = {
                acao: form.action,
                token: (form.querySelector('input[name="_token"]') || {}).value,
                metodo: (form.querySelector('input[name="_method"]') || {}).value,
            };
            const titulo = form.dataset.confirmTitulo || 'Tem a certeza?';
            const texto = form.dataset.confirmTexto || 'Esta acção não pode ser desfeita.';
            const ok = form.dataset.confirmOk || 'Confirmar';

            // Sem Bootstrap JS: usa a confirmação simples do navegador
            if (!(window.bootstrap && window.bootstrap.Modal)) {
                if (window.confirm(titulo + '\n\n' + texto)) submeter(pendente);
                pendente = null;
                return;
            }

            document.getElementById('confirmarTitulo').textContent = titulo;
            document.getElementById('confirmarTexto').textContent = texto;
            okBtn.querySelector('span').textContent = ok;
            okBtn.disabled = false;
            window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
        });

        okBtn.addEventListener('click', function () {
            if (!pendente) return;
            okBtn.disabled = true;
            okBtn.querySelector('span').textContent = 'A apagar...';
            submeter(pendente);
        });

        modalEl.addEventListener('hidden.bs.modal', function () { pendente = null; });
    })();

    // ── Todos os modais ficam centrados e com scroll interno ─────────
    document.addEventListener('show.bs.modal', function (e) {
        const dlg = e.target.querySelector('.modal-dialog');
        if (dlg) dlg.classList.add('modal-dialog-centered');
    });

    // ── Mensagem de sucesso desaparece sozinha ───────────────────────
    document.querySelectorAll('.js-auto-fechar').forEach(el => {
        setTimeout(() => {
            el.style.transition = 'opacity .4s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 450);
        }, 6000);
    });
</script>

</x-app-layout>
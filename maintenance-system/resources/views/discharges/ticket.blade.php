<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
        $op         = $discharge->operation;
        $confirmado = $discharge->status === 'confirmed';
        $fase       = $discharge->fase_pesagem; // por_pesar | aguarda_tara | aguarda_bruto | completa
        $numero     = 'T-' . str_pad($discharge->id, 5, '0', STR_PAD_LEFT);
        $n   = fn ($v, $d = 0) => number_format((float) $v, $d, ',', '.');
        $q   = fn ($v) => rtrim(rtrim(number_format((float) $v, 3, ',', '.'), '0'), ',');
        $sinal = fn ($v, $d = 0) => ($v > 0 ? '+' : '') . number_format((float) $v, $d, ',', '.');
        $kgUn = fn ($it) => $it->unidade === 'tonelada' ? 1000 : (float) $it->peso_unitario_kg;

        $estimado = (float) $discharge->peso_total_estimado;
        $liquido  = $discharge->peso_liquido !== null ? (float) $discharge->peso_liquido : null;
        $difPeso  = $liquido !== null ? $liquido - $estimado : null;
        $diverge  = $discharge->hasDivergence();
        $assin    = $discharge->signature;

        // quantidades já confirmadas na balança (acontece na primeira pesagem)
        $qtdConf = $discharge->items->isNotEmpty()
            && $discharge->items->every(fn ($i) => $i->quantidade_confirmada !== null);

        $avisoProvisorio = match ($fase) {
            'aguarda_tara'  => 'Falta pesar o camião vazio: ticket provisório',
            'aguarda_bruto' => 'Falta pesar o camião cheio: ticket provisório',
            default         => 'Ainda não confirmado na balança: ticket provisório',
        };
    @endphp
    <title>Ticket {{ $numero }} — {{ $discharge->numero_guia }}</title>

    <style>
        /* A4 em pé, dividida em 3 tiras de 99 mm (cada ticket = 210 x 99 mm, "horizontal") */
        @page { size: A4 portrait; margin: 0; }
        * { box-sizing: border-box; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        body { font-family: -apple-system, "Segoe UI", Helvetica, Arial, sans-serif; color: #111827; margin: 0;
               background: #eef1f5; font-size: 9px; line-height: 1.3; }

        .toolbar { position: sticky; top: 0; z-index: 10; background: #0e1015; color: #fff; padding: 10px 20px;
                   display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap; }
        .toolbar a, .toolbar button, .toolbar select { font: inherit; font-size: 12px; color: #fff; text-decoration: none; cursor: pointer;
                   background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.2); border-radius: 6px; padding: 6px 14px; }
        .toolbar select option { color: #111827; }
        .toolbar button.primary { background: #198754; border-color: #198754; font-weight: 600; }
        .toolbar label { font-size: 12px; margin-right: 6px; }

        /* Folha A4 (pré-visualização no ecrã e impressão) */
        .folha { --pos: 1; position: relative; width: 210mm; height: 297mm; max-width: 100%;
                 margin: 16px auto; background: #fff; box-shadow: 0 2px 14px rgba(0,0,0,.1); overflow: hidden; }
        .guias { position: absolute; inset: 0; pointer-events: none;
                 background:
                   linear-gradient(to bottom, transparent calc(99mm - 1px), #cbd5e1 calc(99mm - 1px), #cbd5e1 99mm, transparent 99mm),
                   linear-gradient(to bottom, transparent calc(198mm - 1px), #cbd5e1 calc(198mm - 1px), #cbd5e1 198mm, transparent 198mm); }

        .ticket { position: absolute; left: 0; width: 210mm; height: 99mm;
                  top: calc((var(--pos) - 1) * 99mm);
                  padding: 4mm 7mm 3mm; display: flex; flex-direction: column; overflow: hidden; }

        .head { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #c60a1a; padding-bottom: 4px; }
        .org { font-size: 7.5px; letter-spacing: 1.2px; text-transform: uppercase; color: #64748b; }
        .titulo { font-size: 15px; font-weight: 800; letter-spacing: -.2px; }
        .meio { display: flex; align-items: center; gap: 4px; }
        .num { text-align: right; }
        .num b { font-size: 14px; display: block; letter-spacing: .5px; }
        .num span { font-size: 8.5px; color: #64748b; }

        .selo { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 8.5px; font-weight: 800; letter-spacing: .8px; }
        .selo.ok  { background: #e6f4ec; color: #198754; border: 1px solid #b7dfc7; }
        .selo.pre { background: #fff3cd; color: #856404; border: 1px solid #ffe08a; }
        .selo.dv  { background: #fd7e14; color: #fff; }

        h3 { font-size: 8px; letter-spacing: 1.1px; text-transform: uppercase; color: #64748b; margin: 5px 0 2px;
             border-bottom: 1px solid #e2e8f0; padding-bottom: 1px; }

        .info { display: grid; grid-template-columns: repeat(4, 1fr); gap: 2px 10px; margin-top: 4px; }
        .f .l { font-size: 7px; text-transform: uppercase; letter-spacing: .6px; color: #94a3b8; font-weight: 600; }
        .f .v { font-size: 10px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        .cols { display: grid; grid-template-columns: 1.15fr 1fr; gap: 12px; flex: 1; min-height: 0; }

        table { width: 100%; border-collapse: collapse; }
        th { font-size: 7px; text-transform: uppercase; letter-spacing: .5px; color: #64748b; text-align: left;
             background: #f1f5f9; padding: 2px 4px; border: 1px solid #e2e8f0; }
        td { padding: 2px 4px; border: 1px solid #e2e8f0; font-size: 9px; }
        .r { text-align: right; }
        tfoot td { font-weight: 800; background: #f8fafc; }
        .neg { color: #dc3545; font-weight: 700; } .pos { color: #198754; font-weight: 700; } .mut { color: #94a3b8; }

        .pesos { display: grid; grid-template-columns: repeat(4, 1fr); gap: 4px; }
        .pesos > div { border: 1px solid #e2e8f0; border-radius: 3px; padding: 2px 3px; text-align: center; }
        .pesos .l { font-size: 6.5px; text-transform: uppercase; letter-spacing: .4px; color: #94a3b8; font-weight: 700; }
        .pesos .v { font-size: 11.5px; font-weight: 800; }
        .pesos .h { font-size: 6.5px; color: #94a3b8; line-height: 1; }
        .pesos .liq { background: #f0fdf4; border-color: #b7dfc7; } .pesos .liq .v { color: #198754; }
        .pesos .falta { background: #fffbeb; border-color: #ffe08a; }

        .obs { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 3px; padding: 3px 6px; font-size: 9px; }

        .assinaturas { display: grid; grid-template-columns: 1.3fr 1fr; gap: 12px; }
        .ass .caixa { height: 14mm; border-bottom: 1.2px solid #111827; display: flex; align-items: flex-end; justify-content: center; }
        .ass .caixa img { max-height: 13mm; max-width: 100%; }
        .ass .nome { font-size: 9px; font-weight: 700; text-align: center; margin-top: 1px; }
        .ass .sub  { font-size: 7.5px; color: #64748b; text-align: center; }
        .vazio { color: #cbd5e1; font-size: 9px; padding-bottom: 4px; }

        .rodape { margin-top: 3px; text-align: center; font-size: 7px; color: #94a3b8; border-top: 1px dashed #cbd5e1; padding-top: 2px; }

        .marca { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; pointer-events: none; }
        .marca span { transform: rotate(-12deg); font-size: 34px; font-weight: 900; letter-spacing: 4px;
                      color: rgba(253,126,20,.16); border: 5px solid rgba(253,126,20,.16); padding: 2px 20px; border-radius: 8px; }

        @media print {
            body { background: #fff; }
            .toolbar, .guias { display: none !important; }
            .folha { box-shadow: none; margin: 0; width: 210mm; height: 296mm; }
        }
    </style>
</head>
<body>

<div class="toolbar">
    <a href="{{ $discharge->operation_id ? route('operations.show', $discharge->operation_id) : route('operations.index') }}">← Voltar</a>
    <div>
        @unless($confirmado)
            <span style="font-size:11px;color:#ffe08a;margin-right:10px;">{{ $avisoProvisorio }}</span>
        @endunless
        <label for="pos">Posição na folha:</label>
        <select id="pos" onchange="setPos(this.value)">
            <option value="1">1 — topo</option>
            <option value="2">2 — meio</option>
            <option value="3">3 — fundo</option>
        </select>
        <button class="primary" onclick="window.print()">Imprimir ticket</button>
    </div>
</div>

<div class="folha" id="folha">
    <div class="guias"></div>

    <div class="ticket">

        @unless($confirmado)
            <div class="marca"><span>PROVISÓRIO</span></div>
        @endunless

        {{-- Cabeçalho --}}
        <div class="head">
            <div>
                <div class="org">FEM — Fábrica de Explosivos de Moçambique</div>
                <div class="titulo">Ticket de Descarga</div>
            </div>
            <div class="meio">
                @if($confirmado)
                    <span class="selo ok">✓ CONFIRMADO NA BALANÇA</span>
                @elseif($fase === 'aguarda_tara')
                    <span class="selo pre">AGUARDA TARA (VAZIO)</span>
                @elseif($fase === 'aguarda_bruto')
                    <span class="selo pre">AGUARDA PESO CHEIO</span>
                @else
                    <span class="selo pre">AGUARDA BALANÇA</span>
                @endif
                @if($diverge)<span class="selo dv">DIVERGÊNCIA</span>@endif
            </div>
            <div class="num">
                <b>{{ $numero }}</b>
                <span>Guia {{ $discharge->numero_guia }}</span>
            </div>
        </div>

        {{-- Operação + Transporte --}}
        <div class="info">
            @if($op)
                <div class="f"><div class="l">Navio</div><div class="v">{{ $op->navio }}</div></div>
                <div class="f"><div class="l">Empresa</div><div class="v">{{ $op->empresa }}</div></div>
                <div class="f"><div class="l">BL</div><div class="v">{{ $op->bl ?: '—' }}</div></div>
                <div class="f"><div class="l">Factura</div><div class="v">{{ $op->factura ?: '—' }}</div></div>
            @endif
            <div class="f"><div class="l">Motorista</div><div class="v">{{ $discharge->motorista }}</div></div>
            <div class="f"><div class="l">Matrícula</div><div class="v">{{ $discharge->matricula }}</div></div>
            <div class="f"><div class="l">Transportadora</div><div class="v">{{ $discharge->transportadora }}</div></div>
            <div class="f"><div class="l">Atrelado</div>
                <div class="v">{{ $discharge->tipo_atrelado ? strtoupper($discharge->tipo_atrelado) : '—' }}</div></div>
            <div class="f"><div class="l">Data</div><div class="v">{{ $discharge->data->format('d/m/Y') }}</div></div>
            <div class="f"><div class="l">Saída do porto</div><div class="v">{{ $discharge->hora_saida_porto ?: '—' }}</div></div>
            <div class="f"><div class="l">Chegada à balança</div>
                <div class="v">{{ $discharge->hora_chegada_balanca ?: '—' }}@if($discharge->tempo_transporte) <span class="mut" style="font-weight:400;">({{ $discharge->tempo_transporte }} min)</span>@endif</div></div>
        </div>

        <div class="cols">
            {{-- Coluna esquerda: Carga + Observações --}}
            <div>
                <h3>Carga</h3>
                <table>
                    <thead>
                        <tr><th>Produto</th><th class="r">Porto</th><th class="r">Balança</th><th class="r">Dif.</th><th class="r">Peso est. (kg)</th></tr>
                    </thead>
                    <tbody>
                        @foreach($discharge->items as $l)
                        @php
                            $it  = $l->operationItem;
                            $dif = $l->quantidade_confirmada !== null ? (float) $l->quantidade_confirmada - (float) $l->quantidade : null;
                        @endphp
                        <tr>
                            <td><b>{{ $it->nome }}</b> <span class="mut">{{ $it->unidade === 'saco' ? '(' . $q($it->peso_unitario_kg) . ' kg/saco)' : '(granel)' }}</span></td>
                            <td class="r">{{ $q($l->quantidade) }} {{ $it->unidade_label }}</td>
                            <td class="r">{{ $l->quantidade_confirmada !== null ? $q($l->quantidade_confirmada) : '—' }}</td>
                            <td class="r {{ $dif === null ? 'mut' : (abs($dif) > 0.0005 ? 'neg' : 'pos') }}">
                                {{ $dif === null ? '—' : (abs($dif) > 0.0005 ? $sinal($dif, 3) : '0') }}</td>
                            <td class="r">{{ $n($l->quantidade * $kgUn($it)) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>Total de sacos</td>
                            <td class="r">{{ $discharge->total_sacos }}</td>
                            <td class="r">{{ $qtdConf ? $discharge->sacos_confirmados : '—' }}</td>
                            <td></td>
                            <td class="r">{{ $n($estimado) }}</td>
                        </tr>
                    </tfoot>
                </table>

                @if(filled($discharge->observacoes))
                    <h3>Observações</h3>
                    <div class="obs">{{ $discharge->observacoes }}</div>
                @endif
            </div>

            {{-- Coluna direita: Pesagem + Assinaturas --}}
            <div>
                <h3>Pesagem</h3>
                <div class="pesos">
                    <div class="{{ $discharge->peso_bruto === null ? 'falta' : '' }}">
                        <div class="l">Cheio / Bruto (kg)</div>
                        <div class="v">{{ $discharge->peso_bruto !== null ? $n($discharge->peso_bruto) : '—' }}</div>
                        @if($discharge->peso_bruto_em)<div class="h">{{ $discharge->peso_bruto_em->format('d/m H:i') }}</div>@endif
                    </div>
                    <div class="{{ $discharge->tara === null ? 'falta' : '' }}">
                        <div class="l">Vazio / Tara (kg)</div>
                        <div class="v">{{ $discharge->tara !== null ? $n($discharge->tara) : '—' }}</div>
                        @if($discharge->tara_em)<div class="h">{{ $discharge->tara_em->format('d/m H:i') }}</div>@endif
                    </div>
                    <div class="liq"><div class="l">Líquido (kg)</div><div class="v">{{ $liquido !== null ? $n($liquido) : '—' }}</div></div>
                    <div><div class="l">Líq. − est.</div>
                        <div class="v {{ $difPeso === null ? 'mut' : (abs($difPeso) > 0.5 ? ($difPeso < 0 ? 'neg' : 'pos') : '') }}">
                            {{ $difPeso === null ? '—' : $sinal($difPeso) }}</div></div>
                </div>

                <h3>Assinaturas</h3>
                <div class="assinaturas">
                    <div class="ass">
                        <div class="caixa">
                            @if($assin)
                                <img src="{{ $assin->imagem }}" alt="Assinatura do motorista">
                            @else
                                <span class="vazio">sem assinatura registada</span>
                            @endif
                        </div>
                        <div class="nome">{{ $assin->nome ?? $discharge->motorista }}</div>
                        <div class="sub">Motorista{{ $assin ? ' · ' . $assin->created_at->format('d/m/Y H:i') : '' }}</div>
                    </div>
                    <div class="ass">
                        <div class="caixa"><span class="vazio">{{ $confirmado ? '' : 'pendente' }}</span></div>
                        <div class="nome">{{ $discharge->confirmedBy->name ?? '—' }}</div>
                        <div class="sub">Balança{{ $confirmado ? ' · ' . $discharge->updated_at->format('d/m/Y H:i') : '' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="rodape">
            Registado no porto por {{ $discharge->registeredBy->name ?? '—' }} · {{ $discharge->created_at->format('d/m/Y H:i') }}
            · Emitido em {{ now()->format('d/m/Y H:i') }} por {{ auth()->user()->name }}
        </div>
    </div>
</div>

<script>
    function setPos(v) {
        document.getElementById('folha').style.setProperty('--pos', v);
        document.getElementById('pos').value = v;
        try { localStorage.setItem('ticket_pos', v); } catch (e) {}
    }
    (function () {
        var v = '1';
        try { v = localStorage.getItem('ticket_pos') || '1'; } catch (e) {}
        setPos(v);
    })();
</script>

</body>
</html>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Relatório — {{ $operation->navio }}</title>

    @php
        $n    = fn ($v, $d = 0) => number_format((float) $v, $d, ',', '.');
        $q    = fn ($v) => rtrim(rtrim(number_format((float) $v, 3, ',', '.'), '0'), ',');
        $ton  = fn ($kg) => number_format(((float) $kg) / 1000, 3, ',', '.');
        $sinal = fn ($v, $d = 0) => ($v > 0 ? '+' : '') . number_format((float) $v, $d, ',', '.');
        $refDoc = 'REL-' . str_pad($operation->id, 4, '0', STR_PAD_LEFT) . '/' . now()->format('Y');

        $estado = match ($operation->status) {
            'active'   => ['EM CURSO',  '#0aa2c0'],
            'archived' => ['ARQUIVADA', '#64748b'],
            default    => [$operation->status_label, '#64748b'],
        };
        $kgStatus = fn ($s) => ['confirmed' => 'Confirmado', 'in_transit' => 'Em trânsito', 'pending' => 'Pendente'][$s] ?? $s;
        $extras = $operation->transportadorasExtra();
    @endphp

    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 12mm 14mm 12mm;
            @bottom-left  { content: "{{ $refDoc }} · {{ $operation->navio }}"; font-size: 8px; color: #888; }
            @bottom-right { content: "Página " counter(page) " de " counter(pages); font-size: 8px; color: #888; }
        }
        * { box-sizing: border-box; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        body { font-family: -apple-system, "Segoe UI", Helvetica, Arial, sans-serif; color: #1e293b; margin: 0;
               background: #eef1f5; font-size: 11px; line-height: 1.4; }

        /* barra de ferramentas (não imprime) */
        .toolbar { position: sticky; top: 0; z-index: 10; background: #0e1015; color: #fff; padding: 10px 24px;
                   display: flex; justify-content: space-between; align-items: center; }
        .toolbar a, .toolbar button { font: inherit; font-size: 12px; color: #fff; text-decoration: none; cursor: pointer;
                   background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.2); border-radius: 6px; padding: 6px 14px; }
        .toolbar button.primary { background: #c60a1a; border-color: #c60a1a; font-weight: 600; }
        .toolbar a:hover, .toolbar button:hover { opacity: .85; }

        .page { background: #fff; max-width: 1180px; margin: 18px auto; padding: 26px 30px; box-shadow: 0 2px 14px rgba(0,0,0,.08); }

        /* cabeçalho */
        .doc-head { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #c60a1a; padding-bottom: 12px; margin-bottom: 14px; }
        .doc-org { font-size: 10px; letter-spacing: 1.5px; text-transform: uppercase; color: #64748b; }
        .doc-title { font-size: 20px; font-weight: 800; margin: 2px 0 0; letter-spacing: -.3px; }
        .doc-sub { font-size: 12px; color: #475569; margin-top: 2px; }
        .doc-meta { text-align: right; font-size: 10px; color: #64748b; line-height: 1.6; }
        .doc-meta b { color: #1e293b; }
        .estado { display: inline-block; color: #fff; font-weight: 700; font-size: 9px; letter-spacing: .8px;
                  padding: 2px 9px; border-radius: 20px; vertical-align: middle; margin-left: 8px; }

        /* secções */
        h2 { font-size: 11px; letter-spacing: 1.2px; text-transform: uppercase; color: #fff; background: #1e293b;
             padding: 5px 10px; margin: 18px 0 8px; border-radius: 3px; page-break-after: avoid; }
        h2 span { color: #fca5a5; margin-right: 6px; }
        .sec { page-break-inside: avoid; }

        /* ficha de identificação */
        .ficha { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0; border: 1px solid #e2e8f0; border-radius: 4px; }
        .ficha > div { padding: 7px 10px; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; }
        .ficha > div:nth-child(4n) { border-right: none; }
        .ficha .l { font-size: 8.5px; text-transform: uppercase; letter-spacing: .8px; color: #94a3b8; font-weight: 600; }
        .ficha .v { font-size: 11.5px; font-weight: 600; margin-top: 1px; }

        /* cartões de resumo */
        .cards { display: grid; grid-template-columns: repeat(6, 1fr); gap: 8px; }
        .card { border: 1px solid #e2e8f0; border-left: 4px solid var(--c, #1a56db); border-radius: 4px; padding: 8px 10px; }
        .card .l { font-size: 8.5px; text-transform: uppercase; letter-spacing: .8px; color: #94a3b8; font-weight: 700; }
        .card .v { font-size: 17px; font-weight: 800; line-height: 1.15; margin-top: 2px; }
        .card .s { font-size: 9px; color: #64748b; margin-top: 1px; }

        .bar { height: 9px; background: #e9edf2; border-radius: 5px; overflow: hidden; margin: 10px 0 3px; position: relative; }
        .bar .a { position: absolute; inset: 0 auto 0 0; background: #cbd5e1; }
        .bar .b { position: absolute; inset: 0 auto 0 0; background: linear-gradient(90deg,#1a56db,#0dcaf0); }
        .bar-leg { display: flex; justify-content: space-between; font-size: 9.5px; color: #64748b; }
        .dot { display: inline-block; width: 8px; height: 8px; border-radius: 2px; margin-right: 4px; }

        /* tabelas */
        table { width: 100%; border-collapse: collapse; }
        th { background: #f1f5f9; color: #475569; font-size: 8.5px; text-transform: uppercase; letter-spacing: .6px;
             padding: 5px 6px; border: 1px solid #e2e8f0; text-align: left; }
        td { padding: 4px 6px; border: 1px solid #e2e8f0; font-size: 10px; vertical-align: top; }
        tr { page-break-inside: avoid; }
        thead { display: table-header-group; }
        .r { text-align: right; } .c { text-align: center; }
        .nw { white-space: nowrap; }
        tfoot td { background: #f8fafc; font-weight: 700; }
        tr.div td { background: #fff7e6; }
        .pos { color: #198754; font-weight: 700; } .neg { color: #dc3545; font-weight: 700; } .mut { color: #94a3b8; }

        .tag { display: inline-block; font-size: 8.5px; font-weight: 700; padding: 1px 7px; border-radius: 20px; }
        .tag.ok   { background: #e6f4ec; color: #198754; }
        .tag.tr   { background: #e0f7fc; color: #0aa2c0; }
        .tag.pe   { background: #fff3cd; color: #856404; }
        .tag.dv   { background: #fd7e14; color: #fff; }
        .tag.extra{ background: #fd7e14; color: #fff; }
        .meta { display: inline-block; background: #f1f5f9; border-radius: 3px; padding: 1px 7px; margin: 2px 4px 0 0; font-size: 9.5px; }
        .prod { display: block; white-space: nowrap; }

        .tl { margin: 0; padding: 0; list-style: none; border-left: 2px solid #e2e8f0; }
        .tl li { position: relative; padding: 2px 0 6px 14px; font-size: 10.5px; }
        .tl li::before { content: ""; position: absolute; left: -5px; top: 6px; width: 8px; height: 8px; border-radius: 50%; background: #1a56db; }
        .tl .w { color: #94a3b8; font-size: 9.5px; margin-right: 6px; }

        .assin { display: grid; grid-template-columns: repeat(3, 1fr); gap: 40px; margin-top: 34px; page-break-inside: avoid; }
        .assin div { border-top: 1px solid #1e293b; padding-top: 4px; text-align: center; font-size: 10px; color: #475569; }
        .nota { font-size: 9px; color: #94a3b8; margin-top: 8px; }

        @media print {
            body { background: #fff; }
            .toolbar { display: none !important; }
            .page { box-shadow: none; margin: 0; padding: 0; max-width: none; }
        }
    </style>
</head>
<body>

<div class="toolbar">
    <a href="{{ route('operations.show', $operation) }}">← Voltar à operação</a>
    <div>
        <span style="font-size:11px;opacity:.7;margin-right:12px;">Para guardar em PDF, escolha “Guardar como PDF” no destino da impressão</span>
        <button class="primary" onclick="window.print()">Imprimir / Guardar PDF</button>
    </div>
</div>

<div class="page">

    {{-- CABEÇALHO --}}
    <div class="doc-head">
        <div>
            <div class="doc-org">FEM — Fábrica de Explosivos de Moçambique · Descarga de Nitrato de Amónio</div>
            <div class="doc-title">Relatório de Descarga — {{ $operation->navio }}
                <span class="estado" style="background:{{ $estado[1] }};">{{ $estado[0] }}</span>
            </div>
            <div class="doc-sub">{{ $operation->empresa }}@if($operation->bl) · BL {{ $operation->bl }}@endif @if($operation->factura) · Factura {{ $operation->factura }}@endif</div>
        </div>
        <div class="doc-meta">
            <div>Ref.: <b>{{ $refDoc }}</b></div>
            <div>Emitido em: <b>{{ now()->format('d/m/Y H:i') }}</b></div>
            <div>Emitido por: <b>{{ auth()->user()->name }}</b></div>
        </div>
    </div>

    {{-- 1. IDENTIFICAÇÃO --}}
    <div class="sec">
        <h2><span>1</span>Identificação da operação</h2>
        <div class="ficha">
            <div><div class="l">Navio</div><div class="v">{{ $operation->navio }}</div></div>
            <div><div class="l">Empresa</div><div class="v">{{ $operation->empresa }}</div></div>
            <div><div class="l">BL</div><div class="v">{{ $operation->bl ?: '—' }}</div></div>
            <div><div class="l">Factura</div><div class="v">{{ $operation->factura ?: '—' }}</div></div>
            <div><div class="l">Data de chegada</div><div class="v">{{ $operation->data_chegada?->format('d/m/Y') ?? '—' }}</div></div>
            <div><div class="l">Início da descarga</div><div class="v">{{ $operation->started_at?->format('d/m/Y H:i') ?? '—' }}</div></div>
            <div><div class="l">Primeiro / último camião</div>
                <div class="v">{{ $discharges->first()?->data?->format('d/m') ?? '—' }} → {{ $discharges->last()?->data?->format('d/m/Y') ?? '—' }}</div></div>
            <div><div class="l">Arquivada em</div><div class="v">{{ $operation->archived_at?->format('d/m/Y H:i') ?? '—' }}</div></div>
        </div>

        @if(!empty($operation->metadata))
            <div style="margin-top:6px;">
                @foreach($operation->metadata as $k => $v)
                    <span class="meta"><b>{{ $k }}:</b> {{ $v }}</span>
                @endforeach
            </div>
        @endif
        @if($operation->observacoes)
            <div style="margin-top:6px;font-size:10.5px;color:#475569;"><b>Observações:</b> {{ $operation->observacoes }}</div>
        @endif
    </div>

    {{-- 2. RESUMO --}}
    <div class="sec">
        <h2><span>2</span>Resumo executivo</h2>
        <div class="cards">
            <div class="card" style="--c:#1a56db;"><div class="l">Esperado</div><div class="v">{{ $n($resumo['esperado_ton'], 1) }} t</div><div class="s">segundo o plano</div></div>
            <div class="card" style="--c:#6f42c1;"><div class="l">Lançado no porto</div><div class="v">{{ $n($resumo['porto_ton'], 1) }} t</div><div class="s">{{ $resumo['lancado_pct'] }}% do plano (estimado)</div></div>
            <div class="card" style="--c:#198754;"><div class="l">Pesado na balança</div><div class="v">{{ $n($resumo['balanca_ton'], 1) }} t</div><div class="s">{{ $resumo['progresso'] }}% do plano (líquido)</div></div>
            <div class="card" style="--c:{{ $resumo['falta_ton'] > 0 ? '#dc3545' : '#198754' }};">
                <div class="l">{{ $resumo['falta_ton'] >= 0 ? 'Em falta' : 'Excesso' }}</div>
                <div class="v">{{ $n(abs($resumo['falta_ton']), 1) }} t</div><div class="s">plano − balança</div></div>
            <div class="card" style="--c:#0aa2c0;"><div class="l">Camiões</div><div class="v">{{ $resumo['camioes'] }}</div>
                <div class="s">{{ $resumo['confirmados'] }} confirmados · {{ $resumo['por_confirmar'] }} por confirmar</div></div>
            <div class="card" style="--c:{{ $resumo['divergencias'] > 0 ? '#fd7e14' : '#198754' }};"><div class="l">Divergências</div>
                <div class="v">{{ $resumo['divergencias'] }}</div>
                <div class="s">{{ $resumo['tempo_medio'] !== null ? 'Tempo médio: ' . $resumo['tempo_medio'] . ' min' : 'Sem tempos registados' }}</div></div>
        </div>

        <div class="bar"><div class="a" style="width:{{ $resumo['lancado_pct'] }}%"></div><div class="b" style="width:{{ $resumo['progresso'] }}%"></div></div>
        <div class="bar-leg">
            <span><i class="dot" style="background:#1a56db;"></i>Pesado na balança {{ $resumo['progresso'] }}%
                &nbsp; <i class="dot" style="background:#cbd5e1;"></i>Lançado no porto {{ $resumo['lancado_pct'] }}%</span>
            <span>Diferença pesado vs. estimado (camiões confirmados):
                <b class="{{ $resumo['dif_peso_kg'] < 0 ? 'neg' : 'pos' }}">{{ $sinal($resumo['dif_peso_kg']) }} kg</b></span>
        </div>
    </div>

    {{-- 3. PLANO VS REALIZADO --}}
    <div class="sec">
        <h2><span>3</span>Plano vs. realizado, por produto</h2>
        <table>
            <thead>
                <tr>
                    <th>Produto</th><th class="r">Peso/saco</th>
                    <th class="r">Plano</th><th class="r">No porto</th><th class="r">Confirmado</th>
                    <th class="r">Falta lançar</th><th class="r">Dif. balança vs. porto</th>
                    <th class="r">Plano (t)</th><th class="r">Porto (t)</th><th class="r">Balança (t)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($porProduto as $p)
                @php $it = $p['item']; @endphp
                <tr>
                    <td><b>{{ $it->nome }}</b></td>
                    <td class="r nw">{{ $it->unidade === 'saco' ? $q($it->peso_unitario_kg) . ' kg' : 'granel' }}</td>
                    <td class="r">{{ $q($p['plano']) }} {{ $it->unidade_label }}</td>
                    <td class="r">{{ $q($p['registado']) }}</td>
                    <td class="r">{{ $q($p['confirmado']) }}</td>
                    <td class="r {{ $p['falta'] > 0 ? 'neg' : 'pos' }}">{{ $q($p['falta']) }}</td>
                    <td class="r {{ abs($p['dif']) > 0.0005 ? 'neg' : 'mut' }}">{{ abs($p['dif']) > 0.0005 ? $sinal($p['dif'], 3) : '0' }}</td>
                    <td class="r">{{ $ton($p['plano_kg']) }}</td>
                    <td class="r">{{ $ton($p['porto_kg']) }}</td>
                    <td class="r">{{ $ton($p['confirmado_kg']) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7" class="r">Total</td>
                    <td class="r">{{ $ton($porProduto->sum('plano_kg')) }}</td>
                    <td class="r">{{ $ton($porProduto->sum('porto_kg')) }}</td>
                    <td class="r">{{ $ton($porProduto->sum('confirmado_kg')) }}</td>
                </tr>
            </tfoot>
        </table>
        <div class="nota">“Balança (t)” usa a quantidade confirmada × peso por saco; o peso líquido real da pesagem está no resumo e no detalhe dos camiões.</div>
    </div>

    {{-- 4. TRANSPORTADORAS --}}
    <div class="sec">
        <h2><span>4</span>Transportadoras</h2>
        <table>
            <thead>
                <tr>
                    <th>Transportadora</th><th class="c">Camiões</th><th class="c">Confirmados</th><th class="c">Atrelado normal</th><th class="c">Atrelado link</th><th class="r">Sacos</th>
                    <th class="r">Peso estimado (kg)</th><th class="r">Peso líquido (kg)</th>
                    <th class="c">Tempo médio</th><th class="c">Divergências</th>
                </tr>
            </thead>
            <tbody>
                @forelse($porTransp as $t)
                <tr>
                    <td><b>{{ $t['nome'] }}</b>
                        @if($t['extra'])<span class="tag extra">⚠ fora da lista prevista</span>@endif</td>
                    <td class="c">{{ $t['camioes'] }}</td>
                    <td class="c">{{ $t['confirmados'] }}</td>
                    <td class="c">{{ $t['normal'] }}</td>
                    <td class="c">{{ $t['link'] }}</td>
                    <td class="r">{{ $n($t['sacos']) }}</td>
                    <td class="r">{{ $n($t['estimado_kg']) }}</td>
                    <td class="r">{{ $n($t['liquido_kg']) }}</td>
                    <td class="c">{{ $t['tempo_medio'] !== null ? $t['tempo_medio'] . ' min' : '—' }}</td>
                    <td class="c {{ $t['divergencias'] ? 'neg' : 'mut' }}">{{ $t['divergencias'] }}</td>
                </tr>
                @empty
                <tr><td colspan="10" class="c mut">Sem camiões registados.</td></tr>
                @endforelse
            </tbody>
            @if($porTransp->isNotEmpty())
            <tfoot>
                <tr>
                    <td>Total</td>
                    <td class="c">{{ $porTransp->sum('camioes') }}</td>
                    <td class="c">{{ $porTransp->sum('confirmados') }}</td>
                    <td class="c">{{ $porTransp->sum('normal') }}</td>
                    <td class="c">{{ $porTransp->sum('link') }}</td>
                    <td class="r">{{ $n($porTransp->sum('sacos')) }}</td>
                    <td class="r">{{ $n($porTransp->sum('estimado_kg')) }}</td>
                    <td class="r">{{ $n($porTransp->sum('liquido_kg')) }}</td>
                    <td></td>
                    <td class="c">{{ $porTransp->sum('divergencias') }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
        @if($semCamioes->isNotEmpty())
            <div class="nota">Previstas na operação sem camiões: {{ $semCamioes->implode(', ') }}.</div>
        @endif
    </div>

    {{-- 5. DETALHE DOS CAMIÕES --}}
    <div>
        <h2><span>5</span>Detalhe dos camiões ({{ $discharges->count() }})</h2>
        <table>
            <thead>
                <tr>
                    <th>Nº / Data</th><th>Guia</th><th>Motorista / Matrícula</th><th>Transportadora</th>
                    <th>Saída → Chegada</th><th>Carga (porto → balança)</th>
                    <th class="r">Estimado (kg)</th><th class="r">Bruto</th><th class="r">Tara</th>
                    <th class="r">Líquido (kg)</th><th class="r">Dif. (kg)</th><th class="c">Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($discharges as $d)
                @php
                    $dv  = $d->hasDivergence();
                    $dif = $d->status === 'confirmed' && $d->peso_liquido !== null ? (float) $d->peso_liquido - (float) $d->peso_total_estimado : null;
                    $tpExtra = in_array(mb_strtolower($d->transportadora), $extras, true);
                @endphp
                <tr class="{{ $dv ? 'div' : '' }}">
                    <td class="nw"><b>#{{ $d->id }}</b><br>{{ $d->data->format('d/m/Y') }}</td>
                    <td class="nw"><b>{{ $d->numero_guia }}</b></td>
                    <td>{{ $d->motorista }}<br><span class="mut">{{ $d->matricula }}@if($d->tipo_atrelado) · {{ strtoupper($d->tipo_atrelado) }}@endif</span></td>
                    <td>{{ $d->transportadora }}@if($tpExtra)<br><span class="tag extra">⚠ fora da lista</span>@endif</td>
                    <td class="nw">{{ $d->hora_saida_porto ?: '—' }} → {{ $d->hora_chegada_balanca ?: '—' }}
                        @if($d->tempo_transporte)<br><span class="mut">{{ $d->tempo_transporte }} min</span>@endif</td>
                    <td>
                        @foreach($d->items as $l)
                            @php $dif1 = $l->quantidade_confirmada !== null && abs($l->quantidade_confirmada - $l->quantidade) > 0.0005; @endphp
                            <span class="prod">{{ $l->operationItem->nome }}:
                                {{ $q($l->quantidade) }}
                                @if($l->quantidade_confirmada !== null)
                                    → <b class="{{ $dif1 ? 'neg' : 'pos' }}">{{ $q($l->quantidade_confirmada) }}</b>
                                @endif
                            </span>
                        @endforeach
                    </td>
                    <td class="r">{{ $n($d->peso_total_estimado) }}</td>
                    <td class="r">{{ $d->peso_bruto !== null ? $n($d->peso_bruto) : '—' }}</td>
                    <td class="r">{{ $d->tara !== null ? $n($d->tara) : '—' }}</td>
                    <td class="r"><b>{{ $d->peso_liquido !== null ? $n($d->peso_liquido) : '—' }}</b></td>
                    <td class="r {{ $dif === null ? 'mut' : (abs($dif) > 0.5 ? ($dif < 0 ? 'neg' : 'pos') : 'mut') }}">
                        {{ $dif === null ? '—' : $sinal($dif) }}</td>
                    <td class="c">
                        <span class="tag {{ ['confirmed' => 'ok', 'in_transit' => 'tr'][$d->status] ?? 'pe' }}">{{ $kgStatus($d->status) }}</span>
                        @if($dv)<br><span class="tag dv">Divergência</span>@endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="12" class="c mut">Sem camiões registados nesta operação.</td></tr>
                @endforelse
            </tbody>
            @if($discharges->isNotEmpty())
            <tfoot>
                <tr>
                    <td colspan="6" class="r">Totais ({{ $discharges->count() }} camiões)</td>
                    <td class="r">{{ $n($discharges->sum('peso_total_estimado')) }}</td>
                    <td class="r">{{ $n($discharges->sum('peso_bruto')) }}</td>
                    <td class="r">{{ $n($discharges->sum('tara')) }}</td>
                    <td class="r">{{ $n($discharges->sum('peso_liquido')) }}</td>
                    <td class="r">{{ $sinal($resumo['dif_peso_kg']) }}</td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
        <div class="nota">“Dif. (kg)” = peso líquido da balança − peso estimado no porto, só nos camiões confirmados. Linhas a amarelo têm divergência entre o registado no porto e a balança.</div>
    </div>

    {{-- 6. DIVERGÊNCIAS E OBSERVAÇÕES --}}
    @php
        $comDiv  = $discharges->filter(fn ($d) => $d->hasDivergence());
        $comObs  = $discharges->filter(fn ($d) => filled($d->observacoes));
    @endphp
    <div class="sec">
        <h2><span>6</span>Divergências e observações</h2>
        @if($comDiv->isEmpty() && $comObs->isEmpty())
            <div style="color:#198754;font-weight:600;">✓ Sem divergências nem observações registadas.</div>
        @else
            <table>
                <thead><tr><th>Camião</th><th>Guia</th><th>Motorista</th><th>Divergência por produto (porto → balança)</th><th>Observações</th><th>Confirmado por</th></tr></thead>
                <tbody>
                @foreach($comDiv->merge($comObs)->unique('id')->sortBy('id') as $d)
                    <tr>
                        <td class="nw">#{{ $d->id }} · {{ $d->data->format('d/m/Y') }}</td>
                        <td class="nw">{{ $d->numero_guia }}</td>
                        <td>{{ $d->motorista }} <span class="mut">({{ $d->matricula }})</span></td>
                        <td>
                            @forelse($d->items->filter(fn ($l) => $l->quantidade_confirmada !== null && abs($l->quantidade_confirmada - $l->quantidade) > 0.0005) as $l)
                                <span class="prod">{{ $l->operationItem->nome }}: {{ $q($l->quantidade) }} → <b class="neg">{{ $q($l->quantidade_confirmada) }}</b>
                                    ({{ $sinal($l->quantidade_confirmada - $l->quantidade, 3) }})</span>
                            @empty <span class="mut">—</span> @endforelse
                        </td>
                        <td>{{ $d->observacoes ?: '—' }}</td>
                        <td>{{ $d->confirmedBy->name ?? '—' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- 7. HISTÓRICO --}}
    <div class="sec">
        <h2><span>7</span>Histórico da operação</h2>
        <ul class="tl">
            @foreach($historico as $h)
                <li><span class="w">{{ $h['quando']->format('d/m/Y H:i') }}</span>{{ $h['texto'] }}</li>
            @endforeach
        </ul>
    </div>

    {{-- 8. ASSINATURAS --}}
    <div class="assin">
        <div>Elaborado por<br><b>{{ auth()->user()->name }}</b></div>
        <div>Verificado por</div>
        <div>Aprovado por</div>
    </div>
    <div class="nota" style="text-align:center;">Documento gerado automaticamente pelo Sistema de Gestão FEM · {{ $refDoc }} · {{ now()->format('d/m/Y H:i') }}</div>

</div>
</body>
</html>
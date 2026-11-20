#!/usr/bin/env python3
"""
Mostra a justificação do atraso (sinal vermelho) no ticket, na ficha do camião e no relatório.

Uso, na pasta do maintenance-system:
    python3 aplicar_atraso_nos_documentos.py

Ficheiros alterados:
    app/Models/Discharge.php
    resources/views/discharges/ticket.blade.php
    resources/views/discharges/show.blade.php
    resources/views/operations/report.blade.php

É "tudo ou nada": se alguma âncora obrigatória não for encontrada exactamente uma vez,
não escreve NENHUM ficheiro. Antes de escrever, faz cópias (*.bak-atraso).
"""
import pathlib
import shutil
import sys

PLANO = {}  # caminho -> {'marca': str, 'edicoes': [(nome, antigo, novo, obrigatorio)]}


def ficheiro(caminho, marca):
    PLANO[caminho] = {'marca': marca, 'edicoes': []}
    return PLANO[caminho]['edicoes']


# ───────────────────────── Model ─────────────────────────
e = ficheiro('app/Models/Discharge.php', 'atrasoMotivoTexto')
e.append((
    'metodos novos no model',
    'public function atrasoJustificadoPor(): BelongsTo',
    r'''/** Texto do motivo do atraso: "Fila de descarga" ou "Outro: <texto>". null se não há justificação. */
    public function atrasoMotivoTexto(): ?string
    {
        if (! $this->atrasoJustificado()) {
            return null;
        }

        $rotulo = self::ATRASO_MOTIVOS[$this->atraso_motivo] ?? $this->atraso_motivo;
        $texto  = trim((string) $this->atraso_justificacao);

        return $this->atraso_motivo === 'outro' && $texto !== ''
            ? "{$rotulo}: {$texto}"
            : $rotulo;
    }

    /**
     * Chegou à balança no vermelho e ninguém justificou (registos anteriores a esta regra,
     * ou chegada marcada pelo tablet). Só conta depois da chegada, porque aí o atraso já não muda.
     */
    public function atrasoSemJustificacao(): bool
    {
        return $this->chegou_balanca_em !== null
            && $this->nivelAtraso() === 'danger'
            && ! $this->atrasoJustificado();
    }

    public function atrasoJustificadoPor(): BelongsTo''',
    True,
))

# ───────────────────────── Ticket ─────────────────────────
e = ficheiro('resources/views/discharges/ticket.blade.php', 'atrasoMotivoTexto')
e.append((
    'campo "Atraso justificado" na grelha (12.º lugar, sem aumentar a altura)',
    r'''<div class="v">{{ $discharge->hora_chegada_balanca ?: '—' }}@if($discharge->tempo_transporte) <span class="mut" style="font-weight:400;">({{ $discharge->tempo_transporte }} min)</span>@endif</div></div>''',
    r'''<div class="v">{{ $discharge->hora_chegada_balanca ?: '—' }}@if($discharge->tempo_transporte) <span class="mut" style="font-weight:400;">({{ $discharge->tempo_transporte }} min)</span>@endif</div></div>
            @if($discharge->atrasoJustificado())
                <div class="f"><div class="l">Atraso justificado</div>
                    <div class="v" title="{{ $discharge->atrasoMotivoTexto() }}">{{ $discharge->atrasoMotivoTexto() }}</div></div>
            @elseif($discharge->atrasoSemJustificacao())
                <div class="f"><div class="l">Atraso</div>
                    <div class="v neg">{{ $discharge->atrasoMinutos() }} min · sem justificação</div></div>
            @endif''',
    True,
))
e.append((
    'quem justificou, no rodapé',
    r'''Registado no porto por {{ $discharge->registeredBy->name ?? '—' }} · {{ $discharge->created_at->format('d/m/Y H:i') }}''',
    r'''Registado no porto por {{ $discharge->registeredBy->name ?? '—' }} · {{ $discharge->created_at->format('d/m/Y H:i') }}
            @if($discharge->atrasoJustificado())
                · Atraso de {{ $discharge->atrasoMinutos() }} min justificado por {{ $discharge->atrasoJustificadoPor?->name ?? '—' }}@if($discharge->atraso_justificado_em) ({{ $discharge->atraso_justificado_em->format('d/m H:i') }})@endif
            @endif''',
    True,
))

# ───────────────────────── Ficha do camião (modal) ─────────────────────────
e = ficheiro('resources/views/discharges/show.blade.php', 'atrasoSemJustificacao')
e.append((
    'bloco do atraso antes dos produtos',
    r'''<div class="fw-bold mb-1" style="font-size:.72rem;text-transform:uppercase;color:#64748b;">Produtos</div>''',
    r'''@if($discharge->atrasoJustificado())
        <div class="mb-3 p-2 rounded" style="background:#fdecea;border:1px solid #f5c2c0;font-size:.74rem;">
            <div class="fw-bold text-danger">
                <i class="bi bi-exclamation-octagon-fill me-1"></i>Atraso justificado ({{ $discharge->atrasoMinutos() }} min)
            </div>
            <div>{{ $discharge->atrasoMotivoTexto() }}</div>
            <div class="text-muted" style="font-size:.66rem;">
                Por {{ $discharge->atrasoJustificadoPor?->name ?? '—' }}@if($discharge->atraso_justificado_em) · {{ $discharge->atraso_justificado_em->format('d/m H:i') }}@endif
            </div>
        </div>
    @elseif($discharge->atrasoSemJustificacao())
        <div class="mb-3 p-2 rounded" style="background:#fff3cd;border:1px solid #ffe08a;font-size:.74rem;">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>Chegou à balança com {{ $discharge->atrasoMinutos() }} min de atraso, sem justificação.
        </div>
    @endif

    <div class="fw-bold mb-1" style="font-size:.72rem;text-transform:uppercase;color:#64748b;">Produtos</div>''',
    True,
))

# ───────────────────────── Relatório da operação ─────────────────────────
e = ficheiro('resources/views/operations/report.blade.php', 'motivosAtraso')
e.append((
    'estilos das etiquetas de atraso',
    '.tag.extra{ background: #fd7e14; color: #fff; }',
    r'''.tag.extra{ background: #fd7e14; color: #fff; }
        .tag.atr { background: #fdecea; color: #b3261e; white-space: normal; display: inline-block; max-width: 170px; border-radius: 6px; margin-top: 2px; }
        .tag.atr-sem { background: #b3261e; color: #fff; margin-top: 2px; }''',
    True,
))
e.append((
    'colecoes de atraso no topo',
    '$extras = $operation->transportadorasExtra();',
    r'''$extras = $operation->transportadorasExtra();
        $motivosAtraso = $discharges->filter(fn ($d) => $d->atrasoJustificado());
        $semJustificar = $discharges->filter(fn ($d) => $d->atrasoSemJustificacao());''',
    True,
))
e.append((
    'etiqueta de atraso na tabela de detalhe',
    r'''@if($d->tempo_transporte)<br><span class="mut">{{ $d->tempo_transporte }} min</span>@endif</td>''',
    r'''@if($d->tempo_transporte)<br><span class="mut">{{ $d->tempo_transporte }} min</span>@endif
                        @if($d->atrasoJustificado())<br><span class="tag atr">Atraso: {{ $d->atrasoMotivoTexto() }}</span>
                        @elseif($d->atrasoSemJustificacao())<br><span class="tag atr-sem">Atraso sem justificação</span>@endif</td>''',
    True,
))
e.append((
    'colecao comAtraso',
    '$comObs  = $discharges->filter(fn ($d) => filled($d->observacoes));',
    r'''$comObs  = $discharges->filter(fn ($d) => filled($d->observacoes));
        $comAtraso = $discharges->filter(fn ($d) => $d->atrasoJustificado() || $d->atrasoSemJustificacao());''',
    True,
))
e.append((
    'titulo da seccao 6',
    '<h2><span>6</span>Divergências e observações</h2>',
    '<h2><span>6</span>Divergências, atrasos e observações</h2>',
    False,
))
e.append((
    'resumo dos atrasos + condicao de seccao vazia',
    '@if($comDiv->isEmpty() && $comObs->isEmpty())',
    r'''@if($motivosAtraso->isNotEmpty() || $semJustificar->isNotEmpty())
            <div style="margin-bottom:6px;font-size:10.5px;">
                <b>Atrasos (sinal vermelho):</b> {{ $motivosAtraso->count() }} justificado(s)
                @foreach($motivosAtraso->groupBy('atraso_motivo') as $m => $g)
                    · {{ \App\Models\Discharge::ATRASO_MOTIVOS[$m] ?? $m }}: {{ $g->count() }}
                @endforeach
                @if($semJustificar->isNotEmpty()) · <b class="neg">{{ $semJustificar->count() }} sem justificação</b>@endif
            </div>
        @endif
        @if($comDiv->isEmpty() && $comObs->isEmpty() && $comAtraso->isEmpty())''',
    True,
))
e.append((
    'mensagem de seccao vazia',
    '✓ Sem divergências nem observações registadas.',
    '✓ Sem divergências, atrasos nem observações registadas.',
    False,
))
e.append((
    'coluna Atraso no cabecalho da tabela da seccao 6',
    '<th>Confirmado por</th></tr></thead>',
    '<th>Atraso (porto → balança)</th><th>Confirmado por</th></tr></thead>',
    True,
))
e.append((
    'linhas da seccao 6 incluem os atrasos',
    '@foreach($comDiv->merge($comObs)->unique(\'id\')->sortBy(\'id\') as $d)',
    '@foreach($comDiv->merge($comObs)->merge($comAtraso)->unique(\'id\')->sortBy(\'id\') as $d)',
    True,
))
e.append((
    'celula do atraso na tabela da seccao 6',
    r'''<td>{{ $d->confirmedBy->name ?? '—' }}</td>''',
    r'''<td>
                            @if($d->atrasoJustificado())
                                <b class="neg">{{ $d->atrasoMinutos() }} min</b> · {{ $d->atrasoMotivoTexto() }}
                                <br><span class="mut">{{ $d->atrasoJustificadoPor?->name ?? '—' }}@if($d->atraso_justificado_em) · {{ $d->atraso_justificado_em->format('d/m H:i') }}@endif</span>
                            @elseif($d->atrasoSemJustificacao())
                                <b class="neg">{{ $d->atrasoMinutos() }} min</b> · <span class="neg">sem justificação</span>
                            @else
                                <span class="mut">—</span>
                            @endif
                        </td>
                        <td>{{ $d->confirmedBy->name ?? '—' }}</td>''',
    True,
))


def main():
    novos = {}
    falhas = []

    for caminho, bloco in PLANO.items():
        p = pathlib.Path(caminho)
        if not p.exists():
            falhas.append(f'{caminho}: ficheiro não encontrado (corra o script na pasta do maintenance-system)')
            continue

        texto = p.read_text(encoding='utf-8')
        if bloco['marca'] in texto:
            print(f'  já aplicado  {caminho}')
            continue

        print(caminho)
        for nome, antigo, novo, obrigatorio in bloco['edicoes']:
            n = texto.count(antigo)
            if n == 1:
                texto = texto.replace(antigo, novo)
                print(f'    ok         {nome}')
            elif obrigatorio:
                falhas.append(f'{caminho} → {nome}: encontrei {n} vez(es), esperava 1')
                print(f'    FALHOU     {nome} ({n} ocorrências)')
            else:
                print(f'    ignorada   {nome} ({n} ocorrências; é só texto)')
        novos[caminho] = texto

    if falhas:
        print('\nNada foi alterado. Cole-me esta lista:')
        for f in falhas:
            print('  -', f)
        sys.exit(1)

    if not novos:
        print('\nNada a fazer.')
        return

    for caminho, texto in novos.items():
        shutil.copy2(caminho, caminho + '.bak-atraso')
        pathlib.Path(caminho).write_text(texto, encoding='utf-8')

    print('\nFeito. Cópias de segurança: *.bak-atraso')


if __name__ == '__main__':
    main()

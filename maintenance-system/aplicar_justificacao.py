#!/usr/bin/env python3
"""
Liga a justificação do atraso (sinal vermelho) ao modal da balança.

Uso, na pasta do maintenance-system:
    python3 aplicar_justificacao.py

Faz uma cópia de segurança (index.blade.php.bak-justificacao) e só escreve o ficheiro
se TODAS as âncoras obrigatórias forem encontradas exactamente uma vez.
"""
import pathlib
import shutil
import sys

CAMINHO = pathlib.Path('resources/views/discharges/index.blade.php')

# (nome, texto a encontrar, texto novo, obrigatório)
EDICOES = []


def edicao(nome, antigo, novo, obrigatorio=True):
    EDICOES.append((nome, antigo, novo, obrigatorio))


# 1) Limites do sinal = config/operacoes.php (uma só fonte de verdade, igual ao servidor)
edicao(
    'comentario dos limites',
    "// Alerta porto -> balança (minutos): amarelo a 1h20, vermelho a 2h",
    "// Alerta porto -> balança (minutos): limites em config/operacoes.php (iguais aos do servidor)",
    obrigatorio=False,
)
edicao(
    'limite laranja',
    "const SLA_AMARELO  = 80;",
    "const SLA_AMARELO  = @json((int) config('operacoes.atraso_laranja_min', 60));",
)
edicao(
    'limite vermelho',
    "const SLA_VERMELHO = 120;",
    "const SLA_VERMELHO = @json((int) config('operacoes.atraso_vermelho_min', 80));",
)

# 2) O botão da balança leva os dados do atraso
edicao(
    'dados no botao da balanca',
    'data-saida="{{ $d->hora_saida_porto }}"',
    '''data-saida="{{ $d->hora_saida_porto }}"
                                    data-inicio="{{ $d->inicioTransito()?->toIso8601String() }}"
                                    data-atraso-min="{{ $d->atrasoMinutos() }}"
                                    data-atraso-fixo="{{ $d->chegou_balanca_em ? 1 : 0 }}"
                                    data-justificado="{{ $d->atrasoJustificado() ? 1 : 0 }}"''',
)

# 3) Bloco da justificação no modal (escondido; só aparece no vermelho)
edicao(
    'bloco html da justificacao',
    '{{-- Hora chegada + tempo --}}',
    '''{{-- Justificação do atraso: só aparece com sinal vermelho --}}
                        <div class="col-12 d-none" id="blocoJustificacao">
                            <div class="p-2 rounded" style="background:#fdecea;border:1px solid #f5c2c0;">
                                <div class="fw-bold text-danger" style="font-size:.76rem;">
                                    <i class="bi bi-exclamation-octagon-fill me-1"></i>
                                    Sinal vermelho: <span id="atrasoMinTexto"></span> min desde a saída do porto
                                    (limite <span id="atrasoLimiteTexto"></span> min)
                                </div>
                                <div class="text-muted mb-2" style="font-size:.7rem;">
                                    Justifique o atraso para confirmar a chegada à balança.
                                </div>
                                <div class="d-flex gap-2 flex-wrap">
                                    @foreach(\\App\\Models\\Discharge::ATRASO_MOTIVOS as $chave => $rotulo)
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-motivo"
                                                data-motivo="{{ $chave }}" aria-pressed="false"
                                                onclick="escolherMotivo('{{ $chave }}')">{{ $rotulo }}</button>
                                    @endforeach
                                </div>
                                <input type="hidden" name="atraso_motivo" id="atraso_motivo">
                                <div id="motivoOutroWrap" class="d-none mt-2">
                                    <label class="form-label lbl-b" for="atraso_justificacao">Descreva o motivo</label>
                                    <textarea name="atraso_justificacao" id="atraso_justificacao"
                                              class="form-control form-control-sm" rows="2" maxlength="500"
                                              style="border-radius:7px;font-size:.76rem;"
                                              placeholder="Ex: avaria no caminho"></textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Hora chegada + tempo --}}''',
)

# 4) Ao abrir o modal, decide se mostra o bloco
edicao(
    'preparar ao abrir o modal',
    "construirCamposBalanca(b.data('itens') || []);",
    '''prepararJustificacao(b);
        construirCamposBalanca(b.data('itens') || []);''',
)

# 5) Não deixa confirmar a chegada no vermelho sem motivo
edicao(
    'validacao ao submeter',
    "document.getElementById('formConfirm').addEventListener('submit', function(e){",
    '''document.getElementById('formConfirm').addEventListener('submit', function(e){
        // Sinal vermelho: sem motivo do atraso não confirma a chegada
        if (_exigeJustificacao) {
            const motivo = $('#atraso_motivo').val();
            if (!motivo) {
                e.preventDefault();
                setBalancaStatus('Escolha o motivo do atraso (sinal vermelho).', '#dc3545', 'bi-x-circle-fill');
                return;
            }
            if (motivo === 'outro' && $('#atraso_justificacao').val().trim().length < 5) {
                e.preventDefault();
                setBalancaStatus('Descreva o motivo do atraso (mínimo 5 caracteres).', '#dc3545', 'bi-x-circle-fill');
                $('#atraso_justificacao').trigger('focus');
                return;
            }
        }
''',
)

# 6) Funções novas
edicao(
    'funcoes da justificacao',
    "function setBalancaStatus(msg, cor = '#64748b', icone = 'bi-info-circle') {",
    '''/* ── Justificação do atraso (só com sinal vermelho) ── */
let _exigeJustificacao = false;

// Minutos desde a saída do porto. Se o camião já chegou, o valor está fixo (vem do servidor).
function minutosDeAtraso(b) {
    if (String(b.attr('data-atraso-fixo')) === '1') {
        const m = parseInt(b.attr('data-atraso-min'), 10);
        return Number.isNaN(m) ? null : m;
    }
    const inicio = b.attr('data-inicio');
    if (!inicio) return null;
    const t = new Date(inicio).getTime();
    if (Number.isNaN(t)) return null;
    return Math.max(0, Math.round((Date.now() - t) / 60000));
}

function prepararJustificacao(b) {
    const min = minutosDeAtraso(b);
    const jaJustificado = String(b.attr('data-justificado')) === '1';
    _exigeJustificacao = !jaJustificado && min !== null && min >= SLA_VERMELHO;

    $('#atraso_motivo').val('');
    $('#atraso_justificacao').val('');
    $('#motivoOutroWrap').addClass('d-none');
    $('#blocoJustificacao .btn-motivo').removeClass('active').attr('aria-pressed', 'false');
    $('#blocoJustificacao').toggleClass('d-none', !_exigeJustificacao);

    if (_exigeJustificacao) {
        $('#atrasoMinTexto').text(min);
        $('#atrasoLimiteTexto').text(SLA_VERMELHO);
    }
}

function escolherMotivo(motivo) {
    $('#atraso_motivo').val(motivo);
    $('#blocoJustificacao .btn-motivo').each(function () {
        const ligado = $(this).attr('data-motivo') === motivo;
        $(this).toggleClass('active', ligado).attr('aria-pressed', ligado ? 'true' : 'false');
    });
    $('#motivoOutroWrap').toggleClass('d-none', motivo !== 'outro');
    if (motivo === 'outro') $('#atraso_justificacao').trigger('focus');
    setBalancaStatus('', '#64748b');
}

function setBalancaStatus(msg, cor = '#64748b', icone = 'bi-info-circle') {''',
)


def main():
    if not CAMINHO.exists():
        sys.exit(f'Não encontrei {CAMINHO}. Corra o script na pasta do maintenance-system.')

    texto = CAMINHO.read_text(encoding='utf-8')

    if 'blocoJustificacao' in texto:
        sys.exit('A justificação já está aplicada neste ficheiro. Nada a fazer.')

    falhas = []
    for nome, antigo, novo, obrigatorio in EDICOES:
        n = texto.count(antigo)
        if n == 1:
            texto = texto.replace(antigo, novo)
            print(f'  ok       {nome}')
        elif obrigatorio:
            falhas.append(f'{nome}: encontrei {n} vez(es), esperava 1')
            print(f'  FALHOU   {nome} ({n} ocorrências)')
        else:
            print(f'  ignorada {nome} ({n} ocorrências; é só um comentário)')

    if falhas:
        print('\nNada foi alterado. Cole-me esta lista:')
        for f in falhas:
            print('  -', f)
        sys.exit(1)

    shutil.copy2(CAMINHO, str(CAMINHO) + '.bak-justificacao')
    CAMINHO.write_text(texto, encoding='utf-8')
    print(f'\nFeito. Cópia de segurança: {CAMINHO}.bak-justificacao')


if __name__ == '__main__':
    main()

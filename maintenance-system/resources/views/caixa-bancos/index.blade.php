<x-app-layout>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    .page-wrap{padding:28px 24px 64px}
    .field-lbl{font-size:.6rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#94a3b8;display:block;margin-bottom:5px}
    .form-control,.form-select{font-size:.83rem;border:1px solid #e2e8f0;border-radius:8px;color:#1e293b;background:#fff;transition:border-color .15s,box-shadow .15s;padding:7px 11px}
    .form-control:focus,.form-select:focus{border-color:#64748b;box-shadow:0 0 0 3px rgba(100,116,139,.1);outline:none}
    .top-btn{display:inline-flex;align-items:center;gap:6px;padding:7px 16px;border-radius:8px;font-size:.78rem;font-weight:600;text-decoration:none;border:1px solid #e2e8f0;background:#fff;color:#475569;transition:all .15s;cursor:pointer}
    .top-btn:hover{background:#f8fafc;border-color:#cbd5e1;color:#334155}
    .top-btn.primary{background:#1e293b;border-color:#1e293b;color:#fff}
    .top-btn.primary:hover{background:#334155;color:#fff}
    .top-btn.success{background:#fff;border-color:#bbf7d0;color:#16a34a}
    .top-btn.success:hover{background:#f0fdf4}
    .top-btn.danger-outline{background:#fff;border-color:#fecaca;color:#dc2626}
    .top-btn.danger-outline:hover{background:#fef2f2}

    .section{background:#fff;border:1px solid #e2e8f0;border-radius:12px;margin-bottom:16px;overflow:hidden}
    .section-head{display:flex;align-items:center;gap:10px;padding:12px 20px;background:#f8fafc;border-bottom:1px solid #e2e8f0}
    .s-num{font-size:.6rem;font-weight:800;letter-spacing:1.5px;color:#94a3b8;text-transform:uppercase;background:#e2e8f0;border-radius:4px;padding:2px 7px}
    .s-title{font-size:.8rem;font-weight:700;color:#334155;letter-spacing:.3px}
    .section-body{padding:20px 24px}

    .caixa-tabs{display:flex;gap:8px;margin-bottom:16px}
    .caixa-tab{padding:7px 20px;border-radius:8px;font-size:.78rem;font-weight:700;cursor:pointer;border:1px solid #e2e8f0;background:#fff;color:#64748b;transition:all .15s}
    .caixa-tab.active{background:#1e293b;border-color:#1e293b;color:#fff}
    .caixa-tab:hover:not(.active){background:#f8fafc;border-color:#94a3b8}

    .mov-table{width:100%;border-collapse:collapse}
    .mov-table thead th{font-size:.6rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#94a3b8;background:#f8fafc;border-bottom:1px solid #e2e8f0;padding:8px 8px;white-space:nowrap}
    .mov-table tbody td{padding:4px 5px;border-bottom:1px solid #f1f5f9;vertical-align:middle}
    .mov-table tbody tr:hover td{background:#f8fafc}
    .mov-table tfoot td{padding:9px 8px;font-size:.8rem;font-weight:700;color:#1e293b;background:#f1f5f9;border-top:2px solid #e2e8f0}

    .cell-input{width:100%;border:1px solid transparent;border-radius:5px;padding:4px 7px;font-size:.78rem;color:#334155;background:transparent;transition:all .15s}
    .cell-input:focus{border-color:#64748b;background:#fff;box-shadow:0 0 0 2px rgba(100,116,139,.1);outline:none}
    .cell-input.entrada{color:#16a34a;font-weight:600}
    .cell-input.saida{color:#dc2626;font-weight:600}
    .cell-input:hover:not(:focus){background:#f8fafc}

    .saldo-pos{color:#16a34a;font-weight:700;font-size:.78rem;text-align:right;padding-right:8px}
    .saldo-neg{color:#dc2626;font-weight:700;font-size:.78rem;text-align:right;padding-right:8px}
    .saldo-zero{color:#94a3b8;font-size:.78rem;text-align:right;padding-right:8px}

    .btn-remove{width:22px;height:22px;border-radius:5px;border:1px solid #fecaca;background:#fff;color:#dc2626;display:inline-flex;align-items:center;justify-content:center;font-size:.7rem;cursor:pointer;transition:all .15s}
    .btn-remove:hover{background:#fef2f2}
    .btn-add-row{display:inline-flex;align-items:center;gap:5px;padding:5px 14px;border-radius:7px;font-size:.75rem;font-weight:600;border:1px dashed #cbd5e1;background:#f8fafc;color:#64748b;cursor:pointer;transition:all .15s;margin:10px 20px}
    .btn-add-row:hover{border-color:#94a3b8;background:#f1f5f9;color:#334155}

    .kpi-card{background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:14px 18px;display:flex;align-items:center;gap:12px;box-shadow:0 1px 4px rgba(0,0,0,.04)}
    .kpi-card.green{border-left:4px solid #16a34a}
    .kpi-card.red{border-left:4px solid #dc2626}
    .kpi-card.dark{border-left:4px solid #1e293b}
    .kpi-card.blue{border-left:4px solid #1a56db}
    .kpi-icon{width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:.95rem;flex-shrink:0}
    .kpi-icon.green{background:#f0fdf4;color:#16a34a}
    .kpi-icon.red{background:#fef2f2;color:#dc2626}
    .kpi-icon.dark{background:#f1f5f9;color:#1e293b}
    .kpi-icon.blue{background:#eff6ff;color:#1a56db}
    .kpi-label{font-size:.58rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#94a3b8;margin-bottom:2px}
    .kpi-value{font-size:1.1rem;font-weight:800;color:#1e293b;line-height:1}

    @media print{
        .no-print{display:none!important}
        body *{visibility:hidden}
        #areaPDF,#areaPDF *{visibility:visible}
        #areaPDF{position:absolute;left:0;top:0;width:100%;display:block!important}
    }
</style>

<div class="page-wrap">

    {{-- CABEÇALHO --}}
    <div class="d-flex justify-content-between align-items-start mb-4 no-print">
        <div>
            <div style="font-size:.6rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">Contabilidade</div>
            <h4 class="fw-bold mb-0" style="color:#1e293b;font-size:1.25rem;">Caixa e Bancos</h4>
        </div>
        <div class="d-flex gap-2">
            <button onclick="exportarExcel()" class="top-btn success">
                <i class="bi bi-file-earmark-spreadsheet"></i> Excel
            </button>
            <button onclick="exportarPDF()" class="top-btn danger-outline">
                <i class="bi bi-file-earmark-pdf"></i> Imprimir / PDF
            </button>
        </div>
    </div>

    {{-- KPI CARDS --}}
    <div class="row g-3 mb-4 no-print">
        <div class="col-6 col-md-3">
            <div class="kpi-card blue">
                <div class="kpi-icon blue"><i class="bi bi-bank2"></i></div>
                <div>
                    <div class="kpi-label">Saldo Anterior</div>
                    <div class="kpi-value" id="kpi_saldo_ant">0,00 MT</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card green">
                <div class="kpi-icon green"><i class="bi bi-arrow-down-circle-fill"></i></div>
                <div>
                    <div class="kpi-label">Total Entradas</div>
                    <div class="kpi-value" id="kpi_entradas">0,00 MT</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card red">
                <div class="kpi-icon red"><i class="bi bi-arrow-up-circle-fill"></i></div>
                <div>
                    <div class="kpi-label">Total Saídas</div>
                    <div class="kpi-value" id="kpi_saidas">0,00 MT</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card dark">
                <div class="kpi-icon dark"><i class="bi bi-wallet2"></i></div>
                <div>
                    <div class="kpi-label">Saldo Actual</div>
                    <div class="kpi-value" id="kpi_saldo">0,00 MT</div>
                </div>
            </div>
        </div>
    </div>

    {{-- SECÇÃO 01: CONFIGURAÇÃO --}}
    <div class="section no-print">
        <div class="section-head">
            <span class="s-num">01</span>
            <span class="s-title">Configuração da Folha</span>
        </div>
        <div class="section-body">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="field-lbl">Nº da Folha</label>
                    <input type="text" id="nr_folha" class="form-control" placeholder="Ex: 13/2026" oninput="atualizarBadge()">
                </div>
                <div class="col-md-3">
                    <label class="field-lbl">Delegação / Local</label>
                    <input type="text" id="delegacao" class="form-control" placeholder="Ex: Delegação da Beira" oninput="atualizarBadge()">
                </div>
                <div class="col-md-2">
                    <label class="field-lbl">Tipo</label>
                    <select id="tipo_doc" class="form-select" onchange="atualizarBadge()">
                        <option value="CAIXA">Caixa</option>
                        <option value="BANCO">Banco</option>
                        <option value="FUNDO DE MANEIO">Fundo de Maneio</option>
                        <option value="TRANSFERÊNCIA">Transferência</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="field-lbl">Saldo Anterior (MT)</label>
                    <input type="number" id="saldo_anterior" class="form-control" placeholder="0,00" step="0.01" oninput="renderTabela()">
                </div>
                <div class="col-md-3">
                    <label class="field-lbl">Mês / Período</label>
                    <input type="month" id="periodo" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="field-lbl">Elaborado por</label>
                    <input type="text" id="elaborado_por" class="form-control" placeholder="Nome">
                </div>
                <div class="col-md-3">
                    <label class="field-lbl">Conferido por</label>
                    <input type="text" id="conferido_por" class="form-control" placeholder="Nome">
                </div>
            </div>
        </div>
    </div>

    {{-- TABS CAIXA --}}
    <div class="d-flex justify-content-between align-items-center mb-2 no-print">
        <div class="caixa-tabs">
            <button class="caixa-tab active" onclick="mudarCaixa(this,'caixa1')" data-caixa="caixa1">
                <i class="bi bi-cash-coin me-1"></i> <span id="tab_label_caixa1">Caixa 1</span>
            </button>
            <button class="caixa-tab" onclick="mudarCaixa(this,'caixa2')" data-caixa="caixa2">
                <i class="bi bi-cash-coin me-1"></i> <span id="tab_label_caixa2">Caixa 2</span>
            </button>
        </div>
        <button class="top-btn" onclick="editarNomeCaixa()">
            <i class="bi bi-pencil"></i> Editar Caixas
        </button>
    </div>

    {{-- TABELA --}}
    <div class="section">
        <div class="section-head" style="justify-content:space-between;">
            <div class="d-flex align-items-center gap-2">
                <span class="s-num">02</span>
                <span class="s-title" id="titulo_tabela">Movimentos — Caixa 1</span>
            </div>
            <span style="font-size:.7rem;color:#94a3b8;" id="badge_folha">—</span>
        </div>
        <div class="table-responsive">
            <table class="mov-table">
                <thead>
                    <tr>
                        <th style="width:100px">Data</th>
                        <th style="width:170px">Fornecedor</th>
                        <th>Descrição</th>
                        <th style="width:120px">Nº Documento</th>
                        <th style="width:120px;text-align:right">Entradas (MT)</th>
                        <th style="width:120px;text-align:right">Saídas (MT)</th>
                        <th style="width:120px;text-align:right">Saldo (MT)</th>
                        <th style="width:32px" class="no-print"></th>
                    </tr>
                </thead>
                <tbody id="tbody_movimentos"></tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="font-size:.68rem;letter-spacing:.5px;text-transform:uppercase;color:#64748b;">
                            <i class="bi bi-calculator me-1"></i> Total
                        </td>
                        <td style="text-align:right;color:#16a34a;" id="foot_entradas">0,00</td>
                        <td style="text-align:right;color:#dc2626;" id="foot_saidas">0,00</td>
                        <td style="text-align:right;" id="foot_saldo">0,00</td>
                        <td class="no-print"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <button class="btn-add-row no-print" onclick="adicionarLinha()">
            <i class="bi bi-plus-lg"></i> Adicionar Linha
        </button>
    </div>

</div>

{{-- ÁREA PDF --}}
<div id="areaPDF" style="display:none;font-family:Arial,sans-serif;font-size:10px;color:#111;padding:20px 24px;">

    {{-- TOPO: resumo + logo --}}
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;">
        <div style="min-width:200px;">
            <table style="border-collapse:collapse;font-size:10px;">
                <tr><td colspan="2" style="font-weight:400;font-size:11px;padding:2px 6px;background:#f1f1f1;border:1px solid #222121;">RESUMO CAIXA E BANCO</td></tr>
                <tr>
                    <td style="padding:2px 6px;border:1px solid #222121;border-top:none;">Saldo Anterior</td>
                    <td style="padding:2px 10px;border:1px solid #222121;border-top:none;border-left:none;text-align:right;font-weight:700;" id="pdf_res_saldo_ant">0,00</td>
                </tr>
                <tr>
                    <td style="padding:2px 6px;border:1px solid #222121;border-top:none;">Saldo Actual</td>
                    <td style="padding:2px 10px;border:1px solid #222121;border-top:none;border-left:none;text-align:right;font-weight:700;" id="pdf_res_saldo_act">0,00</td>
                </tr>
                <tr><td colspan="2" style="font-weight:800;font-size:11px;padding:2px 6px;background:#f1f1f1;border:1px solid #222121;border-top:none;" id="pdf_res_tipo_label">RESUMO CAIXA</td></tr>
                <tr>
                    <td style="padding:2px 6px;border:1px solid #222121;border-top:none;">Saldo Anterior</td>
                    <td style="padding:2px 10px;border:1px solid #222121;border-top:none;border-left:none;text-align:right;font-weight:700;" id="pdf_res_saldo_ant2">0,00</td>
                </tr>
                <tr>
                    <td style="padding:2px 6px;border:1px solid #222121;border-top:none;">Entradas</td>
                    <td style="padding:2px 10px;border:1px solid #222121;border-top:none;border-left:none;text-align:right;font-weight:700;" id="pdf_res_entradas">0,00</td>
                </tr>
                <tr>
                    <td style="padding:2px 6px;border:1px solid #222121;border-top:none;">Saídas</td>
                    <td style="padding:2px 10px;border:1px solid #222121;border-top:none;border-left:none;text-align:right;font-weight:700;" id="pdf_res_saidas">0,00</td>
                </tr>
                <tr>
                    <td style="padding:2px 6px;border:1px solid #222121;border-top:none;">Saldo Actual</td>
                    <td style="padding:2px 10px;border:1px solid #222121;border-top:none;border-left:none;text-align:right;font-weight:800;" id="pdf_res_saldo_act2">0,00</td>
                </tr>
            </table>
        </div>
        <div style="text-align:right;display:flex;align-items:center;gap:14px;">
            <img src="{{ asset('images/bymozelogo.png') }}" style="height:120px;width:auto;" alt="FEM">
        </div>
    </div>

    {{-- TÍTULO --}}
    <div style="text-align:left;font-weight:600;font-size:11px;margin:10px 0 8px;text-transform:uppercase;" id="pdf_titulo_completo">
        FOLHA DE CAIXA N°— DE —
    </div>

    {{-- TABELA PRINCIPAL --}}
    <table style="width:100%;border-collapse:collapse;font-size:9.5px;">
        <thead>
            <tr style="background:#333;color:#222121;">
                <th style="padding:5px 6px;text-align:left;border:1px solid #222121;width:75px;">Data</th>
                <th style="padding:5px 6px;text-align:left;border:1px solid #222121;width:140px;">Fornecedor</th>
                <th style="padding:5px 6px;text-align:left;border:1px solid #222121;">Descrição</th>
                <th style="padding:5px 6px;text-align:left;border:1px solid #222121;width:100px;">Nº Documento</th>
                <th style="padding:5px 6px;text-align:right;border:1px solid #222121;width:90px;">Entradas</th>
                <th style="padding:5px 6px;text-align:right;border:1px solid #222121;width:90px;">Saídas</th>
                <th style="padding:5px 6px;text-align:right;border:1px solid #222121;width:90px;">Saldo</th>
            </tr>
        </thead>
        <tbody id="pdf_tbody"></tbody>
        <tfoot>
            <tr style="font-weight:800;background:#f5f5f5;">
                <td colspan="4" style="padding:5px 6px;text-align:right;border:1px solid #222121;">Total</td>
                <td style="padding:5px 8px;text-align:right;border:1px solid #222121;" id="pdf_total_entradas">0,00</td>
                <td style="padding:5px 8px;text-align:right;border:1px solid #222121;" id="pdf_total_saidas">0,00</td>
                <td style="padding:5px 8px;text-align:right;border:1px solid #222121;"></td>
            </tr>
        </tfoot>
    </table>

    {{-- ASSINATURAS --}}
    <div style="margin-top:30px;display:flex;justify-content:space-between;align-items:flex-end;">
        <div style="text-align:center;">
            <div style="width:220px;border-top:1px solid #333;margin-bottom:4px;"></div>
            <div style="font-size:9px;color:#555;">Elaborado por: <span id="pdf_elaborado" style="font-weight:600;">_______________</span></div>
        </div>
        <div style="text-align:center;">
            <div style="width:220px;border-top:1px solid #333;margin-bottom:4px;"></div>
            <div style="font-size:9px;color:#555;">Conferido por: <span id="pdf_conferido" style="font-weight:600;">_______________</span></div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
let caixaActual = 'caixa1';
let dados = { caixa1: [], caixa2: [] };
let nomesCaixas = { caixa1: 'Caixa 1', caixa2: 'Caixa 2' };
const TOTAL_LINHAS = 33; // igual ao PDF original

$(document).ready(function() {
    const hoje = new Date();
    $('#periodo').val(hoje.toISOString().slice(0,7));
    for (let i = 0; i < TOTAL_LINHAS; i++) {
        dados.caixa1.push(linhaVazia());
        dados.caixa2.push(linhaVazia());
    }
    renderTabela();
    atualizarBadge();
});

function linhaVazia() {
    return { data:'', fornecedor:'', descricao:'', nr_doc:'', entrada:'', saida:'' };
}

function getSaldoAnterior() {
    return parseFloat($('#saldo_anterior').val()) || 0;
}

function renderTabela() {
    const tbody = $('#tbody_movimentos');
    tbody.empty();
    let saldo = getSaldoAnterior();
    let totE = 0, totS = 0;

    dados[caixaActual].forEach((l, idx) => {
        const ent = parseFloat(l.entrada) || 0;
        const sai = parseFloat(l.saida)   || 0;
        saldo += ent - sai;
        totE  += ent;
        totS  += sai;

        const saldoCls = ent === 0 && sai === 0
            ? 'saldo-zero'
            : saldo >= 0 ? 'saldo-pos' : 'saldo-neg';
        const saldoTxt = (ent > 0 || sai > 0) ? fmt(Math.abs(saldo)) + (saldo < 0 ? ' -' : '') : fmt(getSaldoAnterior() + (totE - totS));

        tbody.append(`<tr data-idx="${idx}">
            <td><input class="cell-input" type="date" value="${l.data}" onchange="editar(${idx},'data',this.value)"></td>
            <td><input class="cell-input" type="text" value="${escHtml(l.fornecedor)}" placeholder="Fornecedor" onchange="editar(${idx},'fornecedor',this.value)"></td>
            <td><input class="cell-input" type="text" value="${escHtml(l.descricao)}" placeholder="Descrição" onchange="editar(${idx},'descricao',this.value)"></td>
            <td><input class="cell-input" type="text" value="${escHtml(l.nr_doc)}" placeholder="Nº doc" onchange="editar(${idx},'nr_doc',this.value)"></td>
            <td><input class="cell-input entrada" type="number" value="${l.entrada}" placeholder="" step="0.01" min="0" style="text-align:right" oninput="editar(${idx},'entrada',this.value)"></td>
            <td><input class="cell-input saida" type="number" value="${l.saida}" placeholder="" step="0.01" min="0" style="text-align:right" oninput="editar(${idx},'saida',this.value)"></td>
            <td class="${saldoCls}" style="font-size:.78rem;padding-right:8px;">
                ${(ent > 0 || sai > 0) ? fmt(saldo) + ' MT' : (getSaldoAnterior() + totE - totS > 0 ? fmt(getSaldoAnterior() + totE - totS) : '')}
            </td>
            <td class="no-print" style="text-align:center;">
                <button class="btn-remove" onclick="removerLinha(${idx})"><i class="bi bi-trash3"></i></button>
            </td>
        </tr>`);
    });

    // Rodapé
    $('#foot_entradas').text(fmt(totE));
    $('#foot_saidas').text(fmt(totS));
    const sf = getSaldoAnterior() + totE - totS;
    $('#foot_saldo').text(fmt(sf)).css('color', sf >= 0 ? '#16a34a' : '#dc2626');

    // KPIs
    $('#kpi_saldo_ant').text(fmt(getSaldoAnterior()) + ' MT');
    $('#kpi_entradas').text(fmt(totE) + ' MT');
    $('#kpi_saidas').text(fmt(totS) + ' MT');
    $('#kpi_saldo').text(fmt(sf) + ' MT').css('color', sf >= 0 ? '#16a34a' : '#dc2626');
}

function editar(idx, campo, valor) {
    dados[caixaActual][idx][campo] = valor;
    renderTabela();
}

function adicionarLinha() {
    dados[caixaActual].push(linhaVazia());
    renderTabela();
    setTimeout(() => {
        document.getElementById('tbody_movimentos').lastElementChild
            ?.scrollIntoView({ behavior:'smooth', block:'center' });
    }, 80);
}

function removerLinha(idx) {
    if (dados[caixaActual].length <= 1) return;
    dados[caixaActual].splice(idx, 1);
    renderTabela();
}

function mudarCaixa(btn, caixa) {
    caixaActual = caixa;
    $('.caixa-tab').removeClass('active');
    $(btn).addClass('active');
    $('#titulo_tabela').text('Movimentos — ' + nomesCaixas[caixa]);
    renderTabela();
}

function editarNomeCaixa() {
    const n1 = prompt('Nome da Caixa 1:', nomesCaixas.caixa1);
    if (n1) { nomesCaixas.caixa1 = n1; $('#tab_label_caixa1').text(n1); }
    const n2 = prompt('Nome da Caixa 2:', nomesCaixas.caixa2);
    if (n2) { nomesCaixas.caixa2 = n2; $('#tab_label_caixa2').text(n2); }
    $('#titulo_tabela').text('Movimentos — ' + nomesCaixas[caixaActual]);
}

function atualizarBadge() {
    const nr  = $('#nr_folha').val() || '—';
    const del = $('#delegacao').val() || '—';
    const tipo = $('#tipo_doc').val() || 'CAIXA';
    $('#badge_folha').text(`FOLHA ${tipo} Nº${nr} · ${del}`);
}

function fmt(val) {
    return Number(val).toLocaleString('pt-MZ', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function escHtml(str) {
    return (str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function exportarPDF() {
    const nr    = $('#nr_folha').val() || '—';
    const del   = $('#delegacao').val() || 'FÁBRICA DE EXPLOSIVOS MOÇAMBIQUE, LDA';
    const tipo  = $('#tipo_doc').val() || 'CAIXA';
    const saldoAnt = getSaldoAnterior();

    let totE = 0, totS = 0;
    dados[caixaActual].forEach(l => {
        totE += parseFloat(l.entrada) || 0;
        totS += parseFloat(l.saida)   || 0;
    });
    const saldoAct = saldoAnt + totE - totS;

    // Resumo
    $('#pdf_res_saldo_ant').text(fmt(saldoAnt));
    $('#pdf_res_saldo_act').text(fmt(saldoAct));
    $('#pdf_res_tipo_label').text('RESUMO ' + tipo);
    $('#pdf_res_saldo_ant2').text(fmt(saldoAnt));
    $('#pdf_res_entradas').text(fmt(totE));
    $('#pdf_res_saidas').text(fmt(totS));
    $('#pdf_res_saldo_act2').text(fmt(saldoAct));

    // Título
    $('#pdf_titulo_completo').text(`FOLHA DE ${tipo} N°${nr}/2026 DE Delegação da Beira -  ${del}`);

    // Elaborado / Conferido
    $('#pdf_elaborado').text($('#elaborado_por').val() || '________________________________');
    $('#pdf_conferido').text($('#conferido_por').val() || '________________________________');

    // Tabela
    const pdfTbody = $('#pdf_tbody');
    pdfTbody.empty();
    let saldo = saldoAnt;

    dados[caixaActual].forEach((l, i) => {
        const ent = parseFloat(l.entrada) || 0;
        const sai = parseFloat(l.saida)   || 0;
        saldo += ent - sai;
        const even = i % 2 === 0;
        const bg   = even ? '' : 'background:#f9f9f9;';
        const dataFmt = l.data ? new Date(l.data + 'T00:00:00').toLocaleDateString('pt-MZ') : '';
        const saldoTxt = (ent > 0 || sai > 0) ? fmt(saldo) : fmt(saldoAnt + totE - totS);

        pdfTbody.append(`<tr style="${bg}">
            <td style="padding:3px 5px;border:1px solid #353232;">${dataFmt}</td>
            <td style="padding:3px 5px;border:1px solid #353232;border-left:none;">${escHtml(l.fornecedor)}</td>
            <td style="padding:3px 5px;border:1px solid #353232;border-left:none;">${escHtml(l.descricao)}</td>
            <td style="padding:3px 5px;border:1px solid #353232;border-left:none;">${escHtml(l.nr_doc)}</td>
            <td style="padding:3px 7px;border:1px solid #353232;border-left:none;text-align:right;">${ent ? fmt(ent) : ''}</td>
            <td style="padding:3px 7px;border:1px solid #353232;border-left:none;text-align:right;">${sai ? fmt(sai) : ''}</td>
            <td style="padding:3px 7px;border:1px solid #353232;border-left:none;text-align:right;">${saldoTxt}</td>
        </tr>`);
    });

    $('#pdf_total_entradas').text(fmt(totE));
    $('#pdf_total_saidas').text(fmt(totS));

    document.getElementById('areaPDF').style.display = 'block';
    window.print();
    document.getElementById('areaPDF').style.display = 'none';
}

function exportarExcel() {
    const saldoAnt = getSaldoAnterior();
    const wsData = [['Data','Fornecedor','Descrição','Nº Documento','Entradas (MT)','Saídas (MT)','Saldo (MT)']];
    let saldo = saldoAnt;
    dados[caixaActual].forEach(l => {
        const ent = parseFloat(l.entrada) || 0;
        const sai = parseFloat(l.saida)   || 0;
        saldo += ent - sai;
        wsData.push([l.data, l.fornecedor, l.descricao, l.nr_doc, ent||'', sai||'', saldo]);
    });
    const ws = XLSX.utils.aoa_to_sheet(wsData);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, nomesCaixas[caixaActual]);
    const nr = $('#nr_folha').val() || 'sem_nr';
    XLSX.writeFile(wb, `folha_caixa_${nr}_${new Date().toISOString().slice(0,10)}.xlsx`);
}
</script>

</x-app-layout>
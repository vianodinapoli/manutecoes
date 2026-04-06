<x-app-layout>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    /* ── Design System ── */
    .page-wrap{padding:32px 24px 64px}
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
    .top-btn.warn{background:#fff;border-color:#fde68a;color:#d97706}
    .top-btn.warn:hover{background:#fefce8}

    /* ── Secções ── */
    .section{background:#fff;border:1px solid #e2e8f0;border-radius:12px;margin-bottom:16px;overflow:hidden}
    .section-head{display:flex;align-items:center;gap:10px;padding:12px 20px;background:#f8fafc;border-bottom:1px solid #e2e8f0}
    .s-num{font-size:.6rem;font-weight:800;letter-spacing:1.5px;color:#94a3b8;text-transform:uppercase;background:#e2e8f0;border-radius:4px;padding:2px 7px}
    .s-title{font-size:.8rem;font-weight:700;color:#334155;letter-spacing:.3px}
    .section-body{padding:20px 24px}

    /* ── Tabs Caixa ── */
    .caixa-tabs{display:flex;gap:8px;margin-bottom:20px}
    .caixa-tab{padding:7px 20px;border-radius:8px;font-size:.78rem;font-weight:700;cursor:pointer;border:1px solid #e2e8f0;background:#fff;color:#64748b;transition:all .15s}
    .caixa-tab.active{background:#1e293b;border-color:#1e293b;color:#fff}
    .caixa-tab:hover:not(.active){background:#f8fafc;border-color:#94a3b8}

    /* ── Tabela de movimentos ── */
    .mov-table{width:100%;border-collapse:collapse}
    .mov-table thead th{font-size:.6rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#94a3b8;background:#f8fafc;border-bottom:1px solid #e2e8f0;padding:9px 10px;white-space:nowrap}
    .mov-table tbody td{padding:5px 6px;border-bottom:1px solid #f1f5f9;vertical-align:middle}
    .mov-table tbody tr:last-child td{border-bottom:none}
    .mov-table tbody tr:hover td{background:#f8fafc}
    .mov-table tfoot td{padding:10px 10px;font-size:.8rem;font-weight:700;color:#1e293b;background:#f1f5f9;border-top:2px solid #e2e8f0}

    /* ── Inputs dentro da tabela ── */
    .cell-input{width:100%;border:1px solid transparent;border-radius:6px;padding:5px 8px;font-size:.78rem;color:#334155;background:transparent;transition:all .15s}
    .cell-input:focus{border-color:#64748b;background:#fff;box-shadow:0 0 0 2px rgba(100,116,139,.1);outline:none}
    .cell-input.entrada{color:#16a34a;font-weight:600}
    .cell-input.saida{color:#dc2626;font-weight:600}
    .cell-input:hover{background:#f8fafc}

    /* ── Saldo ── */
    .saldo-pos{color:#16a34a;font-weight:700;font-size:.82rem}
    .saldo-neg{color:#dc2626;font-weight:700;font-size:.82rem}

    /* ── Totais box ── */
    .totais-strip{display:flex;gap:16px;padding:14px 20px;background:#f8fafc;border-top:1px solid #e2e8f0;flex-wrap:wrap}
    .totais-item{display:flex;flex-direction:column;gap:2px}
    .totais-item .t-lbl{font-size:.58rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#94a3b8}
    .totais-item .t-val{font-size:.9rem;font-weight:800;color:#1e293b}
    .totais-item .t-val.green{color:#16a34a}
    .totais-item .t-val.red{color:#dc2626}

    /* ── Botão remover linha ── */
    .btn-remove{width:24px;height:24px;border-radius:6px;border:1px solid #fecaca;background:#fff;color:#dc2626;display:inline-flex;align-items:center;justify-content:center;font-size:.75rem;cursor:pointer;transition:all .15s}
    .btn-remove:hover{background:#fef2f2}
    .btn-add-row{display:inline-flex;align-items:center;gap:5px;padding:5px 14px;border-radius:7px;font-size:.75rem;font-weight:600;border:1px dashed #cbd5e1;background:#f8fafc;color:#64748b;cursor:pointer;transition:all .15s;margin:10px 20px}
    .btn-add-row:hover{border-color:#94a3b8;background:#f1f5f9;color:#334155}

    /* ── KPI cards ── */
    .kpi-card{background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:16px 20px;display:flex;align-items:center;gap:14px;box-shadow:0 1px 4px rgba(0,0,0,.04)}
    .kpi-card.green{border-left:4px solid #16a34a}
    .kpi-card.red{border-left:4px solid #dc2626}
    .kpi-card.dark{border-left:4px solid #1e293b}
    .kpi-icon{width:36px;height:36px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0}
    .kpi-icon.green{background:#f0fdf4;color:#16a34a}
    .kpi-icon.red{background:#fef2f2;color:#dc2626}
    .kpi-icon.dark{background:#f1f5f9;color:#1e293b}
    .kpi-label{font-size:.6rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#94a3b8;margin-bottom:2px}
    .kpi-value{font-size:1.2rem;font-weight:800;color:#1e293b;line-height:1}

    /* ── Nr documento badge ── */
    .doc-badge{display:inline-flex;align-items:center;padding:2px 8px;border-radius:5px;font-size:.68rem;font-weight:700;background:#f1f5f9;color:#475569;border:1px solid #e2e8f0}

    /* ── Print ── */
    @media print{
        .no-print{display:none!important}
        .print-only{display:block!important}
        body{font-size:10pt}
        .cell-input{border:none!important;background:transparent!important;padding:3px 4px}
        body * { visibility: hidden; }
        #areaPDF, #areaPDF * { visibility: visible; }
        #areaPDF { position: absolute; left: 0; top: 0; width: 100%; display: block !important; }
    }
    .print-only{display:none}
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
                <i class="bi bi-file-earmark-pdf"></i> PDF
            </button>
        </div>
    </div>

    {{-- KPI CARDS --}}
    <div class="row g-3 mb-4 no-print">
        <div class="col-md-4">
            <div class="kpi-card green">
                <div class="kpi-icon green"><i class="bi bi-arrow-down-circle-fill"></i></div>
                <div>
                    <div class="kpi-label">Total Entradas</div>
                    <div class="kpi-value" id="kpi_entradas">0,00 MT</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card red">
                <div class="kpi-icon red"><i class="bi bi-arrow-up-circle-fill"></i></div>
                <div>
                    <div class="kpi-label">Total Saídas</div>
                    <div class="kpi-value" id="kpi_saidas">0,00 MT</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card dark">
                <div class="kpi-icon dark"><i class="bi bi-bank"></i></div>
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
            <span class="s-title">Configuração do Documento</span>
        </div>
        <div class="section-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="field-lbl">Tipo de Documento</label>
                    <select id="tipo_doc" class="form-select" onchange="atualizarCabecalho()">
                        <option value="caixa">Caixa</option>
                        <option value="banco">Banco</option>
                        <option value="transferencia">Transferência</option>
                        <option value="fundo_maneio">Fundo de Maneio</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="field-lbl">Número do Documento</label>
                    <input type="text" id="nr_doc" class="form-control" placeholder="Ex: CX-001" oninput="atualizarCabecalho()">
                </div>
                <div class="col-md-2">
                    <label class="field-lbl">Data</label>
                    <input type="date" id="data_doc" class="form-control" oninput="atualizarCabecalho()">
                </div>
                <div class="col-md-3">
                    <label class="field-lbl">Elaborado por</label>
                    <input type="text" id="elaborado_por" class="form-control" placeholder="Nome do responsável">
                </div>
                <div class="col-md-2">
                    <label class="field-lbl">Conferido por</label>
                    <input type="text" id="conferido_por" class="form-control" placeholder="Nome">
                </div>
            </div>
        </div>
    </div>

    {{-- SECÇÃO 02: TABS CAIXA --}}
    <div class="d-flex justify-content-between align-items-center mb-2 no-print">
        <div class="caixa-tabs" id="caixaTabs">
            <button class="caixa-tab active" onclick="mudarCaixa(this, 'caixa1')" data-caixa="caixa1">
                <i class="bi bi-cash-coin me-1"></i> Caixa 1
            </button>
            <button class="caixa-tab" onclick="mudarCaixa(this, 'caixa2')" data-caixa="caixa2">
                <i class="bi bi-cash-coin me-1"></i> Caixa 2
            </button>
        </div>
        <div class="d-flex gap-2">
            <button class="top-btn" onclick="editarNomeCaixa()" title="Editar nome das caixas">
                <i class="bi bi-pencil"></i> Editar Caixas
            </button>
        </div>
    </div>

    {{-- SECÇÃO 02: TABELA DE MOVIMENTOS --}}
    <div class="section">
        <div class="section-head d-flex justify-content-between align-items-center" style="padding:12px 20px;">
            <div class="d-flex align-items-center gap-10">
                <span class="s-num">02</span>
                <span class="s-title" id="titulo_tabela">Movimentos — Caixa 1</span>
            </div>
            <div class="d-flex align-items-center gap-2 no-print" style="gap:8px;">
                <span class="doc-badge" id="badge_doc">—</span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="mov-table" id="tabelaMovimentos">
                <thead>
                    <tr>
                        <th style="width:110px">Data</th>
                        <th style="width:160px">Fornecedor</th>
                        <th>Descrição</th>
                        <th style="width:110px">Nº Documento</th>
                        <th style="width:120px" class="text-end">Entradas (MT)</th>
                        <th style="width:120px" class="text-end">Saídas (MT)</th>
                        <th style="width:120px" class="text-end">Saldo (MT)</th>
                        <th style="width:36px" class="no-print"></th>
                    </tr>
                </thead>
                <tbody id="tbody_movimentos">
                    {{-- linhas geradas por JS --}}
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="font-size:.72rem;letter-spacing:.5px;text-transform:uppercase;color:#64748b;">
                            <i class="bi bi-calculator me-1"></i> TOTAIS
                        </td>
                        <td class="text-end" style="color:#16a34a;" id="total_entradas_foot">0,00</td>
                        <td class="text-end" style="color:#dc2626;" id="total_saidas_foot">0,00</td>
                        <td class="text-end" id="saldo_final_foot">0,00</td>
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

{{-- ÁREA DE IMPRESSÃO PDF --}}
<div id="areaPDF" style="display:none;font-family:'DejaVu Sans',sans-serif;padding:30px;font-size:11px;color:#1e293b;">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;border-bottom:3px solid #c60a1a;padding-bottom:14px;margin-bottom:20px;">
        <div style="display:flex;align-items:center;gap:12px;">
            <img src="{{ asset('images/bymozelogo.png') }}" style="height:50px;width:auto;" alt="Logo">
            <div>
                <div style="font-size:13px;font-weight:bold;color:#c60a1a;">Fábrica de Explosivos de Moçambique</div>
                <div style="font-size:9px;color:#555;line-height:1.6;">
                    Contribuinte Nº 400019029<br>
                    Av. Samora Machel Nº — &nbsp;|&nbsp; Telef. +258 21 745 86/03 &nbsp;|&nbsp; FAX. +258 21 745 802
                </div>
            </div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:16px;font-weight:bold;color:#c60a1a;" id="pdf_titulo_doc">CAIXA</div>
            <div style="font-size:11px;color:#475569;margin-top:3px;" id="pdf_nr_doc">Nº —</div>
            <div style="font-size:10px;color:#94a3b8;margin-top:2px;" id="pdf_data_doc">Data: —</div>
        </div>
    </div>

    <table style="width:100%;border-collapse:collapse;margin-bottom:20px;">
        <thead>
            <tr style="background:#c60a1a;color:#fff;">
                <th style="padding:7px 10px;font-size:9px;text-transform:uppercase;letter-spacing:.8px;text-align:left;">Data</th>
                <th style="padding:7px 10px;font-size:9px;text-transform:uppercase;letter-spacing:.8px;text-align:left;">Fornecedor</th>
                <th style="padding:7px 10px;font-size:9px;text-transform:uppercase;letter-spacing:.8px;text-align:left;">Descrição</th>
                <th style="padding:7px 10px;font-size:9px;text-transform:uppercase;letter-spacing:.8px;text-align:left;">Nº Doc</th>
                <th style="padding:7px 10px;font-size:9px;text-transform:uppercase;letter-spacing:.8px;text-align:right;">Entradas</th>
                <th style="padding:7px 10px;font-size:9px;text-transform:uppercase;letter-spacing:.8px;text-align:right;">Saídas</th>
                <th style="padding:7px 10px;font-size:9px;text-transform:uppercase;letter-spacing:.8px;text-align:right;">Saldo</th>
            </tr>
        </thead>
        <tbody id="pdf_tbody"></tbody>
        <tfoot>
            <tr style="background:#f1f5f9;font-weight:bold;border-top:2px solid #c60a1a;">
                <td colspan="4" style="padding:8px 10px;font-size:9px;letter-spacing:.8px;text-transform:uppercase;color:#64748b;">Total Geral</td>
                <td style="padding:8px 10px;text-align:right;color:#16a34a;" id="pdf_total_entradas">0,00</td>
                <td style="padding:8px 10px;text-align:right;color:#dc2626;" id="pdf_total_saidas">0,00</td>
                <td style="padding:8px 10px;text-align:right;font-weight:bold;" id="pdf_saldo_final">0,00</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top:40px;display:flex;justify-content:space-between;">
        <div style="text-align:center;width:200px;">
            <div style="border-top:1px solid #1e293b;margin-bottom:6px;"></div>
            <div style="font-size:9px;color:#64748b;text-transform:uppercase;letter-spacing:.8px;">Elaborado por</div>
            <div style="font-size:10px;font-weight:600;color:#1e293b;margin-top:3px;" id="pdf_elaborado">—</div>
        </div>
        <div style="text-align:center;width:200px;">
            <div style="border-top:1px solid #1e293b;margin-bottom:6px;"></div>
            <div style="font-size:9px;color:#64748b;text-transform:uppercase;letter-spacing:.8px;">Conferido por</div>
            <div style="font-size:10px;font-weight:600;color:#1e293b;margin-top:3px;" id="pdf_conferido">—</div>
        </div>
        <div style="text-align:right;font-size:9px;color:#94a3b8;">
            Documento gerado em {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
// ── Estado --
let caixaActual = 'caixa1';
let dados = { caixa1: [], caixa2: [] };
let nomesCaixas = { caixa1: 'Caixa 1', caixa2: 'Caixa 2' };

// ── Init ──
$(document).ready(function() {
    $('#data_doc').val(new Date().toISOString().split('T')[0]);
    // Inicia com 10 linhas em branco
    for (let i = 0; i < 10; i++) {
        dados.caixa1.push(linhaVazia());
        dados.caixa2.push(linhaVazia());
    }
    renderTabela();
    atualizarCabecalho();
});

function linhaVazia() {
    return { data: '', fornecedor: '', descricao: '', nr_doc: '', entrada: '', saida: '' };
}

// ── Render tabela ──
function renderTabela() {
    const tbody = $('#tbody_movimentos');
    tbody.empty();
    let saldoAcum = 0;
    let totEntradas = 0, totSaidas = 0;

    dados[caixaActual].forEach((linha, idx) => {
        const ent  = parseFloat(linha.entrada) || 0;
        const sai  = parseFloat(linha.saida)   || 0;
        saldoAcum += ent - sai;
        totEntradas += ent;
        totSaidas   += sai;

        const saldoCls = saldoAcum >= 0 ? 'saldo-pos' : 'saldo-neg';
        const saldoTxt = saldoAcum !== 0 ? fmt(saldoAcum) : '';

        tbody.append(`
        <tr data-idx="${idx}">
            <td><input class="cell-input" type="date" value="${linha.data}" onchange="editarLinha(${idx},'data',this.value)"></td>
            <td><input class="cell-input" type="text" value="${linha.fornecedor}" placeholder="Fornecedor" onchange="editarLinha(${idx},'fornecedor',this.value)"></td>
            <td><input class="cell-input" type="text" value="${linha.descricao}" placeholder="Descrição do movimento" onchange="editarLinha(${idx},'descricao',this.value)"></td>
            <td><input class="cell-input" type="text" value="${linha.nr_doc}" placeholder="Nº" onchange="editarLinha(${idx},'nr_doc',this.value)"></td>
            <td><input class="cell-input entrada text-end" type="number" value="${linha.entrada}" placeholder="0,00" step="0.01" min="0" oninput="editarLinha(${idx},'entrada',this.value)" style="text-align:right;"></td>
            <td><input class="cell-input saida text-end" type="number" value="${linha.saida}" placeholder="0,00" step="0.01" min="0" oninput="editarLinha(${idx},'saida',this.value)" style="text-align:right;"></td>
            <td class="text-end ${saldoCls}" id="saldo_linha_${idx}">${saldoTxt ? saldoTxt + ' MT' : ''}</td>
            <td class="no-print text-center">
                <button class="btn-remove" onclick="removerLinha(${idx})" title="Remover linha">
                    <i class="bi bi-trash3"></i>
                </button>
            </td>
        </tr>`);
    });

    // Totais rodapé
    $('#total_entradas_foot').text(fmt(totEntradas));
    $('#total_saidas_foot').text(fmt(totSaidas));
    const saldoFinal = totEntradas - totSaidas;
    $('#saldo_final_foot').text(fmt(saldoFinal)).css('color', saldoFinal >= 0 ? '#16a34a' : '#dc2626');

    // KPIs
    $('#kpi_entradas').text(fmt(totEntradas) + ' MT');
    $('#kpi_saidas').text(fmt(totSaidas) + ' MT');
    $('#kpi_saldo').text(fmt(saldoFinal) + ' MT').css('color', saldoFinal >= 0 ? '#16a34a' : '#dc2626');
}

function editarLinha(idx, campo, valor) {
    dados[caixaActual][idx][campo] = valor;
    renderTabela();
}

function adicionarLinha() {
    dados[caixaActual].push(linhaVazia());
    renderTabela();
    // Scroll para o fim
    setTimeout(() => {
        const tbody = document.getElementById('tbody_movimentos');
        tbody.lastElementChild?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }, 100);
}

function removerLinha(idx) {
    if (dados[caixaActual].length <= 1) return;
    dados[caixaActual].splice(idx, 1);
    renderTabela();
}

// ── Tabs caixa ──
function mudarCaixa(btn, caixa) {
    caixaActual = caixa;
    $('.caixa-tab').removeClass('active');
    $(btn).addClass('active');
    $('#titulo_tabela').text('Movimentos — ' + nomesCaixas[caixa]);
    renderTabela();
}

function editarNomeCaixa() {
    const n1 = prompt('Nome da Caixa 1:', nomesCaixas.caixa1);
    if (n1) nomesCaixas.caixa1 = n1;
    const n2 = prompt('Nome da Caixa 2:', nomesCaixas.caixa2);
    if (n2) nomesCaixas.caixa2 = n2;
    $('[data-caixa="caixa1"]').html('<i class="bi bi-cash-coin me-1"></i> ' + nomesCaixas.caixa1);
    $('[data-caixa="caixa2"]').html('<i class="bi bi-cash-coin me-1"></i> ' + nomesCaixas.caixa2);
    $('#titulo_tabela').text('Movimentos — ' + nomesCaixas[caixaActual]);
}

// ── Cabeçalho dinâmico ──
function atualizarCabecalho() {
    const tipo = $('#tipo_doc').val();
    const nr   = $('#nr_doc').val() || '—';
    const data = $('#data_doc').val();
    const badge = tipo.toUpperCase() + (nr !== '—' ? ' · ' + nr : '');
    $('#badge_doc').text(badge);
}

// ── Formatação ──
function fmt(val) {
    return val.toLocaleString('pt-MZ', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// ── Export PDF (via print) ──
function exportarPDF() {
    // Preenche área PDF
    const tipo = $('#tipo_doc').val().toUpperCase();
    const nr   = $('#nr_doc').val() || '—';
    const data = $('#data_doc').val()
        ? new Date($('#data_doc').val()).toLocaleDateString('pt-MZ') : '—';

    $('#pdf_titulo_doc').text(tipo + ' — ' + nomesCaixas[caixaActual]);
    $('#pdf_nr_doc').text('Nº ' + nr);
    $('#pdf_data_doc').text('Data: ' + data);
    $('#pdf_elaborado').text($('#elaborado_por').val() || '—');
    $('#pdf_conferido').text($('#conferido_por').val() || '—');

    // Preenche tabela PDF
    const pdfTbody = $('#pdf_tbody');
    pdfTbody.empty();
    let saldoAcum = 0, totE = 0, totS = 0;

    dados[caixaActual].forEach((l, i) => {
        const ent = parseFloat(l.entrada) || 0;
        const sai = parseFloat(l.saida)   || 0;
        saldoAcum += ent - sai;
        totE += ent; totS += sai;
        const even = i % 2 === 0;
        pdfTbody.append(`
        <tr style="${even ? 'background:#f9f9f9;' : ''}border-bottom:1px solid #e2e8f0;">
            <td style="padding:6px 10px;">${l.data ? new Date(l.data).toLocaleDateString('pt-MZ') : ''}</td>
            <td style="padding:6px 10px;">${l.fornecedor}</td>
            <td style="padding:6px 10px;">${l.descricao}</td>
            <td style="padding:6px 10px;">${l.nr_doc}</td>
            <td style="padding:6px 10px;text-align:right;color:#16a34a;font-weight:${ent?'600':'400'}">${ent ? fmt(ent) : ''}</td>
            <td style="padding:6px 10px;text-align:right;color:#dc2626;font-weight:${sai?'600':'400'}">${sai ? fmt(sai) : ''}</td>
            <td style="padding:6px 10px;text-align:right;font-weight:600;color:${saldoAcum>=0?'#16a34a':'#dc2626'}">${fmt(saldoAcum)}</td>
        </tr>`);
    });

    $('#pdf_total_entradas').text(fmt(totE));
    $('#pdf_total_saidas').text(fmt(totS));
    const sf = totE - totS;
    $('#pdf_saldo_final').text(fmt(sf)).css('color', sf >= 0 ? '#16a34a' : '#dc2626');

    // Print
    document.getElementById('areaPDF').style.display = 'block';
    window.print();
    document.getElementById('areaPDF').style.display = 'none';
}

// ── Export Excel ──
function exportarExcel() {
    const linhas = dados[caixaActual];
    const wsData = [['Data','Fornecedor','Descrição','Nº Documento','Entradas (MT)','Saídas (MT)','Saldo (MT)']];
    let saldoAcum = 0;
    linhas.forEach(l => {
        const ent = parseFloat(l.entrada) || 0;
        const sai = parseFloat(l.saida)   || 0;
        saldoAcum += ent - sai;
        wsData.push([l.data, l.fornecedor, l.descricao, l.nr_doc, ent || '', sai || '', saldoAcum]);
    });
    const ws = XLSX.utils.aoa_to_sheet(wsData);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, nomesCaixas[caixaActual]);
    const tipo = $('#tipo_doc').val();
    const nr   = $('#nr_doc').val() || 'sem_nr';
    XLSX.writeFile(wb, `caixa_${tipo}_${nr}_${new Date().toISOString().slice(0,10)}.xlsx`);
}
</script>

</x-app-layout>
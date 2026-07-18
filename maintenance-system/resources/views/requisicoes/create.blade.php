<x-app-layout>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    /* ── Cards ── */
    .form-card{background:#fff;border-radius:14px;border:1px solid #e9ecef;box-shadow:0 2px 8px rgba(0,0,0,.04);overflow:hidden;margin-bottom:20px}
    .form-card-header{padding:13px 20px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #e9ecef}
    .form-card-header.red{background:#c60a1a;border-bottom:none}
    .form-card-header.light{background:#f8f9fa}
    .form-card-header h6{margin:0;font-weight:700;font-size:.86rem;display:flex;align-items:center;gap:8px}
    .form-card-body{padding:20px 24px}

    /* ── Fornecedor info ── */
    .info-fornecedor{background:#fdf2f2;border:1px solid #fecaca;border-radius:10px;padding:14px 18px}
    .info-fornecedor .info-label{font-size:.6rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#adb5bd;margin-bottom:2px}
    .info-fornecedor .info-val{font-size:.82rem;font-weight:600;color:#1e293b}

    /* ── Tabela itens ── */
    #tabela_itens thead th{font-size:.67rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#868e96;border-bottom:1px solid #e9ecef;padding:8px 12px;white-space:nowrap;background:#f8f9fa}
    #tabela_itens tbody td{padding:8px 10px;vertical-align:middle;border-bottom:1px solid #f1f3f5;font-size:.82rem}
    #tabela_itens tbody tr:last-child td{border-bottom:none}
    #tabela_itens tbody tr:hover{background:#fdf5f5}

    /* ── Desconto badge na coluna ── */
    .disc-wrap{position:relative}
    .disc-wrap input{padding-right:22px}
    .disc-wrap::after{content:'%';position:absolute;right:8px;top:50%;transform:translateY(-50%);font-size:.7rem;font-weight:700;color:#94a3b8;pointer-events:none}

    /* ── Totais ── */
    .totais-box{background:#f8f9fa;border-radius:12px;padding:18px 20px;border:1px solid #e9ecef}
    .totais-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;font-size:.82rem}
    .totais-label{font-size:.68rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#6c757d}
    .totais-row.final{font-size:1rem;font-weight:800;margin-top:12px;padding-top:12px;border-top:2px solid #dee2e6}

    /* ── Botão guardar ── */
    .btn-guardar{background:#c60a1a;color:#fff;border:none;border-radius:10px;padding:11px 0;font-size:.88rem;font-weight:700;width:100%;margin-top:14px;transition:background .2s;display:flex;align-items:center;justify-content:center;gap:8px}
    .btn-guardar:hover{background:#a30816}
    .btn-guardar:disabled{background:#f87171;cursor:not-allowed}

    /* ── Toggle IVA ── */
    .form-check-input:checked{background-color:#c60a1a;border-color:#c60a1a}

    /* ── Impressão ── */
    @media print{
        body * { visibility: hidden; }
        #area_impressao, #area_impressao * { visibility: visible; }
        #area_impressao { position: absolute; left: 0; top: 0; width: 100%; display: block !important; }
    }
    #area_impressao { display: none; }
</style>

<div class="container-fluid py-4 px-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-file-earmark-plus me-2" style="color:#c60a1a;"></i>Nova Requisição de Compra
            </h4>
            <p class="text-muted small mb-0">Preencha os dados do fornecedor e adicione os itens</p>
        </div>
        <a href="{{ route('requisicoes.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Voltar
        </a>
    </div>

    {{-- ALERTA IMPORTAÇÃO --}}
    @if(isset($compra) && $compra)
    <div class="alert alert-dismissible fade show border-0 mb-4 py-3"
         style="border-radius:10px;background:#fdf2f2;border-left:4px solid #c60a1a !important;color:#7f1d1d;">
        <i class="bi bi-info-circle-fill me-2" style="color:#c60a1a;"></i>
        <strong>Itens importados automaticamente</strong> do Pedido de Compra <strong>#{{ $compra->id }}</strong>.
        Preencha apenas os <strong>preços unitários</strong> para finalizar.
        <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- FORNECEDOR --}}
    <div class="form-card">
        <div class="form-card-header red">
            <h6 class="text-white"><i class="bi bi-building"></i> Fornecedor</h6>
        </div>
        <div class="form-card-body">
            <div class="row g-3 align-items-start">
                <div class="col-md-7">
                    <label class="small fw-bold text-muted mb-1" style="font-size:.68rem;letter-spacing:.8px;text-transform:uppercase;">Selecionar Fornecedor</label>
                    <select id="fornecedor_id" name="supplier_id" class="form-select" required
                            style="border-radius:8px;border:1px solid #dee2e6;font-size:.85rem;background:#f8f9fa;">
                        <option value="" selected disabled>Escolha um fornecedor...</option>
                        @foreach($fornecedores as $f)
                        <option value="{{ $f->id }}"
                                data-name="{{ $f->name }}"
                                data-nuit="{{ $f->nuit }}"
                                data-address="{{ $f->address }}"
                                data-contact="{{ $f->contact }}">
                            {{ $f->code }} — {{ $f->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <div class="info-fornecedor">
                        <div class="row g-2">
                            <div class="col-4">
                                <div class="info-label">NUIT</div>
                                <div class="info-val" id="info_nuit">—</div>
                            </div>
                            <div class="col-4">
                                <div class="info-label">Endereço</div>
                                <div class="info-val" id="info_endereco">—</div>
                            </div>
                            <div class="col-4">
                                <div class="info-label">Contacto</div>
                                <div class="info-val" id="info_telefone">—</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <label class="small fw-bold text-muted mb-1" style="font-size:.68rem;letter-spacing:.8px;text-transform:uppercase;">Nº Cotação / Referência (opcional)</label>
                    <input type="text" id="numero_cotacao" name="numero_cotacao" class="form-control"
                           placeholder="Ex: COT N.º 159846"
                           style="border-radius:8px;border:1px solid #dee2e6;font-size:.85rem;background:#f8f9fa;">
                </div>
            </div>
        </div>
    </div>

    {{-- ITENS --}}
    <div class="form-card">
        <div class="form-card-header light">
            <h6 class="text-dark"><i class="bi bi-list-ul" style="color:#c60a1a;"></i> Itens da Requisição</h6>
            <button type="button"
                    class="btn btn-sm fw-bold px-3"
                    style="background:#c60a1a;color:#fff;border-radius:8px;font-size:.8rem;"
                    onclick="addLinha()">
                <i class="bi bi-plus-lg me-1"></i> Adicionar Item
            </button>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0" id="tabela_itens">
                <thead>
                    <tr>
                        <th style="width:40%">Descrição</th>
                        <th style="width:10%" class="text-center">Qtd</th>
                        <th style="width:17%" class="text-end">Preço Unit. (MT)</th>
                        <th style="width:8%" class="text-center">Desc. %</th>
                        <th style="width:15%" class="text-end">Subtotal</th>
                        <th style="width:5%"></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        {{-- TOTAIS --}}
        <div class="form-card-body border-top" style="background:#fafbfc;">
            <div class="row justify-content-end">
                <div class="col-md-4">
                    <div class="totais-box">

                        {{-- Subtotal Bruto --}}
                        <div class="totais-row">
                            <span class="totais-label">Subtotal Bruto</span>
                            <span class="fw-bold"><span id="total_bruto">0,00</span> MT</span>
                        </div>

                        {{-- Desconto Comercial (só aparece se houver) --}}
                        <div class="totais-row" id="area_desconto" style="display:none;">
                            <span class="totais-label" style="color:#d97706;">Desconto Comercial</span>
                            <span class="fw-bold" style="color:#d97706;">− <span id="valor_desconto">0,00</span> MT</span>
                        </div>

                        {{-- Subtotal Líquido (após desconto) --}}
                        <div class="totais-row">
                            <span class="totais-label">Subtotal Líquido</span>
                            <span class="fw-bold"><span id="total_liquido">0,00</span> MT</span>
                        </div>

                        {{-- IVA toggle --}}
                        <div class="totais-row align-items-center">
                            <label class="totais-label mb-0" for="aplicar_iva" style="cursor:pointer;">
                                Aplicar IVA (16%)
                            </label>
                            <div class="form-check form-switch mb-0 ms-2">
                                <input class="form-check-input" type="checkbox" id="aplicar_iva"
                                       onchange="calcularTotalGeral()" style="cursor:pointer;">
                            </div>
                        </div>

                        {{-- Valor IVA --}}
                        <div id="area_iva" class="totais-row d-none" style="color:#c60a1a;">
                            <span class="totais-label" style="color:#c60a1a;">Valor do IVA (16%)</span>
                            <span class="fw-bold"><span id="valor_iva">0,00</span> MT</span>
                        </div>

                        {{-- Total Geral --}}
                        <div class="totais-row final">
                            <span style="color:#c60a1a;">Total Geral</span>
                            <span style="color:#c60a1a;font-size:1.15rem;"><span id="total_final">0,00</span> MT</span>
                        </div>

                        <button type="button" id="btn_finalizar" class="btn-guardar" onclick="finalizarRequisicao()">
                            <i class="bi bi-check-circle"></i> Gravar Requisição
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ÁREA DE IMPRESSÃO --}}
<div id="area_impressao" class="p-5">
    <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-4">
        <div>
            <h2 class="fw-bold mb-0">REQUISIÇÃO DE COMPRA</h2>
            <p class="mb-0">Data: {{ date('d/m/Y') }}</p>
            <p class="mb-0">Nº da Requisição: <strong id="print_id_gerado">#---</strong></p>
            <p class="mb-0" id="print_linha_cotacao" style="display:none;">Cotação / Referência: <strong id="print_numero_cotacao">---</strong></p>
        </div>
        <div class="text-end">
            <h4 class="fw-bold text-uppercase" style="color:#c60a1a;">Fábrica de Explosivos de Moçambique</h4>
            <small class="text-muted">Av. Samora Machel Nº — Parcela 10 | Telef. +258 21 745 86/03</small>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-6">
            <h6 class="text-uppercase fw-bold border-bottom pb-1" style="color:#c60a1a;">Fornecedor</h6>
            <p class="mb-1 fw-bold" id="print_forn_nome">---</p>
            <p class="mb-1">NUIT: <span id="print_forn_nuit">---</span></p>
            <p class="mb-0">Endereço: <span id="print_forn_end">---</span></p>
        </div>
        <div class="col-6 text-end">
            <h6 class="text-uppercase fw-bold border-bottom pb-1" style="color:#c60a1a;">Estado</h6>
            <span class="badge border border-dark text-dark px-3 py-2">PENDENTE DE APROVAÇÃO</span>
        </div>
    </div>
    <table class="table table-bordered w-100">
        <thead style="background:#c60a1a;color:#fff;">
            <tr class="text-center">
                <th>DESCRIÇÃO DO ITEM</th>
                <th style="width:60px;">QTD</th>
                <th style="width:120px;">P. UNIT (MT)</th>
                <th style="width:70px;">DESC. %</th>
                <th style="width:130px;">TOTAL (MT)</th>
            </tr>
        </thead>
        <tbody id="print_table_body"></tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-end fw-bold">Subtotal Bruto:</td>
                <td class="text-end fw-bold" id="print_subtotal_bruto">0,00</td>
            </tr>
            <tr id="print_linha_desconto" style="display:none;">
                <td colspan="4" class="text-end fw-bold" style="color:#d97706;">Desconto Comercial:</td>
                <td class="text-end fw-bold" style="color:#d97706;" id="print_valor_desconto">0,00</td>
            </tr>
            <tr>
                <td colspan="4" class="text-end fw-bold">Subtotal Líquido:</td>
                <td class="text-end fw-bold" id="print_subtotal">0,00</td>
            </tr>
            <tr id="print_linha_iva" class="d-none">
                <td colspan="4" class="text-end fw-bold">IVA (16%):</td>
                <td class="text-end fw-bold" id="print_valor_iva">0,00</td>
            </tr>
            <tr style="font-size:1.1rem;background:#fdf2f2;">
                <td colspan="4" class="text-end fw-bold" style="color:#c60a1a;">VALOR TOTAL:</td>
                <td class="text-end fw-bold" style="color:#c60a1a;" id="print_total_geral">0,00</td>
            </tr>
        </tfoot>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
let contadorItens = 0;

$(document).ready(function() {
    @if(isset($compra) && $compra && isset($itensPreenchidos))
        const itensDaCompra = @json($itensPreenchidos);
        if (itensDaCompra.length > 0) {
            itensDaCompra.forEach(item => addLinha(item.desc, item.qty));
            calcularTotalGeral();
        } else { addLinha(); }
    @else
        addLinha();
    @endif

    $('#fornecedor_id').on('change', function() {
        const s = $(this).find(':selected');
        $('#info_nuit').text(s.data('nuit') || '—');
        $('#info_endereco').text(s.data('address') || '—');
        $('#info_telefone').text(s.data('contact') || '—');
        $('#print_forn_nome').text(s.data('name') || s.text());
        $('#print_forn_nuit').text(s.data('nuit') || '—');
        $('#print_forn_end').text(s.data('address') || '—');
    });
});

function addLinha(desc, qty) {
    desc = desc || '';
    qty  = qty  || 1;
    contadorItens++;
    const id         = contadorItens;
    const isImported = desc !== '';
    const html =
        '<tr id="linha_' + id + '">' +
            '<td>' +
                '<input type="text" class="form-control form-control-sm desc" value="' + desc + '" ' +
                'placeholder="Descrição do item" required style="border-radius:7px;font-size:.82rem;">' +
            '</td>' +
            '<td>' +
                '<input type="number" class="form-control form-control-sm text-center qty" value="' + qty + '" ' +
                'min="1" oninput="calcularLinha(' + id + ')" required style="border-radius:7px;font-size:.82rem;">' +
            '</td>' +
            '<td>' +
                '<input type="number" class="form-control form-control-sm text-end price ' + (isImported ? 'border-warning' : '') + '" ' +
                (isImported ? 'placeholder="Inserir preço"' : '') +
                ' step="0.01" min="0" oninput="calcularLinha(' + id + ')" required style="border-radius:7px;font-size:.82rem;">' +
            '</td>' +
            '<td>' +
                '<div class="disc-wrap">' +
                    '<input type="number" class="form-control form-control-sm text-center disc" value="0" ' +
                    'min="0" max="100" step="0.01" oninput="calcularLinha(' + id + ')" ' +
                    'style="border-radius:7px;font-size:.82rem;" title="Desconto comercial (%)">' +
                '</div>' +
            '</td>' +
            '<td class="text-end fw-bold" style="color:#c60a1a;">' +
                '<span id="subtotal_' + id + '">0,00</span> MT' +
                '<div id="desc_badge_' + id + '" style="display:none;font-size:.65rem;color:#d97706;font-weight:600;"></div>' +
            '</td>' +
            '<td class="text-center">' +
                '<button type="button" class="btn btn-link p-0" onclick="removerLinha(' + id + ')" style="color:#c60a1a;font-size:.85rem;">' +
                    '<i class="bi bi-trash3"></i>' +
                '</button>' +
            '</td>' +
        '</tr>';
    $('#tabela_itens tbody').append(html);
}

function calcularLinha(id) {
    const qty      = parseFloat($('tr#linha_' + id + ' .qty').val())   || 0;
    const price    = parseFloat($('tr#linha_' + id + ' .price').val()) || 0;
    const disc     = parseFloat($('tr#linha_' + id + ' .disc').val())  || 0;
    const bruto    = qty * price;
    const desconto = bruto * (disc / 100);
    const liquido  = bruto - desconto;

    $('#subtotal_' + id).text(liquido.toLocaleString('pt-MZ', { minimumFractionDigits: 2 }));

    if (disc > 0) {
        $('#desc_badge_' + id)
            .text('− ' + desconto.toLocaleString('pt-MZ', { minimumFractionDigits: 2 }) + ' MT (' + disc + '%)')
            .show();
    } else {
        $('#desc_badge_' + id).hide();
    }

    calcularTotalGeral();
}

function removerLinha(id) {
    $('#linha_' + id).remove();
    calcularTotalGeral();
}

function calcularTotalGeral() {
    let brutoTotal   = 0;
    let liquidoTotal = 0;

    $('tr[id^="linha_"]').each(function() {
        const qty   = parseFloat($(this).find('.qty').val())   || 0;
        const price = parseFloat($(this).find('.price').val()) || 0;
        const disc  = parseFloat($(this).find('.disc').val())  || 0;
        const bruto = qty * price;
        brutoTotal   += bruto;
        liquidoTotal += bruto * (1 - disc / 100);
    });

    const desconto = brutoTotal - liquidoTotal;
    const comIva   = $('#aplicar_iva').is(':checked');
    const iva      = comIva ? liquidoTotal * 0.16 : 0;
    const total    = liquidoTotal + iva;

    if (desconto > 0.001) {
        $('#area_desconto').show();
        $('#valor_desconto').text(desconto.toLocaleString('pt-MZ', { minimumFractionDigits: 2 }));
    } else {
        $('#area_desconto').hide();
    }

    $('#area_iva').toggleClass('d-none', !comIva);

    $('#total_bruto').text(brutoTotal.toLocaleString('pt-MZ',   { minimumFractionDigits: 2 }));
    $('#total_liquido').text(liquidoTotal.toLocaleString('pt-MZ', { minimumFractionDigits: 2 }));
    $('#valor_iva').text(iva.toLocaleString('pt-MZ',             { minimumFractionDigits: 2 }));
    $('#total_final').text(total.toLocaleString('pt-MZ',         { minimumFractionDigits: 2 }));
}

function finalizarRequisicao() {
    const supplierId = $('#fornecedor_id').val();
    if (!supplierId) { alert('Selecione um fornecedor!'); return; }

    const btn = $('#btn_finalizar');
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>A gravar...');

    const brutoVal   = parseFloat($('#total_bruto').text().replace(/\s/g,'').replace(',','.'))   || 0;
    const liquidoVal = parseFloat($('#total_liquido').text().replace(/\s/g,'').replace(',','.')) || 0;
    const descontoV  = brutoVal - liquidoVal;
    const numeroCotacao = $('#numero_cotacao').val().trim() || null;

    const dados = {
        supplier_id:      supplierId,
        numero_cotacao:   numeroCotacao,
        total_bruto:      brutoVal,
        discount_amount:  descontoV,
        total_liquid:     liquidoVal,
        tax_amount:       parseFloat($('#valor_iva').text().replace(/\s/g,'').replace(',','.')) || 0,
        total_final:      parseFloat($('#total_final').text().replace(/\s/g,'').replace(',','.')) || 0,
        has_tax:          $('#aplicar_iva').is(':checked') ? 'true' : 'false',
        items:            [],
    };

    $('#print_table_body').empty();

    $('tr[id^="linha_"]').each(function() {
        const desc  = $(this).find('.desc').val();
        const qty   = $(this).find('.qty').val();
        const price = $(this).find('.price').val();
        const disc  = $(this).find('.disc').val() || '0';
        const sub   = $(this).find('span[id^="subtotal_"]').text();

        if (desc) {
            dados.items.push({ desc, qty, price, disc });
            $('#print_table_body').append(
                '<tr class="text-center">' +
                    '<td class="text-start">' + desc + '</td>' +
                    '<td>' + qty + '</td>' +
                    '<td class="text-end">' + parseFloat(price||0).toLocaleString('pt-MZ',{minimumFractionDigits:2}) + '</td>' +
                    '<td>' + (parseFloat(disc) > 0 ? parseFloat(disc).toFixed(2) + '%' : '—') + '</td>' +
                    '<td class="text-end fw-bold">' + sub + ' MT</td>' +
                '</tr>'
            );
        }
    });

    if (dados.items.length === 0) {
        alert('Adicione pelo menos um item com descrição!');
        btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Gravar Requisição');
        return;
    }

    // Preenche área de impressão
    $('#print_subtotal_bruto').text($('#total_bruto').text());
    $('#print_subtotal').text($('#total_liquido').text());
    $('#print_valor_iva').text($('#valor_iva').text());
    $('#print_total_geral').text($('#total_final').text());

    if (numeroCotacao) {
        $('#print_numero_cotacao').text(numeroCotacao);
        $('#print_linha_cotacao').show();
    } else {
        $('#print_linha_cotacao').hide();
    }

    if (descontoV > 0.001) {
        $('#print_linha_desconto').show();
        $('#print_valor_desconto').text(descontoV.toLocaleString('pt-MZ', { minimumFractionDigits: 2 }));
    } else {
        $('#print_linha_desconto').hide();
    }
    $('#print_linha_iva').toggleClass('d-none', dados.has_tax !== 'true');

    $.ajax({
        url:         "{{ route('requisicoes.store') }}",
        method:      'POST',
        contentType: 'application/json',
        data:        JSON.stringify(dados),
        headers:     { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        success: function(res) {
            if (res.success) {
                $('#print_id_gerado').text('#' + String(res.id).padStart(4, '0'));
                setTimeout(function(){ window.location.href = '/requisicoes'; }, 1000);
            } else {
                alert(res.message || 'Erro ao gravar.');
                btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Gravar Requisição');
            }
        },
        error: function(xhr) {
            const res = xhr.responseJSON;
            let msg = 'Erro ao gravar. Verifique os campos.';
            if (res && res.message) msg = res.message;
            if (res && res.errors)  msg = Object.values(res.errors).flat().join('\n');
            alert(msg);
            btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Gravar Requisição');
        }
    });
}
</script>

</x-app-layout>
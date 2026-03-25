<x-app-layout>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    .form-card{background:#fff;border-radius:14px;border:1px solid #e9ecef;box-shadow:0 2px 8px rgba(0,0,0,.04);overflow:hidden;margin-bottom:20px}
    .form-card-header{padding:14px 20px;display:flex;justify-content:space-between;align-items:center}
    .form-card-header h6{margin:0;font-weight:700;font-size:.88rem;display:flex;align-items:center;gap:8px}
    .form-card-body{padding:20px 24px}
    #tabela_itens thead th{font-size:.67rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#868e96;border-bottom:1px solid #e9ecef;padding:8px 12px;white-space:nowrap;background:#f8f9fa}
    #tabela_itens tbody td{padding:8px 10px;vertical-align:middle;border-bottom:1px solid #f1f3f5;font-size:.82rem}
    #tabela_itens tbody tr:last-child td{border-bottom:none}
    .totais-box{background:#f8f9fa;border-radius:12px;padding:18px 20px;border:1px solid #e9ecef}
    .totais-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;font-size:.82rem}
    .totais-row.final{font-size:1rem;font-weight:800;margin-top:12px;padding-top:12px;border-top:2px solid #dee2e6}
    .info-fornecedor{background:#f0fdf4;border:1px solid #d1fae5;border-radius:10px;padding:14px 18px}
    .info-fornecedor p{margin:0;font-size:.78rem;line-height:1.8}
    @media print {
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
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-file-earmark-plus text-primary me-2"></i>Nova Requisição de Compra</h4>
            <p class="text-muted small mb-0">Preencha os dados do fornecedor e adicione os itens</p>
        </div>
        <a href="{{ route('requisicoes.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Voltar
        </a>
    </div>

    @if(isset($compra) && $compra)
    <div class="alert alert-success alert-dismissible fade show border-0 mb-4 py-2"
         style="border-radius:10px;background:#f0fdf4;border-left:4px solid #198754 !important;color:#166534;">
        <i class="bi bi-check-circle-fill me-2"></i>
        <strong>Itens importados automaticamente</strong> do Pedido de Compra <strong>#{{ $compra->id }}</strong>.
        Preencha apenas os <strong>preços unitários</strong> para finalizar.
        <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- FORNECEDOR --}}
    <div class="form-card">
        <div class="form-card-header" style="background:linear-gradient(135deg,#1a56db,#0dcaf0);">
            <h6 class="text-white"><i class="bi bi-building"></i> Fornecedor</h6>
        </div>
        <div class="form-card-body">
            <div class="row g-3 align-items-start">
                <div class="col-md-7">
                    <label class="small fw-bold text-muted mb-1">Selecionar Fornecedor</label>
                    <select id="fornecedor_id" name="supplier_id" class="form-select" required>
                        <option value="" selected disabled>Escolha um fornecedor...</option>
                        @foreach($fornecedores as $f)
                        <option value="{{ $f->id }}"
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
                        <p><span class="text-muted fw-bold" style="font-size:.65rem;letter-spacing:.8px;text-transform:uppercase;">NUIT</span><br><span id="info_nuit" class="fw-semibold">—</span></p>
                        <p><span class="text-muted fw-bold" style="font-size:.65rem;letter-spacing:.8px;text-transform:uppercase;">Endereço</span><br><span id="info_endereco" class="fw-semibold">—</span></p>
                        <p><span class="text-muted fw-bold" style="font-size:.65rem;letter-spacing:.8px;text-transform:uppercase;">Contacto</span><br><span id="info_telefone" class="fw-semibold">—</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ITENS --}}
    <div class="form-card">
        <div class="form-card-header" style="background:#f8f9fa;border-bottom:1px solid #e9ecef;">
            <h6 class="text-dark"><i class="bi bi-list-ul"></i> Itens da Requisição</h6>
            <button type="button" class="btn btn-primary btn-sm fw-bold px-3" onclick="addLinha()">
                <i class="bi bi-plus-lg me-1"></i> Adicionar Item
            </button>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0" id="tabela_itens">
                <thead>
                    <tr>
                        <th style="width:45%">Descrição</th>
                        <th style="width:12%" class="text-center">Qtd</th>
                        <th style="width:20%" class="text-end">Preço Unit. (MT)</th>
                        <th style="width:18%" class="text-end">Subtotal</th>
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
                        <div class="totais-row">
                            <span class="text-muted fw-bold" style="font-size:.7rem;letter-spacing:.8px;text-transform:uppercase;">Subtotal Líquido</span>
                            <span class="fw-bold"><span id="total_liquido">0,00</span> MT</span>
                        </div>
                        <div class="totais-row align-items-center">
                            <label class="text-muted fw-bold mb-0" for="aplicar_iva" style="font-size:.7rem;letter-spacing:.8px;text-transform:uppercase;cursor:pointer;">
                                Aplicar IVA (16%)
                            </label>
                            <div class="form-check form-switch mb-0 ms-2">
                                <input class="form-check-input" type="checkbox" id="aplicar_iva" onchange="calcularTotalGeral()" style="cursor:pointer;">
                            </div>
                        </div>
                        <div id="area_iva" class="totais-row d-none" style="color:#1a56db;">
                            <span class="fw-bold" style="font-size:.7rem;letter-spacing:.8px;text-transform:uppercase;">Valor do IVA</span>
                            <span class="fw-bold"><span id="valor_iva">0,00</span> MT</span>
                        </div>
                        <div class="totais-row final">
                            <span style="color:#198754;">Total Geral</span>
                            <span style="color:#198754;font-size:1.15rem;"><span id="total_final">0,00</span> MT</span>
                        </div>
                        <button type="button" id="btn_finalizar"
                                class="btn btn-success w-100 fw-bold py-2 mt-3" style="border-radius:10px;"
                                onclick="finalizarRequisicao()">
                            <i class="bi bi-check-circle me-2"></i>Gravar e Imprimir
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
        </div>
        <div class="text-end">
            <h4 class="fw-bold text-uppercase">SUA EMPRESA LOGO</h4>
            <small>Dondo, Sofala — Moçambique</small>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-6">
            <h6 class="text-uppercase fw-bold border-bottom pb-1">Fornecedor</h6>
            <p class="mb-1" id="print_forn_nome">---</p>
            <p class="mb-1">NUIT: <span id="print_forn_nuit">---</span></p>
            <p class="mb-0">Endereço: <span id="print_forn_end">---</span></p>
        </div>
        <div class="col-6 text-end">
            <h6 class="text-uppercase fw-bold border-bottom pb-1">Estado</h6>
            <span class="badge border border-dark text-dark px-3 py-2">PENDENTE DE APROVAÇÃO</span>
        </div>
    </div>
    <table class="table table-bordered border-dark w-100">
        <thead class="bg-light">
            <tr class="text-center">
                <th>DESCRIÇÃO DO ITEM</th>
                <th style="width:80px;">QTD</th>
                <th style="width:130px;">P. UNIT (MT)</th>
                <th style="width:130px;">TOTAL (MT)</th>
            </tr>
        </thead>
        <tbody id="print_table_body"></tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-end fw-bold">Subtotal Líquido:</td>
                <td class="text-end fw-bold" id="print_subtotal">0,00</td>
            </tr>
            <tr id="print_linha_iva" class="d-none">
                <td colspan="3" class="text-end fw-bold">IVA (16%):</td>
                <td class="text-end fw-bold" id="print_valor_iva">0,00</td>
            </tr>
            <tr style="font-size:1.2rem;background:#f8f9fa;">
                <td colspan="3" class="text-end fw-bold">VALOR TOTAL:</td>
                <td class="text-end fw-bold" id="print_total_geral">0,00</td>
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
        $('#print_forn_nome').text(s.text());
        $('#print_forn_nuit').text(s.data('nuit') || '—');
        $('#print_forn_end').text(s.data('address') || '—');
    });
});

function addLinha(desc = '', qty = 1) {
    contadorItens++;
    const isImported = desc !== '';
    const priceClass = isImported ? 'border-warning' : '';
    const html = `
        <tr id="linha_${contadorItens}">
            <td><input type="text" class="form-control form-control-sm desc" value="${desc}" placeholder="Descrição do item" required></td>
            <td><input type="number" class="form-control form-control-sm text-center qty" value="${qty}" min="1" oninput="calcularLinha(${contadorItens})" required></td>
            <td><input type="number" class="form-control form-control-sm text-end price ${priceClass}" ${isImported ? 'placeholder="Inserir preço"' : ''} step="0.01" min="0" oninput="calcularLinha(${contadorItens})" required></td>
            <td class="text-end fw-bold text-success"><span id="subtotal_${contadorItens}">0,00</span> MT</td>
            <td class="text-center">
                <button type="button" class="btn btn-link text-danger p-0" onclick="removerLinha(${contadorItens})" style="font-size:.85rem;">
                    <i class="bi bi-trash3"></i>
                </button>
            </td>
        </tr>`;
    $('#tabela_itens tbody').append(html);
}

function calcularLinha(id) {
    const qty      = parseFloat($(`tr#linha_${id} .qty`).val())   || 0;
    const price    = parseFloat($(`tr#linha_${id} .price`).val()) || 0;
    const subtotal = qty * price;
    $(`#subtotal_${id}`).text(subtotal.toLocaleString('pt-MZ', { minimumFractionDigits: 2 }));
    calcularTotalGeral();
}

function removerLinha(id) {
    $(`#linha_${id}`).remove();
    calcularTotalGeral();
}

function calcularTotalGeral() {
    let soma = 0;
    $('span[id^="subtotal_"]').each(function() {
        soma += parseFloat($(this).text().replace(/\s/g,'').replace(',','.')) || 0;
    });
    const comIva = $('#aplicar_iva').is(':checked');
    const iva    = comIva ? soma * 0.16 : 0;
    $('#area_iva').toggleClass('d-none', !comIva);
    $('#total_liquido').text(soma.toLocaleString('pt-MZ', { minimumFractionDigits: 2 }));
    $('#valor_iva').text(iva.toLocaleString('pt-MZ', { minimumFractionDigits: 2 }));
    $('#total_final').text((soma + iva).toLocaleString('pt-MZ', { minimumFractionDigits: 2 }));
}

function finalizarRequisicao() {
    const supplierId = $('#fornecedor_id').val();
    if (!supplierId) { alert('Selecione um fornecedor!'); return; }

    const btn = $('#btn_finalizar');
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>A gravar...');

    const dados = {
        supplier_id:  supplierId,
        total_liquid: parseFloat($('#total_liquido').text().replace(/\s/g,'').replace(',','.')),
        tax_amount:   parseFloat($('#valor_iva').text().replace(/\s/g,'').replace(',','.')),
        total_final:  parseFloat($('#total_final').text().replace(/\s/g,'').replace(',','.')),
        has_tax:      $('#aplicar_iva').is(':checked') ? 'true' : 'false',
        items:        [],
    };

    $('#print_table_body').empty();
    $('tr[id^="linha_"]').each(function() {
        const desc  = $(this).find('.desc').val();
        const qty   = $(this).find('.qty').val();
        const price = $(this).find('.price').val();
        const sub   = $(this).find('span[id^="subtotal_"]').text();
        if (desc) {
            dados.items.push({ desc, qty, price });
            $('#print_table_body').append(`
                <tr class="text-center">
                    <td class="text-start">${desc}</td>
                    <td>${qty}</td>
                    <td class="text-end">${parseFloat(price).toLocaleString('pt-MZ',{minimumFractionDigits:2})}</td>
                    <td class="text-end fw-bold">${sub}</td>
                </tr>`);
        }
    });

    if (dados.items.length === 0) {
        alert('Adicione pelo menos um item com descrição!');
        btn.prop('disabled', false).html('<i class="bi bi-check-circle me-2"></i>Gravar e Imprimir');
        return;
    }

    $.ajax({
        url:         "{{ route('requisicoes.store') }}",
        method:      'POST',
        contentType: 'application/json',
        data:        JSON.stringify(dados),
        headers:     { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        success: function(res) {
            if (res.success) {
                $('#print_id_gerado').text('#' + String(res.id).padStart(4, '0'));
                $('#print_subtotal').text($('#total_liquido').text());
                $('#print_valor_iva').text($('#valor_iva').text());
                $('#print_total_geral').text($('#total_final').text());
                $('#print_linha_iva').toggleClass('d-none', dados.has_tax !== 'true');
                // window.print();
                setTimeout(() => { window.location.href = '/requisicoes'; }, 1000);
            } else {
                alert(res.message || 'Erro ao gravar.');
                btn.prop('disabled', false).html('<i class="bi bi-check-circle me-2"></i>Gravar e Imprimir');
            }
        },
        error: function(xhr) {
            const res = xhr.responseJSON;
            let msg = 'Erro ao gravar. Verifique os campos.';
            if (res?.message) msg = res.message;
            if (res?.errors)  msg = Object.values(res.errors).flat().join('\n');
            alert(msg);
            btn.prop('disabled', false).html('<i class="bi bi-check-circle me-2"></i>Gravar e Imprimir');
        }
    });
}
</script>

</x-app-layout>
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
</style>

<div class="container-fluid py-4 px-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-pencil-square text-warning me-2"></i>
                Editar Requisição
                <span style="color:#adb5bd;font-weight:400;font-size:.9rem;margin-left:6px;">#{{ str_pad($requisicao->id, 4, '0', STR_PAD_LEFT) }}</span>
            </h4>
            <p class="text-muted small mb-0">Actualiza os dados do fornecedor e os itens</p>
        </div>
        <a href="{{ route('requisicoes.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Voltar
        </a>
    </div>

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
                        <option value="" disabled>Escolha um fornecedor...</option>
                        @foreach($fornecedores as $f)
                        <option value="{{ $f->id }}"
                                data-nuit="{{ $f->nuit }}"
                                data-address="{{ $f->address }}"
                                data-contact="{{ $f->contact }}"
                                {{ $f->id == $requisicao->supplier_id ? 'selected' : '' }}>
                            {{ $f->code }} — {{ $f->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <div class="info-fornecedor">
                        <p><span class="text-muted fw-bold" style="font-size:.65rem;letter-spacing:.8px;text-transform:uppercase;">NUIT</span><br>
                           <span id="info_nuit" class="fw-semibold">{{ $requisicao->supplier->nuit ?? '—' }}</span></p>
                        <p><span class="text-muted fw-bold" style="font-size:.65rem;letter-spacing:.8px;text-transform:uppercase;">Endereço</span><br>
                           <span id="info_endereco" class="fw-semibold">{{ $requisicao->supplier->address ?? '—' }}</span></p>
                        <p><span class="text-muted fw-bold" style="font-size:.65rem;letter-spacing:.8px;text-transform:uppercase;">Contacto</span><br>
                           <span id="info_telefone" class="fw-semibold">{{ $requisicao->supplier->contact ?? '—' }}</span></p>
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
                                <input class="form-check-input" type="checkbox" id="aplicar_iva"
                                       onchange="calcularTotalGeral()"
                                       {{ $requisicao->has_tax ? 'checked' : '' }}
                                       style="cursor:pointer;">
                            </div>
                        </div>
                        <div id="area_iva" class="totais-row {{ $requisicao->has_tax ? '' : 'd-none' }}" style="color:#1a56db;">
                            <span class="fw-bold" style="font-size:.7rem;letter-spacing:.8px;text-transform:uppercase;">Valor do IVA</span>
                            <span class="fw-bold"><span id="valor_iva">0,00</span> MT</span>
                        </div>
                        <div class="totais-row final">
                            <span style="color:#198754;">Total Geral</span>
                            <span style="color:#198754;font-size:1.15rem;"><span id="total_final">0,00</span> MT</span>
                        </div>
                        <button type="button" id="btn_guardar"
                                class="btn btn-warning w-100 fw-bold py-2 mt-3 text-white" style="border-radius:10px;"
                                onclick="guardarEdicao()">
                            <i class="bi bi-check-circle me-2"></i>Guardar Alterações
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
let contadorItens = 0;

// Itens existentes da requisição
const itensExistentes = @json($requisicao->items);

$(document).ready(function() {
    // Carrega os itens existentes
    if (itensExistentes.length > 0) {
        itensExistentes.forEach(item => {
            addLinha(item.description, item.quantity, item.unit_price);
        });
    } else {
        addLinha();
    }

    calcularTotalGeral();

    $('#fornecedor_id').on('change', function() {
        const s = $(this).find(':selected');
        $('#info_nuit').text(s.data('nuit') || '—');
        $('#info_endereco').text(s.data('address') || '—');
        $('#info_telefone').text(s.data('contact') || '—');
    });
});

function addLinha(desc = '', qty = 1, price = '') {
    contadorItens++;
    const html = `
        <tr id="linha_${contadorItens}">
            <td><input type="text" class="form-control form-control-sm desc" value="${desc}" placeholder="Descrição do item" required></td>
            <td><input type="number" class="form-control form-control-sm text-center qty" value="${qty}" min="1" oninput="calcularLinha(${contadorItens})" required></td>
            <td><input type="number" class="form-control form-control-sm text-end price" value="${price}" step="0.01" min="0" oninput="calcularLinha(${contadorItens})" required></td>
            <td class="text-end fw-bold text-success"><span id="subtotal_${contadorItens}">0,00</span> MT</td>
            <td class="text-center">
                <button type="button" class="btn btn-link text-danger p-0" onclick="removerLinha(${contadorItens})" style="font-size:.85rem;">
                    <i class="bi bi-trash3"></i>
                </button>
            </td>
        </tr>`;
    $('#tabela_itens tbody').append(html);
    calcularLinha(contadorItens);
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

function guardarEdicao() {
    const supplierId = $('#fornecedor_id').val();
    if (!supplierId) { alert('Selecione um fornecedor!'); return; }

    const btn = $('#btn_guardar');
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>A guardar...');

    const dados = {
        supplier_id:  supplierId,
        total_liquid: parseFloat($('#total_liquido').text().replace(/\s/g,'').replace(',','.')),
        tax_amount:   parseFloat($('#valor_iva').text().replace(/\s/g,'').replace(',','.')),
        total_final:  parseFloat($('#total_final').text().replace(/\s/g,'').replace(',','.')),
        has_tax:      $('#aplicar_iva').is(':checked') ? 'true' : 'false',
        items:        [],
    };

    $('tr[id^="linha_"]').each(function() {
        const desc  = $(this).find('.desc').val();
        const qty   = $(this).find('.qty').val();
        const price = $(this).find('.price').val();
        if (desc) dados.items.push({ desc, qty, price });
    });

    if (dados.items.length === 0) {
        alert('Adicione pelo menos um item!');
        btn.prop('disabled', false).html('<i class="bi bi-check-circle me-2"></i>Guardar Alterações');
        return;
    }

    $.ajax({
        url:         "{{ route('requisicoes.update', $requisicao->id) }}",
        method:      'PUT',
        contentType: 'application/json',
        data:        JSON.stringify(dados),
        headers:     { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        success: function(res) {
            if (res.success) {
                window.location.href = '/requisicoes';
            } else {
                alert(res.message || 'Erro ao guardar.');
                btn.prop('disabled', false).html('<i class="bi bi-check-circle me-2"></i>Guardar Alterações');
            }
        },
        error: function(xhr) {
            const res = xhr.responseJSON;
            let msg = 'Erro ao guardar.';
            if (res?.message) msg = res.message;
            if (res?.errors)  msg = Object.values(res.errors).flat().join('\n');
            alert(msg);
            btn.prop('disabled', false).html('<i class="bi bi-check-circle me-2"></i>Guardar Alterações');
        }
    });
}
</script>

</x-app-layout>
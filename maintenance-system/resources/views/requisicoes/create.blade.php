<x-app-layout>
    <style>
        @media print {
            body * { visibility: hidden; }
            #area_impressao, #area_impressao * { visibility: visible; }
            #area_impressao { position: absolute; left: 0; top: 0; width: 100%; display: block !important; }
        }
        #area_impressao { display: none; }
    </style>
    
    <div class="container py-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-file-earmark-plus me-2"></i>Nova Requisição de Compra</h5>
                <a href="{{ route('requisicoes.index') }}" class="btn btn-outline-light btn-sm">Voltar</a>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="small fw-bold text-uppercase text-muted">Selecionar Fornecedor</label>
                        <select id="fornecedor_id" name="supplier_id" class="form-select border-primary shadow-sm" required>
                            <option value="" selected disabled>Escolha um fornecedor...</option>
                            @foreach($fornecedores as $f)
                                <option value="{{ $f->id }}" 
                                        data-nuit="{{ $f->nuit }}" 
                                        data-address="{{ $f->address }}"
                                        data-contact="{{ $f->contact }}">
                                    {{ $f->code }} - {{ $f->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded border">
                            <p class="mb-1 small"><strong>NUIT:</strong> <span id="info_nuit">---</span></p>
                            <p class="mb-1 small"><strong>Endereço:</strong> <span id="info_endereco">---</span></p>
                            <p class="mb-0 small"><strong>Contacto:</strong> <span id="info_telefone">---</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Itens da Requisição</h6>
                <button type="button" class="btn btn-light btn-sm fw-bold" onclick="addLinha()">
                    <i class="bi bi-plus-circle me-1"></i> Adicionar Item
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tabela_itens">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 45%" class="small fw-bold">Descrição</th>
                            <th style="width: 15%" class="small fw-bold text-center">Qtd</th>
                            <th style="width: 20%" class="small fw-bold text-end">Preço Unit. (MT)</th>
                            <th style="width: 15%" class="small fw-bold text-end">Subtotal</th>
                            <th style="width: 5%"></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

                <div class="card-footer bg-white p-4">
                    <div class="row justify-content-end">
                        <div class="col-md-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small fw-bold text-uppercase">Subtotal Líquido:</span>
                                <span class="fw-bold"><span id="total_liquido">0,00</span> MT</span>
                            </div>
                            
                            <div class="form-check form-switch d-flex justify-content-between align-items-center mb-2 p-0">
                                <label class="form-check-label small fw-bold text-muted text-uppercase" for="aplicar_iva">Aplicar IVA (16%):</label>
                                <input class="form-check-input ms-0" type="checkbox" id="aplicar_iva" onchange="calcularTotalGeral()">
                            </div>

                            <div id="area_iva" class="d-none d-flex justify-content-between align-items-center mb-2 text-primary">
                                <span class="small fw-bold text-uppercase">Valor do IVA:</span>
                                <span class="fw-bold"><span id="valor_iva">0,00</span> MT</span>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <span class="h6 fw-bold text-dark">TOTAL GERAL:</span>
                                <span class="h5 fw-bold text-success"><span id="total_final">0,00</span> MT</span>
                            </div>

                            <button type="button" id="btn_finalizar" class="btn btn-success w-100 fw-bold py-2" onclick="finalizarRequisicao()">
                                <i class="bi bi-check-circle me-2"></i>GRAVAR E IMPRIMIR
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
                <p class="mb-0">Data: <span>{{ date('d/m/Y') }}</span></p>
                <p class="mb-0">Nº da Requisição: <strong id="print_id_gerado">#---</strong></p>
            </div>
            <div class="text-end">
                <h4 class="fw-bold text-uppercase">SUA EMPRESA LOGO</h4>
                <small>Dondo, Sofala - Moçambique</small>
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
                    <th style="width: 80px;">QTD</th>
                    <th style="width: 130px;">P. UNIT (MT)</th>
                    <th style="width: 130px;">TOTAL (MT)</th>
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
                <tr style="font-size: 1.2rem; background: #f8f9fa;">
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
            addLinha();

            $('#fornecedor_id').on('change', function() {
                const selected = $(this).find(':selected');
                $('#info_nuit').text(selected.data('nuit') || '---');
                $('#info_endereco').text(selected.data('address') || '---');
                $('#info_telefone').text(selected.data('contact') || '---');
                
                $('#print_forn_nome').text(selected.text());
                $('#print_forn_nuit').text(selected.data('nuit') || '---');
                $('#print_forn_end').text(selected.data('address') || '---');
            });
        });

        function addLinha() {
            contadorItens++;
            const html = `
                <tr id="linha_${contadorItens}">
                    <td><input type="text" class="form-control form-control-sm desc" required></td>
                    <td><input type="number" class="form-control form-control-sm text-center qty" value="1" min="1" oninput="calcularLinha(${contadorItens})" required></td>
                    <td><input type="number" class="form-control form-control-sm text-end price" step="0.01" oninput="calcularLinha(${contadorItens})" required></td>
                    <td class="text-end fw-bold"><span id="subtotal_${contadorItens}">0.00</span> MT</td>
                    <td class="text-center"><button type="button" class="btn btn-link text-danger p-0" onclick="removerLinha(${contadorItens})"><i class="bi bi-trash"></i></button></td>
                </tr>`;
            $('#tabela_itens tbody').append(html);
        }

        function calcularLinha(id) {
            const qty   = parseFloat($(`tr#linha_${id} .qty`).val()) || 0;
            const price = parseFloat($(`tr#linha_${id} .price`).val()) || 0;
            const subtotal = qty * price;
            $(`#subtotal_${id}`).text(subtotal.toLocaleString('pt-MZ', { minimumFractionDigits: 2 }));
            calcularTotalGeral();
        }

        function removerLinha(id) {
            $(`#linha_${id}`).remove();
            calcularTotalGeral();
        }

        function calcularTotalGeral() {
            let somaLiquida = 0;
            $('span[id^="subtotal_"]').each(function() {
                let valor = $(this).text().replace(/\s/g, '').replace(',', '.');
                somaLiquida += parseFloat(valor) || 0;
            });

            const comIva   = $('#aplicar_iva').is(':checked');
            let valorIva   = comIva ? somaLiquida * 0.16 : 0;
            
            if (comIva) $('#area_iva').removeClass('d-none');
            else        $('#area_iva').addClass('d-none');

            $('#total_liquido').text(somaLiquida.toLocaleString('pt-MZ', { minimumFractionDigits: 2 }));
            $('#valor_iva').text(valorIva.toLocaleString('pt-MZ', { minimumFractionDigits: 2 }));
            $('#total_final').text((somaLiquida + valorIva).toLocaleString('pt-MZ', { minimumFractionDigits: 2 }));
        }

        function finalizarRequisicao() {
            const supplierId = $('#fornecedor_id').val();
            if (!supplierId) return alert('Selecione um fornecedor!');

            const btn = $('#btn_finalizar');
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Gravando...');

            // ✅ CORRIGIDO: has_tax enviado como string 'true'/'false'
            const dadosForm = {
                supplier_id:   supplierId,
                total_liquid:  parseFloat($('#total_liquido').text().replace(/\s/g, '').replace(',', '.')),
                tax_amount:    parseFloat($('#valor_iva').text().replace(/\s/g, '').replace(',', '.')),
                total_final:   parseFloat($('#total_final').text().replace(/\s/g, '').replace(',', '.')),
                has_tax:       $('#aplicar_iva').is(':checked') ? 'true' : 'false',
                items:         []
            };

            // Monta os itens e a área de impressão
            $('#print_table_body').empty();
            $('tr[id^="linha_"]').each(function() {
                const desc  = $(this).find('.desc').val();
                const qty   = $(this).find('.qty').val();
                const price = $(this).find('.price').val();
                const sub   = $(this).find('span[id^="subtotal_"]').text();

                if (desc) {
                    dadosForm.items.push({ desc: desc, qty: qty, price: price });
                    $('#print_table_body').append(`
                        <tr class="text-center">
                            <td class="text-start">${desc}</td>
                            <td>${qty}</td>
                            <td class="text-end">${parseFloat(price).toLocaleString('pt-MZ', {minimumFractionDigits:2})}</td>
                            <td class="text-end fw-bold">${sub}</td>
                        </tr>`);
                }
            });

            if (dadosForm.items.length === 0) {
                alert('Adicione pelo menos um item com descrição!');
                btn.prop('disabled', false).html('<i class="bi bi-check-circle me-2"></i>GRAVAR E IMPRIMIR');
                return;
            }

            // ✅ CORRIGIDO: $.ajax com JSON em vez de $.post com form-data
            $.ajax({
                url:         "{{ route('requisicoes.store') }}",
                method:      'POST',
                contentType: 'application/json',
                data:        JSON.stringify(dadosForm),
                headers:     { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        // Preenche a área de impressão
                        $('#print_id_gerado').text('#' + String(response.id).padStart(4, '0'));
                        $('#print_subtotal').text($('#total_liquido').text());
                        $('#print_valor_iva').text($('#valor_iva').text());
                        $('#print_total_geral').text($('#total_final').text());

                        if (dadosForm.has_tax === 'true') {
                            $('#print_linha_iva').removeClass('d-none');
                        } else {
                            $('#print_linha_iva').addClass('d-none');
                        }

                        // Imprime e redireciona
                        window.print();
                        setTimeout(() => { window.location.href = "/requisicoes"; }, 1000);
                    } else {
                        alert(response.message || 'Erro ao salvar.');
                        btn.prop('disabled', false).html('<i class="bi bi-check-circle me-2"></i>GRAVAR E IMPRIMIR');
                    }
                },
                error: function(jqXHR) {
                    // Mostra o erro real
                    console.error('Erro detalhado:', jqXHR.responseText);
                    const response = jqXHR.responseJSON;
                    let msg = 'Erro ao salvar. Verifique os campos.';
                    if (response && response.message) msg = response.message;
                    if (response && response.errors) {
                        msg = Object.values(response.errors).flat().join('\n');
                    }
                    alert(msg);
                    btn.prop('disabled', false).html('<i class="bi bi-check-circle me-2"></i>GRAVAR E IMPRIMIR');
                }
            });
        }
    </script>
</x-app-layout>
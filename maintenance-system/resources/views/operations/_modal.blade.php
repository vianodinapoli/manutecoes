{{-- Modal criar/editar operação + plano. Incluir com @include('operations._modal') --}}
@php
    // Nomes já usados em camiões anteriores, para sugerir ao escrever
    $transpSugestoes = \App\Models\Discharge::query()
        ->whereNotNull('transportadora')
        ->distinct()->orderBy('transportadora')->limit(200)
        ->pluck('transportadora');
@endphp
<div class="modal fade" id="modalOperacao" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius:14px;overflow:hidden;">
            <div class="modal-header border-0 text-white"
                 style="background:linear-gradient(135deg,#1a56db,#0dcaf0);padding:12px 20px;">
                <h5 class="modal-title fw-bold" style="font-size:.9rem;">
                    <i class="bi bi-ship me-2"></i><span id="opTitulo">Nova Operação</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="formOperacao" method="POST" action="{{ route('operations.store') }}">
                @csrf
                <input type="hidden" name="_method" id="opMethod" value="PUT" disabled>

                <div class="modal-body p-4">
                    <div class="row g-3">

                        {{-- Identificação --}}
                        <div class="col-md-6">
                            <label class="form-label op-lbl">Navio *</label>
                            <input type="text" name="navio" id="op_navio" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label op-lbl">Empresa *</label>
                            <input type="text" name="empresa" id="op_empresa" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label op-lbl">BL</label>
                            <input type="text" name="bl" id="op_bl" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label op-lbl">Factura</label>
                            <input type="text" name="factura" id="op_factura" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label op-lbl">Data de chegada</label>
                            <input type="date" name="data_chegada" id="op_data_chegada" class="form-control form-control-sm">
                        </div>
                        <div class="col-12">
                            <label class="form-label op-lbl">Observações</label>
                            <input type="text" name="observacoes" id="op_observacoes" class="form-control form-control-sm">
                        </div>

                        {{-- PLANO --}}
                        <div class="col-12">
                            <div class="plan-box">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <div class="op-lbl" style="color:#1a56db;">Plano da descarga *</div>
                                        <div style="font-size:.66rem;color:#94a3b8;">
                                            Defina cada produto / tipo de saco, o peso por saco e a quantidade esperada.
                                        </div>
                                    </div>
                                    <button type="button" id="btnAddItem" class="btn btn-outline-primary btn-sm"
                                            style="font-size:.68rem;border-radius:6px;" onclick="addItemRow()">
                                        <i class="bi bi-plus-lg me-1"></i>Adicionar produto
                                    </button>
                                </div>

                                <div id="planLockMsg" class="alert alert-warning py-2 mb-2" style="display:none;font-size:.72rem;border-radius:8px;"></div>

                                <div class="item-head">
                                    <span>Produto / tipo</span><span>Unidade</span><span>Peso por saco (kg)</span><span>Quantidade</span><span></span>
                                </div>
                                <div id="itemRows" class="d-flex flex-column gap-2"></div>

                                <div class="d-flex justify-content-end mt-2" style="font-size:.78rem;">
                                    <span class="text-muted me-2">Peso esperado total:</span>
                                    <b id="planTotal" style="color:#1a56db;">0 t</b>
                                </div>

                                <div id="motivoWrap" class="mt-2" style="display:none;">
                                    <label class="form-label op-lbl">Motivo da alteração ao plano *</label>
                                    <input type="text" name="motivo_alteracao" id="op_motivo" class="form-control form-control-sm"
                                           maxlength="255" placeholder="Ex.: correcção da quantidade segundo a factura final">
                                </div>
                            </div>
                        </div>

                        {{-- Transportadoras (stickers) --}}
                        <div class="col-12">
                            <label class="form-label op-lbl">Transportadoras</label>
                            <div class="chips-box" onclick="document.getElementById('transpInput').focus()">
                                <span id="transpChips" style="display:contents;"></span>
                                <input type="text" id="transpInput" class="chip-input" list="transpSugestoes"
                                       maxlength="100" autocomplete="off"
                                       placeholder="Escreva o nome e carregue Enter…">
                            </div>
                            <div id="transpHidden"></div>
                            <datalist id="transpSugestoes">
                                @foreach($transpSugestoes as $nomeT)
                                    <option value="{{ $nomeT }}"></option>
                                @endforeach
                            </datalist>
                            <div style="font-size:.66rem;color:#94a3b8;margin-top:4px;">
                                Ao lançar um camião, estas aparecem na lista. Uma transportadora que não esteja aqui
                                pode ser acrescentada na hora e fica a <b style="color:#fd7e14;">laranja</b>.
                            </div>
                        </div>

                        {{-- Metadata --}}
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label op-lbl mb-0">Dados adicionais (metadata)</label>
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                        style="font-size:.68rem;border-radius:6px;" onclick="addMetaRow()">
                                    <i class="bi bi-plus-lg me-1"></i>Adicionar campo
                                </button>
                            </div>
                            <div id="metaRows" class="d-flex flex-column gap-2"></div>
                            <div style="font-size:.66rem;color:#94a3b8;margin-top:4px;">
                                Ex.: Agente = Maersk · Porto de origem = Odessa · Nº contrato = 123/2026
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0" style="padding:0 20px 16px;">
                    <button type="button" class="btn btn-light btn-sm px-3" style="border-radius:7px;" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold" style="border-radius:7px;">
                        <i class="bi bi-check-circle me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .op-lbl{font-size:.68rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px}
    .plan-box{background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:14px}

    /* Stickers de transportadoras */
    .chips-box{display:flex;flex-wrap:wrap;gap:6px;align-items:center;min-height:42px;cursor:text;
               border:1px solid #dee2e6;border-radius:6px;padding:6px 8px;background:#fff}
    .chips-box:focus-within{border-color:#86b7fe;box-shadow:0 0 0 .2rem rgba(13,110,253,.15)}
    .chip-input{flex:1;min-width:180px;border:none;outline:none;font-size:.82rem;padding:2px 4px;background:transparent}
    .tp-sticker{display:inline-flex;align-items:center;gap:6px;border-radius:20px;padding:3px 6px 3px 11px;
                font-size:.72rem;font-weight:600;background:#eef2ff;color:#3b4cca;border:1px solid #d5dcff}
    .tp-sticker.extra{background:#fd7e14;color:#fff;border-color:#fd7e14;box-shadow:0 0 0 3px rgba(253,126,20,.22)}
    .tp-sticker button{border:none;background:rgba(0,0,0,.08);color:inherit;width:18px;height:18px;border-radius:50%;
                       font-size:.6rem;line-height:18px;padding:0;cursor:pointer;display:inline-flex;
                       align-items:center;justify-content:center}
    .tp-sticker button:hover{background:rgba(0,0,0,.2)}
    .item-head,.item-row{display:grid;grid-template-columns:2fr 1fr 1.2fr 1.2fr 36px;gap:8px;align-items:center}
    .item-head{font-size:.6rem;font-weight:700;letter-spacing:.6px;text-transform:uppercase;color:#94a3b8;margin-bottom:4px}
    @media (max-width:768px){
        .item-head{display:none}
        .item-row{grid-template-columns:1fr 1fr;}
    }
</style>

<script>
    const OP_STORE_URL  = @json(route('operations.store'));
    const OP_UPDATE_URL = @json(url('operations')) + '/';
    const OP_IS_SUPER   = @json(auth()->user()->hasRole('super-admin'));
    let itemIdx = 0;

    /* ── Metadata ── */
    function addMetaRow(k = '', v = '') {
        const row = document.createElement('div');
        row.className = 'd-flex gap-2';
        row.innerHTML = `
            <input type="text" name="metadata_keys[]" class="form-control form-control-sm" placeholder="Campo" style="max-width:40%;">
            <input type="text" name="metadata_values[]" class="form-control form-control-sm" placeholder="Valor">
            <button type="button" class="btn btn-outline-danger btn-sm" style="border-radius:6px;" onclick="this.parentNode.remove()">
                <i class="bi bi-x-lg"></i>
            </button>`;
        row.querySelector('[name="metadata_keys[]"]').value = k;
        row.querySelector('[name="metadata_values[]"]').value = v;
        document.getElementById('metaRows').appendChild(row);
    }

    /* ── Transportadoras (stickers) ── */
    let transp = [];   // [{nome, extra}]

    function renderTransp() {
        const box = document.getElementById('transpChips');
        const hid = document.getElementById('transpHidden');
        box.innerHTML = '';
        hid.innerHTML = '';

        transp.forEach((t, i) => {
            const chip = document.createElement('span');
            chip.className = 'tp-sticker' + (t.extra ? ' extra' : '');
            if (t.extra) chip.title = 'Fora da lista prevista. Clique em ✓ para aprovar.';

            if (t.extra) {
                const w = document.createElement('i');
                w.className = 'bi bi-exclamation-triangle-fill';
                chip.appendChild(w);
            }
            const nome = document.createElement('span');
            nome.textContent = t.nome;
            chip.appendChild(nome);

            if (t.extra) {
                const ok = document.createElement('button');
                ok.type = 'button'; ok.title = 'Aprovar (passa a transportadora prevista)';
                ok.innerHTML = '<i class="bi bi-check-lg"></i>';
                ok.onclick = e => { e.stopPropagation(); transp[i].extra = false; renderTransp(); };
                chip.appendChild(ok);
            }
            const rm = document.createElement('button');
            rm.type = 'button'; rm.title = 'Remover';
            rm.innerHTML = '<i class="bi bi-x-lg"></i>';
            rm.onclick = e => { e.stopPropagation(); transp.splice(i, 1); renderTransp(); };
            chip.appendChild(rm);

            box.appendChild(chip);

            const a = document.createElement('input');
            a.type = 'hidden'; a.name = 'transportadoras[]'; a.value = t.nome;
            hid.appendChild(a);
            if (t.extra) {
                const b = document.createElement('input');
                b.type = 'hidden'; b.name = 'transportadoras_extra[]'; b.value = t.nome;
                hid.appendChild(b);
            }
        });
    }

    function addTransp(txt) {
        const nome = (txt || '').replace(/\s+/g, ' ').trim();
        if (!nome) return;
        if (transp.some(t => t.nome.toLowerCase() === nome.toLowerCase())) return;
        transp.push({ nome, extra: false });
        renderTransp();
    }

    function flushTranspInput() {
        const inp = document.getElementById('transpInput');
        inp.value.split(',').forEach(addTransp);
        inp.value = '';
    }

    document.addEventListener('DOMContentLoaded', () => {
        const inp = document.getElementById('transpInput');
        if (!inp) return;

        inp.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                flushTranspInput();
            } else if (e.key === 'Backspace' && inp.value === '' && transp.length) {
                transp.pop();
                renderTransp();
            }
        });
        inp.addEventListener('change', flushTranspInput);   // escolha na lista de sugestões
        inp.addEventListener('blur', flushTranspInput);
        document.getElementById('formOperacao').addEventListener('submit', flushTranspInput);
    });

    /* ── Plano: linhas de produtos ── */
    function addItemRow(it = {}) {
        const i = itemIdx++;
        const row = document.createElement('div');
        row.className = 'item-row';
        row.innerHTML = `
            <input type="hidden" name="items[${i}][id]">
            <input type="text" name="items[${i}][nome]" class="form-control form-control-sm" placeholder="Ex.: Alta, Baixa, Outro" maxlength="100" required>
            <select name="items[${i}][unidade]" class="form-select form-select-sm" onchange="syncUnidade(this.closest('.item-row'))">
                <option value="saco">Saco</option>
                <option value="tonelada">Tonelada</option>
            </select>
            <input type="number" name="items[${i}][peso_unitario_kg]" class="form-control form-control-sm it-peso" placeholder="kg" step="0.001" min="0.001" oninput="calcPlano()">
            <input type="number" name="items[${i}][quantidade]" class="form-control form-control-sm it-qtd" placeholder="Quantidade" step="0.001" min="0.001" required oninput="calcPlano()">
            <button type="button" class="btn btn-outline-danger btn-sm it-rm" style="border-radius:6px;" onclick="this.closest('.item-row').remove();calcPlano()">
                <i class="bi bi-x-lg"></i>
            </button>`;

        row.querySelector('[name$="[id]"]').value         = it.id ?? '';
        row.querySelector('[name$="[nome]"]').value       = it.nome ?? '';
        row.querySelector('[name$="[unidade]"]').value    = it.unidade ?? 'saco';
        row.querySelector('[name$="[peso_unitario_kg]"]').value = it.peso_unitario_kg ?? '';
        row.querySelector('[name$="[quantidade]"]').value = it.quantidade ?? '';

        document.getElementById('itemRows').appendChild(row);
        syncUnidade(row);
    }

    // Em "tonelada" o peso por saco não se aplica
    function syncUnidade(row) {
        const tonelada = row.querySelector('select').value === 'tonelada';
        const peso = row.querySelector('.it-peso');
        if (tonelada) { peso.value = ''; }
        peso.disabled = tonelada;
        peso.required = !tonelada;
        calcPlano();
    }

    function calcPlano() {
        let kg = 0;
        document.querySelectorAll('#itemRows .item-row').forEach(r => {
            const qtd = parseFloat(r.querySelector('.it-qtd').value) || 0;
            const tonelada = r.querySelector('select').value === 'tonelada';
            const peso = parseFloat(r.querySelector('.it-peso').value) || 0;
            kg += tonelada ? qtd * 1000 : qtd * peso;
        });
        document.getElementById('planTotal').textContent =
            (kg / 1000).toLocaleString('pt-PT', { maximumFractionDigits: 3 }) + ' t';
    }

    function bloquearPlano(msg) {
        document.querySelectorAll('#itemRows input, #itemRows select, #itemRows button').forEach(el => el.disabled = true);
        document.getElementById('btnAddItem').style.display = 'none';
        const m = document.getElementById('planLockMsg');
        m.textContent = msg;
        m.style.display = 'block';
    }

    // op = null → criar | op = {id, status, navio, items:[…], …} → editar
    function abrirOperacaoModal(op = null) {
        const f = document.getElementById('formOperacao');
        f.reset();
        document.getElementById('metaRows').innerHTML = '';
        document.getElementById('itemRows').innerHTML = '';
        document.getElementById('btnAddItem').style.display = '';
        document.getElementById('planLockMsg').style.display = 'none';
        document.getElementById('motivoWrap').style.display = 'none';
        document.getElementById('op_motivo').required = false;
        itemIdx = 0;
        transp = (op && op.transportadoras ? op.transportadoras : []).map(t => ({ nome: t.nome, extra: !!t.extra }));
        document.getElementById('transpInput').value = '';
        renderTransp();

        const campos = ['navio','empresa','bl','factura','data_chegada','observacoes'];

        if (op) {
            document.getElementById('opTitulo').textContent = 'Editar Operação';
            f.action = OP_UPDATE_URL + op.id;
            document.getElementById('opMethod').disabled = false;
            campos.forEach(c => document.getElementById('op_' + c).value = op[c] ?? '');
            Object.entries(op.metadata || {}).forEach(([k, v]) => addMetaRow(k, v));

            (op.items && op.items.length ? op.items : [{}]).forEach(addItemRow);

            if (op.status === 'active') {
                if (OP_IS_SUPER) {
                    document.getElementById('motivoWrap').style.display = 'block';
                    document.getElementById('op_motivo').required = true;
                    const m = document.getElementById('planLockMsg');
                    m.textContent = 'A descarga já foi iniciada. Alterar o plano exige um motivo e fica registado.';
                    m.style.display = 'block';
                } else {
                    bloquearPlano('A descarga já foi iniciada: o plano está bloqueado. Só o super-admin o pode alterar.');
                }
            }
        } else {
            document.getElementById('opTitulo').textContent = 'Nova Operação';
            f.action = OP_STORE_URL;
            document.getElementById('opMethod').disabled = true;
            addItemRow({ nome: 'Alta',  unidade: 'saco', peso_unitario_kg: 1200 });
            addItemRow({ nome: 'Baixa', unidade: 'saco', peso_unitario_kg: 1000 });
            addMetaRow();
        }

        calcPlano();
        new bootstrap.Modal(document.getElementById('modalOperacao')).show();
    }
</script>
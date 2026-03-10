<x-app-layout>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    .table-card{background:#fff;border-radius:14px;border:1px solid #e9ecef;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.04)}
    #suppliersTable thead tr{background:#f8f9fa}
    #suppliersTable thead th{font-size:.67rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#868e96;border-bottom:1px solid #e9ecef;padding:8px 12px;white-space:nowrap}
    #suppliersTable tbody td{padding:9px 12px;vertical-align:middle;border-bottom:1px solid #f1f3f5;font-size:.82rem}
    #suppliersTable tbody tr:hover{background:#f8f9ff}
    #suppliersTable tbody tr:last-child td{border-bottom:none}
    .kpi-card{background:#fff;border-radius:14px;border:1px solid #e9ecef;padding:16px 20px;box-shadow:0 2px 8px rgba(0,0,0,.04);transition:transform .15s,box-shadow .15s;height:100%;position:relative;overflow:hidden}
    .kpi-card:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,0,0,.08)}
    .kpi-card::before{content:'';position:absolute;left:0;top:0;bottom:0;width:4px;border-radius:14px 0 0 14px}
    .kpi-card.blue::before{background:#1a56db}.kpi-card.green::before{background:#198754}
    .kpi-card.teal::before{background:#0dcaf0}.kpi-card.purple::before{background:#6f42c1}
    .kpi-label{font-size:.65rem;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:#adb5bd;margin-bottom:6px}
    .kpi-value{font-size:1.7rem;font-weight:800;line-height:1;margin-bottom:2px}
    .kpi-sub{font-size:.72rem;color:#adb5bd}
    .kpi-icon{position:absolute;right:16px;top:50%;transform:translateY(-50%);font-size:2rem;opacity:.1}
    .action-btn{width:28px;height:28px;border-radius:7px;display:inline-flex;align-items:center;justify-content:center;font-size:.72rem;border:1px solid;transition:all .15s;text-decoration:none;cursor:pointer;background:transparent}
    .code-badge{display:inline-flex;align-items:center;background:#e7f1ff;color:#1a56db;border:1px solid #b6d0ff;border-radius:6px;padding:2px 8px;font-size:.68rem;font-weight:700;letter-spacing:.5px}
    .modal-loading{display:flex;align-items:center;justify-content:center;height:300px;flex-direction:column;gap:12px;color:#adb5bd;}
    div.dataTables_wrapper div.dataTables_filter input{border-radius:8px;border:1px solid #dee2e6;padding:6px 12px;font-size:.82rem}
    div.dataTables_wrapper div.dataTables_length select{border-radius:8px;border:1px solid #dee2e6;padding:4px 8px;font-size:.82rem}
</style>

<div class="container-fluid py-4 px-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-building text-primary me-2"></i>Fornecedores</h4>
            <p class="text-muted small mb-0">Gestão centralizada de parceiros e fornecedores</p>
        </div>
        <button class="btn btn-primary btn-sm fw-bold px-3 shadow-sm"
                onclick="abrirModalFornecedor()">
            <i class="bi bi-plus-lg me-1"></i> Novo Fornecedor
        </button>
    </div>

    {{-- TOAST --}}
    @if(session('success'))
    <div class="position-fixed top-0 end-0 p-3" style="z-index:9999;">
        <div id="successToast" class="toast show border-0"
             style="border-radius:10px;min-width:260px;overflow:hidden;
                    background:linear-gradient(135deg,rgba(25,135,84,.92),rgba(32,201,151,.92));
                    backdrop-filter:blur(16px);border:1px solid rgba(255,255,255,.25)!important;
                    box-shadow:0 8px 32px rgba(25,135,84,.3);">
            <div class="d-flex align-items-center gap-3 px-3 py-3">
                <i class="bi bi-check-circle-fill text-white" style="font-size:1.4rem;"></i>
                <div style="font-size:.78rem;color:rgba(255,255,255,.95);line-height:1.4;">{{ session('success') }}</div>
                <button type="button" class="btn-close btn-close-white opacity-75 ms-auto" data-bs-dismiss="toast" style="font-size:.55rem;"></button>
            </div>
            <div style="height:2px;background:rgba(255,255,255,.15);overflow:hidden;">
                <div id="toastProgress" style="height:100%;width:100%;background:rgba(255,255,255,.6);transition:width 3.5s linear;"></div>
            </div>
        </div>
    </div>
    @endif

    {{-- KPI CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="kpi-card blue">
                <div class="kpi-label">Total Fornecedores</div>
                <div class="kpi-value" style="color:#1a56db;">{{ $suppliers->count() }}</div>
                <div class="kpi-sub">registados</div>
                <i class="bi bi-building kpi-icon" style="color:#1a56db;"></i>
            </div>
        </div>
        

    {{-- TABELA --}}
    <div class="table-card">
        <div class="p-3">
            <table class="table table-hover align-middle mb-0" id="suppliersTable" style="width:100%">
                <thead>
                    <tr>
                        <th>CÓDIGO</th>
                        <th>FORNECEDOR</th>
                        <th>NUIT</th>
                        <th>CONTACTO / EMAIL</th>
                        <th class="text-center">AÇÕES</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($suppliers as $s)
                <tr>
                    <td>
                        <span class="code-badge">{{ $s->code }}</span>
                    </td>
                    <td>
                        <span style="font-size:.82rem;font-weight:600;">{{ $s->name }}</span><br>
                        <span class="text-muted" style="font-size:.72rem;">{{ Str::limit($s->address, 35) }}</span>
                    </td>
                    <td>
                        <span style="font-size:.78rem;">{{ $s->nuit ?? '—' }}</span>
                    </td>
                    <td>
                        <div style="font-size:.78rem;">
                            <i class="bi bi-telephone me-1 text-muted" style="font-size:.65rem;"></i>{{ $s->contact ?? '—' }}<br>
                            <i class="bi bi-envelope me-1 text-muted" style="font-size:.65rem;"></i>
                            <span class="text-muted">{{ $s->email ?? '—' }}</span>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            {{-- VER --}}
                            <button type="button"
                                class="action-btn text-info border-info border-opacity-25"
                                onclick="verFornecedor({{ json_encode(['code'=>$s->code,'name'=>$s->name,'nuit'=>$s->nuit,'contact'=>$s->contact,'email'=>$s->email,'address'=>$s->address,'metadata'=>$s->metadata]) }})"
                                title="Ver detalhes">
                                <i class="bi bi-eye"></i>
                            </button>
                            {{-- EDITAR --}}
                            <button type="button"
                                class="action-btn text-warning border-warning border-opacity-25"
                                onclick="editarFornecedor({{ json_encode(['id'=>$s->id,'code'=>$s->code,'name'=>$s->name,'nuit'=>$s->nuit,'contact'=>$s->contact,'email'=>$s->email,'address'=>$s->address,'metadata'=>$s->metadata]) }})"
                                title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>
                            {{-- ELIMINAR --}}
                            <form action="{{ route('suppliers.destroy', $s->id) }}" method="POST" class="m-0">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="action-btn text-danger border-danger border-opacity-25"
                                    onclick="return confirm('Eliminar o fornecedor \'{{ $s->name }}\'?')"
                                    title="Eliminar">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ═══ MODAL UNIVERSAL ═══ --}}
<div class="modal fade" id="modalFornecedor" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header border-0 text-white" id="modalFornecedorHeader"
                 style="background:linear-gradient(135deg,#1a56db,#0dcaf0);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi" id="modalFornecedorIcon" style="font-size:1.1rem;"></i>
                    <h5 class="modal-title fw-bold mb-0" id="modalFornecedorTitulo"></h5>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="modalFornecedorBody" style="min-height:300px;"></div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function(){
    $('#suppliersTable').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json' },
        pageLength: 15,
        order: [[1, 'asc']],
        columnDefs: [{ orderable: false, targets: [4] }],
    });

    @if(session('success'))
    const toastEl = document.getElementById('successToast');
    new bootstrap.Toast(toastEl, { delay: 3500 }).show();
    setTimeout(()=>{ document.getElementById('toastProgress').style.width='0%'; }, 50);
    toastEl.style.opacity='0'; toastEl.style.transform='translateX(60px) scale(0.95)';
    toastEl.style.transition='all 0.5s cubic-bezier(0.34,1.56,0.64,1)';
    setTimeout(()=>{ toastEl.style.opacity='1'; toastEl.style.transform='translateX(0) scale(1)'; }, 50);
    @endif
});

// ── Abrir modal vazio para CRIAR ──
function abrirModalFornecedor() {
    setModalHeader('Novo Fornecedor', 'plus-circle', 'linear-gradient(135deg,#1a56db,#0dcaf0)');
    document.getElementById('modalFornecedorBody').innerHTML = htmlFormCriar();
    document.activeElement.blur();
    new bootstrap.Modal(document.getElementById('modalFornecedor')).show();
    bindMetaButtons('meta-container', 'btn-add-meta');
}

// ── Abrir modal para VER ──
function verFornecedor(s) {
    setModalHeader('Detalhes do Fornecedor', 'building', 'linear-gradient(135deg,#1a1a2e,#0d3b2e)');
    document.getElementById('modalFornecedorBody').innerHTML = htmlView(s);
    document.activeElement.blur();
    new bootstrap.Modal(document.getElementById('modalFornecedor')).show();
}

// ── Abrir modal para EDITAR ──
function editarFornecedor(s) {
    setModalHeader('Editar Fornecedor', 'pencil-square', 'linear-gradient(135deg,#fd7e14,#ffc107)');
    document.getElementById('modalFornecedorBody').innerHTML = htmlFormEditar(s);
    document.activeElement.blur();
    new bootstrap.Modal(document.getElementById('modalFornecedor')).show();
    bindMetaButtons('meta-container-edit', 'btn-add-meta-edit');
    // Preencher metadata
    const container = document.getElementById('meta-container-edit');
    if (s.metadata && Object.keys(s.metadata).length > 0) {
        Object.entries(s.metadata).forEach(([k, v]) => {
            container.insertAdjacentHTML('beforeend', metaRow(k, v));
        });
    }
    bindRemoveMeta();
}

function setModalHeader(titulo, icon, gradient) {
    document.getElementById('modalFornecedorHeader').style.background = gradient;
    document.getElementById('modalFornecedorIcon').className = `bi bi-${icon}`;
    document.getElementById('modalFornecedorTitulo').textContent = titulo;
}

// ── HTML do formulário CRIAR ──
function htmlFormCriar() {
    return `
    <form action="{{ route('suppliers.store') }}" method="POST">
        @csrf
        ${camposFormulario()}
        <div class="mb-3">
            <label class="small fw-bold text-muted mb-2">Campos Extra (Metadata)</label>
            <div id="meta-container"></div>
            <button type="button" id="btn-add-meta" class="btn btn-outline-secondary btn-sm mt-1">
                <i class="bi bi-plus me-1"></i>Adicionar campo
            </button>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
            <button type="button" class="btn btn-light border px-4"
                    onclick="bootstrap.Modal.getInstance(document.getElementById('modalFornecedor')).hide()">
                Cancelar
            </button>
            <button type="submit" class="btn btn-primary px-5 fw-bold">
                <i class="bi bi-check-circle me-1"></i>Guardar
            </button>
        </div>
    </form>`;
}

// ── HTML do formulário EDITAR ──
function htmlFormEditar(s) {
    return `
    <form action="/suppliers/${s.id}" method="POST" id="formEditSupplier">
        @csrf
        @method('PUT')
        ${camposFormulario(s)}
        <div class="mb-3">
            <label class="small fw-bold text-muted mb-2">Campos Extra (Metadata)</label>
            <div id="meta-container-edit"></div>
            <button type="button" id="btn-add-meta-edit" class="btn btn-outline-secondary btn-sm mt-1">
                <i class="bi bi-plus me-1"></i>Adicionar campo
            </button>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
            <button type="button" class="btn btn-light border px-4"
                    onclick="bootstrap.Modal.getInstance(document.getElementById('modalFornecedor')).hide()">
                Cancelar
            </button>
            <button type="submit" class="btn btn-warning px-5 fw-bold">
                <i class="bi bi-check-circle me-1"></i>Guardar Alterações
            </button>
        </div>
    </form>`;
}

// ── Campos partilhados entre criar e editar ──
function camposFormulario(s = {}) {
    return `
    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3"><i class="bi bi-card-list me-1"></i> Identificação</h6>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="small fw-bold text-muted">Código</label>
            <input type="text" name="code" class="form-control" value="${s.code||''}" placeholder="Ex: FRN-001" required>
        </div>
        <div class="col-md-8">
            <label class="small fw-bold text-muted">Nome do Fornecedor</label>
            <input type="text" name="name" class="form-control" value="${s.name||''}" placeholder="Nome completo ou empresa" required>
        </div>
        <div class="col-md-4">
            <label class="small fw-bold text-muted">NUIT</label>
            <input type="text" name="nuit" class="form-control" value="${s.nuit||''}" placeholder="Ex: 123456789">
        </div>
        <div class="col-md-4">
            <label class="small fw-bold text-muted">Contacto</label>
            <input type="text" name="contact" class="form-control" value="${s.contact||''}" placeholder="Ex: +258 84 000 0000">
        </div>
        <div class="col-md-4">
            <label class="small fw-bold text-muted">Email</label>
            <input type="email" name="email" class="form-control" value="${s.email||''}" placeholder="email@empresa.com">
        </div>
        <div class="col-12">
            <label class="small fw-bold text-muted">Morada</label>
            <input type="text" name="address" class="form-control" value="${s.address||''}" placeholder="Endereço completo">
        </div>
    </div>`;
}

// ── HTML da vista de detalhes ──
function htmlView(s) {
    const metaHtml = s.metadata && Object.keys(s.metadata).length > 0
        ? Object.entries(s.metadata).map(([k,v]) => `
            <div class="d-flex justify-content-between border-bottom py-2">
                <span class="text-muted small text-uppercase fw-bold">${k}</span>
                <span class="small fw-bold text-dark">${v}</span>
            </div>`).join('')
        : '<p class="text-center text-muted my-3 small">Sem campos extra.</p>';

    return `
    <div class="row g-4">
        <div class="col-12">
            <h6 class="fw-bold text-primary border-bottom pb-2 mb-3"><i class="bi bi-card-list me-1"></i> Identificação</h6>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="small text-muted fw-bold mb-1">Código</div>
                    <span class="badge" style="background:#e7f1ff;color:#1a56db;border:1px solid #b6d0ff;font-size:.78rem;padding:4px 10px;">${s.code}</span>
                </div>
                <div class="col-md-8">
                    <div class="small text-muted fw-bold mb-1">Nome</div>
                    <div class="fw-bold" style="font-size:.95rem;">${s.name}</div>
                </div>
                <div class="col-md-4">
                    <div class="small text-muted fw-bold mb-1">NUIT</div>
                    <div class="fw-semibold">${s.nuit || '—'}</div>
                </div>
                <div class="col-md-4">
                    <div class="small text-muted fw-bold mb-1">Contacto</div>
                    <div class="fw-semibold"><i class="bi bi-telephone me-1 text-muted"></i>${s.contact || '—'}</div>
                </div>
                <div class="col-md-4">
                    <div class="small text-muted fw-bold mb-1">Email</div>
                    <div class="fw-semibold"><i class="bi bi-envelope me-1 text-muted"></i>${s.email || '—'}</div>
                </div>
                <div class="col-12">
                    <div class="small text-muted fw-bold mb-1">Morada</div>
                    <div class="fw-semibold"><i class="bi bi-geo-alt me-1 text-muted"></i>${s.address || '—'}</div>
                </div>
            </div>
        </div>
        ${Object.keys(s.metadata||{}).length > 0 ? `
        <div class="col-12">
            <h6 class="fw-bold text-primary border-bottom pb-2 mb-3"><i class="bi bi-grid-3x3-gap me-1"></i> Campos Extra</h6>
            ${metaHtml}
        </div>` : ''}
    </div>
    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
        <button type="button" class="btn btn-light border px-4"
                onclick="bootstrap.Modal.getInstance(document.getElementById('modalFornecedor')).hide()">
            <i class="bi bi-x me-1"></i>Fechar
        </button>
        <button type="button" class="btn btn-warning px-4 fw-bold"
                onclick="editarFornecedor(${JSON.stringify(s)})">
            <i class="bi bi-pencil me-1"></i>Editar
        </button>
    </div>`;
}

// ── Metadata helpers ──
function metaRow(k='', v='') {
    return `
    <div class="row g-2 mb-2 align-items-center meta-row">
        <div class="col-5"><input type="text" name="meta_key[]" value="${k}" class="form-control form-control-sm" placeholder="Chave"></div>
        <div class="col-5"><input type="text" name="meta_value[]" value="${v}" class="form-control form-control-sm" placeholder="Valor"></div>
        <div class="col-2"><button type="button" class="btn btn-sm btn-outline-danger remove-meta w-100"><i class="bi bi-x"></i></button></div>
    </div>`;
}

function bindMetaButtons(containerId, addBtnId) {
    document.getElementById(addBtnId)?.addEventListener('click', function() {
        document.getElementById(containerId).insertAdjacentHTML('beforeend', metaRow());
        bindRemoveMeta();
    });
    bindRemoveMeta();
}

function bindRemoveMeta() {
    document.querySelectorAll('.remove-meta').forEach(btn => {
        btn.onclick = () => btn.closest('.meta-row').remove();
    });
}
</script>

</x-app-layout>
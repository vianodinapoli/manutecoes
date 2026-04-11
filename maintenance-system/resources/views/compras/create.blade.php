<x-app-layout>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    /* ── Cards ── */
    .form-card{background:#fff;border-radius:14px;border:1px solid #e9ecef;box-shadow:0 2px 8px rgba(0,0,0,.04);overflow:hidden;margin-bottom:20px}
    .form-card-header{padding:13px 20px;display:flex;justify-content:space-between;align-items:center}
    .form-card-header.red{background:#c60a1a;border-bottom:none}
    .form-card-header.light{background:#f8f9fa;border-bottom:1px solid #e9ecef}
    .form-card-header h6{margin:0;font-weight:700;font-size:.86rem;display:flex;align-items:center;gap:8px}
    .form-card-body{padding:20px 24px}

    /* ── Labels ── */
    .field-label{font-size:.68rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#6c757d;margin-bottom:5px;display:block}

    /* ── Inputs ── */
    .form-control,.form-select{border-radius:8px;border:1px solid #dee2e6;font-size:.84rem;background:#f8f9fa;color:#1e293b}
    .form-control:focus,.form-select:focus{border-color:#c60a1a;box-shadow:0 0 0 3px rgba(198,10,26,.08);background:#fff}
    .form-control::placeholder{color:#adb5bd}

    /* ── Linhas de itens ── */
    .item-row{padding:10px 14px;margin-bottom:8px;background:#fff;border:1px solid #e9ecef;border-radius:10px;transition:border-color .2s,box-shadow .2s}
    .item-row:hover{border-color:#c60a1a;box-shadow:0 2px 8px rgba(198,10,26,.06)}
    .item-row .col-header{font-size:.65rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#adb5bd;margin-bottom:6px}

    /* ── Botões ── */
    .btn-add-item{background:#c60a1a;color:#fff;border:none;border-radius:8px;padding:7px 16px;font-size:.8rem;font-weight:700;display:inline-flex;align-items:center;gap:6px;transition:background .2s;cursor:pointer}
    .btn-add-item:hover{background:#a30816}
    .btn-add-file{background:transparent;border:1px dashed #c60a1a;color:#c60a1a;border-radius:8px;padding:6px 14px;font-size:.78rem;font-weight:600;display:inline-flex;align-items:center;gap:5px;transition:all .2s;cursor:pointer;text-decoration:none!important;margin-top:8px}
    .btn-add-file:hover{background:#fdf2f2}
    .btn-remove-item{width:28px;height:28px;border-radius:7px;border:1px solid #fecaca;background:#fef2f2;color:#c60a1a;font-size:.85rem;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .15s;flex-shrink:0}
    .btn-remove-item:hover{background:#c60a1a;color:#fff;border-color:#c60a1a}
    .btn-remove-item:disabled{opacity:.35;cursor:not-allowed}
    .btn-remove-file{font-size:.75rem;color:#c60a1a;cursor:pointer;font-weight:600;white-space:nowrap}
    .btn-remove-file:hover{text-decoration:underline}

    /* ── Urgência badge ── */
    .urgencia-normal{border-color:#198754!important;color:#198754!important}
    .urgencia-alta{border-color:#fd7e14!important;color:#fd7e14!important}
    .urgencia-critica{border-color:#c60a1a!important;color:#c60a1a!important}

    /* ── Botão submeter ── */
    .btn-submeter{background:#c60a1a;color:#fff;border:none;border-radius:10px;padding:11px 36px;font-size:.9rem;font-weight:700;display:inline-flex;align-items:center;gap:8px;transition:background .2s;box-shadow:0 4px 12px rgba(198,10,26,.25)}
    .btn-submeter:hover{background:#a30816}

    /* ── Painel info lateral ── */
    .info-panel{background:#fdf2f2;border:1px solid #fecaca;border-radius:10px;padding:16px 18px}
    .info-panel-title{font-size:.65rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#c60a1a;margin-bottom:10px;display:flex;align-items:center;gap:6px}
</style>

<div class="container-fluid py-4 px-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-cart-plus me-2" style="color:#c60a1a;"></i>Requisição de Materiais
            </h4>
            <p class="text-muted small mb-0">Preencha os artigos necessários e submeta para aprovação</p>
        </div>
        <a href="{{ route('compras.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Voltar
        </a>
    </div>

    <form action="{{ route('compras.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- ITENS --}}
        <div class="form-card">
            <div class="form-card-header light">
                <h6 class="text-dark">
                    <i class="bi bi-list-ul" style="color:#c60a1a;"></i> Artigos Requisitados
                </h6>
                <button type="button" id="add-item" class="btn-add-item">
                    <i class="bi bi-plus-lg"></i> Adicionar Artigo
                </button>
            </div>

            <div class="form-card-body">

                {{-- Cabeçalho colunas (desktop) --}}
                <div class="row g-2 mb-1 d-none d-md-flex">
                    <div class="col-md-5"><span class="field-label">Material / Peça</span></div>
                    <div class="col-md-2"><span class="field-label">Qtd.</span></div>
                    <div class="col-md-4"><span class="field-label">Destino</span></div>
                    <div class="col-md-1"></div>
                </div>

                <div id="items-container">
                    <div class="item-row row g-2 align-items-center">
                        <div class="col-md-5">
                            <input type="text" name="items[0][name]" class="form-control form-control-sm"
                                   placeholder="Ex: Filtro de Ar" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="items[0][quantity]" class="form-control form-control-sm text-center"
                                   min="1" value="1" required>
                        </div>
                        <div class="col-md-4">
                            <input list="destinos-list" name="items[0][destino]" class="form-control form-control-sm"
                                   placeholder="Onde aplicar?" required>
                        </div>
                        <div class="col-md-1 d-flex justify-content-end">
                            <button type="button" class="btn-remove-item remove-item" disabled title="Remover">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <datalist id="destinos-list">
                    <option value="Stock">
                    <option value="Máquina">
                    <option value="Oficina">
                </datalist>
            </div>
        </div>

        {{-- DETALHES --}}
        <div class="form-card">
            <div class="form-card-header red">
                <h6 class="text-white"><i class="bi bi-clipboard-text"></i> Detalhes da Requisição</h6>
            </div>
            <div class="form-card-body">
                <div class="row g-3">

                    {{-- Justificação --}}
                    <div class="col-md-8">
                        <label class="field-label" for="description">Descrição / Justificação da Necessidade</label>
                        <textarea name="description" id="description" rows="4" class="form-control"
                                  placeholder="Descreva o motivo da compra, localização da avaria, urgência, etc..."></textarea>
                    </div>

                    {{-- Painel lateral --}}
                    <div class="col-md-4">
                        <div class="info-panel">
                            <div class="info-panel-title"><i class="bi bi-info-circle"></i> Campos adicionais</div>

                            <label class="field-label" for="urgencia">Nível de Urgência</label>
                            <select name="urgencia" id="urgencia" class="form-select form-select-sm mb-3">
                                <option value="Normal" selected>Normal</option>
                                <option value="Alta">Alta (Urgente)</option>
                                <option value="Crítica">Crítica (Carro Parado)</option>
                            </select>

                            <label class="field-label">Depratamento / Função</label>
                            <input type="text" name="fornecedor" class="form-control form-control-sm"
                                   placeholder="Ex: Administração / Ass. RH" required>
                        </div>
                    </div>

                    {{-- Anexos --}}
                    <div class="col-12">
                        <div style="border-top:1px solid #f1f3f5;padding-top:18px;">
                            <label class="field-label"><i class="bi bi-paperclip me-1" style="color:#c60a1a;"></i> Anexar Cotações / Fotos</label>
                            <div id="file-inputs-container">
                                <div class="file-input-group mb-2">
                                    <input type="file" name="quotation_files[]" class="form-control form-control-sm">
                                </div>
                            </div>
                            <button type="button" id="add-file" class="btn-add-file">
                                <i class="bi bi-plus-lg"></i> Adicionar outro ficheiro
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- SUBMETER --}}
        <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted small">
                <i class="bi bi-box-seam me-1" style="color:#c60a1a;"></i>
                <span id="item-count">1</span> artigo(s) adicionado(s)
            </span>
            <button type="submit" class="btn-submeter">
                <i class="bi bi-send-check"></i>
                Submeter Requisição
            </button>
        </div>

    </form>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
let itemIndex = 1;
const container  = document.getElementById('items-container');
const countSpan  = document.getElementById('item-count');
const fileContainer = document.getElementById('file-inputs-container');

// ── Adicionar artigo ──
document.getElementById('add-item').addEventListener('click', function() {
    const html =
        '<div class="item-row row g-2 align-items-center">' +
            '<div class="col-md-5">' +
                '<input type="text" name="items[' + itemIndex + '][name]" class="form-control form-control-sm" placeholder="Ex: Filtro de Ar" required>' +
            '</div>' +
            '<div class="col-md-2">' +
                '<input type="number" name="items[' + itemIndex + '][quantity]" class="form-control form-control-sm text-center" min="1" value="1" required>' +
            '</div>' +
            '<div class="col-md-4">' +
                '<input list="destinos-list" name="items[' + itemIndex + '][destino]" class="form-control form-control-sm" placeholder="Onde aplicar?" required>' +
            '</div>' +
            '<div class="col-md-1 d-flex justify-content-end">' +
                '<button type="button" class="btn-remove-item remove-item" title="Remover"><i class="bi bi-x"></i></button>' +
            '</div>' +
        '</div>';
    container.insertAdjacentHTML('beforeend', html);
    itemIndex++;
    updateCount();
});

// ── Remover artigo ──
container.addEventListener('click', function(e) {
    const btn = e.target.closest('.remove-item');
    if (btn && !btn.disabled) {
        btn.closest('.item-row').remove();
        updateCount();
    }
});

function updateCount() {
    const n = document.querySelectorAll('.item-row').length;
    countSpan.textContent = n;
    // O primeiro botão de remoção fica desactivado se só houver 1 item
    const btns = document.querySelectorAll('.remove-item');
    btns.forEach(function(b, i){ b.disabled = (btns.length === 1); });
}

// ── Adicionar ficheiro ──
document.getElementById('add-file').addEventListener('click', function() {
    const div = document.createElement('div');
    div.className = 'file-input-group d-flex align-items-center gap-2 mb-2';
    div.innerHTML =
        '<input type="file" name="quotation_files[]" class="form-control form-control-sm">' +
        '<span class="btn-remove-file">Remover</span>';
    fileContainer.appendChild(div);
});

fileContainer.addEventListener('click', function(e) {
    if (e.target.classList.contains('btn-remove-file')) {
        e.target.parentElement.remove();
    }
});

// ── Urgência: muda cor do select ──
document.getElementById('urgencia').addEventListener('change', function() {
    this.className = 'form-select form-select-sm mb-3';
    if (this.value === 'Alta')    this.classList.add('urgencia-alta');
    if (this.value === 'Crítica') this.classList.add('urgencia-critica');
    if (this.value === 'Normal')  this.classList.add('urgencia-normal');
});
</script>

</x-app-layout>
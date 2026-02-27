<div class="modal fade" id="modalSupplier" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('suppliers.store') }}" method="POST" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Registar Novo Fornecedor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Código</label>
                        <input type="text" name="code" class="form-control" placeholder="EX: FORN-001" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label small fw-bold">Nome Completo</label>
                        <input type="text" name="name" class="form-control" placeholder="Nome da Empresa" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">NUIT</label>
                        <input type="text" name="nuit" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Contacto</label>
                        <input type="text" name="contact" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Morada</label>
                        <textarea name="address" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="col-12">
                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-primary small">Informações Adicionais (Personalizado)</span>
                            <button type="button" id="btn-add-meta" class="btn btn-xs btn-outline-primary">+ Adicionar Campo</button>
                        </div>
                        <div id="meta-container"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary px-4">Gravar Fornecedor</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalViewSupplier" tabindex="-1">
    <div class="modal-dialog shadow-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title fw-bold">Detalhes do Fornecedor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="display-6 fw-bold text-primary" id="view-code"></div>
                    <div class="h5 fw-bold" id="view-name"></div>
                </div>
                
                <div class="bg-light p-3 rounded-3 mb-3">
                    <div class="row text-sm">
                        <div class="col-6 mb-2">
                            <span class="text-muted d-block small">NUIT</span>
                            <span class="fw-bold" id="view-nuit"></span>
                        </div>
                        <div class="col-6 mb-2">
                            <span class="text-muted d-block small">Contacto</span>
                            <span class="fw-bold" id="view-contact"></span>
                        </div>
                        <div class="col-12">
                            <span class="text-muted d-block small">Email</span>
                            <span class="fw-bold" id="view-email"></span>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="text-muted small d-block">Morada</label>
                    <p class="text-dark small fw-bold" id="view-address"></p>
                </div>

                <div class="border-top pt-3">
                    <h6 class="text-primary fw-bold small mb-2 text-uppercase">Campos Extra</h6>
                    <div id="view-metadata"></div>
                </div>
            </div>
        </div>
    </div>
</div>



// Modais de edição e exclusão seriam semelhantes, com formulários pré-preenchidos para edição e confirmação para exclusão.

<div class="modal fade" id="modalEditSupplier" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="formEditSupplier" method="POST" class="modal-content border-0 shadow">
            @csrf
            @method('PUT')
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold">Editar Fornecedor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Código</label>
                        <input type="text" name="code" id="edit-code" class="form-control" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label small fw-bold">Nome Completo</label>
                        <input type="text" name="name" id="edit-name" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">NUIT</label>
                        <input type="text" name="nuit" id="edit-nuit" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Contacto</label>
                        <input type="text" name="contact" id="edit-contact" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Email</label>
                        <input type="email" name="email" id="edit-email" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Morada</label>
                        <textarea name="address" id="edit-address" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="col-12">
                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-warning small">Campos Extra</span>
                            <button type="button" id="btn-add-meta-edit" class="btn btn-xs btn-outline-warning">+ Adicionar</button>
                        </div>
                        <div id="meta-container-edit"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="submit" class="btn btn-warning px-4 fw-bold">Atualizar Dados</button>
            </div>
        </form>
    </div>
</div>
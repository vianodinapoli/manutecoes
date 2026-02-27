<x-app-layout>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<div class="container-fluid py-4">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark mb-0">Fornecedores</h2>
            <p class="text-muted mb-0">Gestão centralizada de parceiros e fornecedores</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <button class="btn btn-primary shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#modalSupplier">
                <i class="fas fa-plus-circle me-2"></i>Novo Fornecedor
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="suppliersTable" class="table table-hover align-middle" style="width:100%">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Código</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Fornecedor</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">NUIT</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Contacto / Email</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($suppliers as $s)
                        <tr>
                            <td>
                                <span class="badge bg-soft-primary text-primary fw-bold">{{ $s->code }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-0 text-sm fw-bold">{{ $s->name }}</h6>
                                    <span class="text-xs text-muted">{{ Str::limit($s->address, 30) }}</span>
                                </div>
                            </td>
                            <td><span class="text-sm font-weight-bold">{{ $s->nuit ?? '---' }}</span></td>
                            <td>
                                <div class="text-sm">
                                    <i class="fas fa-phone me-1 text-xs"></i> {{ $s->contact }} <br>
                                    <i class="fas fa-envelope me-1 text-xs text-muted"></i> {{ $s->email }}
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-icon btn-sm btn-outline-info btn-view-supplier" 
                                            data-bs-toggle="modal" data-bs-target="#modalViewSupplier"
                                            data-code="{{ $s->code }}" data-name="{{ $s->name }}"
                                            data-nuit="{{ $s->nuit }}" data-contact="{{ $s->contact }}"
                                            data-email="{{ $s->email }}" data-address="{{ $s->address }}"
                                            data-metadata="{{ json_encode($s->metadata) }}">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    <button class="btn btn-icon btn-sm btn-outline-warning btn-edit-supplier" 
        data-bs-toggle="modal" data-bs-target="#modalEditSupplier"
        data-id="{{ $s->id }}"
        data-code="{{ $s->code }}" 
        data-name="{{ $s->name }}"
        data-nuit="{{ $s->nuit }}" 
        data-contact="{{ $s->contact }}"
        data-email="{{ $s->email }}" 
        data-address="{{ $s->address }}"
        data-metadata="{{ json_encode($s->metadata) }}">
    <i class="fas fa-edit"></i>
</button>
                                    
                                    <form action="{{ route('suppliers.destroy', $s->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-icon btn-sm btn-outline-danger" onclick="return confirm('Apagar este fornecedor?')">
                                            <i class="fas fa-trash"></i>
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
</div>

@include('suppliers.modals')

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<style>
    /* Estilos para deixar a tabela com visual "Clean" */
    .bg-soft-primary { background-color: #e7f1ff; }
    .btn-icon { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 8px; }
    .dataTables_wrapper .dataTables_paginate .paginate_button { padding: 0; }
    .table thead th { border-bottom: none; }
</style>

<script>
$(document).ready(function() {
    $('#suppliersTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        pageLength: 10,
        responsive: true
    });

    // Lógica do Modal View (reutilizando a anterior)
    $('.btn-view-supplier').on('click', function() {
        $('#view-code').text($(this).data('code'));
        $('#view-name').text($(this).data('name'));
        $('#view-nuit').text($(this).data('nuit') || '---');
        $('#view-contact').text($(this).data('contact') || '---');
        $('#view-email').text($(this).data('email') || '---');
        $('#view-address').text($(this).data('address') || '---');

        const metaContainer = $('#view-metadata');
        metaContainer.empty();
        const metadata = $(this).data('metadata');
        
        if (metadata && Object.keys(metadata).length > 0) {
            $.each(metadata, function(key, value) {
                metaContainer.append(`
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span class="text-muted small text-uppercase fw-bold">${key}</span>
                        <span class="small fw-bold text-dark">${value}</span>
                    </div>
                `);
            });
        } else {
            metaContainer.html('<p class="text-center text-muted my-3">Sem campos extras.</p>');
        }
    });

    // Lógica para adicionar campos MetaData dinamicamente no Modal de Cadastro
    $('#btn-add-meta').click(function() {
        $('#meta-container').append(`
            <div class="row g-2 mb-2 align-items-center animate__animated animate__fadeIn">
                <div class="col-5"><input type="text" name="meta_key[]" class="form-control form-control-sm" placeholder="Chave"></div>
                <div class="col-5"><input type="text" name="meta_value[]" class="form-control form-control-sm" placeholder="Valor"></div>
                <div class="col-2"><button type="button" class="btn btn-sm btn-danger remove-meta w-100"><i class="fas fa-times"></i></button></div>
            </div>
        `);
    });

    $(document).on('click', '.remove-meta', function() {
        $(this).closest('.row').remove();
    });


    $('.btn-edit-supplier').on('click', function() {
    const id = $(this).data('id');
    const metadata = $(this).data('metadata');
    
    // Define a URL do formulário dinamicamente
    $('#formEditSupplier').attr('action', `/suppliers/${id}`);
    
    // Preenche os campos básicos
    $('#edit-code').val($(this).data('code'));
    $('#edit-name').val($(this).data('name'));
    $('#edit-nuit').val($(this).data('nuit'));
    $('#edit-contact').val($(this).data('contact'));
    $('#edit-email').val($(this).data('email'));
    $('#edit-address').val($(this).data('address'));

    // Preenche Metadata na Edição
    const container = $('#meta-container-edit');
    container.empty();
    
    if (metadata) {
        $.each(metadata, function(key, value) {
            container.append(`
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-5"><input type="text" name="meta_key[]" value="${key}" class="form-control form-control-sm"></div>
                    <div class="col-5"><input type="text" name="meta_value[]" value="${value}" class="form-control form-control-sm"></div>
                    <div class="col-2"><button type="button" class="btn btn-sm btn-danger remove-meta w-100">x</button></div>
                </div>
            `);
        });
    }
});

// Botão para adicionar mais campos no Edit
$('#btn-add-meta-edit').click(function() {
    $('#meta-container-edit').append(`
        <div class="row g-2 mb-2 align-items-center">
            <div class="col-5"><input type="text" name="meta_key[]" class="form-control form-control-sm" placeholder="Chave"></div>
            <div class="col-5"><input type="text" name="meta_value[]" class="form-control form-control-sm" placeholder="Valor"></div>
            <div class="col-2"><button type="button" class="btn btn-sm btn-danger remove-meta w-100">x</button></div>
        </div>
    `);
});
});
</script>
</x-app-layout>
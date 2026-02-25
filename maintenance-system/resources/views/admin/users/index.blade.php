<x-app-layout>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        .container { max-width: 100%; }
        .user-avatar {
            width: 40px; height: 40px;
            background: #e9ecef;
            display: flex; align-items: center; justify-content: center;
            border-radius: 50%; font-weight: bold; color: #495057;
        }
        .table thead th { 
            background-color: #f8f9fa; 
            text-transform: uppercase; 
            font-size: 0.75rem; 
            letter-spacing: 0.5px;
            border-top: none;
        }
        .btn-status { min-width: 130px; border-radius: 8px; font-weight: 500; transition: all 0.2s; }
        .card-stats { border-left: 4px solid #6366f1; }
    </style>

    <div class="container mt-5">
        {{-- Cabeçalho e Stats Rápidas --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h2 class="fw-bold mb-0">👥 Gestão de Utilizadores</h2>
                <p class="text-muted small">Controle de acessos e permissões do sistema</p>
            </div>
            <div class="card shadow-sm border-0 py-2 px-4 bg-white card-stats">
                <div class="small text-muted">Total de Utilizadores</div>
                <div class="h4 fw-bold mb-0">{{ $users->count() + 1 }}</div> {{-- +1 inclui você --}}
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Tabela de Utilizadores --}}
        <div class="card shadow-sm border-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Utilizador</th>
                            <th>Cargo / Nível</th>
                            <th class="text-center">Ações de Segurança</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="user-avatar shadow-sm">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $user->name }}</div>
                                        <div class="text-muted small">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($user->hasRole('super-admin'))
                                    <span class="badge rounded-pill bg-danger-subtle text-danger px-3 py-2 border border-danger-subtle">
                                        <i class="bi bi-shield-check me-1"></i> Super Admin
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-light text-secondary px-3 py-2 border">
                                        <i class="bi bi-person me-1"></i> Utilizador
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    {{-- Botão Alternar Cargo --}}
                                    <form action="{{ route('admin.users.toggle', $user) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-status {{ $user->hasRole('super-admin') ? 'btn-light border text-warning' : 'btn-primary' }}">
                                            <i class="bi {{ $user->hasRole('super-admin') ? 'bi-shield-slash' : 'bi-shield-plus' }} me-1"></i>
                                            {{ $user->hasRole('super-admin') ? 'Remover Admin' : 'Tornar Admin' }}
                                        </button>
                                    </form>

                                    {{-- Botão Eliminar --}}
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('ATENÇÃO: Apagar este utilizador permanentemente?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger border-0" title="Eliminar Conta">
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

        <div class="mt-4 text-center">
            <small class="text-muted">
                <i class="bi bi-info-circle"></i> Nota: Você não pode remover o seu próprio acesso ou eliminar a sua conta nesta página.
            </small>
        </div>
    </div>
</x-app-layout>
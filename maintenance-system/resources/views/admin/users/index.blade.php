<x-app-layout>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
    *, *::before, *::after { box-sizing: border-box; }
    .page-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px; gap:12px; flex-wrap:wrap; }
    .kpi-card { background:#fff; border-radius:12px; border:1px solid #e2e8f0; padding:14px 20px; display:flex; align-items:center; gap:12px; box-shadow:0 1px 4px rgba(0,0,0,.04); border-left:4px solid #6366f1; }
    .kpi-icon { width:36px; height:36px; border-radius:10px; background:#eef2ff; color:#6366f1; display:flex; align-items:center; justify-content:center; font-size:.95rem; flex-shrink:0; }
    .kpi-label { font-size:.58rem; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:#94a3b8; }
    .kpi-value { font-size:1.4rem; font-weight:800; color:#1e293b; line-height:1; }
    .table-card { background:#fff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 4px rgba(0,0,0,.04); overflow:hidden; }
    .table-card-head { padding:16px 20px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; gap:10px; }
    .table-card-icon { width:32px; height:32px; background:#1e293b; border-radius:8px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:.85rem; flex-shrink:0; }
    .usr-table { width:100%; border-collapse:collapse; table-layout:fixed; }
    .usr-table thead th { font-size:.6rem; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:#94a3b8; background:#f8fafc; border-bottom:1px solid #e2e8f0; padding:10px 16px; white-space:nowrap; }
    .usr-table tbody td { font-size:.82rem; color:#334155; padding:12px 16px; border-bottom:1px solid #f1f5f9; vertical-align:middle; }
    .usr-table tbody tr:last-child td { border-bottom:none; }
    .usr-table tbody tr:hover { background:#f8fafc; }
    .avatar-lg { width:38px; height:38px; border-radius:50%; background:#e2e8f0; display:flex; align-items:center; justify-content:center; font-size:.85rem; font-weight:700; color:#475569; flex-shrink:0; }
    .role-badge { display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:20px; font-size:.68rem; font-weight:700; border:1px solid; }
    .role-badge.admin { background:#fef2f2; color:#991b1b; border-color:#fecaca; }
    .role-badge.gestor { background:#fffbeb; color:#92400e; border-color:#fde68a; }
    .role-badge.user  { background:#f1f5f9; color:#475569; border-color:#e2e8f0; }
    .perm-chips { display:flex; flex-wrap:wrap; gap:4px; }
    .perm-chip { display:inline-flex; align-items:center; padding:2px 7px; border-radius:5px; font-size:.62rem; font-weight:600; background:#eff6ff; color:#1a56db; border:1px solid #bfdbfe; white-space:nowrap; }
    .perm-chip.inherited { background:#f0fdf4; color:#166534; border-color:#bbf7d0; }
    .btn-primary-dark { background:#1e293b; color:#fff; border:none; border-radius:8px; padding:8px 16px; font-size:.8rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:6px; transition:background .15s; white-space:nowrap; }
    .btn-primary-dark:hover { background:#334155; }
    .btn-edit { background:#6366f1; color:#fff; border:none; border-radius:7px; padding:6px 12px; font-size:.75rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:5px; transition:background .15s; }
    .btn-edit:hover { background:#4f46e5; }
    .btn-del { background:transparent; border:1px solid #fecaca; border-radius:7px; padding:6px 10px; font-size:.75rem; color:#dc2626; cursor:pointer; transition:all .15s; display:inline-flex; align-items:center; }
    .btn-del:hover { background:#fef2f2; }
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(15,23,42,.5); z-index:1050; align-items:flex-start; justify-content:center; padding:24px 16px; overflow-y:auto; }
    .modal-overlay.show { display:flex; }
    .modal-box { background:#fff; border-radius:16px; width:100%; max-width:600px; box-shadow:0 20px 60px rgba(0,0,0,.2); animation:modalIn .2s ease; margin:auto; }
    @keyframes modalIn { from{opacity:0;transform:translateY(12px) scale(.98)} to{opacity:1;transform:none} }
    .modal-header { padding:20px 24px 0; display:flex; align-items:flex-start; justify-content:space-between; gap:12px; }
    .modal-title { font-size:.95rem; font-weight:800; color:#1e293b; }
    .modal-subtitle { font-size:.72rem; color:#94a3b8; margin-top:2px; }
    .modal-close { background:none; border:none; cursor:pointer; color:#94a3b8; font-size:1.1rem; padding:0; line-height:1; }
    .modal-close:hover { color:#334155; }
    .modal-body { padding:20px 24px; }
    .modal-footer { padding:0 24px 20px; display:flex; justify-content:flex-end; gap:8px; }
    .form-label-sm { font-size:.65rem; font-weight:700; letter-spacing:.8px; text-transform:uppercase; color:#64748b; margin-bottom:4px; display:block; }
    .form-input { width:100%; padding:9px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:.82rem; color:#334155; transition:border-color .15s, box-shadow .15s; background:#fff; }
    .form-input:focus { outline:none; border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,.1); }
    .form-row-2 { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
    .mb-14 { margin-bottom:14px; }
    .modulos-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; }
    .modulo-check { border:1px solid #e2e8f0; border-radius:10px; padding:10px 12px; cursor:pointer; transition:all .15s; display:flex; align-items:center; gap:9px; }
    .modulo-check:hover { border-color:#a5b4fc; background:#f5f3ff; }
    .modulo-check input[type=checkbox] { accent-color:#6366f1; width:15px; height:15px; flex-shrink:0; cursor:pointer; }
    .modulo-check.checked { border-color:#6366f1; background:#eef2ff; }
    .modulo-label { font-size:.72rem; font-weight:600; color:#334155; display:flex; align-items:center; gap:6px; line-height:1.3; }
    .modulo-label i { color:#6366f1; font-size:.75rem; width:14px; flex-shrink:0; }
    .modulos-note { font-size:.68rem; color:#94a3b8; margin-top:6px; font-style:italic; }
    .section-title { font-size:.65rem; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:#94a3b8; margin-bottom:12px; display:flex; align-items:center; gap:6px; }
    .section-title::after { content:''; flex:1; height:1px; background:#f1f5f9; }
    .btn-cancel { background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:8px 18px; font-size:.82rem; font-weight:600; color:#64748b; cursor:pointer; }
    .btn-cancel:hover { background:#f1f5f9; }
    .btn-save { background:#6366f1; color:#fff; border:none; border-radius:8px; padding:8px 20px; font-size:.82rem; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:6px; }
    .btn-save:hover { background:#4f46e5; }
    .btn-save-dark { background:#1e293b; color:#fff; border:none; border-radius:8px; padding:8px 20px; font-size:.82rem; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:6px; }
    .btn-save-dark:hover { background:#334155; }
    .required-star { color:#dc2626; margin-left:2px; }
    @media (max-width:768px) {
        .form-row-2 { grid-template-columns:1fr; }
        .modulos-grid { grid-template-columns:repeat(2,1fr); }
        .usr-table th:nth-child(4), .usr-table td:nth-child(4) { display:none; }
    }
    @media (max-width:480px) {
        .modulos-grid { grid-template-columns:1fr 1fr; }
        .usr-table th:nth-child(3), .usr-table td:nth-child(3) { display:none; }
    }
</style>

<div class="container-fluid py-3 px-3 px-md-4">

    {{-- HEADER --}}
    <div class="page-header">
        <div>
            <div style="font-size:.58rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#94a3b8;margin-bottom:3px;">Administração</div>
            <h4 class="fw-bold mb-1" style="color:#1e293b;font-size:1.25rem;">Gestão de Utilizadores</h4>
            <p style="font-size:.75rem;color:#94a3b8;margin:0;">Controlo de acessos e permissões por módulo</p>
        </div>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
            <div class="kpi-card">
                <div class="kpi-icon"><i class="bi bi-people-fill"></i></div>
                <div>
                    <div class="kpi-label">Total</div>
                    <div class="kpi-value">{{ $users->count() }}</div>
                </div>
            </div>
            <button class="btn-primary-dark" onclick="abrirCriar()">
                <i class="bi bi-person-plus-fill"></i> Novo Utilizador
            </button>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 16px;font-size:.8rem;color:#166534;display:flex;align-items:center;gap:8px;margin-bottom:16px;">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:10px 16px;font-size:.8rem;color:#991b1b;display:flex;align-items:center;gap:8px;margin-bottom:16px;">
        <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
    </div>
    @endif

    {{-- TABELA --}}
    <div class="table-card">
        <div class="table-card-head">
            <div class="table-card-icon"><i class="bi bi-people-fill"></i></div>
            <div>
                <div style="font-size:.82rem;font-weight:700;color:#334155;">Utilizadores do Sistema</div>
                <div style="font-size:.7rem;color:#94a3b8;">Clique em Editar para gerir dados e permissões</div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="usr-table">
                <thead>
                    <tr>
                        <th style="width:220px;">Utilizador</th>
                        <th style="width:160px;">Departamento / Função</th>
                        <th style="width:120px;">Cargo</th>
                        <th>Permissões Activas</th>
                        <th style="width:110px;" class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    @php
                        $isSelf        = $user->id === auth()->id();
                        $isSuperAdmin  = $user->hasRole('super-admin');
                        $isGestor      = $user->hasRole('gestor');
                        $permsDirectas = $user->getDirectPermissions()->pluck('name');
                    @endphp
                    <tr>
                        {{-- Utilizador --}}
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div class="avatar-lg">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                <div>
                                    <div style="font-weight:700;font-size:.85rem;">
                                        {{ $user->name }}
                                        @if($isSelf)<span style="font-size:.62rem;background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;border-radius:4px;padding:1px 6px;margin-left:4px;">Você</span>@endif
                                    </div>
                                    <div style="font-size:.72rem;color:#94a3b8;">{{ $user->email }}</div>
                                    <div style="font-size:.68rem;color:#cbd5e1;margin-top:1px;">
                                        <i class="bi bi-calendar3 me-1"></i>{{ $user->created_at->format('d/m/Y') }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Departamento / Função --}}
                        <td>
                            <div style="font-size:.8rem;font-weight:600;color:#334155;">
                                {{ $user->departamento ?? '—' }}
                            </div>
                            <div style="font-size:.7rem;color:#94a3b8;">
                                {{ $user->funcao ?? '—' }}
                            </div>
                        </td>

                        {{-- Cargo --}}
                        <td>
                            @if($isSuperAdmin)
                                <span class="role-badge admin"><i class="bi bi-shield-check"></i> Super Admin</span>
                            @elseif($isGestor)
                                <span class="role-badge gestor"><i class="bi bi-briefcase"></i> Gestor</span>
                            @else
                                <span class="role-badge user"><i class="bi bi-person"></i> Utilizador</span>
                            @endif
                        </td>

                        {{-- Permissões --}}
                        <td>
                            <div class="perm-chips">
                                @if($isSuperAdmin)
                                    <span class="perm-chip inherited"><i class="bi bi-infinity me-1"></i> Todas as permissões</span>
                                @else
                                    @forelse($permsDirectas as $p)
                                        <span class="perm-chip">{{ $modulos[$p]['label'] ?? $p }}</span>
                                    @empty
                                        <span style="font-size:.72rem;color:#cbd5e1;font-style:italic;">Sem permissões directas</span>
                                    @endforelse
                                @endif
                            </div>
                        </td>

                        {{-- Ações --}}
                        <td class="text-end pe-4">
                            <div style="display:flex;justify-content:flex-end;gap:5px;">
                                <button class="btn-edit" onclick="abrirEditar({{ $user->id }})">
                                    <i class="bi bi-pencil-fill"></i> Editar
                                </button>
                                @if(!$isSelf)
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                      onsubmit="return confirm('Apagar {{ addslashes($user->name) }} permanentemente?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-del" title="Eliminar">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>

                    <script>
                        window._userData = window._userData || {};
                        window._userData[{{ $user->id }}] = {
                            id:           {{ $user->id }},
                            name:         "{{ addslashes($user->name) }}",
                            email:        "{{ addslashes($user->email) }}",
                            departamento: "{{ addslashes($user->departamento ?? '') }}",
                            funcao:       "{{ addslashes($user->funcao ?? '') }}",
                            role:         "{{ $user->roles->first()?->name ?? 'utilizador' }}",
                            isSelf:       {{ $isSelf ? 'true' : 'false' }},
                            isSuperAdmin: {{ $isSuperAdmin ? 'true' : 'false' }},
                            perms:        {!! json_encode($permsDirectas->values()) !!},
                        };
                    </script>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div style="margin-top:12px;text-align:center;font-size:.72rem;color:#94a3b8;">
        <i class="bi bi-info-circle"></i> Super Admin herda automaticamente todas as permissões.
        Permissões directas aplicam-se apenas a utilizadores comuns.
    </div>
</div>

{{-- ═══════════════════════════════════
     MODAL — CRIAR UTILIZADOR
═══════════════════════════════════ --}}
<div class="modal-overlay" id="modalCriar">
    <div class="modal-box">
        <div class="modal-header">
            <div>
                <div class="modal-title"><i class="bi bi-person-plus-fill me-2" style="color:#1e293b;"></i>Novo Utilizador</div>
                <div class="modal-subtitle">Preencha os dados e defina as permissões</div>
            </div>
            <button class="modal-close" onclick="fecharModal('modalCriar')"><i class="bi bi-x-lg"></i></button>
        </div>
        <form method="POST" action="{{ route('admin.users.store') }}" id="formCriar">
            @csrf
            <div class="modal-body">

                <div class="section-title"><i class="bi bi-person-fill"></i> Dados Pessoais</div>

                <div class="form-row-2 mb-14">
                    <div>
                        <label class="form-label-sm">Nome <span class="required-star">*</span></label>
                        <input type="text" name="name" class="form-input" required placeholder="Nome completo"
                               value="{{ old('name') }}">
                    </div>
                    <div>
                        <label class="form-label-sm">Email <span class="required-star">*</span></label>
                        <input type="email" name="email" class="form-input" required placeholder="email@exemplo.com"
                               value="{{ old('email') }}">
                    </div>
                </div>

                <div class="form-row-2 mb-14">
                    <div>
                        <label class="form-label-sm">Departamento</label>
                        <input type="text" name="departamento" class="form-input" placeholder="Ex: Produção"
                               value="{{ old('departamento') }}">
                    </div>
                    <div>
                        <label class="form-label-sm">Função</label>
                        <input type="text" name="funcao" class="form-input" placeholder="Ex: Técnico"
                               value="{{ old('funcao') }}">
                    </div>
                </div>

                <div class="form-row-2 mb-14">
                    <div>
                        <label class="form-label-sm">Password <span class="required-star">*</span></label>
                        <input type="password" name="password" class="form-input" required autocomplete="new-password">
                    </div>
                    <div>
                        <label class="form-label-sm">Confirmar Password <span class="required-star">*</span></label>
                        <input type="password" name="password_confirmation" class="form-input" required autocomplete="new-password">
                    </div>
                </div>

                <div class="section-title"><i class="bi bi-shield-fill"></i> Cargo / Role</div>

                <div class="mb-14">
                    <label class="form-label-sm">Cargo <span class="required-star">*</span></label>
                    <select name="role" id="criar-role" class="form-input" onchange="togglePermissoesCriar(this.value)">
                        @foreach($roles as $role)
                        <option value="{{ $role->name }}">{{ ucfirst(str_replace('-', ' ', $role->name)) }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="permissoes-section-criar">
                    <div class="section-title"><i class="bi bi-key-fill"></i> Permissões por Módulo</div>
                    <div class="modulos-grid">
                        @foreach($modulos as $perm => $info)
                        <label class="modulo-check">
                            <input type="checkbox" name="permissoes[]" value="{{ $perm }}"
                                   onchange="this.closest('.modulo-check').classList.toggle('checked', this.checked)">
                            <span class="modulo-label">
                                <i class="fas {{ $info['icon'] }}"></i>
                                {{ $info['label'] }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                    <p class="modulos-note">
                        <i class="bi bi-info-circle"></i>
                        Super Admin tem acesso a tudo automaticamente.
                    </p>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="fecharModal('modalCriar')">Cancelar</button>
                <button type="submit" class="btn-save-dark"><i class="bi bi-person-plus-fill"></i> Criar Utilizador</button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════
     MODAL — EDITAR UTILIZADOR
═══════════════════════════════════ --}}
<div class="modal-overlay" id="modalEditar">
    <div class="modal-box">
        <div class="modal-header">
            <div>
                <div class="modal-title"><i class="bi bi-person-gear me-2" style="color:#6366f1;"></i>Editar Utilizador</div>
                <div class="modal-subtitle" id="modal-subtitle">Dados e permissões de acesso</div>
            </div>
            <button class="modal-close" onclick="fecharModal('modalEditar')"><i class="bi bi-x-lg"></i></button>
        </div>
        <form method="POST" id="formEditar">
            @csrf
            @method('PUT')
            <div class="modal-body">

                <div class="section-title"><i class="bi bi-person-fill"></i> Dados Pessoais</div>

                <div class="form-row-2 mb-14">
                    <div>
                        <label class="form-label-sm">Nome <span class="required-star">*</span></label>
                        <input type="text" name="name" id="edit-name" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label-sm">Email <span class="required-star">*</span></label>
                        <input type="email" name="email" id="edit-email" class="form-input" required>
                    </div>
                </div>

                <div class="form-row-2 mb-14">
                    <div>
                        <label class="form-label-sm">Departamento</label>
                        <input type="text" name="departamento" id="edit-departamento" class="form-input" placeholder="Ex: Produção">
                    </div>
                    <div>
                        <label class="form-label-sm">Função</label>
                        <input type="text" name="funcao" id="edit-funcao" class="form-input" placeholder="Ex: Técnico">
                    </div>
                </div>

                <div class="form-row-2 mb-14">
                    <div>
                        <label class="form-label-sm">Nova Password <span style="font-weight:400;text-transform:none;letter-spacing:0;color:#94a3b8;">(vazio = não altera)</span></label>
                        <input type="password" name="password" id="edit-password" class="form-input" autocomplete="new-password">
                    </div>
                    <div>
                        <label class="form-label-sm">Confirmar Password</label>
                        <input type="password" name="password_confirmation" class="form-input" autocomplete="new-password">
                    </div>
                </div>

                <div class="section-title"><i class="bi bi-shield-fill"></i> Cargo / Role</div>

                <div class="mb-14">
                    <label class="form-label-sm">Cargo</label>
                    <select name="role" id="edit-role" class="form-input" onchange="togglePermissoesEditar(this.value)">
                        @foreach($roles as $role)
                        <option value="{{ $role->name }}">{{ ucfirst(str_replace('-', ' ', $role->name)) }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="permissoes-section-editar">
                    <div class="section-title"><i class="bi bi-key-fill"></i> Permissões por Módulo</div>
                    <div class="modulos-grid">
                        @foreach($modulos as $perm => $info)
                        <label class="modulo-check">
                            <input type="checkbox" name="permissoes[]" value="{{ $perm }}"
                                   onchange="this.closest('.modulo-check').classList.toggle('checked', this.checked)">
                            <span class="modulo-label">
                                <i class="fas {{ $info['icon'] }}"></i>
                                {{ $info['label'] }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                    <p class="modulos-note">
                        <i class="bi bi-info-circle"></i>
                        Super Admin tem acesso a tudo automaticamente — as checkboxes são ignoradas.
                    </p>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="fecharModal('modalEditar')">Cancelar</button>
                <button type="submit" class="btn-save"><i class="bi bi-check-lg"></i> Guardar Alterações</button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirCriar() {
        document.getElementById('formCriar').reset();
        document.querySelectorAll('#modalCriar .modulo-check').forEach(function(el) {
            el.classList.remove('checked');
        });
        togglePermissoesCriar(document.getElementById('criar-role').value);
        document.getElementById('modalCriar').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function abrirEditar(userId) {
        var u = window._userData[userId];
        if (!u) return;

        document.getElementById('formEditar').action = '/admin/users/' + u.id;
        document.getElementById('edit-name').value         = u.name;
        document.getElementById('edit-email').value        = u.email;
        document.getElementById('edit-departamento').value = u.departamento || '';
        document.getElementById('edit-funcao').value       = u.funcao       || '';
        document.getElementById('edit-password').value     = '';
        document.getElementById('modal-subtitle').textContent = u.email;
        document.getElementById('edit-role').value         = u.role;

        // Desmarcar tudo
        document.querySelectorAll('#modalEditar [name="permissoes[]"]').forEach(function(cb) {
            cb.checked = false;
            cb.closest('.modulo-check').classList.remove('checked');
        });
        // Marcar as do utilizador
        u.perms.forEach(function(p) {
            var cb = document.querySelector('#modalEditar [name="permissoes[]"][value="' + p + '"]');
            if (cb) { cb.checked = true; cb.closest('.modulo-check').classList.add('checked'); }
        });

        togglePermissoesEditar(u.role);
        document.getElementById('modalEditar').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function fecharModal(id) {
        document.getElementById(id).classList.remove('show');
        document.body.style.overflow = '';
    }

    function togglePermissoesCriar(role) { _togglePerms('#permissoes-section-criar', role); }
    function togglePermissoesEditar(role) { _togglePerms('#permissoes-section-editar', role); }

    function _togglePerms(sectionSelector, role) {
        var inputs = document.querySelectorAll(sectionSelector + ' input[type=checkbox]');
        if (role === 'super-admin') {
            inputs.forEach(function(cb) {
                cb.disabled = true;
                cb.checked  = true;
                cb.closest('.modulo-check').classList.add('checked');
                cb.closest('.modulo-check').style.opacity = '.5';
                cb.closest('.modulo-check').style.cursor  = 'not-allowed';
            });
        } else {
            inputs.forEach(function(cb) {
                cb.disabled = false;
                cb.closest('.modulo-check').style.opacity = '1';
                cb.closest('.modulo-check').style.cursor  = 'pointer';
            });
        }
    }

    ['modalCriar','modalEditar'].forEach(function(id) {
        document.getElementById(id).addEventListener('click', function(e) {
            if (e.target === this) fecharModal(id);
        });
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { ['modalCriar','modalEditar'].forEach(fecharModal); }
    });

    @if($errors->any() && old('_modal') === 'criar')
        document.addEventListener('DOMContentLoaded', function() { abrirCriar(); });
    @endif
</script>

</x-app-layout>
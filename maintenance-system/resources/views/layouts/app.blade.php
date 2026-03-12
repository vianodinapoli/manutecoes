<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>BYMOZE - SG</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        #sidebar {
            transition: all 0.3s ease-in-out;
            min-height: 100vh;
            z-index: 1000;
            position: relative;
        }

        .sidebar-expanded { width: 260px !important; }
        .sidebar-collapsed { width: 80px !important; }

        .sidebar-collapsed .nav-text,
        .sidebar-collapsed .sidebar-title,
        .sidebar-collapsed .admin-label,
        .sidebar-collapsed .nav-section-label {
            display: none;
        }

        .sidebar-collapsed .nav-item {
            justify-content: center;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        .sidebar-collapsed .nav-item i {
            margin-right: 0 !important;
            font-size: 1.25rem;
        }

        /* ── Itens base ── */
        .nav-item,
        .nav-dropdown-trigger {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: #d1d5db;
            transition: all 0.2s;
            text-decoration: none !important;
            border-radius: 0.25rem;
        }

        .nav-item:hover,
        .nav-dropdown-trigger:hover {
            background-color: #374151;
            color: white;
        }

        .nav-item i,
        .nav-dropdown-trigger i.icon-main {
            width: 20px;
            text-align: center;
            margin-right: 12px;
            flex-shrink: 0;
        }

        /* ── Chevron ── */
        .nav-dropdown-trigger {
            cursor: pointer;
            user-select: none;
        }

        .nav-dropdown-trigger .chevron {
            margin-left: auto;
            font-size: 0.7rem;
            transition: transform 0.3s ease;
            flex-shrink: 0;
        }

        /* ── Subitens expandidos ── */
        .nav-dropdown-items {
            margin-left: 1rem;
            border-left: 2px solid #4b5563;
            padding-left: 0.5rem;
            margin-top: 0.25rem;
        }

        .nav-dropdown-items .nav-item {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }

        .nav-dropdown-items .nav-item i {
            width: 16px;
            font-size: 0.8rem;
        }

        /* ── Sidebar comprimida: dropdown como flyout ── */
        .sidebar-collapsed .nav-dropdown-trigger {
            justify-content: center;
            padding-left: 0 !important;
            padding-right: 0 !important;
            position: relative;
        }

        .sidebar-collapsed .nav-dropdown-trigger .nav-text,
        .sidebar-collapsed .nav-dropdown-trigger .chevron {
            display: none;
        }

        .sidebar-collapsed .nav-dropdown-trigger i.icon-main {
            margin-right: 0;
            font-size: 1.25rem;
        }

        /* flyout panel */
        .nav-flyout {
            display: none;
            position: absolute;
            left: 80px;
            top: 0;
            background: #1f2937;
            border: 1px solid #374151;
            border-radius: 8px;
            min-width: 200px;
            z-index: 9999;
            box-shadow: 4px 4px 16px rgba(0,0,0,.35);
            padding: 6px 0;
        }

        .nav-flyout-label {
            font-size: .65rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #6b7280;
            padding: 6px 14px 4px;
        }

        .nav-flyout a {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            font-size: .83rem;
            color: #d1d5db;
            text-decoration: none;
            transition: background .15s;
        }

        .nav-flyout a:hover {
            background: #374151;
            color: #fff;
        }

        .nav-flyout a i {
            width: 16px;
            font-size: .8rem;
            text-align: center;
        }

        /* wrapper posicionado para flyout */
        .nav-dropdown-wrap {
            position: relative;
        }

        .sidebar-collapsed .nav-dropdown-wrap:hover .nav-flyout {
            display: block;
        }

        /* quando expandido, esconde flyout */
        .sidebar-expanded .nav-flyout {
            display: none !important;
        }
    </style>
</head>
<body class="font-sans antialiased">

    <div class="min-h-screen bg-gray-100">

        @include('layouts.navigation')

        <div class="flex">

            <aside id="sidebar" class="sidebar-expanded bg-gray-800 text-white flex-shrink-0 shadow-lg">

                <div class="p-4 flex items-center justify-between border-b border-gray-700">
                    <span class="sidebar-title font-bold text-lg overflow-hidden whitespace-nowrap">SG | BYMOZE</span>
                    <button id="toggleBtn" class="p-1 hover:bg-gray-700 rounded text-white outline-none">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>

                {{-- 
    SUBSTITUI o bloco <nav class="mt-4 px-2 space-y-1"> ... </nav>
    no ficheiro resources/views/layouts/app.blade.php
--}}

<nav class="mt-4 px-2 space-y-1">

    {{-- Dashboard --}}
    @can('acesso dashboard')
    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="nav-item rounded">
        <i class="fas fa-home"></i>
        <span class="nav-text">Dashboard</span>
    </x-nav-link>
    @endcan

    {{-- Equipamentos --}}
    @can('acesso equipamentos')
    <x-nav-link :href="route('machines.index')" :active="request()->routeIs('machines.*')" class="nav-item rounded">
        <i class="fas fa-tools"></i>
        <span class="nav-text">Equipamento/Máquinas</span>
    </x-nav-link>
    @endcan

    {{-- Manutenções --}}
    @can('acesso manutencoes')
    <div class="nav-dropdown-wrap"
         x-data="{ open: {{ request()->routeIs('maintenances.*') ? 'true' : 'false' }} }">

        <div @click="open = !open" class="nav-dropdown-trigger">
            <i class="fas fa-wrench icon-main"></i>
            <span class="nav-text">Manutenções</span>
            <i class="fas fa-chevron-right chevron" :style="open ? 'transform:rotate(90deg)' : ''"></i>
        </div>

        <div x-show="open" x-cloak x-collapse
             x-transition:enter="transition ease-out duration-200"
             class="nav-dropdown-items">
            <x-nav-link :href="route('maintenances.create')"
                        :active="request()->routeIs('maintenances.create')"
                        class="nav-item rounded">
                <i class="fas fa-plus-circle"></i>
                <span class="nav-text">Criar Manutenção</span>
            </x-nav-link>
            <x-nav-link :href="route('maintenances.index')"
                        :active="request()->routeIs('maintenances.index')"
                        class="nav-item rounded">
                <i class="fas fa-list"></i>
                <span class="nav-text">Ver Manutenções</span>
            </x-nav-link>
        </div>

        <div class="nav-flyout">
            <div class="nav-flyout-label">Manutenções</div>
            <a href="{{ route('maintenances.create') }}"><i class="fas fa-plus-circle"></i> Criar Manutenção</a>
            <a href="{{ route('maintenances.index') }}"><i class="fas fa-list"></i> Ver Manutenções</a>
        </div>
    </div>
    @endcan

    {{-- Stock / Armazém --}}
    @canany(['acesso stock', 'acesso movimentos'])
    <div class="nav-dropdown-wrap"
         x-data="{ open: {{ request()->routeIs('stock-items.*', 'movimentos.*') ? 'true' : 'false' }} }">

        <div @click="open = !open" class="nav-dropdown-trigger">
            <i class="fas fa-boxes icon-main"></i>
            <span class="nav-text">Stock / Armazém</span>
            <i class="fas fa-chevron-right chevron" :style="open ? 'transform:rotate(90deg)' : ''"></i>
        </div>

        <div x-show="open" x-cloak x-collapse
             x-transition:enter="transition ease-out duration-200"
             class="nav-dropdown-items">
            @can('acesso stock')
            <x-nav-link :href="route('stock-items.index')"
                        :active="request()->routeIs('stock-items.*')"
                        class="nav-item rounded">
                <i class="fas fa-boxes"></i>
                <span class="nav-text">Stock</span>
            </x-nav-link>
            @endcan
            @can('acesso movimentos')
            <x-nav-link :href="route('movimentos.index')"
                        :active="request()->routeIs('movimentos.*')"
                        class="nav-item rounded">
                <i class="fas fa-arrow-left-right"></i>
                <span class="nav-text">Movimentos</span>
            </x-nav-link>
            @endcan
        </div>

        <div class="nav-flyout">
            <div class="nav-flyout-label">Stock / Armazém</div>
            @can('acesso stock')
            <a href="{{ route('stock-items.index') }}"><i class="fas fa-boxes"></i> Stock</a>
            @endcan
            @can('acesso movimentos')
            <a href="{{ route('movimentos.index') }}"><i class="fas fa-arrow-left-right"></i> Movimentos</a>
            @endcan
        </div>
    </div>
    @endcanany

    {{-- Pedidos / Requisições --}}
    @can('acesso pedidos')
    <div class="nav-dropdown-wrap"
         x-data="{ open: {{ request()->routeIs('suppliers.*', 'compras.*', 'requisicao.*', 'requisicoes.*') ? 'true' : 'false' }} }">

        <div @click="open = !open" class="nav-dropdown-trigger">
            <i class="fas fa-shopping-cart icon-main"></i>
            <span class="nav-text">Pedidos/Requisições</span>
            <i class="fas fa-chevron-right chevron" :style="open ? 'transform:rotate(90deg)' : ''"></i>
        </div>

        <div x-show="open" x-cloak x-collapse
             x-transition:enter="transition ease-out duration-200"
             class="nav-dropdown-items">
            <x-nav-link :href="route('compras.index')"      :active="request()->routeIs('compras.index')"  class="nav-item rounded">
                <i class="fas fa-list-ul"></i><span class="nav-text">Pedidos internos</span>
            </x-nav-link>
            <x-nav-link :href="route('suppliers.index')"    :active="request()->routeIs('suppliers.*')"    class="nav-item rounded">
                <i class="fas fa-truck"></i><span class="nav-text">Fornecedores</span>
            </x-nav-link>
            <x-nav-link :href="route('requisicoes.create')" :active="request()->routeIs('requisicoes.create')" class="nav-item rounded">
                <i class="fas fa-plus-circle"></i><span class="nav-text">Nova Requisição</span>
            </x-nav-link>
            <x-nav-link :href="route('requisicoes.index')"  :active="request()->routeIs('requisicoes.index')"  class="nav-item rounded">
                <i class="fas fa-list"></i><span class="nav-text">Lista de Requisições</span>
            </x-nav-link>
        </div>

        <div class="nav-flyout">
            <div class="nav-flyout-label">Pedidos / Requisições</div>
            <a href="{{ route('compras.index') }}"><i class="fas fa-list-ul"></i> Pedidos Internos</a>
            <a href="{{ route('suppliers.index') }}"><i class="fas fa-truck"></i> Fornecedores</a>
            <a href="{{ route('requisicoes.create') }}"><i class="fas fa-plus-circle"></i> Nova Requisição</a>
            <a href="{{ route('requisicoes.index') }}"><i class="fas fa-list"></i> Lista de Requisições</a>
        </div>
    </div>
    @endcan

    {{-- Combustível --}}
    @can('acesso combustivel')
    <x-nav-link :href="route('fuel.index')" :active="request()->routeIs('fuel.*')" class="nav-item rounded">
        <i class="fas fa-gas-pump"></i>
        <span class="nav-text">Gestão de Combustível</span>
    </x-nav-link>
    @endcan

    {{-- Discharges --}}
    @can('acesso discharges')
    <x-nav-link :href="route('discharges.index')" :active="request()->routeIs('discharges.*')" class="nav-item rounded">
        <i class="fas fa-sign-out-alt"></i>
        <span class="nav-text">Discharges</span>
    </x-nav-link>
    @endcan

    <hr class="border-gray-700 my-4">

    {{-- Perfil (sempre visível) --}}
    <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')" class="nav-item rounded">
        <i class="fas fa-user-circle"></i>
        <span class="nav-text">Perfil</span>
    </x-nav-link>

    {{-- Administração --}}
    @hasrole('super-admin')
        <div class="admin-label pt-4 pb-2 px-4">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Administração</span>
        </div>
        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" class="nav-item rounded">
            <i class="fas fa-users-cog"></i>
            <span class="nav-text">Utilizadores</span>
        </x-nav-link>
    @endhasrole

</nav>
            </aside>

            {{-- CONTEÚDO PRINCIPAL --}}
            <main class="flex-1">
                @if (isset($header))
                    <header class="bg-white shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif
                <div class="p-6">
                    {{ $slot }}
                </div>
            </main>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- ── TOAST GLOBAL ── --}}
    <style>
        .toast-wrap{position:fixed;bottom:24px;right:24px;z-index:99999;display:flex;flex-direction:column;gap:8px;pointer-events:none}
        .toast-item{display:flex;align-items:flex-start;gap:12px;background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:14px 16px;min-width:280px;max-width:360px;box-shadow:0 8px 30px rgba(0,0,0,.12);pointer-events:all;transform:translateX(120%);transition:transform .3s cubic-bezier(.34,1.56,.64,1),opacity .3s;opacity:0}
        .toast-item.show{transform:translateX(0);opacity:1}
        .toast-item.hide{transform:translateX(120%);opacity:0}
        .toast-icon{width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:.95rem;flex-shrink:0}
        .toast-icon.success{background:#f0fdf4;color:#16a34a}
        .toast-icon.error  {background:#fef2f2;color:#dc2626}
        .toast-icon.warning{background:#fefce8;color:#d97706}
        .toast-icon.info   {background:#f0f9ff;color:#0369a1}
        .toast-body{flex:1;min-width:0}
        .toast-title{font-size:.78rem;font-weight:700;color:#1e293b;margin-bottom:2px}
        .toast-msg{font-size:.73rem;color:#64748b;line-height:1.4;word-break:break-word}
        .toast-close{background:none;border:none;padding:0;color:#94a3b8;cursor:pointer;font-size:.9rem;flex-shrink:0;line-height:1;margin-top:1px}
        .toast-close:hover{color:#475569}
        .toast-progress{height:3px;border-radius:0 0 12px 12px;position:absolute;bottom:0;left:0;right:0;overflow:hidden}
        .toast-progress-bar{height:100%;border-radius:inherit;transition:width linear}
        .toast-progress-bar.success{background:#16a34a}
        .toast-progress-bar.error  {background:#dc2626}
        .toast-progress-bar.warning{background:#d97706}
        .toast-progress-bar.info   {background:#0369a1}
        .toast-item{position:relative}
    </style>

    <div class="toast-wrap" id="toastWrap"></div>

    <script>
        function showToast(msg, type, title) {
            type  = type  || 'success';
            title = title || { success:'Sucesso', error:'Erro', warning:'Aviso', info:'Info' }[type];
            var icons = { success:'bi-check-circle-fill', error:'bi-x-circle-fill', warning:'bi-exclamation-triangle-fill', info:'bi-info-circle-fill' };
            var id = 'toast-' + Date.now();
            var html =
                '<div class="toast-item" id="'+id+'">' +
                  '<div class="toast-icon '+type+'"><i class="bi '+icons[type]+'"></i></div>' +
                  '<div class="toast-body">' +
                    '<div class="toast-title">'+title+'</div>' +
                    '<div class="toast-msg">'+msg+'</div>' +
                  '</div>' +
                  '<button class="toast-close" onclick="dismissToast(\''+id+'\')"><i class="bi bi-x"></i></button>' +
                  '<div class="toast-progress"><div class="toast-progress-bar '+type+'" style="width:100%" id="bar-'+id+'"></div></div>' +
                '</div>';
            $('#toastWrap').append(html);
            var el = document.getElementById(id);
            requestAnimationFrame(function() {
                requestAnimationFrame(function() { el.classList.add('show'); });
            });
            // Barra de progresso
            var bar = document.getElementById('bar-'+id);
            bar.style.transition = 'width 4s linear';
            requestAnimationFrame(function() {
                requestAnimationFrame(function() { bar.style.width = '0%'; });
            });
            // Auto-dismiss
            setTimeout(function() { dismissToast(id); }, 4000);
        }

        function dismissToast(id) {
            var el = document.getElementById(id);
            if (!el) return;
            el.classList.add('hide');
            setTimeout(function() { el && el.remove(); }, 350);
        }

        // Mostrar flash sessions automaticamente
        @if(session('success'))
            $(document).ready(function() { showToast('{{ addslashes(session('success')) }}', 'success'); });
        @endif
        @if(session('error'))
            $(document).ready(function() { showToast('{{ addslashes(session('error')) }}', 'error'); });
        @endif
        @if(session('warning'))
            $(document).ready(function() { showToast('{{ addslashes(session('warning')) }}', 'warning'); });
        @endif
        @if(session('info'))
            $(document).ready(function() { showToast('{{ addslashes(session('info')) }}', 'info'); });
        @endif
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar  = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggleBtn');

            const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
            if (isCollapsed) {
                sidebar.classList.remove('sidebar-expanded');
                sidebar.classList.add('sidebar-collapsed');
            }

            toggleBtn.addEventListener('click', () => {
                if (sidebar.classList.contains('sidebar-expanded')) {
                    sidebar.classList.remove('sidebar-expanded');
                    sidebar.classList.add('sidebar-collapsed');
                    localStorage.setItem('sidebar-collapsed', 'true');
                } else {
                    sidebar.classList.remove('sidebar-collapsed');
                    sidebar.classList.add('sidebar-expanded');
                    localStorage.setItem('sidebar-collapsed', 'false');
                }
            });
        });
    </script>
</body>
</html>
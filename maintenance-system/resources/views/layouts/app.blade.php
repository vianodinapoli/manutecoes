<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>FEM OFICINAS</title>

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
        }

        .sidebar-expanded { width: 260px !important; }
        .sidebar-collapsed { width: 80px !important; }

        .sidebar-collapsed .nav-text,
        .sidebar-collapsed .sidebar-title,
        .sidebar-collapsed .admin-label {
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

        /* Estilo base partilhado por todos os itens */
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

        /* Chevron alinhado à direita */
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

        /* Subitens — mesma cor e fonte, ligeiramente indentados */
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

        /* Comportamento quando sidebar recolhida */
        .sidebar-collapsed .nav-dropdown-trigger .nav-text,
        .sidebar-collapsed .nav-dropdown-trigger .chevron {
            display: none;
        }

        .sidebar-collapsed .nav-dropdown-trigger {
            justify-content: center;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        .sidebar-collapsed .nav-dropdown-trigger i.icon-main {
            margin-right: 0;
            font-size: 1.25rem;
        }

        .sidebar-collapsed .nav-dropdown-items {
            display: none !important;
        }
    </style>
</head>
<body class="font-sans antialiased">

    <div class="min-h-screen bg-gray-100">

        {{-- NAVBAR SUPERIOR --}}
        @include('layouts.navigation')

        <div class="flex">

            {{-- SIDEBAR --}}
            <aside id="sidebar" class="sidebar-expanded bg-gray-800 text-white flex-shrink-0 shadow-lg">

                <div class="p-4 flex items-center justify-between border-b border-gray-700">
                    <span class="sidebar-title font-bold text-lg overflow-hidden whitespace-nowrap">FEM OFICINAS</span>
                    <button id="toggleBtn" class="p-1 hover:bg-gray-700 rounded text-white outline-none">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>

                <nav class="mt-4 px-2 space-y-1">

                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="nav-item rounded">
                        <i class="fas fa-home"></i>
                        <span class="nav-text">Dashboard</span>
                    </x-nav-link>

                    <x-nav-link :href="route('machines.index')" :active="request()->routeIs('machines.*')" class="nav-item rounded">
                        <i class="fas fa-tools"></i>
                        <span class="nav-text">Equipamento/Máquinas</span>
                    </x-nav-link>

                    <x-nav-link :href="route('maintenances.index')" :active="request()->routeIs('maintenances.*')" class="nav-item rounded">
                        <i class="fas fa-wrench"></i>
                        <span class="nav-text">Manutenções</span>
                    </x-nav-link>

                    <x-nav-link :href="route('stock-items.index')" :active="request()->routeIs('stock-items.*')" class="nav-item rounded">
                        <i class="fas fa-boxes"></i>
                        <span class="nav-text">Stock</span>
                    </x-nav-link>

                    {{-- DROPDOWN PEDIDOS/REQUISIÇÕES --}}
                    <div x-data="{ open: {{ request()->routeIs('suppliers.*', 'compras.*', 'requisicao.*', 'requisicoes.*') ? 'true' : 'false' }} }">

                        <div @click="open = !open" class="nav-dropdown-trigger">
                            <i class="fas fa-shopping-cart icon-main"></i>
                            <span class="nav-text">Pedidos/Requisições</span>
                            <i class="fas fa-chevron-right chevron" :style="open ? 'transform: rotate(90deg)' : ''"></i>
                        </div>

                        <div x-show="open"
                             x-cloak
                             x-collapse
                             x-transition:enter="transition ease-out duration-200"
                             class="nav-dropdown-items">

                            <x-nav-link :href="route('compras.index')"
                                        :active="request()->routeIs('compras.index')"
                                        class="nav-item rounded">
                                <i class="fas fa-list-ul"></i>
                                <span class="nav-text">Pedidos internos</span>
                            </x-nav-link>

                            <x-nav-link :href="route('suppliers.index')"
                                        :active="request()->routeIs('suppliers.*')"
                                        class="nav-item rounded">
                                <i class="fas fa-truck"></i>
                                <span class="nav-text">Fornecedores</span>
                            </x-nav-link>

                            <x-nav-link :href="route('requisicoes.create')"
                                        :active="request()->routeIs('requisicoes.create')"
                                        class="nav-item rounded">
                                <i class="fas fa-plus-circle"></i>
                                <span class="nav-text">Nova Requisição</span>
                            </x-nav-link>

                            <x-nav-link :href="route('requisicoes.index')"
                                        :active="request()->routeIs('requisicoes.index')"
                                        class="nav-item rounded">
                                <i class="fas fa-list"></i>
                                <span class="nav-text">Lista de Requisições</span>
                            </x-nav-link>

                        </div>
                    </div>

                    <x-nav-link :href="route('fuel.index')" :active="request()->routeIs('fuel.*')" class="nav-item rounded">
                        <i class="fas fa-gas-pump"></i>
                        <span class="nav-text">Gestão de Combustível</span>
                    </x-nav-link>

                    <x-nav-link :href="route('discharges.index')" :active="request()->routeIs('discharges.*')" class="nav-item rounded">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="nav-text">Discharges</span>
                    </x-nav-link>

                    <hr class="border-gray-700 my-4">

                    <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')" class="nav-item rounded">
                        <i class="fas fa-user-circle"></i>
                        <span class="nav-text">Perfil</span>
                    </x-nav-link>

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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
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
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
        /* Transição suave da Sidebar */
        #sidebar {
            transition: all 0.3s ease-in-out;
            min-height: 100vh;
            z-index: 1000;
        }

        /* Estado Expandido */
        .sidebar-expanded { width: 260px !important; }
        
        /* Estado Recolhido */
        .sidebar-collapsed { width: 80px !important; }

        /* Esconder texto quando recolhido */
        .sidebar-collapsed .nav-text, 
        .sidebar-collapsed .sidebar-title,
        .sidebar-collapsed .admin-label {
            display: none;
        }

        /* Centralizar ícones quando recolhido */
        .sidebar-collapsed .nav-item {
            justify-content: center;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        .sidebar-collapsed .nav-item i {
            margin-right: 0 !important;
            font-size: 1.25rem;
        }

        /* Estilo dos Links */
        .nav-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: #d1d5db; /* gray-300 */
            transition: all 0.2s;
            text-decoration: none !important;
        }

        .nav-item:hover {
            background-color: #374151; /* gray-700 */
            color: white;
        }

        .nav-item i {
            width: 20px;
            text-align: center;
            margin-right: 12px;
        }
    </style>
</head>
<body class="font-sans antialiased">
    
    <div class="min-h-screen bg-gray-100">
        
        {{-- 1. NAVBAR SUPERIOR --}}
        @include('layouts.navigation')

        <div class="flex">
            
            {{-- SIDEBAR --}}
            <aside id="sidebar" class="sidebar-expanded bg-gray-800 text-white flex-shrink-0 shadow-lg">
                
                {{-- Header da Sidebar com Botão Toggle --}}
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
                        <span class="nav-text">Máquinas</span>
                    </x-nav-link>
                    
                    <x-nav-link :href="route('maintenances.index')" :active="request()->routeIs('maintenances.*')" class="nav-item rounded">
                        <i class="fas fa-wrench"></i> 
                        <span class="nav-text">Manutenções</span>
                    </x-nav-link>
                    
                    <x-nav-link :href="route('stock-items.index')" :active="request()->routeIs('stock-items.*')" class="nav-item rounded">
                        <i class="fas fa-boxes"></i> 
                        <span class="nav-text">Stock</span>
                    </x-nav-link>

                    <x-nav-link :href="route('compras.index')" :active="request()->routeIs('compras.*')" class="nav-item rounded">
                        <i class="fas fa-shopping-cart"></i> 
                        <span class="nav-text">Requisições/Compras</span>
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
            
            // Verifica se existe preferência no navegador
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
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">


    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    
    <div class="min-h-screen bg-gray-100">
        
        {{-- 1. NAVBAR SUPERIOR (Header Principal) --}}
        @include('layouts.navigation')

        {{-- 2. HEADER DA PÁGINA (Se definido) --}}
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        {{-- 3. DIV PRINCIPAL DE LAYOUT: SIDEBAR E CONTEÚDO --}}
        <div class="flex">

            
            {{-- ==========================================================
            SIDEBAR FIXA (ESTRUTURA DE NAVEGAÇÃO LATERAL)
            ========================================================== --}}
            <aside class="w-64 bg-gray-800 text-white flex-shrink-0 min-h-screen pt-4">
                <div class="p-4 text-2xl font-semibold border-b border-gray-700 mb-4">
                    {{ config('app.name', 'Laravel') }}
                </div>
                <nav class="space-y-2 px-4">
                    
                    {{-- ITEM 1: Dashboard --}}
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 text-white">
                        <i class="fas fa-home me-2"></i> Dashboard
                    </x-nav-link>

                    {{-- ITEM 2: Máquinas --}}
                    <x-nav-link :href="route('machines.index')" :active="request()->routeIs('machines.*')" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 text-white">
                        <i class="fas fa-tools me-2"></i> Máquinas
                    </x-nav-link>
                    
                    {{-- ITEM 3: Manutenções --}}
                    <x-nav-link :href="route('maintenances.index')" :active="request()->routeIs('maintenances.*')" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 text-white">
                        <i class="fas fa-wrench me-2"></i> Manutenções
                    </x-nav-link>
                    
                    {{-- ITEM 4: Stock --}}
                    <x-nav-link :href="route('stock-items.index')" :active="request()->routeIs('stock-items.*')" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 text-white">
                        <i class="fas fa-boxes me-2"></i> Stock
                    </x-nav-link>

                    {{-- Separador --}}
                    <hr class="border-gray-700 my-4">

                    {{-- ITEM 5: Perfil --}}
                    <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 text-white">
                        <i class="fas fa-user-circle me-2"></i> Perfil
                    </x-nav-link>
                </nav>
            </aside>

            {{-- ==========================================================
            CONTEÚDO PRINCIPAL (Main Content)
            ========================================================== --}}
            <main class="flex-1 p-6"> 
                {{ $slot }}
            </main>
        </div>
        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    {{-- Se tiver o código de Toastify, adicione aqui o script de inicialização --}}
</body>
</html>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Portocargas - Oficinas') }}</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])


    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    {{-- ADICIONADO: Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full font-sans antialiased bg-gray-50">

    @if (session('success'))
        <div 
            id="toast-flash-message-success" 
            style="display: none;" 
            data-toast-success="{{ session('success') }}"
        ></div>
    @endif
    
    @if (session('error'))
        <div 
            id="toast-flash-message-error" 
            style="display: none;" 
            data-toast-error="{{ session('error') }}"
        ></div>
    @endif


    <div class="min-h-full flex flex-col"> {{-- Adicionado flex-col para estrutura vertical --}}
        
        <header class="bg-white border-b border-gray-100 sticky top-0 z-10">
            <div class="max-w-full mx-auto py-3 px-8 flex justify-between items-center">
                <h1 class="text-xl font-medium text-gray-900 flex items-center space-x-2">
                    <span class="text-indigo-600 text-2xl">⚙️</span>
                    <span>Portocargas - Oficinas</span>
                </h1>
                
                {{-- Apenas um link de ação discreto, se necessário, ou perfil de utilizador --}}
                <nav class="flex space-x-4 items-center text-sm">
                    <a href="#" class="text-gray-600 hover:text-indigo-600 transition duration-150">
                        <i class="fa fa-user me-1"></i> Perfil
                    </a>
                </nav>
            </div>
        </header>

        <div class="flex flex-grow">
            
            <aside class="bg-gray-800 text-white w-64 space-y-6 py-7 px-2 flex-shrink-0 shadow-lg">
                
                <p class="text-xs text-gray-400 uppercase tracking-widest px-4">Menu Principal</p>

                <nav>
                    @php
                        // Classes Padrão
                        $default_class = 'block py-2.5 px-4  rounded transition duration-200 hover:bg-gray-700 hover:text-white font-medium text-sm text-gray-300';
                        // Classes para item ATIVO
                        $active_class = 'block py-2.5 px-4 rounded transition duration-200 font-bold text-white text-sm bg-indigo-600 hover:bg-indigo-600 shadow-md';
                    @endphp
                    
                    {{-- 1. DASHBOARD --}}
                    {{-- Nota: Ajuste a rota 'dashboard' se a sua for diferente --}}
                    <a href="#" 
                       class="@if (request()->routeIs('dashboard')) {{ $active_class }} @else {{ $default_class }} @endif">
                        <i class="fa fa-tachometer-alt mr-3"></i> Dashboard
                    </a>
                    
                    {{-- 2. MÁQUINAS & EQUIPAMENTOS --}}
                    <a href="{{ route('machines.index') }}" 
                       class="@if (request()->routeIs('machines.index') || request()->routeIs('machines.create') || request()->routeIs('machines.edit')) {{ $active_class }} @else {{ $default_class }} @endif">
                        <i class="fa fa-cogs mr-3"></i> Equipamentos
                    </a>
                    
                    {{-- 3. MANUTENÇÕES --}}
                    <a href="{{ route('maintenances.index') }}" 
                       class="@if (request()->routeIs('maintenances.index')) {{ $active_class }} @else {{ $default_class }} @endif">
                        <i class="fa fa-wrench mr-3"></i> Manutenções
                    </a>
                    
                    {{-- 4. INVENTÁRIO DE STOCK --}}
                    <a href="{{ route('stock-items.index') }}" 
                       class="@if (request()->routeIs('stock-items.index')) {{ $active_class }} @else {{ $default_class }} @endif">
                        <i class="fa fa-warehouse mr-3"></i> Inventário de Stock
                    </a>
                    
                </nav>
            </aside>

            <main class="flex-grow bg-gray-50">
                <div class="px-6 py-8">
                    {{-- O $slot foi mantido. Agora, o seu conteúdo é injetado aqui ao lado da sidebar --}}
                    {{ $slot }} 
                </div>
            </main>
        </div>
    </div>
</body>
</html>
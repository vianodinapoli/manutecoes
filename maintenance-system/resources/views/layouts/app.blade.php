<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Gestão de Manutenção') }}</title>

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full font-sans antialiased bg-gray-50 text-gray-800">

    @if (session('success'))
        <div id="toast-flash-message-success" style="display: none;" data-toast-success="{{ session('success') }}">
        </div>
    @endif

    @if (session('error'))
        <div id="toast-flash-message-error" style="display: none;" data-toast-error="{{ session('error') }}"></div>
    @endif


    <div class="min-h-full">
        <header class="bg-white border-b border-gray-100">
            {{-- Reduzido py-4 para py-3 para um cabeçalho mais fino --}}
            <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8 flex justify-between items-center">

                <div class="flex items-center">
                    <a href="/" class="focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded">

                        {{-- 
                            ATENÇÃO: Mude 'images/logo.svg' para o caminho real do seu ficheiro 
                            A classe 'h-8' define a altura, ajuste conforme necessário (ex: h-10)
                        --}}
                        <img class="h-8 w-auto" {{-- Apenas um asset() e o caminho direto --}} src="{{ asset('unnamed.jpg') }}"
                            alt="{{ config('app.name', 'Gestão de Manutenção') }}"> </a>
                </div>
                {{-- Título mais discreto e com menos peso --}}
                <h1 class="text-xl font-medium text-gray-900 flex items-center space-x-2">
                    <span class="text-indigo-600 text-2xl">⚙️</span>
                    <span>Gestão de Manutenção</span>
                </h1>

                {{-- Navegação: Espaçamento reduzido --}}
                <nav class="flex space-x-4 items-center text-sm">
                    <a href="#" class="text-gray-600 hover:text-indigo-600 transition duration-150">Dashboard</a>
                    <a href="#"
                        class="text-gray-900 hover:text-indigo-600 font-semibold transition duration-150">Máquinas</a>

                    {{-- Botão: Estilo Ghost/Outline para minimalismo, com cor de ação suave --}}
                    <button
                        class="border border-indigo-200 text-indigo-600 hover:bg-indigo-50 font-medium py-1.5 px-3 rounded-lg transition duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        + Nova Ordem
                    </button>
                </nav>
            </div>
        </header>

        <main>
            {{-- Aumentado py-6 para py-8 para mais espaço vertical (respiro) --}}
            <div class="max-w-7xl mx-auto py-8 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>
    </div>

</body>

</html>

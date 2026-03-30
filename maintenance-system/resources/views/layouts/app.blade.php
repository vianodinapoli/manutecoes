<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>FEM — Fábrica de Explosivos</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <style>
        /* ── TOKENS ──────────────────────────────────────────────── */
        :root {
            --sidebar-bg:        #0d1117;
            --sidebar-surface:   #161b22;
            --sidebar-border:    rgba(255,255,255,.06);
            --sidebar-text:      #8b949e;
            --sidebar-text-hover:#e6edf3;
            --sidebar-active-bg: rgba(210,130,40,.12);
            --sidebar-active-border: #d28228;
            --sidebar-active-text:   #f0a43a;
            --sidebar-section:   rgba(255,255,255,.22);

            --accent:        #d28228;
            --accent-light:  #f0a43a;
            --accent-dim:    rgba(210,130,40,.15);

            --content-bg:    #f4f5f7;
            --header-bg:     #ffffff;

            --sidebar-w-open:   264px;
            --sidebar-w-closed:  68px;
            --transition: all 0.28s cubic-bezier(.4,0,.2,1);
        }

        /* ── RESET / BASE ────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'DM Sans', 'Figtree', sans-serif;
            background: var(--content-bg);
            color: #1a1d23;
            margin: 0;
        }

        /* ── SIDEBAR SHELL ───────────────────────────────────────── */
        #sidebar {
            width: var(--sidebar-w-open);
            min-height: 100vh;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            position: relative;
            transition: var(--transition);
            z-index: 100;
            border-right: 1px solid var(--sidebar-border);
        }

        #sidebar.sidebar-collapsed { width: var(--sidebar-w-closed); }

        /* ── SIDEBAR HEADER ──────────────────────────────────────── */
        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
            height: 64px;
            border-bottom: 1px solid var(--sidebar-border);
            flex-shrink: 0;
            overflow: hidden;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            overflow: hidden;
            white-space: nowrap;
        }

        .sidebar-brand-icon {
            width: 34px;
            height: 34px;
            background: linear-gradient(135deg, var(--accent), #b86a18);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 15px;
            color: #fff;
            font-weight: 700;
            font-family: 'Syne', sans-serif;
            letter-spacing: -0.5px;
        }

        .sidebar-brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.1;
        }

        .sidebar-brand-name {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 14px;
            color: #e6edf3;
            letter-spacing: 0.5px;
        }

        .sidebar-brand-sub {
            font-size: 10px;
            color: var(--sidebar-text);
            letter-spacing: 1.5px;
            text-transform: uppercase;
            font-weight: 500;
            margin-top: 1px;
        }

        .sidebar-toggle {
            width: 30px;
            height: 30px;
            background: rgba(255,255,255,.06);
            border: 1px solid var(--sidebar-border);
            border-radius: 7px;
            color: var(--sidebar-text);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            flex-shrink: 0;
        }

        .sidebar-toggle:hover {
            background: rgba(255,255,255,.1);
            color: var(--sidebar-text-hover);
        }

        /* ── SIDEBAR NAV ─────────────────────────────────────────── */
        .sidebar-nav {
            flex: 1;
            padding: 12px 10px;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar-nav::-webkit-scrollbar { width: 3px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 3px; }

        /* ── NAV SECTION LABEL ───────────────────────────────────── */
        .nav-section-label {
            font-size: 9px;
            font-weight: 600;
            letter-spacing: 1.8px;
            text-transform: uppercase;
            color: rgba(255,255,255,.25);
            padding: 16px 12px 6px;
            white-space: nowrap;
            overflow: hidden;
        }

        .sidebar-collapsed .nav-section-label {
            text-align: center;
            padding: 16px 0 6px;
            font-size: 0;
        }

        .sidebar-collapsed .nav-section-label::after {
            content: '';
            display: block;
            width: 22px;
            height: 1px;
            background: var(--sidebar-border);
            margin: 6px auto 0;
        }

        /* ── NAV ITEM ────────────────────────────────────────────── */
        .nav-item {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 9px 12px;
            border-radius: 8px;
            color: var(--sidebar-text);
            text-decoration: none !important;
            transition: var(--transition);
            position: relative;
            white-space: nowrap;
            overflow: hidden;
            margin-bottom: 2px;
            font-size: 13.5px;
            font-weight: 400;
            letter-spacing: 0.1px;
        }

        .nav-item i {
            width: 18px;
            text-align: center;
            font-size: 14px;
            flex-shrink: 0;
            transition: color 0.2s;
        }

        .nav-item:hover {
            background: rgba(255,255,255,.06);
            color: var(--sidebar-text-hover);
        }

        /* Active state */
        .nav-item.active,
        .nav-item[aria-current="page"] {
            background: var(--sidebar-active-bg);
            color: var(--sidebar-active-text);
            font-weight: 500;
        }

        .nav-item.active::before,
        .nav-item[aria-current="page"]::before {
            content: '';
            position: absolute;
            left: 0;
            top: 20%;
            bottom: 20%;
            width: 3px;
            background: var(--sidebar-active-border);
            border-radius: 0 3px 3px 0;
        }

        .nav-item.active i,
        .nav-item[aria-current="page"] i {
            color: var(--accent-light);
        }

        /* Collapsed: center icon */
        .sidebar-collapsed .nav-item {
            justify-content: center;
            padding: 9px 0;
            gap: 0;
        }

        .sidebar-collapsed .nav-item i {
            font-size: 15px;
        }

        .sidebar-collapsed .nav-text { display: none; }

        /* ── DROPDOWN TRIGGER ────────────────────────────────────── */
        .nav-dropdown-trigger {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 9px 12px;
            border-radius: 8px;
            color: var(--sidebar-text);
            cursor: pointer;
            user-select: none;
            transition: var(--transition);
            white-space: nowrap;
            overflow: hidden;
            margin-bottom: 2px;
            font-size: 13.5px;
        }

        .nav-dropdown-trigger:hover {
            background: rgba(255,255,255,.06);
            color: var(--sidebar-text-hover);
        }

        .nav-dropdown-trigger i.icon-main {
            width: 18px;
            text-align: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .nav-dropdown-trigger .chevron {
            margin-left: auto;
            font-size: 9px;
            transition: transform 0.28s ease;
            color: rgba(255,255,255,.3);
            flex-shrink: 0;
        }

        .sidebar-collapsed .nav-dropdown-trigger {
            justify-content: center;
            padding: 9px 0;
            gap: 0;
        }

        .sidebar-collapsed .nav-dropdown-trigger i.icon-main { font-size: 15px; }
        .sidebar-collapsed .nav-dropdown-trigger .nav-text,
        .sidebar-collapsed .nav-dropdown-trigger .chevron { display: none; }

        /* ── DROPDOWN ITEMS ──────────────────────────────────────── */
        .nav-dropdown-items {
            margin: 2px 0 4px 16px;
            border-left: 1px solid rgba(255,255,255,.08);
            padding-left: 12px;
        }

        .nav-dropdown-items .nav-item {
            padding: 7px 10px;
            font-size: 13px;
        }

        .nav-dropdown-items .nav-item i {
            font-size: 12px;
            width: 15px;
            color: rgba(255,255,255,.3);
        }

        /* ── FLYOUT (collapsed) ──────────────────────────────────── */
        .nav-dropdown-wrap { position: relative; }

        .nav-flyout {
            display: none;
            position: absolute;
            left: calc(var(--sidebar-w-closed) - 8px);
            top: 0;
            background: #1a2030;
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 12px;
            min-width: 192px;
            z-index: 9999;
            box-shadow: 0 16px 48px rgba(0,0,0,.5), 0 4px 16px rgba(0,0,0,.3);
            padding: 6px;
            backdrop-filter: blur(12px);
        }

        .nav-flyout-label {
            font-size: 9px;
            font-weight: 600;
            letter-spacing: 1.8px;
            text-transform: uppercase;
            color: rgba(255,255,255,.3);
            padding: 6px 10px 8px;
        }

        .nav-flyout a {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 8px 10px;
            font-size: 13px;
            color: var(--sidebar-text);
            text-decoration: none;
            border-radius: 7px;
            transition: background .15s, color .15s;
        }

        .nav-flyout a:hover {
            background: rgba(255,255,255,.08);
            color: var(--sidebar-text-hover);
        }

        .nav-flyout a i { width: 15px; font-size: 12px; text-align: center; color: rgba(255,255,255,.4); }

        .sidebar-collapsed .nav-dropdown-wrap:hover .nav-flyout { display: block; }
        .sidebar-expanded .nav-flyout { display: none !important; }

        /* ── SIDEBAR FOOTER ──────────────────────────────────────── */
        .sidebar-footer {
            padding: 12px 10px;
            border-top: 1px solid var(--sidebar-border);
            flex-shrink: 0;
        }

        /* ── MAIN LAYOUT ─────────────────────────────────────────── */
        .layout-wrapper {
            display: flex;
            min-height: 100vh;
        }

        main.content-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            transition: var(--transition);
        }

        /* ── PAGE HEADER ─────────────────────────────────────────── */
        .page-header {
            background: var(--header-bg);
            border-bottom: 1px solid #e8eaed;
            padding: 0 28px;
            height: 60px;
            display: flex;
            align-items: center;
        }

        .page-header h1,
        .page-header h2,
        .page-header h3 {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 18px;
            color: #1a1d23;
            margin: 0;
            letter-spacing: -0.3px;
        }

        /* ── CONTENT BODY ────────────────────────────────────────── */
        .content-body {
            flex: 1;
            padding: 28px;
        }

        /* ── COLLAPSED OVERRIDES ─────────────────────────────────── */
        .sidebar-collapsed .sidebar-brand-text,
        .sidebar-collapsed .sidebar-brand-name,
        .sidebar-collapsed .sidebar-brand-sub { display: none; }

        .sidebar-collapsed .sidebar-header { justify-content: center; }

        /* ── TOAST SYSTEM ────────────────────────────────────────── */
        .toast-wrap {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 99999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        }

        .toast-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            background: #fff;
            border: 1px solid rgba(0,0,0,.08);
            border-radius: 14px;
            padding: 14px 16px 16px;
            min-width: 290px;
            max-width: 370px;
            box-shadow: 0 20px 60px rgba(0,0,0,.12), 0 4px 16px rgba(0,0,0,.06);
            pointer-events: all;
            transform: translateX(110%) scale(.96);
            transition: transform .35s cubic-bezier(.34,1.56,.64,1), opacity .3s;
            opacity: 0;
            position: relative;
        }

        .toast-item.show { transform: translateX(0) scale(1); opacity: 1; }
        .toast-item.hide { transform: translateX(110%) scale(.96); opacity: 0; }

        .toast-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .toast-icon.success { background: #f0fdf4; color: #16a34a; }
        .toast-icon.error   { background: #fef2f2; color: #dc2626; }
        .toast-icon.warning { background: #fffbeb; color: #d97706; }
        .toast-icon.info    { background: #eff6ff; color: #2563eb; }

        .toast-body { flex: 1; min-width: 0; }
        .toast-title { font-size: 13px; font-weight: 600; color: #0f172a; margin-bottom: 2px; font-family: 'Syne', sans-serif; }
        .toast-msg { font-size: 12.5px; color: #64748b; line-height: 1.45; }
        .toast-close { background: none; border: none; padding: 0; color: #cbd5e1; cursor: pointer; font-size: 14px; line-height: 1; margin-top: 2px; flex-shrink: 0; transition: color .15s; }
        .toast-close:hover { color: #475569; }

        .toast-progress { height: 3px; border-radius: 0 0 14px 14px; position: absolute; bottom: 0; left: 0; right: 0; overflow: hidden; }
        .toast-progress-bar { height: 100%; border-radius: inherit; transition: width linear; }
        .toast-progress-bar.success { background: #16a34a; }
        .toast-progress-bar.error   { background: #dc2626; }
        .toast-progress-bar.warning { background: #bb0000; }
        .toast-progress-bar.info    { background: #2563eb; }

        /* ── UTILITIES ───────────────────────────────────────────── */
        .nav-divider {
            height: 1px;
            background: var(--sidebar-border);
            margin: 10px 10px;
        }
    </style>
</head>

<body class="font-sans antialiased">

    <div class="layout-wrapper">

        {{-- ════════════════════════════════════════════════════════
             SIDEBAR
        ════════════════════════════════════════════════════════ --}}
        <aside id="sidebar" class="sidebar-expanded">

            {{-- Brand Header --}}
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <div class="sidebar-brand-icon">F</div>
                    <div class="sidebar-brand-text">
                        <span class="sidebar-brand-name">FEM</span>
                        <span class="sidebar-brand-sub">Sistema de Gestão</span>
                    </div>
                </div>
                <button id="toggleBtn" class="sidebar-toggle" title="Colapsar menu">
                    <i class="fas fa-bars" style="font-size:12px;"></i>
                </button>
            </div>

            {{-- Navigation --}}
            <nav class="sidebar-nav">

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
                    <span class="nav-text">Equipamentos</span>
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
                        <x-nav-link :href="route('maintenances.create')" :active="request()->routeIs('maintenances.create')" class="nav-item">
                            <i class="fas fa-plus-circle"></i>
                            <span class="nav-text">Criar Manutenção</span>
                        </x-nav-link>
                        <x-nav-link :href="route('maintenances.index')" :active="request()->routeIs('maintenances.index')" class="nav-item">
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
                        <x-nav-link :href="route('stock-items.index')" :active="request()->routeIs('stock-items.*')" class="nav-item">
                            <i class="fas fa-boxes"></i>
                            <span class="nav-text">Stock</span>
                        </x-nav-link>
                        @endcan
                        @can('acesso movimentos')
                        <x-nav-link :href="route('movimentos.index')" :active="request()->routeIs('movimentos.*')" class="nav-item">
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
                        <span class="nav-text">Pedidos / Requisições</span>
                        <i class="fas fa-chevron-right chevron" :style="open ? 'transform:rotate(90deg)' : ''"></i>
                    </div>

                    <div x-show="open" x-cloak x-collapse
                         x-transition:enter="transition ease-out duration-200"
                         class="nav-dropdown-items">
                        <x-nav-link :href="route('compras.index')" :active="request()->routeIs('compras.index')" class="nav-item">
                            <i class="fas fa-list-ul"></i><span class="nav-text">Pedidos Internos</span>
                        </x-nav-link>
                        <x-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')" class="nav-item">
                            <i class="fas fa-truck"></i><span class="nav-text">Fornecedores</span>
                        </x-nav-link>
                        <x-nav-link :href="route('requisicoes.create')" :active="request()->routeIs('requisicoes.create')" class="nav-item">
                            <i class="fas fa-plus-circle"></i><span class="nav-text">Nova Requisição</span>
                        </x-nav-link>
                        <x-nav-link :href="route('requisicoes.index')" :active="request()->routeIs('requisicoes.index')" class="nav-item">
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
                <x-nav-link :href="route('fuel.index')" :active="request()->routeIs('fuel.*')" class="nav-item">
                    <i class="fas fa-gas-pump"></i>
                    <span class="nav-text">Gestão de Combustível</span>
                </x-nav-link>
                @endcan

                {{-- Viaturas --}}
                <x-nav-link :href="route('viaturas.index')" :active="request()->routeIs('viaturas.*')" class="nav-item">
                    <i class="fas fa-truck-moving"></i>
                    <span class="nav-text">Docs / Viaturas</span>
                </x-nav-link>

                {{-- Discharges --}}
                @can('acesso discharges')
                <x-nav-link :href="route('discharges.index')" :active="request()->routeIs('discharges.*')" class="nav-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="nav-text">Discharges</span>
                </x-nav-link>
                @endcan

                {{-- ── Conta ── --}}
                <div class="nav-divider"></div>
                <div class="nav-section-label">Conta</div>

                <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')" class="nav-item">
                    <i class="fas fa-user-circle"></i>
                    <span class="nav-text">Perfil</span>
                </x-nav-link>

                {{-- Administração --}}
                @hasrole('super-admin')
                <div class="nav-section-label">Administração</div>
                <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" class="nav-item">
                    <i class="fas fa-users-cog"></i>
                    <span class="nav-text">Utilizadores</span>
                </x-nav-link>
                @endhasrole

            </nav>

        </aside>

        {{-- ════════════════════════════════════════════════════════
             MAIN
        ════════════════════════════════════════════════════════ --}}
        <main class="content-area">

            @include('layouts.navigation')

            @if (isset($header))
                <div class="page-header">
                    {{ $header }}
                </div>
            @endif

            <div class="content-body">
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot }}
                @endif
            </div>

        </main>

    </div>

    {{-- ── SCRIPTS ───────────────────────────────────────────────── --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- TOAST CONTAINER --}}
    <div class="toast-wrap" id="toastWrap"></div>

    <script>
        /* ── Toast ──────────────────────────────────────────────── */
        function showToast(msg, type, title) {
            type  = type  || 'success';
            title = title || { success:'Sucesso', error:'Erro', warning:'Aviso', info:'Info' }[type];
            var icons = {
                success: 'fas fa-check-circle',
                error:   'fas fa-times-circle',
                warning: 'fas fa-exclamation-triangle',
                info:    'fas fa-info-circle'
            };
            var id = 'toast-' + Date.now();
            var html =
                '<div class="toast-item" id="' + id + '">' +
                  '<div class="toast-icon ' + type + '"><i class="' + icons[type] + '"></i></div>' +
                  '<div class="toast-body">' +
                    '<div class="toast-title">' + title + '</div>' +
                    '<div class="toast-msg">' + msg + '</div>' +
                  '</div>' +
                  '<button class="toast-close" onclick="dismissToast(\'' + id + '\')"><i class="fas fa-times"></i></button>' +
                  '<div class="toast-progress"><div class="toast-progress-bar ' + type + '" style="width:100%" id="bar-' + id + '"></div></div>' +
                '</div>';
            $('#toastWrap').append(html);
            var el = document.getElementById(id);
            requestAnimationFrame(function() {
                requestAnimationFrame(function() { el.classList.add('show'); });
            });
            var bar = document.getElementById('bar-' + id);
            bar.style.transition = 'width 4s linear';
            requestAnimationFrame(function() {
                requestAnimationFrame(function() { bar.style.width = '0%'; });
            });
            setTimeout(function() { dismissToast(id); }, 4000);
        }

        function dismissToast(id) {
            var el = document.getElementById(id);
            if (!el) return;
            el.classList.add('hide');
            setTimeout(function() { if (el) el.remove(); }, 380);
        }

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

        /* ── Sidebar toggle ─────────────────────────────────────── */
        document.addEventListener('DOMContentLoaded', function () {
            var sidebar   = document.getElementById('sidebar');
            var toggleBtn = document.getElementById('toggleBtn');

            if (localStorage.getItem('sidebar-collapsed') === 'true') {
                sidebar.classList.remove('sidebar-expanded');
                sidebar.classList.add('sidebar-collapsed');
            }

            toggleBtn.addEventListener('click', function () {
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

    @stack('scripts')

</body>
</html>
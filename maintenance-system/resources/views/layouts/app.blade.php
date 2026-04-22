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
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <style>
        /* ── TOKENS ── */
        :root {
            --sb-w-open:   260px;
            --sb-w-closed:  64px;
            --sb-bg:       #0e1015;
            --sb-surface:  #161a22;
            --sb-border:   rgba(255,255,255,.055);
            --sb-text:     #6b7280;
            --sb-text-hover: #e2e8f0;
            --sb-accent:   #c60a1a;
            --sb-accent-dim: rgba(198,10,26,.14);
            --sb-accent-glow: rgba(198,10,26,.08);
            --content-bg:  #f3f4f6;
            --header-bg:   #ffffff;
            --ease: cubic-bezier(.4,0,.2,1);
            --dur: 0.26s;
        }

        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; background: var(--content-bg); color: #111827; margin: 0; }

        /* ── LAYOUT ── */
        .layout-wrap { display: flex; min-height: 100vh; }

        /* ── SIDEBAR ── */
        #sidebar {
            width: var(--sb-w-open);
            min-height: 100vh;
            background: var(--sb-bg);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            position: relative;
            transition: width var(--dur) var(--ease);
            z-index: 100;
            border-right: 1px solid var(--sb-border);
        }
        #sidebar.collapsed { width: var(--sb-w-closed); }

        /* ── BRAND ── */
        .sb-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 18px;
            height: 64px;
            border-bottom: 1px solid var(--sb-border);
            overflow: hidden;
            flex-shrink: 0;
        }
        .sb-logo-wrap {
            width: 32px;
            height: 32px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .sb-logo-wrap img {
            width: 32px;
            height: 32px;
            object-fit: contain;
            filter: brightness(0) invert(1);
            opacity: .9;
        }
        .sb-brand-text {
            overflow: hidden;
            white-space: nowrap;
            transition: opacity var(--dur) var(--ease), width var(--dur) var(--ease);
        }
        .sb-brand-name {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 14.5px;
            color: #f1f5f9;
            letter-spacing: .2px;
            line-height: 1.15;
        }
        .sb-brand-sub {
            font-size: 10px;
            color: var(--sb-text);
            letter-spacing: 1.4px;
            text-transform: uppercase;
            font-weight: 400;
        }
        .sb-toggle {
            margin-left: auto;
            flex-shrink: 0;
            width: 28px;
            height: 28px;
            border-radius: 7px;
            border: 1px solid var(--sb-border);
            background: transparent;
            color: var(--sb-text);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background var(--dur), color var(--dur), border-color var(--dur);
        }
        .sb-toggle:hover { background: rgba(255,255,255,.07); color: var(--sb-text-hover); border-color: rgba(255,255,255,.12); }
        #sidebar.collapsed .sb-brand-text { opacity: 0; width: 0; }
        #sidebar.collapsed .sb-toggle { margin-left: 0; }

        /* ── NAV SCROLL ── */
        .sb-nav {
            flex: 1;
            padding: 10px 10px 6px;
            overflow-y: auto;
            overflow-x: hidden;
        }
        .sb-nav::-webkit-scrollbar { width: 2px; }
        .sb-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.08); border-radius: 2px; }

        /* ── SECTION LABEL ── */
        .sb-section {
            font-size: 9.5px;
            font-weight: 600;
            letter-spacing: 1.6px;
            text-transform: uppercase;
            color: rgba(255,255,255,.2);
            padding: 18px 10px 5px;
            white-space: nowrap;
            overflow: hidden;
            transition: opacity var(--dur);
        }
        #sidebar.collapsed .sb-section { opacity: 0; height: 20px; padding: 0; }

        /* ── DIVIDER ── */
        .sb-divider { height: 1px; background: var(--sb-border); margin: 8px 10px; }

        /* ── NAV ITEM ── */
        .nav-item {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 8px 10px;
            border-radius: 8px;
            color: var(--sb-text);
            text-decoration: none !important;
            transition: background var(--dur), color var(--dur);
            position: relative;
            white-space: nowrap;
            overflow: hidden;
            margin-bottom: 1px;
            font-size: 13.5px;
            font-weight: 400;
            cursor: pointer;
        }
        .nav-item .ni-icon {
            width: 18px;
            text-align: center;
            font-size: 13px;
            flex-shrink: 0;
            opacity: .6;
            transition: opacity var(--dur), color var(--dur);
        }
        .nav-item .ni-text {
            flex: 1;
            transition: opacity var(--dur);
            font-size: 13.5px;
        }
        .nav-item:hover {
            background: rgba(255,255,255,.055);
            color: var(--sb-text-hover);
        }
        .nav-item:hover .ni-icon { opacity: .9; }

        /* Active */
        .nav-item.active {
            background: var(--sb-accent-dim);
            color: #fca5a5;
            font-weight: 500;
        }
        .nav-item.active::after {
            content: '';
            position: absolute;
            left: 0; top: 18%; bottom: 18%;
            width: 2.5px;
            background: var(--sb-accent);
            border-radius: 0 2px 2px 0;
        }
        .nav-item.active .ni-icon { color: #f87171; opacity: 1; }

        /* Collapsed */
        #sidebar.collapsed .nav-item {
            justify-content: center;
            padding: 9px 0;
            gap: 0;
        }
        #sidebar.collapsed .nav-item .ni-text { opacity: 0; width: 0; overflow: hidden; }
        #sidebar.collapsed .nav-item .ni-icon { font-size: 15px; }
        #sidebar.collapsed .nav-item::after { display: none; }

        /* ── DROPDOWN ── */
        .sb-dropdown { position: relative; }
        .sb-dropdown-trigger {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 8px 10px;
            border-radius: 8px;
            color: var(--sb-text);
            cursor: pointer;
            user-select: none;
            transition: background var(--dur), color var(--dur);
            white-space: nowrap;
            overflow: hidden;
            margin-bottom: 1px;
            font-size: 13.5px;
        }
        .sb-dropdown-trigger:hover { background: rgba(255,255,255,.055); color: var(--sb-text-hover); }
        .sb-dropdown-trigger.open { color: var(--sb-text-hover); background: rgba(255,255,255,.04); }
        .sb-dropdown-trigger .ni-icon {
            width: 18px; text-align: center; font-size: 13px; flex-shrink: 0; opacity: .6;
        }
        .sb-dropdown-trigger .ni-text { flex: 1; font-size: 13.5px; }
        .sb-dropdown-trigger .sb-chevron {
            font-size: 9px;
            color: rgba(255,255,255,.22);
            flex-shrink: 0;
            transition: transform .22s var(--ease);
        }
        .sb-dropdown-trigger.open .sb-chevron { transform: rotate(90deg); }

        /* Collapsed trigger */
        #sidebar.collapsed .sb-dropdown-trigger {
            justify-content: center;
            padding: 9px 0;
            gap: 0;
        }
        #sidebar.collapsed .sb-dropdown-trigger .ni-text,
        #sidebar.collapsed .sb-dropdown-trigger .sb-chevron { display: none; }
        #sidebar.collapsed .sb-dropdown-trigger .ni-icon { font-size: 15px; }

        /* Dropdown children */
        .sb-dropdown-items {
            margin: 2px 0 4px 14px;
            padding-left: 14px;
            border-left: 1px solid rgba(255,255,255,.07);
            overflow: hidden;
        }
        .sb-dropdown-items .nav-item {
            padding: 7px 10px;
            font-size: 12.5px;
            color: rgba(255,255,255,.38);
        }
        .sb-dropdown-items .nav-item .ni-icon { font-size: 11px; width: 14px; opacity: .5; }
        .sb-dropdown-items .nav-item:hover { color: var(--sb-text-hover); }
        .sb-dropdown-items .nav-item.active { color: #fca5a5; background: var(--sb-accent-dim); }
        .sb-dropdown-items .nav-item.active .ni-icon { color: #f87171; }

        /* Flyout for collapsed */
        .sb-flyout {
            display: none;
            position: absolute;
            left: calc(var(--sb-w-closed) + 4px);
            top: 0;
            background: #1b2030;
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 12px;
            min-width: 196px;
            z-index: 9999;
            box-shadow: 0 20px 56px rgba(0,0,0,.5);
            padding: 6px;
        }
        .sb-flyout-label {
            font-size: 9px; font-weight: 600; letter-spacing: 1.6px;
            text-transform: uppercase; color: rgba(255,255,255,.28);
            padding: 6px 10px 8px;
        }
        .sb-flyout a {
            display: flex; align-items: center; gap: 9px;
            padding: 8px 10px; font-size: 13px; color: var(--sb-text);
            text-decoration: none; border-radius: 8px;
            transition: background .14s, color .14s;
        }
        .sb-flyout a:hover { background: rgba(255,255,255,.07); color: var(--sb-text-hover); }
        .sb-flyout a i { width: 14px; font-size: 11px; text-align: center; opacity: .5; }
        #sidebar.collapsed .sb-dropdown:hover .sb-flyout { display: block; }

        /* ── FOOTER ── */
        .sb-footer {
            padding: 10px;
            border-top: 1px solid var(--sb-border);
            flex-shrink: 0;
        }
        .sb-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: 8px;
            transition: background var(--dur);
            overflow: hidden;
            cursor: default;
        }
        .sb-user:hover { background: rgba(255,255,255,.05); }
        .sb-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--sb-accent-dim);
            border: 1px solid rgba(198,10,26,.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 600;
            color: #fca5a5;
            flex-shrink: 0;
            font-family: 'Syne', sans-serif;
        }
        .sb-user-info { overflow: hidden; flex: 1; white-space: nowrap; transition: opacity var(--dur); }
        .sb-user-name { font-size: 12.5px; font-weight: 500; color: #e2e8f0; }
        .sb-user-role { font-size: 10.5px; color: var(--sb-text); margin-top: 1px; }
        #sidebar.collapsed .sb-user-info { opacity: 0; width: 0; }
        #sidebar.collapsed .sb-user { justify-content: center; }

        /* ── LOGOUT LINK in footer ── */
        .sb-logout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            margin-top: 4px;
            padding: 6px 10px;
            border-radius: 7px;
            font-size: 12px;
            color: rgba(255,255,255,.25);
            text-decoration: none;
            cursor: pointer;
            background: none;
            border: none;
            width: 100%;
            transition: color var(--dur), background var(--dur);
        }
        .sb-logout:hover { color: #f87171; background: rgba(198,10,26,.08); }
        #sidebar.collapsed .sb-logout .sb-logout-text { display: none; }

        /* ── MAIN ── */
        main.content-area { flex: 1; display: flex; flex-direction: column; min-width: 0; }

        .page-header {
            background: var(--header-bg);
            border-bottom: 1px solid #e5e7eb;
            padding: 0 28px;
            height: 60px;
            display: flex;
            align-items: center;
        }
        .page-header h1, .page-header h2, .page-header h3 {
            font-family: 'Syne', sans-serif; font-weight: 700;
            font-size: 17px; color: #111827; margin: 0; letter-spacing: -.2px;
        }
        .content-body { flex: 1; padding: 28px; }

        /* ── TOAST ── */
        .toast-wrap {
            position: fixed; bottom: 24px; right: 24px; z-index: 99999;
            display: flex; flex-direction: column; gap: 10px; pointer-events: none;
        }
        .toast-item {
            display: flex; align-items: flex-start; gap: 12px;
            background: #fff; border: 1px solid rgba(0,0,0,.08);
            border-radius: 14px; padding: 14px 16px 18px;
            min-width: 290px; max-width: 370px;
            box-shadow: 0 20px 60px rgba(0,0,0,.12), 0 4px 16px rgba(0,0,0,.06);
            pointer-events: all; position: relative;
            transform: translateX(110%) scale(.96); opacity: 0;
            transition: transform .35s cubic-bezier(.34,1.56,.64,1), opacity .3s;
        }
        .toast-item.show { transform: translateX(0) scale(1); opacity: 1; }
        .toast-item.hide { transform: translateX(110%) scale(.96); opacity: 0; }
        .toast-icon {
            width: 34px; height: 34px; border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; flex-shrink: 0;
        }
        .toast-icon.success { background: #f0fdf4; color: #16a34a; }
        .toast-icon.error   { background: #fef2f2; color: #dc2626; }
        .toast-icon.warning { background: #fffbeb; color: #d97706; }
        .toast-icon.info    { background: #eff6ff; color: #2563eb; }
        .toast-body { flex: 1; min-width: 0; }
        .toast-title { font-size: 13px; font-weight: 600; color: #0f172a; margin-bottom: 2px; font-family: 'Syne', sans-serif; }
        .toast-msg { font-size: 12.5px; color: #64748b; line-height: 1.45; }
        .toast-close { background: none; border: none; padding: 0; color: #cbd5e1; cursor: pointer; font-size: 13px; line-height: 1; margin-top: 1px; flex-shrink: 0; transition: color .15s; }
        .toast-close:hover { color: #475569; }
        .toast-progress { height: 3px; border-radius: 0 0 14px 14px; position: absolute; bottom: 0; left: 0; right: 0; overflow: hidden; }
        .toast-progress-bar { height: 100%; transition: width linear; }
        .toast-progress-bar.success { background: #16a34a; }
        .toast-progress-bar.error   { background: #dc2626; }
        .toast-progress-bar.warning { background: #d97706; }
        .toast-progress-bar.info    { background: #2563eb; }
    </style>
</head>

<body class="font-sans antialiased">
<div class="layout-wrap">

    {{-- ═══════════════════════════════
         SIDEBAR
    ═══════════════════════════════ --}}
    <aside id="sidebar">

        {{-- Brand --}}
        <div class="sb-brand">
            <!-- <div class="sb-logo-wrap">
                <img src="{{ asset('images/bymozelogo.png') }}" alt="FEM">
            </div> -->
            <div class="sb-brand-text">
                <div class="sb-brand-name">FEM</div>
                <div class="sb-brand-sub">Sistema de Gestão</div>
            </div>
            <button id="toggleBtn" class="sb-toggle" title="Colapsar">
                <i class="fas fa-bars" style="font-size:11px;"></i>
            </button>
        </div>

        {{-- Nav --}}
        <nav class="sb-nav">

            {{-- Dashboard --}}
            @can('acesso dashboard')
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="nav-item">
                <i class="fas fa-home ni-icon"></i>
                <span class="ni-text">Dashboard</span>
            </x-nav-link>
            @endcan

            {{-- Equipamentos --}}
            @can('acesso equipamentos')
            <x-nav-link :href="route('machines.index')" :active="request()->routeIs('machines.*')" class="nav-item">
                <i class="fas fa-tools ni-icon"></i>
                <span class="ni-text">Equipamentos</span>
            </x-nav-link>
            @endcan

            {{-- Manutenções --}}
            @can('acesso manutencoes')
            <div class="sb-dropdown"
                 x-data="{ open: {{ request()->routeIs('maintenances.*') ? 'true' : 'false' }} }">
                <div class="sb-dropdown-trigger" :class="open ? 'open' : ''" @click="open = !open">
                    <i class="fas fa-wrench ni-icon"></i>
                    <span class="ni-text">Manutenções</span>
                    <i class="fas fa-chevron-right sb-chevron"></i>
                </div>
                <div x-show="open" x-cloak x-collapse class="sb-dropdown-items">
                    <x-nav-link :href="route('maintenances.create')" :active="request()->routeIs('maintenances.create')" class="nav-item">
                        <i class="fas fa-plus-circle ni-icon"></i><span class="ni-text">Criar Manutenção</span>
                    </x-nav-link>
                    <x-nav-link :href="route('maintenances.index')" :active="request()->routeIs('maintenances.index')" class="nav-item">
                        <i class="fas fa-list ni-icon"></i><span class="ni-text">Ver Manutenções</span>
                    </x-nav-link>
                </div>
                <div class="sb-flyout">
                    <div class="sb-flyout-label">Manutenções</div>
                    <a href="{{ route('maintenances.create') }}"><i class="fas fa-plus-circle"></i> Criar Manutenção</a>
                    <a href="{{ route('maintenances.index') }}"><i class="fas fa-list"></i> Ver Manutenções</a>
                </div>
            </div>
            @endcan

            {{-- Stock / Armazém --}}
            @canany(['acesso stock', 'acesso movimentos'])
            <div class="sb-dropdown"
                 x-data="{ open: {{ request()->routeIs('stock-items.*', 'movimentos.*') ? 'true' : 'false' }} }">
                <div class="sb-dropdown-trigger" :class="open ? 'open' : ''" @click="open = !open">
                    <i class="fas fa-boxes ni-icon"></i>
                    <span class="ni-text">Stock / Armazém</span>
                    <i class="fas fa-chevron-right sb-chevron"></i>
                </div>
                <div x-show="open" x-cloak x-collapse class="sb-dropdown-items">
                    @can('acesso stock')
                    <x-nav-link :href="route('stock-items.index')" :active="request()->routeIs('stock-items.*')" class="nav-item">
                        <i class="fas fa-boxes ni-icon"></i><span class="ni-text">Stock</span>
                    </x-nav-link>
                    @endcan
                    @can('acesso movimentos')
                    <x-nav-link :href="route('movimentos.index')" :active="request()->routeIs('movimentos.*')" class="nav-item">
                        <i class="fas fa-arrow-right-arrow-left ni-icon"></i><span class="ni-text">Movimentos</span>
                    </x-nav-link>
                    @endcan
                </div>
                <div class="sb-flyout">
                    <div class="sb-flyout-label">Stock / Armazém</div>
                    @can('acesso stock')
                    <a href="{{ route('stock-items.index') }}"><i class="fas fa-boxes"></i> Stock</a>
                    @endcan
                    @can('acesso movimentos')
                    <a href="{{ route('movimentos.index') }}"><i class="fas fa-arrow-right-arrow-left"></i> Movimentos</a>
                    @endcan
                </div>
            </div>
            @endcanany

           {{-- Pedidos / Requisições --}}
@canany(['acesso pedidos', 'requisicoes-material'])
<div class="sb-dropdown"
     x-data="{ open: {{ request()->routeIs('suppliers.*', 'compras.*', 'requisicao.*', 'requisicoes.*', 'requisicoes-material.*') ? 'true' : 'false' }} }">
    <div class="sb-dropdown-trigger" :class="open ? 'open' : ''" @click="open = !open">
        <i class="fas fa-shopping-cart ni-icon"></i>
        <span class="ni-text">Pedidos / Requisições</span>
        <i class="fas fa-chevron-right sb-chevron"></i>
    </div>
    <div x-show="open" x-cloak x-collapse class="sb-dropdown-items">

        @can('acesso pedidos')
        <x-nav-link :href="route('compras.index')" :active="request()->routeIs('compras.index')" class="nav-item">
            <i class="fas fa-list-ul ni-icon"></i><span class="ni-text">Pedidos Internos</span>
        </x-nav-link>
        @endcan


        @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('gestor'))
        <x-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')" class="nav-item">
            <i class="fas fa-truck ni-icon"></i><span class="ni-text">Fornecedores</span>
        </x-nav-link>
        <x-nav-link :href="route('requisicoes.create')" :active="request()->routeIs('requisicoes.create')" class="nav-item">
            <i class="fas fa-plus-circle ni-icon"></i><span class="ni-text">Nova Requisição</span>
        </x-nav-link>
        <x-nav-link :href="route('requisicoes.index')" :active="request()->routeIs('requisicoes.index')" class="nav-item">
            <i class="fas fa-list ni-icon"></i><span class="ni-text">Lista de Requisições</span>
        </x-nav-link>
      
        @endif
       @can('requisicoes-material')
<x-nav-link :href="route('requisicoes-material.index')"
    :active="request()->routeIs('requisicoes-material.*')" class="nav-item">
    <i class="fas fa-dolly ni-icon"></i>
    <span class="ni-text">Req. de Material</span>
</x-nav-link>
@endcan

    </div>
    <div class="sb-flyout">
        <div class="sb-flyout-label">Pedidos / Requisições</div>
        @can('acesso pedidos')
        <a href="{{ route('compras.index') }}"><i class="fas fa-list-ul"></i> Pedidos Internos</a>
        @endcan
        @can('requisicoes-material')
        <a href="{{ route('requisicoes-material.index') }}"><i class="fas fa-dolly"></i> Req. de Material</a>
        @endcan
        @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('gestor'))
        <a href="{{ route('suppliers.index') }}"><i class="fas fa-truck"></i> Fornecedores</a>
        <a href="{{ route('requisicoes.create') }}"><i class="fas fa-plus-circle"></i> Nova Requisição</a>
        <a href="{{ route('requisicoes.index') }}"><i class="fas fa-list"></i> Lista de Requisições</a>
        @endif
    </div>
</div>
@endcanany

            {{-- Combustível --}}
            @can('acesso combustivel')
            <x-nav-link :href="route('fuel.index')" :active="request()->routeIs('fuel.*')" class="nav-item">
                <i class="fas fa-gas-pump ni-icon"></i>
                <span class="ni-text">Gestão de Combustível</span>
            </x-nav-link>
            @endcan

          {{-- Viaturas --}}
@can('acesso viaturas')
<x-nav-link :href="route('viaturas.index')" :active="request()->routeIs('viaturas.*')" class="nav-item">
    <i class="fas fa-truck-moving ni-icon"></i>
    <span class="ni-text">Docs / Viaturas</span>
</x-nav-link>
@endcan

            

            {{-- Discharges --}}
            @can('acesso discharges')
            <x-nav-link :href="route('discharges.index')" :active="request()->routeIs('discharges.*')" class="nav-item">
                <i class="fas fa-sign-out-alt ni-icon"></i>
                <span class="ni-text">Descarga / Navio</span>
            </x-nav-link>
            @endcan


            @can('acesso caixa')
<div class="sb-divider"></div>
<div class="sb-section">Contabilidade</div>
<x-nav-link :href="route('caixa.index')" :active="request()->routeIs('caixa.*')" class="nav-item">
    <i class="fas fa-cash-register ni-icon"></i>
    <span class="ni-text">Caixa e Bancos</span>
</x-nav-link>
@endcan

            {{-- Conta --}}
            <div class="sb-divider"></div>
            <div class="sb-section">Conta</div>

            <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')" class="nav-item">
                <i class="fas fa-user-circle ni-icon"></i>
                <span class="ni-text">Perfil</span>
            </x-nav-link>

            {{-- Admin --}}
            @hasrole('super-admin')
          

<div class="sb-section">Administração</div>
<x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" class="nav-item">
    <i class="fas fa-users-cog ni-icon"></i>
    <span class="ni-text">Utilizadores</span>
</x-nav-link>
<x-nav-link :href="route('admin.backup.index')" :active="request()->routeIs('admin.backup.*')" class="nav-item">
    <i class="fas fa-database ni-icon"></i>
    <span class="ni-text">Backup BD</span>
</x-nav-link>

            @endhasrole

        </nav>

        {{-- Footer com utilizador --}}
        <div class="sb-footer">
            <div class="sb-user">
                <div class="sb-avatar">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="sb-user-info">
                    <div class="sb-user-name">{{ auth()->user()->name ?? 'Utilizador' }}</div>
                    <div class="sb-user-role">{{ auth()->user()->getRoleNames()->first() ?? 'Membro' }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sb-logout">
                    <i class="fas fa-arrow-right-from-bracket" style="font-size:11px;"></i>
                    <span class="sb-logout-text">Terminar sessão</span>
                </button>
            </form>
        </div>

    </aside>

    {{-- ═══════════════════════════════
         MAIN CONTENT
    ═══════════════════════════════ --}}
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

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<div class="toast-wrap" id="toastWrap"></div>

<script>
    /* ── Toast ── */
    function showToast(msg, type, title) {
        type  = type  || 'success';
        title = title || { success:'Sucesso', error:'Erro', warning:'Aviso', info:'Info' }[type];
        var icons = { success:'fas fa-check-circle', error:'fas fa-times-circle', warning:'fas fa-exclamation-triangle', info:'fas fa-info-circle' };
        var id = 'toast-' + Date.now();
        var html =
            '<div class="toast-item" id="'+id+'">' +
              '<div class="toast-icon '+type+'"><i class="'+icons[type]+'"></i></div>' +
              '<div class="toast-body"><div class="toast-title">'+title+'</div><div class="toast-msg">'+msg+'</div></div>' +
              '<button class="toast-close" onclick="dismissToast(\''+id+'\')"><i class="fas fa-times"></i></button>' +
              '<div class="toast-progress"><div class="toast-progress-bar '+type+'" style="width:100%" id="bar-'+id+'"></div></div>' +
            '</div>';
        $('#toastWrap').append(html);
        var el = document.getElementById(id);
        requestAnimationFrame(function(){ requestAnimationFrame(function(){ el.classList.add('show'); }); });
        var bar = document.getElementById('bar-'+id);
        bar.style.transition = 'width 4s linear';
        requestAnimationFrame(function(){ requestAnimationFrame(function(){ bar.style.width = '0%'; }); });
        setTimeout(function(){ dismissToast(id); }, 4000);
    }
    function dismissToast(id) {
        var el = document.getElementById(id);
        if (!el) return;
        el.classList.add('hide');
        setTimeout(function(){ if (el) el.remove(); }, 380);
    }

    @if(session('success')) $(document).ready(function(){ showToast('{{ addslashes(session('success')) }}','success'); }); @endif
    @if(session('error'))   $(document).ready(function(){ showToast('{{ addslashes(session('error')) }}','error'); });   @endif
    @if(session('warning')) $(document).ready(function(){ showToast('{{ addslashes(session('warning')) }}','warning'); }); @endif
    @if(session('info'))    $(document).ready(function(){ showToast('{{ addslashes(session('info')) }}','info'); });    @endif

    /* ── Sidebar toggle ── */
    document.addEventListener('DOMContentLoaded', function () {
        var sidebar = document.getElementById('sidebar');
        var btn     = document.getElementById('toggleBtn');

        if (localStorage.getItem('sb-collapsed') === '1') {
            sidebar.classList.add('collapsed');
        }

        btn.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sb-collapsed', sidebar.classList.contains('collapsed') ? '1' : '0');
        });
    });
</script>

@stack('scripts')
</body>
</html>
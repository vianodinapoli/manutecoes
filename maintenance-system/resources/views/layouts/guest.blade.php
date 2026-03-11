<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,300&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --ink: #0a0a0f;
            --ink-soft: #1c1c2e;
            --surface: #f5f4f0;
            --accent: #2d6ef7;
            --accent-glow: rgba(45, 110, 247, 0.18);
            --accent-2: #00c9a7;
            --muted: #8a8a9a;
            --border: rgba(10,10,15,0.08);
            --mono: 'Space Mono', monospace;
            --sans: 'DM Sans', sans-serif;
        }

        html, body { height: 100%; font-family: var(--sans); background: var(--surface); }

        /* ─── WRAPPER ─── */
        .login-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* ─── LEFT PANEL (form side) ─── */
        .panel-form {
            flex: 0 0 480px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 48px 56px;
            background: #fff;
            position: relative;
            z-index: 2;
            box-shadow: 4px 0 40px rgba(0,0,0,0.06);
        }

        .panel-form::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--accent), var(--accent-2));
        }

        /* Logo area */
        .logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-area img {
            height: 32px;
            width: auto;
            object-fit: contain;
        }

        .logo-badge {
            font-family: var(--mono);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--accent);
            background: var(--accent-glow);
            padding: 3px 8px;
            border-radius: 4px;
        }

        /* Form body */
        .form-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 48px 0 32px;
        }

        .form-eyebrow {
            font-family: var(--mono);
            font-size: 10px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--accent);
            margin-bottom: 10px;
        }

        .form-title {
            font-size: 28px;
            font-weight: 600;
            color: var(--ink);
            line-height: 1.25;
            letter-spacing: -0.02em;
            margin-bottom: 8px;
        }

        .form-subtitle {
            font-size: 14px;
            color: var(--muted);
            font-weight: 300;
            margin-bottom: 36px;
            line-height: 1.6;
        }

        /* Slot content */
        .slot-content { width: 100%; }

        /* Footer */
        .panel-footer {
            padding-top: 24px;
            border-top: 1px solid var(--border);
        }

        .footer-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .footer-copy {
            font-family: var(--mono);
            font-size: 10px;
            color: var(--muted);
            letter-spacing: 0.06em;
        }

        .status-dot {
            display: flex;
            align-items: center;
            gap: 6px;
            font-family: var(--mono);
            font-size: 10px;
            color: var(--accent-2);
            letter-spacing: 0.06em;
        }

        .status-dot::before {
            content: '';
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--accent-2);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.85); }
        }

        /* ─── RIGHT PANEL (photo side) ─── */
        .panel-photo {
            flex: 1;
            position: relative;
            overflow: hidden;
            background: var(--ink);
        }

        /* Photo slot — swap the background-image URL or use an <img> */
        .panel-photo-bg {
            position: absolute;
            inset: 0;
            /* background-image: url('https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=1200&q=80'); */
           background-image: url("{{ asset('images/bymozelogin.jpeg') }}");
            background-size: cover;
            background-position: center;
            filter: brightness(0.55) saturate(0.8);
            transition: transform 12s ease;
        }

        .panel-photo:hover .panel-photo-bg {
            transform: scale(1.04);
        }

        /* Overlay grid pattern */
        .panel-photo-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(45,110,247,0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(45,110,247,0.06) 1px, transparent 1px);
            background-size: 48px 48px;
        }

        /* Bottom gradient fade */
        .panel-photo-fade {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                to top,
                rgba(10,10,15,0.92) 0%,
                rgba(10,10,15,0.3) 50%,
                transparent 100%
            );
        }

        /* Top-right decorative corner */
        .panel-photo-corner {
            position: absolute;
            top: 40px; right: 40px;
            width: 80px; height: 80px;
            border-top: 1px solid rgba(255,255,255,0.15);
            border-right: 1px solid rgba(255,255,255,0.15);
        }

        /* Bottom-left decorative corner */
        .panel-photo-corner-bl {
            position: absolute;
            bottom: 40px; left: 40px;
            width: 40px; height: 40px;
            border-bottom: 1px solid rgba(45,110,247,0.5);
            border-left: 1px solid rgba(45,110,247,0.5);
        }

        /* Photo content */
        .panel-photo-content {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 56px;
        }

        .photo-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: var(--mono);
            font-size: 10px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.5);
            margin-bottom: 16px;
        }

        .photo-tag-line {
            display: inline-block;
            width: 24px;
            height: 1px;
            background: var(--accent);
        }

        .photo-headline {
            font-size: 36px;
            font-weight: 600;
            color: #fff;
            line-height: 1.2;
            letter-spacing: -0.025em;
            margin-bottom: 16px;
            max-width: 420px;
        }

        .photo-headline em {
            font-style: normal;
            color: var(--accent-2);
        }

        .photo-desc {
            font-size: 14px;
            color: rgba(255,255,255,0.45);
            font-weight: 300;
            line-height: 1.7;
            max-width: 340px;
            margin-bottom: 36px;
        }

        /* Stats bar */
        .photo-stats {
            display: flex;
            gap: 32px;
        }

        .stat-item { display: flex; flex-direction: column; gap: 4px; }

        .stat-value {
            font-family: var(--mono);
            font-size: 20px;
            font-weight: 700;
            color: #fff;
            letter-spacing: -0.03em;
        }

        .stat-label {
            font-size: 11px;
            color: rgba(255,255,255,0.35);
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .stat-divider {
            width: 1px;
            background: rgba(255,255,255,0.1);
            align-self: stretch;
        }

        /* ─── RESPONSIVE ─── */
        @media (max-width: 900px) {
            .login-wrapper { flex-direction: column; }
            .panel-form { flex: none; width: 100%; padding: 40px 32px; box-shadow: none; }
            .panel-photo { min-height: 260px; flex: none; }
            .panel-photo-content { padding: 32px; }
            .photo-headline { font-size: 24px; }
            .photo-stats { gap: 20px; }
        }

        @media (max-width: 480px) {
            .panel-form { padding: 32px 24px; }
        }
    </style>
</head>
<body>

<div class="login-wrapper">

    <!-- ══════════════════════════════════
         LEFT — Form Panel
    ══════════════════════════════════ -->
    <div class="panel-form">

        <div class="logo-area">
            <a href="{{ route('dashboard') }}">
                <img src="{{ asset('images/bymozelogo.png') }}" alt="Bymoze Logo">
            </a>
            <span class="logo-badge">Platform</span>
        </div>

        <div class="form-body">
            <p class="form-eyebrow">Acesso seguro</p>
            <h1 class="form-title">Bem-vindo de volta.</h1>
            <p class="form-subtitle">Introduza as suas credenciais para<br>aceder à plataforma.</p>

            <div class="slot-content">
                {{ $slot }}
            </div>
        </div>

        <footer class="panel-footer">
            <div class="footer-meta">
                <span class="footer-copy">&copy; {{ date('Y') }} Bymoze · v2.0</span>
                <span class="status-dot">Sistema operacional</span>
            </div>
        </footer>

    </div>

    <!-- ══════════════════════════════════
         RIGHT — Photo Panel
         Troque a URL em .panel-photo-bg
         pelo seu asset: asset('images/login-bg.jpg')
    ══════════════════════════════════ -->
    <div class="panel-photo">
        <div class="panel-photo-bg"></div>
        <div class="panel-photo-grid"></div>
        <div class="panel-photo-fade"></div>

        <!-- Decorative corners -->
        <div class="panel-photo-corner"></div>
        <div class="panel-photo-corner-bl"></div>

        <div class="panel-photo-content">
            <span class="photo-tag">
                <span class="photo-tag-line"></span>
                Bymoze · Transporte e serviços
            </span>

            <h2 class="photo-headline">
                Gestão de frota,<br>manutenção, Stock, requisições<em> e</em><br>Descarga de Navio.
            </h2>

            <p class="photo-desc">
                Rastreamento em tempo real, gestão de frota e relatórios automáticos — tudo numa única plataforma.
            </p>

            <div class="photo-stats">
                <div class="stat-item">
                    <span class="stat-value">70%</span>
                    <span class="stat-label">Desenvolvimento</span>
                </div>
                <div class="stat-divider"></div>
                <!-- <div class="stat-item">
                    <span class="stat-value">+1.2k</span>
                    <span class="stat-label">Entregas/mês</span>
                </div> -->
                <!-- <div class="stat-divider"></div>
                <div class="stat-item">
                    <span class="stat-value">256-bit</span>
                    <span class="stat-label">Encriptação</span> -->
                </div>
            </div>
        </div>
    </div>

</div>

</body>
</html>
{{-- 
    SKELETON LOADING — Dashboard
    Uso: inclui este ficheiro no topo do teu layout ou dashboard.blade.php
    O skeleton aparece automaticamente e desaparece quando a página carrega.
    
    Adiciona isto no teu dashboard.blade.php:
    @include('components.skeleton-loading')
--}}

<style>
    /* ── BASE ── */
    #skeleton-overlay {
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: #f5f6fa;
        display: flex;
        opacity: 1;
        transition: opacity 0.4s ease, visibility 0.4s ease;
    }

    #skeleton-overlay.fade-out {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }

    /* ── SHIMMER ANIMATION ── */
    @keyframes shimmer {
        0%   { background-position: -800px 0; }
        100% { background-position:  800px 0; }
    }

    .sk {
        background: linear-gradient(
            90deg,
            #e8eaf0 25%,
            #f4f5f9 50%,
            #e8eaf0 75%
        );
        background-size: 800px 100%;
        animation: shimmer 1.6s infinite linear;
        border-radius: 6px;
    }

    .sk-dark {
        background: linear-gradient(
            90deg,
            #1e2235 25%,
            #252a3d 50%,
            #1e2235 75%
        );
        background-size: 800px 100%;
        animation: shimmer 1.6s infinite linear;
        border-radius: 6px;
    }

    /* ── SIDEBAR ── */
    .sk-sidebar {
        width: 240px;
        min-height: 100vh;
        background: #151929;
        flex-shrink: 0;
        padding: 28px 20px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .sk-sidebar-logo {
        height: 36px;
        width: 120px;
        margin-bottom: 32px;
    }

    .sk-sidebar-item {
        height: 40px;
        width: 100%;
        border-radius: 8px;
    }

    .sk-sidebar-item.active {
        background: linear-gradient(90deg, #1e2d5a 25%, #243370 50%, #1e2d5a 75%) !important;
        background-size: 800px 100% !important;
        animation: shimmer 1.6s infinite linear !important;
    }

    .sk-sidebar-section {
        height: 10px;
        width: 60px;
        margin: 16px 0 8px;
        border-radius: 4px;
    }

    /* ── MAIN CONTENT ── */
    .sk-main {
        flex: 1;
        padding: 28px 32px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        gap: 28px;
    }

    /* Header row */
    .sk-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .sk-header-title { height: 28px; width: 200px; }
    .sk-header-right { display: flex; gap: 12px; align-items: center; }
    .sk-header-btn   { height: 36px; width: 100px; border-radius: 8px; }
    .sk-avatar       { height: 36px; width: 36px; border-radius: 50%; flex-shrink: 0; }

    /* Stats cards */
    .sk-cards {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }

    .sk-card {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    }

    .sk-card-icon   { height: 36px; width: 36px; border-radius: 8px; }
    .sk-card-value  { height: 28px; width: 80px; }
    .sk-card-label  { height: 12px; width: 110px; }
    .sk-card-trend  { height: 10px; width: 60px; }

    /* Middle row: chart + small chart */
    .sk-middle {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 16px;
    }

    .sk-chart-box {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .sk-chart-title { height: 18px; width: 160px; }
    .sk-chart-sub   { height: 12px; width: 100px; }

    .sk-chart-bars {
        display: flex;
        align-items: flex-end;
        gap: 8px;
        height: 140px;
        margin-top: 8px;
    }

    .sk-bar {
        flex: 1;
        border-radius: 4px 4px 0 0;
    }

    .sk-donut-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 20px;
        padding: 24px;
    }

    .sk-donut {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .sk-legend {
        display: flex;
        flex-direction: column;
        gap: 10px;
        width: 100%;
        padding: 0 8px;
    }

    .sk-legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .sk-legend-dot  { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
    .sk-legend-text { height: 11px; flex: 1; border-radius: 4px; }

    /* Table */
    .sk-table-box {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .sk-table-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .sk-table-title  { height: 18px; width: 140px; }
    .sk-table-filter { height: 32px; width: 120px; border-radius: 8px; }

    .sk-table-cols {
        display: grid;
        grid-template-columns: 2fr 1.5fr 1fr 1fr 80px;
        gap: 12px;
        padding: 0 0 12px;
        border-bottom: 1px solid #f0f1f5;
        margin-bottom: 4px;
    }

    .sk-col-label { height: 11px; border-radius: 4px; }

    .sk-table-row {
        display: grid;
        grid-template-columns: 2fr 1.5fr 1fr 1fr 80px;
        gap: 12px;
        align-items: center;
        padding: 14px 0;
        border-bottom: 1px solid #f8f9fc;
    }

    .sk-table-row:last-child { border-bottom: none; }

    .sk-cell       { height: 14px; border-radius: 4px; }
    .sk-cell-short { height: 14px; width: 60%; border-radius: 4px; }
    .sk-badge      { height: 22px; width: 64px; border-radius: 20px; }
    .sk-cell-name  {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .sk-cell-name .sk-avatar { width: 28px; height: 28px; }
    .sk-cell-name .sk-cell   { flex: 1; }

    /* ── RESPONSIVE ── */
    @media (max-width: 1100px) {
        .sk-cards  { grid-template-columns: repeat(2, 1fr); }
        .sk-middle { grid-template-columns: 1fr; }
    }

    @media (max-width: 768px) {
        .sk-sidebar { display: none; }
        .sk-main    { padding: 20px 16px; }
        .sk-cards   { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 480px) {
        .sk-cards { grid-template-columns: 1fr; }
    }
</style>

<div id="skeleton-overlay">

    {{-- ══ SIDEBAR ══ --}}
    <div class="sk-sidebar">
        <div class="sk sk-dark sk-sidebar-logo"></div>

        <div class="sk sk-dark sk-sidebar-item active"></div>
        <div class="sk sk-dark sk-sidebar-item"></div>
        <div class="sk sk-dark sk-sidebar-item"></div>

        <div class="sk sk-dark sk-sidebar-section"></div>
        <div class="sk sk-dark sk-sidebar-item"></div>
        <div class="sk sk-dark sk-sidebar-item"></div>
        <div class="sk sk-dark sk-sidebar-item"></div>
        <div class="sk sk-dark sk-sidebar-item"></div>

        <div class="sk sk-dark sk-sidebar-section" style="margin-top:auto"></div>
        <div class="sk sk-dark sk-sidebar-item"></div>
    </div>

    {{-- ══ MAIN ══ --}}
    <div class="sk-main">

        {{-- Header --}}
        <div class="sk-header">
            <div class="sk sk-header-title"></div>
            <div class="sk-header-right">
                <div class="sk sk-header-btn"></div>
                <div class="sk sk-avatar"></div>
            </div>
        </div>

        {{-- Stats cards --}}
        <div class="sk-cards">
            @for ($i = 0; $i < 4; $i++)
            <div class="sk-card">
                <div class="sk sk-card-icon"></div>
                <div class="sk sk-card-value"></div>
                <div class="sk sk-card-label"></div>
                <div class="sk sk-card-trend"></div>
            </div>
            @endfor
        </div>

        {{-- Charts row --}}
        <div class="sk-middle">

            {{-- Bar chart --}}
            <div class="sk-chart-box">
                <div class="sk sk-chart-title"></div>
                <div class="sk sk-chart-sub"></div>
                <div class="sk-chart-bars">
                    @php $heights = [55, 80, 45, 95, 65, 75, 50, 85, 60, 90, 70, 40]; @endphp
                    @foreach($heights as $h)
                    <div class="sk" style="height: {{ $h }}%; flex:1; border-radius: 4px 4px 0 0;"></div>
                    @endforeach
                </div>
            </div>

            {{-- Donut chart --}}
            <div class="sk-chart-box sk-donut-wrap">
                <div class="sk sk-donut"></div>
                <div class="sk-legend">
                    @for($i = 0; $i < 4; $i++)
                    <div class="sk-legend-item">
                        <div class="sk sk-legend-dot"></div>
                        <div class="sk sk-legend-text" style="width: {{ [70, 55, 80, 45][$i] }}%"></div>
                    </div>
                    @endfor
                </div>
            </div>

        </div>

        {{-- Table --}}
        <div class="sk-table-box">
            <div class="sk-table-head">
                <div class="sk sk-table-title"></div>
                <div class="sk sk-table-filter"></div>
            </div>

            <div class="sk-table-cols">
                @foreach([90, 70, 60, 55, 50] as $w)
                <div class="sk sk-col-label" style="width: {{ $w }}%"></div>
                @endforeach
            </div>

            @for($i = 0; $i < 6; $i++)
            <div class="sk-table-row">
                <div class="sk-cell-name">
                    <div class="sk sk-avatar"></div>
                    <div class="sk sk-cell"></div>
                </div>
                <div class="sk sk-cell-short"></div>
                <div class="sk sk-cell-short"></div>
                <div class="sk sk-badge"></div>
                <div class="sk sk-cell-short" style="width:40%"></div>
            </div>
            @endfor
        </div>

    </div>{{-- /sk-main --}}
</div>{{-- /skeleton-overlay --}}

<script>
    // Aguarda o DOM + recursos carregarem, depois dissolve o skeleton
    window.addEventListener('load', function () {
        const overlay = document.getElementById('skeleton-overlay');
        if (!overlay) return;

        // Pequeno delay para garantir que o conteúdo real já renderizou
        setTimeout(function () {
            overlay.classList.add('fade-out');
            // Remove do DOM após a transição para não bloquear cliques
            setTimeout(function () {
                overlay.remove();
            }, 450);
        }, 300);
    });
</script>
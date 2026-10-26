<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<style>
    /* ── Separadores entre dashboards ── */
    .dash-tabs{display:inline-flex;background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:3px;gap:2px}
    .dash-tabs a{font-size:.75rem;font-weight:600;color:#64748b;padding:6px 14px;border-radius:8px;text-decoration:none}
    .dash-tabs a:hover{color:#1e293b}
    .dash-tabs a.active{background:#1e293b;color:#fff}

    /* ── KPI cards ── */
    .kpi-card{background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:16px 20px;display:flex;align-items:center;gap:14px;box-shadow:0 1px 4px rgba(0,0,0,.04)}
    .kpi-card.blue  {border-left:4px solid #1a56db}
    .kpi-card.yellow{border-left:4px solid #f59e0b}
    .kpi-card.red   {border-left:4px solid #dc2626}
    .kpi-card.green {border-left:4px solid #16a34a}
    .kpi-icon{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0}
    .kpi-icon.blue  {background:#eff6ff;color:#1a56db}
    .kpi-icon.yellow{background:#fefce8;color:#f59e0b}
    .kpi-icon.red   {background:#fef2f2;color:#dc2626}
    .kpi-icon.green {background:#f0fdf4;color:#16a34a}
    .kpi-label{font-size:.6rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#94a3b8;margin-bottom:2px}
    .kpi-value{font-size:1.5rem;font-weight:800;color:#1e293b;line-height:1}
    .kpi-unit{font-size:.7rem;font-weight:600;color:#94a3b8;margin-left:3px}

    /* ── Chart cards ── */
    .chart-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;box-shadow:0 1px 4px rgba(0,0,0,.04);overflow:hidden}
    .chart-card-head{padding:16px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between}
    .chart-card-title{font-size:.8rem;font-weight:700;color:#334155}
    .chart-card-sub{font-size:.7rem;color:#94a3b8;font-weight:400}
    .chart-card-body{padding:20px}
    .chart-empty{height:100%;display:flex;align-items:center;justify-content:center;font-size:.78rem;color:#94a3b8}

    /* ── Table card ── */
    .table-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;box-shadow:0 1px 4px rgba(0,0,0,.04);overflow:hidden}
    .table-card-head{padding:16px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:10px}
    .table-card-icon{width:32px;height:32px;background:#1e293b;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:.85rem;flex-shrink:0}

    /* ── Table ── */
    .tabela-dash thead th{font-size:.6rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#94a3b8;background:#f8fafc;border-bottom:1px solid #e2e8f0;padding:9px 14px}
    .tabela-dash tbody td{font-size:.8rem;color:#334155;padding:10px 14px;border-bottom:1px solid #f1f5f9;vertical-align:middle}
    .tabela-dash tbody tr:last-child td{border-bottom:none}
    .tabela-dash tbody tr:hover{background:#f8fafc}
    .mono{font-family:monospace;background:#f1f5f9;padding:1px 6px;border-radius:4px;color:#475569;font-size:.72rem}

    /* ── Badges ── */
    .cat-badge{display:inline-flex;align-items:center;padding:3px 9px;border-radius:6px;font-size:.68rem;font-weight:700;border:1px solid}
    .cat-badge.ativa    {background:#eff6ff;color:#1a56db;border-color:#bfdbfe}
    .cat-badge.concluida{background:#f0fdf4;color:#166534;border-color:#bbf7d0}
    .cat-badge.outra    {background:#f8fafc;color:#475569;border-color:#e2e8f0}
    .cat-badge.alerta   {background:#fef2f2;color:#b91c1c;border-color:#fecaca}
    .cat-badge.ok       {background:#f0fdf4;color:#166534;border-color:#bbf7d0}

    /* ── Barra de progresso ── */
    .prog{height:6px;background:#f1f5f9;border-radius:99px;overflow:hidden;min-width:90px}
    .prog > span{display:block;height:100%;background:#1e293b;border-radius:99px}
    .prog > span.full{background:#16a34a}
    .prog > span.over{background:#dc2626}

    /* ── DataTables overrides ── */
    .dataTables_wrapper{padding:14px 20px 16px}
    .dataTables_wrapper .dataTables_filter input{font-size:.8rem;border:1px solid #e2e8f0;border-radius:8px;padding:5px 10px;color:#334155}
    .dataTables_wrapper .dataTables_filter input:focus{outline:none;border-color:#64748b;box-shadow:0 0 0 3px rgba(100,116,139,.1)}
    .dataTables_wrapper .dataTables_length select{font-size:.8rem;border:1px solid #e2e8f0;border-radius:8px;padding:4px 8px}
    .dataTables_wrapper .dataTables_info,.dataTables_wrapper .dataTables_paginate{font-size:.75rem;color:#94a3b8;margin-top:10px}
    .dataTables_wrapper .paginate_button{border-radius:6px!important;font-size:.75rem!important}
    .dataTables_wrapper .paginate_button.current{background:#1e293b!important;color:#fff!important;border:none!important;border-radius:6px!important}
</style>
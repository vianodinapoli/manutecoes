<x-app-layout>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    .page-wrap{max-width:1100px;margin:0 auto;padding:32px 24px 48px}

    /* ── Cards ── */
    .section{background:#fff;border:1px solid #e2e8f0;border-radius:12px;margin-bottom:16px;overflow:hidden}
    .section-head{display:flex;align-items:center;gap:10px;padding:12px 20px;background:#f8fafc;border-bottom:1px solid #e2e8f0}
    .section-head .s-num{font-size:.6rem;font-weight:800;letter-spacing:1.5px;color:#94a3b8;text-transform:uppercase;background:#e2e8f0;border-radius:4px;padding:2px 7px}
    .section-head .s-title{font-size:.8rem;font-weight:700;color:#334155;letter-spacing:.3px}
    .section-body{padding:20px 24px}

    /* ── Campos ── */
    .field-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px 24px}
    .field-grid.col-2{grid-template-columns:repeat(2,1fr)}
    .field-lbl{font-size:.6rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#94a3b8;margin-bottom:3px}
    .field-val{font-size:.83rem;font-weight:600;color:#1e293b}

    /* ── Status badge ── */
    .s-badge{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:6px;font-size:.68rem;font-weight:700;letter-spacing:.3px;border:1px solid}
    .s-badge.operacional{background:#f0fdf4;color:#166534;border-color:#bbf7d0}
    .s-badge.manutencao {background:#f0f9ff;color:#0369a1;border-color:#bae6fd}
    .s-badge.avariada   {background:#fef2f2;color:#dc2626;border-color:#fecaca}
    .s-badge.desativada {background:#f8fafc;color:#475569;border-color:#e2e8f0}

    /* ── Botões topo ── */
    .top-btn{display:inline-flex;align-items:center;gap:6px;padding:7px 16px;border-radius:8px;font-size:.78rem;font-weight:600;text-decoration:none;border:1px solid #e2e8f0;background:#fff;color:#475569;transition:all .15s;cursor:pointer}
    .top-btn:hover{background:#f8fafc;border-color:#cbd5e1;color:#334155}
    .top-btn.dark{background:#1e293b;border-color:#1e293b;color:#fff}
    .top-btn.dark:hover{background:#334155;color:#fff}
    .top-btn.warn{background:#fff;border-color:#fde68a;color:#92400e}
    .top-btn.warn:hover{background:#fefce8}
    .top-btn.danger{background:#fff;border-color:#fecaca;color:#dc2626}
    .top-btn.danger:hover{background:#fef2f2}

    /* ── Tabela histórico ── */
    .hist-table{width:100%;border-collapse:collapse}
    .hist-table thead th{font-size:.6rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#94a3b8;padding:8px 16px;background:#f8fafc;border-bottom:1px solid #e2e8f0;text-align:left}
    .hist-table tbody td{padding:10px 16px;font-size:.8rem;color:#334155;border-bottom:1px solid #f1f5f9}
    .hist-table tbody tr:last-child td{border-bottom:none}
    .hist-table tbody tr:hover{background:#f8fafc}

    /* ── Action buttons ── */
    .action-btn{width:28px;height:28px;border-radius:7px;border:1px solid #e2e8f0;background:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:.8rem;color:#475569;text-decoration:none;transition:all .15s;cursor:pointer}
    .action-btn:hover{background:#f8fafc;border-color:#94a3b8;color:#1e293b}

    /* ── Obs block ── */
    .obs-block{border:1px solid #e2e8f0;border-radius:8px;padding:14px 16px;background:#f8fafc;font-size:.82rem;line-height:1.65;color:#334155;min-height:80px}
</style>

<div class="page-wrap">

    @php
        $statusKey = match($machine->status) {
            'Operacional'   => 'operacional',
            'Em Manutenção' => 'manutencao',
            'Avariada'      => 'avariada',
            'Desativada'    => 'desativada',
            default         => 'desativada',
        };
        $statusIcon = match($machine->status) {
            'Operacional'   => 'check-circle-fill',
            'Em Manutenção' => 'gear-wide-connected',
            'Avariada'      => 'exclamation-triangle-fill',
            'Desativada'    => 'dash-circle',
            default         => 'dash-circle',
        };
    @endphp

    {{-- CABEÇALHO --}}
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <div style="font-size:.6rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">
                Detalhes do Equipamento
            </div>
            <h4 class="fw-bold mb-1" style="color:#1e293b;font-size:1.25rem;">
                {{ $machine->numero_interno }}
                <span style="color:#cbd5e1;font-weight:300;margin:0 6px;">·</span>
                <span style="color:#475569;font-weight:500;font-size:1rem;">{{ $machine->tipo_equipamento }}</span>
            </h4>
            <div style="font-size:.72rem;color:#94a3b8;">
                Registado em {{ $machine->created_at->format('d/m/Y H:i') }}
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="s-badge {{ $statusKey }}">
                <i class="bi bi-{{ $statusIcon }}"></i> {{ $machine->status }}
            </span>
        </div>
    </div>

    {{-- ACÇÕES --}}
    <div class="d-flex gap-2 mb-4">
        <a href="{{ route('machines.index') }}" class="top-btn">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
        <a href="{{ route('machines.edit', $machine->id) }}" class="top-btn warn">
            <i class="bi bi-pencil"></i> Editar
        </a>
        <a href="{{ route('maintenances.createFromMachine', $machine->id) }}" class="top-btn danger">
            <i class="bi bi-exclamation-triangle"></i> Nova Manutenção
        </a>
    </div>

    {{-- FLASH --}}
    @if(session('success'))
    <div class="d-flex align-items-center gap-2 mb-3 px-3 py-2 rounded"
         style="background:#f0fdf4;border:1px solid #bbf7d0;font-size:.8rem;color:#166534;">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
    @endif

    {{-- 01 — IDENTIFICAÇÃO --}}
    <div class="section">
        <div class="section-head">
            <span class="s-num">01</span>
            <span class="s-title">Informação de Identificação</span>
        </div>
        <div class="section-body">
            <div class="field-grid">
                <div>
                    <div class="field-lbl">Nº Interno</div>
                    <div class="field-val" style="color:#1a56db;">{{ $machine->numero_interno }}</div>
                </div>
                <div>
                    <div class="field-lbl">Tipo de Equipamento</div>
                    <div class="field-val">{{ $machine->tipo_equipamento }}</div>
                </div>
                <div>
                    <div class="field-lbl">Estado</div>
                    <span class="s-badge {{ $statusKey }}" style="margin-top:2px;">
                        <i class="bi bi-{{ $statusIcon }}"></i> {{ $machine->status }}
                    </span>
                </div>
                <div>
                    <div class="field-lbl">Marca</div>
                    <div class="field-val">{{ $machine->marca ?? '—' }}</div>
                </div>
                <div>
                    <div class="field-lbl">Modelo</div>
                    <div class="field-val">{{ $machine->modelo ?? '—' }}</div>
                </div>
                <div>
                    <div class="field-lbl">Operador / Responsável</div>
                    <div class="field-val">{{ $machine->operador ?? '—' }}</div>
                </div>
                <div>
                    <div class="field-lbl">Matrícula</div>
                    <div class="field-val">{{ $machine->matricula ?? '—' }}</div>
                </div>
                <div>
                    <div class="field-lbl">Nº de Chassi</div>
                    <div class="field-val">{{ $machine->nr_chassi ?? '—' }}</div>
                </div>
                <div>
                    <div class="field-lbl">Localização</div>
                    <div class="field-val">{{ $machine->localizacao }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- 02 — OBSERVAÇÕES --}}
    <div class="section">
        <div class="section-head">
            <span class="s-num">02</span>
            <span class="s-title">Observações</span>
        </div>
        <div class="section-body">
            <div class="obs-block">
                {{ $machine->observacoes ?? 'Nenhuma observação registada.' }}
            </div>
        </div>
    </div>

    {{-- 03 — HISTÓRICO DE MANUTENÇÕES --}}
    <div class="section">
        <div class="section-head">
            <span class="s-num">03</span>
            <span class="s-title">Histórico de Manutenções</span>
            @if(isset($maintenances))
            <span style="margin-left:auto;font-size:.68rem;color:#94a3b8;font-weight:600;">
                {{ $maintenances->count() }} registo(s)
            </span>
            @endif
        </div>

        @if(!isset($maintenances))
        <div class="section-body" style="font-size:.8rem;color:#f59e0b;">
            <i class="bi bi-exclamation-triangle me-1"></i>
            A variável <code>$maintenances</code> não está disponível no Controller.
        </div>

        @elseif($maintenances->isEmpty())
        <div class="section-body text-center" style="color:#94a3b8;font-size:.8rem;padding:32px;">
            <i class="bi bi-inbox" style="font-size:1.5rem;display:block;margin-bottom:8px;opacity:.35;"></i>
            Ainda não há registos de manutenção para esta máquina.
        </div>

        @else
        <table class="hist-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Estado</th>
                    <th>Descrição da Avaria</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($maintenances as $maintenance)
                @php
                    $mStatusKey = match(strtolower($maintenance->status)) {
                        'pendente'      => ['pendente',   'clock-history',        'fefce8','854d0e','fde68a'],
                        'em_manutencao' => ['manutencao', 'gear-wide-connected',  'f0f9ff','0369a1','bae6fd'],
                        'concluida'     => ['concluida',  'check-circle-fill',    'f0fdf4','166534','bbf7d0'],
                        default         => ['desativada', 'dash-circle',          'f8fafc','475569','e2e8f0'],
                    };
                @endphp
                <tr>
                    <td style="color:#94a3b8;font-size:.75rem;">#{{ str_pad($maintenance->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>
                        <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:6px;font-size:.68rem;font-weight:700;background:#{{ $mStatusKey[2] }};color:#{{ $mStatusKey[3] }};border:1px solid #{{ $mStatusKey[4] }};">
                            <i class="bi bi-{{ $mStatusKey[1] }}"></i>
                            {{ ucfirst(str_replace('_', ' ', $maintenance->status)) }}
                        </span>
                    </td>
                    <td>{{ Str::limit($maintenance->failure_description ?? $maintenance->title ?? '—', 60) }}</td>
                    <td style="color:#94a3b8;white-space:nowrap;">{{ $maintenance->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('maintenances.show', $maintenance->id) }}"
                               class="action-btn" title="Ver detalhes">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('maintenances.edit', $maintenance->id) }}"
                               class="action-btn" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    {{-- RODAPÉ --}}
    <div class="d-flex justify-content-between align-items-center mt-3"
         style="padding-top:16px;border-top:1px solid #f1f5f9;">
        <a href="{{ route('machines.index') }}" class="top-btn">
            <i class="bi bi-arrow-left"></i> Voltar à Lista
        </a>
        <span style="font-size:.65rem;color:#cbd5e1;">
            {{ $machine->numero_interno }} · {{ $machine->created_at->format('d/m/Y') }}
        </span>
    </div>

</div>
</x-app-layout>
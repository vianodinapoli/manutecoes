<x-app-layout>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    .page-wrap{max-width:960px;margin:0 auto;padding:32px 24px 48px}
    .section{background:#fff;border:1px solid #e2e8f0;border-radius:12px;margin-bottom:16px;overflow:hidden}
    .section-head{display:flex;align-items:center;gap:10px;padding:12px 20px;background:#f8fafc;border-bottom:1px solid #e2e8f0}
    .section-head .s-num{font-size:.6rem;font-weight:800;letter-spacing:1.5px;color:#94a3b8;text-transform:uppercase;background:#e2e8f0;border-radius:4px;padding:2px 7px}
    .section-head .s-title{font-size:.8rem;font-weight:700;color:#334155;letter-spacing:.3px}
    .section-body{padding:20px 24px}
    .field-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px 24px}
    .field-grid.col-2{grid-template-columns:repeat(2,1fr)}
    .field-lbl{font-size:.6rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#94a3b8;margin-bottom:3px}
    .field-val{font-size:.83rem;font-weight:600;color:#1e293b}
    .text-block{border:1px solid #e2e8f0;border-radius:8px;padding:14px 16px;background:#f8fafc;font-size:.82rem;line-height:1.65;color:#334155}
    .text-block-label{display:flex;align-items:center;gap:6px;font-size:.6rem;font-weight:800;letter-spacing:1px;text-transform:uppercase;color:#94a3b8;margin-bottom:8px}
    .text-block-label .dot{width:6px;height:6px;border-radius:50%;flex-shrink:0}
    .s-badge{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:6px;font-size:.68rem;font-weight:700;letter-spacing:.3px;border:1px solid}
    .s-badge.pendente{background:#fefce8;color:#854d0e;border-color:#fde68a}
    .s-badge.em_manutencao{background:#f0f9ff;color:#0369a1;border-color:#bae6fd}
    .s-badge.concluida{background:#f0fdf4;color:#166534;border-color:#bbf7d0}
    .s-badge.cancelada{background:#f8fafc;color:#475569;border-color:#cbd5e1}
    .parts-table{width:100%;border-collapse:collapse}
    .parts-table thead th{font-size:.6rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#94a3b8;padding:8px 16px;background:#f8fafc;border-bottom:1px solid #e2e8f0;text-align:left}
    .parts-table tbody td{padding:10px 16px;font-size:.8rem;color:#334155;border-bottom:1px solid #f1f5f9}
    .parts-table tbody tr:last-child td{border-bottom:none}
    .parts-table tbody tr:hover{background:#f8fafc}
    .qty-badge{display:inline-block;background:#e2e8f0;color:#475569;border-radius:5px;padding:1px 8px;font-size:.72rem;font-weight:700}
    .finance-wrap{display:grid;grid-template-columns:1fr 1fr;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;max-width:480px}
    .finance-col{padding:20px 24px;text-align:center}
    .finance-col:first-child{border-right:1px solid #e2e8f0}
    .finance-currency{font-size:.6rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#94a3b8;margin-bottom:6px}
    .finance-amount{font-size:1.5rem;font-weight:800;color:#1e293b;letter-spacing:-.5px}
    .finance-amount span{font-size:.8rem;font-weight:500;color:#94a3b8;margin-left:3px}
    .finance-rate{grid-column:1/-1;background:#f8fafc;border-top:1px solid #e2e8f0;text-align:center;padding:8px;font-size:.68rem;color:#94a3b8;font-weight:500}
    .top-btn{display:inline-flex;align-items:center;gap:6px;padding:7px 16px;border-radius:8px;font-size:.78rem;font-weight:600;text-decoration:none;border:1px solid #e2e8f0;background:#fff;color:#475569;transition:background .15s,border-color .15s;cursor:pointer}
    .top-btn:hover{background:#f8fafc;border-color:#cbd5e1;color:#334155}
    .top-btn.primary{background:#1e293b;border-color:#1e293b;color:#fff}
    .top-btn.primary:hover{background:#334155;border-color:#334155;color:#fff}
    .file-link{display:inline-flex;align-items:center;gap:7px;font-size:.8rem;color:#475569;text-decoration:none;padding:6px 12px;border:1px solid #e2e8f0;border-radius:7px;background:#f8fafc;transition:border-color .15s}
    .file-link:hover{border-color:#94a3b8;color:#1e293b}
    .inner-divider{height:1px;background:#f1f5f9;margin:16px 0}
    @media print {
        .no-print{display:none!important}
        body *{visibility:hidden}
        #printArea,#printArea *{visibility:visible}
        #printArea{position:absolute;left:0;top:0;width:210mm;padding:12mm;font-size:10pt}
        .section{border:1px solid #ccc!important;box-shadow:none!important;break-inside:avoid}
    }
</style>

<div class="page-wrap" id="printArea">

    {{-- CABEÇALHO --}}
    <div class="d-flex justify-content-between align-items-start mb-3 no-print">
        <div>
            <div style="font-size:.6rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">
                Registo de Intervenção Técnica
            </div>
            <h4 class="fw-bold mb-1" style="color:#1e293b;font-size:1.25rem;">
                #{{ str_pad($maintenance->id, 4, '0', STR_PAD_LEFT) }}
                <span style="color:#cbd5e1;font-weight:300;margin:0 6px;">·</span>
                <span style="color:#475569;font-weight:600;font-size:1rem;">{{ $maintenance->machine->numero_interno ?? 'N/A' }}</span>
            </h4>
            <div style="font-size:.72rem;color:#94a3b8;">
                Criado em {{ $maintenance->created_at->format('d/m/Y \à\s H:i') }}
            </div>
        </div>
        @php
            $statusLower   = strtolower($maintenance->status);
            $statusDisplay = ucfirst(str_replace('_', ' ', $statusLower));
            $statusIcon    = match($statusLower) {
                'pendente'      => 'clock-history',
                'em_manutencao' => 'gear-wide-connected',
                'concluida'     => 'check-circle-fill',
                'cancelada'     => 'x-circle',
                default         => 'dash-circle',
            };
            $costEUR = $maintenance->total_cost ?? 0;
            $costMZN = $costEUR * ($exchangeRate ?? 1);
        @endphp
        <span class="s-badge {{ $statusLower }}">
            <i class="bi bi-{{ $statusIcon }}"></i> {{ $statusDisplay }}
        </span>
    </div>

    {{-- ACÇÕES --}}
    <div class="d-flex gap-2 mb-4 no-print">
        <a href="{{ route('maintenances.index') }}" class="top-btn">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
        <a href="{{ route('maintenances.edit', $maintenance->id) }}" class="top-btn">
            <i class="bi bi-pencil"></i> Editar
        </a>
        <button onclick="window.print()" class="top-btn primary">
            <i class="bi bi-printer"></i> Imprimir A4
        </button>
    </div>

    {{-- 01 — EQUIPAMENTO --}}
    <div class="section">
        <div class="section-head">
            <span class="s-num">01</span>
            <span class="s-title">Dados do Equipamento</span>
        </div>
        <div class="section-body">
            <div class="field-grid">
                <div>
                    <div class="field-lbl">Nº Interno</div>
                    <div class="field-val">{{ $maintenance->machine->numero_interno ?? '—' }}</div>
                </div>
                <div>
                    <div class="field-lbl">Tipo de Equipamento</div>
                    <div class="field-val">{{ $maintenance->machine->tipo_equipamento ?? '—' }}</div>
                </div>
                <div>
                    <div class="field-lbl">Localização</div>
                    <div class="field-val">{{ $maintenance->machine->localizacao ?? '—' }}</div>
                </div>
                <div>
                    <div class="field-lbl">Motorista / Operador</div>
                    <div class="field-val">{{ $maintenance->nome_motorista ?? '—' }}</div>
                </div>
                <div>
                    <div class="field-lbl">Data de Entrada</div>
                    <div class="field-val">{{ optional($maintenance->data_entrada)->format('d/m/Y') ?? '—' }}</div>
                </div>
                <div>
                    <div class="field-lbl">Estado</div>
                    <span class="s-badge {{ $statusLower }}" style="margin-top:2px;">
                        <i class="bi bi-{{ $statusIcon }}"></i> {{ $statusDisplay }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- 02 — OCORRÊNCIA --}}
    <div class="section">
        <div class="section-head">
            <span class="s-num">02</span>
            <span class="s-title">Ocorrência e Execução</span>
        </div>
        <div class="section-body">
            <div class="field-grid mb-4">
                <div>
                    <div class="field-lbl">Folha de Obra / Ref.</div>
                    <div class="field-val">{{ $maintenance->work_sheet_ref ?? '—' }}</div>
                </div>
                <div>
                    <div class="field-lbl">Horas / KMS na Entrada</div>
                    <div class="field-val">{{ $maintenance->hours_kms ?? '—' }}</div>
                </div>
                <div>
                    <div class="field-lbl">Horas Trabalhadas</div>
                    <div class="field-val">{{ $maintenance->horas_trabalho ?? '0.00' }} h</div>
                </div>
            </div>
            <div class="text-block-label">
                <span class="dot" style="background:#f59e0b;"></span>Descrição da Falha
            </div>
            <div class="text-block mb-3">{{ $maintenance->failure_description }}</div>
            <div class="text-block-label">
                <span class="dot" style="background:#64748b;"></span>Notas do Técnico
            </div>
            <div class="text-block">
                {{ $maintenance->technician_notes ?? 'Ainda não foram adicionadas notas técnicas.' }}
            </div>
        </div>
    </div>

    {{-- 03 — PEÇAS --}}
    <div class="section">
        <div class="section-head">
            <span class="s-num">03</span>
            <span class="s-title">Peças Utilizadas</span>
            @if($maintenance->movements && $maintenance->movements->count() > 0)
            <span style="margin-left:auto;font-size:.68rem;color:#94a3b8;font-weight:600;">
                {{ $maintenance->movements->count() }} registo(s)
            </span>
            @endif
        </div>
        @if($maintenance->movements && $maintenance->movements->count() > 0)
        <table class="parts-table">
            <thead>
                <tr>
                    <th>Artigo / Peça</th>
                    <th>Referência</th>
                    <th style="text-align:center;">Qtd</th>
                </tr>
            </thead>
            <tbody>
                @foreach($maintenance->movements as $movement)
                <tr>
                    <td>
                        @if($movement->stockItem)
                            <span class="fw-semibold">{{ $movement->stockItem->nome_artigo ?? $movement->stockItem->marca_fabricante }}</span>
                        @else
                            <span style="color:#ef4444;font-style:italic;font-size:.78rem;">Artigo removido do stock</span>
                        @endif
                    </td>
                    <td style="color:#94a3b8;">{{ $movement->stockItem->referencia ?? '—' }}</td>
                    <td style="text-align:center;"><span class="qty-badge">{{ $movement->quantity }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="section-body" style="text-align:center;color:#94a3b8;font-size:.8rem;padding:28px;">
            <i class="bi bi-inbox" style="font-size:1.4rem;display:block;margin-bottom:6px;opacity:.35;"></i>
            Nenhuma peça registada nesta intervenção.
        </div>
        @endif
    </div>

    {{-- 04 — FINANCEIRO --}}
    <div class="section">
        <div class="section-head">
            <span class="s-num">04</span>
            <span class="s-title">Resumo Financeiro e Datas</span>
        </div>
        <div class="section-body">
            <div class="field-grid col-2 mb-4">
                <div>
                    <div class="field-lbl">Concluído em</div>
                    <div class="field-val">{{ optional($maintenance->end_date)->format('d/m/Y H:i') ?? 'Em Aberto' }}</div>
                </div>
                <div>
                    <div class="field-lbl">Criado em</div>
                    <div class="field-val">{{ $maintenance->created_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
            <div class="inner-divider"></div>
            <div class="finance-wrap">
                <div class="finance-col">
                    <div class="finance-currency">Custo em Euros</div>
                    <div class="finance-amount">€ {{ number_format($costEUR, 2, ',', '.') }}</div>
                </div>
                <div class="finance-col">
                    <div class="finance-currency">Custo em Meticais</div>
                    <div class="finance-amount">{{ number_format($costMZN, 2, ',', '.') }}<span>MT</span></div>
                </div>
                <div class="finance-rate">
                    <i class="bi bi-arrow-left-right me-1"></i>
                    Taxa aplicada: 1 € = {{ number_format($exchangeRate ?? 1, 2) }} MT
                </div>
            </div>
        </div>
    </div>

    {{-- 05 — FICHEIROS --}}
    <div class="section no-print">
        <div class="section-head">
            <span class="s-num">05</span>
            <span class="s-title">Ficheiros Anexados</span>
            <span style="margin-left:auto;font-size:.68rem;color:#94a3b8;font-weight:600;">
                {{ $maintenance->files->count() }} ficheiro(s)
            </span>
        </div>
        <div class="section-body">
            @if($maintenance->files->isNotEmpty())
            <div class="d-flex flex-wrap gap-2">
                @foreach($maintenance->files as $file)
                <a href="{{ route('file.download', $file->id) }}" target="_blank" class="file-link">
                    <i class="bi bi-file-earmark-arrow-down"></i> {{ $file->filename }}
                </a>
                @endforeach
            </div>
            @else
            <div style="font-size:.8rem;color:#94a3b8;">
                <i class="bi bi-inbox me-1"></i> Nenhum ficheiro anexado.
            </div>
            @endif
        </div>
    </div>

    {{-- RODAPÉ --}}
    <div class="d-flex justify-content-between align-items-center mt-3 no-print"
         style="padding-top:16px;border-top:1px solid #f1f5f9;">
        <a href="{{ route('machines.show', $maintenance->machine->id) }}" class="top-btn">
            <i class="bi bi-arrow-left"></i> Voltar à Máquina
        </a>
        <span style="font-size:.65rem;color:#cbd5e1;">
            Intervenção #{{ str_pad($maintenance->id, 4, '0', STR_PAD_LEFT) }}
            · {{ $maintenance->created_at->format('d/m/Y') }}
        </span>
    </div>

</div>
</x-app-layout>
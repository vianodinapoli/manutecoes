<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes da Manutenção #{{ $maintenance->id }}</title>
    
    {{-- GARANTINDO O BOOTSTRAP --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    
    {{-- CSS CUSTOMIZADO PARA LAYOUT DE IMPRESSÃO (A4) --}}
    <style>
        /* Estilos Visuais Normais */
        .card-header { font-weight: bold; }
        .data-label { font-weight: bold; }

        /* Estilo para impressão A4 */
        @media print {
            body {
                font-size: 11pt;
                margin: 0;
                padding: 0;
            }
            .container {
                width: 210mm; /* Largura A4 */
                min-height: 297mm; /* Altura A4 */
                padding: 10mm;
                box-shadow: none !important;
            }
            .no-print {
                display: none !important;
            }
            /* Forçar a impressão de cores e bordas */
            .card-header, .bg-primary, .bg-info, .bg-dark, .bg-success, .bg-secondary, .alert {
                background-color: #f0f0f0 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                color: #000 !important;
                border: 1px solid #000;
            }
            .card {
                border: 1px solid #000 !important;
                box-shadow: none !important;
            }
            .text-primary, .text-success {
                color: #000 !important; 
            }
        }
    </style>
</head>
<body>
    <x-app-layout>
    <div class="container mt-4"> 
        
        {{-- BOTÕES DE AÇÃO (NÃO IMPRIMIR) --}}
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <h3>Detalhes da Manutenção: <span class="text-primary">:{{ $maintenance->machine->numero_interno }}</span></h3>
            <div>
                <a href="javascript:window.print()" class="btn btn-primary me-2">
                    🖨️ Imprimir (A4)
                </a>
                <a href="{{ route('maintenances.edit', $maintenance->id) }}" class="btn btn-warning me-2">
                    ✏️ Editar
                </a>
                <a href="{{ route('maintenances.index') }}" class="btn btn-secondary">
                    ⬅️ Voltar à Lista
                </a>
            </div>
        </div>

        {{-- LAYOUT PRINCIPAL (PRONTO PARA IMPRESSÃO) --}}
        <div class="card shadow-sm mb-5">
            <div class="card-header bg-dark text-white text-center">
                <h5 class="mb-0">REGISTO DE INTERVENÇÃO TÉCNICA: {{ $maintenance->machine->numero_interno }}</h5>
            </div>
            <div class="card-body">
                
                {{-- ---------------------------------------------------- --}}
                {{-- SECÇÃO 1: INFORMAÇÕES BÁSICAS E DADOS INICIAIS --}}
                {{-- ---------------------------------------------------- --}}
                <h5 class="mt-2 mb-3 border-bottom pb-1 text-danger">1. Dados do Equipamento e Início da Intervenção</h5>
                
                <div class="row mb-4">
                    <div class="col-md-4">
                        <span class="data-label">Máquina (Nº Interno):</span> {{ $maintenance->machine->numero_interno ?? 'N/A' }}
                    </div>
                    <div class="col-md-4">
                        <span class="data-label">Tipo de Equipamento:</span> {{ $maintenance->machine->tipo_equipamento ?? 'N/A' }}
                    </div>
                    <div class="col-md-4">
                        <span class="data-label">Localização:</span> {{ $maintenance->machine->localizacao ?? 'N/A' }}
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <span class="data-label">Nome do Motorista/Operador:</span> {{ $maintenance->nome_motorista ?? 'N/A' }}
                    </div>
                    <div class="col-md-4">
                        <span class="data-label">Data de Entrada:</span> {{ optional($maintenance->data_entrada)->format('d/m/Y') ?? 'N/A' }}
                    </div>
                    <div class="col-md-4">
                        <span class="data-label">Status Atual:</span> 
                        @php
                            // Usa a variável normalizada do Controller (minúsculas)
                            $safeStatus = str_replace('_', ' ', $maintenance->status);
                            $badge_class = match($maintenance->status) {
                                'pendente' => 'bg-warning text-dark',
                                'em_manutencao' => 'bg-info',
                                'concluida' => 'bg-success',
                                'cancelada' => 'bg-secondary',
                                default => 'bg-secondary',
                            };
                        @endphp
                        <span class="badge {{ $badge_class }}">{{ ucfirst($safeStatus) }}</span>
                    </div>
                </div>

                {{-- ---------------------------------------------------- --}}
                {{-- SECÇÃO 2: DESCRIÇÕES E DETALHES DE REGISTO --}}
                {{-- ---------------------------------------------------- --}}
                <h5 class="mt-4 mb-3 border-bottom pb-1 text-primary">2. Descrição da Ocorrência e Detalhes da Execução</h5>
                
                <div class="row mb-4">
                    <div class="col-md-4">
                        <span class="data-label">Folha de Obra / Ref.:</span> {{ $maintenance->work_sheet_ref ?? 'N/A' }}
                    </div>
                    <div class="col-md-4">
                        <span class="data-label">Nº de Horas/KMS na Entrada:</span> {{ $maintenance->hours_kms ?? 'N/A' }}
                    </div>
                    <div class="col-md-4">
                        <span class="data-label">Total de Horas Trabalhadas:</span> {{ $maintenance->horas_trabalho ?? '0.00' }} h
                    </div>
                </div>

                <div class="mb-4 p-3 border rounded bg-light">
                    <span class="data-label d-block mb-1">⚠️ Descrição da Falha (Ocorrência):</span>
                    <p class="mb-0">{{ $maintenance->failure_description }}</p>
                </div>
                
                <div class="mb-4 p-3 border rounded bg-light">
                    <span class="data-label d-block mb-1">🛠️ Notas do Técnico / Resumo da Intervenção:</span>
                    <p class="mb-0">{{ $maintenance->technician_notes ?? 'Ainda não foram adicionadas notas técnicas ou resumo da intervenção.' }}</p>
                </div>

                {{-- ---------------------------------------------------- --}}
                {{-- SECÇÃO 3: TEMPOS E CUSTOS --}}
                {{-- ---------------------------------------------------- --}}
                <h5 class="mt-4 mb-3 border-bottom pb-1 text-primary">3. Agendamento, Duração e Custos</h5>
                
                <div class="row mb-4">
                    <div class="col-md-3">
                        <span class="data-label">Agendado para:</span> {{ optional($maintenance->scheduled_date)->format('d/m/Y H:i') ?? 'N/A' }}
                    </div>
                    <div class="col-md-3">
                        <span class="data-label">Início Real (Start Date):</span> {{ optional($maintenance->start_date)->format('d/m/Y H:i') ?? 'N/A' }}
                    </div>
                    <div class="col-md-3">
                        <span class="data-label">Concluído em (End Date):</span> {{ optional($maintenance->end_date)->format('d/m/Y H:i') ?? 'Em Aberto' }}
                    </div>
                    <div class="col-md-3">
                        <span class="data-label">Registo Criado em:</span> {{ $maintenance->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        @php
                            $costEUR = $maintenance->total_cost ?? 0;
                            $costMZN = $costEUR * $exchangeRate; 
                        @endphp
                        
                        <div class="alert alert-success text-center">
                            <h4 class="mb-1">Custo Total da Intervenção</h4>
                            <p class="mb-0">
                                <span class="data-label">EUR:</span> **€ {{ number_format($costEUR, 2, ',', '.') }}**
                            </p>
                            <p class="mb-0">
                                <span class="data-label">MZN:</span> **MZN {{ number_format($costMZN, 2, ',', '.') }}**
                            </p>
                            <span class="small text-muted">Taxa: 1 EUR = {{ number_format($exchangeRate, 2) }} MZN</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ficheiros Anexados (Não serão impressos) --}}
            <div class="card-footer no-print">
                <h5 class="mb-2">📎 Ficheiros Anexados ({{ $maintenance->files->count() }})</h5>
                @if($maintenance->files->isNotEmpty())
                    <div class="list-group list-group-flush">
                        @foreach($maintenance->files as $file)
                            <a href="{{ $file->url }}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-1 small">
                                <div>📁 {{ $file->filename }}</div>
                                <span class="badge bg-secondary">{{ round($file->filesize / 1024 / 1024, 2) }} MB</span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <small class="text-muted">Nenhum ficheiro anexado.</small>
                @endif
            </div>
            
        </div>
        
        <div class="mt-4 pb-4 text-center no-print">
            <a href="{{ route('machines.show', $maintenance->machine->id) }}" class="btn btn-secondary btn-lg">
                ⬅️ Voltar à Máquina
            </a>
        </div>
        
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</x-app-layout>
</body>
</html>
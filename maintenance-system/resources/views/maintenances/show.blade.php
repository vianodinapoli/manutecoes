<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes da Manutenção #{{ $maintenance->id }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    {{-- Adicionar Font Awesome ou similar se quiser ícones de ficheiros mais bonitos --}}
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" /> --}}
</head>
<body>
    <div class="container mt-5"> 
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Detalhes da Manutenção: <span class="text-primary">#{{ $maintenance->id }}</span></h1>
            <a href="{{ route('machines.show', $maintenance->machine->id) }}" class="btn btn-secondary">
                ⬅️ Voltar à Máquina
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-4 d-flex gap-2">
            <a href="{{ route('maintenances.edit', $maintenance->id) }}" class="btn btn-warning">
                ✏️ Editar Registo
            </a>
            <a href="{{ route('machines.show', $maintenance->machine->id) }}" class="btn btn-info">
                ⚙️ Ver Máquina ({{ $maintenance->machine->numero_interno }})
            </a>
            
            {{-- Formulário de Eliminação (Recomendado usar AJAX ou modal para confirmação) --}}
            <form action="{{ route('maintenances.destroy', $maintenance->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja eliminar este registo de manutenção? Esta ação é irreversível e apagará os ficheiros anexados!');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">🗑️ Eliminar</button>
            </form>
        </div>
        
        <div class="row">
            
            <div class="col-lg-5 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">📋 Sumário da Intervenção</h5>
                    </div>
                    <ul class="list-group list-group-flush">
                        
                        <li class="list-group-item">
                            <strong>Máquina:</strong> 
                            <a href="{{ route('machines.show', $maintenance->machine->id) }}">
                                **{{ $maintenance->machine->numero_interno }}** ({{ $maintenance->machine->tipo_equipamento }})
                            </a>
                        </li>

                        <li class="list-group-item">
                            <strong>Estado Atual:</strong> 
                            @php
                                $badge_class = match($maintenance->status) {
                                    'Pendente' => 'bg-warning text-dark',
                                    'Em Progresso' => 'bg-info',
                                    'Concluída' => 'bg-success',
                                    'Cancelada' => 'bg-secondary',
                                    default => 'bg-secondary',
                                };
                            @endphp
                            <span class="badge {{ $badge_class }}">{{ $maintenance->status }}</span>
                        </li>

                        <li class="list-group-item"><strong>Criado em:</strong> {{ $maintenance->created_at->format('d/m/Y H:i') }}</li>
                        <li class="list-group-item"><strong>Agendado para:</strong> {{ $maintenance->scheduled_date ? $maintenance->scheduled_date->format('d/m/Y H:i') : 'N/A' }}</li>
                        <li class="list-group-item"><strong>Início Real:</strong> {{ $maintenance->start_date ? $maintenance->start_date->format('d/m/Y H:i') : 'N/A' }}</li>
                        <li class="list-group-item"><strong>Concluído em:</strong> {{ $maintenance->end_date ? $maintenance->end_date->format('d/m/Y H:i') : 'Em Aberto' }}</li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-7 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Descrição da Avaria (Ocorrência)</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">{{ $maintenance->failure_description }}</p>
                    </div>
                </div>
            </div>
            
        </div> 
        
        <div class="row">
             <div class="col-12">
                <div class="card shadow-sm mt-3">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">🛠️ Notas do Técnico / Resumo da Intervenção</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">{{ $maintenance->technician_notes ?? 'Ainda não foram adicionadas notas técnicas ou resumo da intervenção.' }}</p>
                    </div>
                </div>
             </div>
        </div>

        {{-- ================================================= --}}
        {{-- SECÇÃO DE FICHEIROS ANEXADOS (NOVO) --}}
        {{-- ================================================= --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">📎 Ficheiros Anexados ({{ $maintenance->files->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @if($maintenance->files->isNotEmpty())
                            <div class="list-group">
                                @foreach($maintenance->files as $file)
                                    @php
                                        // Determinar um ícone ou tipo (pode ser mais detalhado com base no mime_type)
                                        $fileIcon = match(pathinfo($file->filename, PATHINFO_EXTENSION)) {
                                            'pdf' => '📄 PDF',
                                            'jpg', 'jpeg', 'png', 'gif' => '🖼️ Imagem',
                                            'doc', 'docx' => '📝 Documento',
                                            'zip', 'rar' => '📦 Arquivo',
                                            default => '📁 Ficheiro',
                                        };
                                        // Converter bytes para MB
                                        $fileSizeMB = round($file->filesize / 1024 / 1024, 2);
                                    @endphp
                                    
                                    {{-- O link $file->url usa o acessor que criámos no modelo MaintenanceFile --}}
                                    <a href="{{ $file->url }}" 
                                       target="_blank" Abre em nova tab para visualização
                                       download="{{ $file->filename }}" {{-- Sugere o download do ficheiro --}}
                                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        
                                        <div>
                                            <strong>{{ $fileIcon }}</strong> {{ $file->filename }}
                                        </div>
                                        
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="badge bg-secondary">
                                                {{ $fileSizeMB }} MB
                                            </span>
                                            <span class="btn btn-sm btn-outline-primary" style="pointer-events: none;">
                                                ⬇️ Baixar / Ver
                                            </span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info mb-0">
                                Nenhum ficheiro foi anexado a este registo de manutenção.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
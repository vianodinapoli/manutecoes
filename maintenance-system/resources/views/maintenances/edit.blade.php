<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Manutenção ID {{ $maintenance->id }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5"> 
        
        <div class="mb-4">
            <h1>✏️ Editar Registo de Manutenção</h1>
            <p class="lead">Máquina: <a href="{{ route('machines.show', $machine->id) }}">{{ $machine->name }}</a> (Nº Série: {{ $machine->serial_number }})</p>
        </div>

        <div class="mb-4">
            <a href="{{ route('machines.show', $machine->id) }}" class="btn btn-secondary">
                ⬅️ Voltar aos Detalhes da Máquina
            </a>
        </div>
        
        <form method="POST" action="{{ route('maintenances.update', $maintenance->id) }}">
            @csrf 
            @method('PUT') @if ($errors->any())
                <div class="alert alert-danger">
                    Por favor, corrija os erros de validação abaixo.
                </div>
            @endif
            
            <div class="mb-3">
                <label for="failure_description" class="form-label">Descrição da Avaria (Inicial):</label>
                <textarea id="failure_description" name="failure_description" class="form-control" readonly disabled>{{ $maintenance->failure_description }}</textarea>
                <div class="form-text">A descrição inicial da avaria não pode ser alterada.</div>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Estado da Manutenção:</label>
                <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                    <option value="">Selecione o Estado</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" 
                            {{ old('status', $maintenance->status) == $status ? 'selected' : '' }}>
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <hr>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="scheduled_date" class="form-label">Data Agendada:</label>
                    <input type="datetime-local" id="scheduled_date" name="scheduled_date" class="form-control @error('scheduled_date') is-invalid @enderror" 
                           value="{{ old('scheduled_date', optional($maintenance->scheduled_date)->format('Y-m-d\TH:i')) }}">
                    @error('scheduled_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="start_date" class="form-label">Data de Início Real:</label>
                    <input type="datetime-local" id="start_date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" 
                           value="{{ old('start_date', optional($maintenance->start_date)->format('Y-m-d\TH:i')) }}">
                    @error('start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="end_date" class="form-label">Data de Conclusão:</label>
                    <input type="datetime-local" id="end_date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" 
                           value="{{ old('end_date', optional($maintenance->end_date)->format('Y-m-d\TH:i')) }}">
                    @error('end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="technician_notes" class="form-label">Notas do Técnico:</label>
                <textarea id="technician_notes" name="technician_notes" rows="5" class="form-control @error('technician_notes') is-invalid @enderror">{{ old('technician_notes', $maintenance->technician_notes) }}</textarea>
                @error('technician_notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <button type="submit" class="btn btn-success btn-lg">
                💾 Guardar Alterações
            </button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
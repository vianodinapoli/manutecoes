@extends('layouts.app')

@section('content')
<div class="container mt-4">
    
    @php
        // Define a máquina atual. Se estiver em edit, usa a relação. Se em createFromMachine, usa a variável passada.
        $currentMachine = $maintenance->machine ?? $currentMachine ?? null;
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ $maintenance->id ? '✏️ Editar Manutenção #' . $maintenance->id : '🛠️ Criar Nova Manutenção' }}</h1>
        <a href="{{ $currentMachine ? route('machines.show', $currentMachine->id) : route('maintenances.index') }}" class="btn btn-secondary">
            ⬅️ Voltar
        </a>
    </div>
    
    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">Por favor, corrija os erros no formulário.</div>
    @endif
    
    <form action="{{ $maintenance->id ? route('maintenances.update', $maintenance->id) : route('maintenances.store') }}" method="POST">
        @csrf
        @if($maintenance->id)
            @method('PUT')
        @endif

        {{-- =============================================== --}}
        <h2>Dados da Máquina Selecionada</h2>
        {{-- =============================================== --}}
        
        @if($currentMachine)
            <div class="card bg-light p-3 mb-4 border-primary">
                <div class="row">
                    <div class="col-md-4">
                        <p><strong>Nº Interno:</strong> {{ $currentMachine->numero_interno }}</p>
                        <p><strong>Tipo:</strong> {{ $currentMachine->tipo_equipamento }}</p>
                        <p><strong>Marca:</strong> {{ $currentMachine->marca }}</p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Modelo:</strong> {{ $currentMachine->modelo }}</p>
                        <p><strong>Localização:</strong> {{ $currentMachine->localizacao }}</p>
                        <p><strong>Status:</strong> <span class="badge bg-warning text-dark">{{ $currentMachine->status }}</span></p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Data Automática:</strong> {{ now()->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
            </div>
            <input type="hidden" name="machine_id" value="{{ $currentMachine->id }}">
        @else
            <div class="mb-3">
                <label for="machine_id" class="form-label">Máquina</label>
                <select name="machine_id" id="machine_id" class="form-select @error('machine_id') is-invalid @enderror" required>
                    <option value="">-- Selecione uma Máquina --</option>
                    @foreach($machines as $machine)
                        <option value="{{ $machine->id }}" 
                            {{ old('machine_id', $maintenance->machine_id ?? $selectedMachine) == $machine->id ? 'selected' : '' }}>
                            {{ $machine->numero_interno }} ({{ $machine->tipo_equipamento }})
                        </option>
                    @endforeach
                </select>
                @error('machine_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        @endif
        
        <hr>
        
        {{-- =============================================== --}}
        <h2>Detalhes da Intervenção</h2>
        {{-- =============================================== --}}

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="work_sheet_ref" class="form-label">Folha de Obra / Ref.</label>
                    <input type="text" name="work_sheet_ref" id="work_sheet_ref" class="form-control" 
                           value="{{ old('work_sheet_ref', $maintenance->work_sheet_ref ?? '') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="hours_kms" class="form-label">Nº de Horas / KMS</label>
                    <input type="number" name="hours_kms" id="hours_kms" class="form-control" 
                           value="{{ old('hours_kms', $maintenance->hours_kms ?? '') }}">
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="failure_description" class="form-label">Descrição da Falha (Ocorrência)</label>
            <textarea name="failure_description" id="failure_description" class="form-control @error('failure_description') is-invalid @enderror" rows="3" required>{{ old('failure_description', $maintenance->failure_description) }}</textarea>
            @error('failure_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        
        <div class="mb-3">
            <label for="status" class="form-label">Status da Manutenção</label>
            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                @php $currentStatus = old('status', $maintenance->status); @endphp
                <option value="Pendente" {{ $currentStatus == 'Pendente' ? 'selected' : '' }}>Pendente</option>
                <option value="Em Progresso" {{ $currentStatus == 'Em Progresso' ? 'selected' : '' }}>Em Progresso</option>
                <option value="Concluída" {{ $currentStatus == 'Concluída' ? 'selected' : '' }}>Concluída</option>
                <option value="Cancelada" {{ $currentStatus == 'Cancelada' ? 'selected' : '' }}>Cancelada</option>
            </select>
            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <hr>
        
        {{-- =============================================== --}}
        <h2>Custos e Material (Itens de Serviço)</h2>
        {{-- =============================================== --}}
        
        <div class="mb-3">
            <label for="technician_notes" class="form-label">Descrição do Material / Serviço / Notas do Técnico</label>
            <textarea name="technician_notes" id="technician_notes" class="form-control" rows="4">{{ old('technician_notes', $maintenance->technician_notes ?? '') }}</textarea>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="total_cost" class="form-label">Custo Total (A ser acumulado)</label>
                    <input type="number" step="0.01" name="total_cost" id="total_cost" class="form-control" 
                           value="{{ old('total_cost', $maintenance->total_cost ?? 0) }}" placeholder="0.00">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="end_date" class="form-label">Data de Conclusão (Opcional)</label>
                    <input type="datetime-local" name="end_date" id="end_date" class="form-control"
                           value="{{ old('end_date', $maintenance->end_date ? $maintenance->end_date->format('Y-m-d\TH:i') : '') }}">
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success btn-lg mt-3">
            {{ $maintenance->id ? '✅ Atualizar Manutenção' : '💾 Criar Manutenção' }}
        </button>
    </form>
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="container mt-5" style="width: 90%; margin: 0 auto;">
    <h1>{{ $maintenance->id ? 'Editar Manutenção' : 'Nova Manutenção' }}</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ $maintenance->id ? route('maintenances.update', $maintenance->id) : route('maintenances.store') }}" method="POST">
        @csrf
        @if($maintenance->id)
            @method('PUT')
        @endif

        <div class="mb-3">
            <label for="machine_id" class="form-label">Máquina</label>
            <select name="machine_id" id="machine_id" class="form-control" required>
                @foreach($machines as $machine)
                    <option value="{{ $machine->id }}" 
                        {{ $selectedMachine == $machine->id ? 'selected' : '' }}>
                        {{ $machine->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="title" class="form-label">Título</label>
            <input type="text" name="title" id="title" class="form-control" 
                   value="{{ old('title', $maintenance->title) }}" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Descrição</label>
            <textarea name="description" id="description" class="form-control">{{ old('description', $maintenance->description) }}</textarea>
        </div>

        <div class="mb-3">
    <label for="failure_description" class="form-label">Descrição da Falha</label>
    <textarea name="failure_description" id="failure_description" class="form-control" required>{{ old('failure_description', $maintenance->failure_description) }}</textarea>
</div>


        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-control" required>
                <option value="pendente" {{ $maintenance->status == 'pendente' ? 'selected' : '' }}>Pendente</option>
                <option value="em manutenção" {{ $maintenance->status == 'em manutenção' ? 'selected' : '' }}>Em Manutenção</option>
                <option value="concluída" {{ $maintenance->status == 'concluída' ? 'selected' : '' }}>Concluída</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="scheduled_date" class="form-label">Data Agendada</label>
            <input type="date" name="scheduled_date" id="scheduled_date" class="form-control"
                   value="{{ old('scheduled_date', $maintenance->scheduled_date ? $maintenance->scheduled_date->format('Y-m-d') : '') }}">
        </div>

        <button type="submit" class="btn btn-primary">
            {{ $maintenance->id ? 'Atualizar' : 'Criar' }}
        </button>
    </form>
</div>
@endsection

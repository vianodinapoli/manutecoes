@extends('layouts.app')

@section('content')
<div class="container mt-5" style="max-width: 95%;">
    <h1 class="mb-4">📋 Lista de Manutenções</h1>

    <a href="{{ route('maintenances.create') }}" class="btn btn-primary mb-3">➕ Nova Manutenção</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Máquina</th>
                <th>Título da Última Manutenção</th>
                <th>Status</th>
                <th>Data</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        @foreach($machines as $m)
            @php
                // Pega a última manutenção da máquina
                $lastMaintenance = $m->maintenances->sortByDesc('scheduled_date')->first();
            @endphp
            <tr>
                <td>{{ $m->id }}</td>
                <td>{{ $m->name }}</td>
                <td>{{ $lastMaintenance->title ?? '-' }}</td>
                <td>
                    @if($lastMaintenance)
                        @if($lastMaintenance->status == 'em manutenção')
                            <span class="badge bg-warning text-dark">{{ $lastMaintenance->status }}</span>
                        @elseif($lastMaintenance->status == 'concluída')
                            <span class="badge bg-success">{{ $lastMaintenance->status }}</span>
                        @else
                            <span class="badge bg-secondary">{{ $lastMaintenance->status }}</span>
                        @endif
                    @else
                        <span class="badge bg-secondary">Sem manutenção</span>
                    @endif
                </td>
                <td>{{ $lastMaintenance->scheduled_date ?? '-' }}</td>
                <td>
                    @if($lastMaintenance)
                        <a href="{{ route('maintenances.edit', $lastMaintenance->id) }}" class="btn btn-sm btn-warning mb-1">✏️ Editar</a>

                        <form action="{{ route('maintenances.destroy', $lastMaintenance->id) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Tens certeza que queres apagar esta manutenção?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger mb-1">🗑 Apagar</button>
                        </form>
                    @else
                        <a href="{{ route('machines.maintenances.create', $m->id) }}" class="btn btn-sm btn-primary mb-1">➕ Criar Manutenção</a>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection

<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
        .footer { margin-top: 40px; border-top: 1px solid #eee; padding-top: 10px; }
        .signature-line { margin-top: 30px; border-top: 1px solid #000; width: 200px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Listagem Geral de Máquinas e Equipamentos</h2>
        <p>Data de Extração: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Equipamento</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Nº Chassi</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $machine)
            <tr>
                <td>{{ $machine->numero_interno }}</td>
                <td>{{ $machine->tipo_equipamento }}</td>
                <td>{{ $machine->marca }}</td>
                <td>{{ $machine->modelo }}</td>
                <td>{{ $machine->nr_chassi }}</td>
              <td>
                                    @php
                                        $badge_class = match($machine->status) {
                                            'Operacional' => 'bg-success',
                                            'Em Manutenção' => 'bg-info text-dark',
                                            'Avariada' => 'bg-danger',
                                            'Desativada' => 'bg-secondary',
                                            default => 'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badge_class }}">{{ $machine->status }}</span>
                                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p><strong>Extraído por:</strong> {{ auth()->user()->name }}</p>
        <div class="signature-line"></div>
        <p style="font-size: 9px;">Responsável Técnico</p>
    </div>
</body>
</html>
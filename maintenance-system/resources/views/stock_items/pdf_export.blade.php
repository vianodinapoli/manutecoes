<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 30px; }
        
        /* Novo estilo para a assinatura/rodapé */
        .footer { margin-top: 50px; border-top: 1px solid #eee; padding-top: 10px; }
        .signature-line { margin-top: 40px; border-top: 1px solid #000; width: 250px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Mapa de Inventário / Stock</h2>
        <p>Data de Extração: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Artigo</th>
                <th>Referência</th>
                <th>Marca</th>
                <th>Qtd</th>
                <th>Estado</th>
                <th>Armazém</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item->nome }}</td>
                <td>{{ $item->referencia }}</td>
                <td>{{ $item->marca_fabricante }}</td>
                <td>{{ $item->quantidade }}</td>
                <td>{{ $item->estado }}</td>
                <td>{{ $item->numero_armazem }} - {{ $item->seccao_armazem }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p><strong>Extraído por:</strong> {{ auth()->user()->name }}</p>
        <p style="font-size: 10px; color: #666;">E-mail: {{ auth()->user()->email }}</p>
        
        <div class="signature-line"></div>
        <p style="font-size: 9px;">Assinatura do Responsável</p>
    </div>
</body>
</html>
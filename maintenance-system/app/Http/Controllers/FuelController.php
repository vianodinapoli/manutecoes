<?php

namespace App\Http\Controllers;

use App\Models\FuelLog;
use App\Models\FuelEntry;
use App\Models\Tank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FuelController extends Controller
{
    public function index(Request $request)
    {
        $tanques = Tank::all();
        
        // 1. Captura os filtros da URL
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        $tankId = $request->filter_tank_id;
        $company = $request->company;

        // 2. Query de ENTRADAS
        $queryEntradas = DB::table('fuel_entries')
            ->leftJoin('tanks', 'fuel_entries.tank_id', '=', 'tanks.id')
            ->select(
                'fuel_entries.id',
                'fuel_entries.date',
                'fuel_entries.supplier as ident',
                DB::raw("'' as company"), 
                'fuel_entries.quantity',
                DB::raw("'' as operator"), 
                DB::raw("'' as driver"),
                DB::raw("NULL as start_counter"),
                DB::raw("NULL as end_counter"),
                'tanks.nome as tanque_nome',
                DB::raw("'ENTRADA' as tipo"),
                'fuel_entries.created_at'
            );

        // 3. Query de SAÍDAS
        $querySaidas = DB::table('fuel_logs')
            ->leftJoin('tanks', 'fuel_logs.tank_id', '=', 'tanks.id')
            ->select(
                'fuel_logs.id',
                'fuel_logs.date',
                'fuel_logs.plate as ident',
                'fuel_logs.company',
                'fuel_logs.quantity',
                'fuel_logs.operator',
                'fuel_logs.driver',
                'fuel_logs.start_counter',
                'fuel_logs.end_counter',
                'tanks.nome as tanque_nome',
                DB::raw("'SAÍDA' as tipo"),
                'fuel_logs.created_at'
            );

        // 4. APLICAR FILTROS GERAIS
        if ($fromDate) {
            $queryEntradas->where('fuel_entries.date', '>=', $fromDate);
            $querySaidas->where('fuel_logs.date', '>=', $fromDate);
        }
        if ($toDate) {
            $queryEntradas->where('fuel_entries.date', '<=', $toDate);
            $querySaidas->where('fuel_logs.date', '<=', $toDate);
        }
        if ($tankId) {
            $queryEntradas->where('fuel_entries.tank_id', $tankId);
            $querySaidas->where('fuel_logs.tank_id', $tankId);
        }

        // 5. EXECUTAR A QUERY (Com lógica de empresa)
        if ($company) {
            $querySaidas->where('fuel_logs.company', 'LIKE', "%{$company}%");
            $historico = $querySaidas->orderBy('date', 'desc')->get();
        } else {
            $historico = $queryEntradas->union($querySaidas)
                ->orderBy('date', 'desc')
                ->get();
        }

        // 6. TOTAIS PARA O RELATÓRIO
        $totalConsumidoFiltro = $historico->where('tipo', 'SAÍDA')->sum('quantity');
        $totalEntradaFiltro = $historico->where('tipo', 'ENTRADA')->sum('quantity');
        $contagemRegistos = $historico->count();

        // 7. DASHBOARD CARDS (Stock Total Atual)
        $capacidadeTotal = $tanques->sum('capacidade');
        $restante = $tanques->sum('stock_atual');
        $percentagem = $capacidadeTotal > 0 ? ($restante / $capacidadeTotal) * 100 : 0;

 // 8. LÓGICA DO GRÁFICO (DUAS CORES - STACKED - ESPELHAMENTO)
$ultimasDatas = $historico->pluck('date')->unique()->sort()->take(-10);
$movimentosProcessados = collect();

foreach ($historico->whereIn('date', $ultimasDatas) as $reg) {
    $nomeTanque = trim($reg->tanque_nome);
    $nomeEmpresa = trim($reg->company);

    if ($reg->tipo == 'SAÍDA') {
        // 1. REGISTRO PARA O TANQUE (SAÍDA)
        $movimentosProcessados->push([
            'date' => $reg->date,
            'label' => $nomeTanque,
            'tipo' => 'SAÍDA',
            'quantity' => $reg->quantity
        ]);

        // 2. REGISTRO PARA A EMPRESA (ESPELHAMENTO)
        // Só duplica se houver empresa e for diferente do tanque
        if (!empty($nomeEmpresa) && $nomeEmpresa !== 'N/A' && strtolower($nomeEmpresa) !== strtolower($nomeTanque)) {
            $movimentosProcessados->push([
                'date' => $reg->date,
                'label' => $nomeEmpresa,
                'tipo' => 'SAÍDA',
                'quantity' => $reg->quantity
            ]);
        }
    } else {
        // ENTRADA (ATESTO)
        $movimentosProcessados->push([
            'date' => $reg->date,
            'label' => $nomeTanque,
            'tipo' => 'ENTRADA',
            'quantity' => $reg->quantity
        ]);
    }
}

// Agrupar por "Data - Nome" para criar as barras individuais no eixo X
$dadosAgrupados = $movimentosProcessados->groupBy(function($item) {
    return date('d/m', strtotime($item['date'])) . ' - ' . $item['label'];
});

$labelsCompostas = [];
$dataSaidas = [];
$dataEntradas = [];

foreach ($dadosAgrupados as $chave => $movs) {
    $labelsCompostas[] = explode(' - ', $chave); // Cria as duas linhas na label
    $dataSaidas[] = $movs->where('tipo', 'SAÍDA')->sum('quantity');
    $dataEntradas[] = $movs->where('tipo', 'ENTRADA')->sum('quantity');
}

$datasets = [
    [
        'label' => 'Saídas (Consumo)',
        'data' => $dataSaidas,
        'backgroundColor' => '#db5246', // Vermelho
        'stack' => 'combustivel',
        'borderRadius' => 4
    ],
    [
        'label' => 'Entradas (Atesto)',
        'data' => $dataEntradas,
        'backgroundColor' => '#62c48e', // Verde
        'stack' => 'combustivel',
        'borderRadius' => 4
    ]
];

// 9. RETURN FINAL CORRIGIDO
return view('fuel.index', compact(
    'tanques', 'historico', 'labelsCompostas', 'datasets', 
    'totalConsumidoFiltro', 'totalEntradaFiltro', 'contagemRegistos',
    'capacidadeTotal', 'restante', 'percentagem'
));

    }

    // Registrar Saída (Abastecimento de Viatura)
    public function store(Request $request)
    {
        $request->validate([
            'tank_id' => 'required|exists:tanks,id',
            'date' => 'required|date',
            'plate' => 'required',
            'start_counter' => 'required|numeric',
            'end_counter' => 'required|numeric',
        ]);

        $quantidade = $request->end_counter - $request->start_counter;

        FuelLog::create([
            'tank_id' => $request->tank_id,
            'date' => $request->date,
            'plate' => $request->plate,
            'company' => $request->company ?? 'N/A',
            'start_counter' => $request->start_counter,
            'end_counter' => $request->end_counter,
            'quantity' => $quantidade,
            'operator' => $request->operator ?? auth()->user()->name ?? 'Sistema',
            'driver' => $request->driver ?? 'Não informado',
        ]);

        $tanque = Tank::find($request->tank_id);
        if ($tanque) {
            $tanque->stock_atual -= $quantidade;
            $tanque->save();
        }

        return redirect()->back()->with('success', 'Abastecimento registado!');
    }

    // Registrar Entrada (Reforço de Cisterna)
    public function storeEntry(Request $request)
    {
        $request->validate([
            'tank_id' => 'required|exists:tanks,id',
            'quantity' => 'required|numeric|min:0',
            'date' => 'required|date',
        ]);

        FuelEntry::create([
            'date' => $request->date,
            'quantity' => $request->quantity,
            'supplier' => $request->supplier,
            'tank_id' => $request->tank_id,
        ]);

        $tanque = Tank::find($request->tank_id);
        if ($tanque) {
            $tanque->stock_atual += $request->quantity;
            $tanque->save();
        }

        return redirect()->back()->with('success', 'Stock reforçado!');
    }


 public function destroyLog($id)
{
    FuelLog::findOrFail($id)->delete();
    return response()->json(['success' => true]);
}

public function destroyEntry($id)
{
    FuelEntry::findOrFail($id)->delete();
    return response()->json(['success' => true]);
}
// Exemplo de Edit para Saída (Log)
public function editLog($id)
{
    $log = FuelLog::findOrFail($id);
    $tanques = Tank::all();
    return view('fuel.edit_log', compact('log', 'tanques'));
}


public function getLogJson($id) {
    return response()->json(FuelLog::findOrFail($id));
}

public function getEntryJson($id) {
    return response()->json(FuelEntry::findOrFail($id));
}

// Método de Update (Exemplo para Saída)
public function updateLog(Request $request, $id) {
    $log = FuelLog::findOrFail($id);
    
    // 1. Reverter stock antigo (opcional, mas recomendado)
    $tanque = Tank::find($log->tank_id);
    $tanque->stock_atual += $log->quantity; 

    // 2. Atualizar dados
    $quantidade = $request->end_counter - $request->start_counter;
    $log->update(array_merge($request->all(), ['quantity' => $quantidade]));

    // 3. Aplicar stock novo
    $tanque->stock_atual -= $quantidade;
    $tanque->save();

    return response()->json(['success' => true, 'message' => 'Atualizado com sucesso!']);
}
}
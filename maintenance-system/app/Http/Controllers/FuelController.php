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

  // 8. DADOS PARA O GRÁFICO (AGRUPADO POR TANQUE/EMPRESA)
// Pegamos todos os tanques para serem as nossas Labels (barras)
$labels = $tanques->pluck('nome')->toArray(); 
$dataSaidas = [];
$dataEntradas = [];

foreach ($tanques as $tanque) {
    // Soma saídas deste tanque específico no histórico filtrado
    $saidas = $historico->where('tipo', 'SAÍDA')
                        ->where('tanque_nome', $tanque->nome)
                        ->sum('quantity');
    
    // Soma entradas deste tanque específico no histórico filtrado
    $entradas = $historico->where('tipo', 'ENTRADA')
                         ->where('tanque_nome', $tanque->nome)
                         ->sum('quantity');

    $dataSaidas[] = $saidas;
    $dataEntradas[] = $entradas;
}

// 9. RETURN (Passe as variáveis para a view)
return view('fuel.index', compact(
    'tanques', 'historico', 'labels', 'dataSaidas', 'dataEntradas',
    'totalConsumidoFiltro', 'totalEntradaFiltro', 'contagemRegistos',
    'capacidadeTotal', 'restante', 'percentagem'
));}

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
}
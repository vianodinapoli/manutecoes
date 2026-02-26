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
    $tanques = \App\Models\Tank::all();
    
    // 1. Captura os filtros da URL
    $fromDate = $request->from_date;
    $toDate = $request->to_date;
    $tankId = $request->filter_tank_id;
    $company = $request->company;

    // 2. Query de ENTRADAS (Ajustada para evitar o erro de coluna 'operator')
    $queryEntradas = DB::table('fuel_entries')
        ->leftJoin('tanks', 'fuel_entries.tank_id', '=', 'tanks.id')
        ->select(
            'fuel_entries.date',
            'fuel_entries.supplier as ident',
            DB::raw("'' as company"), 
            'fuel_entries.quantity',
            DB::raw("'' as operator"), // Criamos uma coluna vazia para não dar erro
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

    // 4. APLICAR FILTROS GERAIS (Data e Tanque)
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

    // 5. FILTRO DE EMPRESA (Apenas nas Saídas)
    if ($company) {
        $querySaidas->where('fuel_logs.company', $company);
        // Se filtramos por empresa, não mostramos entradas (pois entradas não têm empresa)
        $historico = $querySaidas->orderBy('created_at', 'desc')->get();
    } else {
        // Se não houver empresa, une as duas tabelas
        $historico = $queryEntradas->union($querySaidas)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // 6. Dados para o Dashboard (Cards do topo)
    $capacidadeTotal = $tanques->sum('capacidade');
    $restante = $tanques->sum('stock_atual');
    $percentagem = $capacidadeTotal > 0 ? ($restante / $capacidadeTotal) * 100 : 0;

// ... (Mantém as queries de filtros anteriores) ...

    $historico = $company ? $querySaidas : $queryEntradas->union($querySaidas);
    $historico = $historico->orderBy('date', 'desc')->get();

    // --- NOVIDADE: TOTAIS PARA O RELATÓRIO ---
    $totalConsumidoFiltro = $historico->where('tipo', 'SAÍDA')->sum('quantity');
    $totalEntradaFiltro = $historico->where('tipo', 'ENTRADA')->sum('quantity');
    $contagemRegistos = $historico->count();

    return view('fuel.index', compact(
        'tanques', 'capacidadeTotal', 'restante', 'percentagem', 
        'historico', 'totalConsumidoFiltro', 'totalEntradaFiltro', 'contagemRegistos'
    ));



    return view('fuel.index', compact('tanques', 'capacidadeTotal', 'restante', 'percentagem', 'historico'));
}

    // Criar novo Tanque (Configuração)
    public function storeTank(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'capacidade' => 'required|numeric|min:0',
        ]);

        Tank::create([
            'nome' => $request->nome,
            'capacidade' => $request->capacidade,
            'stock_atual' => $request->capacidade, // Começa cheio ou ajuste como preferir
        ]);

        return redirect()->back()->with('success', 'Tanque cadastrado com sucesso!');
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

        // 1. Criar o Log
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

        // 2. Dar baixa no stock do tanque selecionado
        $tanque = Tank::find($request->tank_id);
        if ($tanque) {
            $tanque->stock_atual -= $quantidade;
            $tanque->save();
        }

        return redirect()->back()->with('success', 'Abastecimento registado e stock atualizado!');
    }

    // Registrar Entrada (Reforço de Cisterna)
    public function storeEntry(Request $request)
    {
        $request->validate([
            'tank_id' => 'required|exists:tanks,id',
            'quantity' => 'required|numeric|min:0',
            'date' => 'required|date',
        ]);

        // 1. Regista o histórico de entrada
        FuelEntry::create([
            'date' => $request->date,
            'quantity' => $request->quantity,
            'supplier' => $request->supplier,
            'tank_id' => $request->tank_id,
        ]);

        // 2. SOMA o stock no tanque correspondente
        $tanque = Tank::find($request->tank_id);
        if ($tanque) {
            $tanque->stock_atual += $request->quantity;
            $tanque->save();
        }

        return redirect()->back()->with('success', 'Stock reforçado com sucesso!');
    }
}
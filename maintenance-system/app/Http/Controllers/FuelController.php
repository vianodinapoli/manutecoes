<?php

namespace App\Http\Controllers;

use App\Models\FuelLog;
use App\Models\FuelEntry;
use App\Models\Tank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FuelController extends Controller
{
    public function index()
    {
        // 1. Pegar tanques reais da DB
        $tanques = Tank::all();
        
        // Se não houver tanques na DB, o dashboard ficaria vazio. 
        // Cálculos globais baseados nos tanques reais
        $capacidadeTotal = $tanques->sum('capacidade');
        $restante = $tanques->sum('stock_atual');
        $percentagem = $capacidadeTotal > 0 ? ($restante / $capacidadeTotal) * 100 : 0;

        // 2. Query de SAÍDAS (fuel_logs) com Join para pegar o nome do tanque
        $saidas = DB::table('fuel_logs')
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

        // 3. Query de ENTRADAS (fuel_entries) unida com a de Saídas
        $historico = DB::table('fuel_entries')
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
            )
            ->union($saidas)
            ->orderBy('created_at', 'desc')
            ->get();

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
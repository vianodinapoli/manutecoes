<?php

namespace App\Http\Controllers;

use App\Models\FuelLog;
use App\Models\FuelEntry;
use App\Models\FuelSettlement;
use App\Models\Tank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FuelController extends Controller
{
    public function index(Request $request)
    {
        $tanques  = Tank::all();
        $fromDate = $request->from_date;
        $toDate   = $request->to_date;
        $tankId   = $request->filter_tank_id;
        $company  = $request->company;

        $queryEntradas = DB::table('fuel_entries')
            ->leftJoin('tanks', 'fuel_entries.tank_id', '=', 'tanks.id')
            ->select(
                'fuel_entries.id', 'fuel_entries.date',
                'fuel_entries.supplier as ident',
                DB::raw("'' as company"), 'fuel_entries.quantity',
                DB::raw("'' as operator"), DB::raw("'' as driver"),
                DB::raw("NULL as start_counter"), DB::raw("NULL as end_counter"),
                'tanks.nome as tanque_nome', DB::raw("'ENTRADA' as tipo"), 'fuel_entries.created_at'
            );

        $querySaidas = DB::table('fuel_logs')
            ->leftJoin('tanks', 'fuel_logs.tank_id', '=', 'tanks.id')
            ->select(
                'fuel_logs.id', 'fuel_logs.date', 'fuel_logs.plate as ident',
                'fuel_logs.company', 'fuel_logs.quantity', 'fuel_logs.operator', 'fuel_logs.driver',
                'fuel_logs.start_counter', 'fuel_logs.end_counter',
                'tanks.nome as tanque_nome', DB::raw("'SAÍDA' as tipo"), 'fuel_logs.created_at'
            );

        if ($fromDate) { $queryEntradas->where('fuel_entries.date', '>=', $fromDate); $querySaidas->where('fuel_logs.date', '>=', $fromDate); }
        if ($toDate)   { $queryEntradas->where('fuel_entries.date', '<=', $toDate);   $querySaidas->where('fuel_logs.date', '<=', $toDate); }
        if ($tankId)   { $queryEntradas->where('fuel_entries.tank_id', $tankId);       $querySaidas->where('fuel_logs.tank_id', $tankId); }

        if ($company) {
            $querySaidas->where('fuel_logs.company', 'LIKE', "%{$company}%");
            $historico = $querySaidas->orderBy('date', 'desc')->get();
        } else {
            $historico = $queryEntradas->union($querySaidas)->orderBy('date', 'desc')->get();
        }

        $totalConsumidoFiltro = $historico->where('tipo', 'SAÍDA')->sum('quantity');
        $totalEntradaFiltro   = $historico->where('tipo', 'ENTRADA')->sum('quantity');
        $contagemRegistos     = $historico->count();
        $capacidadeTotal      = $tanques->sum('capacidade');
        $restante             = $tanques->sum('stock_atual');
        $percentagem          = $capacidadeTotal > 0 ? ($restante / $capacidadeTotal) * 100 : 0;

        // ── Gráfico ──
        $ultimasDatas          = $historico->pluck('date')->unique()->sort()->take(-10);
        $movimentosProcessados = collect();
        foreach ($historico->whereIn('date', $ultimasDatas) as $reg) {
            $nomeTanque  = trim($reg->tanque_nome);
            $nomeEmpresa = trim($reg->company);
            if ($reg->tipo == 'SAÍDA') {
                $movimentosProcessados->push(['date' => $reg->date, 'label' => $nomeTanque,  'tipo' => 'SAÍDA',   'quantity' => $reg->quantity]);
                if (!empty($nomeEmpresa) && $nomeEmpresa !== 'N/A' && strtolower($nomeEmpresa) !== strtolower($nomeTanque))
                    $movimentosProcessados->push(['date' => $reg->date, 'label' => $nomeEmpresa, 'tipo' => 'SAÍDA', 'quantity' => $reg->quantity]);
            } else {
                $movimentosProcessados->push(['date' => $reg->date, 'label' => $nomeTanque, 'tipo' => 'ENTRADA', 'quantity' => $reg->quantity]);
            }
        }
        $dadosAgrupados  = $movimentosProcessados->groupBy(fn($i) => date('d/m', strtotime($i['date'])) . ' - ' . $i['label']);
        $labelsCompostas = $dataSaidas = $dataEntradas = [];
        foreach ($dadosAgrupados as $chave => $movs) {
            $labelsCompostas[] = explode(' - ', $chave);
            $dataSaidas[]      = $movs->where('tipo', 'SAÍDA')->sum('quantity');
            $dataEntradas[]    = $movs->where('tipo', 'ENTRADA')->sum('quantity');
        }
        $datasets = [
            ['label' => 'Saídas (Consumo)',  'data' => $dataSaidas,   'backgroundColor' => '#db5246', 'stack' => 'combustivel', 'borderRadius' => 4],
            ['label' => 'Entradas (Atesto)', 'data' => $dataEntradas, 'backgroundColor' => '#62c48e', 'stack' => 'combustivel', 'borderRadius' => 4],
        ];

        // ── Saldos por empresa (global, sem filtro de data) ──
        $saidasPorEmpresa = DB::table('fuel_logs')
            ->select('company', DB::raw('SUM(quantity) as total'), DB::raw('COUNT(*) as movimentos'))
            ->whereNotNull('company')->where('company', '!=', '')->where('company', '!=', 'N/A')
            ->groupBy('company')->orderByDesc('total')->get();

        $acertosPorEmpresa = DB::table('fuel_settlements')
            ->select('company', DB::raw('SUM(quantity) as total_acertado'))
            ->groupBy('company')->get()->keyBy('company');

        $saldosEmpresas = [];
        foreach ($saidasPorEmpresa as $row) {
            $acertado = isset($acertosPorEmpresa[$row->company]) ? (float) $acertosPorEmpresa[$row->company]->total_acertado : 0;
            $divida   = max(0, (float) $row->total - $acertado);
            $saldosEmpresas[$row->company] = [
                'saidas'     => (float) $row->total,
                'acertado'   => $acertado,
                'divida'     => $divida,
                'movimentos' => (int) $row->movimentos,
                'historico'  => DB::table('fuel_logs')->where('company', $row->company)->orderBy('date', 'desc')->limit(3)->get(['date', 'quantity', 'plate']),
                'acertos'    => DB::table('fuel_settlements')->where('company', $row->company)->orderBy('date', 'desc')->limit(3)->get(['date', 'quantity', 'notes']),
            ];
        }
        uasort($saldosEmpresas, fn($a, $b) => $b['divida'] <=> $a['divida']);

        return view('fuel.index', compact(
            'tanques', 'historico', 'labelsCompostas', 'datasets',
            'totalConsumidoFiltro', 'totalEntradaFiltro', 'contagemRegistos',
            'capacidadeTotal', 'restante', 'percentagem', 'saldosEmpresas'
        ));
    }

    // ── Registar Saída ──
    public function store(Request $request)
    {
        $request->validate([
            'tank_id' => 'required|exists:tanks,id', 'date' => 'required|date',
            'plate' => 'required', 'start_counter' => 'required|numeric', 'end_counter' => 'required|numeric',
        ]);
        $quantidade = $request->end_counter - $request->start_counter;
        FuelLog::create([
            'tank_id' => $request->tank_id, 'date' => $request->date, 'plate' => $request->plate,
            'company' => $request->company ?? 'N/A',
            'start_counter' => $request->start_counter, 'end_counter' => $request->end_counter,
            'quantity' => $quantidade,
            'operator' => $request->operator ?? auth()->user()->name ?? 'Sistema',
            'driver'   => $request->driver ?? 'Não informado',
        ]);
        $tanque = Tank::find($request->tank_id);
        if ($tanque) { $tanque->stock_atual -= $quantidade; $tanque->save(); }
        return redirect()->back()->with('success', 'Abastecimento registado!');
    }

    // ── Registar Entrada ──
    public function storeEntry(Request $request)
    {
        $request->validate(['tank_id' => 'required|exists:tanks,id', 'quantity' => 'required|numeric|min:0', 'date' => 'required|date']);
        FuelEntry::create(['date' => $request->date, 'quantity' => $request->quantity, 'supplier' => $request->supplier, 'tank_id' => $request->tank_id]);
        $tanque = Tank::find($request->tank_id);
        if ($tanque) { $tanque->stock_atual += $request->quantity; $tanque->save(); }
        return redirect()->back()->with('success', 'Stock reforçado!');
    }

    // ── Registar Acerto (devolução física ao tanque) ──
    public function storeSettlement(Request $request)
    {
        $request->validate([
            'company'  => 'required|string',
            'tank_id'  => 'required|exists:tanks,id',
            'date'     => 'required|date',
            'quantity' => 'required|numeric|min:1',
            'notes'    => 'nullable|string|max:255',
        ]);

        $totalSaidas  = FuelLog::where('company', $request->company)->sum('quantity');
        $totalAcertos = FuelSettlement::where('company', $request->company)->sum('quantity');
        $divida       = $totalSaidas - $totalAcertos;

        if ($request->quantity > $divida) {
            return response()->json([
                'success' => false,
                'message' => 'Não pode devolver ' . number_format($request->quantity, 0) . 'L. A dívida actual é de ' . number_format($divida, 0) . 'L.',
            ], 422);
        }

        FuelSettlement::create([
            'company'    => $request->company,
            'tank_id'    => $request->tank_id,
            'date'       => $request->date,
            'quantity'   => $request->quantity,
            'notes'      => $request->notes,
            'created_by' => auth()->id(),
        ]);

        $tanque = Tank::find($request->tank_id);
        if ($tanque) { $tanque->stock_atual += $request->quantity; $tanque->save(); }

        $novasDivida = max(0, $divida - $request->quantity);

        return response()->json([
            'success'     => true,
            'nova_divida' => $novasDivida,
            'message'     => $novasDivida == 0
                ? $request->company . ' está quite! Dívida totalmente liquidada.'
                : 'Devolvidos ' . number_format($request->quantity, 0) . 'L. Dívida restante: ' . number_format($novasDivida, 0) . 'L.',
        ]);
    }

    // ── Atualizar Entrada ──
    public function updateEntry(Request $request, $id)
    {
        $request->validate(['tank_id' => 'required|exists:tanks,id', 'date' => 'required|date', 'quantity' => 'required|numeric|min:1', 'supplier' => 'nullable|string|max:255']);
        $entry = FuelEntry::findOrFail($id);
        $tank  = Tank::findOrFail($entry->tank_id);
        $tank->stock_atual -= $entry->quantity;
        if ($entry->tank_id != $request->tank_id) { $tank->save(); $tank = Tank::findOrFail($request->tank_id); }
        $tank->stock_atual += $request->quantity;
        $tank->save();
        $entry->update(['tank_id' => $request->tank_id, 'date' => $request->date, 'quantity' => $request->quantity, 'supplier' => $request->supplier]);
        return redirect()->route('fuel.index')->with('success', 'Entrada atualizada.');
    }

    // ── Eliminar Saída — devolve litros ao tanque ──
    public function destroyLog($id)
    {
        $log    = FuelLog::findOrFail($id);
        $tanque = Tank::find($log->tank_id);
        if ($tanque) { $tanque->stock_atual += $log->quantity; $tanque->save(); }
        $log->delete();
        return response()->json(['success' => true]);
    }

    // ── Eliminar Entrada — remove litros do tanque ──
    public function destroyEntry($id)
    {
        $entry  = FuelEntry::findOrFail($id);
        $tanque = Tank::find($entry->tank_id);
        if ($tanque) { $tanque->stock_atual -= $entry->quantity; if ($tanque->stock_atual < 0) $tanque->stock_atual = 0; $tanque->save(); }
        $entry->delete();
        return response()->json(['success' => true]);
    }

    public function editLog($id)      { return view('fuel.edit_log', ['log' => FuelLog::findOrFail($id), 'tanques' => Tank::all()]); }
    public function getLogJson($id)   { return response()->json(FuelLog::findOrFail($id)); }
    public function getEntryJson($id) { return response()->json(FuelEntry::findOrFail($id)); }

    // ── Atualizar Saída ──
    public function updateLog(Request $request, $id)
    {
        $log    = FuelLog::findOrFail($id);
        $tanque = Tank::find($log->tank_id);
        if ($tanque) $tanque->stock_atual += $log->quantity;
        $quantidade = $request->end_counter - $request->start_counter;
        $log->update(array_merge($request->all(), ['quantity' => $quantidade]));
        if ($tanque) { $tanque->stock_atual -= $quantidade; $tanque->save(); }
        return response()->json(['success' => true, 'message' => 'Atualizado com sucesso!']);
    }
}
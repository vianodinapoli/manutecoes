<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Machine;
use App\Models\StockItem;

class ConsumptionController extends Controller
{
    public function index()
    {
        $movs = StockMovement::with(['stockItem', 'machine', 'maintenance'])->get();

        $totalUnits  = $movs->sum('quantity');
        $totalMovs   = $movs->count();
        $uniqueItems = $movs->pluck('stock_item_id')->unique()->count();

        // Top item
        $topItemData = $movs->groupBy('stock_item_id')
            ->map(fn($g) => ['qty' => $g->sum('quantity'), 'name' => optional($g->first()->stockItem)->name])
            ->sortByDesc('qty')->first();
        $topItemName = $topItemData['name'] ?? 'N/A';
        $topItemQty  = $topItemData['qty']  ?? 0;

        // Top máquina
        $topMachData = $movs->groupBy('machine_id')
            ->map(fn($g) => ['qty' => $g->sum('quantity'), 'name' => optional($g->first()->machine)->numero_interno])
            ->sortByDesc('qty')->first();
        $topMachName = $topMachData['name'] ?? 'N/A';
        $topMachQty  = $topMachData['qty']  ?? 0;

        // Ranking itens (top 8)
        $itemRanking = $movs->groupBy('stock_item_id')
            ->map(fn($g) => ['name' => optional($g->first()->stockItem)->name ?? 'Desconhecido', 'qty' => $g->sum('quantity')])
            ->sortByDesc('qty')->take(8)->values();
        $maxItemQty = $itemRanking->max('qty') ?: 1;

        // Ranking máquinas (top 6)
        $machRanking = $movs->groupBy('machine_id')
            ->map(fn($g) => [
                'num'  => optional($g->first()->machine)->numero_interno ?? 'N/A',
                'nome' => optional($g->first()->machine)->nome ?? 'Desconhecido',
                'qty'  => $g->sum('quantity'),
                'movs' => $g->count(),
            ])
            ->sortByDesc('qty')->take(6)->values();

        // Heatmap mensal
        $months    = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];
        $byMonth   = $movs->groupBy(fn($m) => optional($m->created_at)->month);
        $monthlyQty = collect(range(1, 12))
            ->mapWithKeys(fn($m) => [$m => $byMonth->get($m, collect())->sum('quantity')]);
        $maxMonthly = $monthlyQty->max() ?: 1;

        $machines  = Machine::orderBy('numero_interno')->get();
        $stockItems = StockItem::orderBy('name')->get();

        return view('consumption.index', compact(
            'movs', 'totalUnits', 'totalMovs', 'uniqueItems',
            'topItemName', 'topItemQty', 'topMachName', 'topMachQty',
            'itemRanking', 'maxItemQty', 'machRanking',
            'months', 'monthlyQty', 'maxMonthly',
            'machines', 'stockItems'
        ));
    }
}
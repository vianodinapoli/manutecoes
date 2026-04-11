<?php

namespace App\Http\Controllers;

use App\Models\MovimentoArmazem;
use App\Models\StockItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class MovimentoArmazemController extends Controller
{
    /*──────────────────────────────────────
     | INDEX — lista de movimentos
    ──────────────────────────────────────*/
    public function index(Request $request)
    {
        $query = MovimentoArmazem::with('stockItem')
            ->orderBy('created_at', 'desc');

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        if ($request->filled('stock_item_id')) {
            $query->where('stock_item_id', $request->stock_item_id);
        }
        if ($request->filled('responsavel')) {
            $query->where('responsavel', 'like', '%' . $request->responsavel . '%');
        }
        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }
        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }

        $movimentos     = $query->paginate(15)->withQueryString();
        $stockItems     = StockItem::orderBy('nome')->get();
        $totalEntradas  = MovimentoArmazem::where('tipo', 'entrada')->count();
        $totalSaidas    = MovimentoArmazem::where('tipo', 'saida')->count();
        $movimentosHoje = MovimentoArmazem::whereDate('created_at', today())->count();

        return view('armazem.movimentos.index', compact(
            'movimentos', 'stockItems', 'totalEntradas', 'totalSaidas', 'movimentosHoje'
        ));
    }

    /*──────────────────────────────────────
     | EXPORT PDF — relatório de movimentos
    ──────────────────────────────────────*/
    public function exportPdf(Request $request)
    {
        $query = MovimentoArmazem::with('stockItem')
            ->orderBy('created_at', 'desc');

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        if ($request->filled('stock_item_id')) {
            $query->where('stock_item_id', $request->stock_item_id);
        }
        if ($request->filled('responsavel')) {
            $query->where('responsavel', 'like', '%' . $request->responsavel . '%');
        }
        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }
        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }

        $movimentos     = $query->get();
        $totalEntradas  = $movimentos->where('tipo', 'entrada')->count();
        $totalSaidas    = $movimentos->where('tipo', 'saida')->count();
        $movimentosHoje = $movimentos->filter(fn($m) => $m->created_at->isToday())->count();

        $filtros = [
            'tipo'        => $request->tipo,
            'responsavel' => $request->responsavel,
            'data_inicio' => $request->data_inicio,
            'data_fim'    => $request->data_fim,
        ];

        $pdf = Pdf::loadView('armazem.movimentos.pdf', compact(
            'movimentos', 'totalEntradas', 'totalSaidas', 'movimentosHoje', 'filtros'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('movimentos-' . now()->format('Ymd-Hi') . '.pdf');
    }

    /*──────────────────────────────────────
     | STORE — registar novo movimento
    ──────────────────────────────────────*/
    public function store(Request $request)
    {
        $validated = $request->validate([
            'stock_item_id' => ['required', 'exists:stock_items,id'],
            'tipo'          => ['required', Rule::in(['entrada', 'saida'])],
            'quantidade'    => ['required', 'numeric', 'min:0.01'],
            'responsavel'   => ['required', 'string', 'max:255'],
            'observacoes'   => ['nullable', 'string', 'max:1000'],
        ], [
            'stock_item_id.required' => 'Selecione um item.',
            'stock_item_id.exists'   => 'Item inválido.',
            'tipo.required'          => 'Selecione o tipo de movimento.',
            'quantidade.required'    => 'Introduza a quantidade.',
            'quantidade.min'         => 'A quantidade deve ser maior que zero.',
            'responsavel.required'   => 'Introduza o nome do responsável.',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $item = StockItem::lockForUpdate()->findOrFail($validated['stock_item_id']);

                if ($validated['tipo'] === 'saida') {
                    if ($item->quantidade < $validated['quantidade']) {
                        throw new \Exception("Stock insuficiente. Disponível: {$item->quantidade}.");
                    }
                    $item->decrement('quantidade', $validated['quantidade']);
                } else {
                    $item->increment('quantidade', $validated['quantidade']);
                }

                MovimentoArmazem::create([
                    'stock_item_id' => $validated['stock_item_id'],
                    'tipo'          => $validated['tipo'],
                    'quantidade'    => $validated['quantidade'],
                    'responsavel'   => $validated['responsavel'],
                    'observacoes'   => $validated['observacoes'] ?? null,
                    'user_id'       => Auth::id(),
                ]);

                if (class_exists(\App\Models\Activity::class)) {
                    \App\Models\Activity::create([
                        'description' => ($validated['tipo'] === 'saida' ? 'Saída' : 'Entrada') . ' de ' . $validated['quantidade'] . ' × ' . $item->nome,
                        'type'        => 'stock',
                        'user_name'   => $validated['responsavel'],
                        'status'      => 'concluido',
                    ]);
                }
            });
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('movimentos.index')->with('success', 'Movimento registado com sucesso.');
    }

    /*──────────────────────────────────────
     | UPDATE — editar movimento
    ──────────────────────────────────────*/
    public function update(Request $request, MovimentoArmazem $movimento)
    {
        $validated = $request->validate([
            'responsavel' => ['required', 'string', 'max:255'],
            'observacoes' => ['nullable', 'string', 'max:1000'],
        ]);

        $movimento->update($validated);

        return redirect()->route('movimentos.index')->with('success', 'Movimento atualizado.');
    }

    /*──────────────────────────────────────
     | DESTROY — anular movimento
    ──────────────────────────────────────*/
    public function destroy(MovimentoArmazem $movimento)
    {
        try {
            DB::transaction(function () use ($movimento) {
                $item = StockItem::lockForUpdate()->findOrFail($movimento->stock_item_id);

                if ($movimento->tipo === 'saida') {
                    $item->increment('quantidade', $movimento->quantidade);
                } else {
                    if ($item->quantidade < $movimento->quantidade) {
                        throw new \Exception('Não é possível anular: stock actual inferior à quantidade da entrada.');
                    }
                    $item->decrement('quantidade', $movimento->quantidade);
                }

                $movimento->delete();
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('movimentos.index')->with('success', 'Movimento anulado e stock revertido.');
    }
}
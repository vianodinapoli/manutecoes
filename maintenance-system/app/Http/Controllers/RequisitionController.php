<?php

namespace App\Http\Controllers;

use App\Models\Requisition;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class RequisitionController extends Controller
{
    public function index()
    {
        $requisicoes = Requisition::with('supplier', 'items')->orderBy('created_at', 'desc')->get();
        return view('requisicoes.index', compact('requisicoes'));
    }

    // Substitua apenas o método create() no RequisitionController.php

public function create(Request $request)
{
    $fornecedores = Supplier::all();

    // ✅ Se vier de uma compra aprovada, carrega os dados dela
    $compra = null;
    $itensPreenchidos = [];

    if ($request->has('compra_id')) {
        $compra = \App\Models\MaterialPurchase::with('items')->find($request->compra_id);

        if ($compra && $compra->status === 'Aprovado') {
            foreach ($compra->items as $item) {
                $itensPreenchidos[] = [
                    'desc'  => $item->item_name,
                    'qty'   => $item->quantity,
                    'price' => 0, // Sem valor — homem das compras preenche
                ];
            }
        }
    }

    return view('requisicoes.create', compact('fornecedores', 'compra', 'itensPreenchidos'));
}

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'items'       => 'required|array|min:1',
            'total_final' => 'required|numeric',
        ]);

        try {
            $requisicao = DB::transaction(function () use ($request) {
                // 1. Cria o cabeçalho
                $requisicao = Requisition::create([
                    'supplier_id'  => $request->supplier_id,
                    'date'         => now(),
                    'total_liquid' => $request->total_liquid,
                    'tax_amount'   => $request->tax_amount,
                    'total_final'  => $request->total_final,
                    'has_tax'      => $request->has_tax == 'true',
                    'status'       => 'PENDENTE',
                ]);

                // 2. Cria os itens
                foreach ($request->items as $item) {
                    $requisicao->items()->create([
                        'description' => $item['desc'],
                        'quantity'    => $item['qty'],
                        'unit_price'  => $item['price'],
                        'subtotal'    => $item['qty'] * $item['price'],
                    ]);
                }

                return $requisicao;
            });

            // 3. Gera e salva o PDF após a transação
            $requisicao->load(['supplier', 'items']);

            // ✅ CORRIGIDO: variável $requisicao usada consistentemente
            $pdf = Pdf::loadView('requisicoes._print_template', compact('requisicao'));
            $pdf->setPaper('a4', 'portrait');

            $filename = "requisicao_{$requisicao->id}.pdf";
            $path     = "pdfs/{$filename}";

            // Salva em storage/app/public/pdfs/
            Storage::disk('public')->put($path, $pdf->output());

            // Guarda o caminho no banco
            $requisicao->update(['pdf_path' => $path]);

            return response()->json([
                'success' => true,
                'message' => 'Requisição gravada com sucesso!',
                'id'      => $requisicao->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar: ' . $e->getMessage()
            ], 500);
        }
    }

    public function gerarPdf($id)
    {
        $requisicao = Requisition::with(['supplier', 'items'])->findOrFail($id);

        // Se já tem PDF salvo no disco, serve direto
        if ($requisicao->pdf_path && Storage::disk('public')->exists($requisicao->pdf_path)) {
            return response()->file(
                storage_path('app/public/' . $requisicao->pdf_path),
                ['Content-Type' => 'application/pdf']
            );
        }

        // Fallback: gera na hora se o arquivo não existir
        $pdf = Pdf::loadView('requisicoes._print_template', compact('requisicao'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream("requisicao_{$id}.pdf");
    }
}
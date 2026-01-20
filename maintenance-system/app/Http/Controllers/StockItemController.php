<?php

namespace App\Http\Controllers;

use App\Models\StockItem;
use Illuminate\Http\Request;

class StockItemController extends Controller
{
    /**
     * Define as regras de validação comuns.
     */
    protected function validationRules(int $id = null): array
    {
        return [
            'nome' => 'required|string|max:255', // Adicionado para o título do card
            'referencia' => 'required|string|max:255|unique:stock_items,referencia,' . $id,
            'quantidade' => 'required|integer|min:0',
            'estado' => 'required|in:Novo,Recondicionado,Usado',
            'numero_armazem' => 'required|string|max:255',
            'seccao_armazem' => 'nullable|string|max:255',
            'marca_fabricante' => 'nullable|string|max:255',
            'modelo' => 'nullable|string|max:255',
            'categoria' => 'nullable|string|max:255',
            'sistema_maquina' => 'nullable|string|max:255',
            'metadata_key.*' => 'nullable|string|max:255',
            'metadata_value.*' => 'nullable|string|max:255',
        ];
    }
    
    /**
     * Processa os campos dinâmicos para JSON.
     */
    protected function processMetadata(Request $request): array
    {
        $metadata = [];
        if (isset($request->metadata_key) && is_array($request->metadata_key)) {
            $keys = $request->metadata_key;
            $values = $request->metadata_value;
            for ($i = 0; $i < count($keys); $i++) {
                if (!empty(trim($keys[$i]))) {
                    $metadata[trim($keys[$i])] = $values[$i] ?? null;
                }
            }
        }
        return $metadata;
    }

    // =========================================================
    // MÉTODOS DE VISUALIZAÇÃO E CRUD
    // =========================================================

    public function index()
    {
        $stockItems = StockItem::all();
        return view('stock_items.index', compact('stockItems'));
    }

    /**
     * MÉTODO DE EXPORTAÇÃO
     * Responsável por gerar o Excel (CSV) ou PDF
     */
    public function export(Request $request)
    {
        $items = StockItem::all();
        $type = $request->query('type', 'excel');

        if ($type === 'excel') {
            $fileName = 'mapa_de_stock_' . date('d-m-Y') . '.csv';
            
            $headers = [
                "Content-type"        => "text/csv; charset=UTF-8",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $columns = ['Nome do Artigo', 'Referência', 'Marca', 'Qtd', 'Estado', 'Armazém', 'Secção'];

            $callback = function() use($items, $columns) {
                $file = fopen('php://output', 'w');
                // Adiciona o BOM para o Excel abrir com caracteres especiais (acentos) corretamente
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($file, $columns, ';');

                foreach ($items as $item) {
                    fputcsv($file, [
                        $item->nome,
                        $item->referencia,
                        $item->marca_fabricante,
                        $item->quantidade,
                        $item->estado,
                        $item->numero_armazem,
                        $item->seccao_armazem
                    ], ';');
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // Se for PDF (requer barryvdh/laravel-dompdf instalado)
        if ($type === 'pdf') {
            if (!class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
                return redirect()->back()->with('error', 'Biblioteca PDF não instalada.');
            }
$pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('stock_items.pdf_export', compact('items'));
            return $pdf->download('mapa_de_stock.pdf');
        }

        return redirect()->back();
    }

    public function create()
    {
        return view('stock_items.create', ['stockItem' => new StockItem()]);
    }

    public function store(Request $request)
    {
        $request->validate($this->validationRules());
        $data = $request->except(['metadata_key', 'metadata_value']);
        $data['metadata'] = $this->processMetadata($request);

        $stockItem = StockItem::create($data);
        return redirect()->route('stock-items.show', $stockItem->id)
                         ->with('success', 'Item criado com sucesso!');
    }

    public function show(StockItem $stockItem)
    {
        return view('stock_items.show', compact('stockItem'));
    }

    public function edit(StockItem $stockItem)
    {
        return view('stock_items.edit', compact('stockItem'));
    }

    public function update(Request $request, StockItem $stockItem)
    {
        $request->validate($this->validationRules($stockItem->id));
        $data = $request->except(['metadata_key', 'metadata_value']);
        $data['metadata'] = $this->processMetadata($request);

        $stockItem->update($data);
        return redirect()->route('stock-items.show', $stockItem->id)
                         ->with('success', 'Item atualizado com sucesso!');
    }

    public function destroy(StockItem $stockItem)
    {
        $stockItem->delete();
        return redirect()->route('stock-items.index')
                         ->with('success', 'Item eliminado.');
    }

    
}
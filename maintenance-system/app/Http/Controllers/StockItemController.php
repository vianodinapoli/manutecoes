<?php

namespace App\Http\Controllers;

use App\Models\StockItem;
use App\Models\Activity;
use Illuminate\Http\Request;

class StockItemController extends Controller
{
    /**
     * Define as regras de validação comuns.
     */
    protected function validationRules(int $id = null): array
    {
        return [
            'nome' => 'required|string|max:255',
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

    public function index()
    {
        $stockItems = StockItem::all();
        return view('stock_items.index', compact('stockItems'));
    }

    public function create()
    {
        return view('stock_items.create', ['stockItem' => new StockItem()]);
    }

    /**
     * SALVAR NOVO ITEM
     */
    public function store(Request $request)
    {
        // 1. Validação
        $request->validate($this->validationRules());

        // 2. Preparar dados
        $data = $request->except(['metadata_key', 'metadata_value']);
        $data['metadata'] = $this->processMetadata($request);

        // 3. Criar o item primeiro (para ter o objeto e evitar erro de null)
        $stockItem = StockItem::create($data);

        // 4. Registar Atividade Detalhada
        Activity::create([
            'type' => 'stock',
            'description' => "Entrada inicial: {$stockItem->quantidade} unidades de {$stockItem->nome} (Ref: {$stockItem->referencia})",
            'user_name' => auth()->user()->name,
            'status' => 'concluido'
        ]);

        return redirect()->route('stock-items.show', $stockItem->id)
                         ->with('success', 'Item criado e registado no histórico!');
    }

    public function show(StockItem $stockItem)
    {
        return view('stock_items.show', compact('stockItem'));
    }

    public function edit(StockItem $stockItem)
    {
        return view('stock_items.edit', compact('stockItem'));
    }

    /**
     * ATUALIZAR ITEM (SAÍDAS OU EDIÇÕES)
     */
    public function update(Request $request, StockItem $stockItem)
    {
        // 1. Validação
        $request->validate($this->validationRules($stockItem->id));

        // 2. Guardar valores antigos para a descrição da atividade
        $qtdAnterior = $stockItem->quantidade;

        // 3. Processar e Atualizar
        $data = $request->except(['metadata_key', 'metadata_value']);
        $data['metadata'] = $this->processMetadata($request);
        
        $stockItem->update($data);

        // 4. Gerar descrição baseada na mudança de quantidade
        $mensagem = "Editou o item {$stockItem->nome}";
        if ($qtdAnterior != $stockItem->quantidade) {
            $diferenca = $stockItem->quantidade - $qtdAnterior;
            $acao = $diferenca > 0 ? "Entrada" : "Saída";
            $mensagem = "{$acao} de " . abs($diferenca) . " unidades de {$stockItem->nome} (Stock atual: {$stockItem->quantidade})";
        }

        // 5. Registar Atividade
        Activity::create([
            'type' => 'stock',
            'description' => $mensagem,
            'user_name' => auth()->user()->name,
            'status' => $stockItem->quantidade <= 5 ? 'alerta' : 'concluido'
        ]);

        return redirect()->route('stock-items.show', $stockItem->id)
                         ->with('success', 'Item atualizado com sucesso!');
    }

    public function destroy(StockItem $stockItem)
    {
        $nomeRemovido = $stockItem->nome;
        $stockItem->delete();

        Activity::create([
            'type' => 'stock',
            'description' => "Eliminou o item {$nomeRemovido} do sistema",
            'user_name' => auth()->user()->name,
            'status' => 'alerta'
        ]);

        return redirect()->route('stock-items.index')
                         ->with('success', 'Item eliminado.');
    }

    /**
     * EXPORTAÇÃO
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
            ];

            $callback = function() use($items) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($file, ['Nome do Artigo', 'Referência', 'Marca', 'Qtd', 'Estado', 'Armazém'], ';');

                foreach ($items as $item) {
                    fputcsv($file, [$item->nome, $item->referencia, $item->marca_fabricante, $item->quantidade, $item->estado, $item->numero_armazem], ';');
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }

        if ($type === 'pdf' && class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('stock_items.pdf_export', compact('items'));
            return $pdf->download('mapa_de_stock.pdf');
        }

        return redirect()->back();
    }
}
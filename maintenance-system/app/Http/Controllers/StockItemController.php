<?php

namespace App\Http\Controllers;

use App\Models\StockItem;
use Illuminate\Http\Request;

class StockItemController extends Controller
{
    /**
     * Define as regras de validação comuns para os campos fixos.
     * Inclui a regra 'unique' para a referência, ignorando o item atual na edição.
     */
    protected function validationRules(int $id = null): array
    {
        return [
            // Validação dos campos fixos
            'referencia' => 'required|string|max:255|unique:stock_items,referencia,' . $id,
            'quantidade' => 'required|integer|min:0',
            'estado' => 'required|in:Novo,Recondicionado,Usado',
            'numero_armazem' => 'required|string|max:255',
            'seccao_armazem' => 'nullable|string|max:255',
            'marca_fabricante' => 'nullable|string|max:255',
            'modelo' => 'nullable|string|max:255',
            'categoria' => 'nullable|string|max:255',
            'sistema_maquina' => 'nullable|string|max:255',
            
            // Validação básica para os campos dinâmicos
            'metadata_key.*' => 'nullable|string|max:255',
            'metadata_value.*' => 'nullable|string|max:255',
        ];
    }
    
    /**
     * Processa os arrays de chaves e valores dos campos dinâmicos, 
     * convertendo-os para um único array associativo para a coluna metadata (JSON).
     */
    protected function processMetadata(Request $request): array
    {
        $metadata = [];
        if (isset($request->metadata_key) && is_array($request->metadata_key)) {
            $keys = $request->metadata_key;
            $values = $request->metadata_value;

            // Percorre os arrays, emparelhando chave e valor pela posição
            for ($i = 0; $i < count($keys); $i++) {
                // Adiciona apenas se a chave (nome do campo personalizado) não estiver vazia
                if (!empty(trim($keys[$i]))) {
                    $metadata[trim($keys[$i])] = $values[$i] ?? null;
                }
            }
        }
        return $metadata;
    }

    // =========================================================
    // MÉTODOS CRUD PRINCIPAIS
    // =========================================================

    /**
     * Exibe uma listagem de todos os itens de stock.
     */
    public function index()
    {
        $stockItems = StockItem::paginate(15);
        return view('stock_items.index', compact('stockItems'));
    }

    /**
     * Mostra o formulário para criar um novo item.
     */
    public function create()
    {
        // Passa uma instância vazia para o formulário
        return view('stock_items.create', ['stockItem' => new StockItem()]);
    }

    /**
     * Armazena um item recém-criado na base de dados.
     */
    public function store(Request $request)
    {
        $request->validate($this->validationRules());
        
        $data = $request->except(['metadata_key', 'metadata_value']); // Remove os arrays brutos do request
        $data['metadata'] = $this->processMetadata($request); // Adiciona o array metadata processado

        $stockItem = StockItem::create($data);

        return redirect()->route('stock-items.show', $stockItem->id)
                         ->with('success', 'Item de stock criado com sucesso!');
    }

    /**
     * Exibe os detalhes de um item específico.
     */
    public function show(StockItem $stockItem)
    {
        return view('stock_items.show', compact('stockItem'));
    }

    /**
     * Mostra o formulário para editar um item.
     */
    public function edit(StockItem $stockItem)
    {
        return view('stock_items.edit', compact('stockItem'));
    }

    /**
     * Atualiza um item específico na base de dados.
     */
    public function update(Request $request, StockItem $stockItem)
    {
        $request->validate($this->validationRules($stockItem->id));
        
        $data = $request->except(['metadata_key', 'metadata_value']);
        $data['metadata'] = $this->processMetadata($request);

        $stockItem->update($data);

        return redirect()->route('stock-items.show', $stockItem->id)
                         ->with('success', 'Item de stock atualizado com sucesso!');
    }

    /**
     * Remove um item específico da base de dados.
     */
    public function destroy(StockItem $stockItem)
    {
        $stockItem->delete();

        return redirect()->route('stock-items.index')
                         ->with('success', 'Item de stock eliminado com sucesso.');
    }
}
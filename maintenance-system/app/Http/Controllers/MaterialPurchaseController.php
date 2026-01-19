<?php

namespace App\Http\Controllers;

use App\Models\MaterialPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaterialPurchaseController extends Controller
{
    /**
     * Exibe a lista de compras (Página Principal)
     */
    public function index()
    {
        // Ordena pelos mais recentes
        $compras = MaterialPurchase::latest()->paginate(10);
        return view('compras.index', compact('compras'));
    }

    /**
     * Exibe o formulário de criação
     */
    public function create()
    {
        return view('compras.create');
    }

    /**
     * Salva a nova compra no banco de dados
     */
    public function store(Request $request)
{
    $request->validate([
        'item_name'    => 'required|string|max:255',
        'quantity'     => 'required|integer|min:1',
        'price'        => 'nullable|numeric',
        'quotation'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        'metadata'     => 'nullable|array',
    ]);

    $data = $request->all();

    // --- AQUI ESTÁ A MUDANÇA ---
    // Adiciona o ID do utilizador autenticado aos dados que serão salvos
    $data['user_id'] = auth()->id(); 

    // Tratamento do Upload do Ficheiro
    if ($request->hasFile('quotation')) {
        $path = $request->file('quotation')->store('cotacoes', 'public');
        $data['quotation_file'] = $path;
    }

    MaterialPurchase::create($data);

    return redirect()->route('compras.index')
        ->with('success', 'Pedido de compra registado com sucesso!');
}

    /**
     * Atualiza apenas o status da compra (Ação rápida na tabela)
     */
    public function updateStatus(Request $request, MaterialPurchase $compra)
    {
        $request->validate([
            'status' => 'required|in:Pendente,Em processo,Aprovado,Rejeitado'
        ]);

        $compra->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', "Status da compra #{$compra->id} atualizado!");
    }

    /**
     * Elimina um registo e o ficheiro associado
     */
    public function destroy(MaterialPurchase $compra)
    {
        // Remove o ficheiro do storage antes de apagar o registo
        if ($compra->quotation_file) {
            Storage::disk('public')->delete($compra->quotation_file);
        }

        $compra->delete();

        return redirect()->route('compras.index')
            ->with('success', 'Registo eliminado com sucesso.');
    }

    /**
 * Exibe o formulário de edição
 */
public function edit(MaterialPurchase $compra)
{
    return view('compras.edit', compact('compra'));
}

/**
 * Atualiza os dados da compra
 */
public function update(Request $request, MaterialPurchase $compra)
{
    $request->validate([
        'item_name' => 'required|string|max:255',
        'quantity'  => 'required|integer|min:1',
        'price'     => 'nullable|numeric',
        'metadata'  => 'nullable|array'
    ]);

    $data = $request->all();

    if ($request->hasFile('quotation')) {
        // Apaga o ficheiro antigo se existir um novo
        if ($compra->quotation_file) {
            Storage::disk('public')->delete($compra->quotation_file);
        }
        $data['quotation_file'] = $request->file('quotation')->store('cotacoes', 'public');
    }

    $compra->update($data);

    return redirect()->route('compras.index')->with('success', 'Compra atualizada!');
}

public function show(MaterialPurchase $compra)
{
    // O Laravel já carrega o Model automaticamente pelo ID na rota
    return view('compras.show', compact('compra'));
}
}
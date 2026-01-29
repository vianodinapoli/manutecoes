<?php

namespace App\Http\Controllers;

use App\Models\MaterialPurchase;
use Illuminate\Http\Request;

class MaterialPurchaseController extends Controller
{
    public function index()
    {
        $compras = MaterialPurchase::with('user')->orderBy('created_at', 'desc')->get();
        return view('compras.index', compact('compras'));
    }

    public function create()
    {
        return view('compras.create');
    }

    public function store(Request $request)
{
    // 1. Validação aponta para o campo dentro do array metadata
    $request->validate([
        'item_name' => 'required',
        'metadata.urgencia' => 'required', 
        'quotation_file' => 'nullable|file|mimes:pdf,jpg,png|max:2048'
    ]);

    $data = $request->only(['item_name', 'quantity', 'price']);
    $data['user_id'] = auth()->id();
    $data['status'] = 'Pendente';
    
    // 2. Aqui capturamos o array metadata completo (urgencia, placa, etc)
    $data['metadata'] = $request->metadata;

    if ($request->hasFile('quotation_file')) {
        $data['quotation_file'] = $request->file('quotation_file')->store('quotations', 'public');
    }

    \App\Models\MaterialPurchase::create($data);

    return redirect()->route('compras.index')->with('success', 'Pedido criado!');
}

    public function update(Request $request, $id)
    {
        $compra = MaterialPurchase::findOrFail($id);
        
        $data = $request->all();
        
        // Se houver alteração no array de metadata, faz o merge para não apagar dados antigos
        if ($request->has('metadata')) {
            $data['metadata'] = array_merge($compra->metadata ?? [], $request->metadata);
        }

        if ($request->hasFile('quotation_file')) {
            $data['quotation_file'] = $request->file('quotation_file')->store('quotations', 'public');
        }

        $compra->update($data);
        
        return redirect()->route('compras.index')->with('success', 'Solicitação atualizada!');
    }

    public function show($id)
    {
        $compra = MaterialPurchase::with('user')->findOrFail($id);
        return view('compras.show', compact('compra'));
    }

    public function edit($id)
    {
        $compra = MaterialPurchase::findOrFail($id);
        return view('compras.edit', compact('compra'));
    }

    public function updateStatus(Request $request, $id)
    {
        $compra = MaterialPurchase::findOrFail($id);
        $user = auth()->user();

        if ($user->hasRole('super-admin')) {
            $allowedStatus = ['Pendente', 'Em processo', 'Aprovado', 'Rejeitado', 'Finalizado'];
        } else {
            $allowedStatus = ['Em processo', 'Finalizado'];
            if (!in_array($request->status, $allowedStatus)) {
                return back()->with('error', 'Acesso negado para este status.');
            }
        }

        $request->validate([
            'status' => 'required|in:' . implode(',', $allowedStatus),
        ]);

        $compra->update(['status' => $request->status]);
        return back()->with('success', "Status atualizado!");
    }

    public function destroy($id)
    {
        $compra = MaterialPurchase::findOrFail($id);
        $compra->delete();
        return redirect()->route('compras.index')->with('success', 'Eliminado com sucesso!');
    }
}
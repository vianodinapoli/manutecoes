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
        $request->validate([
            'item_name' => 'required',
            'metadata.urgencia' => 'required', 
            'quotation_file' => 'nullable|file|mimes:pdf,jpg,png|max:2048'
        ]);

        $data = $request->only(['item_name', 'quantity', 'price']);
        $data['user_id'] = auth()->id();
        $data['status'] = 'Pendente';
        $data['metadata'] = $request->metadata;

        if ($request->hasFile('quotation_file')) {
            $data['quotation_file'] = $request->file('quotation_file')->store('quotations', 'public');
        }

        MaterialPurchase::create($data);
        return redirect()->route('compras.index')->with('success', 'Pedido criado!');
    }

    public function edit($id)
    {
        $compra = MaterialPurchase::findOrFail($id);

        // BLOQUEIO: Se estiver Finalizado e não for Admin, não entra na tela de edição
        if ($compra->status === 'Finalizado' && !auth()->user()->hasRole('super-admin')) {
            return redirect()->route('compras.index')->with('error', 'Este pedido está finalizado e não pode ser editado.');
        }

        return view('compras.edit', compact('compra'));
    }

    public function update(Request $request, $id)
    {
        $compra = MaterialPurchase::findOrFail($id);

        // BLOQUEIO: Impede salvar alterações se estiver finalizado (segurança de back-end)
        if ($compra->status === 'Finalizado' && !auth()->user()->hasRole('super-admin')) {
            return redirect()->route('compras.index')->with('error', 'Ação não permitida para pedidos finalizados.');
        }

        $data = $request->all();
        
        if ($request->has('metadata')) {
            $data['metadata'] = array_merge($compra->metadata ?? [], $request->metadata);
        }

        if ($request->hasFile('quotation_file')) {
            $data['quotation_file'] = $request->file('quotation_file')->store('quotations', 'public');
        }

        $compra->update($data);
        return redirect()->route('compras.index')->with('success', 'Solicitação atualizada!');
    }

    public function updateStatus(Request $request, $id)
    {
        $compra = MaterialPurchase::findOrFail($id);
        $user = auth()->user();

        // BLOQUEIO: Se já estiver Finalizado, apenas Admin pode reabrir ou mudar
        if ($compra->status === 'Finalizado' && !$user->hasRole('super-admin')) {
            return back()->with('error', 'Apenas administradores podem alterar o status de um pedido finalizado.');
        }

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

    public function show($id)
    {
        $compra = MaterialPurchase::with('user')->findOrFail($id);
        return view('compras.show', compact('compra'));
    }

    public function destroy($id)
    {
        $compra = MaterialPurchase::findOrFail($id);
        $compra->delete();
        return redirect()->route('compras.index')->with('success', 'Eliminado com sucesso!');
    }
}
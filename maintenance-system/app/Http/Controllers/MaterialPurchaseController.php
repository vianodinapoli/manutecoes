<?php

namespace App\Http\Controllers;

use App\Models\MaterialPurchase;
use Illuminate\Http\Request;

class MaterialPurchaseController extends Controller
{
    // Status que impedem a edição por usuários comuns
    protected $lockedStatuses = ['Finalizado', 'Rejeitado'];

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

        // BLOQUEIO: Se o status for Finalizado ou Rejeitado e não for Admin
        if (in_array($compra->status, $this->lockedStatuses) && !auth()->user()->hasRole('super-admin')) {
            return redirect()->route('compras.index')->with('error', 'Este pedido está ' . strtolower($compra->status) . ' e não pode ser editado.');
        }

        return view('compras.edit', compact('compra'));
    }

   public function update(Request $request, $id)
{
    $compra = MaterialPurchase::findOrFail($id);

    // Bloqueio de segurança
    if (in_array($compra->status, $this->lockedStatuses) && !auth()->user()->hasRole('super-admin')) {
        return redirect()->route('compras.index')->with('error', 'Não é permitido editar pedidos encerrados.');
    }

    // Pega os dados básicos
    $data = $request->only(['item_name', 'quantity', 'price']);
    
    // Processa o Metadata (Placa, Urgência, Descrição)
    if ($request->has('metadata')) {
        $data['metadata'] = array_merge($compra->metadata ?? [], $request->metadata);
    }

    // Processa o Ficheiro (Agora com o nome correto: quotation_file)
    if ($request->hasFile('quotation_file')) {
        // Remove o antigo se existir
        if ($compra->quotation_file) {
            \Storage::disk('public')->delete($compra->quotation_file);
        }
        
        // Guarda o novo
        $data['quotation_file'] = $request->file('quotation_file')->store('quotations', 'public');
    }

    $compra->update($data);

    return redirect()->route('compras.index')->with('success', 'Solicitação atualizada com sucesso!');
}

    public function updateStatus(Request $request, $id)
    {
        $compra = MaterialPurchase::findOrFail($id);
        $user = auth()->user();

        // BLOQUEIO: Impede usuário comum de alterar status de pedidos já finalizados ou rejeitados
        if (in_array($compra->status, $this->lockedStatuses) && !$user->hasRole('super-admin')) {
            return back()->with('error', 'Apenas administradores podem reabrir pedidos finalizados ou rejeitados.');
        }

        if ($user->hasRole('super-admin')) {
            $allowedStatus = ['Pendente', 'Em processo', 'Aprovado', 'Rejeitado', 'Finalizado'];
        } else {
            // Usuário comum só pode mover para estes status se o pedido não estiver bloqueado
            $allowedStatus = ['Em processo', 'Finalizado', 'Rejeitado'];
            
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
        
        // Opcional: Impedir que usuários apaguem se estiver finalizado (mesmo que o botão suma na view)
        if (in_array($compra->status, $this->lockedStatuses) && !auth()->user()->hasRole('super-admin')) {
            return back()->with('error', 'Não é possível eliminar um registro finalizado.');
        }

        $compra->delete();
        return redirect()->route('compras.index')->with('success', 'Eliminado com sucesso!');
    }
}
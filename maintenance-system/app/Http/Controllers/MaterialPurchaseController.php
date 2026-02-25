<?php

namespace App\Http\Controllers;

use App\Models\MaterialPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MaterialPurchaseController extends Controller
{
    protected $lockedStatuses = ['Finalizado', 'Rejeitado'];

    public function index()
    {
        // Carregamos também os itens para a listagem se necessário
        $compras = MaterialPurchase::with(['user', 'items'])->orderBy('created_at', 'desc')->get();
        return view('compras.index', compact('compras'));
    }

    public function create()
    {
        return view('compras.create');
    }

    public function store(Request $request)
    {
        // 1. Nova Validação compatível com o formulário dinâmico
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.destino' => 'required|string',
            'urgencia' => 'required|in:Normal,Alta,Crítica',
            'description' => 'nullable|string',
            'fornecedor' => 'nullable|string',
            'quotation_files.*' => 'nullable|file|mimes:pdf,jpg,png|max:5120'
        ]);

        try {
            return DB::transaction(function () use ($request) {
                // 2. Criar a "Capa" do pedido
                $purchase = MaterialPurchase::create([
                    'user_id' => auth()->id(),
                    'fornecedor' => $request->fornecedor,
                    'urgencia' => $request->urgencia,
                    'description' => $request->description,
                    'status' => 'Pendente',
                ]);

                // 3. Salvar os múltiplos ITENS
                foreach ($request->items as $item) {
                    $purchase->items()->create([
                        'item_name' => $item['name'],
                        'quantity'  => $item['quantity'],
                        'destino'   => $item['destino'],
                    ]);
                }

                // 4. Salvar os múltiplos FICHEIROS
                if ($request->hasFile('quotation_files')) {
                    foreach ($request->file('quotation_files') as $file) {
                        $path = $file->store('purchases/attachments', 'public');
                        $purchase->attachments()->create([
                            'file_path' => $path,
                            'file_name' => $file->getClientOriginalName(),
                        ]);
                    }
                }

                return redirect()->route('compras.index')->with('success', 'Requisição multilinear criada com sucesso!');
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro técnico: ' . $e->getMessage());
        }
    }

    // O método show precisa carregar as relações para exibir na tela
    public function show($id)
    {
        $compra = MaterialPurchase::with(['user', 'items', 'attachments'])->findOrFail($id);
        return view('compras.show', compact('compra'));
    }

    // Os métodos Edit e Update precisarão de uma lógica similar à do Store 
    // para lidar com os arrays de itens, se desejar permitir edição multilinear.
    
    public function updateStatus(Request $request, $id)
    {
        $compra = MaterialPurchase::findOrFail($id);
        $user = auth()->user();

        if (in_array($compra->status, $this->lockedStatuses) && !$user->hasRole('super-admin')) {
            return back()->with('error', 'Apenas administradores podem reabrir pedidos finalizados.');
        }

        $allowedStatus = $user->hasRole('super-admin') 
            ? ['Pendente', 'Em processo', 'Aprovado', 'Rejeitado', 'Finalizado']
            : ['Em processo', 'Finalizado', 'Rejeitado'];

        $request->validate(['status' => 'required|in:' . implode(',', $allowedStatus)]);

        $compra->update(['status' => $request->status]);
        return back()->with('success', "Status atualizado!");
    }

    public function destroy($id)
    {
        $compra = MaterialPurchase::findOrFail($id);
        
        if (in_array($compra->status, $this->lockedStatuses) && !auth()->user()->hasRole('super-admin')) {
            return back()->with('error', 'Não é possível eliminar um registro finalizado.');
        }

        // Os anexos e itens serão apagados automaticamente se usou onDelete('cascade') na migration
        $compra->delete();
        return redirect()->route('compras.index')->with('success', 'Eliminado com sucesso!');
    }

    public function edit($id)
    {
        // Carrega a compra com os itens e anexos
        $compra = MaterialPurchase::with(['items', 'attachments'])->findOrFail($id);
        
        // Bloqueio de edição para status finalizados (exceto super-admin)
        if (in_array($compra->status, $this->lockedStatuses) && !auth()->user()->hasRole('super-admin')) {
            return redirect()->route('compras.index')->with('error', 'Este pedido está fechado e não pode ser editado.');
        }

        return view('compras.edit', compact('compra'));
    }

    public function update(Request $request, $id)
    {
        $compra = MaterialPurchase::findOrFail($id);

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.destino' => 'required|string',
            'urgencia' => 'required|in:Normal,Alta,Crítica',
            'description' => 'nullable|string',
            'fornecedor' => 'nullable|string',
            'quotation_files.*' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
            'delete_attachments' => 'nullable|array'
        ]);

        try {
            return DB::transaction(function () use ($request, $compra) {
                // 1. Atualizar a "Capa"
                $compra->update([
                    'fornecedor' => $request->fornecedor,
                    'urgencia' => $request->urgencia,
                    'description' => $request->description,
                ]);

                // 2. Sincronizar Itens (Estratégia: Remover atuais e reinserir)
                // É a forma mais limpa para formulários dinâmicos multilineares
                $compra->items()->delete(); 
                foreach ($request->items as $item) {
                    $compra->items()->create([
                        'item_name' => $item['name'],
                        'quantity'  => $item['quantity'],
                        'destino'   => $item['destino'],
                    ]);
                }

                // 3. Remover anexos selecionados (se houver)
                if ($request->has('delete_attachments')) {
                    foreach ($request->delete_attachments as $attachmentId) {
                        $attachment = $compra->attachments()->find($attachmentId);
                        if ($attachment) {
                            Storage::disk('public')->delete($attachment->file_path);
                            $attachment->delete();
                        }
                    }
                }

                // 4. Salvar novos anexos
                if ($request->hasFile('quotation_files')) {
                    foreach ($request->file('quotation_files') as $file) {
                        $path = $file->store('purchases/attachments', 'public');
                        $compra->attachments()->create([
                            'file_path' => $path,
                            'file_name' => $file->getClientOriginalName(),
                        ]);
                    }
                }

                return redirect()->route('compras.index')->with('success', 'Requisição atualizada com sucesso!');
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao atualizar: ' . $e->getMessage());
        }
    }
}
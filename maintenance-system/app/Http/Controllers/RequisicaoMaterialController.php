<?php
namespace App\Http\Controllers;

use App\Models\RequisicaoMaterial;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class RequisicaoMaterialController extends Controller
{
    public function index()
    {
        $requisicoes = RequisicaoMaterial::with(['items', 'creator', 'supplier'])
            ->latest()->get();

        $suppliers = Supplier::orderBy('name')->get(['id', 'name', 'nuit', 'code']);

        return view('requisicoes-material.index', compact('requisicoes', 'suppliers'));
    }

    public function store(Request $request)
    {
        $this->validateRequisicao($request);

        DB::transaction(function () use ($request) {
            $req = RequisicaoMaterial::create([
                'date'        => $request->date,
                'destino'     => $request->destino,
                'supplier_id' => $request->supplier_id ?: null,
                'matricula'   => $request->matricula,
                'motorista'   => $request->motorista,
                'responsavel' => $request->responsavel,
                'observacoes' => $request->observacoes,
                'status'      => 'EMITIDA',
                'created_by'  => auth()->id(),
            ]);

            $this->syncItems($req, $request->items);
        });

        return response()->json(['success' => true, 'message' => 'Requisição criada com sucesso.']);
    }

    /**
     * Retorna JSON para o modal de edição.
     */
    public function show(RequisicaoMaterial $requisicaoMaterial)
    {
        $requisicaoMaterial->load(['items', 'supplier']);

        return response()->json([
            'id'             => $requisicaoMaterial->id,
            'date'           => $requisicaoMaterial->date->format('Y-m-d'),
            'destino'        => $requisicaoMaterial->destino,
            'supplier_id'    => $requisicaoMaterial->supplier_id,
            'motorista'      => $requisicaoMaterial->motorista,
            'matricula'      => $requisicaoMaterial->matricula,
            'responsavel'    => $requisicaoMaterial->responsavel,
            'observacoes'    => $requisicaoMaterial->observacoes,
            'status'         => $requisicaoMaterial->status,
            'local_descarga' => $requisicaoMaterial->local_descarga,
            'items'          => $requisicaoMaterial->items->map(fn($i) => [
                'description' => $i->description,
                'quantity'    => $i->quantity,
                'unit'        => $i->unit,
                'unit_price'  => $i->unit_price,
            ]),
        ]);
    }

    public function update(Request $request, RequisicaoMaterial $requisicaoMaterial)
    {
        $this->validateRequisicao($request, withStatus: true);

        DB::transaction(function () use ($request, $requisicaoMaterial) {
            $requisicaoMaterial->update([
                'date'        => $request->date,
                'destino'     => $request->destino,
                'supplier_id' => $request->supplier_id ?: null,
                'matricula'   => $request->matricula,
                'motorista'   => $request->motorista,
                'responsavel' => $request->responsavel,
                'observacoes' => $request->observacoes,
                'status'      => $request->status,
            ]);

            $requisicaoMaterial->items()->delete();
            $this->syncItems($requisicaoMaterial, $request->items);
        });

        return response()->json(['success' => true, 'message' => 'Requisição actualizada com sucesso.']);
    }

    public function destroy(RequisicaoMaterial $requisicaoMaterial)
    {
        $requisicaoMaterial->items()->delete();
        $requisicaoMaterial->delete();

        return response()->json(['success' => true]);
    }

    public function pdf(RequisicaoMaterial $requisicaoMaterial)
    {
        $requisicaoMaterial->load(['items', 'creator', 'supplier']);

        $pdf = Pdf::loadView('requisicoes-material.pdf', ['requisicao' => $requisicaoMaterial])
                  ->setPaper('a4', 'portrait');

        return $pdf->stream("req-material-{$requisicaoMaterial->id}.pdf");
    }

    /**
     * Confirma o peso e valor real da carga e finaliza a requisição.
     * POST /requisicoes-material/{requisicaoMaterial}/confirmar-carga
     */
    public function confirmarCarga(Request $request, RequisicaoMaterial $requisicaoMaterial)
    {
        $request->validate([
            'peso_confirmado' => 'required|numeric|min:0',
            'valor_carga'     => 'required|numeric|min:0',
            'numero_guia'     => 'required|string|max:255',
            'local_descarga'  => 'nullable|string|max:500',
        ]);

        $requisicaoMaterial->update([
            'peso_confirmado' => $request->peso_confirmado,
            'valor_carga'     => $request->valor_carga,
            'numero_guia'     => $request->numero_guia,
            'local_descarga'  => $request->local_descarga,
            'status'          => 'FINALIZADA',
        ]);

        return response()->json(['success' => true, 'message' => 'Requisição finalizada com sucesso.']);
    }

    /**
     * Edita os dados de carga de uma requisição já finalizada.
     * PATCH /requisicoes-material/{requisicaoMaterial}/editar-carga
     *
     * Permite corrigir peso, valor, número de guia e local de descarga
     * sem alterar o estado. O PDF é regenerado automaticamente no próximo acesso.
     */
    public function editarCarga(Request $request, RequisicaoMaterial $requisicaoMaterial)
    {
        $request->validate([
            'peso_confirmado' => 'required|numeric|min:0',
            'valor_carga'     => 'required|numeric|min:0',
            'numero_guia'     => 'required|string|max:255',
            'local_descarga'  => 'nullable|string|max:500',
        ]);

        $requisicaoMaterial->update([
            'peso_confirmado' => $request->peso_confirmado,
            'valor_carga'     => $request->valor_carga,
            'numero_guia'     => $request->numero_guia,
            'local_descarga'  => $request->local_descarga,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dados de carga actualizados com sucesso.',
        ]);
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    private function validateRequisicao(Request $request, bool $withStatus = false): void
    {
        $rules = [
            'date'                => 'required|date',
            'destino'             => 'required|string|max:255',
            'supplier_id'         => 'nullable|exists:suppliers,id',
            'matricula'           => 'nullable|string|max:20',
            'motorista'           => 'nullable|string|max:100',
            'responsavel'         => 'nullable|string|max:100',
            'observacoes'         => 'nullable|string',
            'items'               => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity'    => 'required|numeric|min:0.001',
            'items.*.unit'        => 'required|string|max:10',
            'items.*.unit_price'  => 'required|numeric|min:0',
        ];

        if ($withStatus) {
            $rules['status'] = 'required|in:EMITIDA,CONFIRMADA,FINALIZADA,CANCELADO';
        }

        $request->validate($rules);
    }

    private function syncItems(RequisicaoMaterial $req, array $items): void
    {
        foreach ($items as $item) {
            $req->items()->create([
                'description' => $item['description'],
                'quantity'    => $item['quantity'],
                'unit'        => $item['unit'],
                'unit_price'  => $item['unit_price'],
                'subtotal'    => round($item['quantity'] * $item['unit_price'], 2),
            ]);
        }
    }
}
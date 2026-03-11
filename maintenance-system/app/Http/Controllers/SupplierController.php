<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::orderBy('name')->get();
        return view('suppliers.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:suppliers,code',
            'name' => 'required|string|max:255',
            'nuit' => 'nullable|string',
        ]);

        // Processar Metadados Dinâmicos
        $metadata = [];
        if ($request->has('meta_key')) {
            foreach ($request->meta_key as $index => $key) {
                if (!empty($key)) {
                    $metadata[$key] = $request->meta_value[$index] ?? '';
                }
            }
        }

        Supplier::create([
            'code' => $request->code,
            'name' => $request->name,
            'address' => $request->address,
            'nuit' => $request->nuit,
            'contact' => $request->contact,
            'email' => $request->email,
            'metadata' => $metadata
        ]);

        return redirect()->route('suppliers.index')->with('success', 'Fornecedor cadastrado com sucesso!');
    }

  public function destroy(Supplier $supplier)
{
    try {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('deleted', true);
    } catch (\Illuminate\Database\QueryException $e) {
        if ($e->getCode() === '23000') {
            return redirect()->route('suppliers.index')
                ->with('error', "Não é possível eliminar \"{$supplier->name}\" porque tem registos associados.");
        }
        throw $e;
    }
}


    public function update(Request $request, Supplier $supplier)
{
    $request->validate([
        'code' => 'required|unique:suppliers,code,' . $supplier->id,
        'name' => 'required|string|max:255',
    ]);

    $metadata = [];
    if ($request->has('meta_key')) {
        foreach ($request->meta_key as $index => $key) {
            if (!empty($key)) {
                $metadata[$key] = $request->meta_value[$index] ?? '';
            }
        }
    }

    $supplier->update(array_merge($request->all(), ['metadata' => $metadata]));

    return redirect()->route('suppliers.index')->with('success', 'Fornecedor atualizado!');
}
}
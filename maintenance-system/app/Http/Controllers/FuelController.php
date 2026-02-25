<?php

namespace App\Http\Controllers;

use App\Models\FuelLog;
use Illuminate\Http\Request;

class FuelController extends Controller
{
   public function index()
{
    $capacidadeTotal = 20000;

    // 1. Cálculos do Tanque (Soma direta)
    $totalEntradas = \App\Models\FuelEntry::sum('quantity') ?: 0;
    $totalSaidas = \App\Models\FuelLog::sum('quantity') ?: 0;
    $restante = $totalEntradas - $totalSaidas;
    
    $percentagem = ($capacidadeTotal > 0) ? ($restante / $capacidadeTotal) * 100 : 0;
    $percentagem = max(0, min(100, $percentagem));

    // 2. Criar a lista unificada manualmente
    // IMPORTANTE: Adicionei start_counter e end_counter aqui
    $saidas = \App\Models\FuelLog::select(
            'date', 
            'plate as ident', 
            'company', 
            'quantity', 
            'driver as responsavel', 
            'start_counter', 
            'end_counter'
        )
        ->selectRaw("'SAÍDA' as tipo, created_at")
        ->get();

    // Para as entradas, como não têm contadores, enviamos como null ou 0
    $entradas = \App\Models\FuelEntry::select(
            'date', 
            'supplier as ident', 
            \DB::raw("'' as company"), 
            'quantity', 
            \DB::raw("'' as responsavel"),
            \DB::raw("NULL as start_counter"), 
            \DB::raw("NULL as end_counter")
        )
        ->selectRaw("'ENTRADA' as tipo, created_at")
        ->get();

    // Juntamos e ordenamos
    $historico = $saidas->concat($entradas)
        ->sortByDesc('created_at')
        ->take(30); // Aumentei para 30 para o DataTable ter mais dados

    return view('fuel.index', compact('restante', 'capacidadeTotal', 'percentagem', 'historico'));
}

    // Nova função para salvar a entrada de combustível
// public function storeEntry(Request $request)
// {
//     $request->validate([
//         'date' => 'required|date',
//         'quantity' => 'required|numeric|min:1',
//     ]);

//     // Em vez de $request->all(), use only() para pegar só o necessário
//     \App\Models\FuelEntry::create($request->only(['date', 'quantity', 'supplier', 'invoice_no']));

//     return back()->with('success', 'Tanque reabastecido com sucesso!');
// }

    // Para Registrar Saída (Abastecimento de Viatura)
public function store(Request $request)
{
    // Remova o comentário da linha abaixo para testar se os dados chegam ao clicar no botão
    // dd($request->all()); 

    $request->validate([
        'date' => 'required|date',
        'plate' => 'required',
        'start_counter' => 'required|numeric',
        'end_counter' => 'required|numeric',
    ]);

    $quantidade = $request->end_counter - $request->start_counter;

    \App\Models\FuelLog::create([
        'date' => $request->date,
        'plate' => $request->plate,
        'company' => $request->company,
        'start_counter' => $request->start_counter,
        'end_counter' => $request->end_counter,
        'quantity' => $quantidade,
        'operator' => $request->operator,
        'driver' => $request->driver,
    ]);

    return redirect()->back()->with('success', 'Abastecimento registado!');
}

// Para Registrar Entrada (Atestar Tanque)
public function storeEntry(Request $request)
{
    // 1. Validação estrita
    $validated = $request->validate([
        'date'     => 'required|date',
        'quantity' => 'required|numeric|min:1',
        'supplier' => 'nullable|string',
    ]);

    // 2. Gravação direta usando os dados validados
    try {
        \App\Models\FuelEntry::create($validated);
        return redirect()->back()->with('success', 'Stock atualizado com sucesso!');
    } catch (\Exception $e) {
        // Se der erro de base de dados, isto vai mostrar o que é
        return redirect()->back()->with('error', 'Erro ao salvar: ' . $e->getMessage());
    }
}
}
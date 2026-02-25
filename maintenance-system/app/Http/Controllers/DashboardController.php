<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Machine;
use App\Models\StockItem;
use App\Models\Maintenance;
use App\Models\MaterialPurchase; // Confirma se o nome do model é este
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
{

 
    // Dados para os Cards
    $totalMachines = \App\Models\Machine::count();
    $activeMaintenances = \App\Models\Maintenance::where('status', 'em_aberto')->count();
    $lowStock = \App\Models\StockItem::where('quantity', '<=', 5)->count();

    // Dados para a Tabela de Atividades
    $activities = \App\Models\Activity::latest()->take(8)->get();



    // KPIs Existentes
    $totalMaquinas = \App\Models\Machine::count();
    $maquinasParadas = \App\Models\Machine::where('estado', 'Avariada')->count();
    $stockCritico = \App\Models\StockItem::where('quantidade', '<=', 5)->count();
    $comprasPendentes = \App\Models\MaterialPurchase::where('status', 'Pendente')->count();

    // 1. Lógica para as "Últimas Intervenções"
    // Usamos o 'with' para carregar a máquina e evitar erros de consulta
    $ultimasManutencoes = \App\Models\Maintenance::with('machine')
        ->latest() // Ordena pelas mais recentes
        ->take(5)  // Pega apenas as últimas 5
        ->get();

    // 2. Gráfico de Manutenções (Últimos 6 meses)
   $meses = [];
$contagemManutencoes = [];

// Loop para os últimos 12 meses
for ($i = 12; $i >= 0; $i--) {
    $dataReferencia = now()->subMonths($i);
    
    // 1. Adiciona o nome do mês traduzido para o Eixo X
    $meses[] = $dataReferencia->translatedFormat('M');

    // 2. Conta os registos baseados na 'data_entrada'
    // Importante: Usamos 'data_entrada' em vez de 'created_at'
    $contagemManutencoes[] = \App\Models\Maintenance::whereMonth('data_entrada', $dataReferencia->month)
        ->whereYear('data_entrada', $dataReferencia->year)
        ->count();
}

    // 3. Gráfico de Rosca: Stock por Artigo/Peça (Real)
$dadosStock = StockItem::select('nome', DB::raw('sum(quantidade) as total'))
    ->where('quantidade', '>', 0) // Apenas artigos com stock
    ->groupBy('nome')
    ->orderBy('total', 'desc')
    ->take(10) // Limitamos aos 10 principais para o gráfico não ficar ilegível
    ->get();

        $itensCriticos = StockItem::where('quantidade', '<=', 5)
    ->orderBy('quantidade', 'asc')
    ->take(5) // Mostra apenas os 5 mais urgentes na dashboard
    ->get();

    return view('dashboard', compact(
        'totalMaquinas', 
        'maquinasParadas', 
        'stockCritico', 
        'comprasPendentes',
        'ultimasManutencoes', // Variável essencial para a sua tabela
        'meses', 
        'contagemManutencoes',
        'dadosStock',
        'itensCriticos',
         'totalMachines', 'activeMaintenances', 'lowStock', 'activities'



        
    ));

    
}
}
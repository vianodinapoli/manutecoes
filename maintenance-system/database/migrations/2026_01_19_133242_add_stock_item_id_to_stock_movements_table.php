<?php

namespace App\Http\Controllers;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\StockItem;
use Illuminate\Http\Request;


class StockItemController extends Controller
{
    public function index()
    {
        // Pega todos os itens para que a pesquisa geral funcione em tudo
        $stockItems = StockItem::all();
        return view('stock_items.index', compact('stockItems'));
    }

    public function destroy(StockItem $stockItem)
    {
        // O delete agora funcionará porque a Migration foi corrigida
        $stockItem->delete();

        return redirect()->route('stock-items.index')
                         ->with('success', 'Artigo eliminado com sucesso.');
    }
    
    // ... outros métodos (store, edit, update)
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StockItem;

class StockController extends Controller
{
    public function index()
    {
        return response()->json(StockItem::all());
    }

    public function show($id)
    {
        $item = StockItem::findOrFail($id);
        return response()->json($item);
    }
}

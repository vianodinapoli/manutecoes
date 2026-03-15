<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Requisition;

class RequisitionController extends Controller
{
    public function index()
    {
        return response()->json(
            Requisition::with(['supplier', 'items'])->get()
        );
    }

    public function show($id)
    {
        $requisition = Requisition::with(['supplier', 'items'])->findOrFail($id);
        return response()->json($requisition);
    }
}

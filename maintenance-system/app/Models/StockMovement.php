<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = ['maintenance_id', 'machine_id', 'stock_item_id', 'quantity'];

public function stockItem() {
    return $this->belongsTo(StockItem::class, 'stock_item_id');
}
}
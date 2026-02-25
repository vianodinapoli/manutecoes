<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialPurchaseItem extends Model
{
    // Importante: definir a tabela se não seguires o padrão plural
    protected $table = 'material_purchase_items';

    protected $fillable = [
        'material_purchase_id', 
        'item_name', 
        'quantity', 
        'destino', 
        'price'
    ];

    public function purchase()
    {
        return $this->belongsTo(MaterialPurchase::class, 'material_purchase_id');
    }
}
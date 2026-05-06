<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequisicaoMaterialItem extends Model
{
    protected $fillable = [
        'requisicao_material_id', 'description',
        'quantity', 'unit', 'unit_price', 'subtotal', 
    ];
}
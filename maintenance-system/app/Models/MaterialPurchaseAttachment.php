<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialPurchaseAttachment extends Model
{
    protected $table = 'material_purchase_attachments';

    protected $fillable = [
        'material_purchase_id', 
        'file_path', 
        'file_name'
    ];

    public function purchase()
    {
        return $this->belongsTo(MaterialPurchase::class, 'material_purchase_id');
    }
}
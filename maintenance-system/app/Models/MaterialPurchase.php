<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialPurchase extends Model
{
    protected $fillable = [
    'item_name', 'quantity', 'price', 'status', 'quotation_file', 'metadata', 'user_id'
];

protected $casts = [
    'metadata' => 'array',
];


// ... dentro da classe
public function user()
{
    return $this->belongsTo(\App\Models\User::class, 'user_id');
}
}

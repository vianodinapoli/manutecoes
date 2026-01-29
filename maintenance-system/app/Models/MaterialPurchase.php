<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialPurchase extends Model
{
    use HasFactory;

    // Nome da tabela definido na sua migration
    protected $table = 'material_purchases';

    protected $fillable = [
        'item_name',
        'quantity',
        'price',
        'status',
        'quotation_file',
        'metadata',
        'user_id' // Assumindo que você tem relação com usuário
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
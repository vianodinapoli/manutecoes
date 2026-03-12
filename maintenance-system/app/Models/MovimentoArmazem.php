<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimentoArmazem extends Model
{
    protected $table = 'movimentos_armazem';

    protected $fillable = [
        'stock_item_id',
        'tipo',
        'quantidade',
        'responsavel',
        'observacoes',
        'user_id',
    ];

    protected $casts = [
        'quantidade' => 'decimal:2',
    ];

    public function stockItem(): BelongsTo
    {
        return $this->belongsTo(StockItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
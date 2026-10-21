<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DischargeItem extends Model
{
    protected $fillable = ['discharge_id', 'operation_item_id', 'quantidade', 'quantidade_confirmada'];

    protected $casts = [
        'quantidade'            => 'float',
        'quantidade_confirmada' => 'float',
    ];

    public function discharge(): BelongsTo
    {
        return $this->belongsTo(Discharge::class);
    }

    public function operationItem(): BelongsTo
    {
        return $this->belongsTo(DischargeOperationItem::class, 'operation_item_id');
    }
}
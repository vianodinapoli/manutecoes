<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CartaNova extends Model
{
    protected $table = 'cartas_novas';

    protected $fillable = [
        'operation_id', 'do_numero', 'ficheiro', 'total_camioes', 'uploaded_by',
    ];

    public function operation(): BelongsTo
    {
        return $this->belongsTo(DischargeOperation::class, 'operation_id');
    }

    public function camioes(): HasMany
    {
        return $this->hasMany(CartaNovaCamiao::class, 'carta_nova_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}

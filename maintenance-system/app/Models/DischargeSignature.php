<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DischargeSignature extends Model
{
    protected $fillable = ['discharge_id', 'nome', 'imagem', 'assinado_por'];

    public function discharge(): BelongsTo
    {
        return $this->belongsTo(Discharge::class);
    }

    public function registadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assinado_por');
    }
}
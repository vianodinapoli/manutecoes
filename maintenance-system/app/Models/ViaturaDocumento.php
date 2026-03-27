<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ViaturaDocumento extends Model
{
    protected $fillable = [
        'viatura_id',
        'nome',
        'path',
        'mime',
        'tamanho',
    ];

    public function viatura(): BelongsTo
    {
        return $this->belongsTo(Viatura::class);
    }
}
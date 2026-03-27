<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Viatura extends Model
{
    protected $fillable = [
        'marca',
        'modelo',
        'matricula',
        'numero_chassi',
        'cor',
        'seguro',
        'ipo',
        'observacoes',
        'metadata',
    ];

    protected $casts = [
        'seguro'   => 'date',
        'ipo'      => 'date',
        'metadata' => 'array', // JSON cast automático
    ];

    // Relação com documentos
    public function documentos(): HasMany
    {
        return $this->hasMany(ViaturaDocumento::class);
    }
}
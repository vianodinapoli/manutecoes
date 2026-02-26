<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tank extends Model
{
    protected $fillable = ['nome', 'capacidade', 'stock_atual', ''];

    // Garante que os números venham sempre como decimais para o PHP
    protected $casts = [
        'capacidade' => 'double',
        'stock_atual' => 'double',
    ];

    /**
     * Accessor para a percentagem de ocupação.
     * Uso na View: {{ $tanque->percentagem }}
     */
    public function getPercentagemAttribute() 
    {
        return $this->capacidade > 0 ? ($this->stock_atual / $this->capacidade) * 100 : 0;
    }

    /**
     * Relação com as saídas (abastecimentos de viaturas)
     */
    public function logs(): HasMany
    {
        return $this->hasMany(FuelLog::class);
    }

    /**
     * Relação com as entradas (cisternas)
     */
    public function entries(): HasMany
    {
        return $this->hasMany(FuelEntry::class);
    }
}
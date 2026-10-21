<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DischargeOperationItem extends Model
{
    protected $fillable = ['operation_id', 'nome', 'unidade', 'peso_unitario_kg', 'quantidade'];

    protected $casts = [
        'peso_unitario_kg' => 'float',
        'quantidade'       => 'float',
    ];

    public function operation(): BelongsTo
    {
        return $this->belongsTo(DischargeOperation::class, 'operation_id');
    }

    public function dischargeItems(): HasMany
    {
        return $this->hasMany(DischargeItem::class, 'operation_item_id');
    }

    /** Peso esperado deste produto, em kg. */
    public function getPesoEsperadoKgAttribute(): float
    {
        $kgPorUnidade = $this->unidade === 'tonelada' ? 1000 : $this->peso_unitario_kg;
        return round($this->quantidade * $kgPorUnidade, 3);
    }

    /** Saldo por lançar no porto (usa withSum(... as registado) se estiver carregado). */
    public function getSaldoAttribute(): float
    {
        $registado = $this->registado ?? $this->dischargeItems()->sum('quantidade');
        return round($this->quantidade - (float) $registado, 3);
    }

    public function getUnidadeLabelAttribute(): string
    {
        return $this->unidade === 'tonelada' ? 't' : 'sacos';
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Discharge extends Model
{
    protected $table = 'descargas';

    protected $fillable = [
        'operation_id',
        'data', 'motorista', 'matricula', 'transportadora',
        'numero_guia', 'hora_saida_porto', 'registado_por',
        // colunas legado (preenchidas apenas para produtos "Alta" / "Baixa")
        'sacos_alta', 'peso_saco_alta', 'total_alta',
        'sacos_baixa', 'peso_saco_baixa', 'total_baixa',
        'total_sacos', 'peso_total_estimado',
        'hora_chegada_balanca', 'tempo_transporte',
        'peso_bruto', 'tara', 'peso_liquido',
        'sacos_confirmados', 'observacoes', 'confirmado_por', 'status',
    ];

    protected $casts = [
        'data' => 'date',
    ];

    public static array $statusLabels = [
        'in_transit' => 'Em Trânsito',
        'confirmed'  => 'Descarregado',
        'pending'    => 'Pendente',
    ];

    public function getStatusLabelAttribute(): string
    {
        return self::$statusLabels[$this->status] ?? $this->status;
    }

    public function calculateTransportTime(): void
    {
        if ($this->hora_saida_porto && $this->hora_chegada_balanca) {
            $departure = \Carbon\Carbon::createFromTimeString($this->hora_saida_porto);
            $arrival   = \Carbon\Carbon::createFromTimeString($this->hora_chegada_balanca);
            $this->tempo_transporte = $departure->diffInMinutes($arrival);
        }
    }

    /** Divergência: algum produto confirmado na balança difere do registado no porto. */
    public function hasDivergence(): bool
    {
        if ($this->status !== 'confirmed') {
            return false;
        }

        if ($this->items->isNotEmpty()) {
            return $this->items->contains(
                fn ($i) => $i->quantidade_confirmada !== null
                    && abs($i->quantidade_confirmada - $i->quantidade) > 0.0005
            );
        }

        // Registos sem itens (legado)
        return $this->sacos_confirmados && (int) $this->total_sacos !== (int) $this->sacos_confirmados;
    }

    /* ── Relações ── */
    public function operation(): BelongsTo
    {
        return $this->belongsTo(DischargeOperation::class, 'operation_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(DischargeItem::class, 'discharge_id');
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registado_por');
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmado_por');
    }
}
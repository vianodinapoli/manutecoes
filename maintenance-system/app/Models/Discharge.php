<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Discharge extends Model
{
    protected $table = 'descargas';

    protected $fillable = [
        'data', 'motorista', 'matricula', 'transportadora',
        'numero_guia', 'hora_saida_porto', 'registado_por',
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

    public function hasDivergence(): bool
    {
        if (!$this->sacos_confirmados) return false;
        return $this->total_sacos !== $this->sacos_confirmados;
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
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Discharge extends Model
{
    protected $table = 'descargas';

    protected $fillable = [
        'operation_id',
        'data', 'motorista', 'matricula', 'transportadora',
        'numero_guia', 'hora_saida_porto', 'registado_por',
        'tipo_atrelado',
        // colunas legado (preenchidas apenas para produtos "Alta" / "Baixa")
        'sacos_alta', 'peso_saco_alta', 'total_alta',
        'sacos_baixa', 'peso_saco_baixa', 'total_baixa',
        'total_sacos', 'peso_total_estimado',
        'hora_chegada_balanca', 'tempo_transporte',
        'peso_bruto', 'peso_bruto_em', 'tara', 'tara_em', 'peso_liquido',
        'sacos_confirmados', 'observacoes', 'confirmado_por', 'status',
    ];

    protected $casts = [
        'data'          => 'date',
        'peso_bruto_em' => 'datetime',
        'tara_em'       => 'datetime',
    ];

    public static array $statusLabels = [
        'in_transit' => 'Em Trânsito',
        'weighing'   => 'A pesar',
        'confirmed'  => 'Descarregado',
        'pending'    => 'Pendente',
    ];

    public function getStatusLabelAttribute(): string
    {
        return self::$statusLabels[$this->status] ?? $this->status;
    }

    /**
     * Fase da pesagem na balança.
     * O camião chega cheio (bruto) e volta depois vazio (tara), mas os pesos
     * podem ser registados em qualquer ordem.
     *
     *  por_pesar     → nenhum peso registado
     *  aguarda_tara  → já pesou cheio, falta pesar vazio
     *  aguarda_bruto → já pesou vazio, falta pesar cheio
     *  completa      → os dois pesos registados
     */
    public function getFasePesagemAttribute(): string
    {
        return match (true) {
            $this->peso_bruto !== null && $this->tara !== null => 'completa',
            $this->peso_bruto !== null                         => 'aguarda_tara',
            $this->tara !== null                               => 'aguarda_bruto',
            default                                            => 'por_pesar',
        };
    }

    /** Já tem os dois pesos (cheio e vazio). */
    public function pesagemCompleta(): bool
    {
        return $this->peso_bruto !== null && $this->tara !== null;
    }

    public function calculateTransportTime(): void
    {
        if ($this->hora_saida_porto && $this->hora_chegada_balanca) {
            $departure = \Carbon\Carbon::createFromTimeString($this->hora_saida_porto);
            $arrival   = \Carbon\Carbon::createFromTimeString($this->hora_chegada_balanca);
            $this->tempo_transporte = $departure->diffInMinutes($arrival);
        }
    }

    /** Limite de sacos de um atrelado normal. Acima disto é "link". */
    public const LIMITE_ATRELADO_NORMAL = 24;

    /** O tipo escolhido não bate certo com o nº de sacos (aviso, não bloqueia). */
    public function atreladoInconsistente(): bool
    {
        $sacos = (int) $this->total_sacos;

        return ($this->tipo_atrelado === 'normal' && $sacos > self::LIMITE_ATRELADO_NORMAL)
            || ($this->tipo_atrelado === 'link' && $sacos > 0 && $sacos <= self::LIMITE_ATRELADO_NORMAL);
    }

    /**
     * Divergência: algum produto confirmado na balança difere do registado no porto.
     * Visível já a partir da primeira pesagem ("A pesar"), porque as quantidades
     * são confirmadas nessa altura.
     */
    public function hasDivergence(): bool
    {
        if (! in_array($this->status, ['weighing', 'confirmed'], true)) {
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

    public function signature(): HasOne
    {
        return $this->hasOne(DischargeSignature::class, 'discharge_id');
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
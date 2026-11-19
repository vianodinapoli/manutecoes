<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Validation\ValidationException;

class Discharge extends Model
{

    protected $table = 'descargas';

    protected $fillable = [
        'operation_id', 'location_id', 'location_extra',
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
        // 4 tempos do camião (T3)
        'carregado_em', 'carregado_por',
        'saiu_porto_em', 'saiu_porto_por',
        'chegou_balanca_em', 'chegou_balanca_por',
        'saiu_descarga_em', 'saiu_descarga_por',
    ];

    protected $casts = [
        'data'              => 'date',
        'location_extra'    => 'boolean',
        'peso_bruto_em'     => 'datetime',
        'tara_em'           => 'datetime',
        'carregado_em'      => 'datetime',
        'saiu_porto_em'     => 'datetime',
        'chegou_balanca_em' => 'datetime',
        'saiu_descarga_em'  => 'datetime',
    ];

    /**
     * Fluxo: Carregado → Em trânsito → A pesar → Confirmado
     *  loaded     : 1.º agente registou o camião no tablet
     *  in_transit : 2.º agente conferiu e arrastou a linha (saiu do porto)
     *  weighing   : a balança recebeu o 1.º peso
     *  confirmed  : terminou a pesagem do camião vazio
     */
    public static array $statusLabels = [
        'loaded'     => 'Carregado',
        'in_transit' => 'Em trânsito',
        'weighing'   => 'A pesar',
        'confirmed'  => 'Confirmado',
        'pending'    => 'Pendente',
    ];

    /** Ordem obrigatória dos 4 tempos. */
    public const ETAPAS = ['carregado', 'saiu_porto', 'chegou_balanca', 'saiu_descarga'];

    public function getStatusLabelAttribute(): string
    {
        return self::$statusLabels[$this->status] ?? $this->status;
    }

    /**
     * Grava a hora do SERVIDOR e quem marcou. Não deixa saltar etapas
     * e não sobrescreve uma etapa já marcada. Não faz save().
     */
    public function marcarEtapa(string $etapa, ?int $userId): void
    {
        $i = array_search($etapa, self::ETAPAS, true);
        if ($i === false) {
            throw new \InvalidArgumentException("Etapa desconhecida: {$etapa}");
        }

        if ($i > 0 && $this->{self::ETAPAS[$i - 1] . '_em'} === null) {
            throw ValidationException::withMessages([
                'etapa' => 'Não pode saltar etapas: falta "' . self::ETAPAS[$i - 1] . '".',
            ]);
        }

        if ($this->{$etapa . '_em'} !== null) {
            return;
        }

        $this->{$etapa . '_em'}  = now();
        $this->{$etapa . '_por'} = $userId;
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
        // novo: diferença entre os dois tempos reais do servidor
        if ($this->saiu_porto_em && $this->chegou_balanca_em) {
            $this->tempo_transporte = (int) $this->saiu_porto_em->diffInMinutes($this->chegou_balanca_em);
            return;
        }

        // legado: horas H:i escritas à mão
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

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
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

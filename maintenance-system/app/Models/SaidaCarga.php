<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Um camião registado numa saída. 'pendente' = registado, ainda não carregou;
 * 'carregada' = já carregou e a quantidade foi descontada do stock da operação.
 */
class SaidaCarga extends Model
{
    protected $table = 'saidas_carga';

    protected $fillable = [
        'saida_id', 'data', 'empresa', 'nome', 'apelido', 'quantidade', 'quantidade_pedida', 'matricula',
        'tipo_atrelado', 'metadata',
        'status', 'operation_id', 'discharge_id', 'operation_item_id', 'carregado_em',
        'observacoes', 'registado_por', 'carregado_por',
    ];

    protected $casts = [
        'data'              => 'date',
        'metadata'          => 'array',
        'quantidade'        => 'float',
        'quantidade_pedida' => 'float',
        'carregado_em'      => 'datetime',
    ];

    /* ── Scopes ── */
    public function scopePendentes(Builder $q): Builder
    {
        return $q->where('status', 'pendente');
    }

    public function scopeCarregadas(Builder $q): Builder
    {
        return $q->where('status', 'carregada');
    }

    /* ── Relações ── */
    public function saida(): BelongsTo
    {
        return $this->belongsTo(Saida::class, 'saida_id');
    }

    public function operation(): BelongsTo
    {
        return $this->belongsTo(DischargeOperation::class, 'operation_id');
    }

    /** Já não é usada no fluxo novo (saída por operação); fica para registos antigos. */
    public function discharge(): BelongsTo
    {
        return $this->belongsTo(Discharge::class, 'discharge_id');
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registado_por');
    }

    public function carregadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'carregado_por');
    }

    public function getNomeCompletoAttribute(): string
    {
        return trim($this->nome . ' ' . $this->apelido);
    }

    /* ── Stock por operação ── */

    /**
     * Total recebido de um produto numa operação: o confirmado na balança
     * (ou, se ainda não foi pesado, o registado no porto), somando todas as descargas.
     */
    public static function recebidoOperacao(int $operationId, int $itemId): float
    {
        return (float) DB::table('discharge_items')
            ->join('descargas', 'descargas.id', '=', 'discharge_items.discharge_id')
            ->where('descargas.operation_id', $operationId)
            ->where('discharge_items.operation_item_id', $itemId)
            ->sum(DB::raw('COALESCE(discharge_items.quantidade_confirmada, discharge_items.quantidade)'));
    }

    /** Total já saído de um produto numa operação (todos os camiões carregados, de todas as saídas). */
    public static function saiuOperacao(int $operationId, int $itemId): float
    {
        return (float) static::carregadas()
            ->where('operation_id', $operationId)
            ->where('operation_item_id', $itemId)
            ->sum('quantidade');
    }

    /** Disponível em armazém de um produto numa operação: recebido − saído. */
    public static function disponivelOperacao(DischargeOperation $operation, int $itemId): float
    {
        return round(
            static::recebidoOperacao($operation->id, $itemId) - static::saiuOperacao($operation->id, $itemId),
            3
        );
    }

    /**
     * Stock por produto de uma operação: recebido − saído.
     * Devolve uma colecção de linhas [item, recebido, saiu, stock].
     */
    public static function stockOperacao(DischargeOperation $operation): Collection
    {
        $recebido = DB::table('discharge_items')
            ->join('descargas', 'descargas.id', '=', 'discharge_items.discharge_id')
            ->where('descargas.operation_id', $operation->id)
            ->selectRaw('discharge_items.operation_item_id as item_id, SUM(COALESCE(discharge_items.quantidade_confirmada, discharge_items.quantidade)) as total')
            ->groupBy('discharge_items.operation_item_id')
            ->pluck('total', 'item_id');

        $saiu = static::carregadas()
            ->where('operation_id', $operation->id)
            ->selectRaw('operation_item_id, SUM(quantidade) as total')
            ->groupBy('operation_item_id')
            ->pluck('total', 'operation_item_id');

        return $operation->items->map(function ($it) use ($recebido, $saiu) {
            $r = (float) ($recebido[$it->id] ?? 0);
            $s = (float) ($saiu[$it->id] ?? 0);

            return (object) [
                'item'     => $it,
                'recebido' => $r,
                'saiu'     => $s,
                'stock'    => round($r - $s, 3),
            ];
        });
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Uma saída é tratada como uma operação: tem cliente, data, operação de origem
 * (o navio de onde sai o material) e a lista dos camiões registados.
 * Só os camiões registados na saída podem carregar.
 */
class Saida extends Model
{
    protected $table = 'saidas';

    protected $fillable = [
        'codigo', 'cliente', 'data', 'operation_id', 'status',
        'observacoes', 'criado_por', 'fechada_em',
    ];

    protected $casts = [
        'data'       => 'date',
        'fechada_em' => 'datetime',
    ];

    /* ── Relações ── */
    public function operation(): BelongsTo
    {
        return $this->belongsTo(DischargeOperation::class, 'operation_id');
    }

    public function camioes(): HasMany
    {
        return $this->hasMany(SaidaCarga::class, 'saida_id');
    }

    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    public function isFechada(): bool
    {
        return $this->status === 'fechada';
    }

    /* ── Filtros (pesquisa, estado, operação, datas) ── */
    public function scopeFiltrar(Builder $q, array $f): Builder
    {
        return $q
            ->when(! empty($f['q']), function (Builder $q) use ($f) {
                $t = '%' . mb_strtolower(trim($f['q'])) . '%';
                $q->where(function (Builder $w) use ($t) {
                    $w->whereRaw('LOWER(codigo) LIKE ?', [$t])
                      ->orWhereRaw('LOWER(cliente) LIKE ?', [$t])
                      ->orWhereHas('camioes', function (Builder $c) use ($t) {
                          $c->whereRaw('LOWER(matricula) LIKE ?', [$t])
                            ->orWhereRaw('LOWER(nome) LIKE ?', [$t])
                            ->orWhereRaw('LOWER(apelido) LIKE ?', [$t]);
                      });
                });
            })
            ->when(! empty($f['status']), fn (Builder $q) => $q->where('status', $f['status']))
            ->when(! empty($f['operation_id']), fn (Builder $q) => $q->where('operation_id', $f['operation_id']))
            ->when(! empty($f['de']), fn (Builder $q) => $q->whereDate('data', '>=', $f['de']))
            ->when(! empty($f['ate']), fn (Builder $q) => $q->whereDate('data', '<=', $f['ate']));
    }

    /** Código sequencial por ano: SAI-2026-0001. */
    public static function gerarCodigo(): string
    {
        $ano    = now()->format('Y');
        $ultimo = static::where('codigo', 'like', "SAI-{$ano}-%")->orderByDesc('codigo')->value('codigo');
        $n      = $ultimo ? ((int) substr($ultimo, -4)) + 1 : 1;

        return sprintf('SAI-%s-%04d', $ano, $n);
    }
}
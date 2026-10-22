<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DischargeOperation extends Model
{
    // Estados: planned (Planeada) → active (Em curso) → archived (Arquivada)
    protected $fillable = [
        'navio', 'bl', 'factura', 'empresa', 'quantidade_esperada',
        'data_chegada', 'status', 'archived_at', 'archived_by',
        'started_at', 'started_by', 'plan_log',
        'transportadoras',
        'metadata', 'observacoes',
    ];

    protected $casts = [
        'quantidade_esperada' => 'decimal:3',
        'data_chegada'        => 'date',
        'archived_at'         => 'datetime',
        'started_at'          => 'datetime',
        'plan_log'            => 'array',
        'transportadoras'     => 'array',
        'metadata'            => 'array',
    ];

    /* ── Relações ── */
    public function items(): HasMany
    {
        return $this->hasMany(DischargeOperationItem::class, 'operation_id')->orderBy('id');
    }

    public function discharges(): HasMany
    {
        return $this->hasMany(Discharge::class, 'operation_id');
    }

    public function archivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'archived_by');
    }

    public function startedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'started_by');
    }

    /* ── Scopes ── */
    public function scopePlanned($q)  { return $q->where('status', 'planned'); }
    public function scopeActive($q)   { return $q->where('status', 'active'); }
    public function scopeOpen($q)     { return $q->whereIn('status', ['planned', 'active']); }
    public function scopeArchived($q) { return $q->where('status', 'archived'); }

    /* ── Estado ── */
    public function isPlanned(): bool  { return $this->status === 'planned'; }
    public function isActive(): bool   { return $this->status === 'active'; }
    public function isArchived(): bool { return $this->status === 'archived'; }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'planned'  => 'Planeada',
            'active'   => 'Em curso',
            'archived' => 'Arquivada',
            default    => $this->status,
        };
    }

    /** Peso esperado total do plano, em toneladas (soma dos itens). */
    public function calcPesoEsperadoTon(): float
    {
        return round($this->items->sum(fn ($i) => $i->peso_esperado_kg) / 1000, 3);
    }

    /* ── Totais ── */
    /** Peso líquido confirmado (toneladas). */
    public function getRecebidoTonAttribute(): float
    {
        $kg = $this->discharges_sum_peso_liquido
            ?? $this->discharges()->sum('peso_liquido');
        return round(((float) $kg) / 1000, 3);
    }

    public function getFaltaTonAttribute(): float
    {
        return round((float) $this->quantidade_esperada - $this->recebido_ton, 3);
    }

    public function getProgressoAttribute(): float
    {
        $esp = (float) $this->quantidade_esperada;
        return $esp > 0 ? min(100, round($this->recebido_ton / $esp * 100, 1)) : 0;
    }

    /** Nomes (minúsculas) das transportadoras fora da lista prevista. */
    public function transportadorasExtra(): array
    {
        return collect($this->transportadoras ?? [])
            ->filter(fn ($t) => ! empty($t['extra']))
            ->map(fn ($t) => mb_strtolower($t['nome']))
            ->values()
            ->all();
    }

    public function isTransportadoraExtra(?string $nome): bool
    {
        return $nome !== null && in_array(mb_strtolower(trim($nome)), $this->transportadorasExtra(), true);
    }

    /** Dados para o modal de criar/editar (JS: abrirOperacaoModal). */
    public function modalPayload(): array
    {
        return [
            'id'           => $this->id,
            'status'       => $this->status,
            'navio'        => $this->navio,
            'empresa'      => $this->empresa,
            'bl'           => $this->bl,
            'factura'      => $this->factura,
            'observacoes'  => $this->observacoes,
            'data_chegada' => $this->data_chegada?->format('Y-m-d'),
            'metadata'     => $this->metadata ?? [],
            'transportadoras' => $this->transportadoras ?? [],
            'items'        => $this->items->map(fn ($i) => [
                'id'               => $i->id,
                'nome'             => $i->nome,
                'unidade'          => $i->unidade,
                'peso_unitario_kg' => $i->unidade === 'tonelada' ? null : $i->peso_unitario_kg,
                'quantidade'       => $i->quantidade,
            ])->values()->all(),
        ];
    }
}
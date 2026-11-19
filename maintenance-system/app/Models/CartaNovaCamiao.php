<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartaNovaCamiao extends Model
{
    protected $table = 'carta_nova_camioes';

    protected $fillable = [
        'carta_nova_id', 'matricula', 'primeiro_nome', 'ultimo_nome', 'carta_conducao',
    ];

    public function carta(): BelongsTo
    {
        return $this->belongsTo(CartaNova::class, 'carta_nova_id');
    }

    /** "abt 312-mp" → "ABT312MP" */
    public static function normalizarMatricula(?string $matricula): string
    {
        return mb_strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $matricula));
    }

    public function getNomeCompletoAttribute(): string
    {
        return trim($this->primeiro_nome . ' ' . $this->ultimo_nome);
    }
}

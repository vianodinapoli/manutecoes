<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contentor extends Model
{
    protected $table = 'contentores';

    public const ESTADOS = [
        'no_parque'    => 'No parque',
        'em_transito'  => 'Em trânsito',
        'descarregado' => 'Descarregado',
        'carregado'    => 'Carregado',
        'vazio'        => 'Vazio',
        'saiu'         => 'Saiu',
    ];

    public const TIPOS = ['20GP', '40GP', '40HC', '20RF', '40RF', '20OT', '40OT', '20TK'];

    protected $fillable = [
        'numero', 'tipo', 'tamanho', 'estado', 'armador', 'cliente', 'navio',
        'bl', 'selo', 'peso_kg', 'localizacao', 'data_entrada', 'data_saida',
        'observacoes', 'criado_por',
    ];

    protected $casts = [
        'data_entrada' => 'date',
        'data_saida'   => 'date',
        'peso_kg'      => 'decimal:2',
    ];

    public function getDiasNoParqueAttribute(): ?int
    {
        if (! $this->data_entrada) {
            return null;
        }
        $fim = $this->data_saida ?? now();
        return (int) $this->data_entrada->diffInDays($fim);
    }
}

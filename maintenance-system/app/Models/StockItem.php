<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockItem extends Model
{
    use HasFactory;

    /**
     * Os atributos que podem ser atribuídos em massa (mass assignable).
     * Inclui todos os campos da migração, mais a coluna metadata.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'numero_armazem', 
        'seccao_armazem', 
        'referencia', 
        'marca_fabricante', 
        'modelo', 
        'categoria', 
        'sistema_maquina', 
        'estado', 
        'quantidade', 
        'metadata',
    ];

    /**
     * Converte automaticamente a coluna 'metadata' (JSON no DB) para um array/objeto PHP.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'metadata' => 'array',
    ];
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    use HasFactory;
    
   // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'numero_interno',
        'tipo_equipamento',
        'marca',
        'modelo',
        'matricula', 
        'nr_chassi',
        'localizacao',
        'operador',
        'status',
        'observacoes',
    ];
    // Relacionamento: Uma Máquina TEM MUITAS Manutenções
    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }


    
}
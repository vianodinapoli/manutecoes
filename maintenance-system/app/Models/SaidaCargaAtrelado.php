<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaidaCargaAtrelado extends Model
{
    protected $table = 'saida_carga_atrelados';

    protected $fillable = ['saida_carga_id', 'matricula'];
}

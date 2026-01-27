<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    // Adicione esta linha para permitir gravar os dados
    protected $fillable = ['type', 'description', 'user_name', 'status'];
}

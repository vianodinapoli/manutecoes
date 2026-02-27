<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['code', 'name', 'address', 'nuit', 'contact', 'email', 'metadata'];

protected $casts = [
    'metadata' => 'array' // Transforma o JSON automaticamente em array PHP
];
}

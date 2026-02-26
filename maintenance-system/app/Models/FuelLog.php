<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelLog extends Model
{
 // Adicione este array aqui:
    protected $fillable = [
        'tank_id',
        'date',
        'plate',
        'company',
        'start_counter',
        'end_counter',
        'quantity',
        'operator',
        'driver',
    ];
}

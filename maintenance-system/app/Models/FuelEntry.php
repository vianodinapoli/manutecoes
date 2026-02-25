<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelEntry extends Model
{
  protected $fillable = [
        'date',
        'quantity',
        'supplier',
        'invoice_no'
    ];
}

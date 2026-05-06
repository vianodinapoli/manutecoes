<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequisitionItem extends Model
{
    protected $fillable = ['requisition_id', 'description', 'quantity', 'discount', 'unit_price', 'subtotal'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelSettlement extends Model
{
    protected $fillable = [
        'company',
        'quantity',
        'date',
        'tank_id',
        'notes',
        'created_by',
    ];

    /**
     * Tanque onde o combustível foi devolvido fisicamente.
     */
    public function tank()
    {
        return $this->belongsTo(Tank::class);
    }

    /**
     * Utilizador que registou o acerto.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
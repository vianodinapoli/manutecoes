<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['nome', 'tipo', 'capacidade_ton', 'ativo'];

    protected $casts = ['ativo' => 'boolean'];

    public function operations()
    {
        return $this->belongsToMany(
            DischargeOperation::class,
            'discharge_operation_location',
            'location_id',
            'discharge_operation_id'
        )->withPivot('principal');
    }

    public function discharges()
    {
        return $this->hasMany(Discharge::class);
    }
}

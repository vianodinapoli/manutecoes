<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    use HasFactory;
    
    // Indica quais campos podem ser preenchidos em massa
    protected $fillable = ['name', 'description', 'location', 'serial_number'];

    // Relacionamento: Uma Máquina TEM MUITAS Manutenções
    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }
}
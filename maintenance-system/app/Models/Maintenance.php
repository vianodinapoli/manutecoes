<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;
    
    // Indica quais campos podem ser preenchidos em massa
    protected $fillable = [
        'machine_id', 'failure_description', 'status', 'scheduled_date', 
        'start_date', 'end_date', 'technician_notes'
    ];

    // Relacionamento: Uma Manutenção PERTENCE A UMA Máquina
    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
}
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


    // * Define os campos que devem ser convertidos em tipos nativos.*/
    protected $casts = [
        'scheduled_date' => 'datetime', // Adicione esta linha
        'start_date' => 'datetime',     // Adicione esta linha
        'end_date' => 'datetime',       // Adicione esta linha
    ];
}
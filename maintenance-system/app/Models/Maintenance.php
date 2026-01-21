<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute; // <<< É NECESSÁRIO!

class Maintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        // ... (Seu array fillable completo)
        'machine_id',
        'work_sheet_ref',
        'hours_kms',
        'failure_description',
        'status',
        'technician_notes',
        'total_cost',
        'end_date',
        // NOVOS CAMPOS
        'nome_motorista',
        'data_entrada',
        'horas_trabalho',
        'scheduled_date',
        'start_date',
    ];

    protected $casts = [
        'end_date' => 'datetime',
        'scheduled_date' => 'datetime', // Mantém o cast para leitura
        'start_date' => 'datetime',
        'total_cost' => 'decimal:2',
        'data_entrada' => 'date', 
        'horas_trabalho' => 'decimal:2',
    ];

    /**
     * Define como o scheduled_date deve ser gravado/atualizado. (Método moderno)
     * Isto garante que o formato do input datetime-local é convertido para o formato SQL.
     */
    protected function scheduledDate(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => $value ? date('Y-m-d H:i:s', strtotime($value)) : null,
        );
    }
    
    // O método setScheduledDateAttribute FOI REMOVIDO AQUI!

    public function machine()
    {
        return $this->belongsTo(Machine::class, 'machine_id');
    }

    

    public function files()
    {
        return $this->hasMany(\App\Models\MaintenanceFile::class); 
    }

    public function movements() {
    return $this->hasMany(StockMovement::class, 'maintenance_id');
}
}
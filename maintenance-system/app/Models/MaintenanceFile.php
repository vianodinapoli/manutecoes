<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MaintenanceFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_id', 
        'filename', 
        'filepath', 
        'mime_type', 
        'filesize'
    ];

    /**
     * Relação com a Manutenção.
     */
    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class);
    }
    
    /**
     * Acessório para obter o URL público do ficheiro.
     */
    public function getUrlAttribute()
    {
        // Usa o disco 'public' para criar o URL
        return Storage::disk('public')->url($this->filepath);
    }
}
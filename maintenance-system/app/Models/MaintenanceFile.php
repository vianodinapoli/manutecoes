<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MaintenanceFile extends Model
{
    protected $fillable = ['maintenance_id', 'filename', 'filepath', 'mime_type', 'filesize'];

    // Este "Accessor" cria a propriedade $file->url automaticamente
    public function getUrlAttribute()
    {
        // Se o filepath já começa com http, retorna direto, senão gera a URL do storage
        return asset('storage/' . $this->filepath);
    }
}
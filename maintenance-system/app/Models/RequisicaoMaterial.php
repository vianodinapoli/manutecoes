<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequisicaoMaterial extends Model
{
    protected $table = 'requisicoes_material';

    protected $fillable = [
        'date', 'destino', 'supplier_id', 'matricula', 'motorista',
        'responsavel', 'observacoes', 'status', 'created_by', 'peso_confirmado',
    'valor_carga', 'numero_guia','local_descarga',
    ];

    protected $casts = ['date' => 'date'];

    public function items(): HasMany
    {
        return $this->hasMany(RequisicaoMaterialItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function getTotalFinalAttribute(): float
    {
        return $this->items->sum('subtotal');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
    use Barryvdh\DomPDF\Facade\Pdf;

class Requisition extends Model
{
    protected $fillable = [
        'supplier_id', 
        'date', 
        'total_liquid', 
        'tax_amount', 
        'total_final', 
        'has_tax', 
        'status',
        'pdf_path' 
    ];
    protected $casts = [
    'date' => 'datetime',
    'has_tax' => 'boolean',
];

    // ESTA É A PARTE QUE FALTA:
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    // Aproveite e verifique se já tem a relação dos itens aqui também:
    public function items(): HasMany
    {
        return $this->hasMany(RequisitionItem::class);
    }




}
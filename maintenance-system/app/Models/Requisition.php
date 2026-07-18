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
        'numero_cotacao',
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

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(RequisitionItem::class);
    }
}
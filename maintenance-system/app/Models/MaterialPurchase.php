<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialPurchase extends Model
{
    use HasFactory;

    protected $table = 'material_purchases';

    // Removemos item_name e quantity daqui, pois agora eles ficam na tabela de ITENS
    protected $fillable = [
        'user_id',
        'fornecedor',
        'urgencia',
        'description',
        'status',
    ];

    // Relacionamento com o Usuário que criou a requisição
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com os Itens (Artigos) da Compra
     * Permite fazer: $purchase->items
     */
    public function items()
    {
        return $this->hasMany(MaterialPurchaseItem::class);
    }

    /**
     * Relacionamento com os Anexos (Fotos/PDFs)
     * Permite fazer: $purchase->attachments
     */
    public function attachments()
    {
        return $this->hasMany(MaterialPurchaseAttachment::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'invoice_item_id';
    protected $fillable = [
        'invoice_id', 'inventory_id', 'qty', 'rate',
        'discount_percent', 'discount', 'tax_percent',
        'tax', 'amount'
    ];
    protected $casts = [
        'rate' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'tax' => 'decimal:2',
        'amount' => 'decimal:2'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'invoice_id');
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id', 'inventory_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'order_item_id';
    protected $fillable = [
        'order_id', 'inventory_id', 'product_name',
        'quantity', 'price', 'tax', 'discount', 'total',
        'options', 'status'
    ];
    protected $casts = [
        'price' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'options' => 'json'
    ];

    public function order()
    {
        return $this->belongsTo(UserOrder::class, 'order_id', 'order_id');
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id', 'inventory_id');
    }
}

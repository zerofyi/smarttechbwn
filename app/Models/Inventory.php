<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'inventory_id';
    protected $fillable = [
        'user_id', 'shop_id', 'product_id', 'product_name',
        'product_type', 'health', 'age', 'warranty', 'activation_date', 'battery_health', 'color',
        'notes', 'imei_one', 'imei_two', 'serial_number',
        'unit', 'stock', 'purchase_rate', 'gst_per', 'sell_rate'
    ];
    protected $casts = [
        'purchase_rate' => 'decimal:2',
        'gst_per' => 'decimal:2',
        'sell_rate' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'shop_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class, 'inventory_id', 'inventory_id');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'inventory_id', 'inventory_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'inventory_id', 'inventory_id');
    }
}

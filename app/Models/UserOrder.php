<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'order_id';
    protected $fillable = [
        'order_number', 'user_id', 'address_id', 'invoice_id',
        'cart_id', 'subtotal', 'tax', 'shipping', 'discount',
        'total', 'payment_status', 'payment_method', 'transaction_id',
        'status', 'confirmed_at', 'shipped_at', 'delivered_at',
        'cancelled_at', 'notes'
    ];
    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function address()
    {
        return $this->belongsTo(UserAddress::class, 'address_id', 'address_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'invoice_id');
    }

    public function cart()
    {
        return $this->belongsTo(UserCart::class, 'cart_id', 'cart_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'transaction_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    public function updates()
    {
        return $this->hasMany(OrderUpdate::class, 'order_id', 'order_id');
    }
}

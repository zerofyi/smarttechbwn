<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CartItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'cart_item_id';

    protected $fillable = [
        'cart_id',
        'inventory_id',
        'qty',
        'rate',
        'amount',
    ];

    protected $casts = [
        'qty' => 'integer',
        'rate' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function cart()
    {
        return $this->belongsTo(UserCart::class, 'cart_id', 'cart_id');
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id', 'inventory_id');
    }
}

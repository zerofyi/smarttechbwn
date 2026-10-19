<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'shop_id';
    protected $fillable = [
        'user_id', 'shop_name', 'shop_gstin', 'shop_address',
        'shop_pin_code', 'shop_location', 'shop_photo'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'shop_id', 'shop_id');
    }

    public function agreements()
    {
        return $this->hasMany(Agreement::class, 'shop_id', 'shop_id');
    }
}

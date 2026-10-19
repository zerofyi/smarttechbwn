<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAddress extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'address_id';
    protected $fillable = [
        'user_id', 'address_type', 'contact_person',
        'contact_number', 'address_line_1', 'address_line_2',
        'landmark', 'city', 'state', 'country', 'postal_code',
        'latitude', 'longitude'
    ];
    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(UserOrder::class, 'address_id', 'address_id');
    }
}

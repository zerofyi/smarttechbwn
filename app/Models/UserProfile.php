<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'profile_id';
    protected $fillable = [
        'user_id', 'profile_picture', 'date_of_birth',
        'gender', 'alternate_mobile', 'whatsapp_number',
        'default_address_id'
    ];
    protected $casts = [
        'date_of_birth' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function defaultAddress()
    {
        return $this->belongsTo(UserAddress::class, 'default_address_id', 'address_id');
    }
}

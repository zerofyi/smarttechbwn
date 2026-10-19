<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $primaryKey = 'user_id';
    protected $fillable = [
        'name', 'email', 'mobile', 'password', 'role', 'status',
        'parent_id', 'email_verified_at', 'last_login_at'
    ];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->hasOne(Customer::class, 'user_id', 'user_id');
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id', 'user_id');
    }

    public function children()
    {
        return $this->hasMany(User::class, 'parent_id', 'user_id');
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'user_id');
    }

    public function addresses()
    {
        return $this->hasMany(UserAddress::class, 'user_id', 'user_id');
    }

    public function shop()
    {
        return $this->hasOne(Shop::class, 'user_id', 'user_id');
    }

    public function parties()
    {
        return $this->hasMany(Party::class, 'user_id', 'user_id');
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'user_id', 'user_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'user_id', 'user_id');
    }

    public function financers()
    {
        return $this->hasMany(Financer::class, 'user_id', 'user_id');
    }

    public function cart()
    {
        return $this->hasOne(UserCart::class, 'user_id', 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(UserOrder::class, 'user_id', 'user_id');
    }

    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}

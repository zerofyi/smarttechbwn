<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agreement extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'agreement_id';
    protected $fillable = [
        'shop_id', 'financer_id', 'customer_id', 'type',
        'photo', 'doc'
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'shop_id');
    }

    public function financer()
    {
        return $this->belongsTo(Financer::class, 'financer_id', 'financer_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'agreement_id', 'agreement_id');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, 'agreement_id', 'agreement_id');
    }
}

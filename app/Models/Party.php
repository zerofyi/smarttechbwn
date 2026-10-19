<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Party extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'party_id';
    protected $fillable = [
        'user_id', 'name', 'phone', 'email', 'address',
        'state', 'gstin', 'pan', 'bank', 'ifsc', 'ac_no'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function purchaseInvoices()
    {
        return $this->hasMany(Invoice::class, 'party_id', 'party_id')
                   ->where('invoice_type', 'purchase')
                   ->where('invoice_for', 'party');
    }

    public function saleInvoices()
    {
        return $this->hasMany(Invoice::class, 'party_id', 'party_id')
                   ->where('invoice_type', 'sale')
                   ->where('invoice_for', 'party');
    }
}

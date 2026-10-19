<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'transaction_id';
    protected $fillable = [
        'trz_number', 'trz_amount', 'trz_date', 'method',
        'trz_id', 'ref_id', 'status', 'remarks', 'response'
    ];
    protected $casts = [
        'trz_amount' => 'decimal:2',
        'trz_date' => 'date',
        'response' => 'array'
    ];

    public function order()
    {
        return $this->hasOne(UserOrder::class, 'transaction_id', 'transaction_id');
    }

    public function emis()
    {
        return $this->hasMany(Emi::class, 'transaction_id', 'transaction_id');
    }
}

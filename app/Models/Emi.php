<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Emi extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'emi_id';
    protected $fillable = [
        'loan_id', 'emi_no', 'emi_amount', 'emi_date',
        'paid_date', 'paid_amount', 'due_amount', 'method',
        'transaction_id'
    ];
    protected $casts = [
        'emi_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'emi_date' => 'date',
        'paid_date' => 'date'
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class, 'loan_id', 'loan_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'transaction_id');
    }
}

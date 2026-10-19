<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'loan_id';
    protected $fillable = [
        'financer_id', 'agreement_id', 'invoice_id', 'customer_id',
        'loan_no', 'loan_slab', 'invoice_amount', 'down_payment',
        'loan_amount', 'processing_charge', 'file_charge',
        'payable_amount', 'paid_amount', 'settled_amount', 'emi', 'emi_amount', 'loan_date',
        'first_emi', 'last_emi', 'loan_status', 'name',
        'mobile', 'alternative_mobile', 'address', 'aadhaar_no',
        'pan_no', 'photo', 'doc_one', 'doc_two', 'doc_three'
    ];
    protected $casts = [
        'invoice_amount' => 'decimal:2',
        'down_payment' => 'decimal:2',
        'loan_amount' => 'decimal:2',
        'processing_charge' => 'decimal:2',
        'file_charge' => 'decimal:2',
        'payable_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'settled_amount' => 'decimal:2',
        'emi_amount' => 'decimal:2',
        'loan_date' => 'date',
        'first_emi' => 'date',
        'last_emi' => 'date'
    ];

    public function financer()
    {
        return $this->belongsTo(Financer::class, 'financer_id', 'financer_id');
    }

    public function agreement()
    {
        return $this->belongsTo(Agreement::class, 'agreement_id', 'agreement_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'invoice_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function emis()
    {
        return $this->hasMany(Emi::class, 'loan_id', 'loan_id');
    }
}

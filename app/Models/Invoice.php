<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'invoice_id';
    protected $fillable = [
        'distributor_id', 'user_id', 'party_id', 'customer_id',
        'agreement_id', 'employee_id', 'invoice_no', 'invoice_url', 'invoice_type',
        'invoice_for', 'name', 'mobile', 'address', 'subtotal',
        'total_discount', 'total_tax', 'shipping_amount', 'total_amount', 'paid', 'due_amount',
        'payment_type', 'invoice_date', 'due_date', 'notes',
        'photo_url', 'doc_url'
    ];
    protected $casts = [
        'subtotal' => 'decimal:2',
        'total_discount' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'invoice_date' => 'date',
        'due_date' => 'date'
    ];

    public function recalculateTotals()
    {
        $subtotal = 0;
        $totalDiscount = 0;
        $totalTax = 0;

        foreach ($this->items as $item) {
            $subtotal += $item->rate * $item->qty;
            $totalDiscount += $item->discount;
            $totalTax += $item->tax;
        }

        $this->subtotal = $subtotal;
        $this->total_discount = $totalDiscount;
        $this->total_tax = $totalTax;
        $this->total_amount = $subtotal - $totalDiscount + $totalTax + $this->shipping_amount;
        $this->due_amount = $this->total_amount - $this->paid;
    }

    public function distributor()
    {
        return $this->belongsTo(User::class, 'distributor_id', 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function party()
    {
        return $this->belongsTo(Party::class, 'party_id', 'party_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function agreement()
    {
        return $this->belongsTo(Agreement::class, 'agreement_id', 'agreement_id');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id', 'invoice_id');
    }

    public function order()
    {
        return $this->hasOne(UserOrder::class, 'invoice_id', 'invoice_id');
    }

    public function loan()
    {
        return $this->hasOne(Loan::class, 'invoice_id', 'invoice_id');
    }
    
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}

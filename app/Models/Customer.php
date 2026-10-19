<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'customer_id';
    protected $fillable = [
        'user_id', 'name', 'mobile', 'aadhaar_no', 'pan_no',
        'address', 'photo', 'doc_one', 'doc_two',
        'doc_three', 'signature'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function agreements()
    {
        return $this->hasMany(Agreement::class, 'customer_id', 'customer_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'customer_id', 'customer_id');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, 'customer_id', 'customer_id');
    }
}

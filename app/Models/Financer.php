<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Financer extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'financer_id';
    protected $fillable = [
        'user_id', 'name', 'mobile', 'id_no', 'photo',
        'doc_one', 'doc_two', 'doc_three', 'signature',
        'address', 'state', 'gstin', 'pan', 'bank',
        'ifsc', 'ac_no'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function agreements()
    {
        return $this->hasMany(Agreement::class, 'financer_id', 'financer_id');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, 'financer_id', 'financer_id');
    }
}

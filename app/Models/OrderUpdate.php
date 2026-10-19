<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderUpdate extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'update_id';
    protected $fillable = ['order_id', 'status', 'notes', 'updated_by'];

    public function order()
    {
        return $this->belongsTo(UserOrder::class, 'order_id', 'order_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'user_id');
    }
}

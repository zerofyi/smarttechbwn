<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Otp extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'otp_id';
    protected $fillable = [
        'name', 'email', 'mobile', 'otp', 'expires_at',
        'attempts', 'last_request_at', 'api_message'
    ];
    protected $casts = [
        'expires_at' => 'datetime',
        'last_request_at' => 'datetime'
    ];
}

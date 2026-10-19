<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'employee_id';

    protected $fillable = [
        'distributor_id',
        'name',
        'email',
        'phone',
        'incentive_percentage',
        'is_active'
    ];

    /**
     * Get all sales attributed to this employee.
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Invoice::class, 'employee_id', 'employee_id')
                    ->where('invoice_type', 'sale');
    }
}

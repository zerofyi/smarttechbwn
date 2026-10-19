<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attribute extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'attribute_id';
    protected $fillable = ['name', 'display_name'];

    public function values()
    {
        return $this->hasMany(AttributeValue::class, 'attribute_id', 'attribute_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_attributes', 'attribute_id', 'product_id')
                    ->using(ProductAttribute::class)
                    ->withPivot('attribute_value_id');
    }

    public function productAttributes()
    {
        return $this->hasMany(ProductAttribute::class, 'attribute_id', 'attribute_id');
    }
}

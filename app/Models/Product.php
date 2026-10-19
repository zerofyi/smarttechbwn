<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'product_id';
    protected $fillable = [
        'category_id', 'brand_id', 'name', 'slug', 'description', 'color',
        'sku', 'barcode', 'retail_price', 'mrp', 'is_active',
        'img_one', 'img_two', 'img_three', 'img_four', 'img_five', 'vid_url'
    ];
    protected $casts = [
        'retail_price' => 'decimal:2',
        'mrp' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'brand_id');
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'product_attributes', 'product_id', 'attribute_id')
                    ->withPivot('attribute_value_id')
                    ->using(ProductAttribute::class);
    }

    public function productAttributes()
    {
        return $this->hasMany(ProductAttribute::class, 'product_id', 'product_id');
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'product_id', 'product_id');
    }
}

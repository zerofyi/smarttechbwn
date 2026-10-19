<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'category_id';
    protected $fillable = [
        'parent_category_id', 'name', 'description', 'slug',
        'icon_url', 'is_active', 'display_order'
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_category_id', 'category_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_category_id', 'category_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'category_id');
    }

    public function banners()
    {
        return $this->hasMany(Banner::class, 'category_id', 'category_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\Category; // Assuming you have a Category model
use App\Models\Tag; // Assuming you have a Category model

class Product extends Model
{
    use SoftDeletes;

    // Define the attributes that are mass assignable
    protected $fillable = [
        'uuid', 'name', 'category_id', 'price', 'photo'
    ];

    // Define the attributes that should be cast to native types

    // Automatically generate UUID on creation
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->uuid)) {
                $product->uuid = (string) Str::uuid(); // Generate UUID if it's not provided
            }
        });
    }

    // Optionally, you can define relationships with other models, such as Category
    public function category()
    {
        return $this->belongsTo(Category::class); // Assuming Category model exists
    }
    public function tags()
{
    return $this->belongsToMany(Tag::class, 'product_tags', 'product_id', 'tag_id');
}
}

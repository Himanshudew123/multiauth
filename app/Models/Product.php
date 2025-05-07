<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Tag;

class Product extends Model
{
    protected $fillable = [
        'uuid', 'name', 'category_id', 'price', 'photo', 'is_deleted'
    ];

    // Automatically generate UUID on creation and apply global scope
    protected static function booted()
    {
        static::creating(function ($product) {
            if (empty($product->uuid)) {
                $product->uuid = (string) Str::uuid();
            }
        });

        // Global scope to only show products that are not soft deleted
        static::addGlobalScope('notDeleted', function (Builder $builder) {
            $builder->where('is_deleted', 0);
        });
    }

    // Soft-delete logic using boolean column
    public function softDelete()
    {
        $this->is_deleted = 1;
        $this->save();
    }

    // Restore the product (set 'is_deleted' to 0)
    public function restore()
    {
        $this->is_deleted = 0;
        $this->save();
    }

    // Relationship with Category model
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relationship with Tag model (many-to-many)
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tags', 'product_id', 'tag_id');
    }
}

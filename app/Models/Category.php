<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = ['uuid', 'name', 'is_deleted'];

    // Automatically generate UUID on creation and apply global scope
    protected static function booted()
    {
        static::creating(function ($category) {
            if (empty($category->uuid)) {
                $category->uuid = (string) Str::uuid();
            }
        });

        static::addGlobalScope('notDeleted', function (Builder $builder) {
            $builder->where('is_deleted', 0);
        });
    }

    // Relationship with Product model (one-to-many)
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Soft-delete logic using boolean column
    public function softDelete()
    {
        $this->is_deleted = 1;
        $this->save();
    }

    // Restore the category (set 'is_deleted' to 0)
    public function restore()
    {
        $this->is_deleted = 0;
        $this->save();
    }

    // Scope for soft deleted categories
    public function scopeWithDeleted($query)
    {
        return $query->where('is_deleted', 1);
    }

    // Scope for non-deleted categories
    public function scopeActive($query)
    {
        return $query->where('is_deleted', 0);
    }
}

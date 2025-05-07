<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Tag extends Model
{
    protected $fillable = ['uuid', 'name', 'is_deleted'];

    // Automatically generate UUID on creation and apply global scope
    protected static function booted()
    {
        static::creating(function ($tag) {
            if (empty($tag->uuid)) {
                $tag->uuid = (string) Str::uuid();
            }
        });

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

    // Restore soft-deleted record
    public function restore()
    {
        $this->is_deleted = 0;
        $this->save();
    }
}

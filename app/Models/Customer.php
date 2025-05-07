<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Customer extends Model
{
    protected $fillable = [
        'uuid', 'name', 'email', 'password', 'bio', 'gender', 'photo', 'number', 'is_deleted'
    ];

    // Automatically generate UUID on creation and apply global scope
    protected static function booted()
    {
        static::creating(function ($customer) {
            if (empty($customer->uuid)) {
                $customer->uuid = (string) Str::uuid();
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

    public function restore()
    {
        $this->is_deleted = 0;
        $this->save();
    }
}

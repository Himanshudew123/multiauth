<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid', 'name', 'email', 'password', 'bio', 'gender', 'photo', 'number'
    ];

    // Automatically generate UUID on creation
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            if (empty($customer->uuid)) {
                $customer->uuid = (string) Str::uuid();
            }
        });
    }
}

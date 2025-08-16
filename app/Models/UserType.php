<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    public function users(){
        return $this->hasMany(User::class);
    }

     protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }
}

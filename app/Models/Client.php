<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Client extends Model {
    protected $fillable = [
        'phone',
        'address',
        'city',
        'user_id',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function schedulings(){
        return $this->hasMany(Scheduling::class);
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

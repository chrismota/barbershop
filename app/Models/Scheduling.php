<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Scheduling extends Model
{
    protected $fillable = [
        'start_date',
        'end_date',
        'client_id',
    ];

    public function client(){
        return $this->belongsTo(Client::class);
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

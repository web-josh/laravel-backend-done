<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    
    protected $fillable = [
        'user_id'
    ];

    public function likeable()
    {
        // polymorphic relationship so we can use this on other models
        return $this->morphTo();
    }
}

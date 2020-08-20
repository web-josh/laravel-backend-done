<?php

namespace App\Models;

use App\Models\Traits\Likeable;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use Likeable;
    protected $fillable = [
        'body',
        'user_id'
    ];

    public function commentable()
    {
        // polymorphic relationship: we can create one relationship in this class and then we can reuse it across other classes
        // this allows us to use the same comment class across different models
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}

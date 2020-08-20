<?php

namespace App\Models;

use App\Models\Traits\Likeable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Cviebrock\EloquentTaggable\Taggable;

class Design extends Model
{
    use Taggable, Likeable;
    
    protected $fillable=[
        'user_id',
        'team_id',
        'image',
        'title',
        'description',
        'slug',
        'close_to_comment',
        'is_live',
        'upload_successful',
        'disk'
    ];

    protected $casts=[
        'is_live' => 'boolean',
        'upload_successful' => 'boolean',
        'close_to_comments' => 'boolean'
    ];

    public function user()
    {
        // each image belongs to a user
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
    
    public function comments()
    {
        // the relationship is commentable
        // provide a default sort order->order bei created_at, descending
        return $this->morphMany(Comment::class, 'commentable')
                ->orderBy('created_at', 'asc');
    }

    
    public function getImagesAttribute()
    {
        // in the frontend we just want to display the link that we get from the api
        // we define a Accessor (Getter) method here on this model where "Images" in getImagesAttribute is the name of the column we want to access
        // The accessor will automatically be called by Eloquent when attempting to retrieve the value of the Images attribute
        // this will enable us to call $this->images; returns an array
        
        return [
            'thumbnail' => $this->getImagePath('thumbnail'),
            'large' => $this->getImagePath('large'),
            'original' => $this->getImagePath('original'),
        ];
    }

    protected function getImagePath($size)
    {
        // we can call the $this->disk property because we call this in a model instance so we have access to $this
        return Storage::disk($this->disk)
                        ->url("uploads/designs/{$size}/".$this->image);
    }

    

}

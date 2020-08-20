<?php 
namespace App\Models\Traits;

use App\Models\Like;

trait Likeable
{
    public static function bootLikeable()
    {
        // 
        static::deleting(function($model){
            $model->removeLikes();
        });
    }

    // delete likes when model is being deleted
    public function removeLikes()
    {
        if($this->likes()->count()){
            $this->likes()->delete();
        }
    }


    public function likes()
    {
        // relationship this has with the model that is using this trait
        return $this->morphMany(Like::class, 'likeable');
    }

    public function like()
    {
        // check if person is authenticated; if nobody is authenticated and this function is called we just want to return because
        // we have nobody to load into the user_id field
        if(! auth()->check()) return;
    
        // check if the current user has already liked the model; isLikedByUser() is defined in
        if($this->isLikedByUser(auth()->id())){
            return;
        };
       
        $this->likes()->create(['user_id' => auth()->id()]);
    }

    public function unlike()
    {
        // check if logged in, otherwise dont do anything
        if(! auth()->check()) return;

        // = if this is not yet liked by the user then we cannot proceed with unliking because there is no record to delete
        if(! $this->isLikedByUser(auth()->id())){
            return;
        }

        // otherwise we fetch the likes created by this user and delete it
        $this->likes()
            ->where('user_id', auth()
            ->id())->delete();
    }

    public function isLikedByUser($user_id)
    {
        // we look at the likes for that model to see if there is any of them that has the user_id equal to the curren $user_id
        // in which case the person has liked the model, otherwise they have no (boolean)
        return (bool)$this->likes()
                ->where('user_id', $user_id)
                ->count();
    }

    

}
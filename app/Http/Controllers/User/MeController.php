<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;

class MeController extends Controller
{
    

    public function getMe()
    {
        if(auth()->check()){
            $user = auth()->user();
            // use a resource instead of json response because I want to customize exactly what I want the ui to receive/see
            return new UserResource($user);
        }
        return response()->json(null, 401);
    }
}

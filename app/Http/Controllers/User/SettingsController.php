<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Rules\MatchOldPassword;
use App\Rules\CheckSamePassword;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Repositories\Contracts\IUser;
use Grimzy\LaravelMysqlSpatial\Types\Point;

class SettingsController extends Controller
{
    protected $users;
    public function __construct(IUser $users)
    {
        $this->users = $users;
    }

    // request comes from user in the ui, we then handle user validation and then we update the users information
    // In this case we target the currenty autheticated user
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        // we want to define a couple of validation rules; this fields are not required at registration but they are at updating the profile
        $this->validate($request, [
            'tagline' => ['required'],
            'name' => ['required'],
            'about' => ['required', 'string', 'min:20'],
            'formatted_address' => ['required'],
            'location.latitude' => ['required', 'numeric', 'min:-90', 'max:90'],
            'location.longitude' => ['required', 'numeric', 'min:-180', 'max:180']
        ]);

        $location = new Point($request->location['latitude'], $request->location['longitude']);
        

        // update the user and pass in all the fields from above
        $user = $this->users->update(auth()->id(), [
            'name' => $request->name,
            'formatted_address' => $request->formatted_address,
            'location' => $location,
            'available_to_hire' => $request->available_to_hire,
            'about' => $request->about,
            'tagline' => $request->tagline,
        ]);

        return new UserResource($user);

    }

    public function updatePassword(Request $request)
    {
     
        // validate the request that comes from the user
        // we expect 3 fields from the user: current password, new password and new password confirmation
        // create 2 custom rules to check the password
        $this->validate($request, [
            'current_password' => ['required', new MatchOldPassword],
            'password' => ['required', 'confirmed', 'min:6', new CheckSamePassword],
        ]);
        
        $this->users->update(auth()->id(), [
            'password' => bcrypt($request->password)
        ]);

        return response()->json(['message' => 'Password updated'], 200);

    }

}

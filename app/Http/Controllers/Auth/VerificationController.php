<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use App\Repositories\Contracts\IUser;
use App\Providers\RouteServiceProvider;
// use Illuminate\Foundation\Auth\VerifiesEmails;

class VerificationController extends Controller
{
    
    protected $users;

   
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(IUser $users)
    {
        $this->middleware('throttle:6,1')->only('verify', 'resend');
        $this->users = $users;
    }

    // Mark user as verified when they click on link in email (=change email_verified_at in database)
    public function verify(Request $request, User $user)
    {
        // check if the url is a valid signed url from laravel
        // access the URL base class of laravel and access the method hasValidSignature (comes with the URL base class of laravel) and pass in the $request
        // if the url doesent have a valid signature then just return a response
        if(! URL::hasValidSignature($request)){
            return response()->json(["errors" => [
                "message" => "Invalid verification link or signature"
            ]], 422);
        }

        // check if user has already verified the account
        // we are passing the resolved $user Object; we have access to the method hasVerifiedEmail because we have access to the MustVerifyEmail trait
        // if the user has already verified their email we return a response
        if($user->hasVerifiedEmail()){
            return response()->json(["errors" => [
                "message" => "Email address already verified"
            ]], 422);
        }

        // if the user passed those two tests then its time to map the user email as verified
        // again, markEmailAsVerified is coming from the base class
        $user->markEmailAsVerified();
        // fire a native laravel event to notify the system that the user has verified their email
        event(new Verified($user));

        // then sent a response back to the user
        return response()->json(['message' => 'Email successfully verified'], 200);

    }

    public function resend(Request $request)
    {
        // validate the the request has the users email
        $this->validate($request, [
            'email' => ['email', 'required']
        ]);
        
        // grab the user by the email: user where email is equal to the request email and we get the first person
        $user = $this->users->findWhereFirst('email', $request->email);

        // if there is no user with that email in the system
        if(! $user){
            return response()->json(["errors" => [
                "email" => "No user could be found with this email address"
            ]], 422);
        }

        // check if user has already verified email (as above)
        if($user->hasVerifiedEmail()){
            return response()->json(["errors" => [
                "message" => "Email address already verified"
            ]], 422);
        }

        // otherwise: (we defined this method in the User model)
        $user->sendEmailVerificationNotification();

        return response()->json(['status' => "verification link resent"]);

    }



}

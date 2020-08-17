<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    
    use AuthenticatesUsers;


    // override some of the methods from the AuthenticatesUser.php trait to make it fit for our api
    public function attemptLogin(Request $request)
    {
        // attempt to issue a token to the user based on the login credentials (basesd on username and password)
        // passing the credentials to the authentication guard and that will return to us the token (because this is what an api guard does)
        $token = $this->guard()->attempt($this->credentials($request));

        // check if the token got issued
        if( ! $token){
            return false;
        }

        // if there is a token get the current user
        // at this point we already authenticated that the user exists with the credentials so we issue an token and go on
        // get the current user, the user who was just authenticated to whom the token was issued
        $user = $this->guard()->user();

        // if the user is supposed to verify their email but has not done so we return false
        if($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()){
            return false;
        }

        // if all of those tests passed we want to set the users token
        $this->guard()->setToken($token);

        return true;
    }

    protected function sendLoginResponse(Request $request)
    {
        // clear any login attempts that the user might have made because later we maybe try to check how often a user tried to login
        $this->clearLoginAttempts($request);

        // get the token from the authentication guard (jwt)
        // pass token to a string because we send it to the ui 
        $token = (string)$this->guard()->getToken();

        // extract the expiry date of the token
        // when working with the ui we are going to use this expiry date to set cookies in the front end so when the client sends a request to login and receives a token
        // we store this token in a cookie and provide it with an expiry date
        // get the expiry date of the payload
        $expiration = $this->guard()->getPayload()->get('exp');

        // send the response to the user in the frontend
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $expiration
        ]);
    }


    protected function sendFailedLoginResponse()
    {
        // grab the user
        $user = $this->guard()->user();

        if($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()){
            return response()->json(["errors" => [
                "message" => "You need to verify your email account"
            ]], 422);
        }

        // otherwise just throw a validation error
        // $this->username() is a method in the base class of laravel that returns for which field laravel uses as a username (in this case its an email)
        throw ValidationException::withMessages([
            $this->username() => "Invalid credentials"
        ]);
    }

    public function logout()
    {
        // we just call the logout method of the authentication guard
        $this->guard()->logout();
        return response()->json(['message' => 'Logged out successfully!']);
    }



}

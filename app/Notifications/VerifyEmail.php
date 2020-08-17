<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\URL;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Auth\Notifications\VerifyEmail as Notification;

class VerifyEmail extends Notification
{
    

    // we only need to overwrite the method verificationUrl from Auth\VerifyEmail.php, everything else is fine
    protected function verificationUrl($notifiable)
    {
        // building our own url: get the url from .env file
        $appUrl = config('app.client_url', config('app.url'));

        // create a temporary signed url which takes an arguement (the name of the route) that we defined in api.php;
        // and the duration of the time that the url is valid (here: 60min) and we pass the user that we receive the notification from
        $url = URL::temporarySignedRoute(
            'verification.verify', 
            Carbon::now()->addMinutes(60), 
            ['user' => $notifiable->id]
        );

        // take the url that we generated ($url), search for /api and replace it with $appUrl
        return str_replace(url('/api'), $appUrl, $url);
        
    }
}

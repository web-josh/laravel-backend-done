<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Auth\Notifications\ResetPassword as Notification;
use Illuminate\Notifications\Messages\MailMessage;


class ResetPassword extends Notification
{
    
    public function toMail($notifiable)
    {
        // this is going to be the route in the frontend. It takes the token we are passing through from the method sendPasswordResetNotification in User.php and append it to
        // that. We also need a query string from the person requesting a reset (in this case the notifiable object which is the person we are trying to notify).
        // we then encode the email of the person into a url parameter 
        $url = url(config('app.client_url').'/password/reset/'.$this->token).
                    '?email='.urlencode($notifiable->email);
        return (new MailMessage)
                    ->line('you are receiving this email because we received a password reset request for your account')
                    ->action('Reset Password', $url)
                    ->line('If you did not request a password reset, no further action is required.');
    }

}

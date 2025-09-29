<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OtpEmailVerificationNotification extends Notification
{
    // use Queueable; // Not needed for synchronous notification

    protected $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Verify Your Email Address')
            ->line('Your OTP for email verification is: ' . $this->otp)
            ->line('This OTP will expire in 10 minutes.')
            ->line('If you did not create an account, no further action is required.');
    }
}

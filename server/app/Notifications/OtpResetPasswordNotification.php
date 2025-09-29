<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OtpResetPasswordNotification extends Notification
{
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
            ->subject('Your Password Reset OTP')
            ->line('Use the following OTP to reset your password:')
            ->line('OTP: ' . $this->otp)
            ->line('This OTP will expire in 10 minutes.')
            ->line('If you did not request a password reset, no further action is required.');
    }
}

<?php

namespace App\Notifications;

use App\Channels\SMSChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OTPNotification extends Notification
{
    use Queueable;

    protected $otp;

    /**
     * Create a new notification instance.
     */
    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return [SMSChannel::class];
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSMS($notifiable)
    {
        return "Your OTP code is: {$this->otp}. Do not share this with anyone.";
    }
}

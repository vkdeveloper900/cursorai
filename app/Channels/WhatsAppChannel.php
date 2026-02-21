<?php

namespace App\Channels;

use App\Services\Notification\TwilioService;
use Illuminate\Notifications\Notification;

class WhatsAppChannel
{
    protected $twilio;

    public function __construct(TwilioService $twilio)
    {
        $this->twilio = $twilio;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toWhatsApp($notifiable);
        $to = $notifiable->routeNotificationFor('whatsapp', $notification);

        if (!$to) {
            $to = $notifiable->phone;
        }

        if (!$to) {
            return;
        }

        $this->twilio->sendWhatsApp($to, $message);
    }
}

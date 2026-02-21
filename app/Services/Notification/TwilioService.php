<?php

namespace App\Services\Notification;

use Twilio\Rest\Client;

class TwilioService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
    }

    /**
     * Send a WhatsApp message.
     */
    public function sendWhatsApp(string $to, string $message)
    {
        return $this->client->messages->create(
            "whatsapp:$to",
            [
                'from' => config('services.twilio.whatsapp_from'),
                'body' => $message
            ]
        );
    }

    /**
     * Send an SMS message.
     */
    public function sendSMS(string $to, string $message)
    {
        return $this->client->messages->create(
            $to,
            [
                'from' => config('services.twilio.sms_from'),
                'body' => $message
            ]
        );
    }
}

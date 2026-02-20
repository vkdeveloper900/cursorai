<?php

namespace App\Services\Notification;

use Twilio\Rest\Client;

class WhatsAppService
{

    public function send(string $to, string $message)
    {

        $client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );

        return $client->messages->create(
            "whatsapp:$to",
            [
                'from' => config('services.twilio.whatsapp_from'),
                'body' => $message
            ]
        );
    }
}

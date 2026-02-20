<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Notification\WhatsAppService;

class TestNotificationController extends Controller
{
    public function sendWhatsapp()
    {
        $whatsapp = app(WhatsAppService::class);

        $response = $whatsapp->send(
            '+919664166525',
            '✅ Test successful! WhatsApp notification is working.'
        );

        return response()->json([
            'status' => true,
            'message' => 'WhatsApp message sent',
            'twilio_response' => $response
        ]);
    }
}

<?php

namespace App\Services\Payment;

use Razorpay\Api\Api;

class RazorpayPaymentService
{
    protected $api;

    public function __construct()
    {
        $this->api = new Api(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        );
    }

    public function createOrder($amount, $receipt)
    {
        return $this->api->order->create([
            'receipt' => $receipt,
            'amount' => $amount * 100, // paisa
            'currency' => 'INR',
        ]);
    }

    public function verifySignature($attributes)
    {
        $this->api->utility->verifyPaymentSignature($attributes);
        return true;
    }
}

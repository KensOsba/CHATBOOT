<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioService
{
    protected $twilio;

    public function __construct()
    {
        $this->twilio = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
    }

    public function sendWhatsAppMessage($to, $message)
    {
        return $this->twilio->messages->create(
            $to,
            [
                'from' => config('services.twilio.whatsapp_from'),
                'body' => $message,
            ]
        );
    }
}

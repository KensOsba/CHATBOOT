<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\TwilioService;
use App\Models\WhatsAppMessage;
use App\Services\ChatGptService;

class WhatsAppController extends Controller
{
    protected $twilio;
    protected $chatgpt;

    public function __construct(TwilioService $twilio, ChatGptService $chatgpt)
    {
        $this->twilio = $twilio;
        $this->chatgpt = $chatgpt;
    }

    public function handleMessage(Request $request)
    {
        $incomingMsg = $request->input('Body');
        $from = $request->input('From');
        $sid = $request->input('SmsSid');

        if (!$incomingMsg || !$from) {
            Log::warning("Mensaje inválido recibido.", $request->all());
            return response("Invalid message", 400);
        }

        Log::info("Mensaje recibido de $from: $incomingMsg");

        WhatsAppMessage::create([
            'from' => $from,
            'body' => $incomingMsg,
            'twilio_sid' => $sid,
        ]);

        // Aquí obtienes la respuesta de ChatGPT en lugar del match
        $respuesta = $this->chatgpt->obtenerRespuesta($incomingMsg);

        try {
            $this->twilio->sendWhatsAppMessage($from, $respuesta);
        } catch (\Exception $e) {
            Log::error("Error enviando mensaje Twilio: " . $e->getMessage());
        }

        return response('Mensaje procesado', 200);
    }
}

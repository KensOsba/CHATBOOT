<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\TwilioService;
use App\Models\WhatsAppMessage;  // <-- Importa el modelo

class WhatsAppController extends Controller
{
    protected $twilio;

    public function __construct(TwilioService $twilio)
    {
        $this->twilio = $twilio;
    }

    public function handleMessage(Request $request)
    {
        $incomingMsg = $request->input('Body');
        $from = $request->input('From'); // Ej: whatsapp:+521234567890
        $sid = $request->input('SmsSid'); // ID del mensaje en Twilio (opcional)

        if (!$incomingMsg || !$from) {
            Log::warning("Mensaje inválido recibido.", $request->all());
            return response("Invalid message", 400);
        }

        Log::info("Mensaje recibido de $from: $incomingMsg");

        // Guardar mensaje en la base de datos
        WhatsAppMessage::create([
            'from' => $from,
            'body' => $incomingMsg,
            'twilio_sid' => $sid,
        ]);

        // Ejemplo de respuesta según el mensaje
        $respuesta = match (strtolower($incomingMsg)) {
            'hola' => '¡Hola! desde Laravel por Onésimo',
            'menu' => 'Las opciones son: A, B, C',
            default => "Recibido: $incomingMsg"
        };

        // Envía la respuesta como mensaje nuevo vía Twilio
        $this->twilio->sendWhatsAppMessage($from, $respuesta);

        return response('Mensaje procesado', 200);
    }
}
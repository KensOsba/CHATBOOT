<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
class WhatsAppController extends Controller
{
    
    public function handleMessage(Request $request)
    {
        // Aquí recibes los datos que Twilio envía en la petición POST
        $incomingMsg = $request->input('Body');   // mensaje recibido
        $from = $request->input('From');          // número del remitente

        // Puedes poner lógica para responder dependiendo del mensaje
        $responseMessage = "Hola, gracias por tu mensaje: " . $incomingMsg;

        // Twilio usa TwiML para responder, que es un XML especial
        $twimlResponse = "<Response><Message>$responseMessage</Message></Response>";

        return response($twimlResponse, 200)
              ->header('Content-Type', 'text/xml');
    }
}
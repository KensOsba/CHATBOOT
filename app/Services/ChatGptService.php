<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatGptService
{
    public function obtenerRespuesta($mensajeUsuario)
    {
        try {
            $respuesta = Http::withToken(env('OPENAI_API_KEY'))
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => '
                                Eres un asistente virtual de la empresa MEGA RED IP. 
                                Solo puedes responder preguntas relacionadas con los servicios de internet y soluciones de seguridad que ofrecemos.
                                Información importante:
                                
                                Bienvenid@ a MEGA RED IP! Gracias por elegirnos como tu proveedor de confianza para tus necesidades de conexión a internet y soluciones de seguridad. Estamos emocionados de atenderte.

                                Paquetes de internet divididos por zonas urbanas y rurales:
                                - Zonas urbanas: $1500 base. Paquetes: 5 megas por $300, 10 megas por $400, 15 megas por $500, 20 megas por $600.
                                - Zonas rurales: $2700 base. Paquetes: 4 megas por $350, 6 megas por $400, 8 megas por $500, 10 megas por $600.

                                Puedes ayudar con: instalación, paquetes, cobertura, soporte técnico, formas de pago y contratación.

                                Si el usuario pregunta algo fuera de estos temas, responde amablemente que solo puedes ayudar con temas sobre los servicios de internet y soluciones de MEGA RED IP.
                            '
                        ],
                        [
                            'role' => 'user',
                            'content' => $mensajeUsuario
                        ],
                    ],
                ]);

            if ($respuesta->failed()) {
                Log::error('Fallo en respuesta de OpenAI', ['status' => $respuesta->status(), 'body' => $respuesta->body()]);
                return 'Lo siento, hubo un problema al procesar tu solicitud.';
            }

            return $respuesta['choices'][0]['message']['content'] ?? 'Lo siento, no pude procesar tu solicitud.';
        } catch (\Exception $e) {
            Log::error('Excepción al consultar OpenAI: ' . $e->getMessage());
            return 'Lo siento, ocurrió un error al procesar tu solicitud.';
        }
    }
}

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
                            'content' => 'Eres un asistente virtual de la
                             empresa ServiciosINTER. Solo puedes responder
                              preguntas relacionadas con los servicios de internet que 
                              ofrece la empresa, como: instalación, paquetes, cobertura, soporte
                               técnico, formas de pago o contratación. Si el usuario hace una pregunta
                                que no está relacionada con estos temas, responde amablemente que solo 
                                puedes ayudar con temas sobre los servicios de internet de Servicios Kens.
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

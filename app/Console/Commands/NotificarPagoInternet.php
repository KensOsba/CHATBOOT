<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cliente;
use App\Jobs\SendWhatsAppMessageJob;
use Illuminate\Support\Facades\Log;
use App\Models\WhatsAppMessage;

class NotificarPagoInternet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clientes:notificar-pago-internet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía recordatorio de pago por WhatsApp a los clientes al fin de mes';

    /**
     * Execute the console command.
     */
   public function handle()
    {
        try{

            $clientes = Cliente::where('estatus', 1)->get(); // se consultan clientes activos
           

            $this->info("Clientes consultados.");
            Log::info("Clientes consultados.");

            foreach ($clientes as $cliente) {
                
                $mensaje = "Estimado cliente {$cliente->nombre}, te recordamos que el pago del servicio de internet vence este mes. ¡Gracias por tu preferencia!";
                
                Log::info($cliente->numero_telefono." - ".$mensaje);
                
                SendWhatsAppMessageJob::dispatch($cliente->numero_telefono, $mensaje);

                WhatsAppMessage::create([
                    'from' => $cliente->numero_telefono,
                    'respuesta' => $mensaje,
                    'twilio_sid' => "",
                ]);
            }

            $this->info("Mensajes de WhatsApp despachados a " . $clientes->count() . " clientes.");
            Log::info("Mensajes de WhatsApp despachados a " . $clientes->count() . " clientes.");

        }catch(\Exception $e){
            Log::error('Error en clientes:notificar-pago-internet: ' . $e->getMessage());
            $this->info("Errores al enviar mensajes.");
        }
    }
}

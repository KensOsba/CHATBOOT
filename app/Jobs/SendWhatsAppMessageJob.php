<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Twilio\Rest\Client;
use App\Services\TwilioService;

class SendWhatsAppMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $telefono;
    public $mensaje;

    /**
     * Create a new job instance.
     */
    public function __construct(string $telefono, string $mensaje)
    {
        $this->telefono = $telefono;
        $this->mensaje = $mensaje;
    }

    /**
     * Execute the job.
     */
    //public function handle(): void
   // {
     //   $twilio = new TwilioService ();
      //  $twilio->sendWhatsAppMessage("whatsapp:{$this->telefono}", $this->mensaje);
//
  //  }


public function handle(): void
{
    \Log::info("Enviando mensaje a: {$this->telefono}");

    try {
        $twilio = new TwilioService();
        $twilio->sendWhatsAppMessage("whatsapp:{$this->telefono}", $this->mensaje);
        \Log::info("Mensaje enviado a: {$this->telefono}");
    } catch (\Exception $e) {
        \Log::error("Error enviando mensaje a {$this->telefono}: " . $e->getMessage());
    }
}





}


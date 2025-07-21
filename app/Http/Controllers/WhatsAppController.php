<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\TwilioService;
use App\Models\WhatsAppMessage;
use App\Models\Cliente;
use App\Models\Zona;
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
        $incomingMsg = trim(strtolower($request->input('Body')));
        $incomingMsg = preg_replace('/\s+/', '', $incomingMsg); // elimina espacios
        $from = $request->input('From');
        $sid = $request->input('SmsSid');
        if (!$incomingMsg || !$from) {
            Log::warning("Mensaje inválido recibido.", $request->all());
            return response("Invalid message", 400);
        }
        Log::info("Mensaje recibido de $from: [$incomingMsg]");
        WhatsAppMessage::create([
            'from' => $from,
            'body' => $incomingMsg,
            'twilio_sid' => $sid,
        ]);
        $saludos = ['hola', 'buenastardes', 'buenasnoches', 'disculpa', 'hi'];
        $clienteRegistrado = Cliente::where('numero_telefono', $from)->exists();
        // Estado guardado en cache para la conversación
        $estado = Cache::get("estado_{$from}", null);
        if (in_array($incomingMsg, $saludos) && !$clienteRegistrado) {
            $respuesta = "¡Hola! ¿Cómo puedo ayudarte hoy con nuestros servicios de internet o soluciones de seguridad en MEGA RED IP?\n\n*Menú:*\n1️⃣ Cotizaciones\n2️⃣ Soporte técnico\n3️⃣ Información de servicios\n4️⃣ Hablar con un asesor";
            Cache::forget("estado_{$from}"); // Reiniciar estado
        }
        else if ($estado === 'esperando_zona') {
            // Aquí el usuario responde con número de zona
            $zonas = Zona::orderBy('id')->get();
            $indice = (int)$incomingMsg - 1;
            if (isset($zonas[$indice])) {
                $zonaSeleccionada = $zonas[$indice];
                Cache::put("zona_seleccionada_{$from}", $zonaSeleccionada->id, 3600);
                Cache::forget("estado_{$from}");
                // Obtener paquetes de esta zona
                $paquetesTexto = "";
                foreach ($zonaSeleccionada->paquetes as $paquete) {
                    $paquetesTexto .= "{$paquete->velocidad_megas} Mbps - \${$paquete->precio}\n";
                }
                $respuesta = "Has seleccionado la zona *{$zonaSeleccionada->nombre}* ({$zonaSeleccionada->tipo}).\nCosto de instalación: \${$zonaSeleccionada->costo_instalacion}\n\nPaquetes disponibles:\n$paquetesTexto\n\nPor favor, envíanos tu dirección y los servicios que te interesan.";
            } else {
                $respuesta = "No reconocí esa opción. Por favor elige un número válido de la lista de zonas:";
                $respuesta .= $this->getListaZonasTexto();
            }
        }
        else {
            switch ($incomingMsg) {
                case '1':
                case 'uno':
                case 'cotizacion':
                case 'cotizaciones':
                    // Mostrar lista de zonas y pedir que elijan
                    Cache::put("estado_{$from}", 'esperando_zona', 3600);
                    $respuesta = "Claro, elige la zona donde quieres que hagamos la instalación:\n";
                    $respuesta .= $this->getListaZonasTexto();
                    break;
                case '2':
                case 'dos':
                case 'soporte':
                case 'soportetecnico':
                    $respuesta = "Para brindarte soporte técnico, por favor descríbenos el problema que estás experimentando.";
                    break;
                case '3':
                case 'tres':
                case 'informacion':
                case 'servicios':
                    $respuesta = "Ofrecemos servicios de internet residencial y empresarial, instalación de cámaras de seguridad, cercado eléctrico y mucho más.";
                    break;
                case '4':
                case 'cuatro':
                case 'asesor':
                    $respuesta = "Un asesor se pondrá en contacto contigo en breve. Gracias por escribirnos.";
                    break;
                default:
                    $respuesta = $this->chatgpt->obtenerRespuesta($incomingMsg);
                    break;
            }
        }
        try {
            $this->twilio->sendWhatsAppMessage($from, $respuesta);
            WhatsAppMessage::create([
                'from' => $from,
                'respuesta' => $respuesta,
                'twilio_sid' => $sid,
            ]);
        } catch (\Exception $e) {
            Log::error("Error enviando mensaje Twilio: " . $e->getMessage());
        }
        return response('Mensaje procesado', 200);
    }
    private function getListaZonasTexto()
    {
        $zonas = Zona::orderBy('id')->get();
        $texto = "";
        foreach ($zonas as $index => $zona) {
            $num = $index + 1;
            $texto .= "{$num}. {$zona->nombre} ({$zona->tipo})\n";
        }
        return $texto;
    }
}
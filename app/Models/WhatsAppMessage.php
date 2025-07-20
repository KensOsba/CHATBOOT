<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppMessage extends Model
{
    protected $table = 'whatsapp';  // Nombre de la tabla en la base de datos

    protected $fillable = [
        'from',
        'body',
        'respuesta',
        'twilio_sid',
    ];
}
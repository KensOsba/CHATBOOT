<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paquete extends Model
{
    protected $fillable = ['zona_id', 'velocidad_megas', 'precio'];

    public function zona()
    {
        return $this->belongsTo(Zona::class);
    }
}
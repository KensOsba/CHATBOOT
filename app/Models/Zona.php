<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zona extends Model
{
    protected $fillable = ['nombre', 'tipo', 'costo_instalacion'];

    public function paquetes()
    {
        return $this->hasMany(Paquete::class);
    }
}
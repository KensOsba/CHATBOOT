<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZonasPaquetesSeeder extends Seeder
{
    public function run(): void
    {
        $zonasUrbanas = ['Orizaba', 'Córdoba', 'Puebla'];
        $zonasRurales = ['Mixtla', 'Los Reyes', 'Nepopualco'];

        // Insertar zonas urbanas
        foreach ($zonasUrbanas as $nombre) {
            $zonaId = DB::table('zonas')->insertGetId([
                'nombre' => $nombre,
                'tipo' => 'urbana',
                'costo_instalacion' => 1500,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('paquetes')->insert([
                ['zona_id' => $zonaId, 'velocidad_megas' => 5,  'precio' => 300, 'created_at' => now(), 'updated_at' => now()],
                ['zona_id' => $zonaId, 'velocidad_megas' => 10, 'precio' => 400, 'created_at' => now(), 'updated_at' => now()],
                ['zona_id' => $zonaId, 'velocidad_megas' => 15, 'precio' => 500, 'created_at' => now(), 'updated_at' => now()],
                ['zona_id' => $zonaId, 'velocidad_megas' => 20, 'precio' => 600, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // Insertar zonas rurales
        foreach ($zonasRurales as $nombre) {
            $zonaId = DB::table('zonas')->insertGetId([
                'nombre' => $nombre,
                'tipo' => 'rural',
                'costo_instalacion' => 2700,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('paquetes')->insert([
                ['zona_id' => $zonaId, 'velocidad_megas' => 4,  'precio' => 350, 'created_at' => now(), 'updated_at' => now()],
                ['zona_id' => $zonaId, 'velocidad_megas' => 6,  'precio' => 400, 'created_at' => now(), 'updated_at' => now()],
                ['zona_id' => $zonaId, 'velocidad_megas' => 8,  'precio' => 500, 'created_at' => now(), 'updated_at' => now()],
                ['zona_id' => $zonaId, 'velocidad_megas' => 10, 'precio' => 600, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('zonas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->enum('tipo', ['urbana', 'rural']);
            $table->integer('costo_instalacion');
            $table->timestamps();
        });

        Schema::create('paquetes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zona_id')->constrained('zonas')->onDelete('cascade');
            $table->integer('velocidad_megas');
            $table->integer('precio');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('paquetes');
        Schema::dropIfExists('zonas');
    }
};

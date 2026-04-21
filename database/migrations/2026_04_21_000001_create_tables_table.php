<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla de mesas del restaurante.
 *
 * Corrección del sistema legado:
 * - El sistema original no tenía tabla de mesas; las mesas eran un entero
 *   arbitrario en `mesa pedido`. Esto impedía agregar atributos (capacidad,
 *   zona, estado explícito) y forzaba consultas dinámicas para saber si
 *   una mesa estaba ocupada.
 * - Ahora el estado "libre/ocupada" es un campo propio, no se infiere.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->id();

            // Número de mesa (1-40). Único y siempre positivo.
            $table->unsignedTinyInteger('number')->unique();

            // Capacidad de personas. Default 4 (valor razonable para un restaurante).
            $table->unsignedSmallInteger('capacity')->default(4);

            // Estado explícito. Eliminado el "se infiere por registros en otra tabla".
            $table->enum('status', ['free', 'occupied'])->default('free');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};

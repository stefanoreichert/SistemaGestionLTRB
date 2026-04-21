<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla de productos del catálogo.
 *
 * Correcciones del sistema legado:
 * - Bug #6: El sistema original tenía las acciones agregar_producto /
 *   actualizar_producto referenciando columnas `id_tipo` e `Id_tipo_producto`
 *   que NO existían. Se usan los nombres reales: `type` y `category`.
 * - Bug #7: `stock` es UNSIGNED, lo que impide valores negativos a nivel de BD.
 *   Aun así la validación de suficiencia de stock se hace en el servicio.
 * - Mejora: campo `active` para desactivar productos sin eliminarlos
 *   (soft-disable), preservando el historial en order_items.
 * - Índices en `type` y `category` para filtros de catálogo eficientes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('name', 100);

            // Clasificación de primer nivel: Bebida, Comida, Postre
            $table->string('type', 50);

            // Sub-clasificación: Gaseosa, Cerveza, Pizza, Pasta, Carne, etc.
            $table->string('category', 50);

            $table->decimal('price', 10, 2);

            // UNSIGNED impide stock negativo a nivel de BD (Bug #7).
            $table->unsignedInteger('stock')->default(0);

            // Permite ocultar productos sin eliminarlos del histórico.
            $table->boolean('active')->default(true);

            $table->timestamps();

            // Índices para filtros de catálogo (reproducción de los índices del legado).
            $table->index('type');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

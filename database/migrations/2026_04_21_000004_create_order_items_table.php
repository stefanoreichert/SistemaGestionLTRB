<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla de ítems de pedido.
 *
 * Correcciones del sistema legado:
 * - Bug #5: La tabla original se llamaba `mesa pedido` (con espacio), lo que
 *   obligaba a usar backticks en cada query y era fuente frecuente de errores.
 *   Se renombra a `order_items` siguiendo convenciones estándar.
 * - Mejora histórica: `unit_price` guarda el precio del producto AL MOMENTO
 *   del pedido. Si el precio del producto cambia luego, el historial es fiel.
 *   El sistema legado hacía JOIN con la tabla productos al momento del reporte,
 *   lo que significaba que un cambio de precio alteraba reportes históricos.
 * - Regla de negocio 7.3: Un mismo producto solo puede aparecer una vez por
 *   pedido (UNIQUE en order_id + product_id). Si el mozo agrega el mismo
 *   producto, la lógica de OrderService suma la cantidad.
 * - `quantity` es UNSIGNED → mínimo 0 a nivel BD. La validación en el servicio
 *   impide cantidad 0 (Bug #10 del legado: actualizar_cantidad no validaba > 0).
 * - cascadeOnDelete en order_id: si se cancela/elimina un Order, sus ítems
 *   se eliminan en cascada.
 * - restrictOnDelete en product_id: no se puede borrar un producto que ya
 *   fue pedido (integridad del historial).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            // FK al pedido. Si se elimina el pedido, se eliminan los ítems.
            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            // FK al producto. No se puede eliminar un producto con ítems
            // históricos asociados (preservación del historial).
            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();

            // Cantidad pedida. UNSIGNED garantiza ≥ 0 en BD.
            $table->unsignedInteger('quantity');

            // Precio unitario al momento del pedido (snapshot histórico).
            $table->decimal('unit_price', 10, 2);

            $table->timestamps();

            // Un producto aparece una sola vez por pedido.
            // OrderService suma cantidad si ya existe (regla de negocio 7.3).
            $table->unique(['order_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

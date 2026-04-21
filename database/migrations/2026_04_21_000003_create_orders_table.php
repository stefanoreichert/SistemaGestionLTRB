<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla de pedidos (uno activo por mesa + historial de cerrados).
 *
 * Correcciones del sistema legado:
 * - Arquitectura #1: El sistema original eliminaba los registros al cerrar mesa,
 *   perdiendo todo el historial de ventas. Ahora los pedidos nunca se borran;
 *   solo cambian de status ('open' → 'closed' | 'cancelled').
 * - Bug #4 (arquitectura): Al tener una entidad Order separada con status y
 *   timestamps propios, el cierre de mesa es una transacción atómica en el
 *   servicio (OrderService::close()) en lugar de operaciones dispersas.
 * - `total` se calcula y guarda al cerrar (no en tiempo real) para preservar
 *   el valor exacto al momento del cierre.
 * - `user_id` nullable para compatibilidad con migraciones futuras de roles.
 *
 * Restricciones:
 * - Una mesa NO puede tener dos pedidos 'open' simultáneamente.
 *   Esto se garantiza a nivel de aplicación en OrderService::open().
 * - `table_id` con FK estricta: no se puede borrar una mesa con pedidos.
 * - `user_id` con SET NULL al borrar usuario (historial se preserva).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // FK a la mesa. restrictOnDelete: no se puede borrar una mesa
            // que tenga pedidos asociados (histórico).
            $table->foreignId('table_id')->constrained('tables')->restrictOnDelete();

            // FK al usuario que abrió la mesa. nullable + nullOnDelete para
            // preservar el historial aunque se elimine el usuario.
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Total calculado y almacenado al momento del cierre.
            // Default 0 mientras el pedido está abierto.
            $table->decimal('total', 10, 2)->default(0);

            // open: pedido activo | closed: cerrado y cobrado | cancelled: anulado
            $table->enum('status', ['open', 'closed', 'cancelled'])->default('open');

            // Timestamps del ciclo de vida del pedido.
            $table->timestamp('opened_at')->useCurrent();
            $table->timestamp('closed_at')->nullable();

            $table->timestamps();

            // Índice para consultar rápido el pedido abierto de una mesa.
            $table->index(['table_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

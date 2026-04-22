<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Mejoras de rendimiento y persistencia:
 *
 * 1. Agrega `kitchen_status` a order_items.
 *    El sistema anterior guardaba este estado en Cache, lo que significaba que
 *    un reinicio del servidor borraba todos los estados de preparación.
 *    Ahora se persiste en DB para garantizar durabilidad.
 *
 * 2. Índice en order_items.kitchen_status para consultas del KDS.
 *
 * Nota: order_items.order_id y orders.table_id ya tienen índices implícitos
 * por sus foreign keys (MySQL/MariaDB los crea automáticamente).
 * El índice compuesto [table_id, status] en orders ya existe desde la migración
 * original 2026_04_21_000003.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->enum('kitchen_status', ['new', 'preparing', 'ready'])
                  ->default('new')
                  ->after('notes');

            $table->index('kitchen_status', 'order_items_kitchen_status_idx');
        });

        // Filas existentes ya reciben 'new' por el DEFAULT de la columna.
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('order_items_kitchen_status_idx');
            $table->dropColumn('kitchen_status');
        });
    }
};

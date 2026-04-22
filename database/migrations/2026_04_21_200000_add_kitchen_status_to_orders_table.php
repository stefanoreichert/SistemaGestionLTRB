<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega kitchen_status a orders para el flujo cocina → mozo.
 * Valores: pendiente → en_proceso → listo → entregado
 *
 * Se mantiene separado del campo `status` (open/closed/cancelled)
 * que gestiona el ciclo de vida contable del pedido.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('kitchen_status', ['pendiente', 'en_proceso', 'listo', 'entregado'])
                  ->default('pendiente')
                  ->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('kitchen_status');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega soporte para pedidos de delivery.
 *
 * - table_id: ahora nullable (los deliveries no tienen mesa física)
 * - is_delivery: flag booleano para distinguir deliveries de mesas
 * - delivery_label: nombre del cliente / referencia del delivery
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Hacer table_id nullable para deliveries sin mesa física
            $table->foreignId('table_id')->nullable()->change();

            $table->boolean('is_delivery')->default(false)->after('user_id');
            $table->string('delivery_label', 100)->nullable()->after('is_delivery');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['is_delivery', 'delivery_label']);
            $table->foreignId('table_id')->nullable(false)->change();
        });
    }
};

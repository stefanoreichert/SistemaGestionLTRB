<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega delivery_number (1-20) para identificar el slot numerado del delivery.
 * Permite al dashboard mostrar exactamente 20 posiciones fijas.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedTinyInteger('delivery_number')->nullable()->after('delivery_label');
            $table->index(['is_delivery', 'delivery_number', 'status'], 'idx_delivery_slot');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_delivery_slot');
            $table->dropColumn('delivery_number');
        });
    }
};

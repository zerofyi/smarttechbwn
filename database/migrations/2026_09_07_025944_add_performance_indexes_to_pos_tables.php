<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->index(['user_id', 'stock'], 'idx_inv_user_stock');
            $table->index(['imei_one', 'imei_two', 'serial_number'], 'idx_inv_barcodes');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->index(['distributor_id', 'invoice_type', 'invoice_no'], 'idx_inv_dist_type_no');
        });
    }

    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropIndex('idx_inv_user_stock');
            $table->dropIndex('idx_inv_barcodes');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex('idx_inv_dist_type_no');
        });
    }
};

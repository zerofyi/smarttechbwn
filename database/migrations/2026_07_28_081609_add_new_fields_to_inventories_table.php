<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            // 1. Add the new columns
            $table->date('activation_date')->nullable()->after('warranty');
            $table->integer('battery_health')->nullable()->after('activation_date');

            // 2. Change the existing warranty column to be nullable
            $table->string('warranty')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropColumn(['activation_date', 'battery_health']);
            $table->string('warranty')->nullable(false)->change();
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The original user_orders / order_items / order_updates tables were
     * created for the distributor POS flow and their ENUMs do not cover
     * the online customer flow: 'cod' / 'razorpay' payment methods and
     * the 'placed' order status (plus 'return_requested' timeline entries).
     * Without this migration, placing any online order fails with a
     * CHECK constraint violation. This only widens the allowed values;
     * no existing value is removed and no data is changed.
     */
    public function up(): void
    {
        Schema::table('user_orders', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'card', 'upi', 'netbanking', 'wallet', 'emi', 'cod', 'razorpay'])->change();
            $table->enum('status', ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'returned', 'placed'])->change();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->enum('status', ['pending', 'shipped', 'delivered', 'returned', 'cancelled', 'placed'])->change();
        });

        Schema::table('order_updates', function (Blueprint $table) {
            $table->enum('status', ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'returned', 'placed', 'return_requested'])->change();
        });
    }

    public function down(): void
    {
        Schema::table('user_orders', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'card', 'upi', 'netbanking', 'wallet', 'emi'])->change();
            $table->enum('status', ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'returned'])->change();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->enum('status', ['pending', 'shipped', 'delivered', 'returned', 'cancelled'])->change();
        });

        Schema::table('order_updates', function (Blueprint $table) {
            $table->enum('status', ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'returned'])->change();
        });
    }
};

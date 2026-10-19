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
        Schema::create('order_updates', function (Blueprint $table) {
            $table->id('update_id');
            $table->foreignId('order_id')->constrained('user_orders', 'order_id')->onDelete('cascade');
            $table->enum('status', ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'returned']);
            $table->text('notes')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users', 'user_id')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_updates');
    }
};

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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id('inventory_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignId('shop_id')->constrained('shops', 'shop_id')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products', 'product_id')->onDelete('cascade');
            $table->string('product_name');
            $table->enum('product_type', ['new', 'used', 'unused', 'refurbished', 'open box'])->default('used');
            $table->enum('health', ['fair', 'good', 'superb', 'mint', 'super mint', 'sealed pack'])->default('super mint');
            $table->string('age');
            $table->string('warranty');
            $table->string('color')->nullable();
            $table->text('notes')->nullable();
            $table->string('imei_one')->nullable();
            $table->string('imei_two')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('unit')->default('box');
            $table->integer('stock')->default(0);
            $table->decimal('purchase_rate', 12, 2);
            $table->decimal('gst_per', 12, 2);
            $table->decimal('sell_rate', 12, 2)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};

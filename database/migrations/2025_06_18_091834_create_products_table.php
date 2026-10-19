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
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->foreignId('category_id')->constrained('categories', 'category_id')->onDelete('cascade');
            $table->foreignId('brand_id')->constrained('brands', 'brand_id')->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color')->nullable();
            $table->string('sku')->unique();
            $table->string('barcode')->unique()->nullable();
            $table->decimal('retail_price', 12, 2);
            $table->decimal('mrp', 12, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('img_one')->nullable();
            $table->string('img_two')->nullable();
            $table->string('img_three')->nullable();
            $table->string('img_four')->nullable();
            $table->string('img_five')->nullable();
            $table->string('vid_url')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

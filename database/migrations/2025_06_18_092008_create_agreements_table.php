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
        Schema::create('agreements', function (Blueprint $table) {
            $table->id('agreement_id');
            $table->foreignId('shop_id')->constrained('shops', 'shop_id')->onDelete('cascade');
            $table->foreignId('financer_id')->nullable()->constrained('financers', 'financer_id')->onDelete('SET NULL');
            $table->foreignId('customer_id')->nullable()->constrained('customers', 'customer_id')->onDelete('SET NULL');
            $table->enum('type', ['purchase', 'sale', 'loan', 'other'])->default('loan');
            $table->string('photo')->nullable();
            $table->string('doc')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agreements');
    }
};

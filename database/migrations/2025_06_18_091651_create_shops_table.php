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
        Schema::create('shops', function (Blueprint $table) {
            $table->id('shop_id');
            $table->unsignedBigInteger('user_id');
            $table->string('shop_name');
            $table->string('shop_gstin')->nullable();
            $table->string('shop_address')->nullable();
            $table->string('shop_pin_code')->nullable();
            $table->string('shop_location')->nullable();
            $table->string('shop_photo')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};

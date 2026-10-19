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
        Schema::create('customers', function (Blueprint $table) {
            $table->id('customer_id');
            $table->string('name');
            $table->string('mobile');
            $table->string('aadhaar_no')->nullable();
            $table->string('pan_no')->nullable();
            $table->string('address')->nullable();
            $table->string('photo')->nullable();
            $table->string('doc_one')->nullable();
            $table->string('doc_two')->nullable();
            $table->string('doc_three')->nullable();
            $table->string('signature')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};

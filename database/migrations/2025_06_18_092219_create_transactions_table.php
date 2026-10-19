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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id('transaction_id');
            $table->string('trz_number')->unique();
            $table->decimal('trz_amount', 12, 2);
            $table->date('trz_date')->nullable();
            $table->string('method')->nullable();
            $table->string('trz_id')->nullable();
            $table->string('ref_id')->nullable();
            $table->string('status')->nullable();
            $table->string('remarks')->nullable();
            $table->json('response')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

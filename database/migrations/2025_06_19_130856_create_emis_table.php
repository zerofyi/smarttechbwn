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
        Schema::create('emis', function (Blueprint $table) {
            $table->id('emi_id');
            $table->foreignId('loan_id')->constrained('loans', 'loan_id')->onDelete('cascade');
            $table->string('emi_no')->unique();
            $table->decimal('emi_amount', 12, 2);
            $table->date('emi_date')->nullable();
            $table->date('paid_date')->nullable();
            $table->decimal('paid_amount', 12, 2);
            $table->decimal('due_amount', 12, 2);
            $table->string('method')->nullable();
            $table->foreignId('transaction_id')->nullable()->constrained('transactions', 'transaction_id')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emis');
    }
};

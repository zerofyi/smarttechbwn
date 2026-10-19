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
        Schema::create('loans', function (Blueprint $table) {
            $table->id('loan_id');
            $table->foreignId('financer_id')->constrained('financers', 'financer_id')->onDelete('cascade');
            $table->foreignId('agreement_id')->nullable()->constrained('agreements', 'agreement_id')->onDelete('SET NULL');
            $table->foreignId('invoice_id')->nullable()->constrained('invoices', 'invoice_id')->onDelete('SET NULL');
            $table->foreignId('customer_id')->nullable()->constrained('customers', 'customer_id')->onDelete('SET NULL');
            $table->string('loan_no')->unique();
            $table->enum('loan_slab', ['1', '15'])->default('1');
            $table->decimal('invoice_amount', 12, 2);
            $table->decimal('down_payment', 12, 2);
            $table->decimal('loan_amount', 12, 2);
            $table->decimal('processing_charge', 12, 2);
            $table->decimal('file_charge', 12, 2);
            $table->decimal('payable_amount', 12, 2);
            $table->decimal('paid_amount', 12, 2);
            $table->decimal('settled_amount', 12, 2);
            $table->string('emi')->nullable();
            $table->decimal('emi_amount', 12, 2);
            $table->date('loan_date')->nullable();
            $table->date('first_emi')->nullable();
            $table->date('last_emi')->nullable();
            $table->enum('loan_status', ['open', 'closed', 'settled'])->default('open');
            $table->string('name');
            $table->string('mobile');
            $table->string('alternative_mobile')->nullable();
            $table->string('address')->nullable();
            $table->string('aadhaar_no');
            $table->string('pan_no')->nullable();
            $table->string('photo')->nullable();
            $table->string('doc_one')->nullable();
            $table->string('doc_two')->nullable();
            $table->string('doc_three')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};

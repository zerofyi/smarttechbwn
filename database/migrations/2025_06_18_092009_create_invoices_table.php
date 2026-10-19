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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id('invoice_id');
            $table->foreignId('distributor_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users', 'user_id')->onDelete('SET NULL');
            $table->foreignId('party_id')->nullable()->constrained('parties', 'party_id')->onDelete('SET NULL');
            $table->foreignId('customer_id')->nullable()->constrained('customers', 'customer_id')->onDelete('SET NULL');
            $table->foreignId('agreement_id')->nullable()->constrained('agreements', 'agreement_id')->onDelete('SET NULL');
            $table->string('invoice_no')->unique();
            $table->string('invoice_url')->unique();
            $table->enum('invoice_type', ['purchase', 'sale']);
            $table->enum('invoice_for', ['party', 'user', 'customer'])->default('customer');
            $table->string('name')->nullable();
            $table->string('mobile')->nullable();
            $table->string('address')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('total_discount', 12, 2)->default(0);
            $table->decimal('total_tax', 12, 2)->default(0);
            $table->decimal('shipping_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->decimal('paid', 12, 2);
            $table->decimal('due_amount', 12, 2);
            $table->enum('payment_type', ['cash', 'upi', 'card', 'cheque', 'net banking', 'credit'])->default('cash');
            $table->date('invoice_date')->nullable();
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('photo_url')->nullable();
            $table->string('doc_url')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id('invoice_item_id');
            $table->foreignId('invoice_id')->constrained('invoices', 'invoice_id')->onDelete('cascade');
            $table->foreignId('inventory_id')->constrained('inventories', 'inventory_id')->onDelete('cascade');
            $table->integer('qty')->default(0);
            $table->decimal('rate', 12, 2);
            $table->decimal('discount_percent', 12, 2);
            $table->decimal('discount', 12, 2);
            $table->decimal('tax_percent', 12, 2);
            $table->decimal('tax', 12, 2);
            $table->decimal('amount', 12, 2);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};

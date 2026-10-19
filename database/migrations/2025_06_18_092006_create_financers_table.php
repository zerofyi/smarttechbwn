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
        Schema::create('financers', function (Blueprint $table) {
            $table->id('financer_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->string('name');
            $table->string('mobile');
            $table->string('id_no')->nullable();
            $table->string('photo')->nullable();
            $table->string('doc_one')->nullable();
            $table->string('doc_two')->nullable();
            $table->string('doc_three')->nullable();
            $table->string('signature')->nullable();
            $table->string('address')->nullable();
            $table->string('state')->nullable();
            $table->string('gstin')->nullable();
            $table->string('pan')->nullable();
            $table->string('bank')->nullable();
            $table->string('ifsc')->nullable();
            $table->string('ac_no')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financers');
    }
};

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
        Schema::create('otps', function (Blueprint $table) {
            $table->id('otp_id');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile');
            $table->string('otp');
            $table->timestamp('expires_at')->nullable();
            $table->integer('attempts')->default(0);
            $table->timestamp('last_request_at')->nullable();
            $table->string('api_message')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otps');
    }
};

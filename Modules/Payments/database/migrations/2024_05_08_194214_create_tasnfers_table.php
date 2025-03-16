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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->string('accountNumber');
            $table->string('bankName');
            $table->string('iban')->nullable();
            $table->string('swift')->nullable();
            $table->string('beneficiaryName')->nullable();
            $table->string('beneficiaryAddress')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('payment_method_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};

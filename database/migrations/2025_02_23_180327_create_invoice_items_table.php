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
        Schema::disableForeignKeyConstraints();

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('quantity')->nullable();
            $table->decimal('price_net', 10, 2)->nullable();
            $table->decimal('price_gross', 10, 2)->nullable();
            $table->string('tax_rate')->nullable();
            $table->decimal('tax_amount', 10, 2)->nullable();
            $table->decimal('discount', 10, 2)->nullable();
            $table->string('discount_type')->nullable();
            $table->decimal('total_net', 10, 2)->nullable();
            $table->decimal('total_gross', 10, 2)->nullable();
            $table->decimal('total_tax', 10, 2)->nullable();
            $table->decimal('total_discount', 10, 2)->nullable();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};

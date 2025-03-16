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

        Schema::create('costs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('amount', 10, 2);
            $table->text('description')->nullable();
            $table->date('date');
            $table->foreignId('category_id')->constrained('cost_categories');
            $table->string('invoice_number')->nullable();
            $table->string('invoice_file_path')->nullable();
            $table->string('receipt_file_path')->nullable();
            $table->date('invoice_date')->nullable();
            $table->date('invoice_due_date')->nullable();
            $table->date('payment_date')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('costs');
    }
};

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

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('no');
            $table->foreignId('buyer_id')->constrained();
            $table->string('type');
            $table->string('payment_status');
            $table->string('place')->nullable();
            $table->date('sale_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('issue_date');
            $table->foreignId('parent_id')->nullable()->constrained('invoices');
            $table->foreignId('user_id')->constrained();
            $table->string('comment')->nullable();
            $table->string('currency')->default('EUR');
            $table->string('issuer_name')->nullable();
            $table->decimal('grand_total_net', 10, 2)->default(0);
            $table->decimal('grand_total_gross', 10, 2)->default(0);
            $table->decimal('grand_total_tax', 10, 2)->default(0);
            $table->decimal('grand_total_discount', 10, 2)->default(0);
            $table->decimal('paid', 10, 2)->default(0);
            $table->decimal('due', 10, 2)->default(0);
            $table->string('path')->nullable();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

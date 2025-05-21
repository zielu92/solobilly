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

        Schema::create('work_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_id')->constrained();
            $table->dateTime('start');
            $table->dateTime('end');
            $table->string('description')->nullable();
            $table->timestamps();
            $table->index('start');
            $table->index('end');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_logs');
    }
};

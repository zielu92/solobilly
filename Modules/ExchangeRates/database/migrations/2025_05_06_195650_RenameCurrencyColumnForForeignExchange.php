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
        Schema::table('exchange_rates', static function (Blueprint $table) {
            $table->renameColumn('currency', 'currency_id');
            $table->renameColumn('base_currency', 'base_currency_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exchange_rates', static function (Blueprint $table) {
            $table->renameColumn('currency_id', 'currency');
            $table->renameColumn('base_currency_id', 'base_currency');
        });
    }
};

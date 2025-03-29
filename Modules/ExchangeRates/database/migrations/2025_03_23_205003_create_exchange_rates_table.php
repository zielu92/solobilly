<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the "exchange_rates" table.
     *
     * This migration sets up the "exchange_rates" table with the following columns:
     * - id: Auto-incrementing primary key.
     * - type: A string indicating the type of exchange rate.
     * - date: A date field for the exchange rate's reference date.
     * - value: A float representing the exchange rate value.
     * - currency: A string for the currency code.
     * - base_currency: A string for the base currency code.
     * - source: A string denoting the exchange rate source (defaults to 'NBP').
     * - created_at & updated_at: Timestamps managed automatically by the framework.
     */
    public function up(): void
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->date('date');
            $table->float('value');
            $table->string('currency');
            $table->string('base_currency');
            $table->string('source')->default('NBP');
            $table->timestamps();
        });
    }

    /**
     * Revert the migration by dropping the "exchange_rates" table if it exists.
     *
     * This method undoes the changes made in the up() method, ensuring that the database schema
     * returns to its state prior to this migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};

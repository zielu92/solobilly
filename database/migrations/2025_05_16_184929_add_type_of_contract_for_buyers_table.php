<?php

use App\Enum\TypeOfContract;
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
        Schema::table('buyers', function (Blueprint $table) {
            $table->enum('contract_type', array_column(TypeOfContract::cases(), 'value'))->default(TypeOfContract::OTHER->value);
            $table->decimal('contract_rate', 8, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buyers', function (Blueprint $table) {
            $table->dropColumn(['contract_type', 'contract_rate']);
        });
    }
};

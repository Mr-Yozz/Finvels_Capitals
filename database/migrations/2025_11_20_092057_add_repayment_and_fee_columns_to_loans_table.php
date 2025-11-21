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
        Schema::table('loans', function (Blueprint $table) {
            //
            $table->string('repayment_frequency')->default('monthly'); // 'weekly' or 'monthly'
            $table->decimal('processing_fee', 12, 2)->default(0);
            $table->decimal('insurance_amount', 12, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            //
            $table->dropColumn(['repayment_frequency', 'processing_fee', 'insurance_amount']);
        });
    }
};

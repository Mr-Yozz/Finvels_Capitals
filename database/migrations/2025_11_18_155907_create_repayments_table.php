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
        Schema::table('repayments', function (Blueprint $table) {
            //
            $table->decimal('principal_component', 10, 2)->default(0);
            $table->decimal('interest_component', 10, 2)->default(0);
            $table->decimal('balance', 10, 2)->default(0);
            $table->string('loan_instance')->nullable();
            $table->decimal('due_total', 10, 2)->default(0);
            $table->decimal('pr', 10, 2)->default(0);
            $table->decimal('sanchay_due', 10, 2)->default(0);
            $table->string('lp_pal')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repayments', function (Blueprint $table) {
            //
            $table->dropColumn('principal_component');
            $table->dropColumn('interest_component');
            $table->dropColumn('balance');
            $table->dropColumn('loan_instance');
            $table->dropColumn('due_total');
            $table->dropColumn('pr');
            $table->dropColumn('sanchay_due');
            $table->dropColumn('lp_pal');
        });
    }
};

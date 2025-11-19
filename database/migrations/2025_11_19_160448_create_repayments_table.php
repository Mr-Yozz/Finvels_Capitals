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
            $table->string('due_instance')->nullable();

            $table->decimal('member_adv', 10, 2)->nullable()->default(0);
            $table->decimal('due_disb', 10, 2)->nullable()->default(0);
            $table->string('spouse_kyc')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repayments', function (Blueprint $table) {
            //
        });
    }
};

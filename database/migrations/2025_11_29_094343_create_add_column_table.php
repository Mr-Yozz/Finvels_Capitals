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
            // $table->unsignedBigInteger('loan_request_id')->nullable()->after('id');
            // $table->foreign('loan_request_id')->references('id')->on('loan_requests')->onDelete('set null');
            $table->decimal('weekly_emi', 15, 2)->nullable()->after('monthly_emi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            //
            // $table->dropForeign(['loan_request_id']);
            // $table->dropColumn('loan_request_id');
            $table->dropColumn('weekly_emi');
        });
    }
};

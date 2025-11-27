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
        Schema::table('notifications', function (Blueprint $table) {
            //
            $table->string('title')->nullable()->change();
            $table->text('message')->nullable()->change();
            $table->string('notifiable_id')->nullable()->change();
            $table->text('loan_id')->nullable()->change();
            $table->json('data')->nullable()->after('type'); // Add the data column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            //
            $table->dropColumn('data');
        });
    }
};

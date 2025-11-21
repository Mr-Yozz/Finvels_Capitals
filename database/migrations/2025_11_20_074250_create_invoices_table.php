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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_id')->index();
            $table->string('invoice_no')->unique();
            $table->date('invoice_date')->nullable();
            $table->decimal('loan_amount', 14, 2)->default(0);
            $table->decimal('processing_fee', 14, 2)->default(0);
            $table->decimal('insurance_amount', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('loan_id')->references('id')->on('loans')->onDelete('cascade');
        });

        // optional invoice lines / installments (if you want to store schedule rows)
        Schema::create('invoice_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id')->index();
            $table->integer('inst_no')->nullable();
            $table->date('due_date')->nullable();
            $table->decimal('principal', 14, 2)->default(0);
            $table->decimal('interest', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->decimal('prin_os', 14, 2)->default(0);
            $table->string('km_signature')->nullable();
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('invoice_lines');
    }
};

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
        Schema::create('loanrequests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('branch_id');
            $table->string('product_name');
            $table->string('spousename');
            $table->string('moratorium')->nullable();
            $table->string('purpose');
            $table->string('repayment_frequency');
            $table->decimal('insurance_amount', 10, 2);
            $table->decimal('principal', 10, 2);
            $table->decimal('interest_rate', 5, 2);
            $table->integer('tenure_months');
            $table->date('disbursed_at')->nullable();
            $table->string('status')->default('pending'); // pending/approved/rejected
            $table->enum('is_approved', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('created_by'); // manager
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loanrequests');
    }
};

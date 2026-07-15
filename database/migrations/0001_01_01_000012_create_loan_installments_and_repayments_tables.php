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
        Schema::create('loan_installments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('loan_id');
            $table->integer('installment_number');
            $table->date('due_date');
            $table->decimal('principal_amount', 20, 2);
            $table->decimal('interest_amount', 20, 2);
            $table->decimal('penalty_amount', 20, 2)->default(0);
            $table->decimal('total_amount', 20, 2);
            $table->string('status')->default('pending'); // pending, paid, overdue, waived
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('loan_id')->references('id')->on('loan_requests')->onDelete('cascade');
        });

        Schema::create('loan_repayments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('loan_id');
            $table->uuid('installment_id');
            $table->decimal('amount_paid', 20, 2);
            $table->date('payment_date');
            $table->timestamps();

            $table->foreign('loan_id')->references('id')->on('loan_requests')->onDelete('cascade');
            $table->foreign('installment_id')->references('id')->on('loan_installments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_repayments');
        Schema::dropIfExists('loan_installments');
    }
};

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
        Schema::create('loan_agreements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('loan_id')->unique();
            $table->string('agreement_number', 100)->unique();
            $table->string('file_path', 500)->nullable();
            $table->string('status')->default('waiting_signature'); // waiting_signature, signed, active
            $table->timestamp('borrower_signed_at')->nullable();
            $table->timestamp('signed_at')->nullable(); // Fully signed timestamp
            $table->timestamps();

            $table->foreign('loan_id')->references('id')->on('loan_requests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_agreements');
    }
};

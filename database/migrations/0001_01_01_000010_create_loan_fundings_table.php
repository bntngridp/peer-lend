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
        Schema::create('loan_fundings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('loan_id');
            $table->uuid('lender_id');
            $table->decimal('amount', 20, 2);
            $table->decimal('percentage', 5, 2); // lender's share %
            $table->string('status')->default('active'); // active, refunded
            $table->timestamps();

            $table->foreign('loan_id')->references('id')->on('loan_requests')->onDelete('cascade');
            $table->foreign('lender_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_fundings');
    }
};

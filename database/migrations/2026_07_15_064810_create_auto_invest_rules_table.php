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
        Schema::create('auto_invest_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('lender_id')->unique(); // One rule config per lender
            $table->boolean('is_active')->default(false);
            $table->string('min_grade', 2)->default('D');
            $table->string('max_grade', 2)->default('A');
            $table->decimal('max_allocation_per_loan', 15, 2)->default(1000000.00);
            $table->decimal('max_ltv', 5, 2)->default(80.00);
            $table->timestamps();

            $table->foreign('lender_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_invest_rules');
    }
};

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
        Schema::create('loan_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('borrower_id');
            $table->foreignId('category_id')->constrained('loan_categories')->onDelete('restrict');
            $table->decimal('amount', 20, 2);
            $table->decimal('interest_rate', 5, 2); // Annual rate %
            $table->integer('duration'); // number of installments (e.g. 12)
            $table->string('tenor_type')->default('monthly'); // monthly, weekly
            $table->string('purpose', 500);
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('restrict');
            
            // DeFi Crypto Collateral Integration
            $table->foreignId('collateral_currency_id')->nullable()->constrained('currencies')->onDelete('restrict');
            $table->decimal('collateral_amount', 20, 8)->default(0);
            $table->decimal('initial_ltv', 5, 2)->default(0);
            $table->decimal('current_ltv', 5, 2)->default(0);
            $table->decimal('liquidation_ltv', 5, 2)->default(80.00);
            $table->decimal('liquidation_price', 20, 8)->default(0);
            
            $table->text('description')->nullable();
            $table->string('risk_grade', 10)->nullable(); // A, B, C, D
            $table->string('status')->default('draft'); // draft, pending, open_funding, funded, active, completed, default, cancelled
            $table->decimal('funded_percentage', 5, 2)->default(0);
            
            $table->uuid('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('funded_at')->nullable();
            $table->timestamp('disbursed_at')->nullable();
            $table->timestamps();

            $table->foreign('borrower_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_requests');
    }
};

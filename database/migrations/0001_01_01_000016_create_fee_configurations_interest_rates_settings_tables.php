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
        Schema::create('fee_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // platform_fee, origination_fee, withdrawal_fee, penalty_rate
            $table->decimal('value', 10, 4);
            $table->string('value_type')->default('percentage'); // percentage, fixed
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('interest_rates', function (Blueprint $table) {
            $table->id();
            $table->string('risk_grade', 10)->unique(); // A, B, C, D
            $table->decimal('min_rate', 5, 2);
            $table->decimal('max_rate', 5, 2);
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('interest_rates');
        Schema::dropIfExists('fee_configurations');
    }
};

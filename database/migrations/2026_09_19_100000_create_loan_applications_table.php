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
        Schema::create('loan_applications', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name')->index();
            $table->string('loan_type')->index();
            $table->decimal('loan_amount', 15, 2);
            $table->unsignedInteger('tenor_months');
            $table->decimal('monthly_income', 15, 2);
            $table->decimal('monthly_installment', 15, 2);
            $table->decimal('interest_rate_monthly', 5, 4)->default(0.0090); // 0.9% flat per month
            $table->string('status')->default('pending')->index();
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('actioned_at')->nullable();
            $table->string('actioned_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_applications');
    }
};

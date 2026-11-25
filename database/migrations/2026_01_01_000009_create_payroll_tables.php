<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name', 30); // e.g. 2026-01
            $table->date('start_date');
            $table->date('end_date');
            $table->date('payment_date')->nullable();
            $table->string('status', 30)->default('OPEN')->index();
            $table->timestamps();
            $table->unique(['start_date', 'end_date']);
            $table->unique('name');
        });

        Schema::create('payroll_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_period_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('run_number')->default(1);
            $table->string('status', 30)->default('DRAFT')->index();
            $table->decimal('total_gross', 14, 2)->default(0);
            $table->decimal('total_deductions', 14, 2)->default(0);
            $table->decimal('total_net', 14, 2)->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['payroll_period_id', 'run_number']);
            $table->index(['payroll_period_id', 'status']);
        });

        Schema::create('payroll_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_run_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->restrictOnDelete();
            $table->string('employee_code', 40);
            $table->string('employee_name', 210);
            $table->unsignedSmallInteger('working_days')->default(0);
            $table->decimal('present_days', 7, 2)->default(0);
            $table->decimal('paid_leave_days', 7, 2)->default(0);
            $table->decimal('unpaid_leave_days', 7, 2)->default(0);
            $table->decimal('lop_days', 7, 2)->default(0);
            $table->decimal('gross_earnings', 14, 2)->default(0);
            $table->decimal('total_deductions', 14, 2)->default(0);
            $table->decimal('net_pay', 14, 2)->default(0);
            $table->string('status', 30)->default('DRAFT')->index();
            $table->json('calculation_snapshot')->nullable();
            $table->timestamps();
            $table->unique(['payroll_run_id', 'employee_id']);
            $table->index(['employee_id', 'created_at']);
        });

        Schema::create('payroll_item_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('salary_component_id')->nullable()->constrained()->nullOnDelete();
            $table->string('component_name', 100);
            $table->string('component_code', 40);
            $table->string('category', 30);
            $table->decimal('amount', 14, 2);
            $table->boolean('is_taxable')->default(true);
            $table->json('calculation_details')->nullable();
            $table->timestamps();
            $table->index(['payroll_item_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_item_components');
        Schema::dropIfExists('payroll_items');
        Schema::dropIfExists('payroll_runs');
        Schema::dropIfExists('payroll_periods');
    }
};

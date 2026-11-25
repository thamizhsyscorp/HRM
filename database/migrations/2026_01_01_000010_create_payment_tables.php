<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employee_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('account_holder_name', 150);
            $table->string('bank_name', 120);
            $table->text('account_number_encrypted');
            $table->string('ifsc_code', 11);
            $table->string('branch_name', 120)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
            $table->index(['employee_id', 'is_primary']);
        });

        Schema::create('salary_advances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->decimal('requested_amount', 14, 2);
            $table->decimal('approved_amount', 14, 2)->nullable();
            $table->decimal('repaid_amount', 14, 2)->default(0);
            $table->unsignedSmallInteger('installments')->default(1);
            $table->string('status', 30)->default('PENDING')->index();
            $table->text('reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('requested_at');
            $table->date('approved_at')->nullable();
            $table->timestamps();
            $table->index(['employee_id', 'status']);
        });

        Schema::create('payroll_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_item_id')->constrained()->restrictOnDelete();
            $table->foreignId('employee_bank_account_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 14, 2);
            $table->string('payment_method', 30)->default('BANK_TRANSFER');
            $table->string('transaction_reference', 120)->nullable()->index();
            $table->date('paid_on')->nullable();
            $table->string('status', 30)->default('PENDING')->index();
            $table->text('remarks')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['payroll_item_id', 'status']);
        });

        Schema::create('payslips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_item_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('payslip_number', 60)->unique();
            $table->string('file_path')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payslips');
        Schema::dropIfExists('payroll_payments');
        Schema::dropIfExists('salary_advances');
        Schema::dropIfExists('employee_bank_accounts');
    }
};

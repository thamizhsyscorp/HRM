<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PayrollItem extends Model
{
    protected $fillable = ['payroll_run_id', 'employee_id', 'employee_code', 'employee_name', 'working_days', 'present_days', 'paid_leave_days', 'unpaid_leave_days', 'lop_days', 'gross_earnings', 'total_deductions', 'net_pay', 'status', 'calculation_snapshot'];
    protected function casts(): array
    {
        return ['present_days' => 'decimal:2', 'paid_leave_days' => 'decimal:2', 'unpaid_leave_days' => 'decimal:2', 'lop_days' => 'decimal:2', 'gross_earnings' => 'decimal:2', 'total_deductions' => 'decimal:2', 'net_pay' => 'decimal:2', 'calculation_snapshot' => 'array'];
    }
    public function payrollRun(): BelongsTo { return $this->belongsTo(PayrollRun::class); }
    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
    public function components(): HasMany { return $this->hasMany(PayrollItemComponent::class); }
    public function payment(): HasMany { return $this->hasMany(PayrollPayment::class); }
    public function payslip(): HasOne { return $this->hasOne(Payslip::class); }
}

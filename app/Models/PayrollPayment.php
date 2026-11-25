<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollPayment extends Model
{
    protected $fillable = ['payroll_item_id', 'employee_bank_account_id', 'amount', 'payment_method', 'transaction_reference', 'paid_on', 'status', 'remarks', 'recorded_by'];
    protected function casts(): array { return ['amount' => 'decimal:2', 'paid_on' => 'date']; }
    public function payrollItem(): BelongsTo { return $this->belongsTo(PayrollItem::class); }
    public function bankAccount(): BelongsTo { return $this->belongsTo(EmployeeBankAccount::class, 'employee_bank_account_id'); }
    public function recorder(): BelongsTo { return $this->belongsTo(User::class, 'recorded_by'); }
}

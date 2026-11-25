<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeBankAccount extends Model
{
    protected $fillable = ['employee_id', 'account_holder_name', 'bank_name', 'account_number_encrypted', 'ifsc_code', 'branch_name', 'is_primary', 'is_verified'];
    protected function casts(): array { return ['is_primary' => 'boolean', 'is_verified' => 'boolean']; }
    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
    public function payrollPayments(): HasMany { return $this->hasMany(PayrollPayment::class); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryAdvance extends Model
{
    protected $fillable = ['employee_id', 'requested_amount', 'approved_amount', 'repaid_amount', 'installments', 'status', 'reason', 'approved_by', 'requested_at', 'approved_at'];
    protected function casts(): array { return ['requested_amount' => 'decimal:2', 'approved_amount' => 'decimal:2', 'repaid_amount' => 'decimal:2', 'requested_at' => 'date', 'approved_at' => 'date']; }
    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
    public function approver(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
}

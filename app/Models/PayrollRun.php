<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollRun extends Model
{
    protected $fillable = ['payroll_period_id', 'run_number', 'status', 'total_gross', 'total_deductions', 'total_net', 'created_by', 'approved_by', 'approved_at', 'processed_at', 'notes'];
    protected function casts(): array { return ['total_gross' => 'decimal:2', 'total_deductions' => 'decimal:2', 'total_net' => 'decimal:2', 'approved_at' => 'datetime', 'processed_at' => 'datetime']; }
    public function period(): BelongsTo { return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id'); }
    public function items(): HasMany { return $this->hasMany(PayrollItem::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function approver(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
}

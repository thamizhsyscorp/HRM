<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollItemComponent extends Model
{
    protected $fillable = ['payroll_item_id', 'salary_component_id', 'component_name', 'component_code', 'category', 'amount', 'is_taxable', 'calculation_details'];
    protected function casts(): array { return ['amount' => 'decimal:2', 'is_taxable' => 'boolean', 'calculation_details' => 'array']; }
    public function payrollItem(): BelongsTo { return $this->belongsTo(PayrollItem::class); }
    public function salaryComponent(): BelongsTo { return $this->belongsTo(SalaryComponent::class); }
}

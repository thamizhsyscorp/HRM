<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSalaryComponent extends Model
{
    protected $fillable = ['employee_id', 'salary_structure_id', 'salary_component_id', 'amount', 'percentage', 'effective_from', 'effective_to'];
    protected function casts(): array { return ['amount' => 'decimal:2', 'percentage' => 'decimal:4', 'effective_from' => 'date', 'effective_to' => 'date']; }
    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
    public function salaryStructure(): BelongsTo { return $this->belongsTo(SalaryStructure::class); }
    public function salaryComponent(): BelongsTo { return $this->belongsTo(SalaryComponent::class); }
}

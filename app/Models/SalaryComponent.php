<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalaryComponent extends Model
{
    protected $fillable = ['name', 'code', 'category', 'calculation_type', 'is_taxable', 'is_statutory', 'is_active'];
    protected function casts(): array { return ['is_taxable' => 'boolean', 'is_statutory' => 'boolean', 'is_active' => 'boolean']; }
    public function employeeAssignments(): HasMany { return $this->hasMany(EmployeeSalaryComponent::class); }
    public function payrollItemComponents(): HasMany { return $this->hasMany(PayrollItemComponent::class); }
}

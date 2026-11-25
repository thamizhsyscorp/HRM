<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Department extends Model
{
    protected $fillable = ['name', 'code', 'description', 'manager_employee_id', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function employees(): HasMany { return $this->hasMany(Employee::class); }
    public function designations(): HasMany { return $this->hasMany(Designation::class); }
    public function manager(): BelongsTo { return $this->belongsTo(Employee::class, 'manager_employee_id'); }
}

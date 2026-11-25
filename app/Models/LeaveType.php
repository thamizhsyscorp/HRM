<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveType extends Model
{
    protected $fillable = ['name', 'code', 'annual_quota', 'is_paid', 'requires_approval', 'is_active'];
    protected function casts(): array { return ['annual_quota' => 'decimal:2', 'is_paid' => 'boolean', 'requires_approval' => 'boolean', 'is_active' => 'boolean']; }
    public function requests(): HasMany { return $this->hasMany(LeaveRequest::class); }
}

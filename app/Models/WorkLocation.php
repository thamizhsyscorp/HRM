<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkLocation extends Model
{
    protected $fillable = ['name', 'code', 'address', 'latitude', 'longitude', 'allowed_radius_meters', 'geofence_enabled', 'is_active'];
    protected function casts(): array
    {
        return ['latitude' => 'decimal:7', 'longitude' => 'decimal:7', 'geofence_enabled' => 'boolean', 'is_active' => 'boolean'];
    }
    public function employees(): HasMany { return $this->hasMany(Employee::class); }
}

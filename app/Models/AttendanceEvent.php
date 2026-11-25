<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceEvent extends Model
{
    protected $fillable = ['attendance_id', 'employee_id', 'event_type', 'recorded_at', 'photo_path', 'latitude', 'longitude', 'location_accuracy_meters', 'distance_from_office_meters', 'within_geofence', 'source', 'created_by', 'remarks'];
    protected function casts(): array
    {
        return ['recorded_at' => 'datetime', 'latitude' => 'decimal:7', 'longitude' => 'decimal:7', 'location_accuracy_meters' => 'decimal:2', 'within_geofence' => 'boolean'];
    }
    public function attendance(): BelongsTo { return $this->belongsTo(Attendance::class); }
    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}

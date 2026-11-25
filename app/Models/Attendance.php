<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attendance extends Model
{
    protected $fillable = ['employee_id', 'attendance_date', 'first_check_in_at', 'last_check_out_at', 'worked_minutes', 'late_minutes', 'early_leave_minutes', 'status', 'is_manual', 'admin_remarks', 'modified_by'];
    protected function casts(): array
    {
        return ['attendance_date' => 'date', 'first_check_in_at' => 'datetime', 'last_check_out_at' => 'datetime', 'is_manual' => 'boolean'];
    }
    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
    public function events(): HasMany { return $this->hasMany(AttendanceEvent::class); }
    public function modifier(): BelongsTo { return $this->belongsTo(User::class, 'modified_by'); }

    /**
     * Get the GPS coordinates where attendance was registered.
     */
    public function getRegisteredCoordinatesAttribute(): array
    {
        $events = $this->relationLoaded('events')
            ? $this->events
            : $this->events()->whereNotNull('latitude')->whereNotNull('longitude')->get();

        $event = $events->whereNotNull('latitude')->whereNotNull('longitude')->sortByDesc('recorded_at')->first();
        $lat = $event?->latitude ?? $this->employee?->workLocation?->latitude ?? '12.9715987';
        $lng = $event?->longitude ?? $this->employee?->workLocation?->longitude ?? '80.2452345';

        return [
            'latitude' => (string) $lat,
            'longitude' => (string) $lng,
        ];
    }

    /**
     * Get Google Maps URL pointing to the registered attendance GPS coordinates.
     */
    public function getMapsUrlAttribute(): string
    {
        $coords = $this->registered_coordinates;
        return "https://www.google.com/maps?q={$coords['latitude']},{$coords['longitude']}";
    }
}

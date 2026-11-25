<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'employee_code', 'first_name', 'last_name', 'work_email', 'personal_email',
        'phone', 'alternate_phone', 'date_of_birth', 'gender', 'address', 'profile_photo_path',
        'department_id', 'designation_id', 'manager_id', 'work_location_id', 'joining_date',
        'employment_end_date', 'employment_status', 'employment_type', 'emergency_contact_name',
        'emergency_contact_phone', 'notes',
    ];

    protected function casts(): array
    {
        return ['date_of_birth' => 'date', 'joining_date' => 'date', 'employment_end_date' => 'date'];
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function department(): BelongsTo { return $this->belongsTo(Department::class); }
    public function designation(): BelongsTo { return $this->belongsTo(Designation::class); }
    public function manager(): BelongsTo { return $this->belongsTo(self::class, 'manager_id'); }
    public function directReports(): HasMany { return $this->hasMany(self::class, 'manager_id'); }
    public function workLocation(): BelongsTo { return $this->belongsTo(WorkLocation::class); }
    public function attendances(): HasMany { return $this->hasMany(Attendance::class); }
    public function attendanceEvents(): HasMany { return $this->hasMany(AttendanceEvent::class); }
    public function leaveRequests(): HasMany { return $this->hasMany(LeaveRequest::class); }
    public function salaryComponents(): HasMany { return $this->hasMany(EmployeeSalaryComponent::class); }
    public function bankAccounts(): HasMany { return $this->hasMany(EmployeeBankAccount::class); }
    public function salaryAdvances(): HasMany { return $this->hasMany(SalaryAdvance::class); }
    public function payrollItems(): HasMany { return $this->hasMany(PayrollItem::class); }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.($this->last_name ?? ''));
    }
}

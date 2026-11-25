<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    protected $fillable = ['employee_id', 'leave_type_id', 'start_date', 'end_date', 'total_days', 'reason', 'status', 'reviewed_by', 'reviewed_at', 'review_remarks', 'attachment_path'];
    protected function casts(): array { return ['start_date' => 'date', 'end_date' => 'date', 'total_days' => 'decimal:2', 'reviewed_at' => 'datetime']; }
    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
    public function leaveType(): BelongsTo { return $this->belongsTo(LeaveType::class); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payslip extends Model
{
    protected $fillable = ['payroll_item_id', 'payslip_number', 'file_path', 'generated_at'];
    protected function casts(): array { return ['generated_at' => 'datetime']; }
    public function payrollItem(): BelongsTo { return $this->belongsTo(PayrollItem::class); }
}

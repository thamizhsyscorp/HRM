<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyQrCode extends Model
{
    use HasFactory;

    protected $table = 'daily_qr_codes';

    protected $fillable = [
        'qr_date',
        'token',
        'version',
        'expires_at',
        'generated_by',
        'is_active',
    ];

    protected $casts = [
        'qr_date' => 'date',
        'expires_at' => 'datetime',
        'version' => 'integer',
        'is_active' => 'boolean',
    ];

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    /**
     * Check if this QR code is currently active and not yet expired (before 12:00 AM).
     */
    public function isCurrentlyValid(): bool
    {
        return $this->is_active && Carbon::now()->lt($this->expires_at);
    }
}

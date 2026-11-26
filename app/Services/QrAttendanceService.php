<?php

namespace App\Services;

use App\Models\DailyQrCode;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrAttendanceService
{
    /**
     * Get or automatically generate the active Daily QR record for a given date.
     * Automatically expires at 12:00 AM (midnight) of that day.
     */
    public static function getOrCreateDailyQr(?string $date = null): DailyQrCode
    {
        $date = $date ?? Carbon::today()->toDateString();
        $targetDate = Carbon::parse($date)->toDateString();

        $qr = DailyQrCode::whereDate('qr_date', $targetDate)
            ->where('is_active', true)
            ->latest('id')
            ->first();

        // Return if active and not yet expired
        if ($qr && Carbon::now()->lt($qr->expires_at)) {
            return $qr;
        }

        // If expired or not found, automatically generate a new one
        $lastVersion = DailyQrCode::whereDate('qr_date', $targetDate)->max('version') ?? 0;
        $version = $lastVersion + 1;
        $token = 'qr_' . str_replace('-', '', $targetDate) . '_v' . $version . '_' . bin2hex(random_bytes(16));
        $expiresAt = Carbon::parse($targetDate)->endOfDay(); // 11:59:59 PM / 12:00 AM midnight

        // Deactivate older entries for this date
        DailyQrCode::whereDate('qr_date', $targetDate)->update(['is_active' => false]);

        return DailyQrCode::create([
            'qr_date' => $targetDate,
            'token' => $token,
            'version' => $version,
            'expires_at' => $expiresAt,
            'is_active' => true,
        ]);
    }

    /**
     * Force-regenerate a fresh QR code for a given date (for HR / Super Admin).
     */
    public static function regenerateDailyQr(?string $date = null, ?int $userId = null): DailyQrCode
    {
        $date = $date ?? Carbon::today()->toDateString();
        $targetDate = Carbon::parse($date)->toDateString();

        // Deactivate all existing codes for this date
        DailyQrCode::whereDate('qr_date', $targetDate)->update(['is_active' => false]);

        $lastVersion = DailyQrCode::whereDate('qr_date', $targetDate)->max('version') ?? 0;
        $version = $lastVersion + 1;
        $token = 'qr_' . str_replace('-', '', $targetDate) . '_v' . $version . '_' . bin2hex(random_bytes(16));
        $expiresAt = Carbon::parse($targetDate)->endOfDay();

        return DailyQrCode::create([
            'qr_date' => $targetDate,
            'token' => $token,
            'version' => $version,
            'expires_at' => $expiresAt,
            'generated_by' => $userId,
            'is_active' => true,
        ]);
    }

    /**
     * Rotate the Daily QR code upon a successful employee scan:
     * Disables the scanned code so it cannot be reused, and generates a fresh code.
     */
    public static function rotateDailyQr(?DailyQrCode $consumedQr = null, ?int $userId = null): DailyQrCode
    {
        $targetDate = $consumedQr ? $consumedQr->qr_date->toDateString() : Carbon::today()->toDateString();

        // 1. Disable existing active QR code(s) so they cannot be scanned again
        DailyQrCode::whereDate('qr_date', $targetDate)->update(['is_active' => false]);

        // 2. Generate a new QR code with an incremented version
        $lastVersion = DailyQrCode::whereDate('qr_date', $targetDate)->max('version') ?? 0;
        $version = $lastVersion + 1;
        $token = 'qr_' . str_replace('-', '', $targetDate) . '_v' . $version . '_' . bin2hex(random_bytes(16));
        $expiresAt = Carbon::parse($targetDate)->endOfDay();

        return DailyQrCode::create([
            'qr_date' => $targetDate,
            'token' => $token,
            'version' => $version,
            'expires_at' => $expiresAt,
            'generated_by' => $userId,
            'is_active' => true,
        ]);
    }

    /**
     * Get the active daily token for a given date.
     */
    public static function getDailyToken(?string $date = null): string
    {
        return self::getOrCreateDailyQr($date)->token;
    }

    /**
     * Extract token from scanned QR text (handles raw token, JSON, or URL query param).
     */
    public static function extractTokenFromScan(string $scannedText): string
    {
        $scannedText = trim($scannedText);

        // Check if JSON
        if (str_starts_with($scannedText, '{') && str_ends_with($scannedText, '}')) {
            $decoded = json_decode($scannedText, true);
            if (is_array($decoded) && !empty($decoded['token'])) {
                return (string) $decoded['token'];
            }
        }

        // Check if URL with token query parameter
        if (filter_var($scannedText, FILTER_VALIDATE_URL) || str_contains($scannedText, 'token=')) {
            $parts = parse_url($scannedText);
            if (!empty($parts['query'])) {
                parse_str($parts['query'], $query);
                if (!empty($query['token'])) {
                    return (string) $query['token'];
                }
            }
        }

        return $scannedText;
    }

    /**
     * Validate whether the token matches an active, unexpired QR code.
     */
    public static function validateToken(string $token, ?string $date = null): array
    {
        $extractedToken = self::extractTokenFromScan($token);

        $qr = DailyQrCode::where('token', $extractedToken)->first();

        // Fallback check against static HMAC token for backward compatibility
        if (!$qr) {
            $date = $date ?? Carbon::today()->toDateString();
            $key = config('app.key') ?: 'SyscorpDefaultSecretKey32CharsMin!';
            $staticHmac = hash_hmac('sha256', "HRM_DAILY_ATTENDANCE_{$date}", $key);
            if (hash_equals($staticHmac, $extractedToken)) {
                return [
                    'valid' => true,
                    'record' => null,
                ];
            }

            return [
                'valid' => false,
                'message' => 'Invalid QR code. Please scan today\'s official office QR code.',
            ];
        }

        if (!$qr->is_active) {
            return [
                'valid' => false,
                'message' => 'This QR code has already been scanned and expired. Please scan the newly generated QR code on the office screen.',
            ];
        }

        if (Carbon::now()->gte($qr->expires_at)) {
            return [
                'valid' => false,
                'message' => 'This QR code expired at 12:00 AM midnight. Please scan today\'s new active QR code.',
            ];
        }

        return [
            'valid' => true,
            'record' => $qr,
        ];
    }

    /**
     * Standard JSON payload encoded inside the QR code.
     */
    public static function getPayload(?string $date = null): array
    {
        $qr = self::getOrCreateDailyQr($date);

        return [
            'type' => 'HRM_ATTENDANCE',
            'date' => $qr->qr_date->toDateString(),
            'token' => $qr->token,
            'version' => $qr->version,
            'company' => config('app.name', 'Syscorp HRM'),
            'expires_at' => $qr->expires_at->toIso8601String(),
            'expires_at_human' => '12:00 AM (' . $qr->expires_at->format('M d, Y h:i A') . ')',
        ];
    }

    /**
     * Generate an SVG string representation of the QR code.
     */
    public static function generateSvg(?string $date = null, int $size = 280): string
    {
        $qr = self::getOrCreateDailyQr($date);

        // High-contrast, maximum readability QR code with clean margin.
        // Encoding the clean token directly produces larger, clearer modules (29x29 vs 53x53)
        // that scan instantly on webcams and phone cameras under any lighting.
        return (string) @QrCode::format('svg')
            ->size($size)
            ->margin(2)
            ->color(0, 0, 0) // Pure black for maximum optical contrast against white
            ->backgroundColor(255, 255, 255)
            ->generate($qr->token);
    }

    /**
     * Compute Haversine distance in meters between two GPS coordinates.
     */
    public static function calculateDistanceMeters(float $lat1, float $lon1, float $lat2, float $lon2): int
    {
        $earthRadius = 6371000; // in meters
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return (int) round($earthRadius * $c);
    }
}

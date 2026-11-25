<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceEvent;
use App\Models\AuditLog;
use App\Models\DailyQrCode;
use App\Models\WorkLocation;
use App\Services\QrAttendanceService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class QrAttendanceController extends Controller
{
    /**
     * Dedicated API endpoint returning today's QR code payload and token.
     */
    public function getDailyQr(Request $request): JsonResponse
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $payload = QrAttendanceService::getPayload($date);
        $svg = QrAttendanceService::generateSvg($date, (int) $request->input('size', 280));

        return response()->json([
            'status' => 'success',
            'data' => array_merge($payload, [
                'qr_svg' => $svg,
            ]),
        ]);
    }

    /**
     * Dedicated API endpoint returning raw SVG image for kiosk displays or digital signage.
     */
    public function getDailyQrImage(Request $request): Response
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $size = (int) $request->input('size', 320);
        $svg = QrAttendanceService::generateSvg($date, $size);

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'no-cache, private',
        ]);
    }

    /**
     * Full-screen Kiosk display view for office entrance tablet / monitor.
     */
    public function kiosk(Request $request): View
    {
        $today = Carbon::today()->toDateString();
        $qrRecord = QrAttendanceService::getOrCreateDailyQr($today);
        $payload = QrAttendanceService::getPayload($today);
        $qrSvg = QrAttendanceService::generateSvg($today, 320);
        $locations = WorkLocation::where('is_active', true)->get();

        return view('attendance.qr-kiosk', compact('today', 'qrRecord', 'payload', 'qrSvg', 'locations'));
    }

    /**
     * Regenerate a fresh Daily QR code for today (HR Manager & Super Admin).
     */
    public function regenerate(Request $request): JsonResponse|RedirectResponse
    {
        $user = Auth::user();
        if (!$user || (!$user->hasRole('super-admin') && !$user->hasRole('hr-manager'))) {
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized action. Only HR and Super Admin can regenerate daily QR.'], 403);
            }
            return back()->withErrors(['qr' => 'Only HR Managers and Super Admins can generate a new daily QR code.']);
        }

        $today = Carbon::today()->toDateString();
        $newQr = QrAttendanceService::regenerateDailyQr($today, $user->id);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'DAILY_QR_REGENERATED',
            'auditable_type' => DailyQrCode::class,
            'auditable_id' => $newQr->id,
            'new_values' => [
                'qr_date' => $newQr->qr_date->toDateString(),
                'version' => $newQr->version,
                'expires_at' => $newQr->expires_at->toDateTimeString(),
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Fresh daily QR code generated successfully (Version {$newQr->version}). Previous code expired.",
                'data' => QrAttendanceService::getPayload($today),
                'qr_svg' => QrAttendanceService::generateSvg($today, 320),
            ]);
        }

        return back()->with('success', "Fresh daily QR code generated (Version {$newQr->version}). Previous code has been revoked. Expires at 12:00 AM.");
    }

    /**
     * Handle QR-scanned check-in and check-out punches.
     */
    public function punch(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated user.',
            ], 401);
        }

        $employee = $user->employee;
        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'No active employee profile is linked to your account.',
            ], 422);
        }

        $validated = $request->validate([
            'qr_token' => ['required', 'string'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'accuracy' => ['nullable', 'numeric'],
            'action' => ['nullable', 'string', 'in:auto,check_in,check_out'],
        ]);

        $scannedToken = $validated['qr_token'];
        $today = Carbon::today();
        $todayString = $today->toDateString();

        // Validate token against active, unexpired database records
        $validation = QrAttendanceService::validateToken($scannedToken, $todayString);
        if (!$validation['valid']) {
            return response()->json([
                'status' => 'error',
                'message' => $validation['message'],
            ], 422);
        }

        $lat = (float) $validated['latitude'];
        $lng = (float) $validated['longitude'];
        $accuracy = isset($validated['accuracy']) && is_numeric($validated['accuracy']) ? (float) $validated['accuracy'] : null;

        // Check geofence if work location is assigned
        $workLocation = $employee->workLocation;
        $distanceFromOffice = null;
        $withinGeofence = true;
        $geofenceWarning = null;

        if ($workLocation && $workLocation->latitude && $workLocation->longitude) {
            $distanceFromOffice = QrAttendanceService::calculateDistanceMeters(
                $lat,
                $lng,
                (float) $workLocation->latitude,
                (float) $workLocation->longitude
            );

            if ($workLocation->geofence_enabled && $workLocation->allowed_radius_meters) {
                $allowed = (int) $workLocation->allowed_radius_meters;
                if ($distanceFromOffice > $allowed) {
                    $withinGeofence = false;
                    $geofenceWarning = "You are {$distanceFromOffice}m away from {$workLocation->name} (allowed: {$allowed}m).";
                }
            }
        }

        // Retrieve or initialize today's attendance record
        $attendance = Attendance::firstOrCreate(
            ['employee_id' => $employee->id, 'attendance_date' => $today],
            ['status' => 'PRESENT', 'is_manual' => false]
        );

        $action = $validated['action'] ?? 'auto';

        // Auto-detect action if not explicitly forced
        if ($action === 'auto') {
            if (!$attendance->first_check_in_at) {
                $action = 'check_in';
            } elseif ($attendance->first_check_in_at && !$attendance->last_check_out_at) {
                $action = 'check_out';
            } else {
                return response()->json([
                    'status' => 'info',
                    'message' => 'Your shift for today is already completed (' . round($attendance->worked_minutes / 60, 1) . ' hrs worked).',
                    'attendance' => $attendance,
                ]);
            }
        }

        $now = Carbon::now();

        if ($action === 'check_in') {
            if ($attendance->first_check_in_at && !$attendance->last_check_out_at) {
                return response()->json([
                    'status' => 'info',
                    'message' => 'You are already checked in for today at ' . Carbon::parse($attendance->first_check_in_at)->format('h:i A') . '.',
                    'attendance' => $attendance,
                ]);
            }

            $attendance->update([
                'first_check_in_at' => $now,
                'status' => 'PRESENT',
            ]);

            AttendanceEvent::create([
                'attendance_id' => $attendance->id,
                'employee_id' => $employee->id,
                'event_type' => 'CHECK_IN',
                'recorded_at' => $now,
                'latitude' => $lat,
                'longitude' => $lng,
                'location_accuracy_meters' => $accuracy,
                'distance_from_office_meters' => $distanceFromOffice,
                'within_geofence' => $withinGeofence,
                'source' => 'QR_CODE',
                'created_by' => $user->id,
                'remarks' => 'Scanned daily QR code at office' . ($geofenceWarning ? " ({$geofenceWarning})" : ''),
            ]);

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'ATTENDANCE_QR_CHECK_IN',
                'auditable_type' => Attendance::class,
                'auditable_id' => $attendance->id,
                'new_values' => [
                    'first_check_in_at' => $now->toDateTimeString(),
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'distance' => $distanceFromOffice,
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => $now,
            ]);

            $locationNote = $distanceFromOffice !== null ? " ({$distanceFromOffice}m from office)" : "";
            return response()->json([
                'status' => 'success',
                'action' => 'CHECK_IN',
                'time' => $now->format('h:i A'),
                'message' => 'Check-in verified successfully at ' . $now->format('h:i A') . $locationNote . '.',
                'within_geofence' => $withinGeofence,
                'distance_meters' => $distanceFromOffice,
            ]);
        }

        if ($action === 'check_out') {
            if (!$attendance->first_check_in_at) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You must check in first before checking out.',
                ], 422);
            }

            $firstCheckIn = Carbon::parse($attendance->first_check_in_at);
            $workedMinutes = max(0, $firstCheckIn->diffInMinutes($now));

            $attendance->update([
                'last_check_out_at' => $now,
                'worked_minutes' => $workedMinutes,
            ]);

            AttendanceEvent::create([
                'attendance_id' => $attendance->id,
                'employee_id' => $employee->id,
                'event_type' => 'CHECK_OUT',
                'recorded_at' => $now,
                'latitude' => $lat,
                'longitude' => $lng,
                'location_accuracy_meters' => $accuracy,
                'distance_from_office_meters' => $distanceFromOffice,
                'within_geofence' => $withinGeofence,
                'source' => 'QR_CODE',
                'created_by' => $user->id,
                'remarks' => 'Scanned daily QR code for departure' . ($geofenceWarning ? " ({$geofenceWarning})" : ''),
            ]);

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'ATTENDANCE_QR_CHECK_OUT',
                'auditable_type' => Attendance::class,
                'auditable_id' => $attendance->id,
                'new_values' => [
                    'last_check_out_at' => $now->toDateTimeString(),
                    'worked_minutes' => $workedMinutes,
                    'latitude' => $lat,
                    'longitude' => $lng,
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => $now,
            ]);

            $hours = round($workedMinutes / 60, 1);
            return response()->json([
                'status' => 'success',
                'action' => 'CHECK_OUT',
                'time' => $now->format('h:i A'),
                'hours_worked' => $hours,
                'message' => "Checked out successfully at " . $now->format('h:i A') . ". Total time worked today: {$hours} hrs.",
                'within_geofence' => $withinGeofence,
                'distance_meters' => $distanceFromOffice,
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Unknown action requested.',
        ], 400);
    }
}

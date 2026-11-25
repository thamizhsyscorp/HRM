<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HolidayController extends Controller
{
    /**
     * Display corporate holidays and annual calendar schedule.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('hr-manager')) {
            abort(403, 'Unauthorized. Only HR and Super Admin can manage holidays.');
        }

        $isSuperAdmin = $user->hasRole('super-admin');
        $isHr = $user->hasRole('hr-manager');

        $year = (int) $request->input('year', now()->year);
        $availableYears = [now()->year - 1, now()->year, now()->year + 1];

        $holidays = Holiday::whereYear('holiday_date', $year)
            ->orderBy('holiday_date')
            ->get();

        $today = Carbon::today();
        $totalHolidays = $holidays->count();
        $publicHolidays = $holidays->where('type', 'PUBLIC')->count();
        $optionalHolidays = $holidays->where('is_optional', true)->count();
        $upcomingHolidays = $holidays->filter(fn($h) => Carbon::parse($h->holiday_date)->gte($today))->count();

        return view('holidays.index', compact(
            'holidays',
            'year',
            'availableYears',
            'totalHolidays',
            'publicHolidays',
            'optionalHolidays',
            'upcomingHolidays',
            'isSuperAdmin',
            'isHr'
        ));
    }

    /**
     * Store a newly created holiday.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('hr-manager')) {
            abort(403, 'Unauthorized. Only HR and Super Admin can add holidays.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'holiday_date' => ['required', 'date', 'unique:holidays,holiday_date'],
            'type' => ['required', 'string', 'in:PUBLIC,COMPANY,OPTIONAL,FESTIVAL'],
            'is_optional' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $holiday = Holiday::create([
            'name' => $validated['name'],
            'holiday_date' => $validated['holiday_date'],
            'type' => $validated['type'],
            'is_optional' => $request->boolean('is_optional'),
            'description' => $validated['description'] ?? null,
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'HOLIDAY_CREATED',
            'auditable_type' => Holiday::class,
            'auditable_id' => $holiday->id,
            'new_values' => $holiday->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return back()->with('success', "Holiday '{$holiday->name}' on {$holiday->holiday_date->format('M d, Y')} added successfully.");
    }

    /**
     * Update the specified holiday.
     */
    public function update(Request $request, Holiday $holiday): RedirectResponse
    {
        $user = Auth::user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('hr-manager')) {
            abort(403, 'Unauthorized. Only HR and Super Admin can edit holidays.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'holiday_date' => ['required', 'date', 'unique:holidays,holiday_date,' . $holiday->id],
            'type' => ['required', 'string', 'in:PUBLIC,COMPANY,OPTIONAL,FESTIVAL'],
            'is_optional' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $holiday->update([
            'name' => $validated['name'],
            'holiday_date' => $validated['holiday_date'],
            'type' => $validated['type'],
            'is_optional' => $request->boolean('is_optional'),
            'description' => $validated['description'] ?? null,
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'HOLIDAY_UPDATED',
            'auditable_type' => Holiday::class,
            'auditable_id' => $holiday->id,
            'new_values' => $holiday->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return back()->with('success', "Holiday '{$holiday->name}' updated successfully.");
    }

    /**
     * Remove the specified holiday.
     */
    public function destroy(Request $request, Holiday $holiday): RedirectResponse
    {
        $user = Auth::user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('hr-manager')) {
            abort(403, 'Unauthorized. Only HR and Super Admin can remove holidays.');
        }

        $name = $holiday->name;
        $date = $holiday->holiday_date->format('M d, Y');

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'HOLIDAY_DELETED',
            'auditable_type' => Holiday::class,
            'auditable_id' => $holiday->id,
            'new_values' => ['name' => $name, 'date' => $date],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        $holiday->delete();

        return back()->with('success', "Holiday '{$name}' ({$date}) deleted successfully.");
    }

    /**
     * Download a sample CSV file for bulk holiday imports.
     */
    public function sample(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="sample_holidays.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $sampleData = [
            ['name', 'holiday_date', 'type', 'is_optional', 'description'],
            ["New Year's Day", '2026-01-01', 'PUBLIC', '0', 'Global celebration of the New Year'],
            ['Pongal / Makar Sankranti', '2026-01-14', 'FESTIVAL', '0', 'Harvest festival celebrated across Tamil Nadu & Puducherry'],
            ['Republic Day', '2026-01-26', 'PUBLIC', '0', 'National celebration of the Constitution of India'],
            ['Maha Shivratri', '2026-02-15', 'OPTIONAL', '1', 'Optional religious observance'],
            ['May Day / Labor Day', '2026-05-01', 'PUBLIC', '0', "International Workers' Day"],
            ['Independence Day', '2026-08-15', 'PUBLIC', '0', 'National Independence Day of India'],
            ['Gandhi Jayanti', '2026-10-02', 'PUBLIC', '0', 'Mahatma Gandhi birth anniversary'],
            ['Deepavali / Diwali', '2026-11-08', 'FESTIVAL', '0', 'Festival of Lights'],
            ['Company Anniversary', '2026-11-25', 'COMPANY', '0', 'Syscorp Annual Foundation Day'],
            ['Christmas Day', '2026-12-25', 'PUBLIC', '0', 'Christmas public holiday'],
        ];

        return response()->streamDownload(function () use ($sampleData) {
            $output = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel compatibility
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
            foreach ($sampleData as $row) {
                fputcsv($output, $row);
            }
            fclose($output);
        }, 'sample_holidays.csv', $headers);
    }

    /**
     * Bulk import holidays from an uploaded CSV file.
     */
    public function import(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('hr-manager')) {
            abort(403, 'Unauthorized. Only HR and Super Admin can import holidays.');
        }

        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        $handle = fopen($path, 'r');
        if (!$handle) {
            return back()->with('error', 'Unable to open the uploaded file.');
        }

        // Check and skip UTF-8 BOM if present
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return back()->with('error', 'The uploaded CSV file is empty.');
        }

        // Map header column indices flexibly
        $colMap = [];
        foreach ($header as $index => $col) {
            $cleanCol = strtolower(trim($col));
            if (in_array($cleanCol, ['name', 'holiday_name', 'title', 'holiday'])) {
                $colMap['name'] = $index;
            } elseif (in_array($cleanCol, ['holiday_date', 'date', 'holiday date', 'day'])) {
                $colMap['holiday_date'] = $index;
            } elseif (in_array($cleanCol, ['type', 'category', 'holiday_type'])) {
                $colMap['type'] = $index;
            } elseif (in_array($cleanCol, ['is_optional', 'optional', 'is optional', 'optional_leave'])) {
                $colMap['is_optional'] = $index;
            } elseif (in_array($cleanCol, ['description', 'desc', 'notes', 'remarks'])) {
                $colMap['description'] = $index;
            }
        }

        if (!isset($colMap['name']) || !isset($colMap['holiday_date'])) {
            fclose($handle);
            return back()->with('error', 'CSV must contain at least "name" and "holiday_date" (or "date") columns.');
        }

        $createdCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;
        $validTypes = ['PUBLIC', 'COMPANY', 'OPTIONAL', 'FESTIVAL'];

        while (($row = fgetcsv($handle)) !== false) {
            if (empty(array_filter($row, fn($v) => trim($v) !== ''))) {
                continue;
            }

            $name = isset($colMap['name']) ? trim($row[$colMap['name']] ?? '') : '';
            $rawDate = isset($colMap['holiday_date']) ? trim($row[$colMap['holiday_date']] ?? '') : '';
            $rawType = isset($colMap['type']) ? strtoupper(trim($row[$colMap['type']] ?? 'PUBLIC')) : 'PUBLIC';
            $rawOptional = isset($colMap['is_optional']) ? trim($row[$colMap['is_optional']] ?? '') : '';
            $description = isset($colMap['description']) ? trim($row[$colMap['description']] ?? '') : null;

            if (empty($name) || empty($rawDate)) {
                $skippedCount++;
                continue;
            }

            try {
                $date = Carbon::parse($rawDate)->toDateString();
            } catch (\Exception $e) {
                $skippedCount++;
                continue;
            }

            $type = in_array($rawType, $validTypes) ? $rawType : 'PUBLIC';
            $isOptional = in_array(strtolower($rawOptional), ['1', 'true', 'yes', 'y', 'optional']);
            if ($type === 'OPTIONAL') {
                $isOptional = true;
            }

            $existing = Holiday::where('holiday_date', $date)
                ->orWhere('holiday_date', 'like', $date . '%')
                ->first();
            if ($existing) {
                $existing->update([
                    'name' => $name,
                    'type' => $type,
                    'is_optional' => $isOptional,
                    'description' => $description ?: $existing->description,
                ]);
                $updatedCount++;
            } else {
                Holiday::create([
                    'name' => $name,
                    'holiday_date' => $date,
                    'type' => $type,
                    'is_optional' => $isOptional,
                    'description' => $description,
                ]);
                $createdCount++;
            }
        }

        fclose($handle);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'HOLIDAYS_IMPORTED',
            'auditable_type' => Holiday::class,
            'auditable_id' => null,
            'new_values' => ['created' => $createdCount, 'updated' => $updatedCount, 'skipped' => $skippedCount],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        $message = "Holidays import complete: {$createdCount} added, {$updatedCount} updated.";
        if ($skippedCount > 0) {
            $message .= " ({$skippedCount} invalid rows skipped)";
        }

        return back()->with('success', $message);
    }
}

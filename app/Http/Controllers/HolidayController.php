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
}

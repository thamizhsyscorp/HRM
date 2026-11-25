<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    /**
     * Display company announcements for all employees & HR/Admin.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $isSuperAdmin = $user->hasRole('super-admin');
        $isHr = $user->hasRole('hr-manager');

        $categoryFilter = $request->input('category');

        $query = Announcement::with('author')->orderByDesc('is_pinned')->latest();

        if ($categoryFilter) {
            $query->where('category', $categoryFilter);
        }

        $announcements = $query->paginate(12)->withQueryString();

        // Statistics
        $totalAnnouncements = Announcement::count();
        $urgentCount = Announcement::whereIn('priority', ['HIGH', 'URGENT'])->count();
        $policyCount = Announcement::where('category', 'POLICY')->count();
        $eventCount = Announcement::where('category', 'EVENT')->count();

        return view('announcements.index', compact(
            'announcements',
            'categoryFilter',
            'isSuperAdmin',
            'isHr',
            'totalAnnouncements',
            'urgentCount',
            'policyCount',
            'eventCount'
        ));
    }

    /**
     * Store and broadcast a new company announcement to all staff members.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('hr-manager')) {
            abort(403, 'Unauthorized. Only HR and Administrators can publish company announcements.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'category' => ['required', 'string', 'in:GENERAL,URGENT,EVENT,POLICY,HOLIDAY'],
            'priority' => ['required', 'string', 'in:LOW,MEDIUM,HIGH,URGENT'],
            'is_pinned' => ['nullable', 'boolean'],
        ]);

        $announcement = Announcement::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'created_by' => $user->id,
            'is_pinned' => (bool) $request->input('is_pinned', false),
        ]);

        // Broadcast notification to ALL active users
        $allUsers = User::all();
        $now = now();
        $notificationRecords = [];

        foreach ($allUsers as $recipient) {
            $notificationRecords[] = [
                'id' => (string) Str::uuid(),
                'type' => 'App\Notifications\CompanyAnnouncementNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $recipient->id,
                'data' => json_encode([
                    'announcement_id' => $announcement->id,
                    'title' => $announcement->title,
                    'content' => Str::limit($announcement->content, 140),
                    'category' => $announcement->category,
                    'priority' => $announcement->priority,
                    'author' => $user->name,
                    'created_at' => $now->toDateTimeString(),
                ]),
                'read_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($notificationRecords)) {
            DB::table('notifications')->insert($notificationRecords);
        }

        // Audit Log entry
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'ANNOUNCEMENT_BROADCASTED',
            'auditable_type' => Announcement::class,
            'auditable_id' => $announcement->id,
            'new_values' => [
                'title' => $announcement->title,
                'category' => $announcement->category,
                'priority' => $announcement->priority,
                'recipients_count' => count($allUsers),
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return back()->with('success', "Announcement '{$announcement->title}' has been successfully broadcasted to all " . count($allUsers) . " workforce personnel.");
    }

    /**
     * Delete an announcement.
     */
    public function destroy(Announcement $announcement): RedirectResponse
    {
        $user = Auth::user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('hr-manager')) {
            abort(403, 'Unauthorized. Only HR and Administrators can remove announcements.');
        }

        $title = $announcement->title;
        $announcement->delete();

        return back()->with('success', "Announcement '{$title}' was successfully deleted.");
    }
}

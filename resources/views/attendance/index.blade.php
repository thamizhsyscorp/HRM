@extends('layouts.app')

@section('title', ($isSuperAdmin || $isHr) ? 'Daily Workforce Attendance Logs' : 'My Personal Attendance Logs')
@section('page_title', ($isSuperAdmin || $isHr) ? 'Daily Workforce Attendance Logs' : 'My Personal Attendance Logs')

@section('content')
<div class="space-y-6">

    <!-- Top Filter & Search Bar -->
    <div class="bg-white rounded-2xl border border-slate-100 p-5 sm:p-6 shadow-md">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 pb-3 border-b border-slate-100">
            <div>
                <h2 class="text-sm sm:text-base font-extrabold text-slate-900">
                    {{ ($isSuperAdmin || $isHr) ? 'Workforce Attendance Roster (Today)' : 'My Monthly Attendance History' }}
                </h2>
                <p class="text-xs text-slate-400">
                    @if($isSuperAdmin || $isHr)
                        Punches logged for all employees across the organization today
                    @else
                        Everyday attendance history for {{ auth()->user()->name }} — {{ \Carbon\Carbon::parse($startDate)->format('M d') }} to {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                @if($isSuperAdmin || $isHr)
                    <form method="POST" action="{{ route('attendance.qr.regenerate') }}" onsubmit="return confirm('Generate a fresh QR code for today? The previous code will be expired.')" class="inline">
                        @csrf
                        <button type="submit" class="px-3 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 text-xs font-bold transition flex items-center gap-1 cursor-pointer" title="Generate New QR for today">
                            <span>🔄 New QR</span>
                        </button>
                    </form>
                    <a href="{{ route('attendance.qr-kiosk') }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-[#1e3a8a] text-xs font-bold border border-blue-200 shadow-2xs transition flex items-center gap-1.5" title="Open Office Daily QR Kiosk">
                        <span>Office QR Kiosk 📱</span>
                    </a>
                @else
                    <button type="button" onclick="openQrScanner()" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-2xs transition flex items-center gap-1.5 cursor-pointer">
                        <span>📷 Scan QR to Punch</span>
                    </button>
                @endif
                <a href="{{ route('attendance.calendar') }}" class="px-4 py-2 rounded-xl bg-[#1e3a8a] hover:bg-[#1e40af] text-white text-xs font-bold shadow-xs transition flex items-center gap-1.5">
                    <span>Monthly Calendar View 🗓️</span>
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('attendance.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 items-end">
            @if($isSuperAdmin || $isHr)
                <!-- HR: Single Attendance Date Filter (Defaults to Today) -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Attendance Date</label>
                    <input 
                        type="date" 
                        name="date" 
                        value="{{ $selectedDate }}" 
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white font-mono font-medium text-slate-900"
                    >
                </div>

                <!-- HR: Department Filter -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Department</label>
                    <select name="department_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900 font-medium">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $departmentId == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @else
                <!-- Employee: "This date to this date" Range Filter (2 Columns on Mobile) -->
                <div class="grid grid-cols-2 gap-2.5 sm:contents">
                    <div>
                        <label class="block text-[11px] sm:text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">From Date</label>
                        <input 
                            type="date" 
                            name="start_date" 
                            value="{{ $startDate }}" 
                            class="w-full px-3 py-2 sm:px-3.5 sm:py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white font-mono font-medium text-slate-900"
                        >
                    </div>

                    <div>
                        <label class="block text-[11px] sm:text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">To Date</label>
                        <input 
                            type="date" 
                            name="end_date" 
                            value="{{ $endDate }}" 
                            class="w-full px-3 py-2 sm:px-3.5 sm:py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white font-mono font-medium text-slate-900"
                        >
                    </div>
                </div>
            @endif

            <!-- Punch Status Filter -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Punch Status</label>
                <select name="status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900 font-medium">
                    <option value="">All Statuses</option>
                    <option value="PRESENT" {{ $statusFilter === 'PRESENT' ? 'selected' : '' }}>Present</option>
                    <option value="LATE" {{ $statusFilter === 'LATE' ? 'selected' : '' }}>Late</option>
                    <option value="HALF_DAY" {{ $statusFilter === 'HALF_DAY' ? 'selected' : '' }}>Half Day</option>
                    <option value="ABSENT" {{ $statusFilter === 'ABSENT' ? 'selected' : '' }}>Absent</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2.5 px-4 bg-[#1e3a8a] hover:bg-[#1e40af] text-white rounded-xl text-xs font-bold shadow-xs transition cursor-pointer">
                    {{ ($isSuperAdmin || $isHr) ? 'Filter Logs' : 'Filter Range' }}
                </button>
                <a href="{{ route('attendance.index') }}" class="py-2.5 px-3 border border-slate-300 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-bold transition text-center bg-white cursor-pointer">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Attendance Metric Summary Cards: 2 Columns on Mobile, 4 Columns on Desktop -->
    @if($isSuperAdmin || $isHr)
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4 lg:gap-5">
            <div class="bg-white p-3.5 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-100 shadow-2xs sm:shadow-md flex flex-col justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider block">Date Tracked</span>
                <div class="mt-1.5 sm:mt-2">
                    <span class="text-xs sm:text-xl font-extrabold text-slate-900 font-mono tracking-tight">{{ \Carbon\Carbon::parse($selectedDate)->format('M d, Y') }}</span>
                </div>
                <p class="mt-1 sm:mt-2 text-[10px] sm:text-[11px] text-slate-400 font-medium truncate sm:whitespace-normal">Daily roster for today</p>
            </div>

            <div class="bg-white p-3.5 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-100 shadow-2xs sm:shadow-md flex flex-col justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider block">Present Count</span>
                <div class="mt-1.5 sm:mt-2 flex items-baseline">
                    <span class="text-xl sm:text-3xl font-extrabold text-slate-900 font-mono">{{ $presentCount }}</span>
                    <span class="text-[10px] sm:text-xs text-[#1e3a8a] font-bold ml-1.5">Verified</span>
                </div>
                <p class="mt-1 sm:mt-2 text-[10px] sm:text-[11px] text-slate-400 font-medium truncate sm:whitespace-normal">Out of {{ $totalActiveEmployees }} personnel</p>
            </div>

            <div class="bg-white p-3.5 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-100 shadow-2xs sm:shadow-md flex flex-col justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider block">Active Office Sessions</span>
                <div class="mt-1.5 sm:mt-2 flex items-baseline">
                    <span class="text-xl sm:text-3xl font-extrabold text-slate-900 font-mono">{{ $activePunchesCount }}</span>
                    <span class="text-[10px] sm:text-xs text-emerald-700 font-bold ml-1.5">On Duty</span>
                </div>
                <p class="mt-1 sm:mt-2 text-[10px] sm:text-[11px] text-[#1e3a8a] font-semibold flex items-center gap-1 truncate sm:whitespace-normal">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse shrink-0"></span> Clocked in now
                </p>
            </div>

            <div class="bg-white p-3.5 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-100 shadow-2xs sm:shadow-md flex flex-col justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider block">Turnout Rate</span>
                <div class="mt-1.5 sm:mt-2">
                    <span class="text-xl sm:text-3xl font-extrabold text-slate-900 font-mono">
                        {{ $totalActiveEmployees > 0 ? round(($presentCount / $totalActiveEmployees) * 100, 1) : 0 }}%
                    </span>
                </div>
                <p class="mt-1 sm:mt-2 text-[10px] sm:text-[11px] text-slate-400 font-medium truncate sm:whitespace-normal">Overall turnout today</p>
            </div>
        </div>
    @else
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4 lg:gap-5">
            <div class="bg-white p-3.5 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-100 shadow-2xs sm:shadow-md flex flex-col justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider block">Tracking Window</span>
                <div class="mt-1.5 sm:mt-2">
                    <span class="text-[11px] sm:text-lg font-extrabold text-slate-900 font-mono tracking-tight leading-tight block">{{ \Carbon\Carbon::parse($startDate)->format('M d') }} — {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</span>
                </div>
                <p class="mt-1 sm:mt-2 text-[10px] sm:text-[11px] text-slate-400 font-medium truncate sm:whitespace-normal">Everyday attendance of this month</p>
            </div>

            <div class="bg-white p-3.5 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-100 shadow-2xs sm:shadow-md flex flex-col justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider block">Days Present</span>
                <div class="mt-1.5 sm:mt-2 flex items-baseline">
                    <span class="text-xl sm:text-3xl font-extrabold text-slate-900 font-mono">{{ $employeePresentCount }}</span>
                    <span class="text-[10px] sm:text-xs text-emerald-700 font-bold ml-1.5">Verified Days</span>
                </div>
                <p class="mt-1 sm:mt-2 text-[10px] sm:text-[11px] text-slate-400 font-medium truncate sm:whitespace-normal">Out of {{ $employeeDaysCount }} total recorded entries</p>
            </div>

            <div class="bg-white p-3.5 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-100 shadow-2xs sm:shadow-md flex flex-col justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider block">Total Hours Worked</span>
                <div class="mt-1.5 sm:mt-2 flex items-baseline">
                    <span class="text-xl sm:text-3xl font-extrabold text-[#1e3a8a] font-mono">{{ $employeeTotalHours }}</span>
                    <span class="text-[10px] sm:text-xs text-[#1e3a8a] font-bold ml-1">hrs</span>
                </div>
                <p class="mt-1 sm:mt-2 text-[10px] sm:text-[11px] text-slate-400 font-medium truncate sm:whitespace-normal">Cumulative worked duration</p>
            </div>

            <div class="bg-white p-3.5 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-100 shadow-2xs sm:shadow-md flex flex-col justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider block">Monthly Attendance Rate</span>
                <div class="mt-1.5 sm:mt-2">
                    <span class="text-xl sm:text-3xl font-extrabold text-slate-900 font-mono">{{ $employeeTurnoutRate }}%</span>
                </div>
                <p class="mt-1 sm:mt-2 text-[10px] sm:text-[11px] text-slate-400 font-medium truncate sm:whitespace-normal">Turnout rate in selected period</p>
            </div>
        </div>
    @endif

    <!-- Attendance Table -->
    <div class="bg-white rounded-2xl shadow-md border border-slate-100 overflow-hidden">
        <div class="p-3.5 sm:p-5 border-b border-slate-100 flex items-center justify-between bg-white">
            <div>
                <h3 class="text-sm font-extrabold text-slate-900">
                    {{ ($isSuperAdmin || $isHr) ? 'Daily Workforce Attendance Logs' : 'My Personal Attendance Logs' }}
                </h3>
                <p class="text-xs text-slate-400">
                    @if($isSuperAdmin || $isHr)
                        Punches logged for {{ \Carbon\Carbon::parse($selectedDate)->format('l, F j, Y') }}
                    @else
                        Everyday logs for {{ auth()->user()->name }} &bull; {{ \Carbon\Carbon::parse($startDate)->format('M d') }} to {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
                    @endif
                </p>
            </div>
            <span class="px-2.5 sm:px-3 py-1 text-[11px] sm:text-xs font-bold rounded-full bg-[#eff6ff] text-[#1e3a8a] border border-[#bfdbfe] shrink-0">
                {{ $attendances->count() }} Records Found
            </span>
        </div>

        <!-- Mobile Attendance Cards (Visible on screens < 640px) -->
        <div class="sm:hidden divide-y divide-slate-100">
            @forelse($attendances as $att)
                @php
                    $attDate = \Carbon\Carbon::parse($att->attendance_date);
                @endphp
                <div class="p-3.5 space-y-2.5 bg-white">
                    <!-- Top Row: Date / Employee Info & Status Badge -->
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-2.5">
                            @if($isSuperAdmin || $isHr)
                                <div class="w-8 h-8 rounded-full bg-[#eff6ff] text-[#1e3a8a] font-bold flex items-center justify-center text-xs shrink-0 border border-[#bfdbfe]">
                                    {{ strtoupper(substr($att->employee->first_name ?? 'E', 0, 1)) }}
                                </div>
                                <div>
                                    <span class="font-extrabold text-slate-900 text-xs block leading-tight">{{ $att->employee->full_name ?? 'Employee' }}</span>
                                    <span class="text-[10px] font-mono text-slate-400 block mt-0.5">
                                        {{ $att->employee->employee_code ?? '' }} &bull; {{ $att->employee->department->name ?? '—' }}
                                    </span>
                                </div>
                            @else
                                <div class="w-8 h-8 rounded-full bg-indigo-50 text-[#1e3a8a] font-mono font-extrabold flex items-center justify-center text-xs shrink-0 border border-indigo-200">
                                    {{ $attDate->format('d') }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-extrabold text-slate-900 text-xs font-mono leading-tight">{{ $attDate->format('M d, Y') }}</span>
                                        <span class="text-[10px] font-semibold text-slate-500">({{ $attDate->format('D') }})</span>
                                    </div>
                                    <span class="text-[10px] text-slate-400 block mt-0.5">{{ $attDate->isToday() ? 'Today' : $attDate->diffForHumans() }}</span>
                                </div>
                            @endif
                        </div>

                        <div>
                            @if($att->status === 'PRESENT')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-900 border border-emerald-300">
                                    PRESENT
                                </span>
                            @elseif($att->status === 'LATE')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-800 border border-amber-200">
                                    LATE ({{ $att->late_minutes }}m)
                                </span>
                            @elseif($att->status === 'HALF_DAY')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-800 border border-blue-200">
                                    HALF DAY
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $att->status }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Punch Details Grid: Check-In, Check-Out, Hours Worked -->
                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100 grid grid-cols-3 gap-2 text-center text-xs">
                        <div>
                            <span class="text-[9px] uppercase font-bold text-slate-400 block">Check-In</span>
                            <span class="font-mono font-bold text-slate-900 text-[11px] block mt-0.5">
                                {{ $att->first_check_in_at ? \Carbon\Carbon::parse($att->first_check_in_at)->format('h:i A') : '—' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[9px] uppercase font-bold text-slate-400 block">Check-Out</span>
                            <span class="font-mono text-slate-700 text-[11px] block mt-0.5">
                                {{ $att->last_check_out_at ? \Carbon\Carbon::parse($att->last_check_out_at)->format('h:i A') : ($att->first_check_in_at ? 'Active Duty' : '—') }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[9px] uppercase font-bold text-slate-400 block">Worked</span>
                            <span class="font-mono font-bold text-[#1e3a8a] text-[11px] block mt-0.5">
                                {{ round(($att->worked_minutes ?? 0) / 60, 1) }} hrs
                            </span>
                        </div>
                    </div>

                    <!-- Location & Actions -->
                    <div class="flex items-center justify-between text-xs pt-0.5">
                        <div class="flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span class="text-[11px] text-slate-600 font-medium">Verified Geofence</span>
                            @if($att->maps_url)
                                <a href="{{ $att->maps_url }}" target="_blank" rel="noopener noreferrer" class="text-[#1e3a8a] hover:underline text-[10px] font-bold">
                                    [Map 📍]
                                </a>
                            @endif
                        </div>

                        @if($isSuperAdmin || $isHr)
                            <button 
                                type="button" 
                                onclick="openEditAttendanceModal({{ $att->id }}, '{{ addslashes($att->employee->full_name ?? '') }}', '{{ $att->first_check_in_at ? \Carbon\Carbon::parse($att->first_check_in_at)->format('H:i') : '' }}', '{{ $att->last_check_out_at ? \Carbon\Carbon::parse($att->last_check_out_at)->format('H:i') : '' }}', '{{ $att->status }}', '{{ addslashes($att->admin_remarks ?? '') }}')"
                                class="px-2.5 py-1 rounded-lg bg-blue-50 text-[#1e3a8a] border border-blue-200 font-bold text-[11px] cursor-pointer"
                            >
                                Edit Time ✎
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-slate-400 text-xs">
                    No attendance records found for this period.
                </div>
            @endforelse
        </div>

        <!-- Desktop Table (Visible on screens >= 640px) -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50/90 border-b border-slate-200/80 text-slate-500 font-bold uppercase text-[11px] tracking-wider">
                    <tr>
                        @if($isSuperAdmin || $isHr)
                            <th class="py-3.5 px-6 text-xs font-bold tracking-wider whitespace-nowrap">Employee</th>
                            <th class="py-3.5 px-6 text-xs font-bold tracking-wider whitespace-nowrap">Department</th>
                            <th class="py-3.5 px-6 text-xs font-bold tracking-wider whitespace-nowrap">First Check-In</th>
                            <th class="py-3.5 px-6 text-xs font-bold tracking-wider whitespace-nowrap">Last Check-Out</th>
                            <th class="py-3.5 px-6 text-xs font-bold tracking-wider whitespace-nowrap">Hours Worked</th>
                            <th class="py-3.5 px-6 text-xs font-bold tracking-wider text-center whitespace-nowrap">Status</th>
                            <th class="py-3.5 px-6 text-xs font-bold tracking-wider whitespace-nowrap">Mode / Location</th>
                            <th class="py-3.5 px-6 text-xs font-bold tracking-wider text-right whitespace-nowrap">HR Action</th>
                        @else
                            <th class="py-3.5 px-6 text-xs font-bold tracking-wider whitespace-nowrap">Attendance Date</th>
                            <th class="py-3.5 px-6 text-xs font-bold tracking-wider whitespace-nowrap">Day of Week</th>
                            <th class="py-3.5 px-6 text-xs font-bold tracking-wider whitespace-nowrap">First Check-In</th>
                            <th class="py-3.5 px-6 text-xs font-bold tracking-wider whitespace-nowrap">Last Check-Out</th>
                            <th class="py-3.5 px-6 text-xs font-bold tracking-wider whitespace-nowrap">Hours Worked</th>
                            <th class="py-3.5 px-6 text-xs font-bold tracking-wider text-center whitespace-nowrap">Punch Status</th>
                            <th class="py-3.5 px-6 text-xs font-bold tracking-wider whitespace-nowrap">Mode / Location</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 text-xs">
                    @forelse($attendances as $att)
                        @php
                            $attDate = \Carbon\Carbon::parse($att->attendance_date);
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors cursor-pointer">
                            @if($isSuperAdmin || $isHr)
                                <td class="py-4 px-6 whitespace-nowrap align-middle">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-[#eff6ff] text-[#1e3a8a] font-bold flex items-center justify-center text-xs shrink-0 border border-[#bfdbfe]">
                                            {{ strtoupper(substr($att->employee->first_name ?? 'E', 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="font-extrabold text-slate-900 block leading-tight">{{ $att->employee->full_name ?? 'Employee' }}</span>
                                            <span class="text-[11px] font-mono text-slate-400 block mt-0.5">{{ $att->employee->employee_code ?? '' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap align-middle">
                                    <span class="text-slate-800 font-semibold block">{{ $att->employee->department->name ?? '—' }}</span>
                                    <span class="text-[11px] text-slate-400 block mt-0.5">{{ $att->employee->designation->name ?? '—' }}</span>
                                </td>
                            @else
                                <td class="py-4 px-6 whitespace-nowrap align-middle">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-indigo-50 text-[#1e3a8a] font-mono font-extrabold flex items-center justify-center text-xs shrink-0 border border-indigo-200">
                                            {{ $attDate->format('d') }}
                                        </div>
                                        <div>
                                            <span class="font-extrabold text-slate-900 block font-mono leading-tight">{{ $attDate->format('M d, Y') }}</span>
                                            <span class="text-[10px] text-slate-400 block mt-0.5 font-sans">{{ $attDate->isToday() ? 'Today' : $attDate->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap align-middle font-semibold {{ $attDate->isSunday() ? 'text-sky-600 font-bold' : 'text-slate-700' }}">
                                    {{ $attDate->format('l') }}
                                </td>
                            @endif

                            <td class="py-4 px-6 font-mono font-bold text-slate-900 whitespace-nowrap align-middle">
                                {{ $att->first_check_in_at ? \Carbon\Carbon::parse($att->first_check_in_at)->format('h:i A') : '—' }}
                            </td>
                            <td class="py-4 px-6 font-mono text-slate-600 whitespace-nowrap align-middle">
                                {{ $att->last_check_out_at ? \Carbon\Carbon::parse($att->last_check_out_at)->format('h:i A') : ($att->first_check_in_at ? 'Active Duty' : '—') }}
                            </td>
                            <td class="py-4 px-6 font-mono font-bold text-slate-900 whitespace-nowrap align-middle">
                                {{ round(($att->worked_minutes ?? 0) / 60, 1) }} hrs
                            </td>
                            <td class="py-4 px-6 text-center whitespace-nowrap align-middle">
                                @if($att->status === 'PRESENT')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-900 border border-emerald-300">
                                        PRESENT
                                    </span>
                                @elseif($att->status === 'LATE')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-800 border border-amber-200">
                                        LATE ({{ $att->late_minutes }}m)
                                    </span>
                                @elseif($att->status === 'HALF_DAY')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-800 border border-blue-200">
                                        HALF DAY
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $att->status }}
                                    </span>
                                @endif

                                @if($att->is_manual)
                                    <span class="block text-[10px] text-[#1e3a8a] font-semibold mt-0.5">Edited by HR</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap align-middle">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 text-xs text-slate-700 font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Verified Geofence
                                    </span>
                                    <a 
                                        href="{{ $att->maps_url }}" 
                                        target="_blank" 
                                        rel="noopener noreferrer" 
                                        class="inline-flex items-center justify-center p-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-[#1e3a8a] border border-blue-200 transition shadow-2xs group cursor-pointer" 
                                        title="View registered GPS location on Google Maps ({{ $att->registered_coordinates['latitude'] }}, {{ $att->registered_coordinates['longitude'] }})"
                                    >
                                        <svg class="w-3.5 h-3.5 text-[#1e3a8a] group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </a>
                                </div>
                                @if($att->admin_remarks)
                                    <span class="block text-[10px] text-slate-400 truncate max-w-[160px]" title="{{ $att->admin_remarks }}">{{ $att->admin_remarks }}</span>
                                @endif
                            </td>

                            <!-- HR EDIT ACTION -->
                            @if($isSuperAdmin || $isHr)
                                <td class="py-4 px-6 text-right whitespace-nowrap align-middle">
                                    <button 
                                        type="button" 
                                        onclick="openEditAttendanceModal({{ $att->id }}, '{{ addslashes($att->employee->full_name ?? '') }}', '{{ $att->first_check_in_at ? \Carbon\Carbon::parse($att->first_check_in_at)->format('H:i') : '' }}', '{{ $att->last_check_out_at ? \Carbon\Carbon::parse($att->last_check_out_at)->format('H:i') : '' }}', '{{ $att->status }}', '{{ addslashes($att->admin_remarks ?? '') }}')"
                                        class="px-3.5 py-1.5 rounded-xl bg-white hover:bg-[#eff6ff] text-[#1e3a8a] font-bold border border-[#bfdbfe] transition text-xs shadow-2xs cursor-pointer"
                                    >
                                        Edit Time ✎
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ ($isSuperAdmin || $isHr) ? 8 : 7 }}" class="py-12 text-center text-slate-400 text-xs">
                                No attendance records found for this period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- EDIT ATTENDANCE MODAL (FOR HR / SUPER ADMIN) -->
@if($isSuperAdmin || $isHr)
    <div id="editAttendanceModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 sm:p-7 relative">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Edit Employee Punch Time</h3>
                    <p id="modalEmpName" class="text-xs text-slate-400 font-medium mt-0.5">Modify check-in / check-out records</p>
                </div>
                <button type="button" onclick="closeEditAttendanceModal()" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg text-lg font-bold">
                    ✕
                </button>
            </div>

            <form id="editAttendanceForm" method="POST" action="" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- Check In Time -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Check-In Time</label>
                    <input type="time" name="first_check_in_time" id="modalCheckInTime" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white font-mono font-bold text-slate-900">
                </div>

                <!-- Check Out Time -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Check-Out Time</label>
                    <input type="time" name="last_check_out_time" id="modalCheckOutTime" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white font-mono font-bold text-slate-900">
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Attendance Status</label>
                    <select name="status" id="modalStatus" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900">
                        <option value="PRESENT">PRESENT</option>
                        <option value="LATE">LATE</option>
                        <option value="HALF_DAY">HALF_DAY</option>
                        <option value="ABSENT">ABSENT</option>
                    </select>
                </div>

                <!-- Admin Remarks -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">HR / Supervisor Remarks</label>
                    <textarea name="admin_remarks" id="modalRemarks" rows="2" placeholder="Reason for adjusting employee attendance time" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900"></textarea>
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeEditAttendanceModal()" class="px-4 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#1e3a8a] hover:bg-[#1e40af] text-white text-xs font-extrabold shadow-sm transition">
                        Save Adjustments
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
@endsection

@section('scripts')
<script>
    function openEditAttendanceModal(id, empName, checkIn, checkOut, status, remarks) {
        const form = document.getElementById('editAttendanceForm');
        form.action = '/attendance/' + id;
        document.getElementById('modalEmpName').textContent = 'Employee: ' + empName;
        document.getElementById('modalCheckInTime').value = checkIn;
        document.getElementById('modalCheckOutTime').value = checkOut;
        document.getElementById('modalStatus').value = status;
        document.getElementById('modalRemarks').value = remarks;
        document.getElementById('editAttendanceModal').classList.remove('hidden');
    }

    function closeEditAttendanceModal() {
        document.getElementById('editAttendanceModal').classList.add('hidden');
    }
</script>
@endsection

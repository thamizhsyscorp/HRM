@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', $isSuperAdmin ? 'Super Admin Workspace' : ($isHr ? 'HR Management Operations' : 'Employee Self-Service Portal'))

@section('content')
<div class="space-y-6">

    <!-- DASHBOARD TOP HEADER & ACTION BUTTONS -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Dashboard</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                @if($isSuperAdmin || $isHr)
                    Welcome back, {{ $user->name }}. Overview of today's workforce attendance, approved leaves, and operational status.
                @else
                    Welcome back, {{ $user->name }}. Here's what's happening today.
                @endif
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            @if($isSuperAdmin || $isHr)
                <a href="{{ route('attendance.qr-kiosk') }}" target="_blank" class="px-3.5 py-2 bg-blue-50 hover:bg-blue-100 text-[#1e3a8a] rounded-xl text-xs font-bold border border-blue-200 transition shadow-2xs flex items-center gap-1.5 cursor-pointer" title="Open Office Daily QR Kiosk Display">
                    <span>Office QR Kiosk 📱</span>
                </a>
                {{-- <a href="{{ route('employees.create') }}" class="px-4 py-2 bg-[#1e3a8a] hover:bg-[#1e40af] text-white rounded-xl text-xs font-bold shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>+ Add Employee</span>
                </a> --}}
            @endif

            <a href="{{ route('attendance.calendar') }}" class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-bold border border-slate-200 transition shadow-2xs flex items-center gap-1.5 cursor-pointer">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>Calendar</span>
            </a>
        </div>
    </div>

    @if($isSuperAdmin || $isHr)
        <!-- SUPER ADMIN & HR WORKFORCE METRIC CARDS -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5 sm:gap-3.5">
            <!-- 1. Total Headcount -->
            <a href="{{ route('employees.index') }}" class="bg-white p-3 sm:p-3.5 rounded-xl border border-blue-200/60 sm:border-slate-200/90 shadow-2xs hover:-translate-y-0.5 transition duration-150 flex flex-col justify-between group">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-blue-50 text-[#1e3a8a] flex items-center justify-center font-bold text-xs border border-blue-200/60">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <span class="text-[9px] sm:text-[10px] font-extrabold text-[#1e3a8a] bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200 font-mono">
                        Active
                    </span>
                </div>
                <div>
                    <span class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight font-mono">{{ $totalEmployees }}</span>
                    <span class="text-[11px] text-slate-500 font-semibold block mt-0.5 group-hover:text-[#1e3a8a] transition">Total Headcount</span>
                </div>
            </a>

            <!-- 2. Present Today -->
            <a href="{{ route('attendance.index') }}" class="bg-white p-3 sm:p-3.5 rounded-xl border border-emerald-200/60 sm:border-slate-200/90 shadow-2xs hover:-translate-y-0.5 transition duration-150 flex flex-col justify-between group">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs border border-emerald-200/60">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-[9px] sm:text-[10px] font-extrabold text-emerald-800 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200 font-mono">
                        ● Present
                    </span>
                </div>
                <div>
                    <span class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight font-mono">{{ $presentToday }}</span>
                    <span class="text-[11px] text-slate-500 font-semibold block mt-0.5 group-hover:text-emerald-700 transition">Present Today</span>
                </div>
            </a>

            <!-- 3. Leave Today -->
            <a href="{{ route('leaves.index') }}" class="bg-white p-3 sm:p-3.5 rounded-xl border border-amber-200/60 sm:border-slate-200/90 shadow-2xs hover:-translate-y-0.5 transition duration-150 flex flex-col justify-between group">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center font-bold text-xs border border-amber-200/60">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="text-[9px] sm:text-[10px] font-extrabold text-amber-800 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200 font-mono">
                        ● On Leave
                    </span>
                </div>
                <div>
                    <span class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight font-mono">{{ $onLeaveTodayCount }}</span>
                    <span class="text-[11px] text-slate-500 font-semibold block mt-0.5 group-hover:text-amber-800 transition">Leave Today</span>
                </div>
            </a>

            <!-- 4. Pending Leaves -->
            <a href="{{ route('leaves.index', ['status' => 'PENDING']) }}" class="bg-white p-3 sm:p-3.5 rounded-xl border border-rose-200/60 sm:border-slate-200/90 shadow-2xs hover:-translate-y-0.5 transition duration-150 flex flex-col justify-between group">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-xs border border-rose-200/60">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-[9px] sm:text-[10px] font-extrabold text-rose-800 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-200 font-mono">
                        ● Review
                    </span>
                </div>
                <div>
                    <span class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight font-mono">{{ $pendingLeavesCount }}</span>
                    <span class="text-[11px] text-slate-500 font-semibold block mt-0.5 group-hover:text-rose-700 transition">Pending Leaves</span>
                </div>
            </a>

            <!-- 5. Turnout Rate (Spans 2 columns on mobile for balanced grid) -->
            <a href="{{ route('attendance.index') }}" class="col-span-2 sm:col-span-1 bg-gradient-to-r sm:bg-none from-blue-50/50 via-white to-blue-50/30 bg-white p-3 sm:p-3.5 rounded-xl border border-blue-200/70 sm:border-slate-200/90 shadow-2xs hover:-translate-y-0.5 transition duration-150 flex flex-col justify-between group">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-blue-50 text-[#1e3a8a] flex items-center justify-center font-bold text-xs border border-blue-200/60 shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <span class="sm:hidden text-xs font-bold text-slate-700">Workforce Attendance Rate</span>
                    </div>
                    <span class="text-[9px] sm:text-[10px] font-extrabold text-[#1e3a8a] bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200 font-mono">
                        Turnout
                    </span>
                </div>
                <div>
                    <div class="flex items-baseline justify-between sm:block">
                        <span class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight font-mono">{{ $turnoutRate }}%</span>
                        <span class="text-[11px] text-slate-500 font-semibold block mt-0.5 group-hover:text-[#1e3a8a] transition">Attendance Rate</span>
                    </div>
                    <!-- Mobile progress bar indicator -->
                    <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2 sm:hidden overflow-hidden">
                        <div class="bg-[#1e3a8a] h-1.5 rounded-full" style="width: {{ min(100, max(0, $turnoutRate)) }}%"></div>
                    </div>
                </div>
            </a>
        </div>
    @else
        <!-- EMPLOYEE PERSONAL METRIC CARDS -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3.5">
            <!-- 1. Check-In Time -->
            <a href="{{ route('attendance.index') }}" class="bg-white p-3 sm:p-3.5 rounded-xl border border-emerald-200/60 sm:border-slate-200/90 shadow-2xs hover:-translate-y-0.5 transition duration-150 flex flex-col justify-between group">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs border border-emerald-200/60">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                    </div>
                    <span class="text-[9px] sm:text-[10px] font-extrabold {{ $myAttendanceToday && $myAttendanceToday->first_check_in_at ? 'text-emerald-800 bg-emerald-50 border border-emerald-200' : 'text-slate-600 bg-slate-50 border border-slate-200' }} px-1.5 py-0.5 rounded">
                        {{ $myAttendanceToday && $myAttendanceToday->first_check_in_at ? 'Punched' : 'Pending' }}
                    </span>
                </div>
                <div>
                    <span class="text-base sm:text-lg font-extrabold text-slate-900 font-mono tracking-tight block">
                        {{ $myAttendanceToday && $myAttendanceToday->first_check_in_at ? \Carbon\Carbon::parse($myAttendanceToday->first_check_in_at)->format('h:i A') : '—' }}
                    </span>
                    <span class="text-[11px] text-slate-500 font-semibold block mt-0.5 group-hover:text-emerald-700 transition">Check-In Time</span>
                </div>
            </a>

            <!-- 2. Check-Out Time -->
            <a href="{{ route('attendance.index') }}" class="bg-white p-3 sm:p-3.5 rounded-xl border border-blue-200/60 sm:border-slate-200/90 shadow-2xs hover:-translate-y-0.5 transition duration-150 flex flex-col justify-between group">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-blue-50 text-[#1e3a8a] flex items-center justify-center font-bold text-xs border border-blue-200/60">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </div>
                    <span class="text-[9px] sm:text-[10px] font-extrabold {{ $myAttendanceToday && $myAttendanceToday->last_check_out_at ? 'text-blue-800 bg-blue-50 border border-blue-200' : ($myAttendanceToday && $myAttendanceToday->first_check_in_at ? 'text-emerald-800 bg-emerald-50 border border-emerald-200' : 'text-slate-600 bg-slate-50 border border-slate-200') }} px-1.5 py-0.5 rounded">
                        {{ $myAttendanceToday && $myAttendanceToday->last_check_out_at ? 'Done' : ($myAttendanceToday && $myAttendanceToday->first_check_in_at ? 'On Duty' : 'Pending') }}
                    </span>
                </div>
                <div>
                    <span class="text-base sm:text-lg font-extrabold text-slate-900 font-mono tracking-tight block">
                        {{ $myAttendanceToday && $myAttendanceToday->last_check_out_at ? \Carbon\Carbon::parse($myAttendanceToday->last_check_out_at)->format('h:i A') : ($myAttendanceToday && $myAttendanceToday->first_check_in_at ? 'Active' : '—') }}
                    </span>
                    <span class="text-[11px] text-slate-500 font-semibold block mt-0.5 group-hover:text-blue-700 transition">Check-Out Time</span>
                </div>
            </a>

            <!-- 3. Worked Today -->
            <a href="{{ route('attendance.index') }}" class="bg-white p-3 sm:p-3.5 rounded-xl border border-teal-200/60 sm:border-slate-200/90 shadow-2xs hover:-translate-y-0.5 transition duration-150 flex flex-col justify-between group">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center font-bold text-xs border border-teal-200/60">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-[9px] sm:text-[10px] font-extrabold text-teal-800 bg-teal-50 px-1.5 py-0.5 rounded border border-teal-200 font-mono">
                        Hours
                    </span>
                </div>
                <div>
                    <span class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight font-mono">
                        {{ round(($myAttendanceToday->worked_minutes ?? 0) / 60, 1) }}h
                    </span>
                    <span class="text-[11px] text-slate-500 font-semibold block mt-0.5 group-hover:text-teal-700 transition">Worked Today</span>
                </div>
            </a>

            <!-- 4. Pending Leaves -->
            <a href="{{ route('leaves.index') }}" class="bg-white p-3 sm:p-3.5 rounded-xl border border-rose-200/60 sm:border-slate-200/90 shadow-2xs hover:-translate-y-0.5 transition duration-150 flex flex-col justify-between group">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-xs border border-rose-200/60">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="text-[9px] sm:text-[10px] font-extrabold text-rose-800 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-200 font-mono">
                        Review
                    </span>
                </div>
                <div>
                    <span class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight font-mono">
                        {{ $myPendingLeavesCount ?? 0 }}
                    </span>
                    <span class="text-[11px] text-slate-500 font-semibold block mt-0.5 group-hover:text-rose-700 transition">Pending Leaves</span>
                </div>
            </a>
            

            <!-- 5. Annual Leave Entitlements -->
            {{-- <a href="{{ route('leaves.index') }}" class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-100 shadow-md hover:-translate-y-0.5 transition duration-150 flex flex-col justify-between group">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center font-bold text-sm border border-amber-200/60">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span class="text-[10px] font-extrabold text-amber-800 bg-amber-50 px-1.5 py-0.5 rounded-md border border-amber-200 font-mono">
                        Policy
                    </span>
                </div>
                <div>
                    <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight font-mono">
                        {{ $myTotalAvailableLeave ?? 37 }}d
                    </span>
                    <span class="text-xs text-slate-500 font-semibold block mt-0.5 group-hover:text-amber-800 transition">Annual Leave Entitlements</span>
                </div>
            </a> --}}
        </div>
    @endif

    @if(!$isSuperAdmin)
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-100 p-5 sm:p-6 shadow-md flex flex-col justify-between">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></div>
                    <span class="text-xs font-extrabold uppercase tracking-wider text-slate-900">Real-Time Geofence Punch Station</span>
                </div>
                <span id="topLiveClock" class="text-xs font-mono font-bold text-slate-500">
                    {{ now()->format('h:i:s A') }}
                </span>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="grid grid-cols-3 gap-3 text-left">
                    <div>
                        <span class="block text-[10px] uppercase font-bold text-slate-400">Punch Status</span>
                        @if(!$myAttendanceToday || !$myAttendanceToday->first_check_in_at)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-900">
                                ⏳ Not Punched
                            </span>
                        @elseif($myAttendanceToday->first_check_in_at && !$myAttendanceToday->last_check_out_at)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-900">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span> On Duty
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-800">
                                ✓ Punched Out
                            </span>
                        @endif
                    </div>
                    <div>
                        <span class="block text-[10px] uppercase font-bold text-slate-400">Check-In</span>
                        <span class="font-mono font-bold text-xs sm:text-sm text-slate-900">
                            {{ $myAttendanceToday && $myAttendanceToday->first_check_in_at ? \Carbon\Carbon::parse($myAttendanceToday->first_check_in_at)->format('h:i A') : '—' }}
                        </span>
                    </div>
                    <div>
                        <span class="block text-[10px] uppercase font-bold text-slate-400">Worked</span>
                        <span class="font-mono font-bold text-xs sm:text-sm text-[#1e3a8a]">
                            {{ round(($myAttendanceToday->worked_minutes ?? 0) / 60, 1) }}h
                        </span>
                    </div>
                </div>

                <!-- Live Button -->
                <div class="shrink-0 flex items-center gap-2">
                    @if(!$myAttendanceToday || !$myAttendanceToday->first_check_in_at)
                        <button type="button" onclick="openQrScanner('check_in')" class="px-5 py-2.5 bg-[#1e3a8a] hover:bg-[#1e40af] text-white rounded-xl text-xs font-extrabold shadow-md shadow-indigo-500/20 transition flex items-center gap-2 cursor-pointer">
                            <span>📷 Scan QR to Check In</span>
                        </button>
                        <form action="{{ route('attendance.check-in') }}" method="POST" id="dashCheckInForm" class="hidden">
                            @csrf
                            <input type="hidden" name="latitude" id="dashCheckInLat">
                            <input type="hidden" name="longitude" id="dashCheckInLng">
                            <input type="hidden" name="accuracy" id="dashCheckInAcc">
                            <input type="hidden" name="remarks" value="Web Punch-In">
                        </form>
                    @elseif($myAttendanceToday->first_check_in_at && !$myAttendanceToday->last_check_out_at)
                        <button type="button" onclick="openQrScanner('check_out')" class="px-5 py-2.5 bg-slate-900 hover:bg-black text-white rounded-xl text-xs font-extrabold shadow-sm transition flex items-center gap-2 cursor-pointer">
                            <span>📷 Scan QR to Check Out</span>
                        </button>
                        <form action="{{ route('attendance.check-out') }}" method="POST" id="dashCheckOutForm" class="hidden">
                            @csrf
                            <input type="hidden" name="latitude" id="dashCheckOutLat">
                            <input type="hidden" name="longitude" id="dashCheckOutLng">
                            <input type="hidden" name="accuracy" id="dashCheckOutAcc">
                            <input type="hidden" name="remarks" value="Web Punch-Out">
                        </form>
                    @else
                        <span class="text-xs font-bold text-emerald-800 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200 block text-center">
                            Shift Complete ✓
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @endif
        <!-- RECENT ACTIVITY & ATTENDANCE LOGS FOR SUPER ADMIN & HR -->
    @if($isSuperAdmin || $isHr)

                <!-- 2. TODAY'S WORKFORCE ATTENDANCE TABLE -->
        <div class="bg-white rounded-2xl shadow-md border border-slate-100 overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-sm border border-emerald-200/60 shrink-0">
                        ⏱️
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Today's Workforce Attendance</h3>
                        <p class="text-xs text-slate-400">Punches logged for {{ today()->format('l, F j, Y') }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-between sm:justify-end gap-2.5 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                    <span class="px-2.5 py-1 text-xs font-extrabold rounded-full bg-blue-50 text-[#1e3a8a] border border-blue-200 font-mono">
                        {{ $todayAttendances->count() }} Punched Today
                    </span>
                    <a href="{{ route('attendance.index') }}" class="text-xs font-bold text-[#1e3a8a] hover:underline flex items-center gap-1">
                        <span>View Full Logs</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Mobile Attendance Cards (Visible on screens < 640px) -->
            <div class="sm:hidden divide-y divide-slate-100">
                @forelse($todayAttendances as $att)
                    <div class="p-3.5 space-y-2.5 bg-white hover:bg-slate-50/50 transition">
                        <!-- Top: Avatar, Name, Employee Code & Status Badge -->
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-full bg-blue-50 text-[#1e3a8a] font-extrabold flex items-center justify-center text-xs shrink-0 border border-blue-200">
                                    {{ strtoupper(substr($att->employee->first_name ?? 'E', 0, 1)) }}
                                </div>
                                <div>
                                    <span class="font-extrabold text-slate-900 text-xs block leading-tight">{{ $att->employee->full_name ?? 'Employee' }}</span>
                                    <span class="text-[10px] font-mono text-slate-400 block mt-0.5">
                                        {{ $att->employee->employee_code ?? '' }} • {{ $att->employee->department->name ?? '—' }}
                                    </span>
                                </div>
                            </div>
                            <div>
                                @if($att->status === 'PRESENT')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-200 whitespace-nowrap">
                                        ● PRESENT
                                    </span>
                                @elseif($att->status === 'LATE')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-800 border border-amber-200 whitespace-nowrap">
                                        ● LATE ({{ $att->late_minutes }}m)
                                    </span>
                                @elseif($att->status === 'HALF_DAY')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-800 border border-blue-200 whitespace-nowrap">
                                        ● HALF DAY
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-700 border border-slate-200 whitespace-nowrap">
                                        {{ $att->status }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Middle: Punch Metrics Strip -->
                        <div class="grid grid-cols-3 gap-2 bg-slate-50/80 p-2 rounded-lg border border-slate-100 text-center">
                            <div>
                                <span class="block text-[9px] uppercase font-bold text-slate-400">Check-In</span>
                                <span class="font-mono font-bold text-xs text-slate-900">
                                    {{ $att->first_check_in_at ? \Carbon\Carbon::parse($att->first_check_in_at)->format('h:i A') : '—' }}
                                </span>
                            </div>
                            <div>
                                <span class="block text-[9px] uppercase font-bold text-slate-400">Check-Out</span>
                                <span class="font-mono text-xs {{ $att->last_check_out_at ? 'font-bold text-slate-900' : 'text-emerald-700 font-semibold' }}">
                                    {{ $att->last_check_out_at ? \Carbon\Carbon::parse($att->last_check_out_at)->format('h:i A') : ($att->first_check_in_at ? 'Active Duty' : '—') }}
                                </span>
                            </div>
                            <div>
                                <span class="block text-[9px] uppercase font-bold text-slate-400">Worked</span>
                                <span class="font-mono font-bold text-xs text-[#1e3a8a]">
                                    {{ round(($att->worked_minutes ?? 0) / 60, 1) }}h
                                </span>
                            </div>
                        </div>

                        <!-- Bottom: Action buttons -->
                        <div class="flex items-center justify-end gap-2 pt-0.5">
                            <a 
                                href="{{ $att->maps_url }}" 
                                target="_blank" 
                                rel="noopener noreferrer" 
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-[#1e3a8a] text-[11px] font-bold border border-blue-200 transition"
                            >
                                <svg class="w-3 h-3 text-[#1e3a8a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span>GPS Map</span>
                            </a>
                            <button 
                                type="button" 
                                onclick="openDashboardEditModal({{ $att->id }}, '{{ addslashes($att->employee->full_name ?? '') }}', '{{ $att->first_check_in_at ? \Carbon\Carbon::parse($att->first_check_in_at)->format('H:i') : '' }}', '{{ $att->last_check_out_at ? \Carbon\Carbon::parse($att->last_check_out_at)->format('H:i') : '' }}', '{{ $att->status }}', '{{ addslashes($att->admin_remarks ?? '') }}')"
                                class="px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-[#1e3a8a] font-bold border border-blue-200 text-[11px] cursor-pointer transition"
                            >
                                Edit Punch ✎
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-400 text-xs">
                        <span class="font-bold text-slate-700 block">No attendance punches recorded yet today</span>
                        <span class="text-slate-400 text-[11px]">Records will appear here as employees punch in</span>
                    </div>
                @endforelse
            </div>

            <!-- Desktop Attendance Table (Hidden on screens < 640px) -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50/90 border-b border-slate-200/80 text-slate-500 font-bold uppercase text-[11px] tracking-wider">
                        <tr>
                            <th class="py-3.5 px-6 whitespace-nowrap">Employee</th>
                            <th class="py-3.5 px-6 whitespace-nowrap">Department</th>
                            <th class="py-3.5 px-6 whitespace-nowrap">Check-In</th>
                            <th class="py-3.5 px-6 whitespace-nowrap">Check-Out</th>
                            <th class="py-3.5 px-6 whitespace-nowrap">Worked</th>
                            <th class="py-3.5 px-6 whitespace-nowrap text-center">Status</th>
                            <th class="py-3.5 px-6 whitespace-nowrap text-right">HR Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                        @forelse($todayAttendances as $att)
                            <tr class="hover:bg-blue-50/40 transition-colors cursor-pointer">
                                <td class="py-4 px-6 whitespace-nowrap align-middle">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-50 text-[#1e3a8a] font-extrabold flex items-center justify-center text-xs shrink-0 border border-blue-200">
                                            {{ strtoupper(substr($att->employee->first_name ?? 'E', 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="font-extrabold text-slate-900 block leading-tight">{{ $att->employee->full_name ?? 'Employee' }}</span>
                                            <span class="text-[10px] font-mono text-slate-400 block mt-0.5">{{ $att->employee->employee_code ?? '' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap align-middle">
                                    <span class="text-slate-900 font-semibold block">{{ $att->employee->department->name ?? '—' }}</span>
                                    <span class="text-[11px] text-slate-400 block mt-0.5">{{ $att->employee->designation->name ?? '—' }}</span>
                                </td>
                                <td class="py-4 px-6 font-mono font-bold text-slate-900 whitespace-nowrap align-middle">
                                    {{ $att->first_check_in_at ? \Carbon\Carbon::parse($att->first_check_in_at)->format('h:i A') : '—' }}
                                </td>
                                <td class="py-4 px-6 font-mono text-slate-600 whitespace-nowrap align-middle">
                                    {{ $att->last_check_out_at ? \Carbon\Carbon::parse($att->last_check_out_at)->format('h:i A') : ($att->first_check_in_at ? 'Active Duty' : '—') }}
                                </td>
                                <td class="py-4 px-6 font-mono font-bold text-slate-900 whitespace-nowrap align-middle">
                                    {{ round(($att->worked_minutes ?? 0) / 60, 1) }}h
                                </td>
                                <td class="py-4 px-6 text-center whitespace-nowrap align-middle">
                                    @if($att->status === 'PRESENT')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            ● PRESENT
                                        </span>
                                    @elseif($att->status === 'LATE')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-800 border border-amber-200">
                                            ● LATE ({{ $att->late_minutes }}m)
                                        </span>
                                    @elseif($att->status === 'HALF_DAY')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-800 border border-blue-200">
                                            ● HALF DAY
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ $att->status }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap align-middle">
                                    <div class="flex items-center justify-end gap-2">
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
                                        <button 
                                            type="button" 
                                            onclick="openDashboardEditModal({{ $att->id }}, '{{ addslashes($att->employee->full_name ?? '') }}', '{{ $att->first_check_in_at ? \Carbon\Carbon::parse($att->first_check_in_at)->format('H:i') : '' }}', '{{ $att->last_check_out_at ? \Carbon\Carbon::parse($att->last_check_out_at)->format('H:i') : '' }}', '{{ $att->status }}', '{{ addslashes($att->admin_remarks ?? '') }}')"
                                            class="px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-[#1e3a8a] font-bold border border-blue-200 text-xs cursor-pointer transition shadow-2xs"
                                        >
                                            Edit Punch ✎
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400 text-xs">
                                    <div class="flex flex-col items-center justify-center gap-1">
                                        <span class="font-bold text-slate-700">No attendance punches recorded yet today</span>
                                        <span class="text-slate-400 text-[11px]">Records will appear here as employees punch in</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- 1. EMPLOYEES ON LEAVE TODAY TABLE -->
        <div class="bg-white rounded-2xl shadow-md border border-slate-100 overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center font-bold text-sm border border-amber-200/60 shrink-0">
                        🌴
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Employees on Leave Today</h3>
                        <p class="text-xs text-slate-400">Approved leave requests active for {{ today()->format('l, F j, Y') }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-between sm:justify-end gap-2.5 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                    <span class="px-2.5 py-1 text-xs font-extrabold rounded-full {{ $onLeaveTodayCount > 0 ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-slate-100 text-slate-600' }} font-mono">
                        {{ $onLeaveTodayCount }} On Leave Today
                    </span>
                    <a href="{{ route('leaves.index') }}" class="text-xs font-bold text-[#1e3a8a] hover:underline flex items-center gap-1">
                        <span>Manage Leaves</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Mobile Leaves Cards (Visible on screens < 640px) -->
            <div class="sm:hidden divide-y divide-slate-100">
                @forelse($employeesOnLeaveToday as $leave)
                    <div class="p-3.5 space-y-2 bg-white">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-amber-50 text-amber-700 font-extrabold flex items-center justify-center text-xs shrink-0 border border-amber-200">
                                    {{ strtoupper(substr($leave->employee->first_name ?? 'E', 0, 1)) }}
                                </div>
                                <div>
                                    <span class="font-extrabold text-slate-900 text-xs block leading-tight">{{ $leave->employee->full_name ?? 'Employee' }}</span>
                                    <span class="text-[10px] font-mono text-slate-400 block mt-0.5">
                                        {{ $leave->employee->employee_code ?? '' }} • {{ $leave->employee->department->name ?? '—' }}
                                    </span>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-200 whitespace-nowrap">
                                ● APPROVED
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-xs pt-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-50 text-[#1e3a8a] text-[11px] font-bold border border-blue-200">
                                {{ $leave->leaveType->name ?? 'Leave' }}
                            </span>
                            <span class="font-mono text-[11px] text-slate-700 font-bold">
                                {{ \Carbon\Carbon::parse($leave->start_date)->format('M d') }} - {{ \Carbon\Carbon::parse($leave->end_date)->format('M d') }} ({{ $leave->total_days }}d)
                            </span>
                        </div>
                        @if($leave->reason)
                            <p class="text-[11px] text-slate-500 italic bg-slate-50 p-2 rounded border border-slate-100">
                                "{{ $leave->reason }}"
                            </p>
                        @endif
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-400 text-xs">
                        <span class="font-bold text-slate-700 block">No employees on leave today</span>
                        <span class="text-slate-400 text-[11px]">All active personnel are scheduled on duty</span>
                    </div>
                @endforelse
            </div>

            <!-- Desktop Leaves Table (Hidden on screens < 640px) -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50/90 border-b border-slate-200/80 text-slate-500 font-bold uppercase text-[11px] tracking-wider">
                        <tr>
                            <th class="py-3.5 px-6 whitespace-nowrap">Employee</th>
                            <th class="py-3.5 px-6 whitespace-nowrap">Department</th>
                            <th class="py-3.5 px-6 whitespace-nowrap">Leave Category</th>
                            <th class="py-3.5 px-6 whitespace-nowrap">Duration & Dates</th>
                            <th class="py-3.5 px-6 whitespace-nowrap">Reason</th>
                            <th class="py-3.5 px-6 whitespace-nowrap text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 text-xs">
                        @forelse($employeesOnLeaveToday as $leave)
                            <tr class="hover:bg-blue-50/40 transition-colors">
                                <td class="py-4 px-6 whitespace-nowrap align-middle">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-amber-50 text-amber-700 font-extrabold flex items-center justify-center text-xs shrink-0 border border-amber-200">
                                            {{ strtoupper(substr($leave->employee->first_name ?? 'E', 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="font-extrabold text-slate-900 block leading-tight">{{ $leave->employee->full_name ?? 'Employee' }}</span>
                                            <div class="flex items-center gap-1.5 mt-0.5">
                                                <span class="text-[10px] font-mono text-slate-400">{{ $leave->employee->employee_code ?? '' }}</span>
                                                @if($leave->employee && $leave->employee->manager)
                                                    <span class="text-[10px] text-slate-500 font-medium">&bull; Manager: <strong class="text-slate-700">{{ $leave->employee->manager->full_name }}</strong></span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap align-middle">
                                    <span class="text-slate-900 font-semibold block">{{ $leave->employee->department->name ?? '—' }}</span>
                                    <span class="text-[11px] text-slate-400 block mt-0.5">{{ $leave->employee->designation->name ?? '—' }}</span>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap align-middle">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg bg-blue-50 text-[#1e3a8a] text-xs font-bold border border-blue-200">
                                        {{ $leave->leaveType->name ?? 'Leave' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap align-middle font-mono text-xs">
                                    <span class="font-bold text-slate-900 block">{{ \Carbon\Carbon::parse($leave->start_date)->format('M d') }} - {{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}</span>
                                    <span class="text-[11px] text-slate-400 block mt-0.5 font-sans">{{ $leave->total_days }} day(s)</span>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap align-middle text-slate-600 max-w-[200px] truncate" title="{{ $leave->reason }}">
                                    {{ $leave->reason }}
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap align-middle text-center">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        ● APPROVED
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400 text-xs">
                                    <div class="flex flex-col items-center justify-center gap-1">
                                        <span class="font-bold text-slate-700">No employees on leave today</span>
                                        <span class="text-slate-400 text-[11px]">All active personnel are scheduled on duty</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>


    @else
        <!-- EMPLOYEE SELF-SERVICE SECTION -->
        {{-- <div class="space-y-6">
            <!-- Annual Leave Entitlements Cards -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-md p-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Annual Leave Entitlements</h3>
                        <p class="text-xs text-slate-400">Available time-off balances for {{ date('Y') }}</p>
                    </div>
                    <a href="{{ route('leaves.index') }}" class="px-4 py-2 bg-[#1e3a8a] hover:bg-[#1e40af] text-white text-xs font-bold rounded-xl transition shadow-sm">
                        + Apply for Leave
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    @foreach($leaveBalances as $code => $balance)
                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col justify-between">
                            <div>
                                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">{{ $balance['name'] }} ({{ $code }})</span>
                                <div class="flex items-baseline gap-2 mt-2">
                                    <span class="text-3xl font-extrabold text-slate-900 font-mono">{{ $balance['available'] }}</span>
                                    <span class="text-xs text-slate-400 font-semibold">days remaining</span>
                                </div>
                            </div>
                            <div class="mt-4 pt-3 border-t border-slate-200/60 flex items-center justify-between text-xs text-slate-500">
                                <span>Used: <strong>{{ $balance['used'] }}d</strong></span>
                                <span>Quota: <strong>{{ $balance['quota'] }}d</strong></span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div> --}}
    @endif

    <!-- GPS PUNCH CARD & LATEST ANNOUNCEMENT -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Live Geofenced Punch Action / Super Admin Console Status (7 cols) -->
        @if($isSuperAdmin)
            <!-- <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-100 p-5 sm:p-6 shadow-md flex flex-col justify-between">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-pulse"></div>
                        <span class="text-xs font-extrabold uppercase tracking-wider text-slate-900">Administrator Console Status</span>
                    </div>
                    <span id="topLiveClock" class="text-xs font-mono font-bold text-slate-500">
                        {{ now()->format('h:i:s A') }}
                    </span>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-blue-50 text-blue-700 border border-blue-200">
                            🛡️ Super Admin Access — Punching Exempt
                        </span>
                        <p class="text-xs text-slate-500 mt-2 font-medium leading-relaxed">
                            Super Administrators are exempt from daily attendance punching. You have unrestricted administrative authority across all workforce modules.
                        </p>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('attendance.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition">
                            Workforce Roster
                        </a>
                        <a href="{{ route('employees.index') }}" class="px-3.5 py-2 rounded-xl bg-[#1e3a8a] hover:bg-[#1e40af] text-white text-xs font-bold transition">
                            Manage Staff
                        </a>
                    </div>
                </div>
            </div> -->
        @else

        @endif
        

        <!-- Latest Announcement (5 cols) -->
        <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-100 p-5 sm:p-6 shadow-md flex flex-col justify-between">
            @if(isset($latestAnnouncement))
                <div>
                    <div class="flex items-center justify-between gap-2 border-b border-slate-100 pb-2.5 mb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-extrabold uppercase text-slate-900">Latest Notice</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-[#eff6ff] text-[#1e3a8a] border border-[#bfdbfe]">
                                {{ $latestAnnouncement->category }}
                            </span>
                        </div>
                        <span class="text-[11px] text-slate-400 font-mono">{{ $latestAnnouncement->created_at->diffForHumans() }}</span>
                    </div>
                    <h4 class="text-sm font-extrabold text-slate-900 mb-1 line-clamp-1">{{ $latestAnnouncement->title }}</h4>
                    <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">{{ $latestAnnouncement->content }}</p>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-[11px] text-slate-400">By {{ $latestAnnouncement->author->name ?? 'People Ops' }}</span>
                    <a href="{{ route('announcements.index') }}" class="font-bold text-[#1e3a8a] hover:text-[#1e3a8a]">Read bulletin →</a>
                </div>
            @else
                <p class="text-xs text-slate-400">No company announcements published yet.</p>
            @endif
        </div>

    </div>

</div>

<!-- DASHBOARD HR EDIT ATTENDANCE MODAL -->
@if($isSuperAdmin || $isHr)
    <div id="dashEditAttendanceModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 sm:p-7 relative">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Adjust Employee Punch Time</h3>
                    <p id="dashModalEmpName" class="text-xs text-slate-400 font-medium mt-0.5">Edit check-in and check-out logs</p>
                </div>
                <button type="button" onclick="closeDashboardEditModal()" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg text-lg font-bold">
                    ✕
                </button>
            </div>

            <form id="dashEditAttendanceForm" method="POST" action="" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">First Check-In Time</label>
                    <input type="time" name="first_check_in_time" id="dashModalCheckInTime" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white font-mono font-bold text-slate-900">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Last Check-Out Time</label>
                    <input type="time" name="last_check_out_time" id="dashModalCheckOutTime" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white font-mono font-bold text-slate-900">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Attendance Status</label>
                    <select name="status" id="dashModalStatus" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900 font-medium">
                        <option value="PRESENT">PRESENT</option>
                        <option value="LATE">LATE</option>
                        <option value="HALF_DAY">HALF_DAY</option>
                        <option value="ABSENT">ABSENT</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">HR / Supervisor Remarks</label>
                    <textarea name="admin_remarks" id="dashModalRemarks" rows="2" placeholder="Reason for adjusting employee attendance time" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeDashboardEditModal()" class="px-4 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold transition">
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
    function openDashboardEditModal(id, empName, checkIn, checkOut, status, remarks) {
        const form = document.getElementById('dashEditAttendanceForm');
        form.action = '/attendance/' + id;
        document.getElementById('dashModalEmpName').textContent = 'Employee: ' + empName;
        document.getElementById('dashModalCheckInTime').value = checkIn;
        document.getElementById('dashModalCheckOutTime').value = checkOut;
        document.getElementById('dashModalStatus').value = status;
        document.getElementById('dashModalRemarks').value = remarks;
        document.getElementById('dashEditAttendanceModal').classList.remove('hidden');
    }

    function closeDashboardEditModal() {
        document.getElementById('dashEditAttendanceModal').classList.add('hidden');
    }

    function updateLiveClock() {
        const clockEl = document.getElementById('topLiveClock');
        if (!clockEl) return;
        const now = new Date();
        const options = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
        clockEl.textContent = now.toLocaleTimeString('en-US', options);
    }
    setInterval(updateLiveClock, 1000);

    let cachedCoords = null;

    document.addEventListener('DOMContentLoaded', () => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    cachedCoords = pos.coords;
                    updateAllFormCoords(cachedCoords);
                },
                (err) => {},
                { enableHighAccuracy: true, timeout: 8000 }
            );
        }
    });

    function updateAllFormCoords(coords) {
        document.querySelectorAll('input[name="latitude"]').forEach(el => el.value = coords.latitude);
        document.querySelectorAll('input[name="longitude"]').forEach(el => el.value = coords.longitude);
        document.querySelectorAll('input[name="accuracy"]').forEach(el => el.value = coords.accuracy || '');
    }

    function executePunch(formId) {
        const form = document.getElementById(formId);
        if (!form) return;

        const btn = form.querySelector('button[type="button"]');
        const origText = btn ? btn.innerHTML : '';

        if (!navigator.geolocation) {
            alert('Geolocation is required to punch in/out.');
            return;
        }

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<span>Acquiring GPS...</span>`;
        }

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const latInput = form.querySelector('input[name="latitude"]');
                const lngInput = form.querySelector('input[name="longitude"]');
                const accInput = form.querySelector('input[name="accuracy"]');
                if (latInput) latInput.value = position.coords.latitude;
                if (lngInput) lngInput.value = position.coords.longitude;
                if (accInput) accInput.value = position.coords.accuracy;
                form.submit();
            },
            (error) => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = origText;
                }
                alert('GPS location is required when checking in and out. Please grant location access.');
            },
            { enableHighAccuracy: true, timeout: 8000 }
        );
    }
</script>
@endsection

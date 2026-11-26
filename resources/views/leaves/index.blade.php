@extends('layouts.app')

@section('title', 'Leave Management')
@section('page_title', 'Leave Management')

@section('content')
<div class="space-y-4 sm:space-y-6">

    <!-- Top Action / Metric Bar: Responsive 3-Column Grid for Mobile & Desktop -->
    <div class="grid grid-cols-3 gap-2.5 sm:gap-4 lg:gap-5">
        <!-- 1. Pending Decisions -->
        <a href="{{ route('leaves.index', ['status' => 'PENDING']) }}" class="bg-white p-3 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-100 shadow-xs sm:shadow-md hover:-translate-y-0.5 hover:shadow-lg hover:border-amber-300 transition duration-150 flex flex-col justify-between group">
            <div class="flex items-center justify-between mb-2 sm:mb-3">
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold border border-amber-200/60 shadow-2xs shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="hidden sm:inline-block text-[10px] font-extrabold uppercase tracking-wider text-amber-800 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">
                    Awaiting Action
                </span>
            </div>
            <div>
                <div class="flex items-baseline gap-1 sm:gap-2">
                    <span class="text-xl sm:text-3xl font-extrabold text-slate-900 tracking-tight font-mono">{{ $pendingCount }}</span>
                    <span class="text-[10px] sm:text-xs font-bold text-amber-700">Pending</span>
                </div>
            </div>
        </a>

        <!-- 2. Approved Requests -->
        <a href="{{ route('leaves.index', ['status' => 'APPROVED']) }}" class="bg-white p-3 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-100 shadow-xs sm:shadow-md hover:-translate-y-0.5 hover:shadow-lg hover:border-emerald-300 transition duration-150 flex flex-col justify-between group">
            <div class="flex items-center justify-between mb-2 sm:mb-3">
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold border border-emerald-200/60 shadow-2xs shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="hidden sm:inline-block text-[10px] font-extrabold uppercase tracking-wider text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                    Authorized
                </span>
            </div>
            <div>
                <div class="flex items-baseline gap-1 sm:gap-2">
                    <span class="text-xl sm:text-3xl font-extrabold text-slate-900 tracking-tight font-mono">{{ $approvedCount }}</span>
                    <span class="text-[10px] sm:text-xs font-bold text-emerald-700">Approved</span>
                </div>
            </div>
        </a>

        <!-- 3. Rejected Requests -->
        <a href="{{ route('leaves.index', ['status' => 'REJECTED']) }}" class="bg-white p-3 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-100 shadow-xs sm:shadow-md hover:-translate-y-0.5 hover:shadow-lg hover:border-rose-300 transition duration-150 flex flex-col justify-between group">
            <div class="flex items-center justify-between mb-2 sm:mb-3">
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold border border-rose-200/60 shadow-2xs shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="hidden sm:inline-block text-[10px] font-extrabold uppercase tracking-wider text-rose-800 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-200">
                    Declined
                </span>
            </div>
            <div>
                <div class="flex items-baseline gap-1 sm:gap-2">
                    <span class="text-xl sm:text-3xl font-extrabold text-slate-900 tracking-tight font-mono">{{ $rejectedCount }}</span>
                    <span class="text-[10px] sm:text-xs font-bold text-rose-700">Declined</span>
                </div>
            </div>
        </a>
    </div>

    <!-- For Regular Employees: Leave Entitlements & Apply Button -->
    @if(!$isSuperAdmin && !$isHr && !empty($leaveBalances))
        @php
            $totalAvailableDays = collect($leaveBalances)->where('quota', '>', 0)->sum('available');
            $totalQuotaDays = collect($leaveBalances)->where('quota', '>', 0)->sum('quota');
            $totalUsedDays = collect($leaveBalances)->where('quota', '>', 0)->sum('used');

            $leaveCardConfigs = [
                'CL' => [
                    'icon' => '🏖️',
                    'card_bg' => 'bg-gradient-to-br from-sky-50/70 via-white to-white',
                    'border' => 'border-sky-200/90 hover:border-sky-400',
                    'accent_pill' => 'bg-sky-100/80 text-sky-800 border-sky-300',
                    'progress_bar' => 'bg-gradient-to-r from-sky-400 to-sky-600',
                    'tag' => 'Casual Leave',
                    'type_badge' => 'PAID',
                    'badge_color' => 'bg-sky-50 text-sky-700 border-sky-200',
                    'btn_hover' => 'hover:bg-sky-50 hover:text-sky-800 hover:border-sky-300',
                ],
                'SL' => [
                    'icon' => '🩹',
                    'card_bg' => 'bg-gradient-to-br from-emerald-50/70 via-white to-white',
                    'border' => 'border-emerald-200/90 hover:border-emerald-400',
                    'accent_pill' => 'bg-emerald-100/80 text-emerald-800 border-emerald-300',
                    'progress_bar' => 'bg-gradient-to-r from-emerald-400 to-emerald-600',
                    'tag' => 'Sick Leave',
                    'type_badge' => 'PAID',
                    'badge_color' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'btn_hover' => 'hover:bg-emerald-50 hover:text-emerald-800 hover:border-emerald-300',
                ],
                'EL' => [
                    'icon' => '⭐',
                    'card_bg' => 'bg-gradient-to-br from-indigo-50/70 via-white to-white',
                    'border' => 'border-indigo-200/90 hover:border-indigo-400',
                    'accent_pill' => 'bg-indigo-100/80 text-indigo-800 border-indigo-300',
                    'progress_bar' => 'bg-gradient-to-r from-[#1e3a8a] to-[#1e3a8a]',
                    'tag' => 'Earned Leave',
                    'type_badge' => 'PAID',
                    'badge_color' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                    'btn_hover' => 'hover:bg-indigo-50 hover:text-indigo-800 hover:border-indigo-300',
                ],
                'LWP' => [
                    'icon' => '⏱️',
                    'card_bg' => 'bg-gradient-to-br from-slate-50/70 via-white to-white',
                    'border' => 'border-slate-200 hover:border-slate-400',
                    'accent_pill' => 'bg-slate-100 text-slate-800 border-slate-300',
                    'progress_bar' => 'bg-slate-400',
                    'tag' => 'Unpaid Leave',
                    'type_badge' => 'LWP',
                    'badge_color' => 'bg-slate-100 text-slate-700 border-slate-300',
                    'btn_hover' => 'hover:bg-slate-50 hover:text-slate-800 hover:border-slate-300',
                ],
            ];
        @endphp

        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-100 p-4 sm:p-6 lg:p-7 shadow-xs sm:shadow-md">
            <!-- Header Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 mb-4 sm:mb-6 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl bg-[#1e3a8a]/10 text-[#1e3a8a] flex items-center justify-center font-bold text-base sm:text-lg border border-[#1e3a8a]/20 shadow-2xs shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm sm:text-base font-extrabold text-slate-900 truncate">Your Annual Leave Entitlements</h3>
                            <span class="px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-extrabold bg-[#eff6ff] text-[#1e3a8a] border border-[#bfdbfe] font-mono shrink-0">
                                {{ date('Y') }} Quotas
                            </span>
                        </div>
                        <p class="text-[11px] sm:text-xs text-slate-400 mt-0.5 font-medium truncate sm:whitespace-normal">Track your available quotas & balances</p>
                    </div>
                </div>

                <div class="flex items-center justify-between sm:justify-end gap-2 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                    <span class="px-2.5 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-[11px] sm:text-xs font-semibold text-slate-600 truncate">
                        Available Balance: <strong class="text-slate-900 font-mono font-bold">{{ $totalAvailableDays }}</strong> / {{ $totalQuotaDays }} Days
                    </span>
                    <button 
                        type="button" 
                        onclick="openApplyModal()" 
                        class="px-3.5 py-1.5 sm:px-4 sm:py-2 rounded-xl bg-[#1e3a8a] hover:bg-[#1e40af] text-white text-xs font-extrabold shadow-sm hover:shadow-md transition flex items-center gap-1.5 cursor-pointer shrink-0"
                    >
                        <span>+ Apply Leave</span>
                    </button>
                </div>
            </div>

            <!-- Balanced 2-Column Mobile & 4-Column Desktop Grid of Distinct Leave Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4 lg:gap-5">
                @foreach($leaveBalances as $code => $balance)
                    @php
                        $cfg = $leaveCardConfigs[$code] ?? [
                            'icon' => '📌',
                            'card_bg' => 'bg-white',
                            'border' => 'border-slate-200 hover:border-slate-400',
                            'accent_pill' => 'bg-slate-100 text-slate-800 border-slate-300',
                            'progress_bar' => 'bg-indigo-500',
                            'tag' => $balance['name'],
                            'type_badge' => 'LEAVE',
                            'badge_color' => 'bg-slate-100 text-slate-700 border-slate-300',
                            'btn_hover' => 'hover:bg-slate-50 hover:text-slate-800',
                        ];

                        $quota = (float) $balance['quota'];
                        $used = (float) $balance['used'];
                        $available = (float) $balance['available'];
                        $percentAvailable = $quota > 0 ? max(0, min(100, round(($available / $quota) * 100))) : 100;
                        $percentUsed = $quota > 0 ? max(0, min(100, round(($used / $quota) * 100))) : 0;
                    @endphp

                    <div class="{{ $cfg['card_bg'] }} rounded-xl sm:rounded-2xl p-3 sm:p-5 border {{ $cfg['border'] }} shadow-2xs hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
                        <div>
                            <!-- Card Header -->
                            <div class="flex items-center justify-between pb-2 sm:pb-3 border-b border-slate-100">
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <span class="text-sm sm:text-base shrink-0">{{ $cfg['icon'] }}</span>
                                    <span class="text-[11px] sm:text-xs font-extrabold text-slate-900 tracking-tight truncate">{{ $balance['name'] }}</span>
                                </div>
                                <span class="px-1.5 py-0.5 rounded text-[9px] sm:text-[10px] font-mono font-bold border shrink-0 {{ $cfg['badge_color'] }}">
                                    {{ $code }}
                                </span>
                            </div>

                            <!-- Big Number Metric -->
                            <div class="mt-2.5 sm:mt-4 flex items-baseline justify-between">
                                <div>
                                    <div class="flex items-baseline">
                                        <span class="text-xl sm:text-3xl font-extrabold text-slate-900 font-mono tracking-tight">{{ $available }}</span>
                                        <span class="text-[10px] sm:text-xs font-semibold text-slate-400 ml-0.5 sm:ml-1">
                                            @if($quota > 0)
                                                / {{ $quota }}
                                            @else
                                                days
                                            @endif
                                        </span>
                                    </div>
                                    <span class="block text-[9px] sm:text-[11px] font-semibold text-slate-500 mt-0.5">
                                        @if($quota > 0)
                                            Days Remaining
                                        @else
                                            Flexible Unpaid Quota
                                        @endif
                                    </span>
                                </div>

                                @if($quota > 0)
                                    <span class="px-1.5 py-0.5 sm:px-2 rounded-full text-[8px] sm:text-[10px] font-extrabold font-mono border {{ $percentAvailable > 30 ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-amber-50 text-amber-800 border-amber-200' }}">
                                        {{ $percentAvailable }}%
                                    </span>
                                @else
                                    <span class="px-1.5 py-0.5 sm:px-2 rounded-full text-[8px] sm:text-[10px] font-extrabold font-mono border bg-slate-100 text-slate-600 border-slate-200">
                                        No Limit
                                    </span>
                                @endif
                            </div>

                            <!-- Progress Bar -->
                            <div class="mt-2.5 sm:mt-4">
                                <div class="w-full bg-slate-100 rounded-full h-1.5 sm:h-2 overflow-hidden border border-slate-200/50">
                                    @if($quota > 0)
                                        <div class="h-1.5 sm:h-2 rounded-full {{ $cfg['progress_bar'] }} transition-all duration-500" style="width: {{ $percentAvailable }}%"></div>
                                    @else
                                        <div class="h-1.5 sm:h-2 rounded-full bg-slate-400 transition-all duration-500" style="width: 100%"></div>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between text-[9px] sm:text-[11px] text-slate-500 font-semibold mt-1">
                                    <span>{{ $used }} used</span>
                                    @if($quota > 0)
                                        <span>{{ $available }} avail</span>
                                    @else
                                        <span>Loss of Pay</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Card Action -->
                        <button 
                            type="button" 
                            onclick="openApplyModal({{ $balance['id'] }})"
                            class="w-full mt-2.5 sm:mt-4 py-1.5 sm:py-2 px-2 sm:px-3 rounded-lg sm:rounded-xl bg-white text-slate-700 border border-slate-200 font-bold text-[11px] sm:text-xs transition shadow-2xs flex items-center justify-center gap-1 cursor-pointer hover:bg-slate-50 hover:border-slate-300 {{ $cfg['btn_hover'] }}"
                        >
                            <span class="sm:hidden">+ Apply</span>
                            <span class="hidden sm:inline">+ Apply for {{ $balance['name'] }}</span>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Leave Requests Table with Status Tabs -->
    <div class="bg-white rounded-2xl shadow-md border border-slate-100 overflow-hidden">
        <!-- Filter Bar -->
        <div class="p-3.5 sm:p-5 border-b border-slate-100 bg-white">
            <!-- Mobile View: Clean Native Dropdown Select (Visible on screens < 640px) -->
            <div class="sm:hidden flex items-center justify-between gap-2.5">
                <label for="mobileLeaveStatusFilter" class="text-xs font-bold text-slate-700 flex items-center gap-1.5 shrink-0">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    <span>Filter:</span>
                </label>
                <div class="relative flex-1">
                    <select 
                        id="mobileLeaveStatusFilter"
                        onchange="window.location.href=this.value" 
                        class="w-full appearance-none bg-slate-50 border border-slate-200 hover:border-slate-300 text-slate-800 text-xs font-bold rounded-xl px-3.5 py-2 pr-8 focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] shadow-2xs transition cursor-pointer"
                    >
                        <option value="{{ route('leaves.index', ['status' => 'ALL']) }}" {{ $statusFilter === 'ALL' ? 'selected' : '' }}>
                            All Applications
                        </option>
                        <option value="{{ route('leaves.index', ['status' => 'PENDING']) }}" {{ $statusFilter === 'PENDING' ? 'selected' : '' }}>
                            Pending ({{ $pendingCount }})
                        </option>
                        <option value="{{ route('leaves.index', ['status' => 'APPROVED']) }}" {{ $statusFilter === 'APPROVED' ? 'selected' : '' }}>
                            Approved ({{ $approvedCount }})
                        </option>
                        <option value="{{ route('leaves.index', ['status' => 'REVOKE_REQUESTED']) }}" {{ $statusFilter === 'REVOKE_REQUESTED' ? 'selected' : '' }}>
                            Revoke Requests ({{ $revokeRequestedCount }})
                        </option>
                        <option value="{{ route('leaves.index', ['status' => 'REJECTED']) }}" {{ $statusFilter === 'REJECTED' ? 'selected' : '' }}>
                            Rejected ({{ $rejectedCount }})
                        </option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Desktop View: Clean Pill Tab Bar (Visible on screens >= 640px) -->
            <div class="hidden sm:flex sm:items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <a href="{{ route('leaves.index', ['status' => 'ALL']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $statusFilter === 'ALL' ? 'bg-[#1e3a8a] text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        All Applications
                    </a>
                    <a href="{{ route('leaves.index', ['status' => 'PENDING']) }}" class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $statusFilter === 'PENDING' ? 'bg-[#1e3a8a] text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        <span>Pending</span>
                        @if($pendingCount > 0)
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black {{ $statusFilter === 'PENDING' ? 'bg-white text-[#1e3a8a]' : 'bg-rose-500 text-white' }}">{{ $pendingCount }}</span>
                        @else
                            <span class="text-[10px] opacity-70 font-mono">({{ $pendingCount }})</span>
                        @endif
                    </a>
                    <a href="{{ route('leaves.index', ['status' => 'APPROVED']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $statusFilter === 'APPROVED' ? 'bg-[#1e3a8a] text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        Approved
                    </a>
                    <a href="{{ route('leaves.index', ['status' => 'REVOKE_REQUESTED']) }}" class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $statusFilter === 'REVOKE_REQUESTED' ? 'bg-[#1e3a8a] text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        <span>Revoke Requests</span>
                        @if($revokeRequestedCount > 0)
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black {{ $statusFilter === 'REVOKE_REQUESTED' ? 'bg-white text-[#1e3a8a]' : 'bg-amber-500 text-white' }}">{{ $revokeRequestedCount }}</span>
                        @else
                            <span class="text-[10px] opacity-70 font-mono">({{ $revokeRequestedCount }})</span>
                        @endif
                    </a>
                    <a href="{{ route('leaves.index', ['status' => 'REJECTED']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $statusFilter === 'REJECTED' ? 'bg-[#1e3a8a] text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        Rejected
                    </a>
                </div>
            </div>
        </div>

        <!-- Mobile Leave Cards (Visible on screens < 640px) -->
        <div class="sm:hidden divide-y divide-slate-100">
            @forelse($leaveRequests as $req)
                @php
                    $canReviewThis = $isSuperAdmin || $isHr || (
                        auth()->user()->employee && $req->employee && (
                            auth()->user()->employee->id === $req->employee->manager_id ||
                            (auth()->user()->employee->department_id === $req->employee->department_id && (auth()->user()->hasRole('manager') || auth()->user()->hasRole('department-manager') || auth()->user()->hasRole('pm')))
                        )
                    );
                    $isReqOwner = auth()->user()->employee && auth()->user()->employee->id === $req->employee_id;
                @endphp
                <div class="p-3.5 space-y-2.5 bg-white">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-[#eff6ff] text-[#1e3a8a] font-extrabold flex items-center justify-center text-xs shrink-0 border border-[#bfdbfe]">
                                {{ strtoupper(substr($req->employee->first_name ?? 'E', 0, 1)) }}
                            </div>
                            <div>
                                <span class="font-extrabold text-slate-900 text-xs block leading-tight">{{ $req->employee->full_name ?? 'Employee' }}</span>
                                <span class="text-[10px] font-mono text-slate-400 block mt-0.5">
                                    {{ $req->leaveType->name ?? 'Leave' }} &bull; {{ (float)$req->total_days == (int)$req->total_days ? (int)$req->total_days : $req->total_days }} {{ (float)$req->total_days == 1 ? 'day' : 'days' }}
                                </span>
                            </div>
                        </div>

                        <div>
                            @if($req->status === 'APPROVED')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-900 border border-emerald-300">
                                    ✓ APPROVED
                                </span>
                            @elseif($req->status === 'REVOKE_REQUESTED')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-900 border border-amber-300">
                                    REVOKE REQ
                                </span>
                            @elseif($req->status === 'REVOKED')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-700 border border-slate-300">
                                    REVOKED
                                </span>
                            @elseif($req->status === 'REJECTED')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-800 border border-rose-200">
                                    ✕ REJECTED
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-800 border border-amber-200">
                                    PENDING
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100 text-xs text-slate-600 flex items-center justify-between">
                        <span class="font-mono font-semibold text-[11px] text-slate-700">
                            {{ \Carbon\Carbon::parse($req->start_date)->format('M d') }} &rarr; {{ \Carbon\Carbon::parse($req->end_date)->format('M d, Y') }}
                        </span>
                        @if($req->reason)
                            <span class="text-[11px] text-slate-500 truncate max-w-[150px]" title="{{ $req->reason }}">{{ $req->reason }}</span>
                        @endif
                    </div>

                    <!-- Actions -->
                    @if($req->status === 'REVOKE_REQUESTED' && $canReviewThis)
                        <div class="flex items-center gap-2 pt-1">
                            <form method="POST" action="{{ route('leaves.review', $req) }}" class="flex-1">
                                @csrf
                                <input type="hidden" name="status" value="REVOKED">
                                <button type="submit" class="w-full py-1.5 px-3 rounded-lg bg-emerald-600 text-white font-bold text-xs">
                                    Approve Revoke
                                </button>
                            </form>
                            <form method="POST" action="{{ route('leaves.review', $req) }}" class="flex-1">
                                @csrf
                                <input type="hidden" name="status" value="APPROVED">
                                <button type="submit" class="w-full py-1.5 px-3 rounded-lg bg-rose-50 text-rose-800 border border-rose-200 font-bold text-xs">
                                    Decline
                                </button>
                            </form>
                        </div>
                    @elseif($req->status === 'PENDING')
                        <div class="flex items-center gap-2 pt-1">
                            @if($canReviewThis)
                                <form method="POST" action="{{ route('leaves.review', $req) }}" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="status" value="APPROVED">
                                    <button type="submit" class="w-full py-1.5 px-3 rounded-lg bg-emerald-600 text-white font-bold text-xs">
                                        Approve
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('leaves.review', $req) }}" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="status" value="REJECTED">
                                    <button type="submit" class="w-full py-1.5 px-3 rounded-lg bg-rose-50 text-rose-800 border border-rose-200 font-bold text-xs">
                                        Reject
                                    </button>
                                </form>
                            @endif
                            @if($isReqOwner)
                                <form method="POST" action="{{ route('leaves.revoke', $req) }}" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full py-1.5 px-3 rounded-lg bg-slate-100 text-slate-700 border border-slate-200 text-xs font-bold">
                                        Cancel Request
                                    </button>
                                </form>
                            @endif
                        </div>
                    @elseif($req->status === 'APPROVED' && ($isReqOwner || $isSuperAdmin || $isHr))
                        <form method="POST" action="{{ route('leaves.revoke', $req) }}" onsubmit="return confirm('Request to revoke this approved leave? This will be sent to your Reporting Manager and HR for approval.')" class="pt-1">
                            @csrf
                            <button type="submit" class="w-full py-1.5 px-3 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 font-bold text-xs transition cursor-pointer">
                                Revoke Leave
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="py-8 text-center text-slate-400 text-xs">
                    No leave requests recorded under this filter.
                </div>
            @endforelse
        </div>

        <!-- Desktop Table (Visible on screens >= 640px) -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50/90 border-b border-slate-200/80 text-slate-500 font-bold uppercase text-[11px] tracking-wider">
                    <tr>
                        <th class="py-3.5 px-6 whitespace-nowrap">Employee</th>
                        <th class="py-3.5 px-6 whitespace-nowrap">Leave Category</th>
                        <th class="py-3.5 px-6 whitespace-nowrap">Start Date</th>
                        <th class="py-3.5 px-6 whitespace-nowrap">End Date</th>
                        <th class="py-3.5 px-6 whitespace-nowrap text-center">Duration</th>
                        <th class="py-3.5 px-6 whitespace-nowrap">Reason</th>
                        <th class="py-3.5 px-6 whitespace-nowrap text-center">Status</th>
                        <th class="py-3.5 px-6 whitespace-nowrap text-right">Review Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 text-xs">
                    @forelse($leaveRequests as $req)
                        <tr class="hover:bg-slate-50/70 transition-colors cursor-pointer">
                            <td class="py-4 px-6 whitespace-nowrap align-middle">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-[#eff6ff] text-[#1e3a8a] font-extrabold flex items-center justify-center text-xs shrink-0 border border-[#bfdbfe]">
                                        {{ strtoupper(substr($req->employee->first_name ?? 'E', 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="font-extrabold text-slate-900 block leading-tight">{{ $req->employee->full_name ?? 'Employee' }}</span>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <span class="text-[10px] font-mono text-slate-400">{{ $req->employee->employee_code ?? '' }}</span>
                                            @if($req->employee && $req->employee->manager)
                                                <span class="text-[10px] text-slate-500 font-medium">&bull; Manager: <strong class="text-slate-700">{{ $req->employee->manager->full_name }}</strong></span>
                                            @else
                                                <span class="text-[10px] text-slate-400 font-medium">&bull; Manager: <span class="italic text-slate-400">Direct</span></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap align-middle">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-[#eff6ff] text-[#1e3a8a] text-xs font-bold border border-[#bfdbfe]">
                                    {{ $req->leaveType->name ?? 'Leave' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap align-middle font-mono text-xs font-semibold text-slate-700">
                                {{ \Carbon\Carbon::parse($req->start_date)->format('Y-m-d') }}
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap align-middle font-mono text-xs font-semibold text-slate-700">
                                {{ \Carbon\Carbon::parse($req->end_date)->format('Y-m-d') }}
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap align-middle text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 font-bold font-mono text-xs">
                                    {{ (float)$req->total_days == (int)$req->total_days ? (int)$req->total_days : $req->total_days }} {{ (float)$req->total_days == 1 ? 'day' : 'days' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 align-middle max-w-xs text-slate-600" title="{{ $req->reason }}">
                                <span class="line-clamp-2 block leading-relaxed">{{ $req->reason }}</span>
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap align-middle text-center">
                                @if($req->status === 'APPROVED')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold bg-emerald-100 text-emerald-900 border border-emerald-300">
                                        <svg class="w-3 h-3 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span>APPROVED</span>
                                    </span>
                                @elseif($req->status === 'REVOKE_REQUESTED')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold bg-amber-100 text-amber-900 border border-amber-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        <span>REVOKE REQUESTED</span>
                                    </span>
                                @elseif($req->status === 'REVOKED')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold bg-slate-100 text-slate-700 border border-slate-300">
                                        <span>REVOKED</span>
                                    </span>
                                @elseif($req->status === 'REJECTED')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold bg-rose-50 text-rose-800 border border-rose-200">
                                        <svg class="w-3 h-3 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        <span>REJECTED</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold bg-amber-50 text-amber-800 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        <span>PENDING</span>
                                    </span>
                                @endif

                                @if($req->reviewed_at)
                                    <span class="block text-[10px] text-slate-400 font-medium mt-1">Review: {{ $req->reviewer->name ?? 'HR/Manager' }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap align-middle text-right">
                                @php
                                    $canReviewThis = $isSuperAdmin || $isHr || (
                                        auth()->user()->employee && $req->employee && (
                                            auth()->user()->employee->id === $req->employee->manager_id ||
                                            (auth()->user()->employee->department_id === $req->employee->department_id && (auth()->user()->hasRole('manager') || auth()->user()->hasRole('department-manager') || auth()->user()->hasRole('pm')))
                                        )
                                    );
                                    $isReqOwner = auth()->user()->employee && auth()->user()->employee->id === $req->employee_id;
                                @endphp

                                @if($req->status === 'REVOKE_REQUESTED')
                                    @if($canReviewThis)
                                        <div class="flex items-center justify-end gap-1.5">
                                            <form method="POST" action="{{ route('leaves.review', $req) }}" onsubmit="return confirm('Approve leave revocation for {{ $req->employee->full_name }}? The leave will be cancelled and attendance unlocked.')" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="REVOKED">
                                                <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition cursor-pointer shadow-2xs">
                                                    Approve Revoke
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('leaves.review', $req) }}" onsubmit="return confirm('Decline revocation? The leave will remain Approved.')" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="APPROVED">
                                                <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-200 font-bold text-xs transition cursor-pointer shadow-2xs">
                                                    Decline
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-amber-700 font-bold text-xs">Awaiting Approval</span>
                                    @endif
                                @elseif($req->status === 'PENDING')
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if($canReviewThis)
                                            <form method="POST" action="{{ route('leaves.review', $req) }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="APPROVED">
                                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition cursor-pointer shadow-2xs">
                                                    Approve
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('leaves.review', $req) }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="REJECTED">
                                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-200 font-bold text-xs transition cursor-pointer shadow-2xs">
                                                    Reject
                                                </button>
                                            </form>
                                        @endif
                                        @if($isReqOwner)
                                            <form method="POST" action="{{ route('leaves.revoke', $req) }}" onsubmit="return confirm('Cancel this pending leave application?')" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 text-xs font-bold transition cursor-pointer">
                                                    Cancel
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @elseif($req->status === 'APPROVED')
                                    @if($isReqOwner || $isSuperAdmin || $isHr)
                                        <form method="POST" action="{{ route('leaves.revoke', $req) }}" onsubmit="return confirm('Request to revoke this approved leave? This will be sent to your Reporting Manager and HR for approval.')" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 font-bold text-xs transition cursor-pointer shadow-2xs">
                                                Revoke Leave
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-slate-400 text-xs font-mono">No action</span>
                                    @endif
                                @else
                                    <span class="text-slate-400 text-xs font-mono">No action</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400 text-xs">
                                No leave requests recorded under this filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($leaveRequests->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $leaveRequests->links() }}
            </div>
        @endif
    </div>

</div>

<!-- APPLY LEAVE MODAL -->
<div id="applyLeaveModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 sm:p-7 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Submit Time-Off Application</h3>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Request annual or sick leave approval</p>
            </div>
            <button type="button" onclick="closeApplyModal()" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg text-lg font-bold">
                ✕
            </button>
        </div>

        <form action="{{ route('leaves.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Leave Type -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Leave Type <span class="text-rose-500">*</span></label>
                <select name="leave_type_id" id="modalLeaveTypeSelect" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900">
                    @foreach($leaveTypes as $type)
                        <option value="{{ $type->id }}">
                            {{ $type->name }} (Quota: {{ $type->annual_quota }} days)
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Start Date -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Start Date <span class="text-rose-500">*</span></label>
                <input type="date" name="start_date" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900">
            </div>

            <!-- End Date -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">End Date <span class="text-rose-500">*</span></label>
                <input type="date" name="end_date" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900">
            </div>

            <!-- Reason -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Reason for Absence <span class="text-rose-500">*</span></label>
                <textarea name="reason" rows="3" required placeholder="Describe the reason for taking leave..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeApplyModal()" class="px-4 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold transition">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#1e3a8a] hover:bg-[#1e40af] text-white text-xs font-extrabold shadow-sm transition">
                    Submit Application
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openApplyModal(leaveTypeId = null) {
        if (leaveTypeId) {
            const select = document.getElementById('modalLeaveTypeSelect');
            if (select) {
                select.value = leaveTypeId;
            }
        }
        document.getElementById('applyLeaveModal').classList.remove('hidden');
    }

    function closeApplyModal() {
        document.getElementById('applyLeaveModal').classList.add('hidden');
    }
</script>
@endsection

@extends('layouts.app')

@section('title', 'Leave Management')
@section('page_title', 'Employee Leave Management & Applications')

@section('content')
<div class="space-y-6">

    <!-- Top Action / Metric Bar -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <!-- 1. Pending Decisions -->
        <a href="{{ route('leaves.index', ['status' => 'PENDING']) }}" class="bg-white p-5 rounded-2xl border border-slate-100 shadow-md hover:-translate-y-0.5 hover:shadow-lg hover:border-amber-300 transition duration-150 flex flex-col justify-between group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold border border-amber-200/60 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-800 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">
                    Awaiting Action
                </span>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-slate-900 tracking-tight font-mono">{{ $pendingCount }}</span>
                    <span class="text-xs font-bold text-amber-700">Pending</span>
                </div>
                <span class="text-xs text-slate-500 font-semibold block mt-1 group-hover:text-amber-700 transition">Supervisor review required</span>
            </div>
        </a>

        <!-- 2. Approved Requests -->
        <a href="{{ route('leaves.index', ['status' => 'APPROVED']) }}" class="bg-white p-5 rounded-2xl border border-slate-100 shadow-md hover:-translate-y-0.5 hover:shadow-lg hover:border-emerald-300 transition duration-150 flex flex-col justify-between group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold border border-emerald-200/60 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                    Authorized
                </span>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-slate-900 tracking-tight font-mono">{{ $approvedCount }}</span>
                    <span class="text-xs font-bold text-emerald-700">Approved</span>
                </div>
                <span class="text-xs text-slate-500 font-semibold block mt-1 group-hover:text-emerald-700 transition">Scheduled employee time-off</span>
            </div>
        </a>

        <!-- 3. Rejected Requests -->
        <a href="{{ route('leaves.index', ['status' => 'REJECTED']) }}" class="bg-white p-5 rounded-2xl border border-slate-100 shadow-md hover:-translate-y-0.5 hover:shadow-lg hover:border-rose-300 transition duration-150 flex flex-col justify-between group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold border border-rose-200/60 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-rose-800 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-200">
                    Declined
                </span>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-slate-900 tracking-tight font-mono">{{ $rejectedCount }}</span>
                    <span class="text-xs font-bold text-rose-700">Declined</span>
                </div>
                <span class="text-xs text-slate-500 font-semibold block mt-1 group-hover:text-rose-700 transition">Non-approved applications</span>
            </div>
        </a>

        <!-- 4. Active Leave Policies -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-md flex flex-col justify-between group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-[#1e3a8a]/10 text-[#1e3a8a] flex items-center justify-center font-bold border border-[#1e3a8a]/20 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#1e3a8a] bg-[#eff6ff] px-2 py-0.5 rounded-md border border-[#bfdbfe]">
                    Configured
                </span>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-slate-900 tracking-tight font-mono">{{ $leaveTypes->count() }}</span>
                    <span class="text-xs font-bold text-[#1e3a8a]">Policies</span>
                </div>
                <span class="text-xs text-slate-500 font-semibold block mt-1">CL, SL, EL & LWP categories</span>
            </div>
        </div>
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

        <div class="bg-white rounded-3xl border border-slate-100 p-6 sm:p-7 shadow-md">
            <!-- Header Bar -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-[#1e3a8a]/10 text-[#1e3a8a] flex items-center justify-center font-bold text-lg border border-[#1e3a8a]/20 shadow-2xs shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-extrabold text-slate-900">Your Annual Leave Entitlements</h3>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-[#eff6ff] text-[#1e3a8a] border border-[#bfdbfe] font-mono">
                                {{ date('Y') }} Quotas
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5 font-medium">Track your available quotas, real-time usage balances, and apply for time-off</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-600">
                        Available Balance: <strong class="text-slate-900 font-mono font-bold">{{ $totalAvailableDays }}</strong> / {{ $totalQuotaDays }} Days
                    </span>
                    <button 
                        type="button" 
                        onclick="openApplyModal()" 
                        class="px-4 py-2 rounded-xl bg-gradient-to-r from-[#1e3a8a] to-[#1e3a8a] hover:from-[#1e40af] hover:to-[#3730a3] text-white text-xs font-extrabold shadow-sm hover:shadow-md transition flex items-center gap-1.5 cursor-pointer"
                    >
                        <span>+ Apply for Leave</span>
                    </button>
                </div>
            </div>

            <!-- Balanced 4-Column Grid of Distinct Leave Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
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

                    <div class="{{ $cfg['card_bg'] }} rounded-2xl p-5 border {{ $cfg['border'] }} shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
                        <div>
                            <!-- Card Header -->
                            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                <div class="flex items-center gap-2">
                                    <span class="text-base">{{ $cfg['icon'] }}</span>
                                    <span class="text-xs font-extrabold text-slate-900 tracking-tight">{{ $balance['name'] }}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-mono font-bold border {{ $cfg['badge_color'] }}">
                                        {{ $code }}
                                    </span>
                                </div>
                            </div>

                            <!-- Big Number Metric -->
                            <div class="mt-4 flex items-baseline justify-between">
                                <div>
                                    <span class="text-3xl font-extrabold text-slate-900 font-mono tracking-tight">{{ $available }}</span>
                                    <span class="text-xs font-semibold text-slate-400 ml-1">
                                        @if($quota > 0)
                                            / {{ $quota }}
                                        @else
                                            days
                                        @endif
                                    </span>
                                    <span class="block text-[11px] font-semibold text-slate-500 mt-0.5">
                                        @if($quota > 0)
                                            Days Remaining
                                        @else
                                            Flexible Unpaid Quota
                                        @endif
                                    </span>
                                </div>

                                @if($quota > 0)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold font-mono border {{ $percentAvailable > 30 ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-amber-50 text-amber-800 border-amber-200' }}">
                                        {{ $percentAvailable }}% Left
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold font-mono border bg-slate-100 text-slate-600 border-slate-200">
                                        No Limit
                                    </span>
                                @endif
                            </div>

                            <!-- Progress Bar -->
                            <div class="mt-4">
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden border border-slate-200/50">
                                    @if($quota > 0)
                                        <div class="h-2 rounded-full {{ $cfg['progress_bar'] }} transition-all duration-500" style="width: {{ $percentAvailable }}%"></div>
                                    @else
                                        <div class="h-2 rounded-full bg-slate-400 transition-all duration-500" style="width: 100%"></div>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between text-[11px] text-slate-500 font-semibold mt-1.5">
                                    <span>{{ $used }} used</span>
                                    @if($quota > 0)
                                        <span>{{ $available }} available</span>
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
                            class="w-full mt-4 py-2 px-3 rounded-xl bg-white text-slate-700 border border-slate-200 font-bold text-xs transition shadow-2xs flex items-center justify-center gap-1.5 cursor-pointer {{ $cfg['btn_hover'] }}"
                        >
                            <span>+ Apply for {{ $balance['name'] }}</span>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Leave Requests Table with Status Tabs -->
    <div class="bg-white rounded-2xl shadow-md border border-slate-100 overflow-hidden">
        <!-- Tab Bar -->
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white">
            <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0">
                <a href="{{ route('leaves.index', ['status' => 'ALL']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $statusFilter === 'ALL' ? 'bg-[#1e3a8a] text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                    All Applications
                </a>
                <a href="{{ route('leaves.index', ['status' => 'PENDING']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $statusFilter === 'PENDING' ? 'bg-[#1e3a8a] text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                    Pending ({{ $pendingCount }})
                </a>
                <a href="{{ route('leaves.index', ['status' => 'APPROVED']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $statusFilter === 'APPROVED' ? 'bg-[#1e3a8a] text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                    Approved
                </a>
                <a href="{{ route('leaves.index', ['status' => 'REVOKE_REQUESTED']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $statusFilter === 'REVOKE_REQUESTED' ? 'bg-[#1e3a8a] text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                    Revoke Requests ({{ $revokeRequestedCount }})
                </a>
                <a href="{{ route('leaves.index', ['status' => 'REJECTED']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $statusFilter === 'REJECTED' ? 'bg-[#1e3a8a] text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                    Rejected
                </a>
            </div>

            <!-- Employee Quick Apply Button -->
            <button type="button" onclick="openApplyModal()" class="px-3.5 py-1.5 rounded-xl bg-[#1e3a8a] hover:bg-[#1e40af] text-white text-xs font-bold shadow-xs transition self-start sm:self-auto cursor-pointer">
                + New Leave Request
            </button>
        </div>

        <div class="overflow-x-auto">
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

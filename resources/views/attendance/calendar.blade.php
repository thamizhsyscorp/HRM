@extends('layouts.app')

@section('title', 'Attendance Calendar — ' . $monthName)
@section('page_title', 'Workforce Attendance Calendar')

@section('content')
<div class="space-y-6">

    <!-- HERO PROFILE & MONTH SWITCHER CARD -->
    <div class="bg-white rounded-xl border border-slate-200/90 p-4 sm:p-5 shadow-2xs">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            
            <!-- Employee Profile Information -->
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-[#1e3a8a] text-white font-extrabold text-base flex items-center justify-center shadow-xs shrink-0">
                    {{ strtoupper(substr($targetEmployee->first_name, 0, 1) . substr($targetEmployee->last_name ?? '', 0, 1)) }}
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-0.5">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-[#1e3a8a] bg-[#eff6ff] px-2 py-0.5 rounded-full border border-[#bfdbfe]">
                            {{ $targetEmployee->department->name ?? 'General Department' }}
                        </span>
                        <span class="text-[11px] font-mono font-bold text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded-md">
                            {{ $targetEmployee->employee_code }}
                        </span>
                    </div>
                    <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">
                        {{ $targetEmployee->full_name }}
                    </h2>
                    <p class="text-[11px] text-slate-400 font-medium">
                        {{ $targetEmployee->designation->name ?? 'Staff Role' }} &bull; {{ $targetEmployee->work_email }}
                    </p>
                </div>
            </div>

            <!-- Month Switcher & Employee Selector -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 shrink-0">
                <!-- HR & Admin Employee Switcher -->
                @if($isSuperAdmin || $isHr)
                    <form method="GET" action="{{ route('attendance.calendar') }}" class="flex items-center gap-2">
                        <input type="hidden" name="year" value="{{ $year }}">
                        <input type="hidden" name="month" value="{{ $month }}">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-700 shrink-0">Viewing Personnel:</label>
                        <select 
                            name="employee_id" 
                            onchange="this.form.submit()" 
                            class="px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white shadow-2xs text-slate-900"
                        >
                            @foreach($activeEmployees as $emp)
                                <option value="{{ $emp->id }}" {{ $targetEmployee->id == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->full_name }} ({{ $emp->employee_code }})
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif

                <!-- Today Shortcut -->
                <a 
                    href="{{ route('attendance.calendar', ['employee_id' => $targetEmployee->id]) }}" 
                    class="px-3.5 py-2 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-800 text-xs font-bold transition text-center shadow-2xs bg-white"
                >
                    Today
                </a>

                @if($isSuperAdmin || $isHr)
                    <a 
                        href="{{ route('holidays.index') }}" 
                        class="px-3.5 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 text-xs font-bold transition flex items-center gap-1.5 shadow-2xs"
                    >
                        <span>★ Manage Holidays</span>
                    </a>

                    <button 
                        type="button" 
                        onclick="openCalendarHolidayModal()" 
                        class="px-3.5 py-2 rounded-xl bg-[#1e3a8a] hover:bg-[#1e40af] text-white text-xs font-bold transition flex items-center gap-1.5 shadow-2xs cursor-pointer"
                    >
                        <span>+ Add Holiday</span>
                    </button>
                @endif
            </div>

        </div>

        <!-- Monthly Progress Strip -->
        {{-- <div class="mt-6 pt-5 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-4 text-xs">
                @php
                    $workingDays = $presentCount + $absentCount;
                    $rate = $workingDays > 0 ? round(($presentCount / $workingDays) * 100, 1) : 0;
                @endphp
                <div class="flex items-center gap-2">
                    <span class="text-slate-400 font-medium">Monthly Turnout:</span>
                    <span class="font-extrabold text-slate-900 font-mono">{{ $rate }}%</span>
                </div>
                <div class="w-28 bg-slate-100 rounded-full h-2 overflow-hidden">
                    <div class="bg-emerald-600 h-2 rounded-full" style="width: {{ $rate }}%"></div>
                </div>
                <span class="text-slate-300">&bull;</span>
                <span class="text-slate-600 font-medium">{{ $presentCount }} of {{ $workingDays }} scheduled days present</span>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('attendance.index') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition text-slate-600 hover:text-slate-950">
                    Daily Logs →
                </a>
            </div>
        </div> --}}
    </div>

    <!-- MONTHLY SUMMARY METRIC CARDS -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white p-3 sm:p-3.5 rounded-xl border border-emerald-200/70 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[10px] sm:text-[11px] font-bold text-emerald-800 uppercase tracking-wider block">Present Days</span>
                <span class="text-xl sm:text-2xl font-extrabold text-emerald-700 mt-0.5 block font-mono">{{ $presentCount }}</span>
                <span class="text-[10px] text-slate-400 font-medium">Verified PRESENT</span>
            </div>
            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-black text-sm border border-emerald-200 shrink-0">
                ✓
            </div>
        </div>

        <div class="bg-white p-3 sm:p-3.5 rounded-xl border border-rose-200/70 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[10px] sm:text-[11px] font-bold text-rose-800 uppercase tracking-wider block">Absent Days</span>
                <span class="text-xl sm:text-2xl font-extrabold text-rose-700 mt-0.5 block font-mono">{{ $absentCount }}</span>
                <span class="text-[10px] text-slate-400 font-medium">Recorded ABSENT</span>
            </div>
            <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-700 flex items-center justify-center font-black text-sm border border-rose-200 shrink-0">
                ✕
            </div>
        </div>

        <div class="bg-white p-3 sm:p-3.5 rounded-xl border border-amber-200/70 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[10px] sm:text-[11px] font-bold text-amber-900 uppercase tracking-wider block">Company Holidays</span>
                <span class="text-xl sm:text-2xl font-extrabold text-amber-700 mt-0.5 block font-mono">{{ $holidayCount }}</span>
                <span class="text-[10px] text-slate-400 font-medium">Company holidays + Sundays</span>
            </div>
            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-800 flex items-center justify-center font-black text-sm border border-amber-200 shrink-0">
                ★
            </div>
        </div>

        <div class="bg-white p-3 sm:p-3.5 rounded-xl border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-600 uppercase tracking-wider block">Total Hours</span>
                <span class="text-xl sm:text-2xl font-extrabold text-slate-900 font-mono mt-0.5 block">{{ $totalWorkedHours }}h</span>
                <span class="text-[10px] text-slate-400 font-medium">HOLIDAY & On-duty</span>
            </div>
            <div class="w-8 h-8 rounded-lg bg-[#eff6ff] text-[#1e3a8a] flex items-center justify-center font-black text-sm border border-[#bfdbfe] shrink-0">
                ⏱️
            </div>
        </div>
    </div>

    <!-- MINIMALIST CALENDAR GRID -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-md p-6 sm:p-8 max-w-xl mx-auto">
        
        <!-- Header Month Title with Chevrons: < November > -->
        <div class="flex items-center justify-between mb-6 pb-2">
            <a 
                href="{{ route('attendance.calendar', ['year' => $prevMonthDate->year, 'month' => $prevMonthDate->month, 'employee_id' => $targetEmployee->id]) }}" 
                class="p-2 rounded-xl text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition"
                title="Previous Month"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>

            <h3 class="text-lg font-extrabold text-slate-900 tracking-tight">
                {{ $monthName }}
            </h3>

            <a 
                href="{{ route('attendance.calendar', ['year' => $nextMonthDate->year, 'month' => $nextMonthDate->month, 'employee_id' => $targetEmployee->id]) }}" 
                class="p-2 rounded-xl text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition"
                title="Next Month"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <!-- Single-Letter Days Header: S M T W T F S -->
        <div class="grid grid-cols-7 gap-2 text-center mb-4">
            @foreach(['S', 'M', 'T', 'W', 'T', 'F', 'S'] as $dayLetter)
                <div class="text-xs font-bold text-slate-400 py-1 uppercase">
                    {{ $dayLetter }}
                </div>
            @endforeach
        </div>

        <!-- Minimalist Number Cells Grid -->
        <div class="grid grid-cols-7 gap-y-3 gap-x-2 text-center">
            
            <!-- Preceding month days (shown in muted gray like screenshot) -->
            @php
                $prevMonthDaysCount = $prevMonthDate->daysInMonth;
                $startPrevDay = $prevMonthDaysCount - $firstDayOfWeek + 1;
            @endphp
            @for($p = 0; $p < $firstDayOfWeek; $p++)
                <div class="flex items-center justify-center">
                    <span class="text-xs sm:text-sm font-semibold text-slate-300 font-mono">
                        {{ $startPrevDay + $p }}
                    </span>
                </div>
            @endfor

            <!-- Active Month Days -->
            @foreach($calendarDays as $dayInfo)
                @php
                    $isSunday = ($dayInfo['dayOfWeek'] === 0);
                    $isToday = $dayInfo['isToday'];
                    $isGreen = $dayInfo['color'] === 'green';
                    $isRed = $dayInfo['color'] === 'red';
                    $isYellow = $dayInfo['color'] === 'yellow';
                    $hasLeaveReason = !empty($dayInfo['leave_reason']);
                @endphp

                <div class="flex flex-col items-center justify-center">
                    <button 
                        type="button"
                        onclick="inspectCalendarDay({{ json_encode($dayInfo) }})"
                        class="w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center text-xs sm:text-sm font-mono transition-all duration-150 cursor-pointer
                            @if($isToday)
                                bg-[#1e3a8a] text-white shadow-md font-extrabold scale-105 ring-2 ring-[#1e3a8a]/50 ring-offset-2
                            @elseif($isYellow)
                                bg-amber-100 text-amber-900 border border-amber-200 font-extrabold hover:bg-amber-200 shadow-2xs
                            @elseif($isGreen)
                                bg-emerald-100 text-emerald-900 border border-emerald-300 font-extrabold hover:bg-emerald-200 shadow-2xs
                            @elseif($isRed)
                                bg-rose-100 text-rose-900 border border-rose-300 font-extrabold hover:bg-rose-200 shadow-2xs
                            @else
                                text-slate-500 font-medium hover:bg-slate-100
                            @endif
                        "
                        title="{{ $dayInfo['date'] }}: {{ $dayInfo['label'] }} {{ $hasLeaveReason ? '— Reason: ' . $dayInfo['leave_reason'] : '' }}"
                    >
                        <span>{{ str_pad($dayInfo['day'], 2, '0', STR_PAD_LEFT) }}</span>
                    </button>
                </div>
            @endforeach

            <!-- Trailing days from next month in light muted gray -->
            @php
                $totalCellsFilled = $firstDayOfWeek + $daysInMonth;
                $remainingCells = (7 - ($totalCellsFilled % 7)) % 7;
            @endphp
            @for($nextDay = 1; $nextDay <= $remainingCells; $nextDay++)
                <div class="flex items-center justify-center">
                    <span class="text-xs sm:text-sm font-semibold text-slate-300 font-mono">
                        {{ str_pad($nextDay, 2, '0', STR_PAD_LEFT) }}
                    </span>
                </div>
            @endfor

        </div>

        <!-- Legend and Instructions below grid -->
        <div class="mt-8 pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between text-[11px] text-slate-500 font-medium gap-2">
            <div class="flex items-center gap-4">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-emerald-100 border border-emerald-400"></span> Present</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-rose-100 border border-rose-400"></span> Absent</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-amber-100 border border-amber-400"></span> Holiday</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-[#1e3a8a]"></span> Today</span>
            </div>
            <span class="text-slate-400">Click any day to view details & leave reason</span>
        </div>

    </div>

</div>

<!-- INTERACTIVE DAY DETAIL & LEAVE REASON INSPECTION MODAL -->
<div id="dayInspectionModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3.5 mb-4">
            <div>
                <span id="modalDate" class="text-xs font-mono font-bold text-[#1e3a8a] block"></span>
                <h3 class="text-base font-extrabold text-slate-900">Attendance & Leave Detail</h3>
            </div>
            <button type="button" onclick="closeDayModal()" class="text-slate-400 hover:text-slate-700 text-xl font-bold p-1">
                &times;
            </button>
        </div>

        <div id="modalBody" class="space-y-4 text-xs">
            <!-- Populated via JS with Leave Reason -->
        </div>

        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-end">
            <button type="button" onclick="closeDayModal()" class="px-5 py-2 rounded-xl bg-[#1e3a8a] hover:bg-[#1e40af] text-white font-bold transition">
                Close
            </button>
        </div>
    </div>
</div>

@if($isSuperAdmin || $isHr)
<!-- CALENDAR HOLIDAY MODAL -->
<div id="calendarHolidayModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 sm:p-7 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
            <div>
                <h3 id="calHolModalTitle" class="text-base font-extrabold text-slate-900">Add Company Holiday</h3>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Will be updated immediately across the corporate calendar</p>
            </div>
            <button type="button" onclick="closeCalendarHolidayModal()" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg text-lg font-bold">
                ✕
            </button>
        </div>

        <form id="calendarHolidayForm" action="{{ route('holidays.store') }}" method="POST" class="space-y-4">
            @csrf
            <div id="calHolMethodField"></div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Holiday Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="calHolName" required placeholder="e.g. Diwali, Republic Day, Founder's Day" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900 font-semibold">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Holiday Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="holiday_date" id="calHolDate" required value="{{ date('Y-m-d') }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900 font-mono font-bold">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Category Type <span class="text-rose-500">*</span></label>
                    <select name="type" id="calHolType" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900 font-semibold">
                        <option value="PUBLIC">Public / Statutory</option>
                        <option value="COMPANY">Company Holiday</option>
                        <option value="FESTIVAL">Festival</option>
                        <option value="OPTIONAL">Optional</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" name="is_optional" id="calHolOptional" value="1" class="rounded text-[#1e3a8a] focus:ring-[#1e3a8a] border-slate-300">
                <label for="calHolOptional" class="text-xs font-semibold text-slate-700 cursor-pointer">
                    Optional / Restricted Holiday
                </label>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Description</label>
                <textarea name="description" id="calHolDesc" rows="2" placeholder="Memo regarding company closure..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeCalendarHolidayModal()" class="px-4 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold transition">
                    Cancel
                </button>
                <button type="submit" id="calHolSubmitBtn" class="px-5 py-2.5 rounded-xl bg-[#1e3a8a] hover:bg-[#1e40af] text-white text-xs font-extrabold shadow-sm transition">
                    Save Holiday
                </button>
            </div>
        </form>
    </div>
</div>

<!-- CALENDAR ASSIGN / MODIFY LEAVE MODAL -->
<div id="calendarLeaveModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 sm:p-7 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Make Leave Change in Calendar</h3>
                <p id="calLeaveSubtitle" class="text-xs text-slate-400 font-medium mt-0.5">Recording leave for {{ $targetEmployee->full_name }}</p>
            </div>
            <button type="button" onclick="closeCalendarLeaveModal()" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg text-lg font-bold">
                ✕
            </button>
        </div>

        <form action="{{ route('leaves.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="employee_id" value="{{ $targetEmployee->id }}">

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Employee</label>
                <input type="text" readonly value="{{ $targetEmployee->full_name }} ({{ $targetEmployee->employee_code }})" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm font-bold text-slate-700">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Leave Type <span class="text-rose-500">*</span></label>
                    <select name="leave_type_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900 font-medium">
                        @foreach($leaveTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }} ({{ $type->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Status <span class="text-rose-500">*</span></label>
                    <select name="status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900 font-bold">
                        <option value="APPROVED">APPROVED (Authorized)</option>
                        <option value="PENDING">PENDING</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Start Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="start_date" id="calLeaveStartDate" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900 font-mono font-bold">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">End Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="end_date" id="calLeaveEndDate" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900 font-mono font-bold">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Reason / Supervisor Remarks <span class="text-rose-500">*</span></label>
                <textarea name="reason" id="calLeaveReason" rows="2.5" required placeholder="Reason for leave record or calendar adjustment..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeCalendarLeaveModal()" class="px-4 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold transition">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#1e3a8a] hover:bg-[#1e40af] text-white text-xs font-extrabold shadow-sm transition">
                    Save Leave to Calendar
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
    const isManagerRole = {{ ($isSuperAdmin || $isHr) ? 'true' : 'false' }};
    const csrfToken = '{{ csrf_token() }}';

    function inspectCalendarDay(dayInfo) {
        const modal = document.getElementById('dayInspectionModal');
        const modalDate = document.getElementById('modalDate');
        const modalBody = document.getElementById('modalBody');

        modalDate.textContent = dayInfo.date + ' (' + dayInfo.dayName + ')';

        let badgeHtml = '';
        if (dayInfo.color === 'green') {
            badgeHtml = '<span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-900 border border-emerald-300">PRESENT ON DUTY</span>';
        } else if (dayInfo.color === 'red') {
            badgeHtml = '<span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-rose-100 text-rose-900 border border-rose-300">ABSENT</span>';
        } else if (dayInfo.color === 'yellow') {
            badgeHtml = '<span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-amber-100 text-amber-950 border border-amber-300">COMPANY HOLIDAY</span>';
        } else {
            badgeHtml = '<span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">UPCOMING</span>';
        }

        let detailsHtml = `
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-medium">Status:</span>
                    ${badgeHtml}
                </div>
        `;

        if (dayInfo.color === 'red' || dayInfo.leave_reason) {
            const reason = dayInfo.leave_reason || 'Unexcused Absence (No check-in punch or leave request submitted)';
            const category = dayInfo.leave_type ? (dayInfo.leave_type + ' (' + (dayInfo.leave_status || 'Applied') + ')') : 'Direct Unplanned Absence';

            detailsHtml += `
                <div class="p-3.5 rounded-xl bg-rose-50/80 border border-rose-200 text-left">
                    <span class="text-[10px] uppercase font-bold text-rose-800 block">Absence / Leave Reason</span>
                    <p class="text-xs sm:text-sm font-extrabold text-rose-950 mt-1 leading-snug">${reason}</p>
                    <div class="flex items-center justify-between text-[11px] text-rose-700 font-semibold mt-2 pt-2 border-t border-rose-200/60">
                        <span>Type: ${category}</span>
                        <span>Logged in System</span>
                    </div>
                </div>
            `;
        }

        if (dayInfo.details) {
            detailsHtml += `
                <div class="flex justify-between border-t border-slate-200 pt-2">
                    <span class="text-slate-500 font-medium">Check-In:</span>
                    <span class="font-mono font-bold text-slate-900 text-sm">${dayInfo.details.check_in}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500 font-medium">Check-Out:</span>
                    <span class="font-mono font-bold text-slate-900 text-sm">${dayInfo.details.check_out}</span>
                </div>
                <div class="flex justify-between border-t border-slate-200 pt-2">
                    <span class="text-slate-500 font-medium">Worked Hours:</span>
                    <span class="font-mono font-extrabold text-[#1e3a8a] text-sm">${dayInfo.details.worked_hours} Hours</span>
                </div>
            `;
            if (dayInfo.details.is_manual) {
                detailsHtml += `
                    <div class="pt-2 text-slate-500 text-[11px] font-medium border-t border-slate-200">
                        <span class="text-[#1e3a8a] font-bold">Note:</span> Adjusted by HR Supervisor.
                    </div>
                `;
            }
        } else if (dayInfo.holiday) {
            detailsHtml += `
                <div class="border-t border-slate-200 pt-2">
                    <span class="text-slate-500 block mb-1 font-medium">Holiday Description:</span>
                    <p class="font-bold text-slate-800">${dayInfo.holiday.description || dayInfo.label}</p>
                </div>
            `;
        }

        detailsHtml += `
            <div class="border-t border-slate-200 pt-2 flex items-center justify-between text-[11px] text-slate-400 font-mono">
                <span>📍 Tech Campus Geofence</span>
                <span>Verified</span>
            </div>
        </div>`;

        // ADMIN / HR QUICK ACTIONS
        if (isManagerRole) {
            detailsHtml += `<div class="mt-4 pt-4 border-t border-slate-100 space-y-2.5">`;

            // Holiday Actions
            if (dayInfo.holiday && dayInfo.holiday.id) {
                const hol = dayInfo.holiday;
                const holNameEsc = hol.name ? hol.name.replace(/'/g, "\\'") : '';
                const holDescEsc = hol.description ? hol.description.replace(/'/g, "\\'") : '';
                detailsHtml += `
                    <div class="p-3 bg-amber-50 rounded-2xl border border-amber-200 flex items-center justify-between gap-2">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-amber-800 block">Holiday Action</span>
                            <span class="text-xs font-extrabold text-amber-950">${hol.name}</span>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <button type="button" onclick="openCalendarHolidayModal(${JSON.stringify(hol).replace(/"/g, '&quot;')})" class="px-2.5 py-1 rounded-lg bg-white hover:bg-amber-100 text-amber-900 font-bold text-xs border border-amber-300 transition cursor-pointer">
                                Edit ✎
                            </button>
                            <form method="POST" action="/holidays/${hol.id}" onsubmit="return confirm('Remove holiday from calendar?');" class="inline">
                                <input type="hidden" name="_token" value="${csrfToken}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-100 hover:bg-rose-200 text-rose-800 font-bold text-xs transition cursor-pointer">
                                    Delete ✕
                                </button>
                            </form>
                        </div>
                    </div>
                `;
            } else {
                detailsHtml += `
                    <button type="button" onclick="openCalendarHolidayModal(null, '${dayInfo.date}')" class="w-full py-2 px-3 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 font-extrabold text-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                        <span>★ Mark Date as Company Holiday</span>
                    </button>
                `;
            }

            // Leave Actions
            detailsHtml += `
                <button type="button" onclick="openCalendarLeaveModal('${dayInfo.date}', '${dayInfo.leave_reason ? dayInfo.leave_reason.replace(/'/g, "\\'") : ''}')" class="w-full py-2 px-3 rounded-xl bg-[#1e3a8a] hover:bg-[#1e40af] text-white font-extrabold text-xs transition flex items-center justify-center gap-1.5 shadow-2xs cursor-pointer">
                    <span>✎ Make Leave Change on this Date</span>
                </button>
            </div>`;
        }

        modalBody.innerHTML = detailsHtml;
        modal.classList.remove('hidden');
    }

    function closeDayModal() {
        const modal = document.getElementById('dayInspectionModal');
        if (modal) modal.classList.add('hidden');
    }

    function openCalendarHolidayModal(holiday = null, date = null) {
        closeDayModal();
        const form = document.getElementById('calendarHolidayForm');
        const methodDiv = document.getElementById('calHolMethodField');
        const title = document.getElementById('calHolModalTitle');
        const submitBtn = document.getElementById('calHolSubmitBtn');

        if (holiday && holiday.id) {
            form.action = '/holidays/' + holiday.id;
            methodDiv.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            title.textContent = 'Modify Holiday: ' + holiday.name;
            submitBtn.textContent = 'Update Holiday';
            document.getElementById('calHolName').value = holiday.name;
            document.getElementById('calHolDate').value = holiday.date || holiday.holiday_date;
            document.getElementById('calHolType').value = holiday.type || 'PUBLIC';
            document.getElementById('calHolOptional').checked = !!holiday.is_optional;
            document.getElementById('calHolDesc').value = holiday.description || '';
        } else {
            form.action = '{{ route('holidays.store') }}';
            methodDiv.innerHTML = '';
            title.textContent = 'Add Company Holiday';
            submitBtn.textContent = 'Save Holiday';
            document.getElementById('calHolName').value = '';
            document.getElementById('calHolDate').value = date || '{{ date('Y-m-d') }}';
            document.getElementById('calHolType').value = 'PUBLIC';
            document.getElementById('calHolOptional').checked = false;
            document.getElementById('calHolDesc').value = '';
        }

        document.getElementById('calendarHolidayModal').classList.remove('hidden');
    }

    function closeCalendarHolidayModal() {
        document.getElementById('calendarHolidayModal').classList.add('hidden');
    }

    function openCalendarLeaveModal(date, existingReason = '') {
        closeDayModal();
        document.getElementById('calLeaveStartDate').value = date;
        document.getElementById('calLeaveEndDate').value = date;
        document.getElementById('calLeaveReason').value = existingReason || 'Adjusted directly by supervisor on calendar.';
        document.getElementById('calendarLeaveModal').classList.remove('hidden');
    }

    function closeCalendarLeaveModal() {
        document.getElementById('calendarLeaveModal').classList.add('hidden');
    }
</script>
@endsection

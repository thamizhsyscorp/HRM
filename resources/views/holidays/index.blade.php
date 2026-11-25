@extends('layouts.app')

@section('title', 'Holiday Management — ' . $year)
@section('page_title', 'Corporate Holiday & Calendar Schedule')

@section('content')
<div class="space-y-6">

    <!-- TOP HEADER & ACTION BAR -->
    <div class="bg-white rounded-xl border border-slate-200/80 p-4 sm:p-5 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-amber-900 bg-amber-50 px-2.5 py-0.5 rounded-full border border-amber-200">
                        Calendar Master
                    </span>
                    <span class="text-xs font-mono font-bold text-slate-500">
                        Year {{ $year }}
                    </span>
                </div>
                <h1 class="text-2xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                    Corporate Holidays
                </h1>
            </div>

            <div class="flex flex-wrap items-center gap-2 sm:gap-2.5 shrink-0">
                <!-- Year Selector Pills -->
                <div class="flex items-center bg-slate-100 p-1 rounded-xl">
                    @foreach($availableYears as $y)
                        <a 
                            href="{{ route('holidays.index', ['year' => $y]) }}" 
                            class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-extrabold transition {{ $year == $y ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-500 hover:text-slate-900' }}"
                        >
                            {{ $y }}
                        </a>
                    @endforeach
                </div>

                <a href="{{ route('attendance.calendar') }}" class="px-3 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold transition flex items-center gap-1.5 bg-white shadow-2xs">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="hidden sm:inline">View Calendar</span>
                    <span class="sm:hidden">Calendar</span>
                </a>

                <!-- Download Sample CSV -->
                <a 
                    href="{{ route('holidays.sample') }}" 
                    class="px-3 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold transition flex items-center gap-1.5 bg-white shadow-2xs"
                    title="Download Sample Holiday CSV File"
                >
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span class="hidden sm:inline">Sample CSV</span>
                    <span class="sm:hidden">Sample</span>
                </a>

                <!-- Import Holidays Button -->
                <button 
                    type="button" 
                    onclick="openImportHolidayModal()" 
                    class="px-3 sm:px-3.5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-extrabold shadow-2xs transition flex items-center gap-1.5 cursor-pointer"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    <span>Import File</span>
                </button>

                <button 
                    type="button" 
                    onclick="openAddHolidayModal()" 
                    class="px-3.5 sm:px-4 py-2 bg-[#1e3a8a] hover:bg-[#1e40af] text-white rounded-xl text-xs font-extrabold shadow-sm transition flex items-center gap-1.5 cursor-pointer"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>+ Add Holiday</span>
                </button>
            </div>
        </div>
    </div>

    <!-- 4 KPI SUMMARY METRIC CARDS -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white p-3 sm:p-3.5 rounded-xl border border-amber-200/70 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[10px] sm:text-[11px] font-bold text-amber-900 uppercase tracking-wider block">Total Holidays</span>
                <span class="text-xl sm:text-2xl font-extrabold text-amber-800 mt-0.5 block font-mono">{{ $totalHolidays }}</span>
                <span class="text-[10px] text-slate-400 font-medium">Recorded in {{ $year }}</span>
            </div>
            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-800 flex items-center justify-center font-black text-sm border border-amber-200 shrink-0">
                ★
            </div>
        </div>

        <div class="bg-white p-3 sm:p-3.5 rounded-xl border border-emerald-200/70 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[10px] sm:text-[11px] font-bold text-emerald-800 uppercase tracking-wider block">Public / Statutory</span>
                <span class="text-xl sm:text-2xl font-extrabold text-emerald-700 mt-0.5 block font-mono">{{ $publicHolidays }}</span>
                <span class="text-[10px] text-slate-400 font-medium">Mandatory closures</span>
            </div>
            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-black text-sm border border-emerald-200 shrink-0">
                🏛️
            </div>
        </div>

        <div class="bg-white p-3 sm:p-3.5 rounded-xl border border-blue-200/70 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[10px] sm:text-[11px] font-bold text-[#1e3a8a] uppercase tracking-wider block">Optional & Company</span>
                <span class="text-xl sm:text-2xl font-extrabold text-[#1e3a8a] mt-0.5 block font-mono">{{ $optionalHolidays }}</span>
                <span class="text-[10px] text-slate-400 font-medium">Floating / cultural</span>
            </div>
            <div class="w-8 h-8 rounded-lg bg-[#eff6ff] text-[#1e3a8a] flex items-center justify-center font-black text-sm border border-[#bfdbfe] shrink-0">
                🏢
            </div>
        </div>

        <div class="bg-white p-3 sm:p-3.5 rounded-xl border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-600 uppercase tracking-wider block">Upcoming Ahead</span>
                <span class="text-xl sm:text-2xl font-extrabold text-slate-900 mt-0.5 block font-mono">{{ $upcomingHolidays }}</span>
                <span class="text-[10px] text-slate-400 font-medium">Remaining in {{ $year }}</span>
            </div>
            <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center font-black text-sm border border-slate-200 shrink-0">
                ⏳
            </div>
        </div>
    </div>

    <!-- HOLIDAYS TABLE -->
    <div class="bg-white rounded-xl shadow-2xs border border-slate-200/80 overflow-hidden">
        <div class="p-3.5 sm:p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 bg-white">
            <div>
                <h3 class="text-sm font-extrabold text-slate-900">Annual Holiday Roster</h3>
                <p class="text-xs text-slate-400">All dates color-coded with subtle yellow on company calendars</p>
            </div>
            <span class="text-xs font-mono font-bold text-slate-500 self-start sm:self-auto">
                {{ $holidays->count() }} Entries
            </span>
        </div>

        <!-- Mobile Holiday Cards (Visible on screens < 640px) -->
        <div class="sm:hidden divide-y divide-slate-100">
            @forelse($holidays as $h)
                @php
                    $cDate = \Carbon\Carbon::parse($h->holiday_date);
                    $isUpcoming = $cDate->gte(today());
                    $isSunday = $cDate->isSunday();
                @endphp
                <div class="p-3.5 space-y-2.5 bg-white">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-900 border border-amber-300 font-extrabold flex flex-col items-center justify-center shrink-0 font-mono leading-none">
                                <span class="text-xs font-black">{{ $cDate->format('d') }}</span>
                                <span class="text-[9px] uppercase font-bold text-amber-800">{{ $cDate->format('M') }}</span>
                            </div>
                            <div>
                                <h4 class="font-extrabold text-slate-900 text-xs leading-tight">{{ $h->name }}</h4>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="text-[10px] font-semibold {{ $isSunday ? 'text-sky-600 font-bold' : 'text-slate-500' }}">{{ $cDate->format('l') }}</span>
                                    <span class="text-slate-300">•</span>
                                    <span class="text-[10px] text-slate-400 font-sans">{{ $cDate->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            @if($isUpcoming)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-amber-50 text-amber-900 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    Upcoming
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-slate-100 text-slate-500">
                                    Passed
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Category and details -->
                    <div class="flex items-center gap-2 flex-wrap text-xs">
                        @if($h->type === 'PUBLIC')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                Public Holiday
                            </span>
                        @elseif($h->type === 'COMPANY')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-blue-50 text-blue-800 border border-blue-200">
                                Company Off
                            </span>
                        @elseif($h->type === 'FESTIVAL')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-amber-50 text-amber-800 border border-amber-200">
                                Festival
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $h->type }}
                            </span>
                        @endif

                        @if($h->is_optional)
                            <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                Optional Leave Day
                            </span>
                        @endif
                    </div>

                    @if($h->description)
                        <p class="text-[11px] text-slate-500 italic bg-slate-50 p-2 rounded-lg border border-slate-100">
                            {{ $h->description }}
                        </p>
                    @endif

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-2 pt-1 border-t border-slate-50">
                        <button 
                            type="button"
                            onclick="openEditHolidayModal({{ $h->id }}, '{{ addslashes($h->name) }}', '{{ $cDate->format('Y-m-d') }}', '{{ $h->type }}', {{ $h->is_optional ? 'true' : 'false' }}, '{{ addslashes($h->description ?? '') }}')"
                            class="px-3 py-1 rounded-lg bg-[#eff6ff] hover:bg-[#e6e4fa] text-[#1e3a8a] font-bold text-xs border border-[#bfdbfe] transition cursor-pointer"
                        >
                            Edit ✎
                        </button>

                        <form method="POST" action="{{ route('holidays.destroy', $h) }}" onsubmit="return confirm('Delete holiday \'{{ addslashes($h->name) }}\'? This will update attendance calendar color coding immediately.');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button 
                                type="submit" 
                                class="px-3 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition cursor-pointer"
                            >
                                Delete ✕
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-slate-400 text-xs">
                    No holidays scheduled for {{ $year }} yet.
                </div>
            @endforelse
        </div>

        <!-- Desktop Holidays Table (Hidden on screens < 640px) -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50/90 border-b border-slate-200/80 text-slate-500 font-bold uppercase text-[11px] tracking-wider">
                    <tr>
                        <th class="py-3.5 px-6 whitespace-nowrap">Date</th>
                        <th class="py-3.5 px-6 whitespace-nowrap">Day of Week</th>
                        <th class="py-3.5 px-6 whitespace-nowrap">Holiday Name</th>
                        <th class="py-3.5 px-6 whitespace-nowrap">Category Type</th>
                        <th class="py-3.5 px-6 whitespace-nowrap">Description</th>
                        <th class="py-3.5 px-6 whitespace-nowrap text-center">Status</th>
                        <th class="py-3.5 px-6 whitespace-nowrap text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 text-xs">
                    @forelse($holidays as $h)
                        @php
                            $cDate = \Carbon\Carbon::parse($h->holiday_date);
                            $isUpcoming = $cDate->gte(today());
                            $isSunday = $cDate->isSunday();
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-6 whitespace-nowrap align-middle">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-900 border border-amber-300 font-extrabold flex items-center justify-center text-xs shrink-0 font-mono">
                                        {{ $cDate->format('d') }}
                                    </div>
                                    <div>
                                        <span class="font-extrabold text-slate-900 block font-mono leading-tight">{{ $cDate->format('M d, Y') }}</span>
                                        <span class="text-[10px] text-slate-400 block mt-0.5 font-sans">{{ $cDate->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="py-4 px-6 whitespace-nowrap align-middle font-semibold {{ $isSunday ? 'text-sky-600 font-bold' : 'text-slate-700' }}">
                                {{ $cDate->format('l') }}
                            </td>

                            <td class="py-4 px-6 whitespace-nowrap align-middle">
                                <span class="font-extrabold text-slate-900 text-sm block">{{ $h->name }}</span>
                                @if($h->is_optional)
                                    <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                        Optional Leave Day
                                    </span>
                                @endif
                            </td>

                            <td class="py-4 px-6 whitespace-nowrap align-middle">
                                @if($h->type === 'PUBLIC')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        Public Holiday
                                    </span>
                                @elseif($h->type === 'COMPANY')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-extrabold bg-blue-50 text-blue-800 border border-blue-200">
                                        Company Off
                                    </span>
                                @elseif($h->type === 'FESTIVAL')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-extrabold bg-amber-50 text-amber-800 border border-amber-200">
                                        Festival
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-extrabold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $h->type }}
                                    </span>
                                @endif
                            </td>

                            <td class="py-4 px-6 align-middle text-slate-600 max-w-xs text-xs">
                                <span class="line-clamp-2 block leading-relaxed" title="{{ $h->description }}">
                                    {{ $h->description ?: 'Official corporate calendar holiday.' }}
                                </span>
                            </td>

                            <td class="py-4 px-6 whitespace-nowrap align-middle text-center">
                                @if($isUpcoming)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-900 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Upcoming
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500">
                                        Passed
                                    </span>
                                @endif
                            </td>

                            <td class="py-4 px-6 whitespace-nowrap align-middle text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button 
                                        type="button"
                                        onclick="openEditHolidayModal({{ $h->id }}, '{{ addslashes($h->name) }}', '{{ $cDate->format('Y-m-d') }}', '{{ $h->type }}', {{ $h->is_optional ? 'true' : 'false' }}, '{{ addslashes($h->description ?? '') }}')"
                                        class="px-2.5 py-1 rounded-lg bg-[#eff6ff] hover:bg-[#e6e4fa] text-[#1e3a8a] font-bold text-xs border border-[#bfdbfe] transition cursor-pointer"
                                    >
                                        Edit ✎
                                    </button>

                                    <form method="POST" action="{{ route('holidays.destroy', $h) }}" onsubmit="return confirm('Delete holiday \'{{ addslashes($h->name) }}\'? This will update attendance calendar color coding immediately.');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="submit" 
                                            class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition cursor-pointer"
                                        >
                                            Delete ✕
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 text-xs">
                                No holidays recorded for {{ $year }}. Click "+ Add Holiday" to configure dates.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- IMPORT HOLIDAY MODAL -->
<div id="importHolidayModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full p-5 sm:p-7 relative max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg font-bold border border-emerald-200">
                    📂
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Import Holidays from CSV</h3>
                    <p class="text-xs text-slate-400 font-medium">Bulk upload company holidays into calendar</p>
                </div>
            </div>
            <button type="button" onclick="closeImportHolidayModal()" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-xl hover:bg-slate-100 transition cursor-pointer text-lg font-bold">
                ✕
            </button>
        </div>

        <form action="{{ route('holidays.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <!-- Sample Download Box -->
            <div class="p-4 rounded-2xl bg-[#eff6ff] border border-[#bfdbfe] flex items-center justify-between gap-3">
                <div>
                    <h4 class="text-xs font-extrabold text-[#1e3a8a]">Need the CSV format?</h4>
                    <p class="text-[11px] text-slate-600 mt-0.5">Download the official template pre-filled with sample dates.</p>
                </div>
                <a 
                    href="{{ route('holidays.sample') }}" 
                    class="px-3 py-1.5 bg-[#1e3a8a] text-white hover:bg-blue-900 rounded-xl text-xs font-bold shrink-0 transition flex items-center gap-1.5 shadow-2xs"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Sample CSV</span>
                </a>
            </div>

            <!-- File Upload Input -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                    Select CSV File <span class="text-rose-500">*</span>
                </label>
                <div class="border-2 border-dashed border-slate-200 hover:border-emerald-600 rounded-2xl p-5 text-center transition cursor-pointer bg-slate-50/50 hover:bg-emerald-50/20" onclick="document.getElementById('holidayCsvInput').click()">
                    <input 
                        type="file" 
                        name="file" 
                        id="holidayCsvInput" 
                        accept=".csv,text/csv,text/plain" 
                        required 
                        class="hidden"
                        onchange="updateFileNameDisplay(this)"
                    >
                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-800 mx-auto flex items-center justify-center mb-2 font-bold text-lg">
                        ↑
                    </div>
                    <p id="csvFileNameDisplay" class="text-xs font-bold text-slate-800">Click to choose a CSV file</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Supports .csv files up to 2MB</p>
                </div>
            </div>

            <!-- Supported Columns Info -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-[11px] text-slate-600 space-y-1">
                <span class="font-extrabold text-slate-800 block">Expected CSV Columns:</span>
                <p><span class="font-mono font-bold text-slate-800">name</span>: Holiday name (e.g. Diwali)</p>
                <p><span class="font-mono font-bold text-slate-800">holiday_date</span>: YYYY-MM-DD (e.g. 2026-11-08)</p>
                <p><span class="font-mono font-bold text-slate-800">type</span>: PUBLIC, COMPANY, FESTIVAL, or OPTIONAL</p>
                <p><span class="font-mono font-bold text-slate-800">is_optional</span>: 0 (No) or 1 (Yes)</p>
                <p><span class="font-mono font-bold text-slate-800">description</span>: Optional notes</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button 
                    type="button" 
                    onclick="closeImportHolidayModal()" 
                    class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer"
                >
                    Cancel
                </button>
                <button 
                    type="submit" 
                    class="px-5 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    <span>Upload & Import</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ADD HOLIDAY MODAL -->
<div id="addHolidayModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 sm:p-7 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Add Company Holiday</h3>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Will be highlighted on all workforce calendars</p>
            </div>
            <button type="button" onclick="closeAddHolidayModal()" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg text-lg font-bold">
                ✕
            </button>
        </div>

        <form action="{{ route('holidays.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Holiday Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="e.g. Diwali, Christmas, Annual Founders Day" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900 font-semibold">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Holiday Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="holiday_date" id="addHolidayDateInput" required value="{{ date('Y-m-d') }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900 font-mono font-bold">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Category Type <span class="text-rose-500">*</span></label>
                    <select name="type" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900 font-semibold">
                        <option value="PUBLIC">Public / Statutory</option>
                        <option value="COMPANY">Company Holiday</option>
                        <option value="FESTIVAL">Festival</option>
                        <option value="OPTIONAL">Optional</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" name="is_optional" id="addIsOptional" value="1" class="rounded text-[#1e3a8a] focus:ring-[#1e3a8a] border-slate-300">
                <label for="addIsOptional" class="text-xs font-semibold text-slate-700 cursor-pointer">
                    Optional / Restricted Holiday (Employees can elect to take this off)
                </label>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Description / Memo</label>
                <textarea name="description" rows="2" placeholder="Brief note explaining company policy for this holiday..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeAddHolidayModal()" class="px-4 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold transition">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#1e3a8a] hover:bg-[#1e40af] text-white text-xs font-extrabold shadow-sm transition">
                    Save Holiday
                </button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT HOLIDAY MODAL -->
<div id="editHolidayModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 sm:p-7 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Modify Holiday</h3>
                <p id="editModalSubTitle" class="text-xs text-slate-400 font-medium mt-0.5">Adjust date and policy details</p>
            </div>
            <button type="button" onclick="closeEditHolidayModal()" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg text-lg font-bold">
                ✕
            </button>
        </div>

        <form id="editHolidayForm" action="" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Holiday Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="editHolidayName" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900 font-semibold">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Holiday Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="holiday_date" id="editHolidayDate" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900 font-mono font-bold">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Category Type <span class="text-rose-500">*</span></label>
                    <select name="type" id="editHolidayType" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900 font-semibold">
                        <option value="PUBLIC">Public / Statutory</option>
                        <option value="COMPANY">Company Holiday</option>
                        <option value="FESTIVAL">Festival</option>
                        <option value="OPTIONAL">Optional</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" name="is_optional" id="editHolidayOptional" value="1" class="rounded text-[#1e3a8a] focus:ring-[#1e3a8a] border-slate-300">
                <label for="editHolidayOptional" class="text-xs font-semibold text-slate-700 cursor-pointer">
                    Optional / Restricted Holiday
                </label>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Description / Memo</label>
                <textarea name="description" id="editHolidayDesc" rows="2" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeEditHolidayModal()" class="px-4 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold transition">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#1e3a8a] hover:bg-[#1e40af] text-white text-xs font-extrabold shadow-sm transition">
                    Update Holiday
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openAddHolidayModal(prefillDate = null) {
        if (prefillDate) {
            document.getElementById('addHolidayDateInput').value = prefillDate;
        }
        document.getElementById('addHolidayModal').classList.remove('hidden');
    }

    function closeAddHolidayModal() {
        document.getElementById('addHolidayModal').classList.add('hidden');
    }

    function openEditHolidayModal(id, name, date, type, isOptional, description) {
        const form = document.getElementById('editHolidayForm');
        form.action = '/holidays/' + id;
        document.getElementById('editHolidayName').value = name;
        document.getElementById('editHolidayDate').value = date;
        document.getElementById('editHolidayType').value = type;
        document.getElementById('editHolidayOptional').checked = isOptional;
        document.getElementById('editHolidayDesc').value = description;
        document.getElementById('editModalSubTitle').textContent = 'Editing ' + name + ' (' + date + ')';
        document.getElementById('editHolidayModal').classList.remove('hidden');
    }

    function closeEditHolidayModal() {
        document.getElementById('editHolidayModal').classList.add('hidden');
    }

    function openImportHolidayModal() {
        document.getElementById('importHolidayModal').classList.remove('hidden');
    }

    function closeImportHolidayModal() {
        document.getElementById('importHolidayModal').classList.add('hidden');
    }

    function updateFileNameDisplay(input) {
        const display = document.getElementById('csvFileNameDisplay');
        if (input.files && input.files[0]) {
            display.textContent = 'Selected: ' + input.files[0].name;
            display.classList.add('text-emerald-700');
        } else {
            display.textContent = 'Click to choose a CSV file';
            display.classList.remove('text-emerald-700');
        }
    }
</script>
@endsection

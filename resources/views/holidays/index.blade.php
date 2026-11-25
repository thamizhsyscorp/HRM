@extends('layouts.app')

@section('title', 'Holiday Management — ' . $year)
@section('page_title', 'Corporate Holiday & Calendar Schedule')

@section('content')
<div class="space-y-6">

    <!-- TOP HEADER & ACTION BAR -->
    <div class="bg-white rounded-2xl border border-slate-100 p-5 sm:p-7 shadow-md">
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
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Corporate Holidays
                </h1>
                <p class="text-xs sm:text-sm text-slate-400 font-medium mt-0.5">
                    Configure official company holidays, festival leaves, and calendar closures.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <!-- Year Selector Pills -->
                <div class="flex items-center bg-slate-100 p-1 rounded-xl">
                    @foreach($availableYears as $y)
                        <a 
                            href="{{ route('holidays.index', ['year' => $y]) }}" 
                            class="px-3 py-1.5 rounded-lg text-xs font-extrabold transition {{ $year == $y ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-500 hover:text-slate-900' }}"
                        >
                            {{ $y }}
                        </a>
                    @endforeach
                </div>

                <a href="{{ route('attendance.calendar') }}" class="px-3.5 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold transition flex items-center gap-1.5 bg-white shadow-2xs">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>View Calendar</span>
                </a>

                <button 
                    type="button" 
                    onclick="openAddHolidayModal()" 
                    class="px-4 py-2 bg-[#1e3a8a] hover:bg-[#1e40af] text-white rounded-xl text-xs font-extrabold shadow-sm transition flex items-center gap-1.5 cursor-pointer"
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
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-amber-100 shadow-md flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-amber-900 uppercase tracking-wider block">Total Holidays</span>
                <span class="text-2xl sm:text-3xl font-extrabold text-amber-800 mt-1 block font-mono">{{ $totalHolidays }}</span>
                <span class="text-[10px] text-slate-400 font-medium">Recorded in {{ $year }}</span>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-800 flex items-center justify-center font-black text-base border border-amber-200">
                ★
            </div>
        </div>

        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-emerald-100 shadow-md flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider block">Public / Statutory</span>
                <span class="text-2xl sm:text-3xl font-extrabold text-emerald-700 mt-1 block font-mono">{{ $publicHolidays }}</span>
                <span class="text-[10px] text-slate-400 font-medium">Mandatory closures</span>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-black text-base border border-emerald-200">
                🏛️
            </div>
        </div>

        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-indigo-100 shadow-md flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-[#1e3a8a] uppercase tracking-wider block">Optional & Company</span>
                <span class="text-2xl sm:text-3xl font-extrabold text-[#1e3a8a] mt-1 block font-mono">{{ $optionalHolidays }}</span>
                <span class="text-[10px] text-slate-400 font-medium">Floating / cultural days</span>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-[#eff6ff] text-[#1e3a8a] flex items-center justify-center font-black text-base border border-[#bfdbfe]">
                🏢
            </div>
        </div>

        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-100 shadow-md flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider block">Upcoming Ahead</span>
                <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1 block font-mono">{{ $upcomingHolidays }}</span>
                <span class="text-[10px] text-slate-400 font-medium">Remaining this calendar year</span>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center font-black text-base border border-slate-200">
                ⏳
            </div>
        </div>
    </div>

    <!-- HOLIDAYS TABLE -->
    <div class="bg-white rounded-2xl shadow-md border border-slate-100 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-white">
            <div>
                <h3 class="text-sm font-extrabold text-slate-900">Annual Holiday Roster</h3>
                <p class="text-xs text-slate-400">All dates color-coded with subtle yellow on company calendars</p>
            </div>
            <span class="text-xs font-mono font-bold text-slate-500">
                {{ $holidays->count() }} Entries
            </span>
        </div>

        <div class="overflow-x-auto">
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
</script>
@endsection

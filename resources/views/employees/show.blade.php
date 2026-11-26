@extends('layouts.app')

@section('title', $employee->full_name . ' — Profile')
@section('page_title', 'Employee 360° Profile View')

@section('content')
<div class="space-y-6">

    <!-- Top Profile Header Card -->
    <div class="bg-white rounded-2xl border border-slate-100 p-6 sm:p-8 shadow-md">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-[#1e3a8a] to-[#1e3a8a] text-white font-extrabold text-2xl flex items-center justify-center shadow-lg border border-indigo-400/40 shrink-0">
                    {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name ?? '', 0, 1)) }}
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2.5">
                        <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">{{ $employee->full_name }}</h2>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-[#eff6ff] text-[#1e3a8a] border border-[#bfdbfe]">
                            {{ $employee->employment_status }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-slate-100 text-slate-600">
                            {{ $employee->employee_code }}
                        </span>
                    </div>
                    <p class="text-xs text-[#1e3a8a] font-bold mt-1">{{ $employee->designation->name ?? 'Staff Role' }} &bull; {{ $employee->department->name ?? 'General' }}</p>
                    <p class="text-xs text-slate-400 mt-0.5 font-mono">{{ $employee->work_email }} &bull; {{ $employee->phone ?? 'No phone' }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if(auth()->user()->employee_id === $employee->id || auth()->id() === $employee->user_id)
                    <button type="button" onclick="openChangePasswordModal()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition cursor-pointer">
                        Change Password 🔑
                    </button>
                @endif
                @if($isSuperAdmin || $isHr)
                    <a href="{{ route('employees.edit', $employee) }}" class="px-4 py-2 rounded-xl bg-[#1e3a8a] hover:bg-[#1e40af] text-white text-xs font-bold transition shadow-sm">
                        Edit Profile ✎
                    </a>
                @endif
                <a href="{{ route('attendance.calendar', ['employee_id' => $employee->id]) }}" class="px-4 py-2 rounded-xl bg-[#eff6ff] hover:bg-[#dbeafe] text-[#1e3a8a] text-xs font-bold border border-[#bfdbfe] transition">
                    Attendance Calendar 🗓️
                </a>
                <a href="{{ route('employees.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold transition">
                    ← Staff Directory
                </a>
            </div>
        </div>
    </div>

    <!-- Grid: Job Details & Compensation Structure -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Job Details & Hierarchy (5 Cols) -->
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-md">
                <h3 class="text-sm font-extrabold text-slate-900 mb-4 border-b border-slate-100 pb-3">Organization & Placement</h3>
                <div class="space-y-3.5 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-medium">Employee Code:</span>
                        <span class="font-mono font-bold text-[#1e3a8a]">{{ $employee->employee_code }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-medium">Department:</span>
                        <span class="font-bold text-slate-800">{{ $employee->department->name ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-medium">Designation:</span>
                        <span class="font-bold text-slate-800">{{ $employee->designation->name ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-medium">Reporting Manager:</span>
                        <span class="font-bold text-slate-800">{{ $employee->manager->full_name ?? 'Top Executive' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-medium">Work Location:</span>
                        <span class="font-bold text-slate-800">{{ $employee->workLocation->name ?? 'Main Tech Campus' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-medium">Joining Date:</span>
                        <span class="font-bold text-slate-800">{{ $employee->joining_date }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-medium">Employment Type:</span>
                        <span class="font-bold text-[#1e3a8a]">{{ $employee->employment_type }}</span>
                    </div>
                </div>
            </div>

            <!-- Emergency Contact & Bank Info -->
            <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-md">
                <h3 class="text-sm font-extrabold text-slate-900 mb-4 border-b border-slate-100 pb-3">Disbursal & Emergency Contact</h3>
                <div class="space-y-3.5 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-medium">Emergency Person:</span>
                        <span class="font-bold text-slate-800">{{ $employee->emergency_contact_name ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-medium">Emergency Phone:</span>
                        <span class="font-bold text-slate-800 font-mono">{{ $employee->emergency_contact_phone ?? '—' }}</span>
                    </div>
                    @if($employee->bankAccounts->count() > 0)
                        @php $bank = $employee->bankAccounts->first(); @endphp
                        <div class="border-t border-slate-100 pt-3">
                            <span class="text-slate-400 block text-[10px] uppercase font-bold mb-1">Primary Bank Account</span>
                            <p class="font-bold text-slate-800">{{ $bank->bank_name }}</p>
                            <p class="text-slate-500 font-mono text-[11px]">IFSC: {{ $bank->ifsc_code }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Compensation & Salary Breakdown (7 Cols) -->
        <div class="lg:col-span-7 space-y-6">
            @if($employee->user?->hasRole('super-admin'))
                <div class="bg-gradient-to-br from-blue-900 to-[#1e3a8a] text-white rounded-2xl p-6 shadow-md border border-blue-800">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-lg">👑</span>
                        <div>
                            <h3 class="text-base font-extrabold text-white">Executive Profile — Company Owner</h3>
                            <p class="text-xs text-blue-200">Executive & Ownership Tier</p>
                        </div>
                    </div>
                    <p class="text-xs text-blue-100 leading-relaxed">
                        This profile belongs to the <strong>Company Owner / Super Admin</strong>. As company executive, this account is exempt from standard workforce time-tracking, attendance mandates, and statutory employee payroll deductions (PF, ESI, Professional Tax).
                    </p>
                    <div class="mt-4 pt-4 border-t border-white/10 flex flex-wrap items-center gap-2.5">
                        <span class="px-2.5 py-1 rounded-lg bg-emerald-500/20 text-emerald-200 border border-emerald-400/30 text-[11px] font-bold">
                            ✓ Workforce Payroll Exempt
                        </span>
                        <span class="px-2.5 py-1 rounded-lg bg-white/10 text-blue-200 border border-white/20 text-[11px] font-bold">
                            ✓ Attendance Mandate Exempt
                        </span>
                        <span class="px-2.5 py-1 rounded-lg bg-white/10 text-blue-200 border border-white/20 text-[11px] font-bold">
                            ✓ Statutory Deduction Exempt
                        </span>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-md">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Compensation Structure & Salary Breakdown</h3>
                        <p class="text-xs text-slate-400">Configures monthly earnings and statutory deductions</p>
                    </div>
                    <div class="flex items-center gap-3 text-right">
                        <div>
                            <span class="text-[10px] text-slate-400 block font-medium">Total Monthly Earnings</span>
                            <span class="text-sm font-extrabold text-[#1e3a8a] font-mono">
                                ₹{{ number_format($employee->salaryComponents->where('salaryComponent.category', 'EARNING')->sum('amount'), 2) }}
                            </span>
                        </div>
                        <div class="h-6 w-px bg-slate-200"></div>
                        <div>
                            <span class="text-[10px] text-rose-500 block font-medium">Total Deductions</span>
                            <span class="text-sm font-extrabold text-rose-600 font-mono">
                                -₹{{ number_format($employee->salaryComponents->where('salaryComponent.category', 'DEDUCTION')->sum('amount'), 2) }}
                            </span>
                        </div>
                        <div class="h-6 w-px bg-slate-200"></div>
                        <div class="bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                            <span class="text-[9px] text-emerald-700 block font-bold uppercase tracking-wider">Take-Home</span>
                            <span class="text-sm font-black text-emerald-700 font-mono">
                                ₹{{ number_format(max(0, $employee->salaryComponents->where('salaryComponent.category', 'EARNING')->sum('amount') - $employee->salaryComponents->where('salaryComponent.category', 'DEDUCTION')->sum('amount')), 2) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-100">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50/90 border-b border-slate-200/80 text-slate-500 font-bold uppercase text-[11px] tracking-wider">
                            <tr>
                                <th class="py-3 px-5 text-xs font-bold tracking-wider">Component</th>
                                <th class="py-3 px-5 text-xs font-bold tracking-wider">Category</th>
                                <th class="py-3 px-5 text-xs font-bold tracking-wider text-right">Amount (INR)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-xs">
                            @forelse($employee->salaryComponents as $comp)
                                <tr class="hover:bg-slate-50/70 transition-colors cursor-pointer">
                                    <td class="py-3.5 px-5 font-bold text-slate-900">
                                        {{ $comp->salaryComponent->name ?? 'Component' }}
                                        <span class="block text-[10px] text-slate-400 font-mono">{{ $comp->salaryComponent->code ?? '' }}</span>
                                    </td>
                                    <td class="py-3.5 px-5">
                                        @if(($comp->salaryComponent->category ?? '') === 'EARNING')
                                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-extrabold bg-emerald-100 text-emerald-900 border border-emerald-300">EARNING</span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-extrabold bg-amber-50 text-amber-800 border border-amber-200">DEDUCTION</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-5 text-right font-mono font-bold text-slate-900">
                                        ₹{{ number_format($comp->amount, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-6 text-center text-slate-400 text-xs">
                                        No individual salary components assigned yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Recent Attendance & Leave History -->
            <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-md">
                <h3 class="text-sm font-extrabold text-slate-900 mb-3 border-b border-slate-100 pb-3">Recent Daily Punches</h3>
                <div class="space-y-2 text-xs">
                    @forelse($employee->attendances as $att)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200/60">
                            <div>
                                <span class="font-extrabold text-slate-900">{{ \Carbon\Carbon::parse($att->attendance_date)->format('M d, Y') }}</span>
                                <span class="text-slate-400 text-[11px] ml-2">In: {{ $att->first_check_in_at ? \Carbon\Carbon::parse($att->first_check_in_at)->format('h:i A') : '—' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-[#eff6ff] text-[#1e3a8a] border border-[#bfdbfe]">
                                    {{ $att->status }} ({{ round(($att->worked_minutes ?? 0)/60, 1) }} hrs)
                                </span>
                                <a 
                                    href="{{ $att->maps_url }}" 
                                    target="_blank" 
                                    rel="noopener noreferrer" 
                                    class="p-1 rounded-lg bg-white hover:bg-blue-50 text-[#1e3a8a] border border-slate-200 transition shadow-2xs group inline-flex items-center justify-center cursor-pointer" 
                                    title="View registered GPS location on Google Maps"
                                >
                                    <svg class="w-3.5 h-3.5 text-[#1e3a8a] group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-400 text-xs py-4 text-center">No attendance punches recorded yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

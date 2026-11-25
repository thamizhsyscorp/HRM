@extends('layouts.app')

@section('title', 'Payroll & Salary')
@section('page_title', 'Payroll & Take-Home Salary')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    {{-- ========================================================================= --}}
    {{-- 1. EMPLOYEE VIEW (Clean & Simple: Basic Salary, Deductions, Take-Home)    --}}
    {{-- ========================================================================= --}}
    @if(!$isSuperAdmin && !$isHr)

        <!-- Header Greeting -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 sm:p-4.5 rounded-xl border border-slate-200/90 shadow-2xs">
            <div>
                <h2 class="text-base font-extrabold text-slate-900">
                    Salary & Take-Home Overview
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Your monthly salary breakdown, applicable deductions, and accrued take-home pay based on <strong>{{ $myTakeHomeData['attended_days'] ?? 0 }} days attended</strong> of <strong>{{ $workingDaysInMonth }} working days</strong>.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-mono font-bold">
                    {{ now()->format('F Y') }} (Day {{ $elapsedDays }} of {{ $daysInMonth }})
                </span>
                <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold font-mono">
                    {{ $myTakeHomeData['attended_days'] ?? 0 }} Days Attended
                </span>
            </div>
        </div>

        <!-- 3 Primary Clean Metric Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5 sm:gap-4">
            <!-- 1. Basic Salary -->
            <div class="bg-white p-4 sm:p-5 rounded-xl border border-slate-200 shadow-2xs flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Basic Salary</span>
                    <span class="w-7 h-7 rounded-lg bg-blue-50 text-[#1e3a8a] flex items-center justify-center font-bold text-xs">₹</span>
                </div>
                <div class="my-2">
                    <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-mono tracking-tight">
                        ₹{{ number_format($myBasic > 0 ? $myBasic : ($myTakeHomeData['monthly_gross'] ?? 0), 2) }}
                    </span>
                </div>
                <p class="text-[11px] text-slate-400 font-medium">Monthly base compensation</p>
            </div>

            <!-- 2. Applicable Deductions -->
            <div class="bg-white p-4 sm:p-5 rounded-xl border border-slate-200 shadow-2xs flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Deductions</span>
                    @if(($myTakeHomeData['monthly_deductions'] ?? 0) > 0)
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                            Applicable
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                            None Applicable
                        </span>
                    @endif
                </div>
                <div class="my-2">
                    <span class="text-2xl sm:text-3xl font-extrabold font-mono tracking-tight {{ ($myTakeHomeData['monthly_deductions'] ?? 0) > 0 ? 'text-amber-800' : 'text-slate-400' }}">
                        ₹{{ number_format($myTakeHomeData['monthly_deductions'] ?? 0, 2) }}
                    </span>
                </div>
                <p class="text-[11px] text-slate-400 font-medium">
                    @if(($myTakeHomeData['monthly_deductions'] ?? 0) > 0)
                        Statutory EPF & Professional Tax (PT)
                    @else
                        Zero deductions — 100% take-home
                    @endif
                </p>
            </div>

            <!-- 3. Monthly Take-Home Pay -->
            <div class="bg-gradient-to-br from-emerald-50 via-white to-emerald-50/30 p-4 sm:p-5 rounded-xl border-2 border-emerald-300 shadow-2xs flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Monthly Take-Home Pay</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-600 text-white">Net Pay</span>
                </div>
                <div class="my-2">
                    <span class="text-2xl sm:text-3xl font-extrabold text-emerald-900 font-mono tracking-tight">
                        ₹{{ number_format($myTakeHomeData['monthly_net'] ?? 0, 2) }}
                    </span>
                </div>
                <p class="text-[11px] text-emerald-700 font-semibold">Net amount credited to your bank</p>
            </div>
        </div>

        <!-- Real-Time Take-Home Accrued by Today Banner -->
        <div class="bg-gradient-to-r from-slate-900 via-[#1e3a8a] to-slate-900 text-white p-4 sm:p-5 rounded-xl shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-300 bg-emerald-950/80 px-2 py-0.5 rounded-full border border-emerald-500/30">
                    ⚡ Real-Time Accrual
                </span>
                <h3 class="text-xl sm:text-2xl font-extrabold font-mono mt-1.5 text-white">
                    ₹{{ number_format($myTakeHomeData['take_home_by_today'] ?? 0, 2) }}
                </h3>
                <p class="text-xs text-slate-300 mt-1">
                    Your earned take-home salary accrued through today based on <strong>{{ $myTakeHomeData['attended_days'] ?? 0 }} days attended of {{ $workingDaysInMonth }} working days</strong> (paid holidays are included in the amount).
                </p>
            </div>
            <div class="bg-white/10 backdrop-blur-md px-4 py-2.5 rounded-xl border border-white/10 shrink-0 text-left sm:text-right w-full sm:w-auto">
                <span class="text-[10px] uppercase font-bold text-indigo-200 block">Daily Earning Rate</span>
                <span class="text-base font-extrabold font-mono text-emerald-300 block">
                    ₹{{ number_format($myTakeHomeData['daily_rate'] ?? 0, 2) }} / day
                </span>
                <span class="text-[10px] text-indigo-200/80">Calculated over {{ $workingDaysInMonth }} working days</span>
            </div>
        </div>

        <!-- Deductions & Earnings Itemized Breakdown -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Deductions Card -->
            <div class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-2xs">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5 mb-2.5">
                    <h4 class="text-xs font-extrabold text-slate-900">Deductions Breakdown</h4>
                    <span class="text-xs font-mono font-bold text-amber-800">
                        Total: ₹{{ number_format($myTakeHomeData['monthly_deductions'] ?? 0, 2) }}
                    </span>
                </div>

                @if($myDeductionsList->count() > 0 && ($myTakeHomeData['monthly_deductions'] ?? 0) > 0)
                    <div class="divide-y divide-slate-100 text-xs">
                        @foreach($myDeductionsList as $ded)
                            <div class="py-2.5 flex items-center justify-between">
                                <div>
                                    <span class="font-bold text-slate-800 block">{{ $ded->salaryComponent->name }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $ded->salaryComponent->code }}</span>
                                </div>
                                <span class="font-mono font-bold text-amber-800">
                                    - ₹{{ number_format($ded->amount, 2) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-8 text-center text-slate-400 text-xs">
                        <span class="text-2xl block mb-1">🎉</span>
                        No deductions applicable to your salary.
                    </div>
                @endif
            </div>

            <!-- Earnings Card -->
            <div class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-2xs">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5 mb-2.5">
                    <h4 class="text-xs font-extrabold text-slate-900">Earnings Components</h4>
                    <span class="text-xs font-mono font-bold text-emerald-800">
                        Gross: ₹{{ number_format($myTakeHomeData['monthly_gross'] ?? 0, 2) }}
                    </span>
                </div>

                @php
                    $myEarnings = $mySalaryComponents->where('salaryComponent.category', 'EARNING');
                @endphp

                @if($myEarnings->count() > 0)
                    <div class="divide-y divide-slate-100 text-xs">
                        @foreach($myEarnings as $earn)
                            <div class="py-2.5 flex items-center justify-between">
                                <div>
                                    <span class="font-bold text-slate-800 block">{{ $earn->salaryComponent->name }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $earn->salaryComponent->code }}</span>
                                </div>
                                <span class="font-mono font-bold text-slate-900">
                                    + ₹{{ number_format($earn->amount, 2) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-2.5 flex items-center justify-between text-xs">
                        <span class="font-bold text-slate-800">Basic Salary</span>
                        <span class="font-mono font-bold text-slate-900">₹{{ number_format($myBasic, 2) }}</span>
                    </div>
                @endif
            </div>
        </div>

    {{-- ========================================================================= --}}
    {{-- 2. ADMIN / HR VIEW (Workforce Overview & Clean Employee Table)             --}}
    {{-- ========================================================================= --}}
    @else

        <!-- Top Monthly Company Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 sm:gap-4">
            <!-- Gross Monthly Earnings -->
            <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Gross Monthly Earnings</span>
                <div class="my-1.5">
                    <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-mono">₹{{ number_format($totalMonthlyEarnings, 2) }}</span>
                </div>
                <p class="text-[11px] text-slate-400 font-medium">Aggregated basic & allowances across personnel</p>
            </div>

            <!-- Monthly Deductions -->
            <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Monthly Deductions</span>
                <div class="my-1.5">
                    <span class="text-2xl sm:text-3xl font-extrabold text-amber-800 font-mono">₹{{ number_format($totalMonthlyDeductions, 2) }}</span>
                </div>
                <p class="text-[11px] text-slate-400 font-medium">Statutory PF, PT & tax retentions</p>
            </div>

            <!-- Net Monthly Disbursal (Take-Home) -->
            <div class="bg-gradient-to-br from-emerald-50 via-white to-emerald-50/30 p-3.5 sm:p-4 rounded-xl border-2 border-emerald-300 shadow-2xs flex flex-col justify-between">
                <span class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider">Net Monthly Disbursal</span>
                <div class="my-1.5">
                    <span class="text-2xl sm:text-3xl font-extrabold text-emerald-900 font-mono">₹{{ number_format($netMonthlyPayroll, 2) }}</span>
                </div>
                <p class="text-[11px] text-emerald-700 font-semibold">Total workforce take-home disbursal</p>
            </div>
        </div>

        <!-- Clean Staff Take-Home Ledger Table -->
        <div class="bg-white rounded-xl shadow-2xs border border-slate-200/90 overflow-hidden">
            <div class="p-3.5 sm:p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">
                        Staff Salary, Deductions & Take-Home Ledger
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Active staff compensation, monthly deductions, and pro-rata take-home accrued by today.
                    </p>
                </div>
                <span class="px-3 py-1 rounded-xl bg-slate-100 text-slate-700 text-xs font-mono font-bold self-start sm:self-auto">
                    {{ $employeeTakeHomes->count() }} Active Staff
                </span>
            </div>

            <!-- Mobile Staff Cards (Visible on screens < 640px) -->
            <div class="sm:hidden divide-y divide-slate-100">
                @forelse($employeeTakeHomes as $eth)
                    <div class="p-3.5 space-y-2.5 bg-white">
                        <!-- Top: Staff Member & Department -->
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-full bg-[#eff6ff] text-[#1e3a8a] font-bold flex items-center justify-center text-xs shrink-0">
                                    {{ strtoupper(substr($eth['employee']->first_name, 0, 1) . substr($eth['employee']->last_name ?? '', 0, 1)) }}
                                </div>
                                <div>
                                    <span class="font-extrabold text-slate-900 text-xs block leading-tight">{{ $eth['employee']->full_name }}</span>
                                    <span class="text-[10px] font-mono text-slate-400 block mt-0.5">
                                        {{ $eth['employee']->employee_code }} • {{ $eth['employee']->department->name ?? '—' }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-[9px] uppercase font-bold text-slate-400 block">Monthly Net</span>
                                <span class="font-mono font-extrabold text-xs text-emerald-800">
                                    ₹{{ number_format($eth['monthly_net'], 2) }}
                                </span>
                            </div>
                        </div>

                        <!-- Salary & Deductions Grid -->
                        <div class="grid grid-cols-2 gap-2 bg-slate-50/80 p-2 rounded-lg border border-slate-100 text-xs">
                            <div>
                                <span class="text-[10px] text-slate-500 font-semibold block">Basic Salary</span>
                                <span class="font-mono font-bold text-slate-900">₹{{ number_format($eth['basic_salary'], 2) }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-slate-500 font-semibold block">Monthly Deductions</span>
                                @if($eth['monthly_deductions'] > 0)
                                    <span class="font-mono font-bold text-amber-800">- ₹{{ number_format($eth['monthly_deductions'], 2) }}</span>
                                @else
                                    <span class="font-mono text-slate-400">₹0.00</span>
                                @endif
                            </div>
                        </div>

                        <!-- Pro-Rata Accrued Strip -->
                        <div class="flex items-center justify-between p-2 rounded-lg bg-blue-50/60 border border-blue-200/60 text-xs">
                            <div>
                                <span class="text-[10px] text-[#1e3a8a] font-bold block">Attended Days</span>
                                <span class="font-mono text-xs font-extrabold text-slate-900">{{ $eth['attended_days'] }} / {{ $workingDaysInMonth }} days</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-[#1e3a8a] font-bold block">Take-Home by Today</span>
                                <span class="font-mono font-extrabold text-xs text-[#1e3a8a]">₹{{ number_format($eth['take_home_by_today'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-400 text-xs">
                        No active personnel registered in the database.
                    </div>
                @endforelse
            </div>

            <!-- Desktop Staff Table (Hidden on screens < 640px) -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[700px]">
                    <thead class="bg-slate-50/90 border-b border-slate-200 text-slate-500 font-bold uppercase text-[11px] tracking-wider">
                        <tr>
                            <th class="py-3.5 px-5 text-xs">Staff Member</th>
                            <th class="py-3.5 px-4 text-xs">Department</th>
                            <th class="py-3.5 px-4 text-xs">Basic Salary</th>
                            <th class="py-3.5 px-4 text-xs">Deductions</th>
                            <th class="py-3.5 px-4 text-xs">Monthly Take-Home</th>
                            <th class="py-3.5 px-4 text-xs">Attended Days</th>
                            <th class="py-3.5 px-5 text-xs text-right">Take-Home by Today</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 text-xs">
                        @forelse($employeeTakeHomes as $eth)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <!-- Staff Member -->
                                <td class="py-4 px-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-[#eff6ff] text-[#1e3a8a] font-bold flex items-center justify-center text-xs shrink-0">
                                            {{ strtoupper(substr($eth['employee']->first_name, 0, 1) . substr($eth['employee']->last_name ?? '', 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="font-extrabold text-slate-900 block">{{ $eth['employee']->full_name }}</span>
                                            <span class="text-[11px] font-mono text-slate-400">{{ $eth['employee']->employee_code }}</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Department -->
                                <td class="py-4 px-4 text-slate-700">
                                    {{ $eth['employee']->department->name ?? '—' }}
                                </td>

                                <!-- Basic Salary -->
                                <td class="py-4 px-4 font-mono font-bold text-slate-900">
                                    ₹{{ number_format($eth['basic_salary'], 2) }}
                                </td>

                                <!-- Deductions -->
                                <td class="py-4 px-4 font-mono">
                                    @if($eth['monthly_deductions'] > 0)
                                        <span class="font-bold text-amber-800">
                                            - ₹{{ number_format($eth['monthly_deductions'], 2) }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">₹0.00</span>
                                    @endif
                                </td>

                                <!-- Monthly Take-Home -->
                                <td class="py-4 px-4 font-mono font-bold text-emerald-800 text-sm">
                                    ₹{{ number_format($eth['monthly_net'], 2) }}
                                </td>

                                <!-- Attended Days -->
                                <td class="py-4 px-4 font-mono text-slate-700">
                                    <span class="font-extrabold text-slate-900">{{ $eth['attended_days'] }} days</span>
                                    <span class="text-[10px] text-slate-400 block font-sans">of {{ $workingDaysInMonth }} working days</span>
                                </td>

                                <!-- Take-Home by Today -->
                                <td class="py-4 px-5 font-mono font-extrabold text-sm text-[#1e3a8a] text-right">
                                    ₹{{ number_format($eth['take_home_by_today'], 2) }}
                                    <span class="text-[10px] text-emerald-700 font-semibold block font-sans">Paid holidays included</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">
                                    No active personnel registered in the database.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Corporate Settings & Structures (Clean Collapsible/Tab Bottom Cards) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 pt-4">
            <!-- Active Salary Components Breakdown -->
            <div class="lg:col-span-7 bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Configured Salary Components</h3>
                        <p class="text-xs text-slate-400">Company earning and deduction rules</p>
                    </div>
                    <span class="text-xs font-bold text-[#1e3a8a] bg-[#eff6ff] px-2.5 py-1 rounded-full border border-[#bfdbfe]">
                        {{ $earnings->count() + $deductions->count() }} Components
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase text-[10px] tracking-wider">
                            <tr>
                                <th class="py-3 px-4">Code</th>
                                <th class="py-3 px-4">Component Name</th>
                                <th class="py-3 px-4">Category</th>
                                <th class="py-3 px-4">Type</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @foreach($earnings as $earn)
                                <tr>
                                    <td class="py-3 px-4 font-mono font-bold text-slate-900">{{ $earn->code }}</td>
                                    <td class="py-3 px-4 font-semibold text-slate-800">{{ $earn->name }}</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-200">EARNING</span>
                                    </td>
                                    <td class="py-3 px-4 font-mono text-slate-500">{{ $earn->calculation_type }}</td>
                                </tr>
                            @endforeach
                            @foreach($deductions as $ded)
                                <tr>
                                    <td class="py-3 px-4 font-mono font-bold text-amber-800">{{ $ded->code }}</td>
                                    <td class="py-3 px-4 font-semibold text-slate-800">{{ $ded->name }}</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-amber-50 text-amber-800 border border-amber-200">DEDUCTION</span>
                                    </td>
                                    <td class="py-3 px-4 font-mono text-slate-500">{{ $ded->calculation_type }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Corporate Salary Structures -->
            <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <h3 class="text-sm font-extrabold text-slate-900 mb-3 border-b border-slate-100 pb-3">Corporate Salary Structures</h3>
                <div class="space-y-3">
                    @forelse($salaryStructures as $struct)
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-extrabold text-slate-900">{{ $struct->name }}</h4>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#eff6ff] text-[#1e3a8a] font-mono">{{ $struct->currency }}</span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-1">{{ $struct->description }}</p>
                        </div>
                    @empty
                        <p class="text-slate-400 text-xs">No salary structure configured.</p>
                    @endforelse
                </div>
            </div>
        </div>

    @endif

</div>
@endsection

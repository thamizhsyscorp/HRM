@extends('layouts.app')

@section('title', $isSuperAdmin ? 'Company Payroll & Disbursals' : ($isHr ? 'Workforce Payroll' : 'My Salary & Take-Home'))
@section('page_title', $isSuperAdmin ? 'Company Payroll & Workforce Disbursals' : ($isHr ? 'Workforce Payroll Ledger' : 'Salary & Take-Home Overview'))

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    {{-- ========================================================================= --}}
    {{-- 1. EMPLOYEE VIEW (Clean & Simple: Basic Salary, Deductions, Take-Home)    --}}
    {{-- ========================================================================= --}}
    @if(!$isSuperAdmin && !$isHr)

        <!-- Top Row: Real-Time Accrual & Monthly Net Pay (Balanced 7 / 5 Grid) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">
            
            <!-- Left (7 Cols): Real-Time Take-Home Accrued by Today -->
            <div class="lg:col-span-7 bg-gradient-to-br from-slate-900 via-[#1e3a8a] to-slate-900 text-white rounded-2xl p-6 sm:p-7 shadow-md border border-blue-900/40 relative overflow-hidden flex flex-col justify-between">
                <!-- Subtle decorative background radial glow -->
                <div class="absolute -right-8 -bottom-8 w-40 h-40 bg-blue-400/10 rounded-full blur-2xl pointer-events-none"></div>

                <div>
                    <div class="my-2">
                        <span class="text-3xl sm:text-4xl font-black font-mono tracking-tight text-white">
                            ₹{{ number_format($myTakeHomeData['take_home_by_today'] ?? 0, 2) }}
                        </span>
                    </div>

                    <p class="text-xs text-slate-300 leading-relaxed max-w-xl">
                        Your earned take-home salary accrued through today based on <strong>{{ $myTakeHomeData['attended_days'] ?? 0 }} days attended of {{ $workingDaysInMonth }} working days</strong> (paid holidays are included in the amount).
                    </p>
                </div>

                <div class="mt-5 pt-4 border-t border-white/10 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                    <span class="text-indigo-200 text-[11px] font-medium">
                        Daily Earning Rate ({{ $workingDaysInMonth }} working days):
                    </span>
                    <span class="font-mono font-extrabold text-emerald-300 text-sm">
                        ₹{{ number_format($myTakeHomeData['daily_rate'] ?? 0, 2) }} / day
                    </span>
                </div>
            </div>

            <!-- Right (5 Cols): Monthly Take-Home Pay (Net Pay) -->
            <div class="lg:col-span-5 bg-white rounded-2xl p-6 sm:p-7 border border-emerald-200 shadow-md flex flex-col justify-between relative overflow-hidden bg-gradient-to-b from-emerald-50/50 via-white to-white">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-black text-emerald-900 uppercase tracking-wider">Monthly Take-Home Pay</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-600 text-white shadow-2xs">
                            Net Pay
                        </span>
                    </div>

                    <div class="my-2">
                        <span class="text-3xl sm:text-4xl font-black text-emerald-800 font-mono tracking-tight">
                            ₹{{ number_format($myTakeHomeData['monthly_net'] ?? 0, 2) }}
                        </span>
                    </div>

                    <p class="text-xs text-emerald-700 font-medium">
                        Net amount credited to your bank
                    </p>
                </div>

                <div class="mt-5 pt-4 border-t border-emerald-100 flex items-center justify-between text-xs text-slate-500 font-mono">
                    <span>Gross: ₹{{ number_format($myTakeHomeData['monthly_gross'] ?? 0, 2) }}</span>
                    <span class="text-rose-600">- Deductions: ₹{{ number_format($myTakeHomeData['monthly_deductions'] ?? 0, 2) }}</span>
                </div>
            </div>

        </div>

        <!-- Bottom Row: Earnings Components & Deductions Breakdown (Balanced 2-Column Split) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 items-stretch">

            <!-- Earnings Card -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-xl bg-blue-50 text-[#1e3a8a] flex items-center justify-center font-bold text-sm">
                                ₹
                            </span>
                            <div>
                                <h4 class="text-sm font-extrabold text-slate-900">Earnings Components</h4>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-xl text-xs font-mono font-extrabold text-emerald-800 bg-emerald-50 border border-emerald-200">
                            Gross: ₹{{ number_format($myTakeHomeData['monthly_gross'] ?? 0, 2) }}
                        </span>
                    </div>

                    @php
                        $myEarnings = $mySalaryComponents->where('salaryComponent.category', 'EARNING');
                    @endphp

                    @if($myEarnings->count() > 0)
                        <div class="divide-y divide-slate-100 text-xs">
                            @foreach($myEarnings as $earn)
                                <div class="py-3 flex items-center justify-between hover:bg-slate-50/60 px-2 rounded-lg transition">
                                    <div>
                                        <span class="font-bold text-slate-800 block text-xs">{{ $earn->salaryComponent->name }}</span>
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $earn->salaryComponent->code }}</span>
                                    </div>
                                    <span class="font-mono font-bold text-slate-900 text-xs">
                                        + ₹{{ number_format($earn->amount, 2) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-3 flex items-center justify-between text-xs px-2">
                            <span class="font-bold text-slate-800">Basic Salary</span>
                            <span class="font-mono font-bold text-slate-900">+ ₹{{ number_format($myBasic, 2) }}</span>
                        </div>
                    @endif
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs px-2">
                    <span class="text-slate-500 font-semibold">Total Monthly Gross</span>
                    <span class="font-mono font-black text-slate-900 text-sm">
                        ₹{{ number_format($myTakeHomeData['monthly_gross'] ?? 0, 2) }}
                    </span>
                </div>
            </div>

            <!-- Deductions Card -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-sm">
                                %
                            </span>
                            <div>
                                <h4 class="text-sm font-extrabold text-slate-900">Deductions Breakdown</h4>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-xl text-xs font-mono font-extrabold text-rose-700 bg-rose-50 border border-rose-200">
                            Total: ₹{{ number_format($myTakeHomeData['monthly_deductions'] ?? 0, 2) }}
                        </span>
                    </div>

                    @if($myDeductionsList->count() > 0 && ($myTakeHomeData['monthly_deductions'] ?? 0) > 0)
                        <div class="divide-y divide-slate-100 text-xs">
                            @foreach($myDeductionsList as $ded)
                                <div class="py-3 flex items-center justify-between hover:bg-slate-50/60 px-2 rounded-lg transition">
                                    <div>
                                        <span class="font-bold text-slate-800 block text-xs">{{ $ded->salaryComponent->name }}</span>
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $ded->salaryComponent->code }}</span>
                                    </div>
                                    <span class="font-mono font-bold text-rose-600 text-xs">
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

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs px-2">
                    <span class="text-slate-500 font-semibold">Total Monthly Deductions</span>
                    <span class="font-mono font-black text-rose-600 text-sm">
                        - ₹{{ number_format($myTakeHomeData['monthly_deductions'] ?? 0, 2) }}
                    </span>
                </div>
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
    @endif

</div>
@endsection

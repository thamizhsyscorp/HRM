@extends('layouts.app')

@section('title', 'Payroll & Salary Management')
@section('page_title', 'Payroll Structures & Real-Time Compensation')

@section('content')
<div class="space-y-6">

    <!-- REAL-TIME TAKE-HOME BY TODAY HERO CALCULATOR CARD -->
    <div class="bg-gradient-to-r from-[#1e1b4b] via-[#312e81] to-[#1e1b4b] rounded-2xl p-6 sm:p-8 text-white shadow-md relative overflow-hidden">
        <div class="absolute -right-16 -bottom-16 w-60 h-60 bg-[#1e3a8a]/30 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-300 bg-emerald-950/60 px-3 py-0.5 rounded-full border border-emerald-500/30">
                        ⚡ Real-Time Take-Home Accrual
                    </span>
                    <span class="text-xs text-indigo-200 font-mono">
                        {{ now()->format('l, F j, Y') }} (Day {{ $elapsedDays }} of {{ $daysInMonth }})
                    </span>
                </div>

                @if(!$isSuperAdmin && !$isHr && $myTakeHomeData)
                    <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-white font-mono">
                        ₹{{ number_format($myTakeHomeData['take_home_by_today'], 2) }}
                    </h2>
                    <p class="text-xs sm:text-sm text-indigo-100/90 mt-1 max-w-xl">
                        Your verified take-home earnings accrued by today based on {{ $myTakeHomeData['payable_days'] }} payable attended days this month.
                    </p>
                @else
                    <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-white font-mono">
                        ₹{{ number_format($workforceTakeHomeByToday, 2) }}
                    </h2>
                    <p class="text-xs sm:text-sm text-indigo-100/90 mt-1 max-w-xl">
                        Workforce aggregated net take-home salary accrued by today across all active personnel through day {{ $elapsedDays }} of {{ $daysInMonth }}.
                    </p>
                @endif
            </div>

            <!-- Accrual Metric Badges -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/10 shrink-0 text-left">
                <div>
                    <span class="block text-[10px] uppercase font-bold text-indigo-200">Daily Net Rate</span>
                    <span class="text-sm sm:text-base font-extrabold font-mono text-white">
                        ₹{{ number_format($myTakeHomeData ? $myTakeHomeData['daily_rate'] : ($daysInMonth > 0 ? ($netMonthlyPayroll/$daysInMonth) : 0), 2) }}
                    </span>
                    <span class="text-[10px] text-indigo-200/80 block">per day</span>
                </div>

                <div>
                    <span class="block text-[10px] uppercase font-bold text-indigo-200">Payable Days</span>
                    <span class="text-sm sm:text-base font-extrabold font-mono text-white">
                        {{ $myTakeHomeData ? $myTakeHomeData['payable_days'] : $elapsedDays }} / {{ $daysInMonth }}
                    </span>
                    <span class="text-[10px] text-indigo-200/80 block">days to date</span>
                </div>

                <div class="col-span-2 sm:col-span-1 border-t sm:border-t-0 border-white/10 pt-2 sm:pt-0">
                    <span class="block text-[10px] uppercase font-bold text-indigo-200">Month-End Net</span>
                    <span class="text-sm sm:text-base font-extrabold font-mono text-emerald-300">
                        ₹{{ number_format($myTakeHomeData ? $myTakeHomeData['monthly_net'] : $netMonthlyPayroll, 2) }}
                    </span>
                    <span class="text-[10px] text-indigo-200/80 block">full month</span>
                </div>
            </div>
        </div>
    </div>

    <!-- TOP MONTHLY STATUTORY SUMMARY CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-md flex flex-col justify-between">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Gross Monthly Earnings</span>
            <div class="mt-2">
                <span class="text-3xl font-extrabold text-slate-900 font-mono">₹{{ number_format($totalMonthlyEarnings, 2) }}</span>
            </div>
            <p class="mt-2 text-[11px] text-slate-400 font-medium">Aggregated basic & allowances across personnel</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-md flex flex-col justify-between">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Monthly Deductions</span>
            <div class="mt-2">
                <span class="text-3xl font-extrabold text-amber-800 font-mono">₹{{ number_format($totalMonthlyDeductions, 2) }}</span>
            </div>
            <p class="mt-2 text-[11px] text-slate-400 font-medium">Statutory PF, PT & tax retentions</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-md flex flex-col justify-between">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Net Monthly Disbursal</span>
            <div class="mt-2">
                <span class="text-3xl font-extrabold text-[#1e3a8a] font-mono">₹{{ number_format($netMonthlyPayroll, 2) }}</span>
            </div>
            <p class="mt-2 text-[11px] text-[#1e3a8a] font-semibold">Total bank credit budget for month-end</p>
        </div>
    </div>

    <!-- STAFF ACCRUED TAKE-HOME BY TODAY LEDGER -->
    @if($isSuperAdmin || $isHr)
        <div class="bg-white rounded-2xl shadow-md border border-slate-100 overflow-hidden mb-6">
            <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <span>Staff Real-Time Take-Home by Today Ledger</span>
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Daily accrued pro-rata take-home earnings through {{ now()->format('M d, Y') }} (Day {{ $elapsedDays }} of {{ $daysInMonth }})
                    </p>
                </div>
                <div>
                    <span class="px-3.5 py-1.5 rounded-xl text-xs font-mono font-bold bg-[#eff6ff] text-[#1e3a8a] border border-[#bfdbfe]">
                        Formula: (Net ÷ {{ $daysInMonth }}) × Attended Days
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50/90 border-b border-slate-200/80 text-slate-500 font-bold uppercase text-[11px] tracking-wider">
                        <tr>
                            <th class="py-3.5 px-6 text-xs font-bold tracking-wider">Staff Member</th>
                            <th class="py-3.5 px-6 text-xs font-bold tracking-wider">Department</th>
                            <th class="py-3.5 px-6 text-xs font-bold tracking-wider">Monthly Net</th>
                            <th class="py-3.5 px-6 text-xs font-bold tracking-wider">Daily Rate</th>
                            <th class="py-3.5 px-6 text-xs font-bold tracking-wider">Payable Days</th>
                            <th class="py-3.5 px-6 text-xs font-bold tracking-wider">Take-Home by Today</th>
                            <th class="py-3.5 px-6 text-xs font-bold tracking-wider">Bank Account</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 text-xs">
                        @foreach($employeeTakeHomes as $eth)
                            <tr class="hover:bg-slate-50/70 transition-colors cursor-pointer">
                                <td class="py-4 px-6 font-medium text-slate-900">
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
                                <td class="py-4 px-6 text-slate-700">{{ $eth['employee']->department->name ?? '—' }}</td>
                                <td class="py-4 px-6 font-mono font-medium text-slate-700">₹{{ number_format($eth['monthly_net'], 2) }}</td>
                                <td class="py-4 px-6 font-mono text-slate-500">₹{{ number_format($eth['daily_rate'], 2) }}/d</td>
                                <td class="py-4 px-6 font-mono font-medium text-slate-800">{{ $eth['payable_days'] }} of {{ $daysInMonth }}d</td>
                                <td class="py-4 px-6 font-mono font-extrabold text-sm text-[#1e3a8a]">
                                    ₹{{ number_format($eth['take_home_by_today'], 2) }}
                                </td>
                                <td class="py-4 px-6">
                                    @if($eth['bank_account'])
                                        <span class="text-slate-800 font-medium block text-xs">{{ $eth['bank_account']->bank_name }}</span>
                                        <span class="text-slate-400 font-mono text-[11px]">A/C: {{ substr($eth['bank_account']->account_number, -4) ? '••••' . substr($eth['bank_account']->account_number, -4) : 'Configured' }}</span>
                                    @else
                                        <span class="text-amber-700 text-xs">Pending bank entry</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- FOR EMPLOYEES: PERSONAL ACCRUED TAKE-HOME BREAKDOWN -->
    @if(!$isSuperAdmin && !$isHr && $myTakeHomeData)
        <div class="bg-white rounded-2xl border border-slate-100 shadow-md p-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Your Take-Home Pay Earned By Today</h3>
                    <p class="text-xs text-slate-400">Detailed component breakdown accrued through {{ now()->format('M d, Y') }}</p>
                </div>
                <span class="text-xs font-bold text-[#1e3a8a] bg-[#eff6ff] px-3 py-1 rounded-full border border-[#bfdbfe]">
                    Pro-Rata Calculated
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Daily Earning Rate</span>
                    <span class="text-xl font-extrabold text-slate-900 font-mono mt-1 block">₹{{ number_format($myTakeHomeData['daily_rate'], 2) }}</span>
                    <span class="text-[11px] text-slate-500">per calendar day</span>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Accrued Gross by Today</span>
                    <span class="text-xl font-extrabold text-slate-900 font-mono mt-1 block">₹{{ number_format($myTakeHomeData['gross_by_today'], 2) }}</span>
                    <span class="text-[11px] text-slate-500">earned to date</span>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Accrued Deductions</span>
                    <span class="text-xl font-extrabold text-amber-800 font-mono mt-1 block">₹{{ number_format($myTakeHomeData['deductions_by_today'], 2) }}</span>
                    <span class="text-[11px] text-slate-500">PF & PT retentions</span>
                </div>

                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200">
                    <span class="text-[10px] uppercase font-bold text-emerald-800 block">Take-Home By Today</span>
                    <span class="text-2xl font-extrabold text-emerald-900 font-mono mt-1 block">₹{{ number_format($myTakeHomeData['take_home_by_today'], 2) }}</span>
                    <span class="text-[11px] text-emerald-700 font-semibold">Ready for payout</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50/90 border-b border-slate-200/80 text-slate-500 font-bold uppercase text-[11px] tracking-wider">
                        <tr>
                            <th class="py-3.5 px-6 text-xs font-bold tracking-wider">Component</th>
                            <th class="py-3.5 px-6 text-xs font-bold tracking-wider">Category</th>
                            <th class="py-3.5 px-6 text-xs font-bold tracking-wider">Monthly Rate</th>
                            <th class="py-3.5 px-6 text-xs font-bold tracking-wider text-right">Earned by Today</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 text-xs">
                        @foreach($mySalaryComponents as $myComp)
                            @php
                                $compEarned = $daysInMonth > 0 ? round(($myComp->amount / $daysInMonth) * $myTakeHomeData['payable_days'], 2) : 0;
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors cursor-pointer">
                                <td class="py-4 px-6 font-bold text-slate-900">
                                    {{ $myComp->salaryComponent->name }}
                                    <span class="text-[10px] text-slate-400 font-mono block">{{ $myComp->salaryComponent->code }}</span>
                                </td>
                                <td class="py-4 px-6">
                                    @if($myComp->salaryComponent->category === 'EARNING')
                                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-extrabold bg-emerald-100 text-emerald-900 border border-emerald-300">EARNING</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-extrabold bg-amber-50 text-amber-800 border border-amber-200">DEDUCTION</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 font-mono text-slate-700 font-medium">₹{{ number_format($myComp->amount, 2) }}</td>
                                <td class="py-4 px-6 text-right font-mono font-extrabold text-[#1e3a8a]">₹{{ number_format($compEarned, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Salary Structures (Admin & HR) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Active Salary Components Breakdown (7 Cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl shadow-md border border-slate-100 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Configured Salary Components</h3>
                    <p class="text-xs text-slate-400">Statutory and company compensation elements</p>
                </div>
                <span class="text-xs font-bold text-[#1e3a8a] bg-[#eff6ff] px-3 py-1 rounded-full border border-[#bfdbfe]">
                    {{ $earnings->count() + $deductions->count() }} Components
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50/90 border-b border-slate-200/80 text-slate-500 font-bold uppercase text-[11px] tracking-wider">
                        <tr>
                            <th class="py-3.5 px-5 text-xs font-bold tracking-wider">Code</th>
                            <th class="py-3.5 px-5 text-xs font-bold tracking-wider">Component Name</th>
                            <th class="py-3.5 px-5 text-xs font-bold tracking-wider">Category</th>
                            <th class="py-3.5 px-5 text-xs font-bold tracking-wider">Calculation Type</th>
                            <th class="py-3.5 px-5 text-xs font-bold tracking-wider">Statutory</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 text-xs">
                        @foreach($earnings as $earn)
                            <tr class="hover:bg-slate-50/70 transition-colors cursor-pointer">
                                <td class="py-3.5 px-5 font-mono font-bold text-slate-900">{{ $earn->code }}</td>
                                <td class="py-3.5 px-5 font-semibold text-slate-900">{{ $earn->name }}</td>
                                <td class="py-3.5 px-5">
                                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-extrabold bg-emerald-100 text-emerald-900 border border-emerald-300">EARNING</span>
                                </td>
                                <td class="py-3.5 px-5 font-mono text-slate-600">{{ $earn->calculation_type }}</td>
                                <td class="py-3.5 px-5 text-slate-400 font-mono">{{ $earn->is_statutory ? 'YES' : 'NO' }}</td>
                            </tr>
                        @endforeach
                        @foreach($deductions as $ded)
                            <tr class="hover:bg-[#eff6ff] transition-colors cursor-pointer">
                                <td class="py-3.5 px-5 font-mono font-bold text-amber-800">{{ $ded->code }}</td>
                                <td class="py-3.5 px-5 font-semibold text-slate-900">{{ $ded->name }}</td>
                                <td class="py-3.5 px-5">
                                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-extrabold bg-amber-50 text-amber-800 border border-amber-200">DEDUCTION</span>
                                </td>
                                <td class="py-3.5 px-5 font-mono text-slate-600">{{ $ded->calculation_type }}</td>
                                <td class="py-3.5 px-5 text-slate-800 font-mono font-bold">{{ $ded->is_statutory ? 'YES' : 'NO' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Corporate Structure Overview (5 Cols) -->
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-md p-6">
                <h3 class="text-sm font-extrabold text-slate-900 mb-4 border-b border-slate-100 pb-3">Corporate Salary Structures</h3>
                <div class="space-y-4">
                    @forelse($salaryStructures as $struct)
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-extrabold text-slate-900">{{ $struct->name }}</h4>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#eff6ff] text-[#1e3a8a] font-mono">{{ $struct->currency }}</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">{{ $struct->description }}</p>
                            <div class="mt-3 pt-3 border-t border-slate-200 flex justify-between text-xs text-slate-600">
                                <span>Active Assignments:</span>
                                <span class="font-bold text-[#1e3a8a] font-mono">{{ $struct->employee_components_count }} elements</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-400 text-xs">No salary structure configured.</p>
                    @endforelse
                </div>
            </div>

            <!-- Automated Take-Home Engine Card -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-md p-6">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-base font-extrabold text-slate-900">Take-Home Pro-Rata Engine</span>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed mb-4">
                    Employees can take home their accrued earnings as of any given day in the month. Daily earnings are prorated across 30 calendar days factoring verified check-in attendance and statutory PF/PT retention.
                </p>
                <div class="p-3.5 rounded-xl bg-[#eff6ff] border border-[#bfdbfe] text-xs text-[#1e3a8a] font-mono font-medium">
                    Take-Home = (Monthly Net ÷ Days in Month) × Attended Days
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

@extends('layouts.app')

@section('title', 'Workforce Directory')
@section('page_title', 'Employee Workforce Directory')

@section('content')
<div class="space-y-6">

    <!-- Filter and Search Header -->
    <div class="bg-white rounded-2xl border border-slate-100 p-5 sm:p-6 shadow-md">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 pb-3 border-b border-slate-100">
            <div>
                <h2 class="text-base font-extrabold text-slate-900">Workforce Directory</h2>
                <p class="text-xs text-slate-400">Search and manage corporate personnel records</p>
            </div>
            @if($isSuperAdmin || $isHr)
                <a href="{{ route('employees.create') }}" class="px-4 py-2 rounded-xl bg-[#1e3a8a] hover:bg-[#1e40af] text-white text-xs font-bold shadow-xs transition flex items-center gap-1.5 shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Onboard Staff</span>
                </a>
            @endif
        </div>

        @if(!empty($managedDepartment))
            <div class="mb-4 p-3.5 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#1e3a8a]"></span>
                    <span class="text-xs font-bold text-[#1e3a8a]">Departmental Oversight: Viewing members of {{ $managedDepartment->name }}</span>
                </div>
                <span class="text-[11px] font-semibold text-slate-500">Manager Scope</span>
            </div>
        @endif

        <form method="GET" action="{{ route('employees.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <!-- Search Keyword -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Search Staff</label>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Search name, code, email..." 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white placeholder-slate-400 font-medium text-slate-900"
                >
            </div>

            <!-- Department Filter -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Department</label>
                <select name="department_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white font-medium text-slate-900">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }} ({{ $dept->employees_count }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Employment Status</label>
                <select name="status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white font-medium text-slate-900">
                    <option value="">All Statuses</option>
                    <option value="ACTIVE" {{ request('status') === 'ACTIVE' ? 'selected' : '' }}>Active</option>
                    <option value="PROBATION" {{ request('status') === 'PROBATION' ? 'selected' : '' }}>Probation</option>
                    <option value="RESIGNED" {{ request('status') === 'RESIGNED' ? 'selected' : '' }}>Resigned</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2.5 px-4 bg-[#1e3a8a] hover:bg-[#1e40af] text-white rounded-xl text-xs font-bold shadow-xs transition">
                    Filter Directory
                </button>
                <a href="{{ route('employees.index') }}" class="py-2.5 px-3 border border-slate-300 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-bold transition text-center bg-white">
                    Reset
                </a>
            </div>
        </form>
    </div>
    <!-- Employee Directory Table -->
    <div class="bg-white rounded-2xl shadow-md border border-slate-100 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-white">
            <div>
                <h3 class="text-sm font-extrabold text-slate-900">Workforce Directory</h3>
                <p class="text-xs text-slate-400">Employee personnel profiles, organization placements, and status</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1040px] text-left border-collapse">
                <thead class="bg-slate-50/90 border-b border-slate-200/80 text-slate-500 font-bold uppercase text-[11px] tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4 text-xs font-bold tracking-wider whitespace-nowrap">Full Name</th>
                        <th class="py-3.5 px-4 text-xs font-bold tracking-wider whitespace-nowrap">Emp Code</th>
                        <th class="py-3.5 px-4 text-xs font-bold tracking-wider whitespace-nowrap">Department</th>
                        <th class="py-3.5 px-4 text-xs font-bold tracking-wider whitespace-nowrap">Designation</th>
                        <th class="py-3.5 px-4 text-xs font-bold tracking-wider whitespace-nowrap">Reporting Manager</th>
                        <th class="py-3.5 px-4 text-xs font-bold tracking-wider whitespace-nowrap">Salary</th>
                        <th class="py-3.5 px-4 text-xs font-bold tracking-wider whitespace-nowrap">Status</th>
                        <th class="py-3.5 px-4 text-xs font-bold tracking-wider whitespace-nowrap text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 text-xs">
                    @forelse($employees as $emp)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-4 font-medium text-slate-900">
                                <div class="flex items-center gap-3 min-w-[200px]">
                                    <div class="w-8 h-8 rounded-full bg-[#eff6ff] text-[#1e3a8a] font-bold flex items-center justify-center text-xs shrink-0">
                                        {{ strtoupper(substr($emp->first_name, 0, 1) . substr($emp->last_name ?? '', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <span class="font-extrabold text-slate-900 block truncate">{{ $emp->full_name }}</span>
                                        <span class="text-[11px] text-slate-400 font-mono block truncate">{{ $emp->work_email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex whitespace-nowrap font-mono font-bold text-[#1e3a8a] bg-blue-50 border border-blue-100 rounded-md px-2 py-0.5">{{ $emp->employee_code }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-800 font-medium whitespace-nowrap">{{ $emp->department->name ?? '—' }}</td>
                            <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap">{{ $emp->designation->name ?? '—' }}</td>
                            <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap">{{ $emp->manager->full_name ?? 'Top Management' }}</td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-md bg-[#eff6ff] text-[#1e3a8a] font-mono text-[10px] font-bold border border-[#bfdbfe]">
                                    {{ $emp->salaryComponents->count() }} active
                                </span>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @php
                                    $statusTone = match ($emp->employment_status) {
                                        'ACTIVE' => 'bg-emerald-100 text-emerald-900 border-emerald-300',
                                        'PROBATION' => 'bg-amber-100 text-amber-900 border-amber-300',
                                        default => 'bg-slate-100 text-slate-700 border-slate-300',
                                    };
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border {{ $statusTone }}">
                                    {{ $emp->employment_status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('employees.show', $emp) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-white hover:bg-slate-50 text-slate-700 font-bold border border-slate-200 transition text-xs shadow-2xs">
                                        View
                                    </a>
                                    @if($isSuperAdmin || $isHr)
                                        <a href="{{ route('employees.edit', $emp) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-[#1e3a8a] font-bold border border-blue-200 transition text-xs shadow-2xs">
                                            Edit ✎
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400 text-xs">
                                No employees found matching the filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($employees->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $employees->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

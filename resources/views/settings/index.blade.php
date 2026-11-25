@extends('layouts.app')

@section('title', 'Settings')
@section('page_title', 'Settings')

@section('content')
@php
    $nav = [
        'departments' => ['Departments', 'Teams people are placed in'],
        'designations' => ['Designations', 'Job titles used on profiles'],
        'locations' => ['Work locations', 'Offices and geofence sites'],
        'leave-types' => ['Leave types', 'Quotas used on leave requests'],
    ];
    if ($isSuperAdmin) {
        $nav['roles'] = ['Roles & permissions', 'What each role is allowed to do'];
    }
@endphp

<div class="grid grid-cols-1 lg:grid-cols-[240px_minmax(0,1fr)] gap-6 items-start">
    <!-- Sidebar Navigation -->
    <aside class="bg-white rounded-2xl border border-slate-100 shadow-sm p-3 lg:sticky lg:top-20">
        <p class="px-3 pt-2 pb-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Organization</p>
        <div class="space-y-1">
            @foreach($nav as $key => [$label, $hint])
                <a href="{{ route('settings.index', ['section' => $key]) }}" class="block px-3 py-2.5 rounded-xl transition {{ $section === $key ? 'bg-blue-50 text-[#1e3a8a] border border-[#1e3a8a]/20 font-bold' : 'text-slate-600 hover:bg-slate-50 border border-transparent font-medium' }}">
                    <span class="block text-[13px] font-extrabold">{{ $label }}</span>
                    <span class="block text-[11px] text-slate-400 font-medium mt-0.5">{{ $hint }}</span>
                </a>
            @endforeach
        </div>
    </aside>

    <div class="space-y-6 min-w-0">

        {{-- ==================== DEPARTMENTS SECTION ==================== --}}
        @if($section === 'departments')
            <!-- Add Department Card -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900">Add New Department</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Define a functional department before assigning personnel to it.</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('settings.departments.store') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                    @csrf
                    <div class="md:col-span-4">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Department Name</label>
                        <input name="name" value="{{ old('name') }}" placeholder="e.g. Engineering" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-900 focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a]">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Code</label>
                        <input name="code" value="{{ old('code') }}" placeholder="ENG" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-900 uppercase focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a]">
                    </div>
                    <div class="md:col-span-4">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Description</label>
                        <input name="description" value="{{ old('description') }}" placeholder="Core product engineering..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-900 focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a]">
                    </div>
                    <div class="md:col-span-2 flex items-center justify-between gap-3">
                        @include('settings.partials.toggle', ['name' => 'is_active', 'on' => true, 'label' => 'Active'])
                        <button class="px-4 py-2.5 rounded-xl bg-[#1e3a8a] hover:bg-[#1e40af] text-white text-xs font-bold transition shadow-sm">Add</button>
                    </div>
                </form>
            </div>

            <!-- Unified Departments Table -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Configured Departments</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Overview of active workforce departments and team distributions.</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-[#1e3a8a] border border-blue-200">
                        {{ $departments->count() }} Departments
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px] text-left border-collapse">
                        <thead class="bg-slate-50/90 border-b border-slate-200/80 text-slate-500 font-bold uppercase text-[11px] tracking-wider">
                            <tr>
                                <th class="py-3.5 px-6">Department</th>
                                <th class="py-3.5 px-4 text-center">Code</th>
                                <th class="py-3.5 px-4 text-center">Team Members</th>
                                <th class="py-3.5 px-4 text-center">Designations</th>
                                <th class="py-3.5 px-4 text-center">Status</th>
                                <th class="py-3.5 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                            @forelse($departments as $department)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-blue-50 text-[#1e3a8a] font-extrabold flex items-center justify-center text-xs shrink-0 border border-blue-200">
                                                {{ strtoupper(substr($department->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <span class="font-extrabold text-slate-900 block text-sm leading-tight">{{ $department->name }}</span>
                                                @if($department->description)
                                                    <span class="text-slate-400 text-[11px] block mt-0.5">{{ Str::limit($department->description, 50) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="px-2.5 py-0.5 font-mono font-bold rounded-md bg-blue-50 text-[#1e3a8a] border border-blue-200">
                                            {{ $department->code ?: '—' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="font-bold text-slate-900 text-sm">{{ $department->employees_count }}</span>
                                        <span class="text-slate-400 text-[11px]">people</span>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="font-bold text-slate-900 text-sm">{{ $department->designations_count }}</span>
                                        <span class="text-slate-400 text-[11px]">roles</span>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        @if($department->is_active)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button" onclick="openEditDepartmentModal({{ json_encode($department) }})" class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold transition text-xs shadow-xs">
                                                Edit
                                            </button>
                                            @if($department->employees_count === 0 && $department->designations_count === 0)
                                                <form method="POST" action="{{ route('settings.departments.destroy', $department) }}" class="inline-block" onsubmit="return confirm('Delete this empty department?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-3 py-1.5 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold transition text-xs shadow-xs">
                                                        Delete
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-400">No departments configured yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Edit Department Modal -->
            <div id="editDepartmentModal" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
                <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-6 border border-slate-100">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                        <h3 class="text-base font-extrabold text-slate-900">Edit Department</h3>
                        <button type="button" onclick="closeEditDepartmentModal()" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                    </div>
                    <form id="editDepartmentForm" method="POST" action="">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Department Name</label>
                                <input id="editDeptName" name="name" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Code</label>
                                <input id="editDeptCode" name="code" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-mono uppercase">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Description</label>
                                <textarea id="editDeptDescription" name="description" rows="2" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm"></textarea>
                            </div>
                            <div class="pt-2">
                                <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                    <span class="relative inline-flex h-5 w-9 shrink-0">
                                        <input type="hidden" name="is_active" value="0">
                                        <input id="editDeptActive" type="checkbox" name="is_active" value="1" class="peer sr-only">
                                        <span class="block h-5 w-9 rounded-full bg-slate-200 transition peer-checked:bg-[#1e3a8a]"></span>
                                        <span class="pointer-events-none absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white shadow transition peer-checked:translate-x-4"></span>
                                    </span>
                                    <span class="text-xs font-bold text-slate-700">Active Department</span>
                                </label>
                            </div>
                        </div>
                        <div class="mt-6 flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                            <button type="button" onclick="closeEditDepartmentModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50">Cancel</button>
                            <button type="submit" class="px-5 py-2 rounded-xl bg-[#1e3a8a] hover:bg-[#1e40af] text-white text-xs font-bold">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif


        {{-- ==================== DESIGNATIONS SECTION ==================== --}}
        @if($section === 'designations')
            <!-- Add Designation Card -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900">Add New Designation</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Job titles assigned to employees across departments.</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('settings.designations.store') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                    @csrf
                    <div class="md:col-span-3">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Job Title</label>
                        <input name="name" value="{{ old('name') }}" placeholder="e.g. Senior Software Engineer" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-medium">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Department</label>
                        <select name="department_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-medium bg-white">
                            <option value="">Any department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Code</label>
                        <input name="code" value="{{ old('code') }}" placeholder="SSE" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-medium uppercase">
                    </div>
                    <div class="md:col-span-4 flex items-center justify-between gap-3">
                        @include('settings.partials.toggle', ['name' => 'is_active', 'on' => true, 'label' => 'Active'])
                        <button class="px-4 py-2.5 rounded-xl bg-[#1e3a8a] hover:bg-[#1e40af] text-white text-xs font-bold transition shadow-sm">Add</button>
                    </div>
                </form>
            </div>

            <!-- Unified Designations Table -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Configured Designations</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Directory of all corporate designations and role assignments.</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-[#1e3a8a] border border-blue-200">
                        {{ $designations->count() }} Designations
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px] text-left border-collapse">
                        <thead class="bg-slate-50/90 border-b border-slate-200/80 text-slate-500 font-bold uppercase text-[11px] tracking-wider">
                            <tr>
                                <th class="py-3.5 px-6">Title</th>
                                <th class="py-3.5 px-4">Department</th>
                                <th class="py-3.5 px-4 text-center">Code</th>
                                <th class="py-3.5 px-4 text-center">Staff Count</th>
                                <th class="py-3.5 px-4 text-center">Status</th>
                                <th class="py-3.5 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                            @forelse($designations as $designation)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="py-4 px-6 font-bold text-slate-900 text-sm">
                                        {{ $designation->name }}
                                    </td>
                                    <td class="py-4 px-4">
                                        @if($designation->department)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-[#1e3a8a] border border-blue-200">
                                                {{ $designation->department->name }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 text-xs">All Departments</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="px-2.5 py-0.5 font-mono font-bold rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ $designation->code ?: '—' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="font-bold text-slate-900 text-sm">{{ $designation->employees_count }}</span>
                                        <span class="text-slate-400 text-[11px]">assigned</span>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        @if($designation->is_active)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button" onclick="openEditDesignationModal({{ json_encode($designation) }})" class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold transition text-xs shadow-xs">
                                                Edit
                                            </button>
                                            @if($designation->employees_count === 0)
                                                <form method="POST" action="{{ route('settings.designations.destroy', $designation) }}" class="inline-block" onsubmit="return confirm('Delete this designation?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-3 py-1.5 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold transition text-xs shadow-xs">
                                                        Delete
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-400">No designations configured yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Edit Designation Modal -->
            <div id="editDesignationModal" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
                <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-6 border border-slate-100">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                        <h3 class="text-base font-extrabold text-slate-900">Edit Designation</h3>
                        <button type="button" onclick="closeEditDesignationModal()" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                    </div>
                    <form id="editDesignationForm" method="POST" action="">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Job Title</label>
                                <input id="editDesigName" name="name" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Department</label>
                                <select id="editDesigDepartmentId" name="department_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm bg-white">
                                    <option value="">Any department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Code</label>
                                <input id="editDesigCode" name="code" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-mono uppercase">
                            </div>
                            <div class="pt-2">
                                <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                    <span class="relative inline-flex h-5 w-9 shrink-0">
                                        <input type="hidden" name="is_active" value="0">
                                        <input id="editDesigActive" type="checkbox" name="is_active" value="1" class="peer sr-only">
                                        <span class="block h-5 w-9 rounded-full bg-slate-200 transition peer-checked:bg-[#1e3a8a]"></span>
                                        <span class="pointer-events-none absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white shadow transition peer-checked:translate-x-4"></span>
                                    </span>
                                    <span class="text-xs font-bold text-slate-700">Active Designation</span>
                                </label>
                            </div>
                        </div>
                        <div class="mt-6 flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                            <button type="button" onclick="closeEditDesignationModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50">Cancel</button>
                            <button type="submit" class="px-5 py-2 rounded-xl bg-[#1e3a8a] hover:bg-[#1e40af] text-white text-xs font-bold">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif


        {{-- ==================== WORK LOCATIONS SECTION ==================== --}}
        @if($section === 'locations')
            <!-- Add Work Location Card -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900">Add New Work Location</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Offices and sites used for employee placement and GPS geofence tracking.</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('settings.locations.store') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                    @csrf
                    <div class="md:col-span-3">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Location Name</label>
                        <input name="name" value="{{ old('name') }}" placeholder="Headquarters" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-medium">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Code</label>
                        <input name="code" value="{{ old('code') }}" placeholder="HQ-01" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-medium uppercase">
                    </div>
                    <div class="md:col-span-4">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Address</label>
                        <input name="address" value="{{ old('address') }}" placeholder="100 Innovation Way, Suite 400" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-medium">
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Radius (m)</label>
                        <input type="number" name="allowed_radius_meters" value="{{ old('allowed_radius_meters', 100) }}" min="10" max="5000" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm font-medium">
                    </div>
                    <div class="md:col-span-2 flex items-center justify-end">
                        <button class="w-full px-4 py-2.5 rounded-xl bg-[#1e3a8a] hover:bg-[#1e40af] text-white text-xs font-bold transition shadow-sm">Add</button>
                    </div>
                    <div class="md:col-span-12 flex flex-wrap gap-6 pt-1">
                        @include('settings.partials.toggle', ['name' => 'geofence_enabled', 'on' => true, 'label' => 'Geofence Active'])
                        @include('settings.partials.toggle', ['name' => 'is_active', 'on' => true, 'label' => 'Location Active'])
                    </div>
                </form>
            </div>

            <!-- Unified Work Locations Table -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Configured Work Locations</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Physical office premises and GPS geofence parameters.</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-[#1e3a8a] border border-blue-200">
                        {{ $locations->count() }} Locations
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px] text-left border-collapse">
                        <thead class="bg-slate-50/90 border-b border-slate-200/80 text-slate-500 font-bold uppercase text-[11px] tracking-wider">
                            <tr>
                                <th class="py-3.5 px-6">Location</th>
                                <th class="py-3.5 px-4 text-center">Code</th>
                                <th class="py-3.5 px-4">Address</th>
                                <th class="py-3.5 px-4 text-center">Radius</th>
                                <th class="py-3.5 px-4 text-center">Geofence</th>
                                <th class="py-3.5 px-4 text-center">Staff Count</th>
                                <th class="py-3.5 px-4 text-center">Status</th>
                                <th class="py-3.5 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                            @forelse($locations as $location)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="py-4 px-6 font-bold text-slate-900 text-sm">
                                        {{ $location->name }}
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="px-2.5 py-0.5 font-mono font-bold rounded-md bg-blue-50 text-[#1e3a8a] border border-blue-200">
                                            {{ $location->code ?: '—' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-slate-600 max-w-xs truncate">
                                        {{ $location->address ?: '—' }}
                                    </td>
                                    <td class="py-4 px-4 text-center font-mono font-bold text-slate-800">
                                        {{ $location->allowed_radius_meters }}m
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        @if($location->geofence_enabled)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Enforced
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Disabled
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="font-bold text-slate-900 text-sm">{{ $location->employees_count }}</span>
                                        <span class="text-slate-400 text-[11px]">people</span>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        @if($location->is_active)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button" onclick="openEditLocationModal({{ json_encode($location) }})" class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold transition text-xs shadow-xs">
                                                Edit
                                            </button>
                                            @if($location->employees_count === 0)
                                                <form method="POST" action="{{ route('settings.locations.destroy', $location) }}" class="inline-block" onsubmit="return confirm('Delete this work location?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-3 py-1.5 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold transition text-xs shadow-xs">
                                                        Delete
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-12 text-center text-slate-400">No work locations configured yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Edit Location Modal -->
            <div id="editLocationModal" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
                <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-6 border border-slate-100">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                        <h3 class="text-base font-extrabold text-slate-900">Edit Work Location</h3>
                        <button type="button" onclick="closeEditLocationModal()" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                    </div>
                    <form id="editLocationForm" method="POST" action="">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Location Name</label>
                                <input id="editLocName" name="name" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Code</label>
                                    <input id="editLocCode" name="code" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-mono uppercase">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Radius (Meters)</label>
                                    <input id="editLocRadius" type="number" name="allowed_radius_meters" min="10" max="5000" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-medium">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Address</label>
                                <textarea id="editLocAddress" name="address" rows="2" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm"></textarea>
                            </div>
                            <div class="pt-2 flex flex-wrap gap-5">
                                <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                    <span class="relative inline-flex h-5 w-9 shrink-0">
                                        <input type="hidden" name="geofence_enabled" value="0">
                                        <input id="editLocGeofence" type="checkbox" name="geofence_enabled" value="1" class="peer sr-only">
                                        <span class="block h-5 w-9 rounded-full bg-slate-200 transition peer-checked:bg-[#1e3a8a]"></span>
                                        <span class="pointer-events-none absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white shadow transition peer-checked:translate-x-4"></span>
                                    </span>
                                    <span class="text-xs font-bold text-slate-700">Enforce Geofence</span>
                                </label>
                                <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                    <span class="relative inline-flex h-5 w-9 shrink-0">
                                        <input type="hidden" name="is_active" value="0">
                                        <input id="editLocActive" type="checkbox" name="is_active" value="1" class="peer sr-only">
                                        <span class="block h-5 w-9 rounded-full bg-slate-200 transition peer-checked:bg-[#1e3a8a]"></span>
                                        <span class="pointer-events-none absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white shadow transition peer-checked:translate-x-4"></span>
                                    </span>
                                    <span class="text-xs font-bold text-slate-700">Active Location</span>
                                </label>
                            </div>
                        </div>
                        <div class="mt-6 flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                            <button type="button" onclick="closeEditLocationModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50">Cancel</button>
                            <button type="submit" class="px-5 py-2 rounded-xl bg-[#1e3a8a] hover:bg-[#1e40af] text-white text-xs font-bold">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif


        {{-- ==================== LEAVE TYPES SECTION ==================== --}}
        @if($section === 'leave-types')
            <!-- Add Leave Type Card -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900">Add New Leave Type</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Annual quota is the number of days an employee can request per calendar year.</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('settings.leave-types.store') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                    @csrf
                    <div class="md:col-span-4">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Leave Name</label>
                        <input name="name" value="{{ old('name') }}" placeholder="e.g. Casual Leave" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-medium focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a]">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Code</label>
                        <input name="code" value="{{ old('code') }}" placeholder="CL" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-medium uppercase focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a]">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Days / Year</label>
                        <input type="number" step="0.5" min="0" max="365" name="annual_quota" value="{{ old('annual_quota', 12) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-medium focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a]">
                    </div>
                    <div class="md:col-span-4 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            @include('settings.partials.toggle', ['name' => 'is_paid', 'on' => true, 'label' => 'Paid'])
                            @include('settings.partials.toggle', ['name' => 'requires_approval', 'on' => true, 'label' => 'Approval'])
                            @include('settings.partials.toggle', ['name' => 'is_active', 'on' => true, 'label' => 'Active'])
                        </div>
                        <button class="px-4 py-2.5 rounded-xl bg-[#1e3a8a] hover:bg-[#1e40af] text-white text-xs font-bold transition shadow-sm">Add</button>
                    </div>
                </form>
            </div>

            <!-- Unified Modern Leave Types Table -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Configured Leave Policies</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Annual allowance, approval workflows, and payment rules for time-off.</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-[#1e3a8a] border border-blue-200">
                        {{ $leaveTypes->count() }} Leave Types
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[860px] text-left border-collapse">
                        <thead class="bg-slate-50/90 border-b border-slate-200/80 text-slate-500 font-bold uppercase text-[11px] tracking-wider">
                            <tr>
                                <th class="py-3.5 px-6">Leave Category</th>
                                <th class="py-3.5 px-4 text-center">Code</th>
                                <th class="py-3.5 px-4 text-center">Annual Quota</th>
                                <th class="py-3.5 px-4 text-center">Pay Policy</th>
                                <th class="py-3.5 px-4 text-center">Approval</th>
                                <th class="py-3.5 px-4 text-center">Usage</th>
                                <th class="py-3.5 px-4 text-center">Status</th>
                                <th class="py-3.5 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                            @forelse($leaveTypes as $leaveType)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="py-4 px-6 font-bold text-slate-900 text-sm">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-blue-50 text-[#1e3a8a] font-extrabold flex items-center justify-center text-xs shrink-0 border border-blue-200">
                                                {{ strtoupper(substr($leaveType->name, 0, 1)) }}
                                            </div>
                                            <span>{{ $leaveType->name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="px-2.5 py-0.5 font-mono font-bold rounded-md bg-blue-50 text-[#1e3a8a] border border-blue-200">
                                            {{ $leaveType->code }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="font-extrabold text-slate-900 text-sm">{{ number_format($leaveType->annual_quota, 1) }}</span>
                                        <span class="text-slate-400 text-[11px] font-medium">d / yr</span>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        @if($leaveType->is_paid)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Paid
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Unpaid
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        @if($leaveType->requires_approval)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-[#1e3a8a] border border-blue-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#1e3a8a]"></span> Required
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Automatic
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-center font-medium text-slate-500">
                                        <span class="font-bold text-slate-700">{{ $leaveType->requests_count }}</span> requests
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        @if($leaveType->is_active)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button" onclick="openEditLeaveTypeModal({{ json_encode($leaveType) }})" class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold transition text-xs shadow-xs">
                                                Edit
                                            </button>
                                            @if($leaveType->requests_count === 0)
                                                <form method="POST" action="{{ route('settings.leave-types.destroy', $leaveType) }}" class="inline-block" onsubmit="return confirm('Delete this leave type?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-3 py-1.5 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold transition text-xs shadow-xs">
                                                        Delete
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-12 text-center text-slate-400">No leave types configured yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Edit Leave Type Modal -->
            <div id="editLeaveTypeModal" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
                <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-6 border border-slate-100">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                        <h3 class="text-base font-extrabold text-slate-900">Edit Leave Policy</h3>
                        <button type="button" onclick="closeEditLeaveTypeModal()" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                    </div>
                    <form id="editLeaveTypeForm" method="POST" action="">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Leave Name</label>
                                <input id="editLtName" name="name" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Code</label>
                                    <input id="editLtCode" name="code" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-mono uppercase">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Days / Year</label>
                                    <input id="editLtQuota" type="number" step="0.5" min="0" max="365" name="annual_quota" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold">
                                </div>
                            </div>
                            <div class="pt-2 flex flex-wrap gap-5">
                                <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                    <span class="relative inline-flex h-5 w-9 shrink-0">
                                        <input type="hidden" name="is_paid" value="0">
                                        <input id="editLtPaid" type="checkbox" name="is_paid" value="1" class="peer sr-only">
                                        <span class="block h-5 w-9 rounded-full bg-slate-200 transition peer-checked:bg-[#1e3a8a]"></span>
                                        <span class="pointer-events-none absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white shadow transition peer-checked:translate-x-4"></span>
                                    </span>
                                    <span class="text-xs font-bold text-slate-700">Paid Leave</span>
                                </label>
                                <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                    <span class="relative inline-flex h-5 w-9 shrink-0">
                                        <input type="hidden" name="requires_approval" value="0">
                                        <input id="editLtApproval" type="checkbox" name="requires_approval" value="1" class="peer sr-only">
                                        <span class="block h-5 w-9 rounded-full bg-slate-200 transition peer-checked:bg-[#1e3a8a]"></span>
                                        <span class="pointer-events-none absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white shadow transition peer-checked:translate-x-4"></span>
                                    </span>
                                    <span class="text-xs font-bold text-slate-700">Requires Approval</span>
                                </label>
                                <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                    <span class="relative inline-flex h-5 w-9 shrink-0">
                                        <input type="hidden" name="is_active" value="0">
                                        <input id="editLtActive" type="checkbox" name="is_active" value="1" class="peer sr-only">
                                        <span class="block h-5 w-9 rounded-full bg-slate-200 transition peer-checked:bg-[#1e3a8a]"></span>
                                        <span class="pointer-events-none absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white shadow transition peer-checked:translate-x-4"></span>
                                    </span>
                                    <span class="text-xs font-bold text-slate-700">Active Policy</span>
                                </label>
                            </div>
                        </div>
                        <div class="mt-6 flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                            <button type="button" onclick="closeEditLeaveTypeModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50">Cancel</button>
                            <button type="submit" class="px-5 py-2 rounded-xl bg-[#1e3a8a] hover:bg-[#1e40af] text-white text-xs font-bold">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif


        {{-- ==================== ROLES & PERMISSIONS SECTION ==================== --}}
        @if($section === 'roles' && $isSuperAdmin)
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900">Roles & System Access Control</h2>
                        <p class="text-xs text-slate-400 mt-1 max-w-xl">Configure granular permissions per organizational role. Super Admin always retains full configuration privileges.</p>
                    </div>
                    <form method="POST" action="{{ route('settings.roles.store') }}" class="flex flex-col sm:flex-row gap-2 sm:items-center">
                        @csrf
                        <input name="name" required placeholder="Role name" class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-medium w-44">
                        <input name="description" placeholder="Short description" class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-medium w-52">
                        <button class="px-4 py-2 rounded-xl bg-[#1e3a8a] hover:bg-[#1e40af] text-white text-xs font-bold whitespace-nowrap transition shadow-sm">Add Role</button>
                    </form>
                </div>
            </div>

            <form method="POST" action="{{ route('settings.roles.permissions') }}" class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                @csrf
                @method('PUT')
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px] text-left border-collapse">
                        <thead class="bg-slate-50/95 border-b border-slate-200/90 text-slate-600 text-xs font-bold uppercase tracking-wider">
                            <tr>
                                <th class="py-3.5 px-6 sticky left-0 bg-slate-50/95 border-r border-slate-200/80">Permission Module</th>
                                @foreach($roles as $role)
                                    <th class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <input type="hidden" name="role_ids[]" value="{{ $role->id }}">
                                        <span class="block font-extrabold text-slate-900">{{ $role->name }}</span>
                                        <span class="block text-[10px] font-bold text-slate-400 mt-0.5">{{ $role->users_count }} users</span>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                            @foreach($permissions as $module => $modulePermissions)
                                <tr class="bg-slate-50/80">
                                    <td colspan="{{ $roles->count() + 1 }}" class="px-6 py-2.5 text-[11px] font-extrabold uppercase tracking-wider text-slate-700">
                                        {{ $module }}
                                    </td>
                                </tr>
                                @foreach($modulePermissions as $permission)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="py-3 px-6 text-xs font-semibold text-slate-800 whitespace-nowrap sticky left-0 bg-white border-r border-slate-100">
                                            {{ $permission->name }}
                                        </td>
                                        @foreach($roles as $role)
                                            @php
                                                $locked = $role->slug === 'super-admin' && in_array($permission->slug, ['manage-roles', 'manage-settings'], true);
                                                $checked = $locked || $role->permissions->contains('id', $permission->id);
                                            @endphp
                                            <td class="py-3 px-4 text-center">
                                                <label class="relative inline-flex h-5 w-9 cursor-pointer">
                                                    <input type="checkbox" name="grants[{{ $role->id }}][]" value="{{ $permission->id }}" class="peer sr-only" @checked($checked) @disabled($locked)>
                                                    @if($locked)
                                                        <input type="hidden" name="grants[{{ $role->id }}][]" value="{{ $permission->id }}">
                                                    @endif
                                                    <span class="block h-5 w-9 rounded-full bg-slate-200 transition peer-checked:bg-[#1e3a8a] {{ $locked ? 'opacity-70' : '' }}"></span>
                                                    <span class="pointer-events-none absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white shadow transition peer-checked:translate-x-4"></span>
                                                </label>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="flex items-center justify-between gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50/70">
                    <p class="text-[11px] text-slate-400 font-medium">Changes apply immediately to user authorizations on subsequent operations.</p>
                    <button class="px-5 py-2.5 rounded-xl bg-[#1e3a8a] hover:bg-[#1e40af] text-white text-xs font-bold transition shadow-sm">Save Permissions</button>
                </div>
            </form>

            <div class="flex flex-wrap gap-3">
                @foreach($roles as $role)
                    @if(! in_array($role->slug, ['super-admin', 'hr-manager', 'employee'], true) && $role->users_count === 0)
                        <form method="POST" action="{{ route('settings.roles.destroy', $role) }}" onsubmit="return confirm('Delete {{ $role->name }}?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-xs font-bold text-rose-600 bg-white border border-rose-100 hover:bg-rose-50 rounded-xl px-3.5 py-2 transition shadow-xs">
                                Delete {{ $role->name }}
                            </button>
                        </form>
                    @endif
                @endforeach
            </div>
        @endif

    </div>
</div>
@endsection

@section('scripts')
<script>
    // Department Modal Handlers
    function openEditDepartmentModal(dept) {
        document.getElementById('editDeptName').value = dept.name || '';
        document.getElementById('editDeptCode').value = dept.code || '';
        document.getElementById('editDeptDescription').value = dept.description || '';
        document.getElementById('editDeptActive').checked = Boolean(dept.is_active);
        document.getElementById('editDepartmentForm').action = '/settings/departments/' + dept.id;
        document.getElementById('editDepartmentModal').classList.remove('hidden');
    }
    function closeEditDepartmentModal() {
        document.getElementById('editDepartmentModal').classList.add('hidden');
    }

    // Designation Modal Handlers
    function openEditDesignationModal(desig) {
        document.getElementById('editDesigName').value = desig.name || '';
        document.getElementById('editDesigDepartmentId').value = desig.department_id || '';
        document.getElementById('editDesigCode').value = desig.code || '';
        document.getElementById('editDesigActive').checked = Boolean(desig.is_active);
        document.getElementById('editDesignationForm').action = '/settings/designations/' + desig.id;
        document.getElementById('editDesignationModal').classList.remove('hidden');
    }
    function closeEditDesignationModal() {
        document.getElementById('editDesignationModal').classList.add('hidden');
    }

    // Location Modal Handlers
    function openEditLocationModal(loc) {
        document.getElementById('editLocName').value = loc.name || '';
        document.getElementById('editLocCode').value = loc.code || '';
        document.getElementById('editLocRadius').value = loc.allowed_radius_meters || 100;
        document.getElementById('editLocAddress').value = loc.address || '';
        document.getElementById('editLocGeofence').checked = Boolean(loc.geofence_enabled);
        document.getElementById('editLocActive').checked = Boolean(loc.is_active);
        document.getElementById('editLocationForm').action = '/settings/work-locations/' + loc.id;
        document.getElementById('editLocationModal').classList.remove('hidden');
    }
    function closeEditLocationModal() {
        document.getElementById('editLocationModal').classList.add('hidden');
    }

    // Leave Type Modal Handlers
    function openEditLeaveTypeModal(lt) {
        document.getElementById('editLtName').value = lt.name || '';
        document.getElementById('editLtCode').value = lt.code || '';
        document.getElementById('editLtQuota').value = lt.annual_quota || 0;
        document.getElementById('editLtPaid').checked = Boolean(lt.is_paid);
        document.getElementById('editLtApproval').checked = Boolean(lt.requires_approval);
        document.getElementById('editLtActive').checked = Boolean(lt.is_active);
        document.getElementById('editLeaveTypeForm').action = '/settings/leave-types/' + lt.id;
        document.getElementById('editLeaveTypeModal').classList.remove('hidden');
    }
    function closeEditLeaveTypeModal() {
        document.getElementById('editLeaveTypeModal').classList.add('hidden');
    }
</script>
@endsection

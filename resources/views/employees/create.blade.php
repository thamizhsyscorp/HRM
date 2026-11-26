@extends('layouts.app')

@section('title', 'Employee Registration')
@section('page_title', 'Employee Registration & Onboarding')

@section('content')
<div class="space-y-6 max-w-5xl">

    <!-- Top Navigation Header -->
    <div class="bg-white rounded-2xl border border-slate-100 p-5 sm:p-6 shadow-md flex items-center justify-between">
        <div>
            <h2 class="text-base sm:text-lg font-extrabold text-slate-900">New Staff Registration</h2>
            <p class="text-xs text-slate-500 mt-0.5">Configure authenticatable user credentials, department placement, monthly compensation, and bank details</p>
        </div>
        <a href="{{ route('employees.index') }}" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-xl transition flex items-center gap-1.5">
            <span>← Staff Directory</span>
        </a>
    </div>

    <form action="{{ route('employees.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- SECTION 1: Personal & Login Credentials -->
        <div class="bg-white rounded-2xl border border-slate-100 p-6 sm:p-7 shadow-md">
            <div class="flex items-center gap-3 border-b border-slate-100 pb-4 mb-6">
                <div class="w-8 h-8 rounded-xl bg-[#eff6ff] text-[#1e3a8a] font-extrabold flex items-center justify-center text-xs border border-[#bfdbfe]">
                    1
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Personal Identity & Login Credentials</h3>
                    <p class="text-xs text-slate-400">Authenticatable employee user profile and primary contacts</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <!-- First Name -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">First Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required placeholder="e.g. Anand" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] font-medium text-slate-900">
                </div>

                <!-- Last Name -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" placeholder="e.g. Chandran" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] font-medium text-slate-900">
                </div>

                <!-- Work Email -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Work Email (Login) <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="anand@company.test" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] font-mono text-slate-900">
                </div>

                <!-- Initial Password -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Login Password</label>
                    <input type="password" name="password" placeholder="Default: password" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] font-mono text-slate-900">
                    <span class="text-[11px] text-slate-400 mt-1 block">Leave empty to use 'password'</span>
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Primary Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+91 98765 00000" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] font-mono text-slate-900">
                </div>

                <!-- Gender -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Gender</label>
                    <select name="gender" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900">
                        <option value="">Select Gender</option>
                        <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender') === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <!-- Date of Birth -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Date of Birth</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900">
                </div>

                <!-- Emergency Contact Name -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Emergency Contact Name</label>
                    <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" placeholder="Contact person" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] text-slate-900">
                </div>

                <!-- Emergency Contact Phone -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Emergency Contact Phone</label>
                    <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" placeholder="+91 98765 99999" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] font-mono text-slate-900">
                </div>

                <!-- Address -->
                <div class="sm:col-span-2 lg:col-span-3">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Residential Address</label>
                    <input type="text" name="address" value="{{ old('address') }}" placeholder="Street address, city, state" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] text-slate-900">
                </div>
            </div>
        </div>

        <!-- SECTION 2: Organization & Employment Placement -->
        <div class="bg-white rounded-2xl border border-slate-100 p-6 sm:p-7 shadow-md">
            <div class="flex items-center gap-3 border-b border-slate-100 pb-4 mb-6">
                <div class="w-8 h-8 rounded-xl bg-[#eff6ff] text-[#1e3a8a] font-extrabold flex items-center justify-center text-xs border border-[#bfdbfe]">
                    2
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Job Role & Organization Hierarchy</h3>
                    <p class="text-xs text-slate-400">Department allocation, reporting structure, and location</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <!-- Employee Code -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Employee ID Code <span class="text-rose-500">*</span></label>
                    <input type="text" name="employee_code" value="{{ old('employee_code', $nextEmployeeCode) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] font-mono font-bold bg-[#eff6ff]/60 text-[#1e3a8a]">
                </div>

                <!-- Department -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Department <span class="text-rose-500">*</span></label>
                    <select name="department_id" id="departmentSelect" onchange="filterDesignationsByDepartment()" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900">
                        <option value="">Select Department</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }} ({{ $dept->code ?? 'Dept' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Designation -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Designation Title <span class="text-rose-500">*</span></label>
                    <select name="designation_id" id="designationSelect" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900">
                        <option value="">{{ old('department_id') ? 'Select Designation' : '— First Select a Department —' }}</option>
                        @foreach($designations as $desig)
                            @if(!old('department_id') || old('department_id') == $desig->department_id)
                                <option value="{{ $desig->id }}" data-department-id="{{ $desig->department_id }}" {{ old('designation_id') == $desig->id ? 'selected' : '' }}>
                                    {{ $desig->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <!-- Reporting Manager -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Reporting Manager</label>
                    <select name="manager_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900">
                        <option value="">No Direct Manager (Executive)</option>
                        @foreach($managers as $mgr)
                            <option value="{{ $mgr->id }}" {{ old('manager_id') == $mgr->id ? 'selected' : '' }}>
                                {{ $mgr->full_name }} ({{ $mgr->employee_code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Work Location -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Work Location</label>
                    <select name="work_location_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900">
                        @foreach($workLocations as $loc)
                            <option value="{{ $loc->id }}" {{ old('work_location_id') == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Employment Type -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Employment Type <span class="text-rose-500">*</span></label>
                    <select name="employment_type" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900">
                        <option value="FULL_TIME" {{ old('employment_type') === 'FULL_TIME' ? 'selected' : '' }}>Full Time (Permanent)</option>
                        <option value="PART_TIME" {{ old('employment_type') === 'PART_TIME' ? 'selected' : '' }}>Part Time</option>
                        <option value="CONTRACT" {{ old('employment_type') === 'CONTRACT' ? 'selected' : '' }}>Contractual</option>
                        <option value="INTERN" {{ old('employment_type') === 'INTERN' ? 'selected' : '' }}>Intern</option>
                    </select>
                </div>

                <!-- System Access Role -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">System Access Role <span class="text-rose-500">*</span></label>
                    <select name="role_slug" id="roleSelect" onchange="toggleManagerOptions()" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900 font-semibold">
                        <option value="employee" {{ old('role_slug', 'employee') === 'employee' ? 'selected' : '' }}>Employee (Standard Self-Service)</option>
                        <option value="manager" {{ old('role_slug') === 'manager' ? 'selected' : '' }}>Department Manager (Oversees Department Staff & Attendance)</option>
                        <option value="hr-manager" {{ old('role_slug') === 'hr-manager' ? 'selected' : '' }}>HR Manager (Human Resources & Leave Approver)</option>
                        <option value="super-admin" {{ old('role_slug') === 'super-admin' ? 'selected' : '' }}>Super Admin (Unrestricted System Access)</option>
                    </select>
                </div>

                <!-- Joining Date -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Joining Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="joining_date" value="{{ old('joining_date', date('Y-m-d')) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900">
                </div>

                <!-- Department Lead Option (shows when Manager selected) -->
                <div id="managerDeptOption" class="hidden sm:col-span-2 lg:col-span-3 p-3.5 rounded-xl bg-blue-50/80 border border-blue-200">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="is_department_head" value="1" {{ old('is_department_head') ? 'checked' : '' }} class="rounded border-slate-300 text-[#1e3a8a] focus:ring-[#1e3a8a] w-4 h-4">
                        <span class="text-xs font-bold text-[#1e3a8a]">Designate as Head / Lead of the Selected Department</span>
                    </label>
                    <span class="text-[11px] text-slate-500 block mt-1 pl-6">Allows employee to manage staff placement, attendance oversight, and leave visibility for their department.</span>
                </div>
            </div>
        </div>

        <!-- SECTION 3: Salary Components & Compensation (INR) -->
        <div class="bg-white rounded-2xl border border-slate-100 p-6 sm:p-7 shadow-md">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between border-b border-slate-100 pb-5 mb-6 gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-[#eff6ff] text-[#1e3a8a] font-extrabold flex items-center justify-center text-xs border border-[#bfdbfe]">
                        3
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Monthly Compensation & Statutory Structure</h3>
                        <p class="text-xs text-slate-400">Configure allowances, percentage-based statutory deductions & live take-home pay</p>
                    </div>
                </div>
                <!-- Real-time Compensation Strip -->
                <div class="flex flex-wrap items-center gap-3 bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                    <div class="px-2">
                        <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider block">Gross Earnings</span>
                        <p id="previewMonthlyGross" class="text-sm font-extrabold text-slate-800 font-mono">₹57,000</p>
                    </div>
                    <div class="h-7 w-px bg-slate-200"></div>
                    <div class="px-2">
                        <span class="text-[10px] text-rose-500 font-bold uppercase tracking-wider block">Total Deductions</span>
                        <p id="previewTotalDeductions" class="text-sm font-extrabold text-rose-600 font-mono">-₹4,400</p>
                    </div>
                    <div class="h-7 w-px bg-slate-200"></div>
                    <div class="bg-emerald-600 text-white px-3 py-1.5 rounded-lg shadow-sm">
                        <span class="text-[10px] text-emerald-100 font-extrabold uppercase tracking-wider block">Monthly Take-Home</span>
                        <p id="previewMonthlyTakeHome" class="text-base font-black font-mono">₹52,600</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <!-- Basic Pay -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Basic Salary (Monthly) <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-semibold text-sm">₹</span>
                        <input type="number" id="inpBasic" name="basic_salary" value="{{ old('basic_salary', 35000) }}" oninput="updateSalaryPreview()" required min="0" class="w-full pl-8 pr-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] font-mono font-semibold text-slate-900">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Primary base for statutory % deductions</p>
                </div>

                <!-- HRA -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">House Rent Allowance (HRA)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-semibold text-sm">₹</span>
                        <input type="number" id="inpHra" name="hra" value="{{ old('hra', 14000) }}" oninput="updateSalaryPreview()" min="0" class="w-full pl-8 pr-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] font-mono font-semibold text-slate-900">
                    </div>
                </div>

                <!-- Conveyance -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Conveyance Allowance</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-semibold text-sm">₹</span>
                        <input type="number" id="inpConveyance" name="conveyance" value="{{ old('conveyance', 2000) }}" oninput="updateSalaryPreview()" min="0" class="w-full pl-8 pr-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] font-mono font-semibold text-slate-900">
                    </div>
                </div>

                <!-- Special Allowance -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Special Allowance</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-semibold text-sm">₹</span>
                        <input type="number" id="inpSpecial" name="special_allowance" value="{{ old('special_allowance', 6000) }}" oninput="updateSalaryPreview()" min="0" class="w-full pl-8 pr-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] font-mono font-semibold text-slate-900">
                    </div>
                </div>

                <!-- Provident Fund (Percentage) -->
                <div class="bg-amber-50/50 p-3 rounded-xl border border-amber-200/80">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">PF Deduction (%)</label>
                        <span id="previewPfAmount" class="text-xs font-bold text-amber-800 font-mono">₹4,200/mo</span>
                    </div>
                    <div class="relative">
                        <input type="number" id="inpPfPercent" name="provident_fund_percentage" value="{{ old('provident_fund_percentage', 12) }}" oninput="updateSalaryPreview()" step="0.1" min="0" max="100" class="w-full pl-3.5 pr-8 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] font-mono font-semibold text-slate-900 bg-white">
                        <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 font-extrabold text-sm pointer-events-none">%</span>
                    </div>
                    <input type="hidden" id="inpPf" name="provident_fund" value="{{ old('provident_fund', 4200) }}">
                    <p class="text-[11px] text-slate-500 mt-1">Calculated on Basic (EPFO standard 12%)</p>
                </div>

                <!-- Other Deductions (Percentage - TDS / Insurance) -->
                <div class="bg-rose-50/50 p-3 rounded-xl border border-rose-200/80">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Other Deductions (%)</label>
                        <span id="previewOtherDeductionAmount" class="text-xs font-bold text-rose-800 font-mono">₹0/mo</span>
                    </div>
                    <div class="relative">
                        <input type="number" id="inpOtherDeductionPercent" name="other_deductions_percentage" value="{{ old('other_deductions_percentage', 0) }}" oninput="updateSalaryPreview()" step="0.1" min="0" max="100" class="w-full pl-3.5 pr-8 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] font-mono font-semibold text-slate-900 bg-white">
                        <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 font-extrabold text-sm pointer-events-none">%</span>
                    </div>
                    <input type="hidden" id="inpOtherDeduction" name="other_deductions" value="{{ old('other_deductions', 0) }}">
                    <p class="text-[11px] text-slate-500 mt-1">Calculated on Basic (e.g. TDS / ESI / Voluntary)</p>
                </div>

                <!-- Professional Tax (Fixed Amount) -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Professional Tax (PT)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-semibold text-sm">₹</span>
                        <input type="number" id="inpPt" name="professional_tax" value="{{ old('professional_tax', 200) }}" oninput="updateSalaryPreview()" min="0" class="w-full pl-8 pr-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] font-mono font-semibold text-slate-900">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Standard statutory flat tax slab</p>
                </div>
            </div>
        </div>

        <!-- SECTION 4: Annual Leave Quota Allocation -->
        <div class="bg-white rounded-2xl border border-slate-100 p-6 sm:p-7 shadow-md">
            <div class="flex items-center gap-3 border-b border-slate-100 pb-4 mb-6">
                <div class="w-8 h-8 rounded-xl bg-[#eff6ff] text-[#1e3a8a] font-extrabold flex items-center justify-center text-xs border border-[#bfdbfe]">
                    4
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Annual Leave Quota Allocation</h3>
                    <p class="text-xs text-slate-400">Configures calendar year time-off entitlement balances</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="p-4 rounded-2xl bg-[#eff6ff]/70 border border-[#bfdbfe]">
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#1e3a8a] mb-1.5">Casual Leave (CL)</label>
                    <input type="number" name="casual_leave_quota" value="{{ old('casual_leave_quota', 12) }}" min="0" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm font-bold text-slate-900 font-mono bg-white">
                    <span class="text-[11px] text-slate-400 mt-1 block">Default: 12 days / yr</span>
                </div>

                <div class="p-4 rounded-2xl bg-[#eff6ff]/70 border border-[#bfdbfe]">
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#1e3a8a] mb-1.5">Sick Leave (SL)</label>
                    <input type="number" name="sick_leave_quota" value="{{ old('sick_leave_quota', 12) }}" min="0" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm font-bold text-slate-900 font-mono bg-white">
                    <span class="text-[11px] text-slate-400 mt-1 block">Default: 12 days / yr</span>
                </div>

                <div class="p-4 rounded-2xl bg-[#eff6ff]/70 border border-[#bfdbfe]">
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#1e3a8a] mb-1.5">Earned Leave (EL)</label>
                    <input type="number" name="earned_leave_quota" value="{{ old('earned_leave_quota', 15) }}" min="0" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm font-bold text-slate-900 font-mono bg-white">
                    <span class="text-[11px] text-slate-400 mt-1 block">Default: 15 days / yr</span>
                </div>
            </div>
        </div>

        <!-- SECTION 5: Bank Account & Disbursal -->
        <div class="bg-white rounded-2xl border border-slate-100 p-6 sm:p-7 shadow-md">
            <div class="flex items-center gap-3 border-b border-slate-100 pb-4 mb-6">
                <div class="w-8 h-8 rounded-xl bg-[#eff6ff] text-[#1e3a8a] font-extrabold flex items-center justify-center text-xs border border-[#bfdbfe]">
                    5
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Bank Account Details (Disbursal)</h3>
                    <p class="text-xs text-slate-400">Employee account for automated salary disbursements</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Bank Name</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name') }}" placeholder="e.g. State Bank of India" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] text-slate-900">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Account Holder Name</label>
                    <input type="text" name="account_holder_name" value="{{ old('account_holder_name') }}" placeholder="As per bank passbook" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] text-slate-900">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Account Number</label>
                    <input type="text" name="account_number" value="{{ old('account_number') }}" placeholder="e.g. 123456789012" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] font-mono text-slate-900">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">IFSC Code</label>
                    <input type="text" name="ifsc_code" value="{{ old('ifsc_code') }}" placeholder="e.g. SBIN0001234" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] font-mono uppercase text-slate-900">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Branch Name</label>
                    <input type="text" name="branch_name" value="{{ old('branch_name') }}" placeholder="Branch location" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] text-slate-900">
                </div>
            </div>
        </div>

        <!-- Submit Toolbar -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('employees.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition">
                Cancel
            </a>
            <button type="submit" class="px-7 py-3 rounded-xl bg-[#1e3a8a] hover:bg-[#1e40af] text-white text-xs font-extrabold shadow-md shadow-indigo-500/20 transition cursor-pointer flex items-center gap-2">
                <span>Register & Onboard Employee</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    const allDesignations = {!! json_encode($designations->map(fn($d) => [
        'id' => $d->id,
        'department_id' => $d->department_id,
        'name' => $d->name,
    ])) !!};

    function filterDesignationsByDepartment(selectedDesigId = null) {
        const deptSelect = document.getElementById('departmentSelect');
        const desigSelect = document.getElementById('designationSelect');
        if (!deptSelect || !desigSelect) return;

        const selectedDeptId = deptSelect.value;
        const currentVal = selectedDesigId !== null && selectedDesigId !== '' ? selectedDesigId : desigSelect.value;

        desigSelect.innerHTML = '';

        if (!selectedDeptId) {
            const defaultOpt = document.createElement('option');
            defaultOpt.value = '';
            defaultOpt.textContent = '— First Select a Department —';
            desigSelect.appendChild(defaultOpt);
            desigSelect.disabled = true;
            return;
        }

        desigSelect.disabled = false;
        const defaultOpt = document.createElement('option');
        defaultOpt.value = '';
        defaultOpt.textContent = 'Select Designation';
        desigSelect.appendChild(defaultOpt);

        const matching = allDesignations.filter(d => String(d.department_id) === String(selectedDeptId));

        if (matching.length === 0) {
            defaultOpt.textContent = 'No designations configured for this department';
            return;
        }

        let isCurrentValPreserved = false;
        matching.forEach(d => {
            const opt = document.createElement('option');
            opt.value = d.id;
            opt.textContent = d.name;
            if (String(currentVal) === String(d.id)) {
                opt.selected = true;
                isCurrentValPreserved = true;
            }
            desigSelect.appendChild(opt);
        });

        if (!isCurrentValPreserved && selectedDesigId === null) {
            desigSelect.value = '';
        }
    }

    function updateSalaryPreview() {
        const basic = parseFloat(document.getElementById('inpBasic')?.value) || 0;
        const hra = parseFloat(document.getElementById('inpHra')?.value) || 0;
        const conveyance = parseFloat(document.getElementById('inpConveyance')?.value) || 0;
        const special = parseFloat(document.getElementById('inpSpecial')?.value) || 0;
        const gross = basic + hra + conveyance + special;

        // PF percentage calculation on Basic Salary
        const pfPercent = parseFloat(document.getElementById('inpPfPercent')?.value) || 0;
        const pfAmount = Math.round((basic * (pfPercent / 100)) * 100) / 100;
        const hiddenPf = document.getElementById('inpPf');
        if (hiddenPf) hiddenPf.value = pfAmount;
        const previewPf = document.getElementById('previewPfAmount');
        if (previewPf) previewPf.textContent = '₹' + pfAmount.toLocaleString('en-IN') + '/mo (' + pfPercent + '%)';

        // Other Deductions percentage calculation on Basic Salary
        const otherPercent = parseFloat(document.getElementById('inpOtherDeductionPercent')?.value) || 0;
        const otherAmount = Math.round((basic * (otherPercent / 100)) * 100) / 100;
        const hiddenOther = document.getElementById('inpOtherDeduction');
        if (hiddenOther) hiddenOther.value = otherAmount;
        const previewOther = document.getElementById('previewOtherDeductionAmount');
        if (previewOther) previewOther.textContent = '₹' + otherAmount.toLocaleString('en-IN') + '/mo (' + otherPercent + '%)';

        // Professional Tax
        const pt = parseFloat(document.getElementById('inpPt')?.value) || 0;

        // Total deductions & Take Home
        const totalDeductions = pfAmount + otherAmount + pt;
        const takeHome = Math.max(0, gross - totalDeductions);

        const elGross = document.getElementById('previewMonthlyGross');
        if (elGross) elGross.textContent = '₹' + gross.toLocaleString('en-IN');

        const elDeductions = document.getElementById('previewTotalDeductions');
        if (elDeductions) elDeductions.textContent = '-₹' + totalDeductions.toLocaleString('en-IN');

        const elTakeHome = document.getElementById('previewMonthlyTakeHome');
        if (elTakeHome) elTakeHome.textContent = '₹' + takeHome.toLocaleString('en-IN');
    }

    function toggleManagerOptions() {
        const role = document.getElementById('roleSelect')?.value;
        const opt = document.getElementById('managerDeptOption');
        if (opt) {
            if (role === 'manager') {
                opt.classList.remove('hidden');
            } else {
                opt.classList.add('hidden');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        filterDesignationsByDepartment('{{ old('designation_id') }}');
        toggleManagerOptions();
        updateSalaryPreview();
    });
</script>
@endsection

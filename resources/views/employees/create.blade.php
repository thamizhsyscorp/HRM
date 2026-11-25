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
                    <select name="department_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900">
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
                    <select name="designation_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900">
                        <option value="">Select Designation</option>
                        @foreach($designations as $desig)
                            <option value="{{ $desig->id }}" {{ old('designation_id') == $desig->id ? 'selected' : '' }}>
                                {{ $desig->name }}
                            </option>
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
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-[#eff6ff] text-[#1e3a8a] font-extrabold flex items-center justify-center text-xs border border-[#bfdbfe]">
                        3
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Monthly Compensation & Statutory Structure</h3>
                        <p class="text-xs text-slate-400">Configures employee initial salary breakdown components (INR)</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-xs text-slate-400 font-medium">Estimated Monthly Gross</span>
                    <p id="previewMonthlyGross" class="text-xl font-extrabold text-[#1e3a8a] font-mono">₹57,000</p>
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

                <!-- Provident Fund -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Provident Fund (PF Deduction)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-semibold text-sm">₹</span>
                        <input type="number" id="inpPf" name="provident_fund" value="{{ old('provident_fund', 1800) }}" min="0" class="w-full pl-8 pr-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] font-mono font-semibold text-slate-900">
                    </div>
                </div>

                <!-- Professional Tax -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Professional Tax (PT)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-semibold text-sm">₹</span>
                        <input type="number" id="inpPt" name="professional_tax" value="{{ old('professional_tax', 200) }}" min="0" class="w-full pl-8 pr-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] font-mono font-semibold text-slate-900">
                    </div>
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
    function updateSalaryPreview() {
        const basic = parseFloat(document.getElementById('inpBasic').value) || 0;
        const hra = parseFloat(document.getElementById('inpHra').value) || 0;
        const conveyance = parseFloat(document.getElementById('inpConveyance').value) || 0;
        const special = parseFloat(document.getElementById('inpSpecial').value) || 0;
        const total = basic + hra + conveyance + special;
        document.getElementById('previewMonthlyGross').textContent = '₹' + total.toLocaleString('en-IN');
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
    document.addEventListener('DOMContentLoaded', toggleManagerOptions);
</script>
@endsection

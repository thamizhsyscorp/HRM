<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>HRM Portal — Sign In</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        indigo: {
                            50: '#f5f7ff',
                            100: '#ebf0fe',
                            200: '#ced9fd',
                            300: '#a3b8fc',
                            400: '#7290f8',
                            500: '#1e3a8a',
                            600: '#4f46e5',
                            700: '#1e3a8a',
                            800: '#3730a3',
                            900: '#312e81',
                            950: '#1e1b4b',
                        },
                        periwinkle: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#b0adef',
                            400: '#8b84e7',
                            500: '#1e3a8a',
                            600: '#1e40af',
                            700: '#3e4abf',
                            800: '#343da0',
                            900: '#2d3482',
                        }
                    },
                    boxShadow: {
                        'soft': '0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.03)',
                        'card': '0 10px 40px -5px rgba(30, 27, 75, 0.07), 0 2px 8px -1px rgba(30, 27, 75, 0.04)',
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-800 antialiased min-h-screen flex items-center justify-center p-4 sm:p-6 selection:bg-[#eff6ff] selection:text-[#1e3a8a]">

    <!-- Main Container Card -->
    <div class="w-full max-w-5xl bg-white rounded-3xl shadow-card overflow-hidden grid grid-cols-1 lg:grid-cols-12 border border-slate-200/90">
        
        <!-- Left Branding Panel (Rich Deep Indigo Gradient) (5 Cols) -->
        <div class="lg:col-span-5 bg-gradient-to-br from-[#1e1b4b] via-[#312e81] to-[#1e1b4b] text-white p-8 lg:p-12 flex flex-col justify-between relative overflow-hidden border-b lg:border-b-0 lg:border-r border-[#2e2a72]">
            <!-- Ambient Glow Elements -->
            <div class="absolute -right-16 -bottom-16 w-60 h-60 bg-[#1e3a8a]/30 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-16 -top-16 w-60 h-60 bg-[#8b84e7]/25 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10">
                <!-- Brand Emblem -->
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-[#1e3a8a] to-[#1e3a8a] rounded-2xl flex items-center justify-center font-extrabold text-xl text-white shadow-lg border border-indigo-400/40">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-xl font-extrabold tracking-tight text-white block">Syscorp</span>
                        <span class="text-xs text-indigo-200 font-medium">Enterprise HR & Payroll</span>
                    </div>
                </div>

                <div class="mt-12">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-indigo-200 text-xs font-semibold border border-white/15 mb-5 backdrop-blur-sm">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Workforce Operating System
                    </div>
                    <h2 class="text-3xl font-extrabold leading-tight tracking-tight text-white">
                        Smart HR automation, rooted in precision.
                    </h2>
                    <p class="mt-4 text-indigo-100/80 text-sm leading-relaxed font-normal">
                        Unified management for workforce records, real-time GPS-verified attendance logs, multi-tier leave reviews, and statutory payroll structures.
                    </p>
                </div>

                <!-- Features Checklist -->
                <div class="mt-8 space-y-3.5">
                    <div class="flex items-center gap-3 text-xs text-indigo-100 font-medium">
                        <div class="w-5 h-5 rounded-lg bg-indigo-500/40 border border-indigo-300/30 flex items-center justify-center text-white font-bold text-[10px]">✓</div>
                        <span>Role-Based Access Control (Admin, HR, Staff)</span>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-indigo-100 font-medium">
                        <div class="w-5 h-5 rounded-lg bg-indigo-500/40 border border-indigo-300/30 flex items-center justify-center text-white font-bold text-[10px]">✓</div>
                        <span>GPS Geofenced Daily Attendance & Calendar</span>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-indigo-100 font-medium">
                        <div class="w-5 h-5 rounded-lg bg-indigo-500/40 border border-indigo-300/30 flex items-center justify-center text-white font-bold text-[10px]">✓</div>
                        <span>Real-time Accrued Take-Home Salary Calculator</span>
                    </div>
                </div>
            </div>

            <div class="relative z-10 mt-10 pt-6 border-t border-indigo-800/60 flex items-center justify-between text-xs text-indigo-200/80">
                <span>&copy; {{ date('Y') }} Syscorp Enterprise</span>
                <span class="flex items-center gap-1.5 font-mono text-[11px] text-indigo-200 font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Verified Secure
                </span>
            </div>
        </div>

        <!-- Right Form Panel (Crisp White Canvas) (7 Cols) -->
        <div class="lg:col-span-7 p-7 sm:p-11 flex flex-col justify-center bg-white">
            
            <div class="mb-6">
                <span class="text-xs font-extrabold uppercase tracking-wider text-[#1e3a8a]">Workplace Access</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">Sign in to your workplace</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Select your corporate role or enter your credentials</p>
            </div>

            <!-- Corporate Role Quick Access Selector -->
            <div class="mb-6 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-[#1e3a8a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Authorized Personnel Portals
                    </span>
                    <span class="text-[11px] text-slate-400 font-mono">Instant Access</span>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                    <!-- Super Admin -->
                    <button 
                        type="button" 
                        onclick="fillCredentials('admin@hrm.test', 'password')" 
                        class="p-3 rounded-xl border border-slate-200 bg-white hover:border-[#1e3a8a] hover:bg-[#eff6ff] text-left transition duration-150 text-xs shadow-2xs cursor-pointer group"
                    >
                        <div class="flex items-center justify-between">
                            <span class="font-extrabold text-slate-900 group-hover:text-[#1e3a8a]">Super Admin</span>
                        </div>
                        <span class="text-[11px] text-slate-500 group-hover:text-[#1e3a8a] font-mono block mt-1 truncate">admin@hrm.test</span>
                    </button>

                    <!-- HR Manager -->
                    <button 
                        type="button" 
                        onclick="fillCredentials('hr@hrm.test', 'password')" 
                        class="p-3 rounded-xl border border-slate-200 bg-white hover:border-[#1e3a8a] hover:bg-[#eff6ff] text-left transition duration-150 text-xs shadow-2xs cursor-pointer group"
                    >
                        <div class="flex items-center justify-between">
                            <span class="font-extrabold text-slate-900 group-hover:text-[#1e3a8a]">HR Manager</span>
                        </div>
                        <span class="text-[11px] text-slate-500 group-hover:text-[#1e3a8a] font-mono block mt-1 truncate">hr@hrm.test</span>
                    </button>

                    <!-- Employee -->
                    <button 
                        type="button" 
                        onclick="fillCredentials('employee@hrm.test', 'password')" 
                        class="p-3 rounded-xl border border-slate-200 bg-white hover:border-[#1e3a8a] hover:bg-[#eff6ff] text-left transition duration-150 text-xs shadow-2xs cursor-pointer group"
                    >
                        <div class="flex items-center justify-between">
                            <span class="font-extrabold text-slate-900 group-hover:text-[#1e3a8a]">Staff Employee</span>
                        </div>
                        <span class="text-[11px] text-slate-500 group-hover:text-[#1e3a8a] font-mono block mt-1 truncate">employee@hrm.test</span>
                    </button>
                </div>
            </div>

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Work Email Address
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                        </div>
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            value="{{ old('email', 'admin@hrm.test') }}" 
                            required 
                            autofocus 
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white placeholder-slate-400 font-medium transition text-slate-900"
                            placeholder="name@company.com"
                        >
                    </div>
                    @error('email')
                        <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            value="password" 
                            required 
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white placeholder-slate-400 font-medium transition text-slate-900"
                            placeholder="••••••••"
                        >
                    </div>
                    @error('password')
                        <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-[#1e3a8a] focus:ring-[#1e3a8a] border-slate-300">
                        <span class="text-slate-600 font-medium">Keep me signed in</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="w-full py-3 px-4 bg-[#1e3a8a] hover:bg-[#1e40af] active:scale-[0.99] text-white font-extrabold rounded-xl text-sm shadow-md shadow-indigo-500/20 transition flex items-center justify-center gap-2 cursor-pointer mt-2"
                >
                    <span>Sign In to Workplace</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </form>

        </div>
    </div>

    <script>
        function fillCredentials(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
</body>
</html>

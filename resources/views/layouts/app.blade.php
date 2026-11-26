<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HRM Portal') — {{ config('app.name', 'Syscorp') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        indigo: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#1e3a8a',
                            800: '#3730a3',
                            900: '#312e81',
                            950: '#1e1b4b',
                        },
                        periwinkle: {
                            50: '#f4f5fd',
                            100: '#e9ebfb',
                            200: '#d7dbf8',
                            300: '#bac2f3',
                            400: '#95a1ec',
                            500: '#7581e4',
                            600: '#1e3a8a',
                            700: '#4854cc',
                            800: '#3c46a6',
                            900: '#353c84',
                        }
                    },
                    boxShadow: {
                        'soft': '0 1px 3px 0 rgba(0, 0, 0, 0.03), 0 1px 2px -1px rgba(0, 0, 0, 0.02)',
                        'card': '0 4px 20px -2px rgba(15, 23, 42, 0.04), 0 2px 6px -1px rgba(15, 23, 42, 0.02)',
                        'glow': '0 10px 25px -5px rgba(93, 109, 232, 0.25)',
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="/js/html5-qrcode.min.js?v=2.0" onerror="this.onerror=null;this.src='https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js'"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-800 antialiased min-h-full flex flex-col selection:bg-indigo-100 selection:text-indigo-900">

    <div class="min-h-screen flex">
        
        <!-- Mobile Sidebar Backdrop Overlay -->
        <div id="sidebarOverlay" onclick="toggleMobileSidebar()" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-30 hidden md:hidden transition-opacity duration-300"></div>

        <!-- Clean Modern Sidebar -->
        <aside id="sidebarNav" class="w-64 bg-white text-slate-700 flex flex-col justify-between border-r border-slate-200/80 fixed md:sticky top-0 h-screen z-40 transform -translate-x-full md:translate-x-0 transition-transform duration-200 ease-in-out shrink-0 overflow-y-auto">
            <div>
                <!-- Brand Header -->
                <div class="p-5 flex items-center justify-between border-b border-slate-100">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 bg-[#1e3a8a] text-white rounded-xl flex items-center justify-center font-extrabold text-base shadow-md shadow-blue-900/20 group-hover:scale-105 transition duration-150">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="text-base font-extrabold tracking-tight text-slate-900 block group-hover:text-[#1e3a8a] transition">Syscorp</span>
                            <span class="text-[11px] text-slate-400 font-semibold">HRM Portal</span>
                        </div>
                    </a>
                    <button type="button" onclick="toggleMobileSidebar()" class="md:hidden text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Navigation Sections -->
                <nav class="p-3.5 space-y-4 text-xs">
                    
                    <!-- Main -->
                    <div>
                        <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">Workspace</span>
                        <div class="space-y-1">
                            <!-- Dashboard -->
                            <a href="{{ route('dashboard') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('dashboard') ? 'bg-blue-50/80 text-[#1e3a8a] font-extrabold shadow-2xs border border-[#1e3a8a]/20' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-semibold' }}">
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-[#1e3a8a]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                    <span class="text-[13px]">Dashboard</span>
                                </div>
                            </a>

                            <!-- Calendar -->
                            <a href="{{ route('attendance.calendar') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('attendance.calendar') ? 'bg-blue-50/80 text-[#1e3a8a] font-extrabold shadow-2xs border border-[#1e3a8a]/20' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-semibold' }}">
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4 {{ request()->routeIs('attendance.calendar') ? 'text-[#1e3a8a]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-[13px]">Calendar</span>
                                </div>
                            </a>

                            <!-- Announcements -->
                            <a href="{{ route('announcements.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('announcements.*') ? 'bg-blue-50/80 text-[#1e3a8a] font-extrabold shadow-2xs border border-[#1e3a8a]/20' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-semibold' }}">
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4 {{ request()->routeIs('announcements.*') ? 'text-[#1e3a8a]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                    </svg>
                                    <span class="text-[13px]">Announcements</span>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- People -->
                    <div>
                        <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">People</span>
                        <div class="space-y-1">
                            @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('hr-manager'))
                                <a href="{{ route('employees.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('employees.*') ? 'bg-blue-50/80 text-[#1e3a8a] font-extrabold shadow-2xs border border-[#1e3a8a]/20' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-semibold' }}">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-4 h-4 {{ request()->routeIs('employees.*') ? 'text-[#1e3a8a]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                        <span class="text-[13px]">Employees</span>
                                    </div>
                                </a>
                            @endif

                            <a href="{{ route('leaves.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('leaves.*') ? 'bg-blue-50/80 text-[#1e3a8a] font-extrabold shadow-2xs border border-[#1e3a8a]/20' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-semibold' }}">
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4 {{ request()->routeIs('leaves.*') ? 'text-[#1e3a8a]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <span class="text-[13px]">Leave Requests</span>
                                </div>
                            </a>

                            @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('hr-manager'))
                                <a href="{{ route('holidays.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('holidays.*') ? 'bg-blue-50/80 text-[#1e3a8a] font-extrabold shadow-2xs border border-[#1e3a8a]/20' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-semibold' }}">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-4 h-4 {{ request()->routeIs('holidays.*') ? 'text-[#1e3a8a]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                        </svg>
                                        <span class="text-[13px]">Holidays</span>
                                    </div>
                                    <span class="text-[9px] font-mono font-extrabold bg-amber-100 text-amber-800 px-1.5 py-0.5 rounded-md">Calendar</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Time & Payroll -->
                    <div>
                        <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">Time & Payroll</span>
                        <div class="space-y-1">
                            <a href="{{ route('attendance.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('attendance.index') ? 'bg-blue-50/80 text-[#1e3a8a] font-extrabold shadow-2xs border border-[#1e3a8a]/20' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-semibold' }}">
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4 {{ request()->routeIs('attendance.index') ? 'text-[#1e3a8a]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-[13px]">Attendance</span>
                                </div>
                            </a>

                            <a href="{{ route('payroll.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('payroll.*') ? 'bg-blue-50/80 text-[#1e3a8a] font-extrabold shadow-2xs border border-[#1e3a8a]/20' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-semibold' }}">
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4 {{ request()->routeIs('payroll.*') ? 'text-[#1e3a8a]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <span class="text-[13px]">{{ auth()->user()->hasRole('super-admin') ? 'Company Payroll' : (auth()->user()->hasRole('hr-manager') ? 'Workforce Payroll' : 'My Salary & Take-Home') }}</span>
                                </div>
                                <span class="text-[9px] font-mono font-extrabold bg-blue-100 text-blue-900 px-1.5 py-0.5 rounded-md">Live</span>
                            </a>
                        </div>
                    </div>

                    @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('hr-manager'))
                        <div>
                            <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">Admin</span>
                            <div class="space-y-1">
                                <a href="{{ route('settings.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('settings.*') ? 'bg-blue-50/80 text-[#1e3a8a] font-extrabold shadow-2xs border border-[#1e3a8a]/20' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-semibold' }}">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-4 h-4 {{ request()->routeIs('settings.*') ? 'text-[#1e3a8a]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span class="text-[13px]">Settings</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @endif

                </nav>
            </div>

            <!-- User Footer Profile Card -->
            <div class="p-3.5 border-t border-slate-100 bg-white">
                <button 
                    type="button" 
                    onclick="toggleProfilePopover(event)" 
                    class="w-full flex items-center gap-3 p-1 rounded-xl hover:bg-slate-50 transition cursor-pointer text-left group"
                    title="Account Menu"
                >
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#1e3a8a] font-extrabold text-xs flex items-center justify-center shrink-0 border border-blue-200 shadow-2xs group-hover:scale-105 transition">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0 text-left">
                        <p class="text-xs font-bold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-slate-400 truncate">{{ auth()->user()->roles->first()->name ?? 'User' }}</p>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                    </svg>
                </button>
            </div>
        </aside>

        <!-- Main Workspace -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top Navbar -->
            <header class="bg-white border-b border-slate-200/80 px-4 sm:px-6 py-3 sticky top-0 z-20">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="toggleMobileSidebar()" class="md:hidden p-2 rounded-xl text-slate-700 hover:bg-slate-50 border border-slate-200 cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                            </svg>
                            <h1 class="text-xs sm:text-sm font-extrabold text-slate-900 tracking-tight truncate max-w-[130px] sm:max-w-none">
                                @yield('page_title', 'Dashboard')
                            </h1>
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">
                        <!-- Date chip -->
                        <span class="hidden lg:inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                            {{ now()->format('D, M j, Y') }}
                        </span>

                        <!-- Notification Bell -->
                        <a href="{{ route('announcements.index') }}" class="relative p-1.5 sm:p-2 text-slate-500 hover:text-slate-800 hover:bg-slate-50 rounded-xl transition" title="Bulletins">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span class="absolute top-1 right-1 w-2 h-2 rounded-full bg-rose-500"></span>
                        </a>

                        <!-- Pop-Style User Profile Trigger & Menu -->
                        <div class="relative" id="profileDropdownContainer">
                            <button 
                                type="button" 
                                id="profileDropdownBtn"
                                onclick="toggleProfilePopover(event)" 
                                class="flex items-center gap-2 p-1 pl-1.5 pr-2.5 rounded-full hover:bg-slate-100 transition border border-slate-200/80 cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/20"
                                aria-expanded="false"
                                aria-haspopup="true"
                                title="Account Profile Menu"
                            >
                                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-[#1e3a8a] text-white font-extrabold text-[11px] sm:text-xs flex items-center justify-center shadow-xs shrink-0 ring-2 ring-blue-100">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                </div>
                                <span class="hidden sm:inline text-xs font-bold text-slate-800 max-w-[100px] truncate">
                                    {{ auth()->user()->name }}
                                </span>
                                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-150" id="profileChevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Profile Popover Dropdown Menu -->
                            <div 
                                id="profilePopover" 
                                class="hidden absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-slate-200 py-2 z-50 transform origin-top-right transition duration-150"
                            >
                                <!-- User Identity Banner -->
                                <div class="px-4 py-3 border-b border-slate-100 flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#1e3a8a] font-extrabold text-sm flex items-center justify-center shrink-0 border border-blue-200">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-extrabold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                                        <p class="text-[11px] text-slate-400 truncate">{{ auth()->user()->email }}</p>
                                        <span class="inline-block mt-1 px-2 py-0.5 rounded text-[9px] font-bold bg-blue-50 text-[#1e3a8a] border border-blue-200 font-mono">
                                            {{ auth()->user()->hasRole('super-admin') ? 'Super Admin • Owner' : (auth()->user()->roles->first()->name ?? 'User') }}
                                        </span>
                                    </div>
                                </div>

                                <div class="p-1.5 space-y-0.5 text-xs">
                                    <!-- Change Password (Opens Modal) -->
                                    <button 
                                        type="button" 
                                        onclick="closeProfilePopover(); openChangePasswordModal();" 
                                        class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-700 hover:text-[#1e3a8a] hover:bg-blue-50 font-semibold transition cursor-pointer text-left"
                                    >
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                        </svg>
                                        <span>Change Password</span>
                                    </button>

                                    @if(auth()->user()->hasRole('super-admin'))
                                        <!-- Company Owner / Super Admin Items -->
                                        <!-- Company Payroll Ledger -->
                                        <a 
                                            href="{{ route('payroll.index') }}" 
                                            class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-700 hover:text-[#1e3a8a] hover:bg-blue-50 font-semibold transition text-left"
                                        >
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            <span>Company Payroll Ledger</span>
                                        </a>

                                        <!-- Company Settings -->
                                        <a 
                                            href="{{ route('settings.index') }}" 
                                            class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-700 hover:text-[#1e3a8a] hover:bg-blue-50 font-semibold transition text-left"
                                        >
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <span>Company Settings</span>
                                        </a>

                                        <!-- Calendar & Holidays -->
                                        <a 
                                            href="{{ route('attendance.calendar') }}" 
                                            class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-700 hover:text-[#1e3a8a] hover:bg-blue-50 font-semibold transition text-left"
                                        >
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span>Calendar & Holidays</span>
                                        </a>
                                    @else
                                        <!-- Employee Items -->
                                        <!-- My Salary & Take-Home -->
                                        <a 
                                            href="{{ route('payroll.index') }}" 
                                            class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-700 hover:text-[#1e3a8a] hover:bg-blue-50 font-semibold transition text-left"
                                        >
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            <span>My Salary & Take-Home</span>
                                        </a>

                                        <!-- Calendar & Holidays -->
                                        <a 
                                            href="{{ route('attendance.calendar') }}" 
                                            class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-700 hover:text-[#1e3a8a] hover:bg-blue-50 font-semibold transition text-left"
                                        >
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span>Calendar & Holidays</span>
                                        </a>
                                    @endif
                                </div>

                                <div class="p-1.5 border-t border-slate-100">
                                    <!-- Sign Out / Logout Form -->
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button 
                                            type="submit" 
                                            class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-rose-600 hover:bg-rose-50 font-bold transition cursor-pointer text-left text-xs"
                                        >
                                            <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                            </svg>
                                            <span>Sign Out</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-6 max-w-[1400px] w-full mx-auto pb-24 md:pb-8">
                <!-- Success Flash Alert -->
                @if (session('success'))
                    <div id="flash-success" class="p-4 rounded-2xl bg-white border border-emerald-200 text-emerald-900 text-sm flex items-center justify-between shadow-soft">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs border border-emerald-200">✓</div>
                            <span class="font-bold text-slate-800">{{ session('success') }}</span>
                        </div>
                        <button type="button" onclick="document.getElementById('flash-success').remove()" class="text-slate-400 hover:text-slate-700 text-xs font-bold px-2 py-1 cursor-pointer">
                            ✕
                        </button>
                    </div>
                @endif

                <!-- Status Flash Alert -->
                @if (session('status'))
                    <div id="flash-status" class="p-4 rounded-2xl bg-white border border-slate-200 text-slate-800 text-sm flex items-center justify-between shadow-soft">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-xl bg-indigo-100 text-indigo-800 flex items-center justify-center font-bold text-xs border border-indigo-200">ℹ</div>
                            <span class="font-bold text-slate-800">{{ session('status') }}</span>
                        </div>
                        <button type="button" onclick="document.getElementById('flash-status').remove()" class="text-slate-400 hover:text-slate-700 text-xs font-bold px-2 py-1 cursor-pointer">
                            ✕
                        </button>
                    </div>
                @endif

                <!-- Errors Alert -->
                @if ($errors->any())
                    <div class="p-4 rounded-2xl bg-white border border-rose-300 text-rose-800 text-sm shadow-soft">
                        <ul class="list-disc list-inside space-y-1 text-xs font-medium text-rose-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>

        <!-- MOBILE BOTTOM NAVIGATION BAR (md:hidden) -->
        <nav class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200/90 px-2 py-1 shadow-lg flex items-center justify-around select-none">
            @if(auth()->check() && auth()->user()->hasRole('super-admin'))
                <!-- Super Admin Mobile Navigation (Exempt from Attendance Check-In) -->
                <!-- 1. Dashboard -->
                <a 
                    href="{{ route('dashboard') }}" 
                    class="flex flex-col items-center py-1 px-2.5 rounded-xl transition {{ request()->routeIs('dashboard') ? 'text-[#1e3a8a] font-extrabold' : 'text-slate-400 hover:text-slate-700 font-medium' }}"
                >
                    <svg class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-[#1e3a8a]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span class="text-[10px] mt-0.5 tracking-tight">Home</span>
                </a>

                <!-- 2. Attendance Roster -->
                <a 
                    href="{{ route('attendance.index') }}" 
                    class="flex flex-col items-center py-1 px-2.5 rounded-xl transition {{ request()->routeIs('attendance.*') ? 'text-[#1e3a8a] font-extrabold' : 'text-slate-400 hover:text-slate-700 font-medium' }}"
                >
                    <svg class="w-5 h-5 {{ request()->routeIs('attendance.*') ? 'text-[#1e3a8a]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-[10px] mt-0.5 tracking-tight">Attendance</span>
                </a>

                <!-- 3. Staff Directory -->
                <a 
                    href="{{ route('employees.index') }}" 
                    class="flex flex-col items-center py-1 px-2.5 rounded-xl transition {{ request()->routeIs('employees.*') ? 'text-[#1e3a8a] font-extrabold' : 'text-slate-400 hover:text-slate-700 font-medium' }}"
                >
                    <svg class="w-5 h-5 {{ request()->routeIs('employees.*') ? 'text-[#1e3a8a]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="text-[10px] mt-0.5 tracking-tight">Staff</span>
                </a>

                <!-- 4. Payroll Ledger -->
                <a 
                    href="{{ route('payroll.index') }}" 
                    class="flex flex-col items-center py-1 px-2.5 rounded-xl transition {{ request()->routeIs('payroll.*') ? 'text-[#1e3a8a] font-extrabold' : 'text-slate-400 hover:text-slate-700 font-medium' }}"
                >
                    <svg class="w-5 h-5 {{ request()->routeIs('payroll.*') ? 'text-[#1e3a8a]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="text-[10px] mt-0.5 tracking-tight">Payroll</span>
                </a>

                <!-- 5. Drawer Menu / More -->
                <button 
                    type="button" 
                    onclick="toggleMobileSidebar()" 
                    class="flex flex-col items-center py-1 px-2.5 rounded-xl text-slate-400 hover:text-slate-700 transition cursor-pointer"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <span class="text-[10px] mt-0.5 tracking-tight">Menu</span>
                </button>
            @else
                <!-- Workforce & Employee Mobile Navigation (With Quick Scan QR) -->
                <!-- 1. Home / Dashboard -->
                <a 
                    href="{{ route('dashboard') }}" 
                    class="flex flex-col items-center py-1 px-2.5 rounded-xl transition {{ request()->routeIs('dashboard') ? 'text-[#1e3a8a] font-extrabold' : 'text-slate-400 hover:text-slate-700 font-medium' }}"
                >
                    <svg class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-[#1e3a8a]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span class="text-[10px] mt-0.5 tracking-tight">Home</span>
                </a>

                <!-- 2. Calendar -->
                <a 
                    href="{{ route('attendance.calendar') }}" 
                    class="flex flex-col items-center py-1 px-2.5 rounded-xl transition {{ request()->routeIs('attendance.calendar') ? 'text-[#1e3a8a] font-extrabold' : 'text-slate-400 hover:text-slate-700 font-medium' }}"
                >
                    <svg class="w-5 h-5 {{ request()->routeIs('attendance.calendar') ? 'text-[#1e3a8a]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-[10px] mt-0.5 tracking-tight">Calendar</span>
                </a>

                @php
                    $navUser = auth()->user();
                    $navEmployee = $navUser?->employee;
                    $navToday = \Carbon\Carbon::today()->toDateString();
                    $navAttendanceToday = $navEmployee ? \App\Models\Attendance::where('employee_id', $navEmployee->id)->whereDate('attendance_date', $navToday)->first() : null;
                    $navOnLeaveToday = $navEmployee ? \App\Models\LeaveRequest::where('employee_id', $navEmployee->id)
                        ->whereIn('status', ['APPROVED', 'REVOKE_REQUESTED'])
                        ->whereDate('start_date', '<=', $navToday)
                        ->whereDate('end_date', '>=', $navToday)
                        ->first() : null;
                @endphp

                <!-- 3. Prominent Quick Scan QR Button with Dynamic Status Colors -->
                @if($navOnLeaveToday)
                    <!-- On Approved Leave or Revocation Pending: GREY & BLOCKED -->
                    @php
                        $alertMsg = $navOnLeaveToday->status === 'REVOKE_REQUESTED'
                            ? 'Your leave revocation request is awaiting approval from your Reporting Manager or HR. Attendance scanning will unlock once accepted.'
                            : 'You are on approved leave today (' . ($navOnLeaveToday->leaveType->name ?? 'Leave') . '). QR Scan is blocked.\n\nIf you want to work today, go to Time-Off and click Revoke Leave for HR / Reporting Manager approval.';
                    @endphp
                    <button 
                        type="button" 
                        onclick="alert('{{ addslashes($alertMsg) }}')" 
                        class="-mt-5 w-12 h-12 rounded-full bg-slate-400 text-white flex items-center justify-center shadow-md border-2 border-white ring-4 ring-slate-200 cursor-not-allowed opacity-75"
                        title="{{ $navOnLeaveToday->status === 'REVOKE_REQUESTED' ? 'Revocation Pending Approval (Scan Blocked)' : 'On Leave Today (Scan Blocked)' }}"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                    </button>
                @elseif($navAttendanceToday && $navAttendanceToday->first_check_in_at && !$navAttendanceToday->last_check_out_at)
                    <!-- Checked In: RED (Ready to Check Out) -->
                    <button 
                        type="button" 
                        onclick="openQrScanner('check_out')" 
                        class="-mt-5 w-12 h-12 rounded-full bg-rose-600 hover:bg-rose-700 text-white flex items-center justify-center shadow-lg shadow-rose-500/30 border-2 border-white ring-4 ring-rose-100 active:scale-95 transition cursor-pointer"
                        title="Scan QR to Check Out"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                @elseif(!$navAttendanceToday || !$navAttendanceToday->first_check_in_at)
                    <!-- Not Checked In (or Revoke Approved): GREEN (Ready to Check In) -->
                    <button 
                        type="button" 
                        onclick="openQrScanner('check_in')" 
                        class="-mt-5 w-12 h-12 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white flex items-center justify-center shadow-lg shadow-emerald-500/30 border-2 border-white ring-4 ring-emerald-100 active:scale-95 transition cursor-pointer"
                        title="Scan QR to Check In"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                    </button>
                @else
                    <!-- Shift Completed for Today -->
                    <button 
                        type="button" 
                        onclick="openQrScanner('auto')" 
                        class="-mt-5 w-12 h-12 rounded-full bg-slate-800 text-emerald-400 flex items-center justify-center shadow-md border-2 border-white ring-4 ring-slate-100 active:scale-95 transition cursor-pointer"
                        title="Shift Completed ✓"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </button>
                @endif

                <!-- 4. Payroll / Salary -->
                <a 
                    href="{{ route('payroll.index') }}" 
                    class="flex flex-col items-center py-1 px-2.5 rounded-xl transition {{ request()->routeIs('payroll.*') ? 'text-[#1e3a8a] font-extrabold' : 'text-slate-400 hover:text-slate-700 font-medium' }}"
                >
                    <svg class="w-5 h-5 {{ request()->routeIs('payroll.*') ? 'text-[#1e3a8a]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="text-[10px] mt-0.5 tracking-tight">{{ auth()->user()->hasRole('super-admin') ? 'Payroll' : 'Salary' }}</span>
                </a>

                <!-- 5. Drawer Menu / More -->
                <button 
                    type="button" 
                    onclick="toggleMobileSidebar()" 
                    class="flex flex-col items-center py-1 px-2.5 rounded-xl text-slate-400 hover:text-slate-700 transition cursor-pointer"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <span class="text-[10px] mt-0.5 tracking-tight">Menu</span>
                </button>
            @endif
        </nav>
    </div>

    <!-- CHANGE PASSWORD MODAL -->
    <div id="changePasswordModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 {{ ($errors->has('current_password') || $errors->has('password')) ? '' : 'hidden' }} flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-md w-full p-6 sm:p-7 relative animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-blue-50 text-[#1e3a8a] flex items-center justify-center font-bold border border-blue-200 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 tracking-tight">Change Your Password</h3>
                        <p class="text-xs text-slate-500 font-medium">Update your account login credentials</p>
                    </div>
                </div>
                <button type="button" onclick="closeChangePasswordModal()" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-xl hover:bg-slate-100 transition cursor-pointer text-lg font-bold">
                    ✕
                </button>
            </div>

            <form action="{{ route('profile.password.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Current Password <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            name="current_password" 
                            id="modal_current_password" 
                            required 
                            autocomplete="current-password"
                            placeholder="Enter your current password"
                            class="w-full px-3.5 py-2.5 rounded-xl border {{ $errors->has('current_password') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-200 bg-slate-50/50' }} text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1e3a8a] focus:bg-white transition pr-10"
                        >
                        <button type="button" onclick="togglePasswordVisibility('modal_current_password')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                    @error('current_password')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        New Password <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            name="password" 
                            id="modal_new_password" 
                            required 
                            minlength="6"
                            autocomplete="new-password"
                            placeholder="Minimum 6 characters"
                            class="w-full px-3.5 py-2.5 rounded-xl border {{ $errors->has('password') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-200 bg-slate-50/50' }} text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1e3a8a] focus:bg-white transition pr-10"
                        >
                        <button type="button" onclick="togglePasswordVisibility('modal_new_password')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Confirm New Password <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            id="modal_password_confirmation" 
                            required 
                            minlength="6"
                            autocomplete="new-password"
                            placeholder="Re-enter new password"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1e3a8a] focus:bg-white transition pr-10"
                        >
                        <button type="button" onclick="togglePasswordVisibility('modal_password_confirmation')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button 
                        type="button" 
                        onclick="closeChangePasswordModal()" 
                        class="px-4 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold transition cursor-pointer"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        class="px-5 py-2.5 rounded-xl bg-[#1e3a8a] hover:bg-[#1e40af] text-white text-xs font-bold shadow-md hover:shadow-lg transition cursor-pointer"
                    >
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('sidebarNav');
            const overlay = document.getElementById('sidebarOverlay');
            if (!sidebar || !overlay) return;
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        }

        function openChangePasswordModal() {
            const modal = document.getElementById('changePasswordModal');
            if (modal) {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    const input = document.getElementById('modal_current_password');
                    if (input) input.focus();
                }, 50);
            }
        }

        function closeChangePasswordModal() {
            const modal = document.getElementById('changePasswordModal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }

        function togglePasswordVisibility(fieldId) {
            const input = document.getElementById(fieldId);
            if (input) {
                input.type = input.type === 'password' ? 'text' : 'password';
            }
        }

        function toggleProfilePopover(event) {
            if (event) {
                event.stopPropagation();
            }
            const popover = document.getElementById('profilePopover');
            const chevron = document.getElementById('profileChevron');
            if (!popover) return;
            const isHidden = popover.classList.contains('hidden');
            if (isHidden) {
                popover.classList.remove('hidden');
                if (chevron) chevron.classList.add('rotate-180');
            } else {
                popover.classList.add('hidden');
                if (chevron) chevron.classList.remove('rotate-180');
            }
        }

        function closeProfilePopover() {
            const popover = document.getElementById('profilePopover');
            const chevron = document.getElementById('profileChevron');
            if (popover) popover.classList.add('hidden');
            if (chevron) chevron.classList.remove('rotate-180');
        }

        // Close popover when clicking outside
        document.addEventListener('click', function(event) {
            const container = document.getElementById('profileDropdownContainer');
            if (container && !container.contains(event.target)) {
                closeProfilePopover();
            }
        });

        // Close modal and popovers on Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeChangePasswordModal();
                closeProfilePopover();
                closeQrScanner();
            }
        });
    </script>

    <!-- QR ATTENDANCE SCANNER MODAL -->
    <div id="qrScannerModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-xs z-50 hidden flex items-center justify-center p-3 sm:p-4">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-md w-full p-5 sm:p-6 relative animate-in fade-in zoom-in-95 duration-150">
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-blue-50 text-[#1e3a8a] flex items-center justify-center font-bold text-lg border border-blue-200 shadow-2xs">
                        📷
                    </div>
                    <div>
                        <h3 id="qrModalTitle" class="text-base font-extrabold text-slate-900 tracking-tight">Scan Daily QR Code</h3>
                        <p id="qrModalSubtitle" class="text-xs text-slate-500 font-medium">Point phone camera at office QR terminal</p>
                    </div>
                </div>
                <button type="button" onclick="closeQrScanner()" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-xl hover:bg-slate-100 transition cursor-pointer text-lg font-bold">
                    ✕
                </button>
            </div>

            <!-- Custom CSS for Video Containment & Scanner Laser -->
            <style>
                #qr-scanner-view {
                    width: 100% !important;
                    height: 100% !important;
                    position: relative;
                    overflow: hidden;
                    border: none !important;
                    background: transparent;
                }
                #qr-scanner-view video {
                    width: 100% !important;
                    height: 100% !important;
                    object-fit: cover !important;
                    border-radius: 1rem;
                }
                #qr-scanner-view img[alt="Info icon"],
                #qr-scanner-view a {
                    display: none !important;
                }
                @keyframes laserSweep {
                    0% { top: 12%; opacity: 0.9; }
                    50% { top: 86%; opacity: 1; }
                    100% { top: 12%; opacity: 0.9; }
                }
                .animate-laser {
                    animation: laserSweep 2s ease-in-out infinite;
                }
            </style>

            <!-- Live Camera Viewfinder Box -->
            <div id="qrViewfinderWrapper" class="relative bg-slate-900 rounded-2xl overflow-hidden aspect-square flex items-center justify-center border-2 border-slate-800 shadow-inner">
                <!-- Html5Qrcode Scanner Target -->
                <div id="qr-scanner-view" class="w-full h-full relative"></div>

                <!-- Laser Scan Line -->
                <div id="qrLaserLine" class="hidden absolute left-4 right-4 h-0.5 bg-gradient-to-r from-emerald-500 via-cyan-300 to-emerald-500 shadow-[0_0_12px_#10b981] animate-laser pointer-events-none z-10"></div>

                <!-- Target Corner Frame Guide -->
                <div id="qrTargetFrame" class="absolute inset-8 border border-white/20 rounded-2xl pointer-events-none hidden z-10">
                    <div class="absolute -top-1 -left-1 w-6 h-6 border-t-4 border-l-4 border-emerald-400 rounded-tl"></div>
                    <div class="absolute -top-1 -right-1 w-6 h-6 border-t-4 border-r-4 border-emerald-400 rounded-tr"></div>
                    <div class="absolute -bottom-1 -left-1 w-6 h-6 border-b-4 border-l-4 border-emerald-400 rounded-bl"></div>
                    <div class="absolute -bottom-1 -right-1 w-6 h-6 border-b-4 border-r-4 border-emerald-400 rounded-br"></div>
                </div>

                <!-- Permission / Standby Prompt -->
                <div id="qrPermissionPrompt" class="absolute inset-0 bg-slate-900 text-white flex flex-col items-center justify-center p-6 text-center z-15">
                    <div class="w-16 h-16 rounded-3xl bg-blue-900/60 text-blue-300 border border-blue-700/60 flex items-center justify-center text-3xl mb-3 shadow-lg">
                        📷
                    </div>
                    <h4 id="qrPromptTitle" class="text-sm font-extrabold text-white">Camera Access</h4>
                    <p id="qrPromptDesc" class="text-xs text-slate-300 mt-1 max-w-xs leading-relaxed">
                        Point camera at today's QR code on the office screen.
                    </p>
                    
                    <button type="button" onclick="startScannerEngine(true)" class="mt-4 px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-md flex items-center gap-2 cursor-pointer">
                        <span>🎥 Allow Camera & Scan</span>
                    </button>
                </div>

                <!-- Status Overlay (Verifying / Success / Error) -->
                <div id="qrStatusOverlay" class="absolute inset-0 bg-slate-950/90 text-white flex flex-col items-center justify-center p-6 text-center hidden z-20">
                    <div id="qrStatusIcon" class="w-16 h-16 rounded-2xl bg-emerald-500 text-white text-3xl flex items-center justify-center mb-3.5 font-bold shadow-lg">✓</div>
                    <h4 id="qrStatusTitle" class="text-lg font-extrabold text-white">Punch Verified!</h4>
                    <p id="qrStatusMessage" class="text-xs text-slate-300 mt-1 max-w-xs leading-relaxed">Attendance and location recorded successfully.</p>
                    <button type="button" id="qrRetryBtn" onclick="restartQrScanner()" class="mt-4 px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl hidden transition cursor-pointer shadow-md">
                        Scan Again 🔄
                    </button>
                </div>
            </div>

            <!-- Live Camera Diagnostic Status -->
            <div class="mt-3.5 flex items-center justify-between px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                <div class="flex items-center gap-2">
                    <span id="qrCameraDot" class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                    <span id="qrCameraText" class="font-bold text-slate-700">Camera: Starting...</span>
                </div>
                <button type="button" onclick="startScannerEngine(true)" class="text-[11px] font-bold text-[#1e3a8a] hover:underline cursor-pointer">
                    Re-check
                </button>
            </div>

            <!-- Action Controls (Pure Camera Only - No Upload Options) -->
            <div class="mt-3.5 pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                <button type="button" onclick="switchCamera()" class="px-4 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold flex items-center gap-1.5 transition cursor-pointer">
                    <span>🔄 Flip Camera</span>
                </button>
                <button type="button" onclick="closeQrScanner()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition cursor-pointer">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <script>
        let html5QrScanner = null;
        let currentPunchAction = 'auto';
        let currentGpsPosition = null;
        let isProcessingScan = false;
        let currentCameraFacing = "environment"; // "environment" or "user"
        let isScannerRunning = false;

        // Open QR Scanner Modal
        function openQrScanner(action = 'auto') {
            currentPunchAction = action;
            isProcessingScan = false;

            const modal = document.getElementById('qrScannerModal');
            const titleEl = document.getElementById('qrModalTitle');
            const subtitleEl = document.getElementById('qrModalSubtitle');
            const overlay = document.getElementById('qrStatusOverlay');
            const retryBtn = document.getElementById('qrRetryBtn');

            if (overlay) overlay.classList.add('hidden');
            if (retryBtn) retryBtn.classList.add('hidden');

            if (action === 'check_in') {
                if (titleEl) titleEl.innerText = 'Scan Daily QR to Check In';
                if (subtitleEl) subtitleEl.innerText = 'Point your camera at the office daily QR code';
            } else if (action === 'check_out') {
                if (titleEl) titleEl.innerText = 'Scan Daily QR to Check Out';
                if (subtitleEl) subtitleEl.innerText = 'Point your camera at the office daily QR code';
            } else {
                if (titleEl) titleEl.innerText = 'Scan Daily QR Code';
                if (subtitleEl) subtitleEl.innerText = 'Point your camera at the office daily QR code';
            }

            if (modal) modal.classList.remove('hidden');

            // Wait 80ms for modal DOM reflow and layout before requesting camera
            setTimeout(() => {
                startScannerEngine(true);
            }, 80);
        }

        // Acquire GPS Location smoothly in background
        function acquireGpsLocation() {
            if (!navigator.geolocation) return;
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    currentGpsPosition = position.coords;
                },
                (error) => {
                    console.warn("GPS acquire notice:", error);
                },
                { enableHighAccuracy: true, timeout: 6000, maximumAge: 300000 }
            );
        }

        // Start Scanner Engine
        async function startScannerEngine(userInitiated = false) {
            if (isProcessingScan) return;

            const camDot = document.getElementById('qrCameraDot');
            const camText = document.getElementById('qrCameraText');
            const promptBox = document.getElementById('qrPermissionPrompt');
            const promptDesc = document.getElementById('qrPromptDesc');
            const targetFrame = document.getElementById('qrTargetFrame');
            const laserLine = document.getElementById('qrLaserLine');

            if (camDot) camDot.className = 'w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse';
            if (camText) camText.innerText = 'Camera: Requesting...';

            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                if (camDot) camDot.className = 'w-2.5 h-2.5 rounded-full bg-rose-500';
                if (camText) camText.innerText = 'Camera: HTTPS Required';
                if (promptBox) {
                    promptBox.classList.remove('hidden');
                    if (promptDesc) {
                        promptDesc.innerHTML = '<span class="text-rose-400 font-bold">HTTPS Required</span><br><span class="text-[11px] text-slate-300">Mobile browsers require a secure HTTPS connection to access the camera.</span>';
                    }
                }
                return;
            }

            try {
                if (typeof Html5Qrcode === 'undefined') {
                    if (camText) camText.innerText = 'Camera: Loading scanner engine...';
                    await new Promise((resolve, reject) => {
                        const script = document.createElement('script');
                        script.src = 'https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js';
                        script.onload = () => resolve();
                        script.onerror = () => reject(new Error('Html5Qrcode library could not be loaded'));
                        document.head.appendChild(script);
                    });
                }

                // Cleanly release any existing scanner instance to prevent ongoing transition collisions
                if (html5QrScanner) {
                    try {
                        if (html5QrScanner.isScanning) {
                            await html5QrScanner.stop();
                        }
                        await html5QrScanner.clear();
                    } catch(e) {}
                    html5QrScanner = null;
                }

                // Reset container element DOM
                const viewEl = document.getElementById('qr-scanner-view');
                if (viewEl) viewEl.innerHTML = '';

                // Create fresh scanner instance
                html5QrScanner = new Html5Qrcode("qr-scanner-view");

                // Full-frame scanning without restrictively small or 0-dimension qrbox
                const config = {
                    fps: 20,
                    disableFlip: currentCameraFacing === "environment"
                };

                let started = false;

                // 1. Primary attempt: using facingMode (environment for rear camera)
                try {
                    await html5QrScanner.start(
                        { facingMode: currentCameraFacing },
                        config,
                        onScanSuccess,
                        () => {} // silent on frame decode misses
                    );
                    started = true;
                } catch (camErr) {
                    console.warn("Camera start with facingMode failed:", camErr);

                    // 2. Fallback attempt: recreate instance and try user/front camera
                    try {
                        if (html5QrScanner) {
                            try { await html5QrScanner.clear(); } catch(e) {}
                            html5QrScanner = null;
                        }
                        if (viewEl) viewEl.innerHTML = '';
                        html5QrScanner = new Html5Qrcode("qr-scanner-view");

                        currentCameraFacing = currentCameraFacing === "environment" ? "user" : "environment";
                        await html5QrScanner.start(
                            { facingMode: currentCameraFacing },
                            config,
                            onScanSuccess,
                            () => {}
                        );
                        started = true;
                    } catch (fallbackErr) {
                        console.warn("Camera fallback failed:", fallbackErr);
                        throw camErr; // Bubble up original error for detailed diagnostics
                    }
                }

                // Ensure video element plays inline on iOS Safari
                if (viewEl) {
                    const videoEl = viewEl.querySelector('video');
                    if (videoEl) {
                        videoEl.setAttribute('playsinline', 'true');
                        videoEl.setAttribute('webkit-playsinline', 'true');
                        videoEl.setAttribute('autoplay', 'true');
                        videoEl.setAttribute('muted', 'true');
                        videoEl.play().catch(() => {});
                    }
                }

                isScannerRunning = true;
                if (promptBox) promptBox.classList.add('hidden');
                if (targetFrame) targetFrame.classList.remove('hidden');
                if (laserLine) laserLine.classList.remove('hidden');
                if (camDot) camDot.className = 'w-2.5 h-2.5 rounded-full bg-emerald-500';
                if (camText) camText.innerText = 'Camera: Scanning Active';

                // Query GPS location in background without blocking camera UI
                setTimeout(acquireGpsLocation, 500);

            } catch (err) {
                console.error("Camera startup error:", err);
                isScannerRunning = false;
                if (promptBox) promptBox.classList.remove('hidden');
                if (promptDesc) {
                    const errName = err ? (err.name || '') : '';
                    const errMsg = err ? (err.message || String(err)) : '';

                    if (errName === 'NotAllowedError' || errMsg.includes('Permission') || errMsg.includes('NotAllowedError')) {
                        promptDesc.innerHTML = '<span class="text-rose-400 font-bold">Camera Access Denied</span><br><span class="text-[11px] text-slate-300 leading-relaxed mt-1 block">Safari has blocked camera access for this site.<br><br>👉 <strong>How to Enable in Safari:</strong><br>1. Tap the <strong>aA</strong> icon in your address bar<br>2. Tap <strong>Website Settings</strong><br>3. Set <strong>Camera</strong> to <strong>Allow</strong><br>4. Tap <strong>Allow Camera & Scan</strong> below.</span>';
                    } else {
                        promptDesc.innerHTML = `<span class="text-rose-400 font-bold">Camera Access Blocked</span><br><span class="text-[11px] text-slate-300 leading-relaxed mt-1 block">${errMsg ? errMsg : 'Tap the aA icon in your address bar ➔ Website Settings ➔ Camera: Allow, then tap button below.'}</span>`;
                    }
                }
                if (camDot) camDot.className = 'w-2.5 h-2.5 rounded-full bg-rose-500';
                if (camText) camText.innerText = 'Camera: Blocked';
            }
        }

        // Switch Camera (Rear <-> Front)
        async function switchCamera() {
            currentCameraFacing = currentCameraFacing === "environment" ? "user" : "environment";
            if (html5QrScanner && html5QrScanner.isScanning) {
                try {
                    await html5QrScanner.stop();
                    isScannerRunning = false;
                } catch(e) {}
            }
            await startScannerEngine(true);
        }

        // Close QR Scanner
        async function closeQrScanner() {
            const modal = document.getElementById('qrScannerModal');
            if (modal) modal.classList.add('hidden');

            if (html5QrScanner && html5QrScanner.isScanning) {
                try {
                    await html5QrScanner.stop();
                    await html5QrScanner.clear();
                } catch(e) {}
            }
            isScannerRunning = false;
            const targetFrame = document.getElementById('qrTargetFrame');
            const laserLine = document.getElementById('qrLaserLine');
            if (targetFrame) targetFrame.classList.add('hidden');
            if (laserLine) laserLine.classList.add('hidden');
            isProcessingScan = false;
        }

        // Restart Scanner after error
        function restartQrScanner() {
            const overlay = document.getElementById('qrStatusOverlay');
            if (overlay) overlay.classList.add('hidden');
            isProcessingScan = false;
            startScannerEngine(true);
        }

        // Handle Successful Scan
        function onScanSuccess(decodedText) {
            if (isProcessingScan) return;
            isProcessingScan = true;

            // Haptic vibration feedback
            if (navigator.vibrate) {
                try { navigator.vibrate([80, 40, 80]); } catch(e) {}
            }

            // Stop camera while processing
            if (html5QrScanner && html5QrScanner.isScanning) {
                try { html5QrScanner.stop().catch(() => {}); } catch(e) {}
            }

            // Show Verifying Overlay
            const overlay = document.getElementById('qrStatusOverlay');
            const iconEl = document.getElementById('qrStatusIcon');
            const titleEl = document.getElementById('qrStatusTitle');
            const msgEl = document.getElementById('qrStatusMessage');
            const retryBtn = document.getElementById('qrRetryBtn');

            if (overlay) overlay.classList.remove('hidden');
            if (retryBtn) retryBtn.classList.add('hidden');
            if (iconEl) {
                iconEl.className = 'w-16 h-16 rounded-2xl bg-[#1e3a8a] text-white text-3xl flex items-center justify-center mb-3.5 font-bold shadow-lg animate-spin';
                iconEl.innerText = '⏳';
            }
            if (titleEl) titleEl.innerText = 'Verifying Attendance...';
            if (msgEl) msgEl.innerText = 'Matching today\'s office QR signature and recording attendance...';

            // Ensure GPS is available or fallback fast
            if (currentGpsPosition) {
                submitQrPunch(decodedText, currentGpsPosition);
            } else if (navigator.geolocation) {
                let locationHandled = false;
                const gpsTimer = setTimeout(() => {
                    if (!locationHandled) {
                        locationHandled = true;
                        submitQrPunch(decodedText, { latitude: 0, longitude: 0, accuracy: null });
                    }
                }, 2000);

                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        if (!locationHandled) {
                            locationHandled = true;
                            clearTimeout(gpsTimer);
                            currentGpsPosition = pos.coords;
                            submitQrPunch(decodedText, pos.coords);
                        }
                    },
                    (err) => {
                        if (!locationHandled) {
                            locationHandled = true;
                            clearTimeout(gpsTimer);
                            submitQrPunch(decodedText, { latitude: 0, longitude: 0, accuracy: null });
                        }
                    },
                    { enableHighAccuracy: true, timeout: 2000, maximumAge: 60000 }
                );
            } else {
                submitQrPunch(decodedText, { latitude: 0, longitude: 0, accuracy: null });
            }
        }

        // Submit Punch to Server
        function submitQrPunch(qrToken, coords) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fetch("{{ route('attendance.qr-punch') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    qr_token: qrToken,
                    latitude: coords.latitude,
                    longitude: coords.longitude,
                    accuracy: coords.accuracy,
                    action: currentPunchAction,
                })
            })
            .then(res => res.json().then(data => ({ status: res.status, ok: res.ok, body: data })))
            .then(({ status, ok, body }) => {
                const iconEl = document.getElementById('qrStatusIcon');
                const titleEl = document.getElementById('qrStatusTitle');
                const msgEl = document.getElementById('qrStatusMessage');
                const retryBtn = document.getElementById('qrRetryBtn');

                if (ok && (body.status === 'success' || body.status === 'info')) {
                    if (iconEl) {
                        iconEl.className = 'w-16 h-16 rounded-2xl bg-emerald-500 text-white text-3xl flex items-center justify-center mb-3.5 font-bold shadow-lg';
                        iconEl.innerText = '✓';
                    }
                    if (titleEl) {
                        titleEl.innerText = body.action === 'CHECK_IN' ? 'Check-In Successful! 🟢' : (body.action === 'CHECK_OUT' ? 'Check-Out Successful! 🔴' : 'Attendance Verified!');
                    }
                    if (msgEl) {
                        msgEl.innerText = body.message;
                    }

                    // Reload page after 1.5s to reflect updated attendance status
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    if (iconEl) {
                        iconEl.className = 'w-16 h-16 rounded-2xl bg-rose-500 text-white text-3xl flex items-center justify-center mb-3.5 font-bold shadow-lg';
                        iconEl.innerText = '✕';
                    }
                    if (titleEl) titleEl.innerText = 'Verification Failed';
                    if (msgEl) msgEl.innerText = body.message || 'Could not verify attendance. Please try again.';
                    if (retryBtn) retryBtn.classList.remove('hidden');
                }
            })
            .catch(err => {
                const iconEl = document.getElementById('qrStatusIcon');
                const titleEl = document.getElementById('qrStatusTitle');
                const msgEl = document.getElementById('qrStatusMessage');
                const retryBtn = document.getElementById('qrRetryBtn');

                if (iconEl) {
                    iconEl.className = 'w-16 h-16 rounded-2xl bg-rose-500 text-white text-3xl flex items-center justify-center mb-3.5 font-bold shadow-lg';
                    iconEl.innerText = '✕';
                }
                if (titleEl) titleEl.innerText = 'Network Error';
                if (msgEl) msgEl.innerText = 'Failed to connect to the server. Please check your internet connection.';
                if (retryBtn) retryBtn.classList.remove('hidden');
            });
        }
    </script>
    @yield('scripts')
</body>
</html>
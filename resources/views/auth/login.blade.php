<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Syscorp HRM Portal — Sign in</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        mono: ['JetBrains Mono', 'ui-monospace', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50:  '#eff4ff',
                            100: '#dbe6fe',
                            200: '#bfd3fe',
                            600: '#2b3fd1',
                            700: '#2332b0',
                            800: '#1e2a8a',
                        },
                    },
                    boxShadow: {
                        card: '0 20px 50px -12px rgba(15, 23, 42, 0.18), 0 2px 6px rgba(15, 23, 42, 0.05)',
                    },
                },
            },
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }

        /*
         * Background image.
         * To use your own file, drop it in public/images/login-bg.jpg and replace the url() with:
         *   url('{{ asset('images/login-bg.jpg') }}')
         */
        .login-bg {
            background-color: #e8eefc;
            background-image:
                linear-gradient(180deg, rgba(248, 250, 252, 0.55) 0%, rgba(232, 238, 252, 0.80) 100%),
                url('https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1920&q=70');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        @media (max-width: 1023px) {
            .login-bg { background-attachment: scroll; }
        }
    </style>
</head>
<body class="login-bg min-h-screen text-slate-800 antialiased flex items-center justify-center p-4 sm:p-6 selection:bg-brand-100 selection:text-brand-800">

    <main class="w-full max-w-lg rounded-2xl bg-white/95 backdrop-blur-sm shadow-card border border-slate-200 overflow-hidden">

        <!-- Brand header -->
        <header class="flex items-center justify-between gap-3 border-b border-slate-200 bg-slate-50/90 px-6 py-4 sm:px-8">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-brand-800 flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div class="leading-tight">
                    <span class="block text-base font-bold text-slate-900">Syscorp</span>
                    <span class="block text-xs text-slate-500">HRM Portal</span>
                </div>
            </div>
            <span class="inline-flex items-center gap-1.5 rounded-md border border-emerald-200 bg-emerald-50 px-2 py-0.5 font-mono text-[11px] font-semibold text-emerald-700">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Live
            </span>
        </header>

        <!-- Right: form -->
        <section class="p-6 sm:p-8 bg-white">

            <div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Welcome back</h1>
                <p class="mt-1 text-sm text-slate-500">Sign in to the Employee Self-Service Portal.</p>
            </div>

            <!-- Quick access -->
            <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50/70 p-3.5">
                <div class="flex items-center justify-between mb-2.5">
                    <span class="text-xs font-semibold text-slate-700">Quick access</span>
                    <span class="font-mono text-[11px] text-slate-400">Demo accounts</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <button type="button" onclick="fillCredentials('admin@hrm.test', 'password', this)"
                            class="role-btn group rounded-lg border border-slate-200 bg-white px-3 py-2 text-left transition hover:border-brand-600 hover:bg-brand-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-600">
                        <span class="block text-sm font-semibold text-slate-900 group-hover:text-brand-800">Super Admin</span>
                        <span class="block mt-0.5 truncate font-mono text-[11px] text-slate-500">admin@hrm.test</span>
                    </button>
                    <button type="button" onclick="fillCredentials('hr@hrm.test', 'password', this)"
                            class="role-btn group rounded-lg border border-slate-200 bg-white px-3 py-2 text-left transition hover:border-brand-600 hover:bg-brand-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-600">
                        <span class="block text-sm font-semibold text-slate-900 group-hover:text-brand-800">HR Manager</span>
                        <span class="block mt-0.5 truncate font-mono text-[11px] text-slate-500">hr@hrm.test</span>
                    </button>
                    <button type="button" onclick="fillCredentials('employee@hrm.test', 'password', this)"
                            class="role-btn group rounded-lg border border-slate-200 bg-white px-3 py-2 text-left transition hover:border-brand-600 hover:bg-brand-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-600">
                        <span class="block text-sm font-semibold text-slate-900 group-hover:text-brand-800">Staff Employee</span>
                        <span class="block mt-0.5 truncate font-mono text-[11px] text-slate-500">employee@hrm.test</span>
                    </button>
                </div>
            </div>

            <!-- Login form -->
            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Work email</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </span>
                        <input type="email" name="email" id="email"
                               value="{{ old('email', 'admin@hrm.test') }}"
                               required autofocus autocomplete="username"
                               placeholder="name@company.com"
                               class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-900 placeholder-slate-400 transition focus:border-brand-600 focus:ring-2 focus:ring-brand-600/30">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </span>
                        <input type="password" name="password" id="password"
                               value="password"
                               required autocomplete="current-password"
                               placeholder="••••••••"
                               class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-10 pr-11 text-sm text-slate-900 placeholder-slate-400 transition focus:border-brand-600 focus:ring-2 focus:ring-brand-600/30">
                        <button type="button" id="togglePassword" aria-label="Show password"
                                class="absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-400 hover:text-slate-600 focus:outline-none focus-visible:text-brand-600">
                            <svg id="eyeOn" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg id="eyeOff" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex cursor-pointer items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="remember"
                               class="h-4 w-4 rounded border-slate-300 text-brand-700 focus:ring-brand-600">
                        Keep me signed in
                    </label>
                </div>

                <!-- Submit -->
                <button type="submit"
                        class="mt-2 flex w-full items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 active:scale-[0.99] focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2">
                    Sign in
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </form>
        </section>

        <footer class="flex items-center justify-between border-t border-slate-200 bg-slate-50/90 px-6 py-3 text-xs text-slate-400 sm:px-8">
            <span>&copy; {{ date('Y') }} Syscorp Enterprise</span>
            <span class="inline-flex items-center gap-1.5 font-mono text-[11px] text-slate-500">
                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Secure
            </span>
        </footer>
    </main>

    <script>
        function fillCredentials(email, password, btn) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;

            document.querySelectorAll('.role-btn').forEach(function (b) {
                b.classList.remove('border-brand-600', 'bg-brand-50');
            });
            if (btn) btn.classList.add('border-brand-600', 'bg-brand-50');
        }

        (function () {
            var input = document.getElementById('password');
            var toggle = document.getElementById('togglePassword');
            var on = document.getElementById('eyeOn');
            var off = document.getElementById('eyeOff');
            toggle.addEventListener('click', function () {
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                on.classList.toggle('hidden', show);
                off.classList.toggle('hidden', !show);
                toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
            });
        })();
    </script>
</body>
</html>
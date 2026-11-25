<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Attendance QR Kiosk — Syscorp HRM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        brand: {
                            DEFAULT: '#1e3a8a',
                            dark: '#0f172a',
                            light: '#3b82f6',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @media print {
            body { background: white !important; color: black !important; }
            .no-print { display: none !important; }
            .print-card { box-shadow: none !important; border: 2px solid #000 !important; }
        }
    </style>
</head>
<body class="h-full bg-slate-950 text-slate-100 flex flex-col justify-between font-sans selection:bg-[#1e3a8a] selection:text-white">

    <!-- Top Bar -->
    <header class="p-6 sm:p-8 flex items-center justify-between no-print">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-[#1e3a8a] text-white flex items-center justify-center font-black text-xl shadow-lg border border-blue-400/30">
                ⚡
            </div>
            <div>
                <h1 class="text-lg font-black tracking-tight text-white flex items-center gap-2">
                    Syscorp <span class="text-blue-400 font-extrabold text-xs uppercase tracking-wider bg-blue-950/80 px-2 py-0.5 rounded-md border border-blue-800/60">HRM Kiosk</span>
                </h1>
                <p class="text-xs text-slate-400 font-medium">Smart QR Code Terminal</p>
            </div>
        </div>

        <div class="flex items-center gap-2.5 sm:gap-3 flex-wrap">
            @if(auth()->user() && (auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('hr-manager')))
                <form method="POST" action="{{ route('attendance.qr.regenerate') }}" onsubmit="return confirm('Generate a new QR code for today? The previous code will be immediately revoked.')">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-xs font-bold text-white transition flex items-center gap-1.5 cursor-pointer shadow-md" title="Revoke current QR and generate a new one">
                        <span>🔄 Generate New QR</span>
                    </button>
                </form>
            @endif

            <button type="button" onclick="window.print()" class="px-4 py-2 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-xs font-bold text-slate-200 border border-slate-700/80 transition flex items-center gap-1.5 cursor-pointer">
                <span>🖨️ Print Code</span>
            </button>
            <button type="button" onclick="toggleFullscreen()" class="px-4 py-2 rounded-xl bg-blue-900/40 hover:bg-blue-800/60 text-xs font-bold text-blue-300 border border-blue-700/50 transition flex items-center gap-1.5 cursor-pointer">
                <span>⛶ Fullscreen</span>
            </button>
            <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-xl bg-slate-800/60 hover:bg-slate-700 text-xs font-bold text-slate-300 transition">
                ← Exit
            </a>
        </div>
    </header>

    <!-- Main QR Center Stage -->
    <main class="flex-1 flex flex-col items-center justify-center p-6 text-center max-w-4xl mx-auto w-full">
        
        <!-- Live Clock & Date -->
        <div class="mb-6">
            <div id="liveClock" class="font-mono text-5xl sm:text-6xl font-black tracking-tight text-white drop-shadow-md">
                {{ now()->format('h:i:s A') }}
            </div>
            <div class="text-sm sm:text-base font-semibold text-slate-400 mt-1">
                {{ now()->format('l, F j, Y') }}
            </div>
        </div>

        <!-- The Daily QR Container Card -->
        <div class="bg-white rounded-3xl p-7 sm:p-9 shadow-2xl border-4 border-blue-500/30 relative print-card max-w-md w-full animate-in fade-in zoom-in-95 duration-200">
            <!-- Pulsing Active Beacon -->
            <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 px-4 py-1 rounded-full bg-[#1e3a8a] text-white text-[11px] font-extrabold uppercase tracking-widest shadow-md flex items-center gap-1.5 border border-blue-300/40 whitespace-nowrap">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                <span>Active Daily QR (v{{ $qrRecord->version ?? 1 }}) &bull; {{ $today }}</span>
            </div>

            <!-- QR Code Graphic -->
            <div class="w-full aspect-square flex items-center justify-center p-2 rounded-2xl bg-white border border-slate-100">
                <div class="w-full h-full flex items-center justify-center [&>svg]:w-full [&>svg]:h-full [&>svg]:max-w-[280px]">
                    {!! $qrSvg !!}
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-slate-100">
                <p class="text-xs font-extrabold text-slate-800 tracking-tight">Scan with your phone to punch In / Out</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Captures your exact punch timestamp & verified office GPS</p>
            </div>
        </div>

        <!-- Expiration & Countdown Timer Banner -->
        <div class="mt-5 flex flex-wrap items-center justify-center gap-2 text-xs font-semibold text-slate-400 no-print">
            <span class="inline-flex items-center gap-1.5 bg-slate-900 px-3 py-1.5 rounded-full border border-slate-800">
                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                <span>Expires at <strong>12:00 AM (midnight)</strong> &bull; New code auto-generates for next day</span>
            </span>
            <span class="inline-flex items-center gap-1.5 bg-blue-950/70 text-blue-300 px-3 py-1.5 rounded-full border border-blue-800/60 font-mono">
                <span>Expires in:</span>
                <strong id="midnightCountdown">--:--:--</strong>
            </span>
        </div>

        <!-- Step Instructions -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 mt-7 max-w-2xl w-full no-print">
            <div class="p-3.5 rounded-2xl bg-slate-900/80 border border-slate-800 text-left">
                <span class="w-6 h-6 rounded-lg bg-blue-900/60 text-blue-300 font-extrabold text-xs flex items-center justify-center mb-1.5">1</span>
                <h4 class="text-xs font-bold text-slate-200">Open HRM Portal</h4>
                <p class="text-[11px] text-slate-400 mt-0.5">Log in on your mobile phone browser.</p>
            </div>
            <div class="p-3.5 rounded-2xl bg-slate-900/80 border border-slate-800 text-left">
                <span class="w-6 h-6 rounded-lg bg-blue-900/60 text-blue-300 font-extrabold text-xs flex items-center justify-center mb-1.5">2</span>
                <h4 class="text-xs font-bold text-slate-200">Tap Punch In / Out</h4>
                <p class="text-[11px] text-slate-400 mt-0.5">Your phone camera automatically opens.</p>
            </div>
            <div class="p-3.5 rounded-2xl bg-slate-900/80 border border-slate-800 text-left">
                <span class="w-6 h-6 rounded-lg bg-blue-900/60 text-blue-300 font-extrabold text-xs flex items-center justify-center mb-1.5">3</span>
                <h4 class="text-xs font-bold text-slate-200">Scan & Verify</h4>
                <p class="text-[11px] text-slate-400 mt-0.5">GPS location and time recorded instantly.</p>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="p-6 text-center text-xs text-slate-500 border-t border-slate-900 flex flex-col sm:flex-row items-center justify-between gap-3 no-print max-w-5xl mx-auto w-full">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <span>Dedicated Terminal API: <code class="text-blue-400 font-mono text-[11px] bg-slate-900 px-1.5 py-0.5 rounded">GET /api/attendance/daily-qr</code></span>
        </div>
        <div class="text-[11px]">
            Security: Database-tracked &bull; Daily midnight expiration (12:00 AM) &bull; HR on-demand regeneration
        </div>
    </footer>

    <script>
        // Live Clock
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
            const clockEl = document.getElementById('liveClock');
            if (clockEl) clockEl.innerText = timeStr;

            // Update midnight countdown
            const midnight = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1, 0, 0, 0);
            const diffMs = midnight.getTime() - now.getTime();

            if (diffMs <= 0) {
                // Midnight reached: reload to display the next day's new QR code!
                window.location.reload();
                return;
            }

            const hours = Math.floor(diffMs / (1000 * 60 * 60));
            const minutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diffMs % (1000 * 60)) / 1000);

            const pad = (n) => String(n).padStart(2, '0');
            const countdownEl = document.getElementById('midnightCountdown');
            if (countdownEl) {
                countdownEl.innerText = `${pad(hours)}h ${pad(minutes)}m ${pad(seconds)}s`;
            }
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Fullscreen toggle
        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => alert(err.message));
            } else {
                document.exitFullscreen();
            }
        }
    </script>
</body>
</html>

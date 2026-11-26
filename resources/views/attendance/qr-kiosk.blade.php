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

    <!-- Real-Time Attendance Celebration Modal / Banner -->
    <div id="attendanceCelebrationModal" class="fixed inset-x-4 top-6 sm:top-8 max-w-lg mx-auto z-50 transition-all duration-500 transform -translate-y-36 opacity-0 pointer-events-none">
        <div class="relative bg-slate-900/95 backdrop-blur-xl border-2 border-emerald-500/80 rounded-3xl p-5 sm:p-6 shadow-[0_20px_50px_rgba(16,185,129,0.35)] flex items-center gap-4 text-left">
            <!-- Pulsing Avatar / Success Badge -->
            <div id="celebAvatar" class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-600 text-white font-black text-2xl flex items-center justify-center shadow-lg shrink-0 border-2 border-emerald-300">
                ✓
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    <span id="celebBadge" class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-widest bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                        Check-In Verified ✓
                    </span>
                    <span id="celebTime" class="text-xs font-mono text-slate-400 font-semibold">
                        09:30 AM
                    </span>
                </div>
                <h3 id="celebTitle" class="text-lg sm:text-xl font-black text-white tracking-tight truncate">
                    Thamizh, your attendance MARKED!
                </h3>
                <p id="celebSub" class="text-xs text-slate-300 font-medium mt-0.5 truncate">
                    Staff &bull; Office Verified
                </p>
            </div>
            <!-- Animated Party Popper -->
            <div class="text-3xl animate-bounce shrink-0">
                🎉
            </div>
        </div>
    </div>

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
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold font-mono bg-emerald-950/80 text-emerald-300 border border-emerald-800/60">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                <span id="liveFeedStatus">Live WebSocket Stream</span>
            </span>
            <button type="button" onclick="toggleFullscreen()" class="p-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-300 text-xs font-bold border border-slate-800 transition" title="Toggle Fullscreen">
                ⛶
            </button>
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
            <div id="qrVersionBadge" class="absolute -top-3.5 left-1/2 -translate-x-1/2 px-4 py-1 rounded-full bg-[#1e3a8a] text-white text-[11px] font-extrabold uppercase tracking-widest shadow-md flex items-center gap-1.5 border border-blue-300/40 whitespace-nowrap">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                <span>Active Daily QR (v{{ $qrRecord->version ?? 1 }}) &bull; {{ $today }}</span>
            </div>

            <!-- QR Code Graphic -->
            <div class="w-full aspect-square flex items-center justify-center p-2 rounded-2xl bg-white border border-slate-100">
                <div id="qrSvgContainer" class="w-full h-full flex items-center justify-center [&>svg]:w-full [&>svg]:h-full [&>svg]:max-w-[280px] transition-all duration-300">
                    {!! $qrSvg !!}
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-slate-100">
                <p class="text-xs font-extrabold text-slate-800 tracking-tight">Scan with your phone to punch In / Out</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Captures your exact punch timestamp & verified office GPS</p>
            </div>
        </div>

        <!-- Live Real-Time Activity Feed -->
        <div class="mt-6 max-w-md w-full no-print">
            <div class="flex items-center justify-between text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2 px-1">
                <span class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span>Live Office Punches</span>
                </span>
                <span class="text-slate-500 font-mono text-[10px]">Real-time Auto-Update</span>
            </div>
            <div id="livePunchesList" class="space-y-1.5">
                <div id="emptyFeedNotice" class="p-3 rounded-2xl bg-slate-900/60 border border-slate-800 text-slate-400 text-xs">
                    Terminal active. Waiting for staff punch...
                </div>
            </div>
        </div>
    </main>

    <script>
        // Web Audio Synthesizer for celebration chime
        function playChime() {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const ctx = new AudioCtx();
                if (ctx.state === 'suspended') ctx.resume();

                // Two ascending pleasant harmonic tones
                const notes = [659.25, 783.99];
                notes.forEach((freq, idx) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, ctx.currentTime + idx * 0.12);
                    gain.gain.setValueAtTime(0.3, ctx.currentTime + idx * 0.12);
                    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + idx * 0.12 + 0.35);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(ctx.currentTime + idx * 0.12);
                    osc.stop(ctx.currentTime + idx * 0.12 + 0.36);
                });
            } catch (e) {}
        }

        // Live Celebration Popup
        let modalTimeout = null;
        function showCelebrationPopup(punch) {
            playChime();

            // Rotate on-screen QR immediately if attached to punch payload
            if (punch.active_qr) {
                updateKioskQr(punch.active_qr);
            }

            const modal = document.getElementById('attendanceCelebrationModal');
            const title = document.getElementById('celebTitle');
            const sub = document.getElementById('celebSub');
            const badge = document.getElementById('celebBadge');
            const timeEl = document.getElementById('celebTime');
            const avatar = document.getElementById('celebAvatar');

            if (!modal) return;

            title.textContent = (punch.first_name || 'Staff') + ', your attendance MARKED!';
            sub.textContent = (punch.employee_code ? punch.employee_code + ' • ' : '') + (punch.department || 'Office Staff');
            timeEl.textContent = punch.time;
            avatar.textContent = punch.avatar_char || '✓';

            if (punch.event_type === 'CHECK_IN') {
                badge.textContent = 'Check-In Verified ✓';
                badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-widest bg-emerald-500/20 text-emerald-300 border border-emerald-400/30';
                avatar.className = 'w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-600 text-white font-black text-2xl flex items-center justify-center shadow-lg shrink-0 border-2 border-emerald-300';
            } else {
                badge.textContent = 'Check-Out Recorded 👋';
                badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-widest bg-blue-500/20 text-blue-300 border border-blue-400/30';
                avatar.className = 'w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-400 to-indigo-600 text-white font-black text-2xl flex items-center justify-center shadow-lg shrink-0 border-2 border-blue-300';
            }

            modal.classList.remove('-translate-y-36', 'opacity-0', 'pointer-events-none');
            modal.classList.add('translate-y-0', 'opacity-100');

            addPunchToFeed(punch);

            if (modalTimeout) clearTimeout(modalTimeout);
            modalTimeout = setTimeout(() => {
                modal.classList.remove('translate-y-0', 'opacity-100');
                modal.classList.add('-translate-y-36', 'opacity-0', 'pointer-events-none');
            }, 6000);
        }

        // Live Feed Ticker
        function addPunchToFeed(punch) {
            const list = document.getElementById('livePunchesList');
            const emptyNotice = document.getElementById('emptyFeedNotice');
            if (emptyNotice) emptyNotice.remove();
            if (!list) return;

            const isCheckIn = punch.event_type === 'CHECK_IN';
            const item = document.createElement('div');
            item.className = 'p-2.5 rounded-2xl bg-slate-900/90 border border-slate-800 flex items-center justify-between text-xs animate-in fade-in slide-in-from-top-2 duration-300';
            item.innerHTML = `
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="w-6 h-6 rounded-lg ${isCheckIn ? 'bg-emerald-950 text-emerald-300 border border-emerald-800' : 'bg-blue-950 text-blue-300 border border-blue-800'} font-black text-[11px] flex items-center justify-center shrink-0">
                        ${punch.avatar_char || '✓'}
                    </span>
                    <div class="truncate">
                        <span class="font-extrabold text-white">${punch.first_name}</span>
                        <span class="text-slate-400 text-[11px] ml-1">${punch.type_label || 'Punched'}</span>
                    </div>
                </div>
                <div class="text-right shrink-0 font-mono text-[11px] text-slate-400">
                    ${punch.time}
                </div>
            `;

            list.insertBefore(item, list.firstChild);
            while (list.children.length > 4) {
                list.removeChild(list.lastChild);
            }
        }

        // Real-Time Connection (SSE stream + fallback poller)
        let lastKnownPunchId = {{ \App\Models\AttendanceEvent::whereDate('recorded_at', now()->toDateString())->max('id') ?? 0 }};
        let sse = null;

        function initRealtimeStream() {
            if (window.EventSource) {
                try {
                    sse = new EventSource('/api/attendance/kiosk-stream?since_id=' + lastKnownPunchId);

                    sse.addEventListener('punch', function(e) {
                        try {
                            const punch = JSON.parse(e.data);
                            if (punch && punch.id > lastKnownPunchId) {
                                lastKnownPunchId = punch.id;
                                showCelebrationPopup(punch);
                            }
                        } catch (err) {
                            console.error('Error parsing punch event', err);
                        }
                    });

                    sse.onopen = function() {
                        const statusEl = document.getElementById('liveFeedStatus');
                        if (statusEl) statusEl.textContent = 'Live Real-Time Active ⚡';
                    };

                    sse.onerror = function() {
                        if (sse) sse.close();
                        setTimeout(initRealtimeStream, 4000);
                    };
                } catch (e) {
                    // Fall back to periodic polling
                }
            }
        }

        // Live Dynamic Single-Use QR Rotation Handler
        function updateKioskQr(activeQr) {
            if (!activeQr || !activeQr.qr_svg) return;
            const container = document.getElementById('qrSvgContainer');
            const badge = document.getElementById('qrVersionBadge');

            if (container && activeQr.qr_svg) {
                container.style.opacity = '0.2';
                container.style.transform = 'scale(0.92)';
                setTimeout(() => {
                    container.innerHTML = activeQr.qr_svg;
                    container.style.opacity = '1';
                    container.style.transform = 'scale(1)';
                }, 180);
            }

            if (badge && activeQr.version) {
                badge.innerHTML = `<span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span><span>Active Daily QR (v${activeQr.version}) &bull; ${activeQr.date || '{{ $today }}'}</span>`;
            }
        }

        // Background poller (ensures instant sub-millisecond delivery on all networks)
        let isFetchingPunches = false;
        async function fetchRecentPunches() {
            if (isFetchingPunches) return;
            isFetchingPunches = true;
            try {
                const res = await fetch('/api/attendance/recent-punches?since_id=' + lastKnownPunchId);
                if (!res.ok) return;
                const json = await res.json();

                if (json && json.active_qr) {
                    updateKioskQr(json.active_qr);
                }

                if (json && json.punches && json.punches.length > 0) {
                    // Process oldest to newest
                    json.punches.reverse().forEach(punch => {
                        if (punch.id > lastKnownPunchId) {
                            lastKnownPunchId = punch.id;
                            showCelebrationPopup(punch);
                        }
                    });
                }
            } catch (err) {
            } finally {
                isFetchingPunches = false;
            }
        }

        setInterval(fetchRecentPunches, 2500);
        initRealtimeStream();

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

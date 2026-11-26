@extends('layouts.app')

@section('title', 'Company Announcements')
@section('page_title', 'Corporate Announcements & Workforce Broadcasts')

@section('content')
<div class="space-y-6">
    <!-- Category Filter Bar -->
    <div class="flex flex-wrap items-center gap-2">
        <a 
            href="{{ route('announcements.index') }}" 
            class="px-4 py-2 rounded-xl text-xs font-bold transition {{ !$categoryFilter ? 'bg-[#1e3a8a] text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}"
        >
            All Announcements
        </a>
        @foreach(['POLICY' => 'Policies', 'URGENT' => 'Urgent', 'EVENT' => 'Events', 'HOLIDAY' => 'Holidays', 'GENERAL' => 'General'] as $catKey => $catLabel)
            <a 
                href="{{ route('announcements.index', ['category' => $catKey]) }}" 
                class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $categoryFilter === $catKey ? 'bg-[#1e3a8a] text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}"
            >
                {{ $catLabel }}
            </a>
        @endforeach
    </div>

    <!-- Announcements Feed -->
    <div class="space-y-4">
        @forelse($announcements as $ann)
            <div class="bg-white rounded-2xl border {{ $ann->is_pinned ? 'border-[#1e3a8a] shadow-md ring-1 ring-[#1e3a8a]/30' : 'border-slate-100 shadow-md' }} p-6 transition hover:border-[#1e3a8a]">
                
                <!-- Card Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4 mb-4">
                    <div class="flex flex-wrap items-center gap-2">
                        @if($ann->is_pinned)
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-[#1e1b4b] text-white flex items-center gap-1 shadow-xs">
                                📌 PINNED
                            </span>
                        @endif

                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold 
                            {{ $ann->category === 'URGENT' ? 'bg-rose-50 text-rose-800 border border-rose-200' : '' }}
                            {{ $ann->category === 'POLICY' ? 'bg-[#eff6ff] text-[#1e3a8a] border border-[#bfdbfe]' : '' }}
                            {{ $ann->category === 'EVENT' ? 'bg-amber-50 text-amber-800 border border-amber-200' : '' }}
                            {{ $ann->category === 'HOLIDAY' ? 'bg-amber-100 text-amber-900 border border-amber-300' : '' }}
                            {{ $ann->category === 'GENERAL' ? 'bg-slate-100 text-slate-800 border border-slate-200' : '' }}
                        ">
                            {{ $ann->category }}
                        </span>

                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase
                            {{ $ann->priority === 'URGENT' || $ann->priority === 'HIGH' ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-600' }}
                        ">
                            Priority: {{ $ann->priority }}
                        </span>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="text-xs text-slate-400 font-mono">
                            {{ $ann->created_at->format('M d, Y • h:i A') }} ({{ $ann->created_at->diffForHumans() }})
                        </span>

                        @if($isSuperAdmin || $isHr)
                            <form method="POST" action="{{ route('announcements.destroy', $ann) }}" onsubmit="return confirm('Are you sure you want to delete this announcement?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-rose-600 hover:text-rose-800 font-bold p-1 rounded hover:bg-rose-50 transition" title="Delete Announcement">
                                    🗑️
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Announcement Body -->
                <div>
                    <h3 class="text-base sm:text-lg font-extrabold text-slate-900 mb-2.5">
                        {{ $ann->title }}
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                        {{ $ann->content }}
                    </p>
                </div>

                <!-- Footer: Author Meta -->
                <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-[#eff6ff] text-[#1e3a8a] font-extrabold text-xs flex items-center justify-center border border-[#bfdbfe]">
                            {{ strtoupper(substr($ann->author->name ?? 'HR', 0, 1)) }}
                        </div>
                        <span class="font-bold text-slate-800">{{ $ann->author->name ?? 'People Operations' }}</span>
                        <span class="text-slate-400">&bull; Authorized Bulletin</span>
                    </div>

                    <span class="text-[11px] text-[#1e3a8a] font-semibold flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Sent to all employees
                    </span>
                </div>

            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-100 p-12 text-center shadow-md">
                <div class="w-12 h-12 mx-auto rounded-2xl bg-[#eff6ff] text-[#1e3a8a] flex items-center justify-center font-bold text-xl mb-3">
                    📢
                </div>
                <h3 class="text-sm font-bold text-slate-900">No Announcements Found</h3>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                    There are no bulletins currently published under this category. Check back soon for company updates.
                </p>
            </div>
        @endforelse

        <!-- Pagination -->
        @if($announcements->hasPages())
            <div class="pt-4">
                {{ $announcements->links() }}
            </div>
        @endif
    </div>

</div>

<!-- PUBLISH ANNOUNCEMENT MODAL (HR & SUPER ADMIN) -->
@if($isSuperAdmin || $isHr)
    <div id="announcementModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Broadcast New Announcement</h3>
                    <p class="text-xs text-slate-400">Notice will be broadcasted to all company staff accounts</p>
                </div>
                <button type="button" onclick="closeAnnouncementModal()" class="text-slate-400 hover:text-slate-700 text-lg font-bold p-1">
                    &times;
                </button>
            </div>

            <form action="{{ route('announcements.store') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Title -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Announcement Title</label>
                    <input 
                        type="text" 
                        name="title" 
                        required 
                        placeholder="e.g. Annual Company Townhall 2026" 
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white font-medium text-slate-900"
                    >
                </div>

                <!-- Category & Priority Row -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Category</label>
                        <select name="category" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900">
                            <option value="GENERAL">General Notice</option>
                            <option value="POLICY">Company Policy</option>
                            <option value="EVENT">Event & Townhall</option>
                            <option value="HOLIDAY">Holiday Bulletin</option>
                            <option value="URGENT">Urgent Alert</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Priority</label>
                        <select name="priority" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900">
                            <option value="MEDIUM">Medium Priority</option>
                            <option value="HIGH">High Priority</option>
                            <option value="URGENT">Urgent</option>
                            <option value="LOW">Low</option>
                        </select>
                    </div>
                </div>

                <!-- Content -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Announcement Message</label>
                    <textarea 
                        name="content" 
                        rows="4" 
                        required 
                        placeholder="Write your official message to the workforce..." 
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900"
                    ></textarea>
                </div>

                <!-- Pin Option -->
                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="is_pinned" id="is_pinned" value="1" class="rounded text-[#1e3a8a] focus:ring-[#1e3a8a] border-slate-300">
                    <label for="is_pinned" class="text-xs font-semibold text-slate-700 cursor-pointer">
                        Pin this announcement to the top of the feed
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="closeAnnouncementModal()" class="px-4 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#1e3a8a] hover:bg-[#1e40af] text-white text-xs font-extrabold shadow-sm transition flex items-center gap-2">
                        <span>Broadcast to All</span>
                        <span>→</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
@endsection

@section('scripts')
<script>
    function openAnnouncementModal() {
        const modal = document.getElementById('announcementModal');
        if (modal) modal.classList.remove('hidden');
    }

    function closeAnnouncementModal() {
        const modal = document.getElementById('announcementModal');
        if (modal) modal.classList.add('hidden');
    }
</script>
@endsection

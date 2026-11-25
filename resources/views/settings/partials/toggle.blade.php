<label class="inline-flex items-center gap-2 cursor-pointer select-none">
    <span class="relative inline-flex h-5 w-9 shrink-0">
        <input type="hidden" name="{{ $name }}" value="0">
        <input type="checkbox" name="{{ $name }}" value="1" class="peer sr-only" @checked($on)>
        <span class="block h-5 w-9 rounded-full bg-slate-200 transition peer-checked:bg-[#1e3a8a]"></span>
        <span class="pointer-events-none absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white shadow transition peer-checked:translate-x-4"></span>
    </span>
    @if(($label ?? '') !== '')
        <span class="text-[11px] font-bold text-slate-500 whitespace-nowrap">{{ $label }}</span>
    @endif
</label>

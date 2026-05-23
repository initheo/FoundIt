<!-- Admin Footer -->
<footer class="mt-auto py-6 px-8 border-t border-slate-100 bg-white/50 backdrop-blur-sm">
    <div class="flex flex-col md:flex-row items-center justify-between gap-4 text-center md:text-left">
        <!-- Brand & Info -->
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-white flex items-center justify-center p-1 overflow-hidden shadow-sm border border-slate-100">
                <img src="{{ asset('logo.png') }}" class="w-full h-full object-contain" alt="FoundIt Logo">
            </div>
            <div class="flex flex-col">
                <span class="text-xs font-black text-slate-800 tracking-tight leading-none">FoundIt</span>
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-1">Admin Control Panel</span>
            </div>
        </div>
        
        <!-- Copyright & Credits -->
        <div class="flex flex-col md:items-end gap-1.5">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest leading-none">
                &copy; {{ date('Y') }} FoundIt Team. <span class="hidden md:inline">All rights reserved.</span>
            </p>
            <div class="flex items-center justify-center md:justify-end gap-3 text-[9px] font-bold tracking-tighter text-slate-400 uppercase">
                <span class="flex items-center gap-1.5 py-0.5 px-2 bg-slate-100 rounded-full text-slate-500">
                    <i data-lucide="cpu" class="w-3 h-3 text-red-500"></i> v1.0.0
                </span>
                <div class="hidden md:block w-1 h-1 bg-slate-200 rounded-full"></div>
                <span class="flex items-center gap-1.5">
                    Crafted with <i data-lucide="heart" class="w-3 h-3 text-rose-500 fill-rose-500 animate-pulse"></i> for Lost & Found
                </span>
            </div>
        </div>
    </div>
</footer>

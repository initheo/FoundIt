<!-- Top Bar -->
<header class="topbar px-6">
    <!-- Left: Sidebar Toggle -->
    <div class="flex items-center gap-4 flex-1">
        <button class="nav-icon-btn lg:hidden" 
                @click="sidebarOpen = !sidebarOpen"
                aria-label="Toggle sidebar">
            <i data-lucide="menu" class="w-6 h-6"></i>
        </button>

        <!-- Realtime Clock (Desktop) -->
        <div x-data="{ 
                currentDateTime: '',
                updateDateTime() {
                    const now = new Date();
                    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    
                    const day = days[now.getDay()];
                    const date = now.getDate();
                    const month = months[now.getMonth()];
                    const year = now.getFullYear();
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    const seconds = String(now.getSeconds()).padStart(2, '0');
                    
                    this.currentDateTime = `${day}, ${date} ${month} ${year} • ${hours}:${minutes}:${seconds}`;
                }
             }" 
             x-init="updateDateTime(); setInterval(() => updateDateTime(), 1000)" 
             class="hidden lg:flex items-center gap-2.5 text-slate-400 bg-slate-50/50 px-4 py-2 rounded-xl border border-slate-100/50">
            <i data-lucide="calendar-days" class="w-4 h-4 text-red-500"></i>
            <span class="text-[11px] font-bold uppercase tracking-widest text-slate-500" x-text="currentDateTime"></span>
        </div>
    </div>

    <!-- Right: User Dropdown -->
    <div class="flex items-center gap-4">
        @auth
        <!-- User Dropdown -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" 
                    @click.outside="open = false"
                    class="flex items-center gap-3 p-1 rounded-xl hover:bg-slate-50 transition-all duration-200 group">
                <div class="user-avatar overflow-hidden w-10 h-10 rounded-full bg-red-50 text-red-600 flex items-center justify-center font-bold text-xs border border-red-100">
                    {{ collect(explode(' ', auth()->user()->name))->map(fn($n) => strtoupper(substr($n, 0, 1)))->take(2)->implode('') }}
                </div>
                <div class="hidden lg:block text-left">
                    <div class="text-xs font-bold text-slate-800 leading-none">{{ auth()->user()->name }}</div>
                    <div class="text-[10px] font-semibold text-slate-400 leading-none mt-1">{{ ucfirst(auth()->user()->role) }}</div>
                </div>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="transform opacity-0 scale-95 translate-y-2"
                 x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="transform opacity-0 scale-95 translate-y-2"
                 class="absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50 overflow-hidden"
                 style="display: none;">
                
                <!-- Profile Header -->
                <div class="px-5 py-4 border-b border-slate-50">
                    <div class="text-sm font-bold text-slate-800 mb-0.5">{{ auth()->user()->name }}</div>
                    <div class="text-[11px] font-medium text-slate-400 transition-all truncate">{{ auth()->user()->email }}</div>
                </div>

                <!-- Menu Items -->
                <div class="p-2 space-y-1">
                    <button type="button" @click="confirmLogout()" 
                            class="flex items-center gap-3 w-full px-3 py-2.5 text-sm font-bold text-red-500 rounded-xl hover:bg-red-50 transition-all duration-200">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                        <span>Keluar Sesi</span>
                    </button>
                </div>
            </div>
        </div>
        @endauth
    </div>
</header>

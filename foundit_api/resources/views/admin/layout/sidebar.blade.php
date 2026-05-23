<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
       class="sidebar lg:translate-x-0 -translate-x-full transition-transform duration-300 ease-in-out">
    <!-- Brand -->
    <div class="brand">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center p-1 overflow-hidden shadow-sm border border-slate-100">
                <img src="{{ asset('logo.png') }}" class="w-full h-full object-contain" alt="FoundIt Logo">
            </div>
            <div class="flex flex-col">
                <h5 class="text-sm font-bold text-slate-800 tracking-tight leading-tight">FoundIt</h5>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none">Admin Panel</span>
            </div>
        </div>
    </div>

    <div class="space-y-4 pb-10 flex flex-col justify-between h-[calc(100vh-80px)] overflow-y-auto">
        <div class="space-y-4">
            <!-- Main Menu -->
            <div>
                <div class="nav-section">
                    <span class="nav-section-title">Main Menu</span>
                </div>
                <nav class="space-y-1">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="nav-link group {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                        <span>Dashboard</span>
                    </a>
                </nav>
            </div>

            <!-- Lost & Found Management -->
            <div>
                <div class="nav-section">
                    <span class="nav-section-title">Lost & Found</span>
                </div>
                <nav class="space-y-1">
                    <a href="{{ route('admin.items.index') }}" 
                       class="nav-link group {{ request()->routeIs('admin.items.*') ? 'active' : '' }}">
                        <i data-lucide="package" class="w-5 h-5"></i>
                        <span>Laporan Barang</span>
                    </a>

                    <a href="{{ route('admin.claims.index') }}" 
                       class="nav-link group {{ request()->routeIs('admin.claims.*') ? 'active' : '' }}">
                        <i data-lucide="file-check-2" class="w-5 h-5"></i>
                        <span>Klaim Barang</span>
                    </a>

                    <a href="{{ route('admin.categories.index') }}" 
                       class="nav-link group {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <i data-lucide="tag" class="w-5 h-5"></i>
                        <span>Kategori Barang</span>
                    </a>
                </nav>
            </div>

            <!-- Accounts Management -->
            <div>
                <div class="nav-section">
                    <span class="nav-section-title">Accounts</span>
                </div>
                <nav class="space-y-1">
                    <a href="{{ route('admin.users.index') }}" 
                       class="nav-link group {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i data-lucide="users" class="w-5 h-5"></i>
                        <span>Pengguna</span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Logout & Session Management -->
        <div class="px-4 pt-4 mt-auto border-t border-slate-100">
            <button type="button" 
                    @click="confirmLogout()"
                    class="flex items-center justify-between w-full px-5 py-4 rounded-2xl bg-white border border-slate-100 text-slate-600 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-100 transition-all duration-300 group shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-rose-100/50 group-hover:text-rose-500 transition-all">
                        <i data-lucide="log-out" class="w-5 h-5"></i>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-widest">Keluar Sesi</span>
                </div>
                <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 group-hover:text-rose-400 group-hover:translate-x-1 transition-all"></i>
            </button>
        </div>
    </div>
</aside>

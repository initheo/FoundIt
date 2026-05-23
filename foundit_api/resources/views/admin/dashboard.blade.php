@extends('admin.layout.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Top Row: Welcome & Mini Stats -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <!-- Welcome Card -->
        <div class="xl:col-span-8 h-full">
            <div class="relative overflow-hidden bg-white rounded-3xl border border-slate-100 shadow-sm transition-all hover:shadow-md h-full">
                <div class="flex flex-col md:flex-row items-center h-full">
                    <div class="flex-1 p-8 lg:p-10">
                        <h4 class="text-xl lg:text-2xl font-bold text-slate-800 mb-2">Selamat Datang, Admin! 👋</h4>
                        <p class="text-slate-500 mb-6 leading-relaxed max-w-md">
                            Monitor aktivitas penemuan & kehilangan barang, tinjau pengajuan klaim, serta kelola pengguna dari satu dashboard terintegrasi FoundIt.
                        </p>
                        <a href="{{ route('admin.claims.index') }}" class="inline-flex items-center px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-red-500/20 active:scale-95 cursor-pointer">
                            Tinjau Klaim Barang
                        </a>
                    </div>
                    <div class="hidden md:flex md:w-2/5 p-4 justify-center items-center md:bg-transparent">
                        <div class="relative group">
                            <div class="absolute -inset-8 bg-red-100/50 rounded-full blur-3xl group-hover:bg-red-200/50 transition-all duration-500"></div>
                            <i data-lucide="shield-check" class="relative w-36 h-36 text-red-500 drop-shadow-2xl animate-float"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mini Stats Grid -->
        <div class="xl:col-span-4">
            <div class="grid grid-cols-2 gap-4 h-full">
                <!-- Total Items -->
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col justify-between group transition-all hover:-translate-y-1 hover:shadow-md">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-12 h-12 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center transition-colors group-hover:bg-red-600 group-hover:text-white">
                            <i data-lucide="package" class="w-6 h-6"></i>
                        </div>
                        <div class="px-2 py-0.5 bg-slate-50 text-slate-500 rounded-lg text-[10px] font-black uppercase">Semua</div>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-slate-800 leading-none mb-1">{{ $stats['total_items'] ?? 0 }}</h3>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Laporan</p>
                    </div>
                </div>

                <!-- Active Items -->
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col justify-between group transition-all hover:-translate-y-1 hover:shadow-md">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-12 h-12 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center transition-colors group-hover:bg-amber-500 group-hover:text-white">
                            <i data-lucide="search" class="w-6 h-6"></i>
                        </div>
                        <span class="inline-block w-2.5 h-2.5 bg-amber-400 rounded-full animate-pulse"></span>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-slate-800 leading-none mb-1">{{ $stats['active_items'] ?? 0 }}</h3>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Laporan Aktif</p>
                    </div>
                </div>

                <!-- Resolved Items -->
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col justify-between group transition-all hover:-translate-y-1 hover:shadow-md">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-12 h-12 bg-green-50 text-green-500 rounded-2xl flex items-center justify-center transition-colors group-hover:bg-green-500 group-hover:text-white">
                            <i data-lucide="check-circle" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-slate-800 leading-none mb-1">{{ $stats['resolved_items'] ?? 0 }}</h3>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Laporan Selesai</p>
                    </div>
                </div>

                <!-- Pending Claims -->
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col justify-between group transition-all hover:-translate-y-1 hover:shadow-md">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center transition-colors group-hover:bg-blue-500 group-hover:text-white">
                            <i data-lucide="file-clock" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-slate-800 leading-none mb-1">{{ $stats['pending_claims'] ?? 0 }}</h3>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Klaim Pending</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Row: Sebaran Lokasi Barang -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden p-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-red-50 text-red-500 rounded-xl flex items-center justify-center">
                <i data-lucide="map" class="w-5 h-5"></i>
            </div>
            <div>
                <h4 class="font-bold text-slate-800">Peta Sebaran Laporan Barang</h4>
                <p class="text-xs text-slate-500 font-medium">Visualisasi lokasi barang hilang & temuan di sekitar area kampus</p>
            </div>
        </div>
        <div id="dashboard-map" class="w-full rounded-2xl border border-slate-100 z-0" style="height: 450px;"></div>
    </div>

    <!-- Middle Row: Recent Items & Recent Claims -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Items -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-red-50 text-red-500 rounded-xl flex items-center justify-center">
                        <i data-lucide="package" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800">Laporan Terbaru</h4>
                        <p class="text-xs text-slate-500 font-medium">Laporan barang hilang & temuan terbaru</p>
                    </div>
                </div>
                <a href="{{ route('admin.items.index') }}" class="text-xs font-bold text-red-600 hover:text-red-700 transition-colors uppercase tracking-wider">Semua Laporan</a>
            </div>
            <div class="p-6 divide-y divide-slate-50">
                @forelse($stats['latest_items'] ?? [] as $item)
                    <a href="{{ route('admin.items.show', $item->id) }}" class="flex items-center gap-4 py-4 first:pt-0 last:pb-0 group rounded-xl px-2 -mx-2 hover:bg-slate-50/60 transition-colors" title="Lihat detail {{ $item->title }}">
                        <div class="w-11 h-11 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 font-bold border border-slate-100 shadow-sm transition-all group-hover:bg-red-50 group-hover:text-red-500 group-hover:border-red-100 overflow-hidden shrink-0">
                            @if($item->photos->isNotEmpty())
                                <img src="{{ asset('storage/' . $item->photos->first()->photo_url) }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                            @else
                                <i data-lucide="image" class="w-5 h-5"></i>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h5 class="text-sm font-bold text-slate-800 truncate mb-0.5 transition-colors group-hover:text-red-600">{{ $item->title }}</h5>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase {{ $item->type === 'lost' ? 'bg-red-50 text-red-600 border border-red-100' : 'bg-green-50 text-green-600 border border-green-100' }}">
                                    {{ $item->type === 'lost' ? 'Hilang' : 'Temuan' }}
                                </span>
                                <span class="text-xs text-slate-400 font-medium truncate">oleh {{ $item->user->name ?? 'User' }}</span>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <div class="text-[10px] font-bold text-slate-500 whitespace-nowrap">{{ $item->created_at->diffForHumans() }}</div>
                            <span class="inline-block px-2 py-0.5 rounded-lg text-[9px] font-black uppercase mt-1
                                {{ $item->status === 'active' ? 'bg-amber-50 text-amber-600 border border-amber-100' : 
                                   ($item->status === 'claimed' ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-green-50 text-green-600 border border-green-100') }}">
                                {{ $item->status }}
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mb-4">
                            <i data-lucide="ghost" class="w-8 h-8"></i>
                        </div>
                        <p class="text-sm font-bold text-slate-400">Belum ada laporan barang</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Claims -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-50 text-blue-500 rounded-xl flex items-center justify-center">
                        <i data-lucide="file-check-2" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800">Klaim Terbaru</h4>
                        <p class="text-xs text-slate-500 font-medium">Pengajuan klaim barang penemuan</p>
                    </div>
                </div>
                <a href="{{ route('admin.claims.index') }}" class="text-xs font-bold text-red-600 hover:text-red-700 transition-colors uppercase tracking-wider">Semua Klaim</a>
            </div>
            <div class="p-6 divide-y divide-slate-50">
                @forelse($stats['latest_claims'] ?? [] as $claim)
                    <a href="{{ route('admin.claims.show', $claim->id) }}" class="flex items-center gap-4 py-4 first:pt-0 last:pb-0 group rounded-xl px-2 -mx-2 hover:bg-slate-50/60 transition-colors" title="Tinjau klaim {{ $claim->claimer->name ?? 'User' }}">
                        <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center font-bold text-xs shadow-sm transition-all group-hover:bg-blue-500 group-hover:text-white shrink-0">
                            {{ collect(explode(' ', $claim->claimer->name ?? 'User'))->map(fn($n) => strtoupper(substr($n, 0, 1)))->take(2)->implode('') }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h5 class="text-sm font-bold text-slate-800 truncate mb-0.5 transition-colors group-hover:text-blue-600">{{ $claim->claimer->name ?? 'User' }}</h5>
                            <p class="text-xs text-slate-400 font-medium truncate">Mengklaim: <strong>{{ $claim->item->title ?? 'Barang' }}</strong></p>
                        </div>
                        <div class="text-right shrink-0">
                            <div class="text-[10px] font-bold text-slate-500 whitespace-nowrap">{{ $claim->created_at->diffForHumans() }}</div>
                            <span class="inline-block px-2 py-0.5 rounded-lg text-[9px] font-black uppercase mt-1
                                {{ $claim->status === 'pending' ? 'bg-amber-50 text-amber-600 border border-amber-100' : 
                                   ($claim->status === 'approved' ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-red-50 text-red-600 border border-red-100') }}">
                                {{ $claim->status }}
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mb-4">
                            <i data-lucide="ghost" class="w-8 h-8"></i>
                        </div>
                        <p class="text-sm font-bold text-slate-400">Belum ada pengajuan klaim</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Bottom Row: System Summary & Quick Actions -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 pb-6">
        <!-- System Summary -->
        <div class="xl:col-span-1 bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
            <h4 class="font-bold text-slate-800 mb-6 flex items-center gap-2">
                <i data-lucide="bar-chart-2" class="w-5 h-5 text-red-500"></i>
                Ringkasan Sistem
            </h4>
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100/50">
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Total Pengguna</p>
                    <p class="text-xl font-black text-slate-800">{{ $stats['total_users'] ?? 0 }}</p>
                </div>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100/50">
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Laporan Aktif</p>
                    <p class="text-xl font-black text-slate-800">{{ $stats['active_items'] ?? 0 }}</p>
                </div>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100/50">
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Laporan Selesai</p>
                    <p class="text-xl font-black text-slate-800">{{ $stats['resolved_items'] ?? 0 }}</p>
                </div>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100/50">
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Klaim Pending</p>
                    <p class="text-xl font-black text-slate-800">{{ $stats['pending_claims'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="xl:col-span-2 bg-white rounded-3xl border border-slate-100 shadow-sm p-6 overflow-hidden">
            <h4 class="font-bold text-slate-800 mb-6 flex items-center gap-2">
                <i data-lucide="zap" class="w-5 h-5 text-amber-500"></i>
                Aksi Cepat
            </h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('admin.items.create') }}" class="group flex flex-col items-center justify-center p-6 bg-white border border-slate-100 rounded-3xl transition-all hover:bg-red-600 hover:border-red-600 hover:-translate-y-1 hover:shadow-lg hover:shadow-red-500/20 active:scale-95 cursor-pointer">
                    <div class="w-12 h-12 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center mb-4 transition-colors group-hover:bg-white/20 group-hover:text-white">
                        <i data-lucide="package-plus" class="w-6 h-6"></i>
                    </div>
                    <span class="text-[11px] font-black text-slate-400 uppercase tracking-wider group-hover:text-white transition-colors text-center">Tambah Laporan</span>
                </a>
                <a href="{{ route('admin.categories.create') }}" class="group flex flex-col items-center justify-center p-6 bg-white border border-slate-100 rounded-3xl transition-all hover:bg-green-600 hover:border-green-600 hover:-translate-y-1 hover:shadow-lg hover:shadow-green-500/20 active:scale-95 cursor-pointer">
                    <div class="w-12 h-12 bg-green-50 text-green-500 rounded-2xl flex items-center justify-center mb-4 transition-colors group-hover:bg-white/20 group-hover:text-white">
                        <i data-lucide="tag" class="w-6 h-6"></i>
                    </div>
                    <span class="text-[11px] font-black text-slate-400 uppercase tracking-wider group-hover:text-white transition-colors text-center">Tambah Kategori</span>
                </a>
                <a href="{{ route('admin.claims.index', ['status' => 'pending']) }}" class="group flex flex-col items-center justify-center p-6 bg-white border border-slate-100 rounded-3xl transition-all hover:bg-blue-600 hover:border-blue-600 hover:-translate-y-1 hover:shadow-lg hover:shadow-blue-500/20 active:scale-95 cursor-pointer">
                    <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center mb-4 transition-colors group-hover:bg-white/20 group-hover:text-white">
                        <i data-lucide="file-clock" class="w-6 h-6"></i>
                    </div>
                    <span class="text-[11px] font-black text-slate-400 uppercase tracking-wider group-hover:text-white transition-colors text-center">Klaim Pending</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="group flex flex-col items-center justify-center p-6 bg-white border border-slate-100 rounded-3xl transition-all hover:bg-slate-800 hover:border-slate-800 hover:-translate-y-1 hover:shadow-lg hover:shadow-slate-800/20 active:scale-95 cursor-pointer">
                    <div class="w-12 h-12 bg-slate-50 text-slate-500 rounded-2xl flex items-center justify-center mb-4 transition-colors group-hover:bg-white/20 group-hover:text-white">
                        <i data-lucide="users" class="w-6 h-6"></i>
                    </div>
                    <span class="text-[11px] font-black text-slate-400 uppercase tracking-wider group-hover:text-white transition-colors text-center">Kelola User</span>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    .animate-float {
        animation: float 4s ease-in-out infinite;
    }
</style>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .custom-div-icon {
        background: transparent !important;
        border: none !important;
    }
    /* Style Leaflet popups to look modern and match our UI */
    #dashboard-map .leaflet-popup-content-wrapper {
        border-radius: 1rem !important;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.1), 0 8px 10px -6px rgba(15, 23, 42, 0.1) !important;
        border: 1px solid #e2e8f0 !important;
        padding: 0 !important;
        overflow: hidden !important;
    }
    #dashboard-map .leaflet-popup-content {
        margin: 0 !important;
        width: 260px !important;
    }
    #dashboard-map .leaflet-popup-tip-container {
        display: none !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mapItems = @json($mapItems);
        
        // Default coordinates: Universitas Internasional Semen Indonesia (UISI)
        const defaultLat = -7.1756;
        const defaultLng = 112.6492;
        
        const map = L.map('dashboard-map').setView([defaultLat, defaultLng], 15);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        const markers = [];
        
        mapItems.forEach(item => {
            const lat = parseFloat(item.latitude);
            const lng = parseFloat(item.longitude);
            
            if (isNaN(lat) || isNaN(lng)) return;
            
            // Determine marker color based on item type
            // Lost (Hilang) -> Red, Found (Temuan) -> Green
            const pinColorClass = item.type === 'lost' ? 'bg-red-600' : 'bg-green-600';
            const iconSvg = item.type === 'lost' 
                ? `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>`
                : `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-navigation"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>`;
            
            const customIcon = L.divIcon({
                className: 'custom-div-icon',
                html: `<div class="flex items-center justify-center w-8 h-8 rounded-full ${pinColorClass} border-2 border-white shadow-lg text-white">
                          ${iconSvg}
                       </div>`,
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });
            
            // Build custom popup HTML card
            const badgeClass = item.type === 'lost' 
                ? 'bg-red-50 text-red-600 border border-red-100' 
                : 'bg-green-50 text-green-600 border border-green-100';
            
            const typeLabel = item.type === 'lost' ? 'Hilang' : 'Temuan';
            
            const statusClass = item.status === 'active' 
                ? 'bg-amber-50 text-amber-600 border border-amber-100' 
                : (item.status === 'claimed' ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-green-50 text-green-600 border border-green-100');
            
            let photoHtml = '';
            if (item.photo_url) {
                photoHtml = `<div class="relative h-32 w-full bg-slate-50 border-b border-slate-100 overflow-hidden">
                                <img src="${item.photo_url}" alt="${item.title}" class="w-full h-full object-cover">
                             </div>`;
            } else {
                photoHtml = `<div class="relative h-24 w-full bg-slate-50 border-b border-slate-100 flex items-center justify-center text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-image"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                             </div>`;
            }
            
            const popupContent = `
                <div class="flex flex-col font-sans">
                    ${photoHtml}
                    <div class="p-4 flex-1">
                        <div class="flex flex-wrap items-center gap-1.5 mb-2">
                            <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase ${badgeClass}">${typeLabel}</span>
                            <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase bg-slate-50 text-slate-600 border border-slate-100">${item.category_name}</span>
                            <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase ${statusClass}">${item.status}</span>
                        </div>
                        <h5 class="text-sm font-bold text-slate-800 mb-1 leading-snug line-clamp-1">${item.title}</h5>
                        <p class="text-[11px] text-slate-500 mb-4 flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin text-slate-400 shrink-0"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                            <span class="truncate">${item.location || 'Lokasi tidak spesifik'}</span>
                        </p>
                        <a href="${item.detail_url}" class="w-full flex items-center justify-center py-2 bg-slate-900 hover:bg-slate-800 text-white text-[11px] font-black rounded-lg transition-colors uppercase tracking-wider">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            `;
            
            const marker = L.marker([lat, lng], { icon: customIcon })
                .bindPopup(popupContent)
                .addTo(map);
                
            markers.push(marker);
        });
        
        // Dynamically adjust map bounds to show all markers
        if (markers.length > 0) {
            const group = new L.featureGroup(markers);
            map.fitBounds(group.getBounds().pad(0.15));
        }
    });
</script>
@endpush

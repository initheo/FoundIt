@extends('admin.layout.app')

@section('title', 'Detail Laporan Barang')

@section('content')
<div class="space-y-6 pb-8 max-w-[1400px] mx-auto">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.items.index') }}" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-all cursor-pointer">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Detail Laporan Barang</h2>
                <p class="text-slate-500 text-xs mt-1 font-medium tracking-tight">Rincian spesifikasi barang, foto, lokasi, pelapor, dan daftar klaim</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.items.edit', $item->id) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 text-xs font-bold uppercase tracking-wider rounded-xl transition-all cursor-pointer">
                <i data-lucide="edit-3" class="w-4 h-4"></i> Edit Laporan
            </a>
            <button type="button"
                    onclick="confirmDelete('{{ route('admin.items.destroy', $item->id) }}', 'Apakah Anda yakin ingin menghapus laporan {{ $item->title }}? Semua foto dan relasi klaim yang terhubung dengan laporan ini akan ikut terhapus.')"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-50 hover:bg-rose-100 border border-rose-100 text-rose-600 text-xs font-bold uppercase tracking-wider rounded-xl transition-all cursor-pointer">
                <i data-lucide="trash-2" class="w-4 h-4"></i> Hapus Laporan
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <!-- Left Column: Details & Photos -->
        <div class="xl:col-span-8 space-y-6">
            <!-- Details Card -->
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden p-6 lg:p-8">
                <!-- Status Badges -->
                <div class="flex flex-wrap items-center gap-3 mb-6">
                    <span class="inline-flex items-center px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider border shadow-sm
                        {{ $item->type === 'lost' ? 'bg-red-50 text-red-600 border-red-100' : 'bg-green-50 text-green-600 border-green-100' }}">
                        {{ $item->type === 'lost' ? 'Kehilangan (Lost)' : 'Temuan (Found)' }}
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider border shadow-sm
                        {{ $item->status === 'active' ? 'bg-amber-50 text-amber-600 border border-amber-100' : 
                           ($item->status === 'claimed' ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-green-50 text-green-600 border border-green-100') }}">
                        {{ $item->status }}
                    </span>
                </div>

                <h3 class="text-xl lg:text-2xl font-bold text-slate-800 tracking-tight mb-4">{{ $item->title }}</h3>
                
                <!-- Description -->
                <div class="prose prose-sm max-w-none text-slate-600 mb-8 leading-relaxed">
                    <h5 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Deskripsi Barang</h5>
                    <p class="whitespace-pre-line bg-slate-50/50 p-4 rounded-2xl border border-slate-100/50 text-sm font-medium">{!! e($item->description) !!}</p>
                </div>

                <!-- Specs Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 border-t border-slate-50 pt-8">
                    <div class="space-y-4">
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-1">Kategori</span>
                            <span class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                                <span class="w-7 h-7 bg-red-50 text-red-500 rounded-lg flex items-center justify-center shrink-0">
                                    <i data-lucide="{{ $item->category->icon ?? 'tag' }}" class="w-4 h-4"></i>
                                </span>
                                {{ $item->category->name ?? 'Tanpa Kategori' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-1">Waktu Kejadian</span>
                            <span class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                                <span class="w-7 h-7 bg-slate-50 text-slate-500 rounded-lg flex items-center justify-center shrink-0">
                                    <i data-lucide="calendar" class="w-4 h-4"></i>
                                </span>
                                {{ \Carbon\Carbon::parse($item->date_time)->format('d F Y, H:i') }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-1">Info Penyimpanan / Kontak Tambahan</span>
                            <span class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                                <span class="w-7 h-7 bg-slate-50 text-slate-500 rounded-lg flex items-center justify-center shrink-0">
                                    <i data-lucide="archive" class="w-4 h-4"></i>
                                </span>
                                {{ $item->storage_info ?? '-' }}
                            </span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-1">Lokasi Kejadian</span>
                            <span class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                                <span class="w-7 h-7 bg-slate-50 text-slate-500 rounded-lg flex items-center justify-center shrink-0">
                                    <i data-lucide="map-pin" class="w-4 h-4"></i>
                                </span>
                                {{ $item->location }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-1">Detail Lokasi</span>
                            <span class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                                <span class="w-7 h-7 bg-slate-50 text-slate-500 rounded-lg flex items-center justify-center shrink-0">
                                    <i data-lucide="info" class="w-4 h-4"></i>
                                </span>
                                {{ $item->location_detail ?? '-' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-1">Koordinat GPS</span>
                            @if($item->latitude && $item->longitude)
                                <a href="https://www.google.com/maps/search/?api=1&query={{ $item->latitude }},{{ $item->longitude }}" 
                                   target="_blank" 
                                   class="text-sm font-bold text-red-600 hover:text-red-700 hover:underline flex items-center gap-2 group/map">
                                    <span class="w-7 h-7 bg-red-50 text-red-500 group-hover/map:bg-red-500 group-hover/map:text-white rounded-lg flex items-center justify-center shrink-0 transition-all">
                                        <i data-lucide="navigation" class="w-4 h-4"></i>
                                    </span>
                                    {{ $item->latitude }}, {{ $item->longitude }}
                                </a>
                            @else
                                <span class="text-sm font-semibold text-slate-400 flex items-center gap-2">
                                    <span class="w-7 h-7 bg-slate-50 text-slate-400 rounded-lg flex items-center justify-center shrink-0">
                                        <i data-lucide="navigation-off" class="w-4 h-4"></i>
                                    </span>
                                    Tidak Tersemat GPS
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Map Section -->
                @if($item->latitude && $item->longitude)
                    <div class="border-t border-slate-50 pt-8 mb-8">
                        <h5 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Peta Lokasi</h5>
                        <div id="map-detail" class="h-80 w-full rounded-2xl border border-slate-200 shadow-sm z-0"></div>
                    </div>
                @endif

                <!-- Photos Section -->
                <div class="border-t border-slate-50 pt-8">
                    <h5 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Lampiran Foto ({{ $item->photos->count() }}/3)</h5>
                    @if($item->photos->isNotEmpty())
                        <div class="grid grid-cols-3 gap-4">
                            @foreach($item->photos as $photo)
                                <div class="relative group aspect-square rounded-2xl overflow-hidden bg-slate-50 border border-slate-100 shadow-sm cursor-zoom-in"
                                     @click="openLightbox('{{ asset('storage/' . $photo->photo_url) }}', '{{ $item->title }}')">
                                    <img src="{{ asset('storage/' . $photo->photo_url) }}" alt="{{ $item->title }}" class="w-full h-full object-cover transition-all group-hover:scale-105">
                                    <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white">
                                        <i data-lucide="zoom-in" class="w-6 h-6"></i>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-slate-50 rounded-2xl p-8 text-center border border-dashed border-slate-200">
                            <i data-lucide="image-off" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                            <p class="text-xs text-slate-400 font-medium">Tidak ada foto dilampirkan</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Reporter & Claim list -->
        <div class="xl:col-span-4 space-y-6">
            <!-- Reporter Info Card -->
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                <h4 class="font-bold text-slate-800 text-sm tracking-tight mb-5 flex items-center gap-2">
                    <i data-lucide="user" class="w-4 h-4 text-red-500"></i>
                    Informasi Pelapor
                </h4>
                @if($item->user)
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-11 h-11 rounded-xl bg-red-50 text-red-600 flex items-center justify-center font-bold text-sm border border-red-100 shadow-inner shrink-0">
                            {{ collect(explode(' ', $item->user->name))->map(fn($n) => strtoupper(substr($n, 0, 1)))->take(2)->implode('') }}
                        </div>
                        <div class="min-w-0">
                            <a href="{{ route('admin.users.show', $item->user->id) }}" class="text-sm font-bold text-slate-700 block truncate hover:text-red-600 transition-colors">{{ $item->user->name }}</a>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mt-0.5">{{ $item->user->role }}</span>
                        </div>
                    </div>

                    <div class="space-y-3.5 border-t border-slate-50 pt-5">
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-400 font-bold uppercase tracking-wider">Email</span>
                            <span class="text-slate-700 font-bold break-all max-w-[200px] text-right">{{ $item->user->email }}</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-400 font-bold uppercase tracking-wider">Prodi / Unit</span>
                            <span class="text-slate-700 font-bold text-right">{{ $item->user->prodi_unit ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-400 font-bold uppercase tracking-wider">Telepon</span>
                            <span class="text-slate-700 font-bold text-right">{{ $item->user->phone ?? '-' }}</span>
                        </div>
                    </div>
                @else
                    <div class="text-slate-400 text-xs py-4 text-center font-medium">Pelapor tidak ditemukan atau telah dihapus.</div>
                @endif
            </div>

            <!-- Claim list Card (Only relevant if item status is claimed/returned/active or claims exist) -->
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                <h4 class="font-bold text-slate-800 text-sm tracking-tight mb-5 flex items-center gap-2">
                    <i data-lucide="file-check-2" class="w-4 h-4 text-blue-500"></i>
                    Daftar Pengajuan Klaim ({{ $item->claims->count() }})
                </h4>
                
                <div class="space-y-4">
                    @forelse($item->claims as $claim)
                        <div class="p-4 bg-slate-50/60 hover:bg-slate-50 border border-slate-100 rounded-2xl transition-all flex flex-col gap-3 group relative">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center font-bold text-xs shrink-0">
                                    {{ collect(explode(' ', $claim->claimer->name ?? 'User'))->map(fn($n) => strtoupper(substr($n, 0, 1)))->take(2)->implode('') }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <a href="{{ route('admin.claims.show', $claim->id) }}" class="text-xs font-bold text-slate-700 block truncate group-hover:text-blue-600 transition-colors">{{ $claim->claimer->name ?? 'User' }}</a>
                                    <span class="text-[9px] text-slate-400 font-bold tracking-tight block mt-0.5">{{ $claim->created_at->diffForHumans() }}</span>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase shrink-0
                                    {{ $claim->status === 'pending' ? 'bg-amber-50 text-amber-600 border border-amber-100' : 
                                       ($claim->status === 'approved' ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-red-50 text-red-600 border border-red-100') }}">
                                    {{ $claim->status }}
                                </span>
                            </div>

                            <p class="text-[11px] text-slate-500 leading-normal line-clamp-2">"{{ $claim->reason }}"</p>
                            
                            <div class="flex justify-end border-t border-slate-100/50 pt-2">
                                <a href="{{ route('admin.claims.show', $claim->id) }}" class="text-[10px] font-black text-blue-600 hover:text-blue-700 transition-colors uppercase tracking-wider flex items-center gap-1 cursor-pointer">
                                    Tinjau Klaim <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-slate-400 text-xs py-8 text-center font-medium">
                            <i data-lucide="ghost" class="w-7 h-7 text-slate-300 mx-auto mb-2"></i>
                            Belum ada pengajuan klaim untuk barang ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .custom-div-icon {
        background: transparent !important;
        border: none !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const lat = {{ $item->latitude ?? 'null' }};
        const lng = {{ $item->longitude ?? 'null' }};
        
        if (lat !== null && lng !== null) {
            const map = L.map('map-detail').setView([lat, lng], 17);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            const customIcon = L.divIcon({
                className: 'custom-div-icon',
                html: `<div class="flex items-center justify-center w-8 h-8 rounded-full bg-red-600 border-2 border-white shadow-lg text-white">
                          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                       </div>`,
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });
            
            L.marker([lat, lng], { icon: customIcon }).addTo(map)
                .bindPopup("<b>{!! addslashes(e($item->title)) !!}</b><br>{!! addslashes(e($item->location)) !!}").openPopup();
        }
    });
</script>
@endpush

@extends('admin.layout.app')

@section('title', 'Tinjau Klaim Barang')

@section('content')
<div class="space-y-6 pb-8 max-w-[1400px] mx-auto" x-data="{ showRejectModal: false, reason: '' }">
    <!-- Page Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.claims.index') }}" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-all cursor-pointer">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Tinjau Klaim Barang</h2>
            <p class="text-slate-500 text-xs mt-1 font-medium tracking-tight">Periksa bukti kepemilikan dan proses pengajuan klaim</p>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        
        <!-- Left: Claim Details & Decision -->
        <div class="xl:col-span-7 space-y-6">
            <!-- Details Card -->
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 lg:p-8">
                <div class="flex items-center justify-between mb-6">
                    <span class="inline-flex items-center px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider border shadow-sm
                        {{ $claim->status === 'pending' ? 'bg-amber-50 text-amber-600 border-amber-100' : 
                           ($claim->status === 'approved' ? 'bg-green-50 text-green-600 border-green-100' : 'bg-red-50 text-red-600 border-red-100') }}">
                        {{ $claim->status }}
                    </span>
                    <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">ID Klaim: #{{ $claim->id }}</span>
                </div>

                <!-- Claimant Info -->
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg border border-blue-100 shadow-inner shrink-0">
                        {{ collect(explode(' ', $claim->claimer->name ?? 'User'))->map(fn($n) => strtoupper(substr($n, 0, 1)))->take(2)->implode('') }}
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-slate-800 leading-tight mb-1">{{ $claim->claimer->name ?? 'User' }}</h4>
                        <span class="text-xs text-slate-500 font-semibold">{{ $claim->claimer->email ?? '-' }}</span>
                    </div>
                </div>

                <!-- Specs -->
                <div class="grid grid-cols-2 gap-4 border-t border-b border-slate-50 py-6 mb-6">
                    <div>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-1">No. Telepon / WA</span>
                        <span class="text-sm font-semibold text-slate-700">{{ $claim->claimer->phone ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-1">Prodi / Unit</span>
                        <span class="text-sm font-semibold text-slate-700">{{ $claim->claimer->prodi_unit ?? '-' }}</span>
                    </div>
                    <div class="mt-4">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-1">Diajukan Pada</span>
                        <span class="text-xs font-semibold text-slate-700">{{ $claim->created_at->format('d M Y H:i') }} ({{ $claim->created_at->diffForHumans() }})</span>
                    </div>
                    @if($claim->reviewed_at)
                        <div class="mt-4">
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-1">Ditinjau Pada</span>
                            <span class="text-xs font-semibold text-slate-700">{{ \Carbon\Carbon::parse($claim->reviewed_at)->format('d M Y H:i') }}</span>
                        </div>
                    @endif
                </div>

                <!-- Verification Code (Only for Approved Claims) -->
                @if($claim->status === 'approved' && $claim->verification_code)
                    <div class="mb-6 p-4 bg-emerald-50 rounded-2xl border border-emerald-100 flex items-center justify-between shadow-sm">
                        <div>
                            <span class="text-[10px] text-emerald-500 font-bold uppercase tracking-wider block mb-0.5">Kode Verifikasi Serah Terima</span>
                            <span class="text-lg font-black text-emerald-700 tracking-widest">{{ $claim->verification_code }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-[9px] text-emerald-500 font-bold uppercase tracking-wider block">Status Klaim</span>
                            <span class="text-xs font-black text-emerald-600 flex items-center gap-1 mt-0.5 justify-end uppercase tracking-tight">
                                <i class="w-4 h-4" data-feather="check-circle"></i> Disetujui
                            </span>
                        </div>
                    </div>
                @endif

                <!-- Claim Reason -->
                <div class="space-y-2 mb-6">
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Alasan Pengajuan & Bukti Kepemilikan</span>
                    <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100 text-sm text-slate-700 font-medium leading-relaxed whitespace-pre-line">
                        "{!! e($claim->reason) !!}"
                    </div>
                </div>

                <!-- Rejection Reason if applicable -->
                @if($claim->status === 'rejected' && $claim->rejection_reason)
                    <div class="space-y-2 mb-6">
                        <span class="text-[10px] text-red-400 font-bold uppercase tracking-wider block">Alasan Penolakan Admin</span>
                        <div class="p-5 bg-red-50 text-red-700 rounded-2xl border border-red-100 text-sm font-medium leading-relaxed">
                            "{!! e($claim->rejection_reason) !!}"
                        </div>
                    </div>
                @endif

                <!-- Decision Panel (Only for Pending) -->
                @if($claim->status === 'pending')
                    <div class="mt-8 pt-6 border-t border-slate-50">
                        <!-- Alert message about cascade rejection -->
                        @if($otherClaims->isNotEmpty())
                            <div class="bg-amber-50 border border-amber-100 rounded-2xl p-4 mb-6 flex gap-3 text-amber-700 text-xs font-medium">
                                <i data-lucide="info" class="w-5 h-5 shrink-0 text-amber-500"></i>
                                <div>
                                    <span class="font-bold">Perhatian:</span> Menyetujui klaim ini secara otomatis akan <strong>menolak</strong> {{ $otherClaims->count() }} pengajuan klaim pending lainnya pada barang ini.
                                </div>
                            </div>
                        @endif

                        <div class="flex flex-col sm:flex-row gap-3">
                            <form action="{{ route('admin.claims.approve', $claim->id) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full justify-center inline-flex items-center gap-2 px-6 py-3.5 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white text-xs font-black uppercase tracking-wider rounded-xl transition-all shadow-lg shadow-green-500/20 active:scale-95 cursor-pointer">
                                    <i data-lucide="check" class="w-4.5 h-4.5"></i> Setujui Klaim
                                </button>
                            </form>

                            <button type="button" 
                                    @click="showRejectModal = true"
                                    class="flex-1 justify-center inline-flex items-center gap-2 px-6 py-3.5 bg-red-50 border border-red-100 hover:bg-red-100 text-red-600 text-xs font-black uppercase tracking-wider rounded-xl transition-all active:scale-95 cursor-pointer">
                                <i data-lucide="x" class="w-4.5 h-4.5"></i> Tolak Klaim
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right: Item Details & Rival Claims -->
        <div class="xl:col-span-5 space-y-6">
            <!-- Item Info Card -->
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                <h4 class="font-bold text-slate-800 text-sm tracking-tight mb-5 flex items-center gap-2">
                    <i data-lucide="package" class="w-4 h-4 text-red-500"></i>
                    Barang yang Diklaim
                </h4>

                @if($claim->item)
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-16 h-16 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 overflow-hidden shrink-0 shadow-sm cursor-zoom-in"
                             @click="openLightbox('{{ $claim->item->photos->isNotEmpty() ? asset('storage/' . $claim->item->photos->first()->photo_url) : '' }}', '{{ $claim->item->title }}')">
                            @if($claim->item->photos->isNotEmpty())
                                <img src="{{ asset('storage/' . $claim->item->photos->first()->photo_url) }}" alt="{{ $claim->item->title }}" class="w-full h-full object-cover">
                            @else
                                <i data-lucide="image" class="w-5 h-5 text-slate-300"></i>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <a href="{{ route('admin.items.show', $claim->item_id) }}" class="text-sm font-bold text-slate-800 leading-tight hover:text-red-600 transition-colors block truncate">{{ $claim->item->title }}</a>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[8px] font-black uppercase border tracking-tight mt-1.5
                                {{ $claim->item->type === 'lost' ? 'bg-red-50 text-red-600 border-red-100' : 'bg-green-50 text-green-600 border-green-100' }}">
                                {{ $claim->item->type === 'lost' ? 'Hilang' : 'Temuan' }}
                            </span>
                        </div>
                    </div>

                    <div class="space-y-3.5 border-t border-slate-50 pt-5 text-xs font-semibold">
                        <div class="flex justify-between">
                            <span class="text-slate-400 font-bold uppercase tracking-wider">Kategori</span>
                            <span class="text-slate-700">{{ $claim->item->category->name ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400 font-bold uppercase tracking-wider">Lokasi</span>
                            <span class="text-slate-700">{{ $claim->item->location }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400 font-bold uppercase tracking-wider">Tanggal Lapor</span>
                            <span class="text-slate-700">{{ $claim->item->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400 font-bold uppercase tracking-wider">Status Barang</span>
                            <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase
                                {{ $claim->item->status === 'active' ? 'bg-amber-50 text-amber-600 border border-amber-100' : 
                                   ($claim->item->status === 'claimed' ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-green-50 text-green-600 border border-green-100') }}">
                                {{ $claim->item->status }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-6 pt-5 border-t border-slate-50">
                        <a href="{{ route('admin.items.show', $claim->item_id) }}" class="w-full justify-center inline-flex items-center gap-1.5 px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-xl transition-all border border-slate-200 cursor-pointer">
                            Lihat Rincian Laporan Barang <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                @else
                    <div class="text-slate-400 text-xs py-4 text-center font-medium">Barang terkait tidak ditemukan atau telah dihapus.</div>
                @endif
            </div>

            <!-- Rival Claims List -->
            @if($otherClaims->isNotEmpty())
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                    <h4 class="font-bold text-slate-800 text-sm tracking-tight mb-5 flex items-center gap-2">
                        <i data-lucide="file-warning" class="w-4 h-4 text-amber-500"></i>
                        Klaim Tandingan Lainnya ({{ $otherClaims->count() }})
                    </h4>

                    <div class="space-y-4 max-h-[350px] overflow-y-auto pr-1">
                        @foreach($otherClaims as $oth)
                            <div class="p-4 bg-slate-50/50 hover:bg-slate-50 border border-slate-100 rounded-2xl transition-all flex flex-col gap-2.5 group relative">
                                <div class="flex items-center gap-2 justify-between">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-[10px] shrink-0">
                                            {{ collect(explode(' ', $oth->claimer->name ?? 'User'))->map(fn($n) => strtoupper(substr($n, 0, 1)))->take(2)->implode('') }}
                                        </div>
                                        <div class="min-w-0">
                                            <a href="{{ route('admin.claims.show', $oth->id) }}" class="text-xs font-bold text-slate-700 truncate block hover:text-blue-600 transition-colors">{{ $oth->claimer->name ?? 'User' }}</a>
                                            <span class="text-[9px] text-slate-400 block font-medium">{{ $oth->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase shrink-0
                                        {{ $oth->status === 'pending' ? 'bg-amber-50 text-amber-600 border border-amber-100' : 
                                           ($oth->status === 'approved' ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-red-50 text-red-600 border border-red-100') }}">
                                        {{ $oth->status }}
                                    </span>
                                </div>
                                <p class="text-[10px] text-slate-500 leading-relaxed line-clamp-2">"{{ $oth->reason }}"</p>
                                <div class="flex justify-end border-t border-slate-100/50 pt-2">
                                    <a href="{{ route('admin.claims.show', $oth->id) }}" class="text-[9px] font-black text-slate-500 hover:text-blue-600 transition-colors uppercase tracking-wider flex items-center gap-1 cursor-pointer">
                                        Periksa <i data-lucide="arrow-right" class="w-3 h-3"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

    </div>

    <!-- Rejection Reason Modal (Alpine.js) -->
    <template x-teleport="body">
        <div class="fixed inset-0 z-[1100] flex items-center justify-center p-4 bg-black/50" 
             x-show="showRejectModal" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @keydown.escape.window="showRejectModal = false"
             x-cloak>
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden"
                 @click.outside="showRejectModal = false"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="scale-95 opacity-0"
                 x-transition:enter-end="scale-100 opacity-100">
                
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h5 class="font-bold text-slate-800 text-sm tracking-tight flex items-center gap-2">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-red-500"></i>
                        Tolak Pengajuan Klaim
                    </h5>
                    <button type="button" class="text-slate-400 hover:text-slate-600 transition-colors" @click="showRejectModal = false">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <form action="{{ route('admin.claims.reject', $claim->id) }}" method="POST">
                    @csrf
                    <div class="p-6 space-y-4">
                        <p class="text-xs text-slate-500 font-medium leading-relaxed">
                            Masukkan alasan penolakan pengajuan klaim ini. Alasan ini akan dikirimkan kepada pelapor sebagai feedback penolakan.
                        </p>
                        
                        <div>
                            <label for="reason" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Alasan Penolakan</label>
                            <textarea
                                id="reason"
                                name="reason"
                                rows="4"
                                class="w-full px-3 py-2 text-sm text-slate-800 placeholder:text-slate-400 border border-slate-200 rounded-xl outline-none focus:border-red-500 focus:ring-4 focus:ring-red-500/5 transition-all font-medium"
                                placeholder="Contoh: Deskripsi bukti kepemilikan kurang spesifik dan tidak sesuai dengan barang temuan asli..."
                                required
                                minlength="5"
                                maxlength="1000"></textarea>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-3 bg-slate-50/50">
                        <button type="button" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold uppercase tracking-wider hover:bg-slate-100 transition-all cursor-pointer" @click="showRejectModal = false">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white text-xs font-black uppercase tracking-wider rounded-xl transition-all shadow-md active:scale-95 flex items-center gap-1.5 cursor-pointer">
                            <i data-lucide="x" class="w-4 h-4"></i> Tolak Klaim
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>
@endsection

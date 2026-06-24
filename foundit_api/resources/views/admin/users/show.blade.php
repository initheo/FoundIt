@extends('admin.layout.app')

@section('title', 'Detail Pengguna')

@section('content')
<div class="space-y-6 pb-8 max-w-[1400px] mx-auto">
    <!-- Page Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.users.index') }}" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-all cursor-pointer">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Detail Pengguna</h2>
            <p class="text-slate-500 text-xs mt-1 font-medium tracking-tight">Informasi akun, histori pelaporan barang, dan pengajuan klaim</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <!-- Left Column: User Profile Info Card -->
        <div class="xl:col-span-4 space-y-6">
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden p-6 text-center">
                <!-- Avatar -->
                <div class="w-24 h-24 rounded-3xl bg-red-50 text-red-600 border border-red-100 flex items-center justify-center font-bold text-3xl shadow-inner mx-auto mb-4">
                    {{ collect(explode(' ', $user->name))->map(fn($n) => strtoupper(substr($n, 0, 1)))->take(2)->implode('') }}
                </div>
                
                <h3 class="text-lg font-bold text-slate-800 leading-tight mb-1">{{ $user->name }}</h3>
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold border shadow-sm uppercase tracking-tighter mb-6
                    {{ $user->role === 'admin' ? 'bg-red-50 text-red-600 border-red-100' : 'bg-slate-50 text-slate-600 border-slate-100' }}">
                    {{ $user->role }}
                </span>

                <!-- Profile details -->
                <div class="text-left space-y-4 border-t border-slate-50 pt-6">
                    <div>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-0.5">Email</span>
                        <span class="text-sm font-semibold text-slate-700 block break-all">{{ $user->email }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-0.5">Program Studi / Unit</span>
                        <span class="text-sm font-semibold text-slate-700 block">{{ $user->prodi_unit ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-0.5">No. Telepon</span>
                        <span class="text-sm font-semibold text-slate-700 block">{{ $user->phone ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-0.5">Terdaftar Sejak</span>
                        <div class="flex items-center gap-2 mt-0.5">
                            <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
                            <span class="text-xs font-bold text-slate-600">{{ $user->created_at->format('d F Y H:i') }}</span>
                        </div>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wide block mt-1 ml-6">({{ $user->created_at->diffForHumans() }})</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-2 mt-8 pt-6 border-t border-slate-50">
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="flex-1 inline-flex justify-center items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold uppercase tracking-wider rounded-xl transition-all border border-slate-200 cursor-pointer">
                        <i data-lucide="edit-3" class="w-4 h-4"></i> Edit Profil
                    </a>
                    @if(auth()->id() !== $user->id)
                        <button type="button"
                                onclick="confirmDelete('{{ route('admin.users.destroy', $user->id) }}', 'Apakah Anda yakin ingin menghapus pengguna {{ $user->name }}? Data pengguna yang memiliki relasi laporan atau klaim tidak dapat dihapus.')"
                                class="inline-flex justify-center items-center p-2.5 bg-rose-50 hover:bg-rose-100 border border-rose-100 text-rose-600 rounded-xl transition-all cursor-pointer"
                                title="Hapus Pengguna">
                            <i data-lucide="trash-2" class="w-5 h-5"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: History Tabs (Reported Items & Claims) -->
        <div class="xl:col-span-8 space-y-6" x-data="{ activeTab: 'items' }">
            <!-- Tabs Header -->
            <div class="flex border-b border-slate-200">
                <button @click="activeTab = 'items'" 
                        :class="activeTab === 'items' ? 'border-red-500 text-red-600' : 'border-transparent text-slate-500 hover:text-slate-700'"
                        class="px-6 py-3 border-b-2 font-bold text-sm transition-all focus:outline-none flex items-center gap-2 cursor-pointer">
                    <i data-lucide="package" class="w-4 h-4"></i>
                    Laporan Barang ({{ $user->items->count() }})
                </button>
                <button @click="activeTab = 'claims'" 
                        :class="activeTab === 'claims' ? 'border-red-500 text-red-600' : 'border-transparent text-slate-500 hover:text-slate-700'"
                        class="px-6 py-3 border-b-2 font-bold text-sm transition-all focus:outline-none flex items-center gap-2 cursor-pointer">
                    <i data-lucide="file-check-2" class="w-4 h-4"></i>
                    Klaim Barang ({{ $user->claims->count() }})
                </button>
            </div>

            <!-- Tab Contents: Reported Items -->
            <div x-show="activeTab === 'items'" class="space-y-4">
                @forelse($user->items as $item)
                    <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between group transition-all hover:shadow-md">
                        <div class="flex items-center gap-4 min-w-0">
                            <!-- Image/Icon placeholder -->
                            <div class="w-16 h-16 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 font-bold border border-slate-100 shadow-sm shrink-0 overflow-hidden group-hover:border-red-200 group-hover:bg-red-50/20 transition-all">
                                @if($item->photos->isNotEmpty())
                                    <img src="{{ asset('storage/' . $item->photos->first()->photo_url) }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                                @else
                                    <i data-lucide="image" class="w-6 h-6 text-slate-300"></i>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-sm font-bold text-slate-800 truncate mb-1 group-hover:text-red-600 transition-colors">
                                    <a href="{{ route('admin.items.show', $item->id) }}">{{ $item->title }}</a>
                                </h4>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase {{ $item->type === 'lost' ? 'bg-red-50 text-red-600 border border-red-100' : 'bg-green-50 text-green-600 border border-green-100' }}">
                                        {{ $item->type === 'lost' ? 'Hilang' : 'Temuan' }}
                                    </span>
                                    <span class="text-xs text-slate-400 font-semibold flex items-center gap-1">
                                        <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                                        {{ $item->location }}
                                    </span>
                                </div>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tight mt-1 block">Dilaporkan pada {{ $item->created_at->format('d M Y') }} ({{ $item->created_at->diffForHumans() }})</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-4 w-full sm:w-auto justify-between sm:justify-end border-t sm:border-t-0 border-slate-50 pt-3 sm:pt-0 shrink-0">
                            <span class="px-3 py-1 rounded-xl text-xs font-bold uppercase tracking-tight
                                {{ $item->status === 'active' ? 'bg-amber-50 text-amber-600 border border-amber-100' : 
                                   ($item->status === 'claimed' ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-green-50 text-green-600 border border-green-100') }}">
                                {{ $item->status }}
                            </span>
                            <a href="{{ route('admin.items.show', $item->id) }}" class="inline-flex p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all border border-transparent hover:border-red-100 cursor-pointer">
                                <i data-lucide="chevron-right" class="w-5 h-5"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-12 text-center">
                        <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="package" class="w-8 h-8"></i>
                        </div>
                        <h5 class="text-sm font-bold text-slate-500 mb-1">Belum Ada Laporan</h5>
                        <p class="text-xs text-slate-400 max-w-xs mx-auto">Pengguna ini belum pernah melaporkan kehilangan atau penemuan barang.</p>
                    </div>
                @endforelse
            </div>

            <!-- Tab Contents: Claims -->
            <div x-show="activeTab === 'claims'" class="space-y-4" x-cloak>
                @forelse($user->claims as $claim)
                    <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between group transition-all hover:shadow-md">
                        <div class="flex items-center gap-4 min-w-0">
                            <!-- Image/Icon placeholder -->
                            <div class="w-16 h-16 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 font-bold border border-slate-100 shadow-sm shrink-0 overflow-hidden group-hover:border-blue-200 group-hover:bg-blue-50/20 transition-all">
                                @if($claim->item && $claim->item->photos->isNotEmpty())
                                    <img src="{{ asset('storage/' . $claim->item->photos->first()->photo_url) }}" alt="{{ $claim->item->title }}" class="w-full h-full object-cover">
                                @else
                                    <i data-lucide="image" class="w-6 h-6 text-slate-300"></i>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-sm font-bold text-slate-800 truncate mb-1 group-hover:text-blue-600 transition-colors">
                                    @if($claim->item)
                                        <a href="{{ route('admin.claims.show', $claim->id) }}">{{ $claim->item->title }}</a>
                                    @else
                                        Barang Terhapus
                                    @endif
                                </h4>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-xs text-slate-500 font-medium truncate max-w-[280px]">
                                        Alasan: "{{ Str::limit($claim->reason, 60) }}"
                                    </span>
                                </div>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tight mt-1 block">Diajukan pada {{ $claim->created_at->format('d M Y') }} ({{ $claim->created_at->diffForHumans() }})</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-4 w-full sm:w-auto justify-between sm:justify-end border-t sm:border-t-0 border-slate-50 pt-3 sm:pt-0 shrink-0">
                            <span class="px-3 py-1 rounded-xl text-xs font-bold uppercase tracking-tight
                                {{ $claim->status === 'pending' ? 'bg-amber-50 text-amber-600 border border-amber-100' : 
                                   ($claim->status === 'approved' ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-red-50 text-red-600 border border-red-100') }}">
                                {{ $claim->status }}
                            </span>
                            <a href="{{ route('admin.claims.show', $claim->id) }}" class="inline-flex p-2 text-slate-400 hover:text-blue-500 hover:bg-blue-50 rounded-xl transition-all border border-transparent hover:border-blue-100 cursor-pointer">
                                <i data-lucide="chevron-right" class="w-5 h-5"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-12 text-center">
                        <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="file-check-2" class="w-8 h-8"></i>
                        </div>
                        <h5 class="text-sm font-bold text-slate-500 mb-1">Belum Ada Pengajuan Klaim</h5>
                        <p class="text-xs text-slate-400 max-w-xs mx-auto">Pengguna ini belum pernah mengajukan klaim untuk barang temuan.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

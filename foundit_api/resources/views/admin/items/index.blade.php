@extends('admin.layout.app')

@section('title', 'Kelola Laporan Barang')

@section('content')
<div class="space-y-6 pb-8 max-w-[1400px] mx-auto">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Kelola Laporan Barang</h2>
            <p class="text-slate-600 text-xs mt-1 font-semibold tracking-tight">Daftar penemuan dan kehilangan barang di lingkungan kampus</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.items.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold uppercase tracking-wider rounded-xl transition-all shadow-lg shadow-red-500/20 active:scale-95 cursor-pointer">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Laporan
            </a>
        </div>
    </div>

    <!-- Filter Bar Card -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <form method="GET" action="{{ route('admin.items.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Search -->
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-red-500 transition-colors">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                <input type="text" 
                       name="search" 
                       class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-4 focus:ring-red-500/5 focus:border-red-500 transition-all focus:bg-white shadow-sm"
                       placeholder="Cari barang, deskripsi, lokasi..." 
                       value="{{ request('search') }}">
            </div>

            <!-- Type -->
            <div>
                <select name="type" 
                        onchange="this.form.submit()"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 px-4 py-2.5 focus:outline-none focus:border-red-500 focus:bg-white shadow-sm cursor-pointer appearance-none">
                    <option value="">Semua Tipe</option>
                    <option value="lost" {{ request('type') === 'lost' ? 'selected' : '' }}>Kehilangan (Lost)</option>
                    <option value="found" {{ request('type') === 'found' ? 'selected' : '' }}>Temuan (Found)</option>
                </select>
            </div>

            <!-- Category -->
            <div>
                <select name="category_id" 
                        onchange="this.form.submit()"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 px-4 py-2.5 focus:outline-none focus:border-red-500 focus:bg-white shadow-sm cursor-pointer appearance-none">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status -->
            <div>
                <select name="status" 
                        onchange="this.form.submit()"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 px-4 py-2.5 focus:outline-none focus:border-red-500 focus:bg-white shadow-sm cursor-pointer appearance-none">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif (Active)</option>
                    <option value="claimed" {{ request('status') === 'claimed' ? 'selected' : '' }}>Klaim Diproses (Claimed)</option>
                    <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Selesai / Dikembalikan (Returned)</option>
                </select>
            </div>

            <!-- Reset Actions / Status Indicator -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition-all shadow-sm cursor-pointer">
                    Filter
                </button>
                @if(request('search') || request('type') || request('category_id') || request('status'))
                    <a href="{{ route('admin.items.index') }}" class="p-2.5 bg-rose-50 border border-rose-100 hover:bg-rose-100 text-rose-600 rounded-xl transition-all cursor-pointer" title="Reset Filter">
                        <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Items Table Card -->
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-50 flex items-center justify-between bg-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-50 text-red-600 rounded-lg flex items-center justify-center shadow-inner">
                    <i data-lucide="package" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 text-base leading-tight">Daftar Laporan Barang</h4>
                    <p class="text-[10px] text-slate-500 font-black uppercase tracking-widest mt-0.5">{{ $items->total() }} TOTAL LAPORAN</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto card-datatable">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-slate-50">
                        <th class="px-6 py-4 text-[11px] font-extrabold text-slate-500 uppercase tracking-widest">Informasi Barang</th>
                        <th class="px-6 py-4 text-[11px] font-extrabold text-slate-500 uppercase tracking-widest">Pelapor</th>
                        <th class="px-6 py-4 text-[11px] font-extrabold text-slate-500 uppercase tracking-widest">Waktu & Lokasi Kejadian</th>
                        <th class="px-6 py-4 text-[11px] font-extrabold text-slate-500 uppercase tracking-widest text-center">Tipe</th>
                        <th class="px-6 py-4 text-[11px] font-extrabold text-slate-500 uppercase tracking-widest text-center">Status</th>
                        <th class="px-6 py-4 text-[11px] font-extrabold text-slate-500 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr class="group hover:bg-slate-50/50 transition-colors border-b border-slate-50">
                        <!-- Item detail -->
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.items.show', $item->id) }}" class="flex items-center gap-3 group/item">
                                <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 overflow-hidden shrink-0 shadow-sm transition-all group-hover/item:border-red-100 group-hover/item:bg-red-50/20">
                                    @if($item->photos->isNotEmpty())
                                        <img src="{{ asset('storage/' . $item->photos->first()->photo_url) }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                                    @else
                                        <i data-lucide="image" class="w-5 h-5 text-slate-300"></i>
                                    @endif
                                </div>
                                <div class="flex flex-col min-w-0 max-w-[200px]">
                                    <span class="text-sm font-bold text-slate-900 leading-tight group-hover/item:text-red-600 transition-colors truncate">{{ $item->title }}</span>
                                    <span class="inline-flex items-center gap-1 text-[10px] text-slate-500 font-extrabold uppercase tracking-wide mt-1">
                                        <i data-lucide="tag" class="w-3 h-3 text-slate-400"></i>
                                        {{ $item->category->name ?? 'Tanpa Kategori' }}
                                    </span>
                                </div>
                            </a>
                        </td>

                        <!-- Reporter -->
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-slate-800 leading-tight">{{ $item->user->name ?? 'User' }}</span>
                                <span class="text-[10px] text-slate-500 font-bold mt-0.5">{{ $item->user->email ?? '-' }}</span>
                            </div>
                        </td>

                        <!-- Location & Time -->
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-xs font-semibold text-slate-800 flex items-center gap-1">
                                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-500 shrink-0"></i>
                                    {{ $item->location }}
                                </span>
                                <span class="text-[10px] text-slate-500 font-extrabold uppercase tracking-wider mt-1 flex items-center gap-1">
                                    <i data-lucide="clock" class="w-3 h-3 text-slate-400 shrink-0"></i>
                                    {{ \Carbon\Carbon::parse($item->date_time)->format('d M Y H:i') }}
                                </span>
                            </div>
                        </td>

                        <!-- Type Badge -->
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold border shadow-sm uppercase tracking-tighter
                                {{ $item->type === 'lost' ? 'bg-red-50 text-red-600 border-red-100' : 'bg-green-50 text-green-600 border-green-100' }}">
                                {{ $item->type === 'lost' ? 'Hilang (Lost)' : 'Temuan (Found)' }}
                            </span>
                        </td>

                        <!-- Status Badge -->
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold border shadow-sm uppercase tracking-tighter
                                {{ $item->status === 'active' ? 'bg-amber-50 text-amber-600 border border-amber-100' : 
                                   ($item->status === 'claimed' ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-green-50 text-green-600 border border-green-100') }}">
                                {{ $item->status }}
                            </span>
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 text-right text-nowrap space-x-1">
                            <a href="{{ route('admin.items.show', $item->id) }}" 
                               class="inline-flex p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all border border-transparent hover:border-red-100 cursor-pointer"
                               title="Lihat Detail">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                            <a href="{{ route('admin.items.edit', $item->id) }}" 
                               class="inline-flex p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all border border-transparent hover:border-red-100 cursor-pointer"
                               title="Edit Laporan">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </a>
                            <button type="button"
                                    onclick="confirmDelete('{{ route('admin.items.destroy', $item->id) }}', 'Apakah Anda yakin ingin menghapus laporan {{ $item->title }}? Semua foto dan relasi klaim yang terhubung dengan laporan ini akan ikut terhapus.')"
                                    class="inline-flex p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all border border-transparent hover:border-red-100 cursor-pointer"
                                    title="Hapus Laporan">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-20 h-20 bg-slate-50 text-slate-200 rounded-3xl flex items-center justify-center mb-6 shadow-inner">
                                    <i data-lucide="package" class="w-10 h-10"></i>
                                </div>
                                <div>
                                    <p class="text-slate-500 font-bold text-sm tracking-tight">
                                        @if(request('search') || request('type') || request('category_id') || request('status'))
                                            Tidak ditemukan
                                        @else
                                            Belum ada laporan barang
                                        @endif
                                    </p>
                                    <p class="text-slate-500 text-xs mt-1 font-medium">
                                        @if(request('search') || request('type') || request('category_id') || request('status'))
                                            Tidak ada laporan barang dengan filter / kata kunci pencarian Anda
                                        @else
                                            Belum ada laporan barang hilang atau temuan yang masuk ke dalam sistem.
                                        @endif
                                    </p>
                                </div>
                                @if(request('search') || request('type') || request('category_id') || request('status'))
                                <a href="{{ route('admin.items.index') }}" class="mt-4 inline-flex items-center gap-2 bg-slate-100 text-slate-600 px-6 py-2 rounded-lg font-bold text-sm hover:bg-slate-200 transition-all border border-slate-200">
                                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                                    Reset Filter & Pencarian
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($items->hasPages())
        <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-50">
            {{ $items->appends(request()->query())->onEachSide(1)->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@extends('admin.layout.app')

@section('title', 'Kelola Klaim Barang')

@section('content')
<div class="space-y-6 pb-8 max-w-[1400px] mx-auto">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Kelola Klaim Barang</h2>
            <p class="text-slate-500 text-xs mt-1 font-medium tracking-tight">Tinjau, setujui, atau tolak klaim kepemilikan barang temuan</p>
        </div>
    </div>

    <!-- Filter Bar Card -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <form method="GET" action="{{ route('admin.claims.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Search -->
            <div class="relative group sm:col-span-2">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-red-500 transition-colors">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                <input type="text" 
                       name="search" 
                       class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-4 focus:ring-red-500/5 focus:border-red-500 transition-all focus:bg-white shadow-sm"
                       placeholder="Cari pengaju, email, atau judul barang..." 
                       value="{{ request('search') }}">
            </div>

            <!-- Status & Submit -->
            <div class="flex items-center gap-2">
                <select name="status" 
                        onchange="this.form.submit()"
                        class="flex-1 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium px-4 py-2.5 focus:outline-none focus:border-red-500 focus:bg-white shadow-sm cursor-pointer appearance-none">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>

                @if(request('search') || request('status'))
                    <a href="{{ route('admin.claims.index') }}" class="p-2.5 bg-rose-50 border border-rose-100 hover:bg-rose-100 text-rose-600 rounded-xl transition-all cursor-pointer shadow-sm shrink-0" title="Reset Filter">
                        <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Claims Table Card -->
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-50 flex items-center justify-between bg-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-50 text-red-600 rounded-lg flex items-center justify-center shadow-inner">
                    <i data-lucide="file-check-2" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-800 text-base leading-tight">Daftar Pengajuan Klaim</h4>
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">{{ $claims->total() }} TOTAL PENGAJUAN</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto card-datatable">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-slate-50">
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Pengaju Klaim</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Barang yang Diklaim</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Alasan / Bukti Singkat</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-right">Waktu Pengajuan</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">Status</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($claims as $claim)
                    <tr class="group hover:bg-slate-50/50 transition-colors border-b border-slate-50">
                        <!-- Claimant -->
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($claim->claimer->photo_url)
                                <img src="{{ asset($claim->claimer->photo_url) }}" alt="{{ $claim->claimer->name }}" class="w-10 h-10 rounded-xl object-cover border border-blue-100 shadow-inner shrink-0">
                                @else
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm border border-blue-100 shadow-inner shrink-0">
                                    {{ collect(explode(' ', $claim->claimer->name ?? 'User'))->map(fn($n) => strtoupper(substr($n, 0, 1)))->take(2)->implode('') }}
                                </div>
                                @endif
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-700 leading-tight">{{ $claim->claimer->name ?? 'User' }}</span>
                                    <span class="text-[10px] text-slate-400 font-medium mt-0.5">{{ $claim->claimer->email ?? '-' }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- Item Claimed -->
                        <td class="px-6 py-4">
                            @if($claim->item)
                                <a href="{{ route('admin.items.show', $claim->item->id) }}" class="flex items-center gap-3 group/item">
                                    <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 overflow-hidden shrink-0 shadow-sm transition-all group-hover/item:border-red-100 group-hover/item:bg-red-50/20">
                                        @if($claim->item->photos->isNotEmpty())
                                            <img src="{{ asset('storage/' . $claim->item->photos->first()->photo_url) }}" alt="{{ $claim->item->title }}" class="w-full h-full object-cover">
                                        @else
                                            <i data-lucide="image" class="w-5 h-5 text-slate-300"></i>
                                        @endif
                                    </div>
                                    <span class="text-sm font-bold text-slate-700 leading-tight group-hover/item:text-red-600 transition-colors truncate max-w-[150px]">{{ $claim->item->title }}</span>
                                </a>
                            @else
                                <span class="text-xs text-slate-400 font-semibold italic">Barang Telah Dihapus</span>
                            @endif
                        </td>

                        <!-- Reason -->
                        <td class="px-6 py-4">
                            <span class="text-xs font-semibold text-slate-600 line-clamp-2 max-w-[250px]" title="{{ $claim->reason }}">
                                "{{ $claim->reason }}"
                            </span>
                        </td>

                        <!-- Submission Date -->
                        <td class="px-6 py-4 text-right">
                            <div class="flex flex-col items-end">
                                <span class="text-xs font-bold text-slate-600 tracking-tight">{{ $claim->created_at->format('d M Y H:i') }}</span>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">{{ $claim->created_at->diffForHumans() }}</span>
                            </div>
                        </td>

                        <!-- Status Badge -->
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold border shadow-sm uppercase tracking-tighter
                                {{ $claim->status === 'pending' ? 'bg-amber-50 text-amber-600 border border-amber-100' : 
                                   ($claim->status === 'approved' ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-red-50 text-red-600 border border-red-100') }}">
                                {{ $claim->status }}
                            </span>
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 text-right text-nowrap">
                            <a href="{{ route('admin.claims.show', $claim->id) }}" 
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 border border-slate-200 hover:bg-red-50 hover:text-red-600 hover:border-red-100 text-slate-600 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all cursor-pointer shadow-sm active:scale-95"
                               title="Tinjau Detail Klaim">
                                Tinjau <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-20 h-20 bg-slate-50 text-slate-200 rounded-3xl flex items-center justify-center mb-6 shadow-inner">
                                    <i data-lucide="file-check-2" class="w-10 h-10"></i>
                                </div>
                                <div>
                                    <p class="text-slate-500 font-bold text-sm tracking-tight">
                                        @if(request('search') || request('status'))
                                            Tidak ditemukan
                                        @else
                                            Belum ada klaim barang
                                        @endif
                                    </p>
                                    <p class="text-slate-500 text-xs mt-1 font-medium">
                                        @if(request('search') || request('status'))
                                            Tidak ada klaim dengan filter / kata kunci pencarian Anda
                                        @else
                                            Belum ada pengguna yang mengajukan klaim atas penemuan barang.
                                        @endif
                                    </p>
                                </div>
                                @if(request('search') || request('status'))
                                <a href="{{ route('admin.claims.index') }}" class="mt-4 inline-flex items-center gap-2 bg-slate-100 text-slate-600 px-6 py-2 rounded-lg font-bold text-sm hover:bg-slate-200 transition-all border border-slate-200">
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
        
        @if($claims->hasPages())
        <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-50">
            {{ $claims->appends(request()->query())->onEachSide(1)->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

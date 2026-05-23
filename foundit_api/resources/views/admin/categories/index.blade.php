@extends('admin.layout.app')

@section('title', 'Kategori Barang')

@section('content')
<div class="space-y-6 pb-8 max-w-[1400px] mx-auto">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Kategori Barang</h2>
            <p class="text-slate-500 text-xs mt-1 font-medium tracking-tight">Kelola kategori barang untuk mempermudah kategorisasi lost & found</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 md:gap-3 w-full md:w-auto mt-4 md:mt-0">
            <form method="GET" class="relative group flex-1 md:flex-none min-w-[300px]">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-red-500 transition-colors">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                <input type="text" 
                       name="search" 
                       class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-medium focus:outline-none focus:ring-4 focus:ring-red-500/5 focus:border-red-500 transition-all shadow-sm"
                       placeholder="Cari nama kategori..." 
                       value="{{ request('search') }}">
                @if(request('search'))
                    <a href="{{ route('admin.categories.index') }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </a>
                @endif
            </form>
            <a href="{{ route('admin.categories.create') }}" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white text-xs font-bold uppercase tracking-wider rounded-xl transition-all shadow-lg shadow-red-500/20 active:scale-95 flex items-center gap-2 cursor-pointer">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Kategori
            </a>
        </div>
    </div>

    <!-- Categories Table Card -->
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-50 flex items-center justify-between bg-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-50 text-red-600 rounded-lg flex items-center justify-center shadow-inner">
                    <i data-lucide="tag" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-800 text-base leading-tight">Daftar Kategori</h4>
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">{{ $categories->total() }} TOTAL KATEGORI</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto card-datatable">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-slate-50">
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Kategori</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">Lucide Icon Class</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">Jumlah Barang</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr class="group hover:bg-slate-50/50 transition-colors border-b border-slate-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center font-bold text-sm border border-red-100 shadow-inner shrink-0">
                                    <i data-lucide="{{ $category->icon ?? 'tag' }}" class="w-5 h-5"></i>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-700 leading-tight">{{ $category->name }}</span>
                                    <span class="text-[10px] text-slate-400 font-medium mt-0.5">Dibuat {{ $category->created_at->format('d M Y') }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <code class="text-xs px-2 py-1 bg-slate-50 border border-slate-100 rounded text-slate-600">{{ $category->icon }}</code>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-50 text-red-600 rounded-lg text-[10px] font-bold border border-red-100 shadow-sm uppercase">
                                {{ $category->items()->count() }} Item
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-nowrap space-x-1">
                            <a href="{{ route('admin.categories.edit', $category->id) }}" 
                               class="inline-flex p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all border border-transparent hover:border-red-100 cursor-pointer"
                               title="Edit Kategori">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </a>
                            <button type="button"
                                    onclick="confirmDelete('{{ route('admin.categories.destroy', $category->id) }}', 'Apakah Anda yakin ingin menghapus kategori {{ $category->name }}? Kategori yang masih terhubung dengan item tidak dapat dihapus.')"
                                    class="inline-flex p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all border border-transparent hover:border-red-100 cursor-pointer"
                                    title="Hapus Kategori">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-20 h-20 bg-slate-50 text-slate-200 rounded-3xl flex items-center justify-center mb-6 shadow-inner">
                                    <i data-lucide="tag" class="w-10 h-10"></i>
                                </div>
                                <div>
                                    <p class="text-slate-500 font-bold text-sm tracking-tight">
                                        @if(request('search'))
                                            Tidak ditemukan
                                        @else
                                            Belum ada kategori
                                        @endif
                                    </p>
                                    <p class="text-slate-500 text-xs mt-1 font-medium">
                                        @if(request('search'))
                                            Tidak ada kategori dengan nama "{{ request('search') }}"
                                        @else
                                            Belum ada kategori yang ditambahkan. Silakan klik tombol Tambah Kategori di atas.
                                        @endif
                                    </p>
                                </div>
                                @if(request('search'))
                                <a href="{{ route('admin.categories.index') }}" class="mt-4 inline-flex items-center gap-2 bg-slate-100 text-slate-600 px-6 py-2 rounded-lg font-bold text-sm hover:bg-slate-200 transition-all border border-slate-200">
                                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                                    Lihat Semua
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($categories->hasPages())
        <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-50">
            {{ $categories->appends(request()->query())->onEachSide(1)->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

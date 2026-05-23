@extends('admin.layout.app')

@section('title', 'Kelola Pengguna')

@section('content')
<div class="space-y-6 pb-8 max-w-[1400px] mx-auto">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Kelola Pengguna</h2>
            <p class="text-slate-500 text-xs mt-1 font-medium tracking-tight">Kelola akun pengguna, prodi/unit, dan peran (role) sistem</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 md:gap-3 w-full md:w-auto mt-4 md:mt-0">
            <form method="GET" class="relative group flex-1 md:flex-none min-w-[300px]">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-red-500 transition-colors">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                <input type="text" 
                       name="search" 
                       class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-medium focus:outline-none focus:ring-4 focus:ring-red-500/5 focus:border-red-500 transition-all shadow-sm"
                       placeholder="Cari nama, email, prodi..." 
                       value="{{ request('search') }}">
                @if(request('search'))
                    <a href="{{ route('admin.users.index') }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </a>
                @endif
            </form>
            <form method="GET" class="flex-1 md:flex-none">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                <select name="role" 
                        onchange="this.form.submit()"
                        class="bg-white border border-slate-200 rounded-lg text-sm font-medium px-4 py-2 focus:outline-none focus:border-red-500 shadow-sm cursor-pointer">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-50 flex items-center justify-between bg-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-50 text-red-600 rounded-lg flex items-center justify-center shadow-inner">
                    <i data-lucide="users" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-800 text-base leading-tight">Daftar Pengguna</h4>
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">{{ $users->total() }} TOTAL PENGGUNA</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto card-datatable">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-slate-50">
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Pengguna</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Prodi / Unit</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">No. Telepon</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">Role</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-right">Terdaftar</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr class="group hover:bg-slate-50/50 transition-colors border-b border-slate-50">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.users.show', $user->id) }}" class="flex items-center gap-3 group/user" title="Lihat detail {{ $user->name }}">
                                @if($user->photo_url)
                                <img src="{{ asset($user->photo_url) }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-xl object-cover border border-red-100 shadow-inner shrink-0">
                                @else
                                <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center font-bold text-sm border border-red-100 shadow-inner shrink-0">
                                    {{ collect(explode(' ', $user->name))->map(fn($n) => strtoupper(substr($n, 0, 1)))->take(2)->implode('') }}
                                </div>
                                @endif
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-700 leading-tight group-hover:text-red-600 transition-colors">{{ $user->name }}</span>
                                    <span class="text-[10px] text-slate-400 font-medium mt-0.5">{{ $user->email }}</span>
                                </div>
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-semibold text-slate-600">{{ $user->prodi_unit ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-semibold text-slate-600">{{ $user->phone ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold border shadow-sm uppercase tracking-tighter
                                {{ $user->role === 'admin' ? 'bg-red-50 text-red-600 border-red-100' : 'bg-slate-50 text-slate-600 border-slate-100' }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex flex-col items-end">
                                <span class="text-xs font-bold text-slate-600 tracking-tight">{{ $user->created_at->format('d M Y') }}</span>
                                <span class="text-[10px] text-slate-300 font-bold uppercase tracking-widest mt-0.5">{{ $user->created_at->diffForHumans() }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right text-nowrap space-x-1">
                            <a href="{{ route('admin.users.show', $user->id) }}" 
                               class="inline-flex p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all border border-transparent hover:border-red-100 cursor-pointer"
                               title="Lihat Detail">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                            <a href="{{ route('admin.users.edit', $user->id) }}" 
                               class="inline-flex p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all border border-transparent hover:border-red-100 cursor-pointer"
                               title="Edit Pengguna">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </a>
                            @if(auth()->id() !== $user->id)
                                <button type="button"
                                        onclick="confirmDelete('{{ route('admin.users.destroy', $user->id) }}', 'Apakah Anda yakin ingin menghapus pengguna {{ $user->name }}? Data pengguna yang memiliki relasi laporan atau klaim tidak dapat dihapus.')"
                                        class="inline-flex p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all border border-transparent hover:border-red-100 cursor-pointer"
                                        title="Hapus Pengguna">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-20 h-20 bg-slate-50 text-slate-200 rounded-3xl flex items-center justify-center mb-6 shadow-inner">
                                    <i data-lucide="users" class="w-10 h-10"></i>
                                </div>
                                <div>
                                    <p class="text-slate-500 font-bold text-sm tracking-tight">
                                        @if(request('search') || request('role'))
                                            Tidak ditemukan
                                        @else
                                            Belum ada pengguna
                                        @endif
                                    </p>
                                    <p class="text-slate-500 text-xs mt-1 font-medium">
                                        @if(request('search') || request('role'))
                                            Tidak ada pengguna dengan filter / kata kunci pencarian Anda
                                        @else
                                            Belum ada pengguna yang mendaftar ke sistem.
                                        @endif
                                    </p>
                                </div>
                                @if(request('search') || request('role'))
                                <a href="{{ route('admin.users.index') }}" class="mt-4 inline-flex items-center gap-2 bg-slate-100 text-slate-600 px-6 py-2 rounded-lg font-bold text-sm hover:bg-slate-200 transition-all border border-slate-200">
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
        
        @if($users->hasPages())
        <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-50">
            {{ $users->appends(request()->query())->onEachSide(1)->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

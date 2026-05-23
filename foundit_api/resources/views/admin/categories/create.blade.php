@extends('admin.layout.app')

@section('title', 'Tambah Kategori')

@section('content')
<div class="space-y-6 pb-8 max-w-[1400px] mx-auto">
    <!-- Page Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.categories.index') }}" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-all cursor-pointer">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Tambah Kategori</h2>
            <p class="text-slate-500 text-xs mt-1 font-medium tracking-tight">Tambahkan kategori barang baru ke sistem</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden p-6">
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 border-l-4 border-l-red-500 rounded-lg p-4 mb-6">
                <div class="flex gap-3 text-red-700">
                    <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
                    <ul class="text-[0.875rem] space-y-1 list-none p-0 m-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-6">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">Nama Kategori</label>
                <div class="group flex rounded-lg border border-slate-200 focus-within:border-red-500 focus-within:ring-4 focus-within:ring-red-500/5 transition-all duration-200 overflow-hidden bg-white">
                    <span class="flex items-center px-3.5 bg-slate-50 border-0 border-r border-slate-200 text-slate-400 group-focus-within:border-red-500 transition-colors">
                        <i data-lucide="tag" class="w-4 h-4"></i>
                    </span>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="w-full px-3.5 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 outline-none border-0 bg-transparent focus:ring-0 appearance-none font-medium"
                        placeholder="Contoh: Elektronik, Dokumen, Aksesoris"
                        value="{{ old('name') }}"
                        required
                        autofocus>
                </div>
            </div>

            <!-- Icon -->
            <div x-data="{ selectedIcon: '{{ old('icon', 'tag') }}' }">
                <label for="icon" class="block text-sm font-semibold text-slate-700 mb-2">Lucide Icon Class</label>
                <div class="group flex rounded-lg border border-slate-200 focus-within:border-red-500 focus-within:ring-4 focus-within:ring-red-500/5 transition-all duration-200 overflow-hidden bg-white mb-4">
                    <span class="flex items-center px-3.5 bg-slate-50 border-0 border-r border-slate-200 text-red-500 group-focus-within:border-red-500 transition-colors shrink-0">
                        <i :data-lucide="selectedIcon" class="w-5 h-5" x-init="$watch('selectedIcon', value => $nextTick(() => lucide.createIcons()))"></i>
                    </span>
                    <input
                        type="text"
                        id="icon"
                        name="icon"
                        x-model="selectedIcon"
                        class="w-full px-3.5 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 outline-none border-0 bg-transparent focus:ring-0 appearance-none font-mono"
                        placeholder="Contoh: laptop, smartphone, key, book"
                        required>
                </div>
                <p class="text-[11px] text-slate-400 font-medium mb-3">Masukkan nama icon dari <a href="https://lucide.dev/icons" target="_blank" class="text-red-500 hover:underline">Lucide Icons</a> (contoh: <code>laptop</code>, <code>smartphone</code>, <code>key</code>, <code>book</code>, <code>wallet</code>, <code>shirt</code>, <code>glasses</code>).</p>
                
                <!-- Quick Selection Grid -->
                <div class="space-y-2">
                    <span class="text-xs font-semibold text-slate-500">Pilihan Cepat:</span>
                    <div class="grid grid-cols-5 sm:grid-cols-8 gap-2">
                        @php
                            $icons = ['tag', 'laptop', 'smartphone', 'key', 'book', 'wallet', 'shirt', 'glasses', 'file-text', 'briefcase', 'watch', 'hard-hat', 'gem', 'backpack', 'bike'];
                        @endphp
                        @foreach($icons as $ico)
                            <button type="button" 
                                    @click="selectedIcon = '{{ $ico }}'"
                                    :class="selectedIcon === '{{ $ico }}' ? 'border-red-500 bg-red-50 text-red-600' : 'border-slate-100 bg-slate-50 hover:bg-slate-100 text-slate-500'"
                                    class="p-3 border rounded-xl flex flex-col items-center justify-center gap-1 transition-all cursor-pointer">
                                <i data-lucide="{{ $ico }}" class="w-5 h-5"></i>
                                <span class="text-[9px] font-mono leading-none truncate max-w-full mt-1">{{ $ico }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.categories.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold uppercase tracking-wider hover:bg-slate-50 transition-all cursor-pointer">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white text-xs font-bold uppercase tracking-wider rounded-xl transition-all shadow-lg shadow-red-500/20 active:scale-95 flex items-center gap-2 cursor-pointer">
                    <i data-lucide="check" class="w-4 h-4"></i> Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

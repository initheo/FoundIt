@extends('admin.layout.app')

@section('title', 'Edit Laporan Barang')

@section('content')
<div class="space-y-6 pb-8 max-w-[1400px] mx-auto">
    <!-- Page Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.items.show', $item->id) }}" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-all cursor-pointer">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Edit Laporan Barang</h2>
            <p class="text-slate-500 text-xs mt-1 font-medium tracking-tight font-sans">Perbarui informasi laporan, status penyelesaian, atau kelola foto barang</p>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left: General Fields Form -->
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                <h4 class="font-bold text-slate-800 text-sm tracking-tight mb-6 flex items-center gap-2">
                    <i data-lucide="edit-3" class="w-4 h-4 text-red-500"></i>
                    Detail Informasi Laporan
                </h4>

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

                <form method="POST" action="{{ route('admin.items.update', $item->id) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Status Selection -->
                        <div class="sm:col-span-2">
                            <label for="status" class="block text-sm font-semibold text-slate-700 mb-2">Status Laporan</label>
                            <div class="grid grid-cols-3 gap-3" x-data="{ status: '{{ old('status', $item->status) }}' }">
                                <!-- Active -->
                                <label class="relative flex items-center justify-between p-3 border rounded-xl cursor-pointer hover:bg-slate-50/50 transition-all select-none"
                                       :class="status === 'active' ? 'border-amber-500 bg-amber-50/10' : 'border-slate-200 bg-white'"
                                       @click="status = 'active'; $refs.activeInput.checked = true">
                                    <span class="text-xs font-bold text-slate-800">Aktif</span>
                                    <input type="radio" name="status" value="active" class="accent-amber-600" x-ref="activeInput" {{ old('status', $item->status) === 'active' ? 'checked' : '' }} @change="status = 'active'">
                                </label>
                                <!-- Claimed -->
                                <label class="relative flex items-center justify-between p-3 border rounded-xl cursor-pointer hover:bg-slate-50/50 transition-all select-none"
                                       :class="status === 'claimed' ? 'border-blue-500 bg-blue-50/10' : 'border-slate-200 bg-white'"
                                       @click="status = 'claimed'; $refs.claimedInput.checked = true">
                                    <span class="text-xs font-bold text-slate-800">Klaim Diproses</span>
                                    <input type="radio" name="status" value="claimed" class="accent-blue-600" x-ref="claimedInput" {{ old('status', $item->status) === 'claimed' ? 'checked' : '' }} @change="status = 'claimed'">
                                </label>
                                <!-- Returned -->
                                <label class="relative flex items-center justify-between p-3 border rounded-xl cursor-pointer hover:bg-slate-50/50 transition-all select-none"
                                       :class="status === 'returned' ? 'border-green-500 bg-green-50/10' : 'border-slate-200 bg-white'"
                                       @click="status = 'returned'; $refs.returnedInput.checked = true">
                                    <span class="text-xs font-bold text-slate-800">Dikembalikan</span>
                                    <input type="radio" name="status" value="returned" class="accent-green-600" x-ref="returnedInput" {{ old('status', $item->status) === 'returned' ? 'checked' : '' }} @change="status = 'returned'">
                                </label>
                            </div>
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category_id" class="block text-sm font-semibold text-slate-700 mb-2">Kategori Barang</label>
                            <div class="group flex rounded-lg border border-slate-200 focus-within:border-red-500 focus-within:ring-4 focus-within:ring-red-500/5 transition-all duration-200 overflow-hidden bg-white">
                                <span class="flex items-center px-3.5 bg-slate-50 border-0 border-r border-slate-200 text-slate-400 group-focus-within:border-red-500 transition-colors">
                                    <i data-lucide="tag" class="w-4 h-4"></i>
                                </span>
                                <select id="category_id" name="category_id" class="w-full px-3.5 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 outline-none border-0 bg-transparent focus:ring-0 cursor-pointer appearance-none font-medium" required>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Reporter (User) -->
                        <div>
                            <label for="user_id" class="block text-sm font-semibold text-slate-700 mb-2">Pelapor (User)</label>
                            <div class="group flex rounded-lg border border-slate-200 focus-within:border-red-500 focus-within:ring-4 focus-within:ring-red-500/5 transition-all duration-200 overflow-hidden bg-white">
                                <span class="flex items-center px-3.5 bg-slate-50 border-0 border-r border-slate-200 text-slate-400 group-focus-within:border-red-500 transition-colors">
                                    <i data-lucide="user" class="w-4 h-4"></i>
                                </span>
                                <select id="user_id" name="user_id" class="w-full px-3.5 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 outline-none border-0 bg-transparent focus:ring-0 cursor-pointer appearance-none font-medium" required>
                                    @foreach($users as $usr)
                                        <option value="{{ $usr->id }}" {{ old('user_id', $item->user_id) == $usr->id ? 'selected' : '' }}>{{ $usr->name }} ({{ $usr->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Date Time -->
                        <div>
                            <label for="date_time" class="block text-sm font-semibold text-slate-700 mb-2">Waktu Kehilangan/Penemuan</label>
                            <div class="group flex rounded-lg border border-slate-200 focus-within:border-red-500 focus-within:ring-4 focus-within:ring-red-500/5 transition-all duration-200 overflow-hidden bg-white">
                                <span class="flex items-center px-3.5 bg-slate-50 border-0 border-r border-slate-200 text-slate-400 group-focus-within:border-red-500 transition-colors">
                                    <i data-lucide="calendar" class="w-4 h-4"></i>
                                </span>
                                <input
                                    type="datetime-local"
                                    id="date_time"
                                    name="date_time"
                                    class="w-full px-3.5 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 outline-none border-0 bg-transparent focus:ring-0 font-medium"
                                    value="{{ old('date_time', \Carbon\Carbon::parse($item->date_time)->format('Y-m-d\TH:i')) }}"
                                    required>
                            </div>
                        </div>

                        <!-- Location -->
                        <div>
                            <label for="location" class="block text-sm font-semibold text-slate-700 mb-2">Lokasi Utama</label>
                            <div class="group flex rounded-lg border border-slate-200 focus-within:border-red-500 focus-within:ring-4 focus-within:ring-red-500/5 transition-all duration-200 overflow-hidden bg-white">
                                <span class="flex items-center px-3.5 bg-slate-50 border-0 border-r border-slate-200 text-slate-400 group-focus-within:border-red-500 transition-colors">
                                    <i data-lucide="map-pin" class="w-4 h-4"></i>
                                </span>
                                <input
                                    type="text"
                                    id="location"
                                    name="location"
                                    class="w-full px-3.5 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 outline-none border-0 bg-transparent focus:ring-0 appearance-none font-medium"
                                    placeholder="Contoh: Masjid UISI, R.201"
                                    value="{{ old('location', $item->location) }}"
                                    required>
                            </div>
                        </div>
                    </div>

                    <!-- Title -->
                    <div class="mt-6">
                        <label for="title" class="block text-sm font-semibold text-slate-700 mb-2">Nama / Judul Barang</label>
                        <div class="group flex rounded-lg border border-slate-200 focus-within:border-red-500 focus-within:ring-4 focus-within:ring-red-500/5 transition-all duration-200 overflow-hidden bg-white">
                            <span class="flex items-center px-3.5 bg-slate-50 border-0 border-r border-slate-200 text-slate-400 group-focus-within:border-red-500 transition-colors">
                                <i data-lucide="package" class="w-4 h-4"></i>
                            </span>
                            <input
                                type="text"
                                id="title"
                                name="title"
                                class="w-full px-3.5 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 outline-none border-0 bg-transparent focus:ring-0 appearance-none font-medium"
                                placeholder="Masukkan judul barang"
                                value="{{ old('title', $item->title) }}"
                                required>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mt-6">
                        <label for="description" class="block text-sm font-semibold text-slate-700 mb-2">Deskripsi Detail</label>
                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            class="w-full px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 border border-slate-200 rounded-lg outline-none focus:border-red-500 focus:ring-4 focus:ring-red-500/5 transition-all font-medium"
                            placeholder="Jelaskan ciri-ciri barang secara detail..."
                            required>{{ old('description', $item->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6">
                        <!-- Location Detail -->
                        <div>
                            <label for="location_detail" class="block text-sm font-semibold text-slate-700 mb-2">Detail Lokasi Spesifik (Opsional)</label>
                            <div class="group flex rounded-lg border border-slate-200 focus-within:border-red-500 focus-within:ring-4 focus-within:ring-red-500/5 transition-all duration-200 overflow-hidden bg-white">
                                <span class="flex items-center px-3.5 bg-slate-50 border-0 border-r border-slate-200 text-slate-400 group-focus-within:border-red-500 transition-colors">
                                    <i data-lucide="info" class="w-4 h-4"></i>
                                </span>
                                <input
                                    type="text"
                                    id="location_detail"
                                    name="location_detail"
                                    class="w-full px-3.5 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 outline-none border-0 bg-transparent focus:ring-0 appearance-none font-medium"
                                    placeholder="Detail posisi penemuan..."
                                    value="{{ old('location_detail', $item->location_detail) }}">
                            </div>
                        </div>

                        <!-- Latitude -->
                        <div>
                            <label for="latitude" class="block text-sm font-semibold text-slate-700 mb-2">Latitude (Opsional)</label>
                            <div class="group flex rounded-lg border border-slate-200 focus-within:border-red-500 focus-within:ring-4 focus-within:ring-red-500/5 transition-all duration-200 overflow-hidden bg-white">
                                <span class="flex items-center px-3.5 bg-slate-50 border-0 border-r border-slate-200 text-slate-400 group-focus-within:border-red-500 transition-colors font-mono text-[10px]">LAT</span>
                                <input
                                    type="number"
                                    step="any"
                                    id="latitude"
                                    name="latitude"
                                    class="w-full px-3.5 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 outline-none border-0 bg-transparent focus:ring-0 appearance-none font-medium"
                                    placeholder="-7.123456"
                                    value="{{ old('latitude', $item->latitude) }}">
                            </div>
                        </div>

                        <!-- Longitude -->
                        <div>
                            <label for="longitude" class="block text-sm font-semibold text-slate-700 mb-2">Longitude (Opsional)</label>
                            <div class="group flex rounded-lg border border-slate-200 focus-within:border-red-500 focus-within:ring-4 focus-within:ring-red-500/5 transition-all duration-200 overflow-hidden bg-white">
                                <span class="flex items-center px-3.5 bg-slate-50 border-0 border-r border-slate-200 text-slate-400 group-focus-within:border-red-500 transition-colors font-mono text-[10px]">LNG</span>
                                <input
                                    type="number"
                                    step="any"
                                    id="longitude"
                                    name="longitude"
                                    class="w-full px-3.5 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 outline-none border-0 bg-transparent focus:ring-0 appearance-none font-medium"
                                    placeholder="112.654321"
                                    value="{{ old('longitude', $item->longitude) }}">
                            </div>
                        </div>

                        <!-- Map Tagger -->
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Pilih Lokasi di Peta</label>
                            <p class="text-xs text-slate-400 font-medium mb-3">Klik pada peta untuk menempatkan pin lokasi atau geser pin yang ada. Koordinat Latitude dan Longitude akan otomatis terisi.</p>
                            <div id="map" class="h-80 w-full rounded-2xl border border-slate-200 shadow-sm z-0"></div>
                        </div>

                        <!-- Storage Info -->
                        <div>
                            <label for="storage_info" class="block text-sm font-semibold text-slate-700 mb-2">Info Penyimpanan (Opsional)</label>
                            <div class="group flex rounded-lg border border-slate-200 focus-within:border-red-500 focus-within:ring-4 focus-within:ring-red-500/5 transition-all duration-200 overflow-hidden bg-white">
                                <span class="flex items-center px-3.5 bg-slate-50 border-0 border-r border-slate-200 text-slate-400 group-focus-within:border-red-500 transition-colors">
                                    <i data-lucide="archive" class="w-4 h-4"></i>
                                </span>
                                <input
                                    type="text"
                                    id="storage_info"
                                    name="storage_info"
                                    class="w-full px-3.5 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 outline-none border-0 bg-transparent focus:ring-0 appearance-none font-medium"
                                    placeholder="Lokasi fisik penyimpanan..."
                                    value="{{ old('storage_info', $item->storage_info) }}">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Actions -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100 mt-8">
                        <a href="{{ route('admin.items.show', $item->id) }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold uppercase tracking-wider hover:bg-slate-50 transition-all cursor-pointer">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white text-xs font-bold uppercase tracking-wider rounded-xl transition-all shadow-lg shadow-red-500/20 active:scale-95 flex items-center gap-2 cursor-pointer">
                            <i data-lucide="check" class="w-4 h-4"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right: Photos Management -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                <h4 class="font-bold text-slate-800 text-sm tracking-tight mb-6 flex items-center gap-2">
                    <i data-lucide="image" class="w-4 h-4 text-red-500"></i>
                    Kelola Foto Laporan ({{ $item->photos->count() }}/3)
                </h4>

                <!-- Existing Photos List -->
                <div class="space-y-4 mb-6">
                    @forelse($item->photos as $photo)
                        <div class="relative group aspect-video rounded-2xl overflow-hidden bg-slate-50 border border-slate-100 shadow-sm">
                            <img src="{{ asset('storage/' . $photo->photo_url) }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                            
                            <!-- Delete Overlay Form -->
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <form action="{{ route('admin.items.photos.delete', [$item->id, $photo->id]) }}" method="POST"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus foto ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-10 h-10 rounded-xl bg-red-600 hover:bg-red-700 text-white flex items-center justify-center transition-all active:scale-90 cursor-pointer shadow-lg shadow-red-500/20" title="Hapus Foto">
                                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="bg-slate-50 border border-dashed border-slate-200 rounded-2xl p-6 text-center text-slate-400">
                            <i data-lucide="image-off" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                            <p class="text-xs font-semibold">Belum ada foto lampiran</p>
                        </div>
                    @endforelse
                </div>

                <!-- Add New Photo Form (Only if count < 3) -->
                @if($item->photos->count() < 3)
                    <div class="border-t border-slate-100 pt-6">
                        <h5 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">Tambah Foto Baru</h5>
                        <form action="{{ route('admin.items.photos.add', $item->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div class="space-y-4"
                                 x-data="{
                                     file: null,
                                     previewUrl: null,
                                     fileName: '',
                                     fileSize: '',
                                     handleFile(e) {
                                         const selectedFile = e.target.files[0];
                                         if (!selectedFile) return;
                                         
                                         if (!['image/jpeg', 'image/png', 'image/jpg'].includes(selectedFile.type)) {
                                             alert('Format file tidak didukung.');
                                             this.clearFile();
                                             return;
                                         }
                                         if (selectedFile.size > 2 * 1024 * 1024) {
                                             alert('Ukuran file melebihi 2MB.');
                                             this.clearFile();
                                             return;
                                         }
                                         
                                         this.file = selectedFile;
                                         this.fileName = selectedFile.name;
                                         this.fileSize = this.formatSize(selectedFile.size);
                                         if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
                                         this.previewUrl = URL.createObjectURL(selectedFile);
                                     },
                                     clearFile() {
                                         if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
                                         this.file = null;
                                         this.previewUrl = null;
                                         this.fileName = '';
                                         this.fileSize = '';
                                         this.$refs.photoInput.value = '';
                                     },
                                     formatSize(bytes) {
                                         if (bytes === 0) return '0 Bytes';
                                         const k = 1024;
                                         const sizes = ['Bytes', 'KB', 'MB'];
                                         const i = Math.floor(Math.log(bytes) / Math.log(k));
                                         return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                                     }
                                 }">
                                <div class="group flex flex-col items-center justify-center p-4 border-2 border-dashed border-slate-200 hover:border-red-500 hover:bg-slate-50/50 rounded-xl transition-all cursor-pointer relative"
                                     @click="$refs.photoInput.click()">
                                    <i data-lucide="upload-cloud" class="w-8 h-8 text-slate-400 mb-2 group-hover:text-red-500 transition-colors"></i>
                                    <span class="text-[10px] font-bold text-slate-500 block mb-1">Pilih File Foto</span>
                                    <span class="text-[9px] text-slate-400 font-medium">Format: JPG, PNG (Maks. 2MB)</span>
                                    <span class="mt-2 text-xs font-black text-red-600 animate-pulse" x-show="previewUrl" x-text="'Foto Terpilih'"></span>
                                    <input
                                        type="file"
                                        name="photo"
                                        accept="image/png, image/jpeg, image/jpg"
                                        class="hidden"
                                        x-ref="photoInput"
                                        required
                                        @change="handleFile($event)">
                                </div>

                                <!-- Visual Preview -->
                                <div class="relative group border border-slate-100 rounded-2xl overflow-hidden bg-slate-50/50 shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md mt-4" x-show="previewUrl" x-cloak>
                                    <div class="aspect-video w-full bg-slate-100 relative overflow-hidden">
                                        <img :src="previewUrl" class="w-full h-full object-cover" />
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <button type="button" 
                                                    @click.stop="clearFile()" 
                                                    class="w-10 h-10 rounded-xl bg-red-600 hover:bg-red-700 text-white flex items-center justify-center transition-all active:scale-90 cursor-pointer shadow-lg shadow-red-500/20" 
                                                    title="Batal Pilih">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="p-3 bg-white border-t border-slate-100 flex items-center justify-between gap-2">
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs font-bold text-slate-700 truncate" x-text="fileName"></p>
                                            <p class="text-[10px] text-slate-400 font-medium mt-0.5" x-text="fileSize"></p>
                                        </div>
                                        <button type="button" 
                                                @click.stop="clearFile()" 
                                                class="text-slate-400 hover:text-red-500 p-1.5 hover:bg-slate-50 rounded-lg transition-colors cursor-pointer shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                        </button>
                                    </div>
                                </div>

                                <button type="submit" class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition-all shadow-md active:scale-[0.98] cursor-pointer">
                                    Upload Foto
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="bg-red-50/50 border border-red-100/50 rounded-2xl p-4 text-center">
                        <p class="text-xs text-red-600 font-bold leading-normal">Mencapai Batas Maksimal</p>
                        <p class="text-[10px] text-red-500 font-semibold mt-1 leading-normal">Laporan ini sudah memiliki 3 foto. Hapus salah satu foto di atas jika ingin mengganti.</p>
                    </div>
                @endif
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
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        
        // Default UISI coordinates
        const defaultLat = -7.1756;
        const defaultLng = 112.6492;
        
        let initialLat = parseFloat(latInput.value);
        let initialLng = parseFloat(lngInput.value);
        
        const hasInitialCoords = !isNaN(initialLat) && !isNaN(initialLng);
        
        const map = L.map('map').setView(
            hasInitialCoords ? [initialLat, initialLng] : [defaultLat, defaultLng],
            16
        );
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        // Custom Red Pin Icon
        const customIcon = L.divIcon({
            className: 'custom-div-icon',
            html: `<div class="flex items-center justify-center w-8 h-8 rounded-full bg-red-600 border-2 border-white shadow-lg text-white">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                   </div>`,
            iconSize: [32, 32],
            iconAnchor: [16, 32],
            popupAnchor: [0, -32]
        });
        
        let marker = null;
        
        function updateInputs(lat, lng) {
            latInput.value = parseFloat(lat).toFixed(6);
            lngInput.value = parseFloat(lng).toFixed(6);
        }
        
        function setMarker(lat, lng) {
            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng], {
                    icon: customIcon,
                    draggable: true
                }).addTo(map);
                
                marker.on('dragend', function (e) {
                    const position = marker.getLatLng();
                    updateInputs(position.lat, position.lng);
                });
            }
        }
        
        // If we have initial coordinates, place marker
        if (hasInitialCoords) {
            setMarker(initialLat, initialLng);
        }
        
        // Click on map to place/move marker
        map.on('click', function (e) {
            setMarker(e.latlng.lat, e.latlng.lng);
            updateInputs(e.latlng.lat, e.latlng.lng);
        });
        
        // Update marker on input changes
        function handleInputChange() {
            const lat = parseFloat(latInput.value);
            const lng = parseFloat(lngInput.value);
            if (!isNaN(lat) && !isNaN(lng)) {
                setMarker(lat, lng);
                map.panTo([lat, lng]);
            } else if (latInput.value === '' && lngInput.value === '' && marker) {
                map.removeLayer(marker);
                marker = null;
            }
        }
        
        latInput.addEventListener('input', handleInputChange);
        lngInput.addEventListener('input', handleInputChange);
    });
</script>
@endpush

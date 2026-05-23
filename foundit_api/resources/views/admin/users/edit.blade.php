@extends('admin.layout.app')

@section('title', 'Edit Pengguna')

@section('content')
<div class="space-y-6 pb-8 max-w-[1400px] mx-auto">
    <!-- Page Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.users.show', $user->id) }}" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-all cursor-pointer">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Edit Pengguna</h2>
            <p class="text-slate-500 text-xs mt-1 font-medium tracking-tight">Perbarui informasi profil atau wewenang akun pengguna</p>
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

        <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">Nama Lengkap</label>
                <div class="group flex rounded-lg border border-slate-200 focus-within:border-red-500 focus-within:ring-4 focus-within:ring-red-500/5 transition-all duration-200 overflow-hidden bg-white">
                    <span class="flex items-center px-3.5 bg-slate-50 border-0 border-r border-slate-200 text-slate-400 group-focus-within:border-red-500 transition-colors">
                        <i data-lucide="user" class="w-4 h-4"></i>
                    </span>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="w-full px-3.5 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 outline-none border-0 bg-transparent focus:ring-0 appearance-none font-medium"
                        placeholder="Masukkan nama lengkap"
                        value="{{ old('name', $user->name) }}"
                        required>
                </div>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Alamat Email</label>
                <div class="group flex rounded-lg border border-slate-200 focus-within:border-red-500 focus-within:ring-4 focus-within:ring-red-500/5 transition-all duration-200 overflow-hidden bg-white">
                    <span class="flex items-center px-3.5 bg-slate-50 border-0 border-r border-slate-200 text-slate-400 group-focus-within:border-red-500 transition-colors">
                        <i data-lucide="mail" class="w-4 h-4"></i>
                    </span>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="w-full px-3.5 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 outline-none border-0 bg-transparent focus:ring-0 appearance-none font-medium"
                        placeholder="contoh@student.uisi.ac.id"
                        value="{{ old('email', $user->email) }}"
                        required>
                </div>
            </div>

            <!-- Phone -->
            <div>
                <label for="phone" class="block text-sm font-semibold text-slate-700 mb-2">No. Telepon / WhatsApp</label>
                <div class="group flex rounded-lg border border-slate-200 focus-within:border-red-500 focus-within:ring-4 focus-within:ring-red-500/5 transition-all duration-200 overflow-hidden bg-white">
                    <span class="flex items-center px-3.5 bg-slate-50 border-0 border-r border-slate-200 text-slate-400 group-focus-within:border-red-500 transition-colors">
                        <i data-lucide="phone" class="w-4 h-4"></i>
                    </span>
                    <input
                        type="text"
                        id="phone"
                        name="phone"
                        class="w-full px-3.5 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 outline-none border-0 bg-transparent focus:ring-0 appearance-none font-medium"
                        placeholder="Contoh: 081234567890"
                        value="{{ old('phone', $user->phone) }}">
                </div>
            </div>

            <!-- Prodi / Unit -->
            <div>
                <label for="prodi_unit" class="block text-sm font-semibold text-slate-700 mb-2">Program Studi / Unit Kerja</label>
                <div class="group flex rounded-lg border border-slate-200 focus-within:border-red-500 focus-within:ring-4 focus-within:ring-red-500/5 transition-all duration-200 overflow-hidden bg-white">
                    <span class="flex items-center px-3.5 bg-slate-50 border-0 border-r border-slate-200 text-slate-400 group-focus-within:border-red-500 transition-colors">
                        <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                    </span>
                    <input
                        type="text"
                        id="prodi_unit"
                        name="prodi_unit"
                        class="w-full px-3.5 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 outline-none border-0 bg-transparent focus:ring-0 appearance-none font-medium"
                        placeholder="Contoh: Informatika, Manajemen, Bagian Umum"
                        value="{{ old('prodi_unit', $user->prodi_unit) }}">
                </div>
            </div>

            <!-- Role Selection -->
            <div>
                <label for="role" class="block text-sm font-semibold text-slate-700 mb-2">Peran (Role) Akses</label>
                <div class="grid grid-cols-2 gap-4">
                    <!-- Option User -->
                    <label class="relative flex items-center justify-between p-4 border rounded-2xl cursor-pointer hover:bg-slate-50/50 transition-all select-none"
                           :class="role === 'user' ? 'border-red-500 bg-red-50/10' : 'border-slate-200 bg-white'"
                           x-data="{ role: '{{ old('role', $user->role) }}' }"
                           @click="role = 'user'; $refs.userInput.checked = true">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-slate-100 text-slate-600 rounded-xl flex items-center justify-center shrink-0"
                                 :class="role === 'user' ? 'bg-red-100/50 text-red-600' : ''">
                                <i data-lucide="user" class="w-5 h-5"></i>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-slate-800">User</span>
                                <span class="text-[10px] text-slate-400 font-medium">Akses aplikasi mobile saja</span>
                            </div>
                        </div>
                        <input type="radio" 
                               id="role_user" 
                               name="role" 
                               value="user" 
                               class="accent-red-600"
                               x-ref="userInput"
                               {{ old('role', $user->role) === 'user' ? 'checked' : '' }}
                               @change="role = 'user'">
                    </label>

                    <!-- Option Admin -->
                    <label class="relative flex items-center justify-between p-4 border rounded-2xl cursor-pointer hover:bg-slate-50/50 transition-all select-none"
                           :class="role === 'admin' ? 'border-red-500 bg-red-50/10' : 'border-slate-200 bg-white'"
                           x-data="{ role: '{{ old('role', $user->role) }}' }"
                           @click="role = 'admin'; $refs.adminInput.checked = true">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-slate-100 text-slate-600 rounded-xl flex items-center justify-center shrink-0"
                                 :class="role === 'admin' ? 'bg-red-100/50 text-red-600' : ''">
                                <i data-lucide="shield-check" class="w-5 h-5"></i>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-slate-800">Admin</span>
                                <span class="text-[10px] text-slate-400 font-medium">Akses penuh dashboard web</span>
                            </div>
                        </div>
                        <input type="radio" 
                               id="role_admin" 
                               name="role" 
                               value="admin" 
                               class="accent-red-600"
                               x-ref="adminInput"
                               {{ old('role', $user->role) === 'admin' ? 'checked' : '' }}
                               @change="role = 'admin'">
                    </label>
                </div>
            </div>

            <!-- Password & Password Confirmation -->
            <div class="border-t border-slate-100 pt-6">
                <h4 class="font-bold text-slate-800 text-sm tracking-tight mb-4 flex items-center gap-2">
                    <i data-lucide="key-round" class="w-4 h-4 text-red-500"></i>
                    Ubah Password (Opsional)
                </h4>
                <p class="text-xs text-slate-400 font-medium mb-4">Kosongkan kolom di bawah jika Anda tidak ingin mengubah password pengguna ini.</p>
                
                <div class="grid grid-cols-1 gap-6">
                    <!-- Password Baru -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">Password Baru</label>
                        <div class="group flex rounded-lg border border-slate-200 focus-within:border-red-500 focus-within:ring-4 focus-within:ring-red-500/5 transition-all duration-200 overflow-hidden bg-white">
                            <span class="flex items-center px-3.5 bg-slate-50 border-0 border-r border-slate-200 text-slate-400 group-focus-within:border-red-500 transition-colors">
                                <i data-lucide="lock" class="w-4 h-4"></i>
                            </span>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="w-full px-3.5 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 outline-none border-0 bg-transparent focus:ring-0 appearance-none font-medium"
                                placeholder="Masukkan password baru">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('admin.users.show', $user->id) }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold uppercase tracking-wider hover:bg-slate-50 transition-all cursor-pointer">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white text-xs font-bold uppercase tracking-wider rounded-xl transition-all shadow-lg shadow-red-500/20 active:scale-95 flex items-center gap-2 cursor-pointer">
                    <i data-lucide="check" class="w-4 h-4"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

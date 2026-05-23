@extends('admin.layout.guest')

@section('title', 'Login Admin')

@section('content')
<div class="relative min-h-screen flex flex-col items-center justify-center p-6 overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-red-100/40 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-red-100/40 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Auth Card -->
    <div class="relative w-full max-w-[440px] bg-white rounded-2xl shadow-[0_2px_20px_rgba(0,0,0,0.08)] border border-gray-200 px-[2.5rem] py-[2.75rem] z-10 transition-all duration-300">
        
        <!-- Brand Header -->
        <div class="flex flex-col items-center mb-8">
            <div class="w-16 h-16 rounded-2xl bg-white flex items-center justify-center p-2.5 mb-4 shadow-md border border-slate-100 overflow-hidden">
                <img src="{{ asset('logo.png') }}" class="w-full h-full object-contain" alt="FoundIt Logo">
            </div>
            <h1 class="font-sans text-[1.4rem] font-bold text-slate-800 tracking-[-0.3px]">FoundIt</h1>
            <span class="text-[0.7rem] uppercase tracking-[2.5px] text-red-600 font-bold mt-0.5">Admin Panel</span>
        </div>

        <!-- Heading -->
        <div class="mb-7">
            <h2 class="font-sans text-[1.3rem] font-bold text-slate-800 mb-1.5">Selamat Datang! 👋</h2>
            <p class="text-[0.875rem] text-slate-500">Silakan masuk ke akun admin Anda</p>
        </div>

        <!-- Errors -->
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 border-l-4 border-l-red-500 rounded-lg p-4 mb-5 animate-in fade-in slide-in-from-top-2">
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

        <!-- Form -->
        <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-4">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-[0.85rem] font-semibold text-slate-700 mb-1.5 px-0.5">Email</label>
                <div class="group flex rounded-lg border-[1.5px] border-slate-200 focus-within:border-red-500 focus-within:ring-[3px] focus-within:ring-red-500/10 transition-all duration-200 overflow-hidden bg-white">
                    <span class="flex items-center px-3.5 bg-slate-50 border-0 border-r-[1.5px] border-slate-200 text-red-500 group-focus-within:border-red-500 transition-colors">
                        <i data-lucide="mail" class="w-4 h-4"></i>
                    </span>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="w-full px-3.5 py-2.5 text-[0.9rem] text-slate-800 placeholder:text-slate-400 outline-none border-0 bg-transparent focus:ring-0 appearance-none"
                        placeholder="admin@student.uisi.ac.id"
                        value="{{ old('email') }}"
                        required
                        autofocus>
                </div>
            </div>

            <!-- Password -->
            <div x-data="{ show: false }">
                <label for="password" class="block text-[0.85rem] font-semibold text-slate-700 mb-1.5 px-0.5">Password</label>
                <div class="group flex rounded-lg border-[1.5px] border-slate-200 focus-within:border-red-500 focus-within:ring-[3px] focus-within:ring-red-500/10 transition-all duration-200 overflow-hidden bg-white">
                    <span class="flex items-center px-3.5 bg-slate-50 border-0 border-r-[1.5px] border-slate-200 text-red-500 group-focus-within:border-red-500 transition-colors">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                    </span>
                    <input
                        :type="show ? 'text' : 'password'"
                        id="password"
                        name="password"
                        class="w-full px-3.5 py-2.5 text-[0.9rem] text-slate-800 placeholder:text-slate-400 outline-none border-0 bg-transparent focus:ring-0 appearance-none"
                        placeholder="·············"
                        required>
                    <button 
                        type="button" 
                        @click="show = !show"
                        class="flex items-center px-3.5 bg-slate-50 border-0 border-l-[1.5px] border-slate-200 text-slate-400 hover:text-red-500 group-focus-within:border-red-500 transition-colors">
                        <i x-show="!show" data-lucide="eye-off" class="w-4 h-4"></i>
                        <i x-show="show" data-lucide="eye" class="w-4 h-4" x-cloak></i>
                    </button>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between py-1">
                <label class="flex items-center gap-2 cursor-pointer group">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-red-500 focus:ring-red-500/20 transition-all checked:bg-red-500">
                    <span class="text-[0.875rem] text-slate-600 font-medium group-hover:text-red-500 transition-colors">Ingat Saya</span>
                </label>
            </div>

            <!-- Submit -->
            <button type="submit" class="w-full py-3 px-6 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-bold rounded-lg shadow-lg shadow-red-500/20 flex items-center justify-center gap-2 transition-all duration-200 transform hover:-translate-y-0.5 active:translate-y-0 cursor-pointer">
                <i data-lucide="log-in" class="w-5 h-5"></i>
                Masuk
            </button>
        </form>

        <!-- Divider -->
        <div class="flex items-center gap-4 my-5 py-2">
            <div class="flex-1 h-px bg-slate-200"></div>
            <span class="text-[0.8rem] tracking-wider text-slate-400">Informasi</span>
            <div class="flex-1 h-px bg-slate-200"></div>
        </div>

        <!-- Demo Hint -->
        <div class="bg-red-50 border border-red-100 rounded-lg p-3.5 flex items-start gap-3">
            <i data-lucide="info" class="w-5 h-5 text-red-600 shrink-0 mt-0.5"></i>
            <div class="text-[0.8rem] text-red-800 font-medium">
                <strong>Default Admin:</strong><br>
                Email: muhammad.nuha23@student.uisi.ac.id<br>
                Password: password123
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6 text-[0.78rem] text-slate-400">
            &copy; {{ date('Y') }} FoundIt &dash; Lost and Found UISI
        </div>

    </div>
</div>
@endsection

@push('styles')
<style>
    @keyframes fadeInSlide {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-in { animation: fadeInSlide 0.4s ease-out forwards; }
</style>
@endpush

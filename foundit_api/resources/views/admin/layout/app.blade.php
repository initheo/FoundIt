<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - FoundIt</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Nunito:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Vite (Tailwind & JS bundle: Alpine.js + Lucide) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Hide HTML5 validation icons and spinners in WebKit browsers */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none !important;
            margin: 0 !important;
        }
        input[type=number] {
            -moz-appearance: textfield !important;
        }
        /* Hide clear button and other browser-native artifacts */
        input::-ms-clear, input::-ms-reveal {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }
        input::-webkit-search-decoration,
        input::-webkit-search-cancel-button,
        input::-webkit-search-results-button,
        input::-webkit-search-results-decoration {
            display: none !important;
        }
        input:invalid, input:valid {
            background-image: none !important;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-app-bg text-primary font-sans leading-relaxed antialiased" 
      x-data="{ 
          sidebarOpen: false, 
          showLogoutModal: false,
          deleteModal: { show: false, action: '', message: '' },
          lightbox: { show: false, image: '', title: '', scale: 1, translateX: 0, translateY: 0, isDragging: false, startX: 0, startY: 0 },
          toasts: [],
          addToast(message, type = 'success') {
              const id = Date.now();
              this.toasts.push({ id, message, type });
              setTimeout(() => this.removeToast(id), 5000);
          },
          removeToast(id) {
              this.toasts = this.toasts.filter(t => t.id !== id);
          },
          confirmDelete(action, message) {
              this.deleteModal.action = action;
              this.deleteModal.message = message || 'Data yang dihapus tidak dapat dikembalikan.';
              this.deleteModal.show = true;
          },
          openLightbox(image, title) {
              this.lightbox.image = image;
              this.lightbox.title = title || '';
              this.lightbox.scale = 1;
              this.lightbox.translateX = 0;
              this.lightbox.translateY = 0;
              this.lightbox.show = true;
          },
          zoomLightbox(factor) {
              this.lightbox.scale = Math.min(Math.max(this.lightbox.scale + factor, 0.5), 10);
              if (this.lightbox.scale === 1) { this.lightbox.translateX = 0; this.lightbox.translateY = 0; }
          },
          resetLightboxZoom() {
              this.lightbox.scale = 1;
              this.lightbox.translateX = 0;
              this.lightbox.translateY = 0;
          },
          confirmLogout() {
              this.showLogoutModal = true;
          }
      }"
      x-init="
          @if(session('success')) addToast('{{ session('success') }}', 'success'); @endif
          @if(session('error')) addToast('{{ session('error') }}', 'error'); @endif
      "
      @confirm-delete.window="confirmDelete($event.detail.action, $event.detail.message)"
      @confirm-logout.window="confirmLogout()"
      @open-lightbox.window="openLightbox($event.detail.image, $event.detail.title)">
    
    <!-- Mobile sidebar overlay -->
    <div class="sidebar-overlay fixed inset-0 bg-black/40 z-[1040] transition-opacity duration-300" 
         x-show="sidebarOpen" 
         x-cloak
         @click="sidebarOpen = false"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"></div>

    @include('admin.layout.sidebar')
    
    <!-- Main Content -->
    <div class="main-content min-h-screen flex flex-col">

        <!-- Top Bar -->
        @include('admin.layout.header')
        
        <!-- Content -->
        <div class="content-wrapper flex-1 {{ request()->routeIs('admin.dashboard') ? 'dashboard-wrapper' : '' }}">
            <div class="fade-in-up">
                @yield('content')
            </div>
        </div>

        @include('admin.layout.footer')
    </div>

    <!-- Toast Container -->
    <div class="fixed bottom-6 right-6 z-[2000] flex flex-col gap-3 w-full max-w-xs pointer-events-none">
        <template x-for="toast in toasts" :key="toast.id">
            <div class="pointer-events-auto flex items-center gap-4 px-5 py-4 rounded-2xl shadow-2xl bg-white/95 backdrop-blur-md cursor-pointer transition-all hover:scale-[1.02] border border-slate-100"
                 x-init="$nextTick(() => lucide.createIcons())"
                 @click="removeToast(toast.id)">
                
                <!-- Icon Area -->
                <div class="flex items-center justify-center shrink-0"
                     :class="{
                         'text-success-500': toast.type === 'success',
                         'text-error-500': toast.type === 'error'
                     }">
                    <template x-if="toast.type === 'success'">
                        <i data-lucide="check-circle" class="w-7 h-7 text-green-500"></i>
                    </template>
                    <template x-if="toast.type === 'error'">
                        <i data-lucide="alert-circle" class="w-7 h-7 text-red-500"></i>
                    </template>
                </div>

                <!-- Text Area -->
                <div class="flex-1 min-w-0">
                    <p class="text-[15px] font-bold text-slate-800 leading-tight" x-text="toast.message"></p>
                </div>

                <!-- Close Button -->
                <button type="button" class="text-slate-300 hover:text-slate-500 transition-colors ml-2">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </template>
    </div>
    
    <!-- Delete Confirmation Modal (Alpine.js) -->
    <template x-teleport="body">
        <div class="fixed inset-0 z-[1100] flex items-center justify-center p-4 bg-black/50" 
             x-show="deleteModal.show" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-cloak>
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden"
                 @click.outside="deleteModal.show = false"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="scale-95 opacity-0"
                 x-transition:enter-end="scale-100 opacity-100">
                <div class="p-6 text-center">
                    <div class="w-20 h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-5 text-4xl">
                        <i data-lucide="trash-2" class="w-10 h-10"></i>
                    </div>
                    <h5 class="text-xl font-bold text-gray-900 mb-2">Hapus Data?</h5>
                    <p class="text-gray-500 text-sm mb-6" x-text="deleteModal.message"></p>
                    
                    <div class="flex gap-3 justify-center">
                        <button type="button" class="px-6 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition-colors" @click="deleteModal.show = false">
                             Batal
                        </button>
                        <form :action="deleteModal.action" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-6 py-2.5 rounded-lg bg-red-600 text-white font-medium hover:bg-red-700 shadow-lg shadow-red-200 transition-all flex items-center gap-2">
                                <i data-lucide="trash-2" class="w-4 h-4"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>
    
    <!-- Global Lightbox Component (Alpine.js) -->
    <template x-teleport="body">
        <div x-show="lightbox.show" 
             x-init="$nextTick(() => { if (lightbox.show) lucide.createIcons(); }); $watch('lightbox.show', value => { if(value) $nextTick(() => lucide.createIcons()) })"
             class="fixed inset-0 z-[2000] flex items-center justify-center p-0 backdrop-blur-md bg-slate-900/95 overflow-hidden select-none"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-cloak
             @keydown.escape.window="lightbox.show = false"
             @keydown.ctrl.plus.prevent.window="zoomLightbox(0.5)"
             @keydown.ctrl.equal.prevent.window="zoomLightbox(0.5)"
             @keydown.ctrl.minus.prevent.window="zoomLightbox(-0.5)"
             @keydown.r.window="resetLightboxZoom()"
             @wheel.prevent="zoomLightbox(event.deltaY < 0 ? 0.25 : -0.25)"
             @mousemove="if(lightbox.isDragging) { lightbox.translateX = event.clientX - lightbox.startX; lightbox.translateY = event.clientY - lightbox.startY; }"
             @mouseup="lightbox.isDragging = false"
             @mouseleave="lightbox.isDragging = false">
            
            <!-- Toolbar -->
            <div class="absolute top-6 left-1/2 -translate-x-1/2 flex items-center gap-1 bg-white/10 backdrop-blur-md p-1.5 rounded-2xl border border-white/10 shadow-2xl z-[2001] transition-opacity duration-300 hover:opacity-100 opacity-60">
                <button @click="zoomLightbox(0.5)" class="w-10 h-10 flex items-center justify-center rounded-xl text-white hover:bg-white/20 transition-all" title="Zoom In (Ctrl +)">
                    <i data-lucide="zoom-in" class="w-5 h-5"></i>
                </button>
                <button @click="zoomLightbox(-0.5)" class="w-10 h-10 flex items-center justify-center rounded-xl text-white hover:bg-white/20 transition-all" title="Zoom Out (Ctrl -)">
                    <i data-lucide="zoom-out" class="w-5 h-5"></i>
                </button>
                <div class="w-px h-6 bg-white/10 mx-1"></div>
                <button @click="resetLightboxZoom()" class="w-10 h-10 flex items-center justify-center rounded-xl text-white hover:bg-white/20 transition-all" title="Reset (R)">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                </button>
                <span class="px-3 text-[10px] font-black text-white/50 min-w-[50px] text-center uppercase tracking-widest" x-text="Math.round(lightbox.scale * 100) + '%'"></span>
            </div>

            <!-- Close Button -->
            <button @click="lightbox.show = false" 
                    class="absolute top-6 right-6 w-12 h-12 bg-white/10 hover:bg-white/20 text-white rounded-full flex items-center justify-center transition-all active:scale-95 group z-[2001]">
                <i data-lucide="x" class="w-6 h-6 group-hover:rotate-90 transition-transform"></i>
            </button>

            <!-- Image Container -->
            <div class="relative w-full h-full flex items-center justify-center overflow-hidden cursor-grab active:cursor-grabbing"
                 @mousedown="lightbox.isDragging = true; lightbox.startX = event.clientX - lightbox.translateX; lightbox.startY = event.clientY - lightbox.translateY;">
                <img :src="lightbox.image" 
                     :alt="lightbox.title" 
                     :style="`transform: translate(${lightbox.translateX}px, ${lightbox.translateY}px) scale(${lightbox.scale}); transition: ${lightbox.isDragging ? 'none' : 'transform 0.2s cubic-bezier(0.4, 0, 0.2, 1)'}; image-rendering: ${lightbox.scale > 2 ? 'pixelated' : 'auto'};`"
                     class="max-w-none max-h-none object-contain rounded-sm shadow-2xl pointer-events-none origin-center"
                     @load="event.target.style.width = 'auto'; event.target.style.height = 'auto';">
            </div>

            <!-- Navigation Hint -->
            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex items-center gap-4 text-white/40 text-[9px] font-bold uppercase tracking-[0.2em] pointer-events-none">
                <span class="flex items-center gap-1.5"><i data-lucide="mouse-pointer-2" class="w-3 h-3 text-red-400"></i> SCROLL TO ZOOM</span>
                <div class="w-1.5 h-1.5 bg-white/10 rounded-full"></div>
                <span class="flex items-center gap-1.5"><i data-lucide="move" class="w-3 h-3 text-red-400"></i> DRAG TO PAN</span>
                <div class="w-1.5 h-1.5 bg-white/10 rounded-full"></div>
                <span class="flex items-center gap-1.5"><i data-lucide="keyboard" class="w-3 h-3 text-red-400"></i> ESC TO CLOSE</span>
            </div>
        </div>
    </template>

    <!-- Global Logout Confirmation Modal (Alpine.js) -->
    <template x-teleport="body">
        <div class="fixed inset-0 z-[1100] flex items-center justify-center p-4 bg-black/50" 
             x-show="showLogoutModal" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @keydown.escape.window="showLogoutModal = false"
             x-cloak>
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden"
                 @click.outside="showLogoutModal = false"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="scale-95 opacity-0"
                 x-transition:enter-end="scale-100 opacity-100">
                <div class="p-6 text-center">
                    <div class="w-20 h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-5 text-4xl">
                        <i data-lucide="log-out" class="w-10 h-10 text-red-500"></i>
                    </div>
                    <h5 class="text-xl font-bold text-gray-900 mb-2">Konfirmasi Keluar</h5>
                    <p class="text-gray-500 text-sm mb-6">Apakah Anda yakin ingin mengakhiri sesi admin ini?</p>
                    
                    <div class="flex gap-3 justify-center">
                        <button type="button" class="px-6 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition-colors" @click="showLogoutModal = false">
                            Batal
                        </button>
                        <form action="{{ route('admin.logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-6 py-2.5 rounded-lg bg-red-600 text-white font-medium hover:bg-red-700 shadow-lg shadow-red-200 transition-all flex items-center gap-2">
                                <i data-lucide="log-out" class="w-4 h-4"></i> Ya, Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>
    
    <script>
        // Global helper for the delete modal
        window.confirmDelete = (action, message) => {
            window.dispatchEvent(new CustomEvent('confirm-delete', { detail: { action, message } }));
        };

        // Global helper for the lightbox
        window.openLightbox = (image, title) => {
            window.dispatchEvent(new CustomEvent('open-lightbox', { detail: { image, title } }));
        };

        // Global helper for the logout confirmation
        window.confirmLogout = () => {
            window.dispatchEvent(new CustomEvent('confirm-logout'));
        };
    </script>
    
    @stack('scripts')
</body>
</html>

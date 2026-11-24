@php
    $isOperator = auth()->user()->can('operate-laundry');
    $isAdmin    = auth()->user()->can('manage-laundry');
@endphp

<!-- Mobile Backdrop Overlay -->
<div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
     class="fixed inset-0 z-40 bg-black/40 backdrop-blur-[2px] lg:hidden no-print" style="display:none"></div>

<!-- Unified Persistent Left Sidebar (Consistent across ALL pages) -->
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
       class="kw-vt-sidebar fixed lg:sticky top-0 left-0 z-50 h-screen w-[248px] shrink-0 bg-white border-r border-black/[0.07] flex flex-col justify-between p-4 transition-transform duration-200 ease-out no-print">

    <!-- Top Brand & Flat Intuitive Navigation -->
    <div class="space-y-6 overflow-y-auto">
        <!-- Brand Header -->
        <div class="flex items-center justify-between px-2 pt-1">
            <a href="{{ route($isOperator ? 'dashboard' : 'customer.index') }}" class="flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-[10px] bg-[#141416] text-white inline-flex items-center justify-center shadow-sm">
                    <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="13" r="5.5"/>
                        <rect x="4" y="3" width="16" height="18" rx="2.5"/>
                        <line x1="8" y1="6.5" x2="8.01" y2="6.5"/>
                        <line x1="11.5" y1="6.5" x2="11.51" y2="6.5"/>
                    </svg>
                </span>
                <span class="leading-tight">
                    <span class="block text-[16px] font-semibold tracking-[-0.03em] text-[#141416]">kilawash</span>
                    <span class="block text-[11px] text-[#71717a]">Laundry System</span>
                </span>
            </a>

            <button type="button" @click="sidebarOpen = false" aria-label="Tutup menu"
                    class="lg:hidden w-8 h-8 rounded-full hover:bg-[#f4f4f6] text-[#52525b] inline-flex items-center justify-center">
                &times;
            </button>
        </div>

        <!-- Primary Navigation: Simple, Flat, Immediately Obvious -->
        <nav aria-label="Navigasi utama" class="space-y-1">
            @can('operate-laundry')
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-[13.5px] font-medium transition {{ request()->routeIs('dashboard', 'admin.dashboard') ? 'bg-[#141416] text-white shadow-sm' : 'text-[#52525b] hover:bg-[#f4f4f6] hover:text-[#141416]' }}">
                    <svg class="w-[18px] h-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg>
                    <span>Dashboard</span>
                </a>
            @endcan

            <a href="{{ route('customer.index') }}"
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-[13.5px] font-medium transition {{ request()->routeIs('customer.index') ? 'bg-[#141416] text-white shadow-sm' : 'text-[#52525b] hover:bg-[#f4f4f6] hover:text-[#141416]' }}">
                <svg class="w-[18px] h-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                <span>Buat Pesanan</span>
            </a>

            @can('operate-laundry')
                <a href="{{ route('services.index') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-[13.5px] font-medium transition {{ request()->routeIs('services.*') ? 'bg-[#141416] text-white shadow-sm' : 'text-[#52525b] hover:bg-[#f4f4f6] hover:text-[#141416]' }}">
                    <svg class="w-[18px] h-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41 13.42 20.58a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82Z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                    <span>Daftar Layanan</span>
                </a>

                <a href="{{ route('admin.report') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-[13.5px] font-medium transition {{ request()->routeIs('admin.report') ? 'bg-[#141416] text-white shadow-sm' : 'text-[#52525b] hover:bg-[#f4f4f6] hover:text-[#141416]' }}">
                    <svg class="w-[18px] h-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    <span>Laporan</span>
                </a>
            @endcan

            <div class="pt-3 mt-3 border-t border-black/[0.06]">
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-[13.5px] font-medium transition {{ request()->routeIs('profile.*') ? 'bg-[#141416] text-white shadow-sm' : 'text-[#52525b] hover:bg-[#f4f4f6] hover:text-[#141416]' }}">
                    <svg class="w-[18px] h-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 0 0-16 0"/></svg>
                    <span>Profil &amp; Akun</span>
                </a>
            </div>
        </nav>
    </div>

    <!-- Bottom Pinned User Profile & Logout -->
    <div class="pt-4 border-t border-black/[0.06] space-y-2.5">
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 p-2 rounded-xl hover:bg-[#f4f4f6] transition">
            <span class="w-9 h-9 rounded-full bg-[#141416] text-white text-xs font-semibold inline-flex items-center justify-center overflow-hidden shrink-0">
                @if(Auth::user()->avatar)
                    <img src="{{ Auth::user()->avatar }}" alt="" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                @endif
            </span>
            <div class="min-w-0 flex-1">
                <p class="text-[13px] font-semibold text-[#141416] truncate">{{ Auth::user()->name }}</p>
                <p class="text-[11px] text-[#71717a] flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full {{ $isAdmin ? 'bg-[#16a34a]' : 'bg-[#2563eb]' }}"></span>
                    {{ $isAdmin ? 'Admin' : ($isOperator ? 'Kasir' : 'Pelanggan') }}
                </p>
            </div>
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full min-h-[38px] px-3.5 rounded-xl border border-black/[0.09] bg-white hover:bg-[#f4f4f6] text-xs font-medium text-[#52525b] hover:text-[#141416] inline-flex items-center justify-center gap-2 transition">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                <span>Keluar</span>
            </button>
        </form>
    </div>
</aside>

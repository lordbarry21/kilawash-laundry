<nav x-data="{ open: false }" class="bg-white border-b border-[#1f3038]/12 sticky top-0 z-30 no-print">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-8">
                <!-- Logo Brand -->
                <a href="{{ route(auth()->user()->can('operate-laundry') ? 'dashboard' : 'customer.index') }}" class="flex items-center gap-3 shrink-0">
                    <span class="w-9 h-9 rounded-lg bg-[#0d7480] text-white inline-flex items-center justify-center">
                        <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="13" r="6"/>
                            <path d="M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/>
                            <line x1="7" y1="6" x2="7.01" y2="6"/>
                            <line x1="11" y1="6" x2="11.01" y2="6"/>
                        </svg>
                    </span>
                    <span class="leading-tight">
                        <span class="block text-base font-semibold tracking-tight text-[#1f3038]">{{ config('app.name', 'KilaWash') }}</span>
                        <span class="block text-[11px] text-[#51656f]">Manajemen Laundry</span>
                    </span>
                </a>

                <!-- Desktop Navigation Links -->
                <div class="hidden md:flex items-center gap-1">
                    <a href="{{ route(auth()->user()->can('operate-laundry') ? 'dashboard' : 'customer.index') }}"
                       class="px-3.5 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('dashboard', 'admin.dashboard') ? 'bg-[#e3f2f4] text-[#07474f]' : 'text-[#51656f] hover:bg-[#ecf0f2] hover:text-[#1f3038]' }}">
                        {{ auth()->user()->can('operate-laundry') ? 'Rekap Pesanan' : 'Layanan Laundry' }}
                    </a>

                    @can('operate-laundry')
                        <a href="{{ route('services.index') }}"
                           class="px-3.5 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('services.*') ? 'bg-[#e3f2f4] text-[#07474f]' : 'text-[#51656f] hover:bg-[#ecf0f2] hover:text-[#1f3038]' }}">
                            Data Layanan
                        </a>
                        <a href="{{ route('admin.report') }}"
                           class="px-3.5 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.report') ? 'bg-[#e3f2f4] text-[#07474f]' : 'text-[#51656f] hover:bg-[#ecf0f2] hover:text-[#1f3038]' }}">
                            Laporan Keuangan
                        </a>
                    @endcan

                    <a href="{{ route('customer.index') }}"
                       class="px-3.5 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('customer.index') ? 'bg-[#e3f2f4] text-[#07474f]' : 'text-[#51656f] hover:bg-[#ecf0f2] hover:text-[#1f3038]' }}">
                        Katalog Order
                    </a>

                    <a href="{{ route('erd.index') }}"
                       class="px-3.5 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('erd.index') ? 'bg-[#e3f2f4] text-[#07474f]' : 'text-[#51656f] hover:bg-[#ecf0f2] hover:text-[#1f3038]' }}">
                        ERD
                    </a>
                </div>
            </div>

            <!-- Right User Dropdown + Role Badge -->
            <div class="hidden md:flex items-center gap-3">
                @if(auth()->user()->can('manage-laundry'))
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-[#e3f2f4] text-[#07474f]">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#0d7480]"></span>
                        Admin — Akses Penuh
                    </span>
                @elseif(auth()->user()->can('operate-laundry'))
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-[#e0f2fe] text-[#0369a1]">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#0369a1]"></span>
                        Kasir — Operasional
                    </span>
                @endif

                <x-dropdown align="right" width="52">
                    <x-slot name="trigger">
                        <button type="button" class="inline-flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg border border-[#1f3038]/15 bg-white hover:bg-[#f7f9fa] text-sm font-medium text-[#1f3038] transition">
                            <span class="w-7 h-7 rounded-md bg-[#ecf0f2] text-[#1f3038] text-xs font-semibold inline-flex items-center justify-center overflow-hidden">
                                @if(Auth::user()->avatar)
                                    <img src="{{ Auth::user()->avatar }}" alt="" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                @endif
                            </span>
                            <span class="max-w-[140px] truncate">{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4 text-[#51656f]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Pengaturan Akun &amp; Foto
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Keluar
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Mobile Hamburger -->
            <div class="flex items-center md:hidden">
                <button @click="open = ! open" type="button" aria-label="Buka menu navigasi"
                    class="inline-flex items-center justify-center w-11 h-11 rounded-lg border border-[#1f3038]/15 text-[#1f3038] hover:bg-[#ecf0f2]">
                    <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden md:hidden border-t border-[#1f3038]/10 bg-white">
        <div class="px-4 py-3 space-y-1">
            <a href="{{ route(auth()->user()->can('operate-laundry') ? 'dashboard' : 'customer.index') }}"
               class="block px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard', 'admin.dashboard') ? 'bg-[#e3f2f4] text-[#07474f]' : 'text-[#51656f]' }}">
                {{ auth()->user()->can('operate-laundry') ? 'Rekap Pesanan' : 'Layanan Laundry' }}
            </a>
            @can('operate-laundry')
                <a href="{{ route('services.index') }}"
                   class="block px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('services.*') ? 'bg-[#e3f2f4] text-[#07474f]' : 'text-[#51656f]' }}">
                    Data Layanan
                </a>
                <a href="{{ route('admin.report') }}"
                   class="block px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('admin.report') ? 'bg-[#e3f2f4] text-[#07474f]' : 'text-[#51656f]' }}">
                    Laporan Keuangan
                </a>
            @endcan
            <a href="{{ route('customer.index') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-[#51656f]">Katalog Order</a>
            <a href="{{ route('erd.index') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-[#51656f]">ERD Database</a>
        </div>

        <div class="px-4 py-3 border-t border-[#1f3038]/10 space-y-2">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-lg bg-[#ecf0f2] text-[#1f3038] text-sm font-semibold inline-flex items-center justify-center overflow-hidden">
                    @if(Auth::user()->avatar)
                        <img src="{{ Auth::user()->avatar }}" alt="" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    @endif
                </span>
                <div>
                    <div class="text-sm font-medium text-[#1f3038]">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-[#51656f]">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <div class="flex gap-2 pt-1">
                <a href="{{ route('profile.edit') }}" class="flex-1 text-center px-3 py-2 rounded-lg border border-[#1f3038]/20 text-sm font-medium text-[#1f3038]">Pengaturan Akun</a>
                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full px-3 py-2 rounded-lg bg-[#ecf0f2] text-sm font-medium text-[#1f3038]">Keluar</button>
                </form>
            </div>
        </div>
    </div>
</nav>

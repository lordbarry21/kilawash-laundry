<nav x-data="{ open: false }" class="bg-[#f4f4f6]/90 backdrop-blur-md sticky top-0 z-30 border-b border-black/[0.05] no-print">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 gap-4">
            <!-- Brand Mark (Zentra minimal style) -->
            <a href="{{ route(auth()->user()->can('operate-laundry') ? 'dashboard' : 'customer.index') }}" class="flex items-center gap-2.5 shrink-0">
                <span class="w-8 h-8 rounded-[9px] bg-[#141416] text-white inline-flex items-center justify-center shadow-sm">
                    <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="13" r="5.5"/>
                        <rect x="4" y="3" width="16" height="18" rx="2.5"/>
                        <line x1="8" y1="6.5" x2="8.01" y2="6.5"/>
                        <line x1="11.5" y1="6.5" x2="11.51" y2="6.5"/>
                    </svg>
                </span>
                <span class="text-[17px] font-semibold tracking-[-0.03em] text-[#141416]">kilawash</span>
            </a>

            <!-- Center Pill Navigation (No ERD link exposed!) -->
            <div class="hidden md:flex items-center gap-1">
                <a href="{{ route(auth()->user()->can('operate-laundry') ? 'dashboard' : 'customer.index') }}"
                   class="px-4 py-2 rounded-full text-[13.5px] font-medium transition {{ request()->routeIs('dashboard', 'admin.dashboard') ? 'bg-[#141416] text-white shadow-sm' : 'text-[#52525b] hover:text-[#141416] hover:bg-black/[0.04]' }}">
                    {{ auth()->user()->can('operate-laundry') ? 'Overview' : 'Layanan Laundry' }}
                </a>

                @can('operate-laundry')
                    <a href="{{ route('services.index') }}"
                       class="px-4 py-2 rounded-full text-[13.5px] font-medium transition {{ request()->routeIs('services.*') ? 'bg-[#141416] text-white shadow-sm' : 'text-[#52525b] hover:text-[#141416] hover:bg-black/[0.04]' }}">
                        Data Layanan
                    </a>
                    <a href="{{ route('admin.report') }}"
                       class="px-4 py-2 rounded-full text-[13.5px] font-medium transition {{ request()->routeIs('admin.report') ? 'bg-[#141416] text-white shadow-sm' : 'text-[#52525b] hover:text-[#141416] hover:bg-black/[0.04]' }}">
                        Laporan
                    </a>
                @endcan

                <a href="{{ route('customer.index') }}"
                   class="px-4 py-2 rounded-full text-[13.5px] font-medium transition {{ request()->routeIs('customer.index') ? 'bg-[#141416] text-white shadow-sm' : 'text-[#52525b] hover:text-[#141416] hover:bg-black/[0.04]' }}">
                    Pemesanan
                </a>
            </div>

            <!-- Right Account Pill -->
            <div class="hidden md:flex items-center gap-2.5">
                @if(auth()->user()->can('manage-laundry'))
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-white border border-black/[0.07] text-[#141416]">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#16a34a]"></span>
                        Admin
                    </span>
                @elseif(auth()->user()->can('operate-laundry'))
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-white border border-black/[0.07] text-[#141416]">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#2563eb]"></span>
                        Kasir
                    </span>
                @endif

                <x-dropdown align="right" width="52">
                    <x-slot name="trigger">
                        <button type="button" class="inline-flex items-center gap-2 pl-1.5 pr-3 py-1 rounded-full bg-white border border-black/[0.08] hover:border-black/20 text-[13px] font-medium text-[#141416] transition shadow-xs">
                            <span class="w-7 h-7 rounded-full bg-[#141416] text-white text-xs font-semibold inline-flex items-center justify-center overflow-hidden">
                                @if(Auth::user()->avatar)
                                    <img src="{{ Auth::user()->avatar }}" alt="" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                @endif
                            </span>
                            <span class="max-w-[130px] truncate">{{ Auth::user()->name }}</span>
                            <svg class="w-3.5 h-3.5 text-[#71717a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white border border-black/10 text-[#141416]">
                    <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M4 12h16M4 17h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Sheet -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden md:hidden bg-white border-t border-black/[0.06] shadow-lg">
        <div class="px-4 py-3 space-y-1">
            <a href="{{ route(auth()->user()->can('operate-laundry') ? 'dashboard' : 'customer.index') }}"
               class="block px-4 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('dashboard', 'admin.dashboard') ? 'bg-[#141416] text-white' : 'text-[#52525b]' }}">
                {{ auth()->user()->can('operate-laundry') ? 'Overview' : 'Layanan Laundry' }}
            </a>
            @can('operate-laundry')
                <a href="{{ route('services.index') }}"
                   class="block px-4 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('services.*') ? 'bg-[#141416] text-white' : 'text-[#52525b]' }}">
                    Data Layanan
                </a>
                <a href="{{ route('admin.report') }}"
                   class="block px-4 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.report') ? 'bg-[#141416] text-white' : 'text-[#52525b]' }}">
                    Laporan
                </a>
            @endcan
            <a href="{{ route('customer.index') }}" class="block px-4 py-2.5 rounded-xl text-sm font-medium text-[#52525b]">Pemesanan</a>
        </div>

        <div class="px-4 py-3 border-t border-black/[0.06] flex items-center justify-between gap-2">
            <a href="{{ route('profile.edit') }}" class="text-sm font-medium text-[#141416] underline">Pengaturan Akun</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-full bg-[#f4f4f6] text-xs font-medium text-[#141416]">Keluar</button>
            </form>
        </div>
    </div>
</nav>

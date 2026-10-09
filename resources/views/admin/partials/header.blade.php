<header x-data="{ dropdownOpen: false, notifOpen: false }"
    class="sticky top-0 z-30 flex w-full border-b border-gray-200 bg-white/95 backdrop-blur-sm
           dark:border-gray-800 dark:bg-gray-900/95">
    <div class="flex flex-grow items-center justify-between px-3 sm:px-4 py-3 md:px-6">

        <!-- LEFT -->
        <div class="flex items-center gap-2 sm:gap-3">

            <!-- Sidebar Toggle (Mobile) -->
            <button @click="sidebarToggle = !sidebarToggle"
                type="button"
                aria-label="Toggle Menu"
                class="lg:hidden rounded-xl border border-gray-200 p-2 text-gray-600 hover:bg-gray-50 active:bg-gray-100
                       dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition">
                <svg width="22" height="22" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <!-- Logo (Mobile) -->
            <a href="{{ route('dashboard') }}" class="lg:hidden flex items-center gap-2">
                <img class="h-7 w-7 object-contain dark:hidden" src="{{ asset('storage/icons/hospital.png') }}" alt="Logo">
                <img class="h-7 w-7 object-contain hidden dark:block" src="{{ asset('storage/icons/hospital.png') }}" alt="Logo">
                <span class="text-xs font-black tracking-tight text-gray-800 dark:text-white truncate max-w-[130px] sm:max-w-xs">
                    {{ Str::limit(auth()->user()->clinic?->name ?? 'Sport Therapy', 16) }}
                </span>
            </a>
        </div>

        <!-- RIGHT -->
        <div class="flex items-center gap-2 sm:gap-4">
            {{-- 
            <!-- 🔔 NOTIFICATION -->
            <div class="relative">
                <button
                    @click="notifOpen = !notifOpen"
                    class="relative rounded-lg border border-gray-200 p-2
                           dark:border-gray-700 dark:text-gray-400"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11
                                 a6 6 0 10-12 0v3.159
                                 c0 .538-.214 1.055-.595 1.436L4 17h5m6 0
                                 a3 3 0 11-6 0h6z"/>
                    </svg>

                    <!-- BADGE -->
                    <span
                        class="absolute -top-1 -right-1 flex h-5 w-5 items-center
                               justify-center rounded-full bg-red-600 text-xs text-white">
                        3
                    </span>
                </button>

                <!-- NOTIFICATION DROPDOWN -->
                <div
                    x-show="notifOpen"
                    @click.outside="notifOpen = false"
                    x-transition
                    class="absolute right-0 mt-3 w-80 rounded-xl border
                           border-gray-200 bg-white shadow-lg
                           dark:border-gray-800 dark:bg-gray-900"
                >
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-800">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                            Notifications
                        </h4>
                    </div>

                    <ul class="max-h-72 overflow-y-auto">
                        <!-- ITEM -->
                        <li>
                            <a href="#"
                               class="flex gap-3 px-4 py-3 hover:bg-gray-100
                                      dark:hover:bg-white/5">
                                <div
                                    class="flex h-9 w-9 items-center justify-center
                                           rounded-full bg-blue-100 text-blue-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M13 16h-1v-4h-1m1-4h.01M12 18a9 9 0 100-18 9 9 0 000 18z"/>
                                    </svg>
                                </div>

                                <div class="flex-1">
                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                        New report is ready
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        2 minutes ago
                                    </p>
                                </div>
                            </a>
                        </li>

                        <!-- ITEM -->
                        <li>
                            <a href="#"
                               class="flex gap-3 px-4 py-3 hover:bg-gray-100
                                      dark:hover:bg-white/5">
                                <div
                                    class="flex h-9 w-9 items-center justify-center
                                           rounded-full bg-green-100 text-green-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>

                                <div class="flex-1">
                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                        Profile updated successfully
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        1 hour ago
                                    </p>
                                </div>
                            </a>
                        </li>
                    </ul>

                    <div class="border-t border-gray-200 dark:border-gray-800 p-2">
                        <a href="#"
                           class="block rounded-lg px-3 py-2 text-center text-sm
                                  text-primary hover:bg-gray-100
                                  dark:hover:bg-white/5">
                            View all notifications
                        </a>
                    </div>
                </div>
            </div> --}}

            <!-- 👤 USER DROPDOWN -->
            <div class="relative">
                <button @click="dropdownOpen = !dropdownOpen" class="flex items-center gap-3">
                    <img class="h-9 w-9 rounded-full object-cover border border-gray-200 dark:border-gray-700"
                        src="{{ Auth::user()->avatar ?? asset('admin/images/user/user-37.jpg') }}">

                    <div class="hidden text-left sm:block">
                        <span class="block text-xs font-bold text-gray-800 dark:text-gray-200 leading-tight">
                            {{ Auth::user()->name }}
                        </span>
                        <span class="block text-[10px] text-gray-500 dark:text-gray-400 font-medium">
                            @if(Auth::user()->isSaasAdmin())
                                <span class="text-blue-600 font-bold dark:text-blue-400">SaaS Superadmin</span>
                            @elseif(Auth::user()->isClinicAdmin())
                                <span class="text-purple-600 font-bold dark:text-purple-400">Admin Klinik</span>
                            @else
                                <span class="text-emerald-600 font-bold dark:text-emerald-400">Operator Klinik</span>
                            @endif
                        </span>
                    </div>

                    <svg :class="dropdownOpen && 'rotate-180'" class="h-4 w-4 text-gray-500 transition-transform"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- USER MENU -->
                <div x-show="dropdownOpen" @click.outside="dropdownOpen = false" x-transition
                    class="absolute right-0 mt-3 w-56 max-w-[calc(100vw-2rem)] rounded-2xl border
                           border-gray-200 bg-white p-2 shadow-2xl
                           dark:border-gray-800 dark:bg-gray-900 z-50">

                    <div class="px-3 py-2 border-b border-gray-100 dark:border-gray-800 mb-1">
                        <p class="text-xs font-bold text-gray-900 dark:text-white">{{ Auth::user()->name }}</p>
                        <p class="text-[11px] text-gray-500 truncate">{{ Auth::user()->email }}</p>
                        <p class="text-[10px] mt-1 font-semibold text-blue-600 dark:text-blue-400">
                            {{ Auth::user()->role_display }}
                        </p>
                    </div>

                    @if(Auth::user()->isSaasAdmin())
                        <a href="{{ route('saas.dashboard') }}"
                           class="block w-full rounded-lg px-3 py-2 text-left text-xs font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5 transition">
                            Panel SaaS Platform
                        </a>
                        <a href="{{ route('saas.clinics.index') }}"
                           class="block w-full rounded-lg px-3 py-2 text-left text-xs font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5 transition">
                            Kelola Mitra Klinik
                        </a>
                        <a href="{{ route('saas.cms.index') }}"
                           class="block w-full rounded-lg px-3 py-2 text-left text-xs font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5 transition">
                            CMS Landing Page
                        </a>
                    @endif

                    <a href="{{ url('/') }}" target="_blank"
                       class="block w-full rounded-lg px-3 py-2 text-left text-xs font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5 transition">
                        Lihat Landing Page ↗
                    </a>

                    <div class="my-1 border-t border-gray-100 dark:border-gray-800"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full rounded-lg px-3 py-2 text-left text-xs font-semibold
                                   text-red-600 hover:bg-red-50
                                   dark:hover:bg-red-500/10 transition">
                            Logout
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</header>

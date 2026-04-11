<header x-data="{ dropdownOpen: false, notifOpen: false }"
    class="sticky top-0 z-50 flex w-full border-b border-gray-200 bg-white
           dark:border-gray-800 dark:bg-gray-900">
    <div class="flex flex-grow items-center justify-between px-4 py-4 md:px-6">

        <!-- LEFT -->
        <div class="flex items-center gap-2">

            <!-- Sidebar Toggle (Mobile) -->
            <button @click="sidebarToggle = !sidebarToggle"
                class="lg:hidden rounded-lg border border-gray-200 p-2
                       dark:border-gray-700 dark:text-gray-400">
                <svg width="20" height="20" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <!-- Logo (Mobile) -->
            <a href="{{ route('dashboard') }}" class="lg:hidden">
                <img class="h-8 dark:hidden" src="{{ asset('admin/images/logo/logo.svg') }}">
                <img class="hidden h-8 dark:block" src="{{ asset('admin/images/logo/logo-dark.svg') }}">
            </a>
        </div>

        <!-- RIGHT -->
        <div class="flex items-center gap-4">
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
                    <img class="h-10 w-10 rounded-full object-cover"
                        src="{{ Auth::user()->avatar ?? asset('admin/images/user/user-37.jpg') }}">

                    <span class="hidden text-sm font-medium text-gray-700 dark:text-gray-300 sm:block">
                        {{-- {{ Auth::user()->name }} --}}
                    </span>

                    <svg :class="dropdownOpen && 'rotate-180'" class="h-4 w-4 text-gray-500 transition-transform"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- USER MENU -->
                <div x-show="dropdownOpen" @click.outside="dropdownOpen = false" x-transition
                    class="absolute right-0 mt-3 w-56 rounded-xl border
                           border-gray-200 bg-white p-3 shadow-lg
                           dark:border-gray-800 dark:bg-gray-900">

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full rounded-lg px-3 py-2 text-left text-sm
                                   text-red-600 hover:bg-red-50
                                   dark:hover:bg-red-500/10">
                            Logout
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</header>

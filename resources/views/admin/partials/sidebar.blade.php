<aside
    x-data="{ selected: null }"
    :class="sidebarToggle ? 'translate-x-0 lg:w-[90px]' : '-translate-x-full'"
    class="sidebar fixed left-0 top-0 z-50 flex h-screen w-[290px] flex-col
           overflow-y-hidden border-r border-gray-200 bg-white px-5
           dark:border-gray-800 dark:bg-black lg:static lg:translate-x-0"
>
    <!-- HEADER -->
    <div
        :class="sidebarToggle ? 'justify-center' : 'justify-between'"
        class="flex items-center gap-2 pt-8 pb-7"
    >
        <a href="{{ route('dashboard') }}">
            <span class="logo" :class="sidebarToggle ? 'hidden' : ''">
                <img class="dark:hidden"
                     src="{{ asset('admin/images/logo/logo.svg') }}" />
                <img class="hidden dark:block"
                     src="{{ asset('admin/images/logo/logo-dark.svg') }}" />
            </span>

            <img
                class="logo-icon"
                :class="sidebarToggle ? 'lg:block' : 'hidden'"
                src="{{ asset('admin/images/logo/logo-icon.svg') }}"
            />
        </a>
    </div>

    <!-- MENU -->
    <div class="flex flex-col overflow-y-auto no-scrollbar">
        <nav>

            <!-- GROUP -->
            <h3 class="mb-4 text-xs uppercase text-gray-400">
                <span :class="sidebarToggle ? 'lg:hidden' : ''">Menu</span>
            </h3>

            <ul class="flex flex-col gap-4 mb-6">

                <!-- DASHBOARD -->
                <li>
                    <a
                        href="{{ route('dashboard') }}"
                        class="menu-item group flex items-center gap-3
                        {{ request()->routeIs('dashboard')
                            ? 'menu-item-active'
                            : 'menu-item-inactive' }}"
                    >
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 12l2-2 7-7 7 7v10a1 1 0 01-1 1h-3v-6H9v6H6a1 1 0 01-1-1z"/>
                        </svg>

                        <span class="menu-item-text"
                              :class="sidebarToggle ? 'lg:hidden' : ''">
                            Dashboard
                        </span>
                    </a>
                </li>

                <!-- CALENDAR -->
                <li>
                    <a
                        href="#"
                        class="menu-item group flex items-center gap-3
                        {{ request()->routeIs('admin.perizinan')
                            ? 'menu-item-active'
                            : 'menu-item-inactive' }}"
                    >
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6M7 4h8l4 4v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z" />
                        </svg>

                        <span class="menu-item-text"
                              :class="sidebarToggle ? 'lg:hidden' : ''">
                            Perizinan
                        </span>
                    </a>
                </li>

                 <li>
                    <a
                        href="#"
                        class="menu-item group flex items-center gap-3
                        {{ request()->routeIs('admin.pengaduan')
                            ? 'menu-item-active'
                            : 'menu-item-inactive' }}"
                    >
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4-.84L3 20l1.34-3.58A7.97 7.97 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>

                        <span class="menu-item-text"
                              :class="sidebarToggle ? 'lg:hidden' : ''">
                            Pengaduan
                        </span>
                    </a>
                </li>
            </ul>

            <!-- OTHERS -->
            <h3 class="mb-4 text-xs uppercase text-gray-400">
                <span :class="sidebarToggle ? 'lg:hidden' : ''">Master Data</span>
            </h3>

            <ul class="flex flex-col gap-4 mb-6">

                                <li>
                    <a
                        href="#"
                        class="menu-item group flex items-center gap-3
                        {{ request()->routeIs('admin.pengaju')
                            ? 'menu-item-active'
                            : 'menu-item-inactive' }}"
                    >
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 21h18M6 21V7a2 2 0 012-2h8a2 2 0 012 2v14M9 9h.01M9 13h.01M9 17h.01M15 9h.01M15 13h.01M15 17h.01" />
                    </svg>

                        <span class="menu-item-text"
                              :class="sidebarToggle ? 'lg:hidden' : ''">
                            Data Pengaju
                        </span>
                    </a>
                </li>

                <li>
                    <a
                        href="{{ route("news.index") }}"
                        class="menu-item group flex items-center gap-3
                        {{ request()->routeIs('news.index')
                            ? 'menu-item-active'
                            : 'menu-item-inactive' }}"
                    >
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h11l5 5v9a2 2 0 01-2 2z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 9h6M9 13h6M9 17h4" />
                        </svg>

                        <span class="menu-item-text"
                              :class="sidebarToggle ? 'lg:hidden' : ''">
                            News
                        </span>
                    </a>
                </li>
                                <!-- FORMS (DROPDOWN) -->
                {{-- <li x-data="{ open: {{ request()->routeIs('admin.forms.*') ? 'true' : 'false' }} }">

                    <a
                        href="#"
                        @click.prevent="open = !open"
                        class="menu-item group flex items-center justify-between
                        {{ request()->routeIs('admin.forms.*')
                            ? 'menu-item-active'
                            : 'menu-item-inactive' }}"
                    >
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 6h11M9 12h11M9 18h11" />
                                <circle cx="5" cy="6" r="1" />
                                <circle cx="5" cy="12" r="1" />
                                <circle cx="5" cy="18" r="1" />
                            </svg>


                            <span class="menu-item-text"
                                  :class="sidebarToggle ? 'lg:hidden' : ''">
                                Data Klinik
                            </span>
                        </div>

                        <!-- ARROW -->
                        <span
                            class="transition-transform duration-200"
                            :class="[
                                sidebarToggle ? 'lg:hidden' : '',
                                open ? 'rotate-180' : ''
                            ]"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 9l-7 7-7-7"/>
                            </svg>
                        </span>
                    </a>

                    <!-- SUB MENU -->
                    <div x-show="open" x-transition>
                        <ul class="mt-2 pl-9 space-y-2">
                            <li>
                                <a
                                    href="#"
                                    class="menu-dropdown-item flex items-center gap-2
                                    {{ request()->routeIs('admin.forms.elements')
                                        ? 'menu-dropdown-item-active'
                                        : 'menu-dropdown-item-inactive' }}"
                                >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <!-- User -->
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M15 7a3 3 0 11-6 0 3 3 0 016 0Z"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M4 21a8 8 0 0116 0"
                                    />

                                    <!-- Medical Cross -->
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M18 10v2m-1-1h2"
                                    />
                                </svg>


                                    User Klinik
                                </a>
                            </li>
                                                        <li>
                                <a
                                    href="#"
                                    class="menu-dropdown-item flex items-center gap-2
                                    {{ request()->routeIs('admin.forms.elements')
                                        ? 'menu-dropdown-item-active'
                                        : 'menu-dropdown-item-inactive' }}"
                                >
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6 3v4a4 4 0 008 0V3M10 13v2a4 4 0 004 4h1" />
                                    <circle cx="19" cy="17" r="2" />
                                </svg>

                                    Tindakan Klinik
                                </a>
                            </li>
                        </ul>
                    </div>
                </li> --}}
            </ul>

                        <!-- OTHERS -->
            <h3 class="mb-4 text-xs uppercase text-gray-400">
                <span :class="sidebarToggle ? 'lg:hidden' : ''">Settings</span>
            </h3>

            <ul class="flex flex-col gap-4 mb-6">

                <li>
                    <a
                        href="{{ route("roles.index") }}"
                        class="menu-item group flex items-center gap-3
                        {{ request()->routeIs('roles.index')
                            ? 'menu-item-active'
                            : 'menu-item-inactive' }}"
                    >
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3l7 3v6c0 4.5-3 8.5-7 9-4-.5-7-4.5-7-9V6l7-3z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9a2 2 0 100 4 2 2 0 000-4zM8.5 16c0-1.5 7-1.5 7 0" />
                        </svg>

                        <span class="menu-item-text"
                              :class="sidebarToggle ? 'lg:hidden' : ''">
                            Roles
                        </span>
                    </a>
                </li>

                                <li>
                    <a
                        href="{{ route("users.index") }}"
                        class="menu-item group flex items-center gap-3
                        {{ request()->routeIs('users.index')
                            ? 'menu-item-active'
                            : 'menu-item-inactive' }}"
                    >
                        <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <!-- User -->
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M15 7a3 3 0 11-6 0 3 3 0 016 0Z"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M4 21a8 8 0 0116 0"
                                    />

                                    <!-- Medical Cross -->
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M18 10v2m-1-1h2"
                                    />
                                </svg>
                        <span class="menu-item-text"
                              :class="sidebarToggle ? 'lg:hidden' : ''">
                            User
                        </span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>

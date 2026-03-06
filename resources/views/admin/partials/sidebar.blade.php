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
            <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3"
            >
                {{-- LOGO --}}
                <img
                    class="w-[50px] h-[50px] dark:hidden"
                    src="{{ asset('/storage/images/klinik-icon.jpg') }}"
                    alt="Dishub"
                />
                <img
                    class="w-[50px] h-[50px] hidden dark:block"
                    src="{{ asset('/storage/images/klinik-icon.jpg') }}"
                    alt="Dishub"
                />

                {{-- TEXT --}}
                <span
                    class="text-base font-semibold whitespace-nowrap"
                    :class="sidebarToggle ? 'hidden' : ''"
                >
                    Sistem Informasi Klinik
                </span>
            </a>

            {{-- ICON SAAT SIDEBAR COLLAPSE --}}
            <img
                class="logo-icon"
                :class="sidebarToggle ? 'lg:block' : 'hidden'"
                src="{{ asset('/storage/images/logo-icon.svg') }}"
            />
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

                 <li>
                    <a
                        href="{{ route('antrian') }}"
                        class="menu-item group flex items-center gap-3
                        {{ request()->routeIs('antrian*')
                            ? 'menu-item-active'
                            : 'menu-item-inactive' }}"
                    >
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 7h16v4a2 2 0 010 4v4H4v-4a2 2 0 010-4V7z
                        M9 11h6
                        M9 15h4"/>
                    </svg>
                        <span class="menu-item-text"
                              :class="sidebarToggle ? 'lg:hidden' : ''">
                            Antrian Admisi
                        </span>
                    </a>
                </li>

                <!-- CALENDAR -->
                <li>
                    <a
                        href="{{ route('registrasi.index') }}"
                        class="menu-item group flex items-center gap-3
                        {{ request()->routeIs('registrasi*')
                            ? 'menu-item-active'
                            : 'menu-item-inactive' }}"
                    >
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6M7 4h8l4 4v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z" />
                        </svg>

                        <span class="menu-item-text"
                              :class="sidebarToggle ? 'lg:hidden' : ''">
                            Registrasi
                        </span>
                    </a>
                </li>

                                <li>
                    <a
                        href="{{ route('perizinan') }}"
                        class="menu-item group flex items-center gap-3
                        {{ request()->routeIs('perizinan*')
                            ? 'menu-item-active'
                            : 'menu-item-inactive' }}"
                    >
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 3h6a2 2 0 012 2v2h-10V5a2 2 0 012-2z
                            M7 7h10v14H7z
                            M12 11v4m-2-2h4"/>
                        </svg>

                        <span class="menu-item-text"
                              :class="sidebarToggle ? 'lg:hidden' : ''">
                            Amnesa Perawat
                        </span>
                    </a>
                </li>

                <li>
                    <a
                        href="{{ route('perizinan') }}"
                        class="menu-item group flex items-center gap-3
                        {{ request()->routeIs('perizinan*')
                            ? 'menu-item-active'
                            : 'menu-item-inactive' }}"
                    >
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 11a4 4 0 100-8 4 4 0 000 8z
                            M4 21a8 8 0 0116 0
                            M12 14v4m-2-2h4"/>
                        </svg>

                        <span class="menu-item-text"
                              :class="sidebarToggle ? 'lg:hidden' : ''">
                            Amnesa Dokter
                        </span>
                    </a>
                </li>


                <li>
                    <a
                        href="{{ route('perizinan') }}"
                        class="menu-item group flex items-center gap-3
                        {{ request()->routeIs('perizinan*')
                            ? 'menu-item-active'
                            : 'menu-item-inactive' }}"
                    >
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6M7 4h8l4 4v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z" />
                        </svg>

                        <span class="menu-item-text"
                              :class="sidebarToggle ? 'lg:hidden' : ''">
                            Tindakan Dokter
                        </span>
                    </a>
                </li>

                <li>
                    <a
                        href="{{ route('perizinan') }}"
                        class="menu-item group flex items-center gap-3
                        {{ request()->routeIs('perizinan*')
                            ? 'menu-item-active'
                            : 'menu-item-inactive' }}"
                    >
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 3h7l5 5v13H7z
                        M14 3v5h5
                        M9 15l6-6
                        M10 10l4 4"/>
                    </svg>

                        <span class="menu-item-text"
                              :class="sidebarToggle ? 'lg:hidden' : ''">
                            Resep
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
                        href="{{ route("listpengaju") }}"
                        class="menu-item group flex items-center gap-3
                        {{ request()->routeIs('pengaju*')
                            ? 'menu-item-active'
                            : 'menu-item-inactive' }}"
                    >
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 21V7a2 2 0 012-2h3V3h8v2h3a2 2 0 012 2v14
                        M9 21v-4h6v4
                        M7 9h2M7 13h2M15 9h2M15 13h2"/>
                    </svg>
                        <span class="menu-item-text"
                              :class="sidebarToggle ? 'lg:hidden' : ''">
                            Klinik
                        </span>
                    </a>
                </li>

                <li>
                    <a
                        href="{{ route("listpengaju") }}"
                        class="menu-item group flex items-center gap-3
                        {{ request()->routeIs('pengaju*')
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
                                </svg>
                        <span class="menu-item-text"
                              :class="sidebarToggle ? 'lg:hidden' : ''">
                            Pasien
                        </span>
                    </a>
                </li>

                <li>
                    <a
                        href="{{ route("listpengaju") }}"
                        class="menu-item group flex items-center gap-3
                        {{ request()->routeIs('pengaju*')
                            ? 'menu-item-active'
                            : 'menu-item-inactive' }}"
                    >
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10l9-6 9 6M4 10h16v10H4V10z"/>
                        </svg>

                        <span class="menu-item-text"
                              :class="sidebarToggle ? 'lg:hidden' : ''">
                            Eselon
                        </span>
                    </a>
                </li>

                <li>
                    <a
                        href="{{ route("listpengaju") }}"
                        class="menu-item group flex items-center gap-3
                        {{ request()->routeIs('pengaju*')
                            ? 'menu-item-active'
                            : 'menu-item-inactive' }}"
                    >
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v8m-4-4h8M6 20h12a2 2 0 002-2v-5a6 6 0 10-12 0v5a2 2 0 002 2z"/>
                        </svg>

                        <span class="menu-item-text"
                              :class="sidebarToggle ? 'lg:hidden' : ''">
                            Dokter
                        </span>
                    </a>
                </li>

                <li>
                    <a
                        href="{{ route("listpengaju") }}"
                        class="menu-item group flex items-center gap-3
                        {{ request()->routeIs('pengaju*')
                            ? 'menu-item-active'
                            : 'menu-item-inactive' }}"
                    >
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l-6 6m0-6l6 6m4-14l6 6m-3-9l6 6M14 3l7 7-4 4-7-7 4-4z"/>
                        </svg>

                        <span class="menu-item-text"
                              :class="sidebarToggle ? 'lg:hidden' : ''">
                            Obat
                        </span>
                    </a>
                </li>

                <li>
                    <a
                        href="{{ route("listpengaju") }}"
                        class="menu-item group flex items-center gap-3
                        {{ request()->routeIs('pengaju*')
                            ? 'menu-item-active'
                            : 'menu-item-inactive' }}"
                    >
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-3-3v6m-7 4h14a2 2 0 002-2V7l-5-5H7a2 2 0 00-2 2v13a2 2 0 002 2z"/>
                        </svg>

                        <span class="menu-item-text"
                              :class="sidebarToggle ? 'lg:hidden' : ''">
                            ICD
                        </span>
                    </a>
                </li>

                <li>
                    <a
                        href="{{ route("listpengaju") }}"
                        class="menu-item group flex items-center gap-3
                        {{ request()->routeIs('pengaju*')
                            ? 'menu-item-active'
                            : 'menu-item-inactive' }}"
                    >
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14.7 6.3l3 3m-9.4 9.4l-4 1 1-4 9.4-9.4 3 3-9.4 9.4z"/>
                        </svg>

                        <span class="menu-item-text"
                              :class="sidebarToggle ? 'lg:hidden' : ''">
                            Tindakan
                        </span>
                    </a>
                </li>

                <li>
                    <a
                        href="{{ route("listpengaju") }}"
                        class="menu-item group flex items-center gap-3
                        {{ request()->routeIs('pengaju*')
                            ? 'menu-item-active'
                            : 'menu-item-inactive' }}"
                    >
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 21v-7a2 2 0 012-2h3v9M14 21v-9h3a2 2 0 012 2v7M3 10l9-7 9 7"/>
                        </svg>

                        <span class="menu-item-text"
                              :class="sidebarToggle ? 'lg:hidden' : ''">
                            Poly Klinik
                        </span>
                    </a>
                </li>

                <li x-data="{ open: {{ request()->routeIs('admin.forms.*') ? 'true' : 'false' }} }">

                    <a
                        href="#"
                        @click.prevent="open = !open"
                        class="menu-item group flex items-center justify-between
                        {{ request()->routeIs('admin.forms.*')
                            ? 'menu-item-active'
                            : 'menu-item-inactive' }}"
                    >
                        <div class="flex items-center gap-3">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 21V8a2 2 0 012-2h4V4a2 2 0 012-2h2a2 2 0 012 2v2h4a2 2 0 012 2v13M9 14h6m-3-3v6"/>
                            </svg>

                            <span class="menu-item-text"
                                  :class="sidebarToggle ? 'lg:hidden' : ''">
                                Poly
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
                                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4h16v6H4zM4 14h16v6H4z"/>
                                </svg>


                                    ICD Poly
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
                                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11a4 4 0 10-8 0m8 0v1a4 4 0 01-8 0v-1m4-7v4m-2-2h4"/>
                                </svg>

                                    Dokter Poly
                                </a>
                            </li>

                             </li>
                                                        <li>
                                <a
                                    href="#"
                                    class="menu-dropdown-item flex items-center gap-2
                                    {{ request()->routeIs('admin.forms.elements')
                                        ? 'menu-dropdown-item-active'
                                        : 'menu-dropdown-item-inactive' }}"
                                >
                                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7m-2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h6"/>
                                </svg>

                                    Tindakan Poly
                                </a>
                            </li>
                        </ul>
                    </div>
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
                        href="{{ route("listpengaju") }}"
                        class="menu-item group flex items-center gap-3
                        {{ request()->routeIs('pengaju*')
                            ? 'menu-item-active'
                            : 'menu-item-inactive' }}"
                    >
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h6m-9 10h14a2 2 0 002-2V7H4v12a2 2 0 002 2z"/>
                        </svg>

                        <span class="menu-item-text"
                              :class="sidebarToggle ? 'lg:hidden' : ''">
                            Jadwal Dokter
                        </span>
                    </a>
                </li>

                <li>
                    <a
                        href="{{ route("roles.index") }}"
                        class="menu-item group flex items-center gap-3
                        {{ request()->routeIs('roles*')
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
                        {{ request()->routeIs('users*')
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

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Admin Dashboard')</title>

    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>

<body
    x-data="{ page: 'ecommerce', loaded: true, darkMode: false, stickyMenu: false, sidebarToggle: false, scrollTop: false, showAlert: false, alertMessage: '', alertType: '' }"
    x-init="
        darkMode = JSON.parse(localStorage.getItem('darkMode'));
        $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)));
    "
    :class="{'dark bg-gray-900': darkMode === true}"
>
    {{-- Preloader --}}
    @include('admin.partials.preloader')

    <div class="flex h-screen overflow-hidden">
        {{-- Sidebar --}}
        @include('admin.partials.sidebar')

        <div class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto">
            {{-- Overlay --}}
            @include('admin.partials.overlay')

            {{-- Header --}}
            @include('admin.partials.header')

            {{-- MAIN CONTENT --}}
            <main>
                <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
                    
                    {{-- Alert --}}
                    <template x-if="showAlert">
                        <div 
                            x-transition
                            class="mb-4 p-4 rounded shadow text-white"
                            :class="alertType === 'success' ? 'bg-green-500' : 'bg-red-500'"
                        >
                            <div class="flex justify-between items-center">
                                <span x-text="alertMessage"></span>
                                <button type="button" @click="showAlert = false" class="ml-4 font-bold">×</button>
                            </div>
                        </div>
                    </template>

                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>
</html>

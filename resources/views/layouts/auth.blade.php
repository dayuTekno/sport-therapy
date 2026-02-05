<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />

  <title>@yield('title', 'Auth')</title>

  @vite([
    'resources/css/admin.css',
    'resources/js/admin.js'
  ])
</head>

<body
  x-data="{
    page: 'auth',
    loaded: true,
    darkMode: JSON.parse(localStorage.getItem('darkMode')) ?? false,
    stickyMenu: false,
    sidebarToggle: false,
    scrollTop: false
  }"
  x-init="$watch('darkMode', v => localStorage.setItem('darkMode', JSON.stringify(v)))"
  :class="{ 'dark bg-gray-900': darkMode }"
>

  {{-- Preloader --}}
  @include('admin.partials.preloader')

  <div class="relative p-6 bg-white z-1 dark:bg-gray-900 sm:p-0">
    @yield('content')
  </div>

</body>
</html>

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="theme-color" content="#2563eb">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="default">
  <meta name="format-detection" content="telephone=no">

  <title>@yield('title', 'Auth') - SportClinic</title>

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

  <div class="relative min-h-screen bg-slate-50 dark:bg-gray-900 flex flex-col justify-center">
    @yield('content')
  </div>

</body>
</html>

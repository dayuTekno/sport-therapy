@extends('layouts.auth')

@section('title', 'Sign In')

@section('content')
<div
  class="relative flex flex-col justify-center w-full h-screen dark:bg-gray-900 sm:p-0 lg:flex-row"
>
  <!-- FORM -->
  <div class="flex flex-col flex-1 w-full lg:w-1/2">

    <div class="flex flex-col justify-center flex-1 w-full max-w-md mx-auto">
      <h1 class="mb-2 font-semibold text-gray-800 text-title-sm dark:text-white/90">
        Sign In
      </h1>

      <p class="mb-6 text-sm text-gray-500 dark:text-gray-400">
        Enter your email and password to sign in!
      </p>

      <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email -->
        <div>
          <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
            Email
          </label>
          <input type="email" name="email" required
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
        </div>

        <!-- Password -->
        <div x-data="{ showPassword: false }">
          <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
            Password
          </label>
          <div class="relative">
            <input
              :type="showPassword ? 'text' : 'password'"
              name="password"
              required
              class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 pr-11 text-sm dark:border-gray-700 dark:bg-gray-900" />
            <span
              @click="showPassword = !showPassword"
              class="absolute right-4 top-1/2 -translate-y-1/2 cursor-pointer">
              👁
            </span>
          </div>
        </div>

        <!-- Remember -->
        <div class="flex items-center justify-between">
          <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-400">
            <input type="checkbox" name="remember">
            Remember me
          </label>

          <a href="{{ route('password.request') }}"
             class="text-sm text-brand-500">
            Forgot password?
          </a>
        </div>

        <!-- Submit -->
        <button
          class="w-full py-3 text-sm font-medium text-white rounded-lg bg-brand-500 hover:bg-brand-600">
          Sign In
        </button>
      </form>

      <p class="mt-5 text-sm text-gray-700 dark:text-gray-400">
        Don't have an account?
        <a href="{{ route('register') }}" class="text-brand-500">Sign Up</a>
      </p>
    </div>
  </div>

  <!-- RIGHT PANEL -->
  <div
    class="relative hidden w-full h-full bg-brand-950 dark:bg-white/5 lg:grid lg:w-1/2 place-items-center"
  >
    @include('admin.partials.common-grid-shape')

<div class="flex flex-col items-center max-w-xs">
    {{-- LOGO --}}
    <div class="flex items-center gap-4 mb-4">
        <img
            src="{{ asset('/storage/images/new-dishub-2.png') }}"
            class="w-[200px] h-[100px] object-contain"
        />
        <img
            src="{{ asset('/storage/images/cirebon.png') }}"
            class="w-[200px] h-[100px] object-contain"
        />
    </div>

    {{-- TEXT --}}
    <p class="text-center text-2xl text-gray-400 dark:text-white/60">
        Sistem Informasi Dinas Perhubungan Angkutan Darat Kota Cirebon
    </p>
</div>

  </div>

  <!-- DARK MODE TOGGLER -->
  <div class="fixed bottom-6 right-6 z-50 hidden sm:block">
    <button
      @click="darkMode = !darkMode"
      class="size-14 rounded-full bg-brand-500 text-white">
      🌓
    </button>
  </div>
</div>
@endsection

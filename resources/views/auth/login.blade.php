@extends('layouts.auth')

@section('title', 'Sign In')

@section('content')
    <div class="flex w-full h-screen dark:bg-gray-900">

        <!-- LEFT: FORM -->
        <div class="flex items-center justify-center w-full lg:w-1/2 px-6">
            <div class="w-full max-w-md">

                <h1 class="mb-2 text-xl font-semibold text-gray-800 dark:text-white">
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
                            class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                    </div>

                    <!-- Password -->
                    <div x-data="{ showPassword: false }">
                        <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                            Password
                        </label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" name="password" required
                                class="h-11 w-full rounded-lg border border-gray-300 px-4 pr-11 text-sm dark:border-gray-700 dark:bg-gray-900" />

                            <span @click="showPassword = !showPassword"
                                class="absolute right-4 top-1/2 -translate-y-1/2 cursor-pointer">
                                👁
                            </span>
                        </div>
                    </div>

                    <!-- Remember -->
                    <div class="flex items-center justify-between">

                        <a href="{{ route('password.request') }}" class="text-sm text-blue-500">
                            Forgot password?
                        </a>
                    </div>

                    <!-- Submit -->
                    <button class="w-full py-3 text-sm font-medium text-white rounded-lg bg-blue-600 hover:bg-blue-700">
                        Sign In
                    </button>
                </form>

            </div>
        </div>

        <!-- RIGHT: FULL IMAGE -->
        <div class="relative hidden lg:block w-1/2 h-full">

            <!-- Background Image -->
            <img src="{{ asset('storage/images/klinik.jpg') }}" alt="Klinik"
                class="absolute inset-0 w-full h-full object-cover">

            <!-- Overlay -->
            <div class="absolute inset-0 bg-black/50"></div>

            <!-- Content -->
            <div class="relative z-10 flex flex-col items-center justify-center h-full text-center px-6">

                <!-- Title -->
                <h2 class="text-3xl lg:text-4xl font-semibold text-white mb-3">
                    SmartKlinik
                </h2>

                <!-- Subtitle -->
                <p class="text-lg text-white/80 max-w-md">
                    Transformasi Digital untuk Klinik yang Lebih Efisien dan Profesional
                </p>

            </div>

        </div>

    </div>

    <!-- DARK MODE TOGGLER -->
    <div class="fixed bottom-6 right-6 z-50 hidden sm:block">
        <button @click="darkMode = !darkMode" class="size-14 rounded-full bg-blue-600 text-white">
            🌓
        </button>
    </div>
@endsection

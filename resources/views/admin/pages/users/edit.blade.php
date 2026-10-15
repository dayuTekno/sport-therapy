@extends('admin.layouts.app')

@section('title', 'Edit User')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Dashboard', 'url' => route('dashboard')],
    ['label' => 'Users', 'url' => route('users.index')],
    ['label' => 'Edit'],
]"/>

<div class="w-full max-w-2xl bg-white p-6 rounded-xl shadow mt-6">

    {{-- Page Title with user type badge --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Edit User</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $user->name }}</p>
        </div>
        @if ($isPlatformUser)
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-700 border border-purple-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                User Platform
            </span>
        @else
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                User Klinik
                @if ($user->clinic)
                    &mdash; {{ $user->clinic->name }}
                @endif
            </span>
        @endif
    </div>

    {{-- Error Alert Banner --}}
    @if (isset($errors) && $errors->any())
        <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700">
            <div class="flex items-center gap-2 font-bold mb-1">
                <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Terdapat kesalahan pengisian formulir:</span>
            </div>
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        {{-- Name --}}
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-700">
                Nama Lengkap
            </label>
            <input
                type="text"
                name="name"
                value="{{ old('name', $user->name) }}"
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 bg-red-50/30 @else border-gray-300 @enderror"
                required
            >
            @error('name')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-700">
                Email
            </label>
            <input
                type="email"
                name="email"
                value="{{ old('email', $user->email) }}"
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 bg-red-50/30 @else border-gray-300 @enderror"
                required
            >
            @error('email')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-700">
                Password
                <span class="text-xs text-gray-400 font-normal">(kosongkan jika tidak ingin mengubah password)</span>
            </label>
            <input
                type="password"
                name="password"
                placeholder="Biarkan kosong jika tidak diubah"
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 bg-red-50/30 @else border-gray-300 @enderror"
            >
            @error('password')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Roles — adaptif berdasarkan tipe user --}}
        <div>
            <label class="block mb-1.5 text-sm font-medium text-gray-700">
                Role / Hak Akses
            </label>

            @if ($isPlatformUser)
                {{-- Info untuk user platform --}}
                <div class="mb-3 p-3 rounded-lg bg-purple-50 border border-purple-100 text-xs text-purple-700 flex gap-2">
                    <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Role berikut berlaku pada level platform — bukan role klinik spesifik.</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ($availableRoles as $role)
                        @php
                            $isChecked = old('roles') !== null
                                ? in_array($role->name, old('roles', []))
                                : in_array($role->name, $userRoles);

                            $roleTitle = match($role->name) {
                                'saas_admin' => 'Super Admin Platform',
                                'operator'   => 'Operator Platform',
                                default      => $role->name,
                            };
                            $roleDesc = match($role->name) {
                                'saas_admin' => 'Akses penuh ke seluruh sistem SaaS, manajemen klinik, CMS, dan subscription',
                                'operator'   => 'Akses operasional terbatas pada fitur-fitur tertentu platform',
                                default      => '',
                            };
                            $borderColor = $isChecked ? 'border-purple-400 bg-purple-50' : 'border-gray-200';
                        @endphp
                        <label class="flex items-start gap-3 p-3.5 border rounded-xl cursor-pointer hover:bg-purple-50/60 transition {{ $borderColor }}">
                            <input
                                type="checkbox"
                                name="roles[]"
                                value="{{ $role->name }}"
                                class="rounded border-gray-300 text-purple-600 focus:ring-purple-500 mt-0.5"
                                {{ $isChecked ? 'checked' : '' }}
                            >
                            <div>
                                <span class="font-bold text-xs text-gray-900 block">{{ $roleTitle }}</span>
                                <span class="text-[11px] text-gray-500">{{ $roleDesc }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>

            @else
                {{-- Info untuk user klinik --}}
                <div class="mb-3 p-3 rounded-lg bg-blue-50 border border-blue-100 text-xs text-blue-700 flex gap-2">
                    <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>
                        Role berikut berlaku di klinik
                        <strong>{{ $user->clinic?->name ?? '—' }}</strong>.
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ($availableRoles as $role)
                        @php
                            $isChecked = old('roles') !== null
                                ? in_array($role->name, old('roles', []))
                                : in_array($role->name, $userRoles);

                            $roleTitle = match($role->name) {
                                'admin'    => 'Administrator Klinik',
                                'operator' => 'Operator Layanan Klinik',
                                default    => $role->name,
                            };
                            $roleDesc = match($role->name) {
                                'admin'    => 'Akses penuh seluruh layanan & staf klinik',
                                'operator' => 'Akses operasional terapi, reservasi, kasir, dan master pasien',
                                default    => '',
                            };
                            $borderColor = $isChecked ? 'border-blue-400 bg-blue-50' : 'border-gray-200';
                        @endphp
                        <label class="flex items-start gap-3 p-3.5 border rounded-xl cursor-pointer hover:bg-blue-50/60 transition {{ $borderColor }}">
                            <input
                                type="checkbox"
                                name="roles[]"
                                value="{{ $role->name }}"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mt-0.5"
                                {{ $isChecked ? 'checked' : '' }}
                            >
                            <div>
                                <span class="font-bold text-xs text-gray-900 block">{{ $roleTitle }}</span>
                                <span class="text-[11px] text-gray-500">{{ $roleDesc }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            @endif

            @error('roles')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Action --}}
        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('users.index') }}"
               class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition font-medium text-sm">
                Batal
            </a>
            <button
                type="submit"
                class="px-6 py-2 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 shadow-sm transition text-sm">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

@endsection

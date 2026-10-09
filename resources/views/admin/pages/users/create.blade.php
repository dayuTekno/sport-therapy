@extends('admin.layouts.app')

@section('title', 'Create User')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Dashboard', 'url' => route('dashboard')],
    ['label' => 'Users', 'url' => route('users.index')],
    ['label' => 'Create'],
]"/>

<div class="w-full bg-white p-6 rounded-lg shadow mt-6">
    <h2 class="text-xl font-semibold mb-6">Tambah User Staf Klinik</h2>

    {{-- Error Alert Banner --}}
    @if (isset($errors) && $errors->any())
        <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700">
            <div class="flex items-center gap-2 font-bold mb-1">
                <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Terdapat kesalahan pengisian formulir:</span>
            </div>
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
        @csrf

        {{-- Name --}}
        <div>
            <label class="block text-sm font-medium mb-1">
                Name
            </label>
            <input
                type="text"
                name="name"
                value="{{ old('name') }}"
                class="w-full border rounded-lg px-3 py-2
                       focus:outline-none focus:ring-2 focus:ring-blue-500
                       @error('name') border-red-500 @enderror"
            >
            @error('name')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label class="block text-sm font-medium mb-1">
                Email
            </label>
            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                class="w-full border rounded-lg px-3 py-2
                       focus:outline-none focus:ring-2 focus:ring-blue-500
                       @error('email') border-red-500 @enderror"
            >
            @error('email')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label class="block text-sm font-medium mb-1">
                Password
            </label>
            <input
                type="password"
                name="password"
                class="w-full border rounded-lg px-3 py-2
                       focus:outline-none focus:ring-2 focus:ring-blue-500
                       @error('password') border-red-500 @enderror"
            >
            @error('password')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Roles --}}
        <div>
            <label class="block text-sm font-medium mb-2">
                Roles
            </label>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach ($roles as $role)
                    <label class="flex items-start gap-3 p-3.5 border rounded-xl cursor-pointer hover:bg-slate-50 transition border-gray-200">
                        <input
                            type="checkbox"
                            name="roles[]"
                            value="{{ $role->name }}"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mt-0.5"
                            {{ in_array($role->name, old('roles', [$role->name === 'operator' ? 'operator' : ''])) ? 'checked' : '' }}
                        >
                        <div>
                            <span class="font-bold text-xs text-gray-900 block">
                                {{ $role->name === 'admin' ? 'Administrator Klinik' : 'Operator Layanan Klinik' }}
                            </span>
                            <span class="text-[11px] text-gray-500">
                                {{ $role->name === 'admin' ? 'Akses penuh seluruh layanan & staf klinik' : 'Akses operasional terapi, reservasi, kasir, dan master pasien' }}
                            </span>
                        </div>
                    </label>
                @endforeach
            </div>

            @error('roles')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit --}}
        <button
            type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700
                   text-white py-2 rounded-lg transition"
        >
            Save
        </button>
    </form>
</div>

@endsection

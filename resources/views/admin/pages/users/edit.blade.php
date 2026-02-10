@extends('admin.layouts.app')

@section('title', 'Edit User')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Dashboard', 'url' => route('dashboard')],
    ['label' => 'Users', 'url' => route('users.index')],
    ['label' => 'Edit'],
]"/>

<div class="w-full bg-white p-6 rounded-lg shadow mt-6">
    <h2 class="text-xl font-semibold mb-6">Edit User</h2>

    <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        {{-- Name --}}
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-700">
                Name
            </label>
            <input
                type="text"
                name="name"
                value="{{ old('name', $user->name) }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg
                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                required
            >
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
                class="w-full px-3 py-2 border border-gray-300 rounded-lg
                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                required
            >
        </div>

        {{-- Password --}}
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-700">
                Password <span class="text-xs text-gray-500">(kosongkan jika tidak diubah)</span>
            </label>
            <input
                type="password"
                name="password"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg
                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
        </div>

        {{-- Roles --}}
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">
                Roles
            </label>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                @foreach ($roles as $role)
                    <label class="flex items-center gap-2 text-sm">
                        <input
                            type="checkbox"
                            name="roles[]"
                            value="{{ $role->name }}"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            {{ in_array($role->name, $userRoles) ? 'checked' : '' }}
                        >
                        {{ $role->name }}
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Action --}}
        <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('users.index') }}"
               class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100">
                Batal
            </a>

            <button
                type="submit"
                class="px-6 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                Update
            </button>
        </div>
    </form>
</div>

@endsection

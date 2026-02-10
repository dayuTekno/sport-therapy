@extends('admin.layouts.app')

@section('title', 'Create User')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Dashboard', 'url' => route('dashboard')],
    ['label' => 'Users', 'url' => route('users.index')],
    ['label' => 'Create'],
]"/>

<div class="w-full bg-white p-6 rounded-lg shadow mt-6">
    <h2 class="text-xl font-semibold mb-6">Create User</h2>

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

            <div class="grid grid-cols-2 gap-2">
                @foreach ($roles as $role)
                    <label class="flex items-center gap-2 text-sm">
                        <input
                            type="checkbox"
                            name="roles[]"
                            value="{{ $role->name }}"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            {{ in_array($role->name, old('roles', [])) ? 'checked' : '' }}
                        >
                        {{ $role->name }}
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

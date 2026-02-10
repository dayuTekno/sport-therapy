@extends('admin.layouts.app')

@section('title', 'Edit User')

@section('content')

<x-breadcrumb :items="[
['label' => 'Dashboard', 'url' => route('dashboard')],
    ['label' => 'Users', 'url' => route('users.index')],
    ['label' => 'Edit'],
]"/>

<div class="max-w-lg mx-auto bg-white p-6 rounded shadow mt-6">
    <h2 class="text-xl font-semibold mb-4">Edit User</h2>

    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <x-input label="Name" name="name" :value="$user->name"/>
        <x-input label="Email" name="email" type="email" :value="$user->email"/>
        <x-input label="Password (optional)" name="password" type="password"/>

        <div class="mb-4">
            <label class="block mb-2 font-medium">Roles</label>
            @foreach ($roles as $role)
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                        {{ in_array($role->name, $userRoles) ? 'checked' : '' }}>
                    {{ $role->name }}
                </label>
            @endforeach
        </div>

        <button class="w-full bg-blue-600 text-white py-2 rounded">
            Update
        </button>
    </form>
</div>

@endsection

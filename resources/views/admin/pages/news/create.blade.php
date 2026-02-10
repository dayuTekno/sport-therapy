@extends('admin.layouts.app')

@section('title', 'Create User')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Dashboard', 'url' => route('dashboard')],
    ['label' => 'Users', 'url' => route('users.index')],
    ['label' => 'Create'],
]"/>

<div class="max-w-lg mx-auto bg-white p-6 rounded shadow mt-6">
    <h2 class="text-xl font-semibold mb-4">Create User</h2>

    <form action="{{ route('users.store') }}" method="POST">
        @csrf

        <x-input label="Name" name="name"/>
        <x-input label="Email" name="email" type="email"/>
        <x-input label="Password" name="password" type="password"/>

        <div class="mb-4">
            <label class="block mb-2 font-medium">Roles</label>
            @foreach ($roles as $role)
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="roles[]" value="{{ $role->name }}">
                    {{ $role->name }}
                </label>
            @endforeach
        </div>

        <button class="w-full bg-blue-600 text-white py-2 rounded">
            Save
        </button>
    </form>
</div>

@endsection

@extends('admin.layouts.app')

@section('title', 'Create Role')

@section('content')

    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Roles', 'url' => route('roles.index')],
        ['label' => 'Create Role'],
    ]"/>
<div class="mt-6 w-full bg-white p-6 rounded shadow">
    <h2 class="text-xl font-semibold mb-4">Create New Role</h2>

    {{-- Error Alert --}}
    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('roles.store') }}" method="POST">
        @csrf

        {{-- Role Name --}}
        <div class="mb-4">
            <label for="name" class="block text-gray-700 font-medium mb-1">Role Name</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        {{-- Permissions --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-2">Assign Permissions</label>

            <div class="mb-2 flex gap-2">
                <button type="button" id="selectAll" 
                        class="flex-1 px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                    Select All
                </button>
                <button type="button" id="deselectAll" 
                        class="flex-1 px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                    Deselect All
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                @foreach ($permissions as $permission)
                    <label class="inline-flex items-center w-full">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                               class="permission-checkbox form-checkbox h-5 w-5 text-blue-600"
                               {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                        <span class="ml-2 text-gray-700">{{ $permission->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Buttons --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('roles.index') }}"
               class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
                Cancel
            </a>
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Save Role
            </button>
        </div>
    </form>
</div>

{{-- JavaScript --}}
<script>
    document.getElementById('selectAll').addEventListener('click', function() {
        document.querySelectorAll('.permission-checkbox').forEach(function(cb) {
            cb.checked = true;
        });
    });

    document.getElementById('deselectAll').addEventListener('click', function() {
        document.querySelectorAll('.permission-checkbox').forEach(function(cb) {
            cb.checked = false;
        });
    });
</script>

@endsection

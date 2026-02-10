@extends('admin.layouts.app')

@section('title', 'User Management')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Dashboard', 'url' => route('dashboard')],
    ['label' => 'Settings'],
    ['label' => 'Users'],
]" />

<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold text-gray-800">User Management</h1>

    <a href="{{ route('users.create') }}"
       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
        + Tambah User
    </a>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Nama
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Email
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Role
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                Aksi
            </th>
        </tr>
        </thead>

        <tbody class="divide-y divide-gray-200 bg-white">
        @forelse ($users as $user)
            <tr>
                <td class="px-6 py-4 font-medium text-gray-800">
                    {{ $user->name }}
                </td>

                <td class="px-6 py-4 text-sm text-gray-600">
                    {{ $user->email }}
                </td>

                <td class="px-6 py-4 text-sm text-gray-600">
                    @forelse ($user->roles as $role)
                        <span class="inline-flex items-center
                                     px-2 py-1 mr-1
                                     rounded-md text-xs
                                     bg-blue-50 text-blue-700">
                            {{ $role->name }}
                        </span>
                    @empty
                        -
                    @endforelse
                </td>

                <td class="px-6 py-4 text-right space-x-2">

                    {{-- EDIT --}}
                    <a href="{{ route('users.edit', $user) }}"
                       class="inline-flex items-center justify-center
                              w-8 h-8 rounded-lg
                              text-blue-600 hover:bg-blue-50"
                       title="Edit">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke-width="1.5"
                             stroke="currentColor"
                             class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M16.862 4.487a2.25 2.25 0 113.182 3.182L7.5 20.25H3v-4.5L16.862 4.487z" />
                        </svg>
                    </a>

                    {{-- DELETE --}}
                    <form action="{{ route('users.destroy', $user) }}"
                          method="POST"
                          class="inline"
                          onsubmit="return confirm('Yakin hapus user ini?')">
                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="inline-flex items-center justify-center
                                       w-8 h-8 rounded-lg
                                       text-red-600 hover:bg-red-50"
                                title="Hapus">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke-width="1.5"
                                 stroke="currentColor"
                                 class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21
                                         c.342.052.682.107 1.022.166M4.772 5.79
                                         c.34-.059.68-.114 1.022-.166m12.456 0
                                         a48.108 48.108 0 00-3.478-.397m-8.004 0
                                         a48.11 48.11 0 013.478-.397m7.5 0v-.916
                                         c0-1.18-.91-2.164-2.09-2.201a51.964
                                         51.964 0 00-3.32 0c-1.18.037-2.09
                                         1.022-2.09 2.201v.916m7.5 0" />
                            </svg>
                        </button>
                    </form>

                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4"
                    class="px-6 py-6 text-center text-gray-500">
                    Data user belum tersedia
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>


{{-- Pagination --}}
@if ($users->hasPages())
    <div class="mt-6">
        {{ $users->links() }}
    </div>
@endif

@endsection

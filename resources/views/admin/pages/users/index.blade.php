@extends('admin.layouts.app')

@section('title', 'User Management')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Dashboard', 'url' => route('dashboard')],
    ['label' => 'Settings'],
    ['label' => 'Users'],
]" />

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-semibold text-gray-800">User Management</h1>
        <p class="text-sm text-gray-500 mt-0.5">Kelola seluruh pengguna platform dan klinik</p>
    </div>
    @can('user.create')
    <a href="{{ route('users.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium shadow-sm transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah User
    </a>
    @endcan
</div>

{{-- Alert --}}
@if (session('success'))
    <div class="mb-5 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 flex items-center gap-2 text-sm">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-center gap-2 text-sm">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        {{ session('error') }}
    </div>
@endif

{{-- ================================================================
     SECTION 1: USER PLATFORM (hanya tampil untuk SaaS Admin)
     ================================================================ --}}
@if ($isSaasAdmin && $platformUsers->isNotEmpty())
<div class="mb-8">
    {{-- Section Header --}}
    <div class="flex items-center gap-3 mb-3">
        <div class="flex items-center gap-2 px-3 py-1.5 bg-purple-100 border border-purple-200 rounded-full">
            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <span class="text-xs font-bold text-purple-700 uppercase tracking-wide">User Platform</span>
        </div>
        <span class="text-xs text-gray-400">{{ $platformUsers->count() }} pengguna</span>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-purple-100 overflow-hidden">
        <div class="bg-purple-50 px-4 py-2.5 border-b border-purple-100">
            <p class="text-xs text-purple-600 font-medium">
                ⚙️ Pengguna tingkat platform — tidak terikat pada klinik manapun
            </p>
        </div>
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Nama</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Email</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Role</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 bg-white">
                @foreach ($platformUsers as $user)
                    <tr class="hover:bg-purple-50/30 transition">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center shrink-0">
                                    <span class="text-xs font-bold text-purple-600">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                                <span class="font-medium text-gray-800 text-sm">{{ $user->name }}</span>
                                @if ($user->id === auth()->id())
                                    <span class="text-[10px] bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded-full font-semibold">Anda</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-sm text-gray-600">{{ $user->email }}</td>
                        <td class="px-5 py-3.5">
                            @forelse ($user->roles as $role)
                                @php
                                    $roleColor = match($role->name) {
                                        'saas_admin' => 'bg-purple-100 text-purple-700 border border-purple-200',
                                        'admin'      => 'bg-indigo-100 text-indigo-700 border border-indigo-200',
                                        default      => 'bg-gray-100 text-gray-600 border border-gray-200',
                                    };
                                    $roleLabel = match($role->name) {
                                        'saas_admin' => 'Super Admin',
                                        'admin'      => 'Administrator',
                                        'operator'   => 'Operator',
                                        default      => $role->name,
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 mr-1 rounded-md text-[11px] font-bold {{ $roleColor }}">
                                    {{ $roleLabel }}
                                </span>
                            @empty
                                <span class="text-xs text-gray-400 italic">Tidak ada role</span>
                            @endforelse
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex justify-end gap-1.5">
                                @can('user.edit')
                                <a href="{{ route('users.edit', $user) }}"
                                   class="p-1.5 text-yellow-600 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition"
                                   title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </a>
                                @endcan
                                @can('user.delete')
                                @if ($user->id !== auth()->id() && !$user->isSaasAdmin())
                                <form action="{{ route('users.destroy', $user) }}" method="POST"
                                      class="inline-block" onsubmit="return confirm('Yakin hapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="p-1.5 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition"
                                            title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- ================================================================
     SECTION 2: USER KLINIK (dikelompokkan per klinik)
     ================================================================ --}}
@if ($clinicUsers->isNotEmpty())
<div class="mb-4">
    <div class="flex items-center gap-3 mb-3">
        <div class="flex items-center gap-2 px-3 py-1.5 bg-blue-100 border border-blue-200 rounded-full">
            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span class="text-xs font-bold text-blue-700 uppercase tracking-wide">User Klinik</span>
        </div>
        <span class="text-xs text-gray-400">
            {{ $clinicUsers->flatten()->count() }} pengguna
            @if ($isSaasAdmin) · {{ $clinicUsers->count() }} klinik @endif
        </span>
    </div>

    {{-- Loop per klinik --}}
    <div class="space-y-5">
        @foreach ($clinicUsers as $clinicName => $users)
        <div class="bg-white rounded-xl shadow-sm border border-blue-100 overflow-hidden">
            {{-- Clinic Name Header --}}
            @if ($isSaasAdmin)
            <div class="bg-blue-50 px-5 py-2.5 border-b border-blue-100 flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4zm3 1h6v4H7V5zm2 5h2v2H9v-2zm-2 0h2v2H7v-2zm4 0h2v2h-2v-2z" clip-rule="evenodd"/>
                </svg>
                <span class="text-xs font-semibold text-blue-700">{{ $clinicName }}</span>
                <span class="ml-auto text-[11px] text-blue-400">{{ $users->count() }} user</span>
            </div>
            @endif

            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Nama</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Email</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Role</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 bg-white">
                    @forelse ($users as $user)
                        <tr class="hover:bg-blue-50/20 transition">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                                        <span class="text-xs font-bold text-blue-600">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                    </div>
                                    <span class="font-medium text-gray-800 text-sm">{{ $user->name }}</span>
                                    @if ($user->id === auth()->id())
                                        <span class="text-[10px] bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded-full font-semibold">Anda</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-sm text-gray-600">{{ $user->email }}</td>
                            <td class="px-5 py-3.5">
                                @forelse ($user->roles as $role)
                                    @php
                                        $roleColor = match($role->name) {
                                            'admin'    => 'bg-indigo-100 text-indigo-700 border border-indigo-200',
                                            'operator' => 'bg-teal-100 text-teal-700 border border-teal-200',
                                            default    => 'bg-gray-100 text-gray-600 border border-gray-200',
                                        };
                                        $roleLabel = match($role->name) {
                                            'admin'    => 'Admin Klinik',
                                            'operator' => 'Operator',
                                            default    => $role->name,
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 mr-1 rounded-md text-[11px] font-bold {{ $roleColor }}">
                                        {{ $roleLabel }}
                                    </span>
                                @empty
                                    <span class="text-xs text-gray-400 italic">Tidak ada role</span>
                                @endforelse
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex justify-end gap-1.5">
                                    @can('user.edit')
                                    <a href="{{ route('users.edit', $user) }}"
                                       class="p-1.5 text-yellow-600 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition"
                                       title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </a>
                                    @endcan
                                    @can('user.delete')
                                    @if ($user->id !== auth()->id())
                                    <form action="{{ route('users.destroy', $user) }}" method="POST"
                                          class="inline-block" onsubmit="return confirm('Yakin hapus user ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-1.5 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition"
                                                title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                    @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-6 text-center text-gray-400 text-sm">
                                Belum ada user di klinik ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Empty state --}}
@if ($platformUsers->isEmpty() && $clinicUsers->isEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 py-16 text-center">
        <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <p class="text-gray-400 text-sm">Belum ada data pengguna</p>
    </div>
@endif

@endsection

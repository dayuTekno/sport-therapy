@extends('admin.layouts.app')

@section('title', 'Edit Profil & Foto Admin')

@section('content')

@php
    $label = 'mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-200';
    $input = 'block w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2.5 text-xs text-gray-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-100';
    $fieldErr = ' border-red-400 focus:border-red-500 focus:ring-red-500/15';
    $currentAvatar = $user->avatar_url;
@endphp

<x-breadcrumb :items="[
    ['label' => 'Pengaturan', 'url' => '#'],
    ['label' => 'Edit Profil & Foto'],
]" />

{{-- HEADER --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <span>👤</span> Profil & Foto Pengguna
        </h1>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            Kelola foto profil akun Anda, perbarui identitas, serta ubah kata sandi login.
        </p>
    </div>
</div>

@include('admin.partials.alert')

@if ($errors->any())
    <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-xs text-red-700">
        <div class="font-bold mb-1">Gagal menyimpan perubahan profil:</div>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data"
      x-data="{
          photoPreview: null,
          removePhoto: false,
          updatePreview(event) {
              const file = event.target.files[0];
              if (file) {
                  this.removePhoto = false;
                  const reader = new FileReader();
                  reader.onload = (e) => {
                      this.photoPreview = e.target.result;
                  };
                  reader.readAsDataURL(file);
              }
          },
          markRemovePhoto() {
              this.photoPreview = null;
              this.removePhoto = true;
              $refs.photoInput.value = '';
          }
      }">
    @csrf
    @method('PUT')

    <input type="hidden" name="remove_avatar" :value="removePhoto ? 1 : 0">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- KOLOM KIRI: FOTO PROFIL (1 KOLOM) --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 text-center">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-4 pb-2 border-b border-gray-100 dark:border-gray-700">
                    Foto Profil Anda
                </h3>

                {{-- AVATAR PREVIEW --}}
                <div class="relative mx-auto w-32 h-32 mb-4">
                    {{-- Default / Current Avatar --}}
                    <template x-if="!photoPreview && !removePhoto">
                        <img src="{{ $currentAvatar }}" 
                             alt="{{ $user->name }}" 
                             class="w-32 h-32 rounded-full object-cover border-4 border-white dark:border-gray-800 shadow-md ring-2 ring-blue-500/30">
                    </template>

                    {{-- Live Preview of newly selected photo --}}
                    <template x-if="photoPreview">
                        <img :src="photoPreview" 
                             alt="Preview Foto" 
                             class="w-32 h-32 rounded-full object-cover border-4 border-white dark:border-gray-800 shadow-md ring-2 ring-emerald-500/50">
                    </template>

                    {{-- Placeholder when user clicked remove photo --}}
                    <template x-if="removePhoto && !photoPreview">
                        <img src="{{ asset('admin/images/user/user-37.jpg') }}" 
                             alt="Default Avatar" 
                             class="w-32 h-32 rounded-full object-cover border-4 border-white dark:border-gray-800 shadow-md ring-2 ring-gray-300 opacity-70">
                    </template>

                    {{-- Badge Status --}}
                    <div class="absolute bottom-1 right-1">
                        <template x-if="photoPreview">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500 text-white text-[11px] shadow" title="Foto Baru Dipilih">
                                ✓
                            </span>
                        </template>
                    </div>
                </div>

                {{-- NAMA & ROLE --}}
                <h2 class="text-sm font-bold text-gray-900 dark:text-white">{{ $user->name }}</h2>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate mt-0.5">{{ $user->email }}</p>
                <div class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300">
                    <span>🛡️</span> {{ $user->role_display }}
                </div>

                {{-- TOMBOL UPLOAD & HAPUS FOTO --}}
                <div class="mt-6 flex flex-col gap-2">
                    <input type="file" name="avatar" x-ref="photoInput" @change="updatePreview"
                           accept="image/png,image/jpeg,image/jpg,image/webp" class="hidden">

                    <button type="button" @click="$refs.photoInput.click()"
                            class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold text-xs rounded-xl shadow-sm transition flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Pilih Foto Profil Baru</span>
                    </button>

                    @if($user->getRawOriginal('avatar'))
                        <button type="button" @click="markRemovePhoto()" x-show="!removePhoto"
                                class="w-full py-2 px-3 text-rose-600 hover:text-rose-800 hover:bg-rose-50 dark:hover:bg-rose-900/20 text-xs font-semibold rounded-xl transition border border-rose-200 dark:border-rose-800/40 cursor-pointer">
                            🗑️ Hapus Foto Profil
                        </button>
                    @endif

                    <template x-if="photoPreview || removePhoto">
                        <button type="button" 
                                @click="photoPreview = null; removePhoto = false; $refs.photoInput.value = ''"
                                class="w-full py-1.5 px-3 text-gray-500 hover:text-gray-700 text-[11px] font-medium transition">
                            Reset Pilihan Foto
                        </button>
                    </template>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 text-left">
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 leading-relaxed">
                        💡 <strong>Ketentuan Foto:</strong><br>
                        Format PNG, JPG, atau WEBP dengan ukuran maks. 2MB. Disarankan foto berasio 1:1 (persegi) agar simetris.
                    </p>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: IDENTITAS & PASSWORD (2 KOLOM) --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- 1. IDENTITAS AKUN --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-4 pb-2 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                    <span>🪪</span> Informasi Akun
                </h3>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Nama Lengkap --}}
                        <div>
                            <label class="{{ $label }}">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                                   class="{{ $input }} @error('name'){{ $fieldErr }}@enderror" required>
                            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="{{ $label }}">Alamat Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                                   class="{{ $input }} @error('email'){{ $fieldErr }}@enderror" required>
                            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        {{-- Hak Akses / Role --}}
                        <div>
                            <label class="{{ $label }}">Tingkat Akses / Role</label>
                            <div class="px-3.5 py-2.5 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-700 text-xs font-semibold text-gray-700 dark:text-gray-300 flex items-center justify-between">
                                <span>{{ $user->role_display }}</span>
                                <span class="text-[10px] text-gray-400">Sistem Tetap</span>
                            </div>
                        </div>

                        {{-- Afiliasi Klinik --}}
                        <div>
                            <label class="{{ $label }}">Klinik / Unit Kerja</label>
                            <div class="px-3.5 py-2.5 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-700 text-xs font-semibold text-gray-700 dark:text-gray-300 flex items-center justify-between">
                                <span>{{ $user->clinic_name }}</span>
                                <span class="text-[10px] text-gray-400">{{ $user->clinic ? 'Klinik Mitra' : 'Global' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. GANTI PASSWORD (OPSIONAL) --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-2 pb-2 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                    <span>🔐</span> Ubah Kata Sandi (Opsional)
                </h3>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 mb-4">
                    Biarkan kolom password kosong jika Anda tidak ingin mengubah kata sandi akun Anda.
                </p>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Password Baru --}}
                        <div>
                            <label class="{{ $label }}">Kata Sandi Baru</label>
                            <input type="password" name="password" autocomplete="new-password"
                                   placeholder="Minimal 6 karakter"
                                   class="{{ $input }} @error('password'){{ $fieldErr }}@enderror">
                            @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Konfirmasi Password Baru --}}
                        <div>
                            <label class="{{ $label }}">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" name="password_confirmation" autocomplete="new-password"
                                   placeholder="Ulangi kata sandi baru"
                                   class="{{ $input }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- TOMBOL SIMPAN --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="submit" 
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-xs rounded-xl shadow-md shadow-blue-500/20 transition flex items-center gap-2 cursor-pointer">
                    <span>💾</span>
                    <span>Simpan Perubahan Profil</span>
                </button>
            </div>

        </div>

    </div>
</form>

@endsection

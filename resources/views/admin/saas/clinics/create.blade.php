@extends('admin.layouts.app')

@section('title', 'Tambah Mitra Klinik Baru')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'SaaS Administrator', 'url' => route('saas.dashboard')],
    ['label' => 'Klinik Mitra', 'url' => route('saas.clinics.index')],
    ['label' => 'Daftarkan Klinik Baru'],
]" />

<div class="max-w-2xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <span>🏥</span> Tambah Mitra Klinik Baru
            </h1>
            <p class="text-xs text-gray-500 mt-1">Daftarkan tenant klinik baru ke dalam platform SaaS.</p>
        </div>

        <a href="{{ route('saas.clinics.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition">
            ← Kembali
        </a>
    </div>

    @include('admin.partials.alert')

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
        <form action="{{ route('saas.clinics.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf

            <div>
                <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                    Nama Klinik / Sport Center <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       placeholder="Contoh: Surabaya Sport Physiotherapy"
                       class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Email Resmi Klinik <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           placeholder="admin@surabayafisio.com"
                           class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Nomor Telepon / WhatsApp <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="phone_number" value="{{ old('phone_number') }}" required
                           placeholder="08123456789"
                           class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Paket Langganan SaaS <span class="text-rose-500">*</span>
                    </label>
                    <select name="subscription_plan_id" required class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 font-semibold text-gray-800">
                        @foreach($plans as $p)
                            <option value="{{ $p->id }}" {{ ($p->slug === 'paket-1-tahun' || $loop->first) ? 'selected' : '' }}>
                                {{ $p->name }} {{ $p->subtitle ? '— ' . $p->subtitle : '' }} ({{ $p->formatted_monthly_rate }} | {{ $p->formatted_commitment }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Status Awal Tenant <span class="text-rose-500">*</span>
                    </label>
                    <select name="status" required class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 font-semibold text-gray-800">
                        <option value="active" selected>Aktif Langsung</option>
                        <option value="trial">Trial (14 Hari)</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                    Alamat Lengkap
                </label>
                <textarea name="address" rows="2" placeholder="Alamat fisik klinik..."
                          class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">{{ old('address') }}</textarea>
            </div>

            <div class="border-t border-gray-100 pt-4">
                <h4 class="font-bold text-gray-800 uppercase tracking-wider mb-3">Akun Administrator Klinik Baru</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Nama Admin Klinik <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="admin_name" value="{{ old('admin_name') }}" required
                               placeholder="Nama Penanggung Jawab"
                               class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Password Login <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" name="admin_password" required
                               placeholder="Minimal 8 karakter"
                               class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3">
                <a href="{{ route('saas.clinics.index') }}" class="px-4 py-2 bg-white hover:bg-gray-100 text-gray-700 text-xs font-semibold rounded-xl border border-gray-200 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md transition">
                    Daftarkan Tenant Klinik
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

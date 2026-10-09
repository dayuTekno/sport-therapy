@extends('admin.layouts.app')

@section('title', 'Edit Data Klinik Mitra - ' . $clinic->name)

@section('content')

<x-breadcrumb :items="[
    ['label' => 'SaaS Administrator', 'url' => route('saas.dashboard')],
    ['label' => 'Klinik Mitra', 'url' => route('saas.clinics.index')],
    ['label' => 'Edit ' . $clinic->name],
]" />

<div class="max-w-2xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <span>🏥</span> Edit Data Klinik Mitra
            </h1>
            <p class="text-xs text-gray-500 mt-1">Kelola status keaktifan, kuota terapis, dan paket langganan klinik.</p>
        </div>

        <a href="{{ route('saas.clinics.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition">
            ← Kembali
        </a>
    </div>

    @include('admin.partials.alert')

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
        <form action="{{ route('saas.clinics.update', $clinic->id) }}" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PUT')

            <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-gray-400 font-bold uppercase block">Kode Tenant:</span>
                    <span class="font-mono font-bold text-gray-900 text-xs">{{ $clinic->clinic_code }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-gray-400 font-bold uppercase block">Slug:</span>
                    <span class="font-mono text-gray-700 text-xs">{{ $clinic->slug }}</span>
                </div>
            </div>

            <div>
                <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                    Nama Klinik / Sport Center <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $clinic->name) }}" required
                       class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Email Resmi Klinik <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email', $clinic->email) }}" required
                           class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Nomor Telepon / WhatsApp <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="phone_number" value="{{ old('phone_number', $clinic->phone_number) }}" required
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
                            <option value="{{ $p->id }}" {{ $clinic->subscription_plan_id == $p->id ? 'selected' : '' }}>
                                {{ $p->name }} {{ $p->subtitle ? '— ' . $p->subtitle : '' }} ({{ $p->formatted_monthly_rate }} | {{ $p->formatted_commitment }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Status Tenant <span class="text-rose-500">*</span>
                    </label>
                    <select name="status" required class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 font-semibold text-gray-800">
                        <option value="active" {{ $clinic->status === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="trial" {{ $clinic->status === 'trial' ? 'selected' : '' }}>Trial</option>
                        <option value="suspended" {{ $clinic->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        <option value="expired" {{ $clinic->status === 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Batas Kuota Terapis <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="max_therapists" value="{{ old('max_therapists', $clinic->max_therapists) }}" required min="1"
                           class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 font-bold">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Batas Kuota Pasien Bulanan <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="max_patients" value="{{ old('max_patients', $clinic->max_patients) }}" required min="1"
                           class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 font-bold">
                </div>
            </div>

            <div>
                <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                    Alamat Lengkap
                </label>
                <textarea name="address" rows="2" class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">{{ old('address', $clinic->address) }}</textarea>
            </div>

            <div>
                <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                    Catatan Internal Super Admin
                </label>
                <textarea name="notes" rows="2" placeholder="Catatan perjanjian khusus, kuota tambahan, dll..."
                          class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">{{ old('notes', $clinic->notes) }}</textarea>
            </div>

            <div class="pt-4 flex items-center justify-between border-t border-gray-100">
                <a href="{{ route('saas.clinics.impersonate', $clinic->id) }}" class="px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-xl transition flex items-center gap-1.5">
                    <span>👁️</span> Tinjau & Kelola Klinik Ini
                </a>

                <div class="flex items-center gap-2">
                    <a href="{{ route('saas.clinics.index') }}" class="px-4 py-2 bg-white hover:bg-gray-100 text-gray-700 text-xs font-semibold rounded-xl border border-gray-200 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md transition">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

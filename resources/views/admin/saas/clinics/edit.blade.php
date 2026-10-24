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
                <div>
                    <span class="text-[10px] text-gray-400 font-bold uppercase block">Kode Referal:</span>
                    <span class="font-mono font-bold text-blue-700 text-xs">{{ $clinic->referral_code ?? '-' }}</span>
                </div>
            </div>

            @php $referralCommission = $clinic->referralCommission; @endphp
            <div>
                <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                    Kode Referal Klinik Perujuk (Opsional)
                </label>
                <input type="text" name="referral_code" maxlength="20" autocomplete="off"
                       value="{{ old('referral_code', $clinic->referrer?->referral_code) }}"
                       oninput="this.value = this.value.toUpperCase()"
                       placeholder="Kosongkan jika tidak melalui referal"
                       @disabled($referralCommission)
                       class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 font-mono font-bold tracking-wider disabled:bg-gray-100 disabled:text-gray-500">
                @if($referralCommission)
                    <p class="text-[10px] text-gray-500 mt-1">
                        Diundang oleh <strong>{{ $clinic->referrer?->name ?? 'klinik terhapus' }}</strong>. Komisi
                        Rp {{ number_format($referralCommission->commission_amount, 0, ',', '.') }} sudah tercatat ({{ $referralCommission->status_label }}), sehingga perujuk tidak dapat diubah.
                    </p>
                @elseif($clinic->referrer)
                    <p class="text-[10px] text-gray-500 mt-1">Diundang oleh <strong>{{ $clinic->referrer->name }}</strong>. Komisi dicatat otomatis saat status klinik diubah menjadi Aktif.</p>
                @else
                    <p class="text-[10px] text-gray-400 mt-1">Komisi untuk klinik perujuk dicatat otomatis saat status klinik diubah menjadi Aktif.</p>
                @endif
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

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
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
                <div>
                    <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                        <span>End Date Subscribe</span>
                        @if($clinic->subscription_ends_at)
                            @if($clinic->subscription_ends_at->isPast())
                                <span class="text-rose-600 font-bold text-[10px] bg-rose-50 px-1.5 py-0.5 rounded">Habis</span>
                            @else
                                <span class="text-emerald-600 font-bold text-[10px] bg-emerald-50 px-1.5 py-0.5 rounded">{{ now()->diffInDays($clinic->subscription_ends_at) }} hari lagi</span>
                            @endif
                        @endif
                    </label>
                    <input type="date" name="subscription_ends_at" 
                           value="{{ old('subscription_ends_at', $clinic->subscription_ends_at?->format('Y-m-d')) }}"
                           class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 font-medium">
                    <p class="text-[10px] text-gray-400 mt-1">Batas tanggal aktif langganan operasional klinik</p>
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

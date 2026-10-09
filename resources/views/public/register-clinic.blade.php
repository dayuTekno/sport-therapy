<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftarkan Klinik Mitra Baru - SportClinic.io</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen py-10 px-4 flex items-center justify-center">

    <div class="max-w-xl w-full bg-white rounded-3xl shadow-xl border border-gray-100 p-8 sm:p-10">
        {{-- Header Form --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-blue-600 text-white font-black text-2xl shadow-md shadow-blue-500/25 mb-3">
                ⚡
            </div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Daftarkan Klinik Terapi Anda</h1>
            <p class="text-xs text-gray-500 mt-1">Dapatkan akses langsung ke platform PaaS Sport Clinic dengan <strong>14 hari masa uji coba gratis</strong> tanpa kartu kredit.</p>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800 space-y-1">
                <span class="font-bold block">Mohon periksa data berikut:</span>
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.clinic.submit') }}" method="POST" class="space-y-4 text-xs">
            @csrf

            {{-- Nama Klinik --}}
            <div>
                <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                    Nama Klinik / Pusat Terapi <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="clinic_name" value="{{ old('clinic_name') }}" required
                       placeholder="Contoh: Prima Sport Physiotherapy Center"
                       class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Nama Penanggung Jawab & WhatsApp --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Nama Admin / Owner <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="admin_name" value="{{ old('admin_name') }}" required
                           placeholder="Nama lengkap Anda"
                           class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Nomor WhatsApp <span class="text-rose-500">*</span>
                    </label>
                    <input type="tel" name="phone_number" value="{{ old('phone_number') }}" required
                           inputmode="tel" autocomplete="tel"
                           placeholder="08xxxxxxxxxx"
                           class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            {{-- Email & Password --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Email Login <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           autocomplete="email" inputmode="email" autocapitalize="none" spellcheck="false"
                           placeholder="admin@klinikanda.com"
                           class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Pilihan Paket SaaS <span class="text-rose-500">*</span>
                    </label>
                    <select name="subscription_plan_id" required class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 font-semibold text-gray-800">
                        @foreach($plans as $p)
                            <option value="{{ $p->id }}" {{ (request('plan_id') == $p->id || $p->slug == 'paket-1-tahun' || $loop->first) ? 'selected' : '' }}>
                                {{ $p->name }} {{ $p->subtitle ? '— ' . $p->subtitle : '' }} ({{ $p->formatted_monthly_rate }} | {{ $p->formatted_commitment }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Kata Sandi <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" name="password" required
                           autocomplete="new-password"
                           placeholder="Minimal 8 karakter"
                           class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Ulangi Kata Sandi <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" name="password_confirmation" required
                           autocomplete="new-password"
                           placeholder="Konfirmasi password"
                           class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            {{-- Alamat --}}
            <div>
                <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                    Alamat Lengkap Klinik (Opsional)
                </label>
                <textarea name="address" rows="2" placeholder="Alamat lokasi klinik Anda..."
                          class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">{{ old('address') }}</textarea>
            </div>

            {{-- Tombol Submit --}}
            <div class="pt-4">
                <button type="submit" 
                        class="w-full py-3.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-blue-500/25 transition cursor-pointer">
                    🚀 Mulai Uji Coba Gratis 14 Hari
                </button>
            </div>
        </form>

        <div class="mt-6 pt-6 border-t border-gray-100 text-center text-xs text-gray-500">
            Sudah memiliki akun klinik terdaftar? 
            <a href="{{ route('login') }}" class="font-bold text-blue-600 hover:underline">Masuk ke Portal Klinik</a>
        </div>
    </div>

</body>
</html>

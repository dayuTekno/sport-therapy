@extends('admin.layouts.app')

@section('title', 'Panduan Penggunaan')

@section('content')

@php
    /*
     * Isi panduan. Setiap langkah: judul, penjelasan, screenshot (public/images/panduan),
     * dan poin bernomor yang sama dengan penanda oranye di screenshot.
     * Screenshot dibuat ulang bila tampilan halaman berubah.
     */
    $sections = [
        [
            'id' => 'persiapan',
            'title' => 'Persiapan Awal',
            'subtitle' => 'Dilakukan sekali saat klinik mulai memakai aplikasi (oleh Admin Klinik).',
            'steps' => [
                [
                    'id' => 'login', 'title' => 'Masuk ke aplikasi', 'img' => '01-login',
                    'desc' => 'Buka halaman login, lalu masuk memakai akun yang dibuat saat pendaftaran klinik atau yang diberikan Admin Klinik.',
                    'points' => ['Isi email akun.', 'Isi password. Klik ikon mata untuk melihat password yang diketik.', 'Klik <b>Masuk ke Dashboard</b>.'],
                ],
                [
                    'id' => 'dashboard', 'title' => 'Mengenal Dashboard & menu', 'img' => '02-dashboard', 'route' => 'dashboard',
                    'desc' => 'Dashboard menampilkan ringkasan hari ini (reservasi, sesi selesai, total pasien, pendapatan). Semua menu ada di sidebar kiri.',
                    'points' => ['<b>Menu Layanan</b>: pekerjaan harian (reservasi, sesi terapi, kasir).', '<b>Master Data</b>: data pasien, terapis, peralatan, tindakan, dan referal.', '<b>Statistik Pendapatan Real</b>: grafik pendapatan dengan filter tanggal (lihat bagian Laporan).'],
                ],
                [
                    'id' => 'setting-klinik', 'title' => 'Atur profil klinik', 'img' => '04-setting-klinik', 'route' => 'clinic.settings', 'menu' => 'Settings → Setting Klinik',
                    'desc' => 'Data ini dicetak di kop kwitansi dan Surat Keterangan Sakit, jadi pastikan sudah benar sebelum melayani pasien.',
                    'points' => ['Lengkapi identitas klinik: nama, kota, telepon, email, dan alamat.', 'Unggah logo, cap/stempel, dan tanda tangan (disarankan PNG transparan).', 'Klik <b>Simpan Pengaturan Klinik</b>.'],
                ],
                [
                    'id' => 'tindakan', 'title' => 'Isi katalog Tindakan & tarif', 'img' => '05-tindakan', 'route' => 'therapy-types.index', 'menu' => 'Master Data → Tindakan',
                    'desc' => 'Tindakan adalah tahapan terapi (misalnya Tahap A s/d D) beserta tarifnya. Tarif inilah yang otomatis masuk ke tagihan kasir setiap kali sesi dibuat.',
                    'points' => ['Klik <b>+ Tambah Tindakan</b> untuk menambah tahapan baru.', 'Daftar tindakan yang aktif. Klik <b>Edit</b> untuk mengubah tarif atau durasi.'],
                ],
                [
                    'id' => 'tambah-tindakan', 'title' => 'Form tambah Tindakan', 'img' => '06-tambah-tindakan', 'route' => 'therapy-types.create',
                    'desc' => 'Isi satu tindakan untuk setiap tahap terapi.',
                    'points' => ['<b>Kode</b> singkat, contoh: TA.', '<b>Urutan Tahap</b>: 1 = Tahap A, 2 = Tahap B, dan seterusnya. Dipakai untuk menyarankan tahap berikutnya.', '<b>Nama Tahapan</b>, contoh: "Tahap A: Penanganan Fase Akut".', '<b>Tarif Layanan</b> per sesi (beserta durasi sesi dalam menit).', 'Klik <b>Simpan Tindakan</b>. Pastikan "Aktifkan Tindakan Ini" dicentang agar bisa dipilih saat membuat sesi.'],
                ],
                [
                    'id' => 'terapis', 'title' => 'Tambah data Terapis', 'img' => '07-terapis', 'route' => 'therapists.create', 'menu' => 'Master Data → Terapis',
                    'desc' => 'Terapis dipilih saat membuat reservasi dan sesi. Sistem otomatis menolak jadwal yang bentrok untuk terapis yang sama di jam yang sama.',
                    'points' => ['<b>Kode Terapis</b>, contoh: TRP-001.', '<b>Nama lengkap</b> beserta gelar.', '<b>Keahlian / spesialisasi</b>, nomor HP, dan email.', 'Klik <b>Simpan Terapis</b>.'],
                ],
            ],
        ],
        [
            'id' => 'alur-layanan',
            'title' => 'Alur Layanan Pasien',
            'subtitle' => 'Pekerjaan harian: dari pasien datang sampai pembayaran dan kwitansi.',
            'steps' => [
                [
                    'id' => 'pasien', 'title' => '1. Daftarkan pasien', 'img' => '08-tambah-pasien', 'route' => 'patients.create', 'menu' => 'Master Data → Pasien → Tambah Pasien',
                    'desc' => 'Pasien cukup didaftarkan sekali. Untuk kunjungan berikutnya, langsung buat reservasi.',
                    'points' => ['<b>Nomor HP / WhatsApp</b> dipakai untuk pencarian dan pengingat jadwal via WhatsApp.', 'Isi nama lengkap, usia, jenis kelamin, dan pekerjaan.', '<b>Referal</b> (opsional): pilih dokter/mitra perujuk bila pasien datang dari rujukan.', 'Klik <b>Simpan Pasien</b>.'],
                ],
                [
                    'id' => 'reservasi', 'title' => '2. Buat reservasi', 'img' => '09-buat-reservasi', 'route' => 'reservations.create', 'menu' => 'Menu Layanan → Buat Reservasi',
                    'desc' => 'Reservasi mencatat keluhan pasien dan rencana jadwal kedatangan.',
                    'points' => ['<b>Pilih pasien</b>: ketik nama lalu pilih dari daftar. Jika belum terdaftar, klik <b>+ Pasien Baru</b>.', 'Isi <b>keluhan utama</b>, lama keluhan, dan riwayat cedera (jika ada).', 'Pilih <b>tanggal & jam</b> kedatangan. Ada tombol cepat Hari Ini / Besok / Lusa dan pilihan jam.', '<b>Terapis</b> (opsional): kosongkan jika ditentukan klinik.', 'Klik <b>Kirim Data Reservasi</b>. Status awal reservasi: <i>Menunggu Konfirmasi</i>.'],
                ],
                [
                    'id' => 'daftar-reservasi', 'title' => '3. Pantau reservasi aktif', 'img' => '10-daftar-reservasi', 'route' => 'reservations.index', 'menu' => 'Menu Layanan → Reservasi Terapi',
                    'desc' => 'Semua reservasi yang belum selesai tampil di sini. Angka di menu sidebar menunjukkan jumlah reservasi baru.',
                    'points' => ['<b>+ Buat Reservasi Baru</b>.', 'Tab status: Menunggu Konfirmasi, Terkonfirmasi, dan Sedang Terapi.', 'Klik <b>kode reservasi</b> (atau tombol Detail) untuk membuka detailnya.'],
                ],
                [
                    'id' => 'detail-reservasi', 'title' => '4. Detail reservasi', 'img' => '11-detail-reservasi',
                    'desc' => 'Halaman pusat untuk satu reservasi: data pasien, keluhan, pengingat WhatsApp, sesi terapi, dan pembayaran.',
                    'points' => ['<b>Status</b> reservasi, status kasir, dan rujukan pasien.', '<b>Kirim Pengingat ke WhatsApp</b>: membuka WhatsApp dengan pesan jadwal yang sudah tersusun (bisa juga disalin).', '<b>Tandai Selesai</b> bila terapi sudah tuntas. Tombol <b>Batalkan Reservasi</b> wajib disertai alasan.'],
                ],
                [
                    'id' => 'buka-sesi', 'title' => '5. Buka sesi terapi dari reservasi', 'img' => '12-buka-sesi',
                    'desc' => 'Gulir ke bawah di halaman detail reservasi ke bagian <b>Tindakan Terapi Pasien</b>.',
                    'points' => ['Klik <b>Buka / Tambah Sesi Terapi Pasien Ini</b>.', 'Tabel riwayat sesi pasien beserta tarif tiap sesi dan <b>Total Tagihan Reservasi</b>.'],
                ],
                [
                    'id' => 'tambah-sesi', 'title' => '6. Jadwalkan sesi & pilih Tindakan', 'img' => '13-tambah-sesi',
                    'desc' => 'Data pasien dan keluhan otomatis terisi dari reservasi. Satu pasien bisa mendapat lebih dari satu sesi dalam sehari (misalnya sesi pagi dan sore).',
                    'points' => ['Atur <b>jadwal / jam sesi</b> (urutan sesi hari ini terisi otomatis).', '<b>Pilih Tindakan</b>: sistem menyarankan tahap berikutnya sesuai riwayat pasien.', 'Pilih <b>terapis</b> penanggung jawab.', 'Klik <b>Simpan & Buka Sesi Terapi</b>.'],
                ],
                [
                    'id' => 'lembar-sesi', 'title' => '7. Isi lembar tindakan & evaluasi', 'img' => '14-lembar-sesi',
                    'desc' => 'Diisi terapis setelah menangani pasien. Catatan ini menjadi rekam terapi pasien.',
                    'points' => ['<b>Tindakan / teknik terapi</b> yang dilakukan (wajib).', '<b>Catatan evaluasi</b> dan hasil sesi, misalnya skala nyeri atau lingkup gerak (wajib).', 'Centang <b>Langsung Buat Sesi Terapi Lanjutan</b> bila pasien lanjut ke tahap berikutnya, lalu pilih tahap, jadwal, dan terapisnya.', 'Klik <b>Selesaikan Sesi Terapi Ini</b>.'],
                    'note' => 'Jika sesi diselesaikan <b>tanpa</b> sesi lanjutan dan tidak ada sesi lain yang masih terjadwal, reservasi otomatis berstatus <b>Selesai</b> dan siap dibayar di kasir.',
                ],
                [
                    'id' => 'sesi-terapi', 'title' => '8. Pantau jadwal sesi harian', 'img' => '15-sesi-terapi', 'route' => 'therapy-sessions.index', 'menu' => 'Menu Layanan → Sesi Terapi Pasien',
                    'desc' => 'Daftar semua sesi per tanggal untuk terapis dan admin, termasuk pasien yang mendapat lebih dari satu sesi dalam sehari.',
                    'points' => ['<b>+ Tambah Sesi Terapi Baru</b> untuk membuat sesi langsung dari sini.', 'Filter berdasarkan tanggal, status sesi, atau cari nama/no. HP pasien, lalu klik <b>Terapkan</b>.'],
                ],
                [
                    'id' => 'kasir', 'title' => '9. Pembayaran di kasir', 'img' => '16-kasir', 'route' => 'cashier.index', 'menu' => 'Menu Layanan → Kasir & Pembayaran',
                    'desc' => 'Tagihan dihitung otomatis dari tarif semua sesi pada reservasi.',
                    'points' => ['Tab <b>Menunggu Pembayaran</b> berisi daftar tagihan yang belum lunas.', 'Klik <b>Bayar di Kasir</b> pada tagihan pasien.', 'Tab <b>Riwayat Pembayaran (Lunas)</b> untuk melihat dan mencetak ulang kwitansi.'],
                ],
                [
                    'id' => 'proses-bayar', 'title' => '10. Proses pembayaran', 'img' => '17-proses-bayar',
                    'desc' => 'Periksa rincian tagihan, lalu konfirmasi pembayaran. Semua input nominal otomatis memakai titik pemisah ribuan.',
                    'points' => ['<b>Biaya lain-lain</b> (opsional) beserta keterangannya, misalnya kinesio tape atau perban. Tersedia tombol cepat +15rb, +25rb, dan seterusnya.', '<b>Diskon</b> dalam Rupiah atau Persen. Total tagihan akhir langsung dihitung ulang.', 'Pilih <b>metode pembayaran</b>: Cash, QRIS, Transfer, Debit, atau Lainnya.', 'Isi <b>nominal diterima</b>. Kembalian dihitung otomatis. Untuk non-tunai, nominal otomatis pas.', 'Klik <b>Konfirmasi Pembayaran & Terbitkan Invoice</b>.'],
                ],
                [
                    'id' => 'kwitansi', 'title' => '11. Cetak kwitansi & surat keterangan sakit', 'img' => '18-kwitansi',
                    'desc' => 'Setelah pembayaran, halaman kwitansi langsung terbuka.',
                    'points' => ['<b>Cetak Kwitansi</b> lewat printer atau simpan sebagai PDF.', '<b>Cetak Surat Keterangan Sakit (PDF)</b> bila pasien membutuhkannya.'],
                ],
            ],
        ],
        [
            'id' => 'laporan',
            'title' => 'Laporan & Riwayat',
            'subtitle' => 'Memantau pendapatan, arsip reservasi, dan rekam terapi pasien.',
            'steps' => [
                [
                    'id' => 'statistik', 'title' => 'Statistik pendapatan real', 'img' => '03-statistik-pendapatan', 'route' => 'dashboard',
                    'desc' => 'Di Dashboard. Pendapatan real = total tagihan + biaya lain − diskon dari transaksi lunas, <b>tanpa</b> uang kembalian.',
                    'points' => ['Pilih rentang cepat: Hari ini, 7 hari, 30 hari, Bulan ini, Bulan lalu, atau Tahun ini.', 'Atau isi tanggal <b>Dari</b>–<b>Sampai</b> sendiri, lalu klik <b>Terapkan</b>.', 'Ringkasan: total pendapatan dan perbandingannya dengan periode sebelumnya.', 'Rincian per metode pembayaran. Di bawah grafik ada tombol <b>Lihat tabel data</b>.'],
                ],
                [
                    'id' => 'riwayat', 'title' => 'Riwayat terapi', 'img' => '19-riwayat', 'route' => 'reservations.history', 'menu' => 'Menu Layanan → Riwayat Terapi',
                    'desc' => 'Arsip reservasi yang sudah selesai maupun dibatalkan (beserta alasannya).',
                    'points' => ['Cari dan filter arsip.', 'Klik kode reservasi untuk melihat detail atau mencetak ulang dokumen.'],
                ],
                [
                    'id' => 'rekap', 'title' => 'Rekap rekam terapi', 'img' => '20-rekap', 'route' => 'reports.medical_records.index', 'menu' => 'Laporan → Rekap Rekam Terapi',
                    'desc' => 'Rekap seluruh sesi terapi beserta evaluasi klinisnya untuk keperluan laporan.',
                    'points' => ['Filter berdasarkan tanggal, terapis, tindakan, dan status sesi, lalu klik <b>Terapkan Filter</b>.', 'Klik <b>Lihat Detail</b> untuk membuka rekam terapi lengkap pasien.'],
                ],
            ],
        ],
        [
            'id' => 'akun',
            'title' => 'Pengaturan, Akun & Langganan',
            'subtitle' => 'Referal, pengguna, paket langganan, dan program referal klinik.',
            'steps' => [
                [
                    'id' => 'referal-pasien', 'title' => 'Mitra referal pasien', 'img' => '21-referal-pasien', 'route' => 'referrals.index', 'menu' => 'Master Data → Referal',
                    'desc' => 'Data dokter atau instansi yang merujuk pasien ke klinik, beserta komisi per sesi terapi.',
                    'points' => ['Klik <b>Tambah Referal</b> untuk menambah mitra perujuk.', 'Pantau jumlah pasien terafiliasi, sesi terapi, dan akumulasi komisi tiap mitra.'],
                ],
                [
                    'id' => 'user', 'title' => 'Akun staf (User)', 'img' => '25-user', 'route' => 'users.index', 'menu' => 'Settings → User',
                    'desc' => 'Buat akun terpisah untuk setiap staf. Role <b>admin</b> mendapat akses penuh. Role <b>operator</b> mendapat akses operasional tanpa menu Settings.',
                    'points' => ['Klik <b>Tambah User</b>, isi nama, email, password, dan pilih role.'],
                ],
                [
                    'id' => 'langganan', 'title' => 'Beli / perpanjang paket langganan', 'img' => '23-langganan', 'route' => 'clinic.subscription', 'menu' => 'Settings → Langganan Paket',
                    'desc' => 'Jika masa langganan habis, menu akan terkunci, tetapi halaman ini tetap bisa dibuka.',
                    'points' => ['Pilih paket.', 'Lakukan pembayaran sesuai instruksi (rekening tujuan).', 'Pilih metode, unggah bukti bayar (bisa menyusul), lalu klik <b>Kirim Request Pembelian Paket</b>. Paket aktif setelah diverifikasi admin platform.'],
                    'note' => 'Jika langganan masih berjalan, masa aktif paket baru otomatis disambung dari tanggal berakhir yang lama, jadi tidak ada hari yang hilang.',
                ],
                [
                    'id' => 'referal-klinik', 'title' => 'Program referal klinik', 'img' => '24-referal-klinik', 'route' => 'clinic.referral', 'menu' => 'Settings → Referal Klinik',
                    'desc' => 'Setiap klinik punya kode referal sendiri. Ajak klinik lain mendaftar dan dapatkan komisi saat mereka berlangganan paket.',
                    'points' => ['Kode referal dan link undangan klinik Anda. Klik untuk menyalin.', 'Klik <b>Bagikan via WhatsApp</b> untuk mengirim undangan.', 'Besaran komisi untuk setiap paket. Komisi tidak diberikan saat klinik undangan mendaftar atau selama masa uji coba.'],
                ],
                [
                    'id' => 'daftar-klinik', 'title' => 'Pendaftaran klinik baru (dengan kode referal)', 'img' => '22-daftar-klinik',
                    'desc' => 'Tampilan yang dilihat klinik undangan saat membuka link referal Anda. Kode referal sudah otomatis terisi.',
                    'points' => ['Pilih paket yang diinginkan.', 'Kode referal klinik pengundang (otomatis dari link).', 'Klik <b>Mulai Uji Coba Gratis</b>.'],
                ],
            ],
        ],
    ];

    if ($isSaasAdmin) {
        $sections[] = [
            'id' => 'saas',
            'title' => 'Khusus Admin Platform (SaaS)',
            'subtitle' => 'Bagian ini hanya terlihat oleh Administrator SaaS.',
            'steps' => [
                [
                    'id' => 'saas-request', 'title' => 'Verifikasi request pembelian paket', 'img' => '30-saas-request-paket', 'route' => 'saas.subscription-requests.index', 'menu' => 'Platform SaaS → Request Paket',
                    'desc' => 'Periksa bukti bayar dari klinik, lalu setujui atau tolak secara manual.',
                    'points' => ['<b>Setujui & Aktifkan Paket</b>: klinik langsung aktif, masa langganan diperpanjang, dan komisi referal tercatat (jika ini langganan pertama klinik undangan).', '<b>Tolak</b>: wajib mengisi alasan agar klinik mengetahui penyebabnya.', 'Atur <b>instruksi pembayaran</b> (rekening tujuan) yang tampil di halaman klinik.'],
                ],
                [
                    'id' => 'saas-paket', 'title' => 'Kelola paket langganan', 'img' => '31-saas-paket', 'route' => 'saas.plans.index', 'menu' => 'Platform SaaS → Paket Langganan',
                    'desc' => 'Ubah harga, durasi, kuota, fitur, dan masa trial setiap paket.',
                    'points' => ['Klik <b>Edit Paket</b> pada kartu paket untuk mengubahnya.', 'Form <b>Tambah Tier Paket Baru</b>.'],
                ],
                [
                    'id' => 'saas-referal', 'title' => 'Setting komisi referal klinik', 'img' => '32-saas-referal', 'route' => 'saas.referrals.index', 'menu' => 'Platform SaaS → Referal Klinik',
                    'desc' => 'Komisi diatur per paket, dalam Rupiah atau Persen dari harga paket.',
                    'points' => ['Atur jenis dan nilai komisi untuk tiap paket. Kolom kanan menampilkan nominal yang diterima perujuk.', 'Klik <b>Simpan Setting Komisi</b>. Komisi yang sudah tercatat tidak ikut berubah. Di bawahnya, tandai komisi yang sudah dibayarkan.'],
                ],
                [
                    'id' => 'saas-klinik', 'title' => 'Kelola mitra klinik', 'img' => '33-saas-klinik', 'route' => 'saas.clinics.index', 'menu' => 'Platform SaaS → Mitra Klinik',
                    'desc' => 'Daftar seluruh klinik: status, paket, kuota, kode referal, dan klinik perujuknya.',
                    'points' => ['<b>Daftarkan Klinik Baru</b> secara manual.', 'Klik <b>Edit</b> untuk mengubah status atau masa aktif. Klik <b>Tinjau</b> untuk masuk dan melihat operasional klinik tersebut.'],
                ],
            ],
        ];
    }

    $flow = [
        ['pasien', '👤', 'Daftarkan pasien'],
        ['reservasi', '📋', 'Buat reservasi'],
        ['tambah-sesi', '🗓️', 'Jadwalkan sesi'],
        ['lembar-sesi', '📝', 'Isi evaluasi'],
        ['proses-bayar', '💳', 'Bayar di kasir'],
        ['kwitansi', '🖨️', 'Cetak kwitansi'],
    ];

    $faqs = [
        ['Pasien belum terdaftar saat membuat reservasi?', 'Klik <b>+ Pasien Baru</b> di form reservasi, atau tambahkan lewat <b>Master Data → Pasien</b> terlebih dahulu.'],
        ['Muncul pesan jadwal terapis bentrok?', 'Terapis tersebut sudah punya jadwal di jam yang sama. Pilih jam lain atau terapis lain.'],
        ['Kenapa reservasi tiba-tiba berstatus Selesai?', 'Reservasi otomatis selesai saat sesi terakhir diselesaikan tanpa sesi lanjutan. Tagihannya lalu muncul di Kasir.'],
        ['Tagihan di kasir tidak sesuai?', 'Tagihan dihitung dari tarif Tindakan pada setiap sesi. Periksa tarif di <b>Master Data → Tindakan</b> dan sesi pada detail reservasi.'],
        ['Semua menu terkunci?', 'Masa langganan klinik sudah habis. Buka <b>Beli / Perpanjang Paket</b>. Menu kembali terbuka setelah pembayaran diverifikasi.'],
        ['Kenapa pendapatan di statistik berbeda dengan uang yang diterima?', 'Statistik menghitung nilai tagihan (setelah diskon dan biaya lain), bukan uang yang diserahkan pasien, karena uang kembalian tidak termasuk pendapatan.'],
    ];
@endphp

<div x-data="{ zoom: null }" @keydown.escape.window="zoom = null">

    {{-- HEADER --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <span>📖</span> Panduan Penggunaan
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 max-w-3xl">
                Langkah demi langkah memakai aplikasi sesuai alur layanan klinik. Nomor oranye pada setiap gambar sama dengan nomor langkah di sebelahnya. Klik gambar untuk memperbesar.
            </p>
        </div>
    </div>

    {{-- ALUR UTAMA --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-5 mb-8">
        <h2 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-4">Alur layanan pasien</h2>
        <ol class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @foreach($flow as $i => [$anchor, $icon, $label])
                <li>
                    <a href="#{{ $anchor }}" class="relative flex flex-col items-center text-center gap-1.5 p-3 rounded-xl border border-gray-100 dark:border-gray-800 hover:border-blue-300 hover:bg-blue-50/50 dark:hover:bg-gray-800 transition h-full">
                        <span class="absolute top-2 left-2 w-5 h-5 rounded-full bg-orange-500 text-white text-[10px] font-black flex items-center justify-center">{{ $i + 1 }}</span>
                        <span class="text-2xl" aria-hidden="true">{{ $icon }}</span>
                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-200">{{ $label }}</span>
                    </a>
                </li>
            @endforeach
        </ol>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[230px_minmax(0,1fr)] gap-8">

        {{-- DAFTAR ISI --}}
        <nav class="hidden lg:block" aria-label="Daftar isi panduan">
            <div class="sticky top-24 max-h-[calc(100dvh-8rem)] overflow-y-auto pr-2 space-y-5 text-sm">
                @foreach($sections as $section)
                    <div>
                        <a href="#{{ $section['id'] }}" class="block text-xs font-bold uppercase tracking-wider text-gray-500 hover:text-blue-600 mb-1.5">{{ $section['title'] }}</a>
                        <ul class="space-y-1 border-l border-gray-200 dark:border-gray-800">
                            @foreach($section['steps'] as $step)
                                <li><a href="#{{ $step['id'] }}" class="block pl-3 py-0.5 -ml-px border-l border-transparent text-gray-600 dark:text-gray-400 hover:text-blue-600 hover:border-blue-500 text-[13px]">{{ $step['title'] }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
                <a href="#faq" class="block text-xs font-bold uppercase tracking-wider text-gray-500 hover:text-blue-600">Pertanyaan Umum</a>
            </div>
        </nav>

        {{-- ISI PANDUAN --}}
        <div class="min-w-0 space-y-12">
            @foreach($sections as $section)
                <section id="{{ $section['id'] }}" class="scroll-mt-24">
                    <div class="mb-5 pb-3 border-b border-gray-200 dark:border-gray-800">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $section['title'] }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $section['subtitle'] }}</p>
                    </div>

                    <div class="space-y-8">
                        @foreach($section['steps'] as $step)
                            @php $imgUrl = asset('images/panduan/' . $step['img'] . '.jpg'); @endphp
                            <article id="{{ $step['id'] }}" class="scroll-mt-24 bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
                                <div class="p-5 md:p-6">
                                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-2 mb-2">
                                        <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ $step['title'] }}</h3>
                                        @if(!empty($step['route']) && \Illuminate\Support\Facades\Route::has($step['route']))
                                            <a href="{{ route($step['route']) }}" class="shrink-0 text-xs font-semibold text-blue-600 hover:underline">Buka halaman →</a>
                                        @endif
                                    </div>
                                    @if(!empty($step['menu']))
                                        <p class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 mb-2">
                                            <span class="px-2 py-0.5 rounded-md bg-gray-100 dark:bg-gray-800">Menu: {{ $step['menu'] }}</span>
                                        </p>
                                    @endif
                                    <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">{!! $step['desc'] !!}</p>

                                    <ol class="mt-4 space-y-2">
                                        @foreach($step['points'] as $n => $point)
                                            <li class="flex gap-3 text-sm text-gray-700 dark:text-gray-200 leading-relaxed">
                                                <span class="shrink-0 w-6 h-6 rounded-full bg-orange-500 text-white text-xs font-black flex items-center justify-center mt-px">{{ $n + 1 }}</span>
                                                <span>{!! $point !!}</span>
                                            </li>
                                        @endforeach
                                    </ol>

                                    @if(!empty($step['note']))
                                        <div class="mt-4 p-3 rounded-xl bg-blue-50 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900 text-[13px] text-blue-900 dark:text-blue-200 leading-relaxed">
                                            💡 {!! $step['note'] !!}
                                        </div>
                                    @endif
                                </div>

                                <figure class="border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-950 p-3 md:p-4">
                                    <button type="button" class="block w-full cursor-zoom-in" @click="zoom = @js($imgUrl)" aria-label="Perbesar gambar {{ $step['title'] }}">
                                        <img src="{{ $imgUrl }}" alt="Tampilan layar: {{ $step['title'] }}" loading="lazy" decoding="async"
                                             class="w-full h-auto rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm">
                                    </button>
                                </figure>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endforeach

            {{-- FAQ --}}
            <section id="faq" class="scroll-mt-24">
                <div class="mb-5 pb-3 border-b border-gray-200 dark:border-gray-800">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Pertanyaan Umum</h2>
                </div>
                <div class="space-y-3">
                    @foreach($faqs as [$q, $a])
                        <details class="group bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm">
                            <summary class="cursor-pointer list-none flex items-center justify-between gap-3 p-4 text-sm font-semibold text-gray-800 dark:text-gray-100">
                                {{ $q }}
                                <span class="text-gray-400 transition group-open:rotate-45 text-lg leading-none" aria-hidden="true">+</span>
                            </summary>
                            <p class="px-4 pb-4 -mt-1 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">{!! $a !!}</p>
                        </details>
                    @endforeach
                </div>
            </section>
        </div>
    </div>

    {{-- PERBESAR GAMBAR --}}
    <template x-if="zoom">
        <div class="fixed inset-0 z-[999] bg-black/80 flex items-center justify-center p-4 cursor-zoom-out" @click="zoom = null">
            <img :src="zoom" alt="" class="max-w-full max-h-full rounded-lg shadow-2xl">
            <button type="button" class="absolute top-4 right-4 w-10 h-10 rounded-full bg-white/90 text-gray-800 text-xl font-bold" aria-label="Tutup">×</button>
        </div>
    </template>
</div>

@endsection

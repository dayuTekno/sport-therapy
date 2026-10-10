#!/usr/bin/env python3
"""
Script to generate high-impact presentation deck in PPTX format:
SportClinic.io - Platform SaaS Manajemen Klinik Fisioterapi & Cedera Olahraga Modern
"""

import sys
import os
from pptx import Presentation
from pptx.util import Inches, Pt
from pptx.dml.color import RGBColor
from pptx.enum.text import PP_ALIGN, MSO_ANCHOR
from pptx.enum.shapes import MSO_SHAPE

def create_deck(filename="SportClinic_Presentation.pptx"):
    prs = Presentation()
    # 16:9 widescreen layout
    prs.slide_width = Inches(13.333)
    prs.slide_height = Inches(7.5)
    blank_layout = prs.slide_layouts[6]

    # Theme Colors
    COLOR_NAVY_DARK   = RGBColor(15, 23, 42)      # #0f172a (Primary dark background)
    COLOR_NAVY_LIGHT  = RGBColor(30, 41, 59)      # #1e293b (Card dark)
    COLOR_BG_LIGHT    = RGBColor(248, 250, 252)   # #f8fafc (Clean light background)
    COLOR_WHITE       = RGBColor(255, 255, 255)   # #ffffff
    COLOR_BLUE_MAIN   = RGBColor(37, 99, 235)     # #2563eb (Medical blue brand)
    COLOR_BLUE_LIGHT  = RGBColor(239, 246, 255)   # #eff6ff
    COLOR_TEAL_MAIN   = RGBColor(13, 148, 136)    # #0d9488
    COLOR_TEAL_LIGHT  = RGBColor(240, 253, 250)   # #f0fdf4
    COLOR_GREEN_MAIN  = RGBColor(16, 185, 129)    # #10b981
    COLOR_AMBER_MAIN  = RGBColor(245, 158, 11)    # #f59e0b
    COLOR_ROSE_MAIN   = RGBColor(225, 29, 72)     # #e11d48
    COLOR_ROSE_LIGHT  = RGBColor(255, 241, 242)   # #fff1f2
    COLOR_TEXT_DARK   = RGBColor(15, 23, 42)      # #0f172a
    COLOR_TEXT_MUTED  = RGBColor(100, 116, 139)   # #64748b
    COLOR_BORDER_LIGHT= RGBColor(226, 232, 240)   # #e2e8f0

    def set_slide_background(slide, color):
        bg = slide.shapes.add_shape(MSO_SHAPE.RECTANGLE, 0, 0, prs.slide_width, prs.slide_height)
        bg.fill.solid()
        bg.fill.fore_color.rgb = color
        bg.line.fill.background()
        return bg

    def add_header(slide, badge_text, title_text, subtitle_text=None, is_dark=False):
        # Badge
        badge = slide.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(0.5), Inches(3.8), Inches(0.38))
        badge.fill.solid()
        badge.fill.fore_color.rgb = RGBColor(30, 58, 138) if is_dark else COLOR_BLUE_LIGHT
        badge.line.color.rgb = COLOR_BLUE_MAIN
        tf_b = badge.text_frame
        tf_b.word_wrap = True
        p_b = tf_b.paragraphs[0]
        p_b.text = badge_text.upper()
        p_b.font.size = Pt(10)
        p_b.font.bold = True
        p_b.font.color.rgb = RGBColor(147, 197, 253) if is_dark else COLOR_BLUE_MAIN
        p_b.alignment = PP_ALIGN.CENTER

        # Title
        tb = slide.shapes.add_textbox(Inches(0.8), Inches(0.95), Inches(11.7), Inches(0.8))
        tf = tb.text_frame
        tf.word_wrap = True
        tf.margin_left = tf.margin_top = tf.margin_right = tf.margin_bottom = 0
        p = tf.paragraphs[0]
        p.text = title_text
        p.font.size = Pt(24)
        p.font.bold = True
        p.font.color.rgb = COLOR_WHITE if is_dark else COLOR_TEXT_DARK

        # Subtitle
        if subtitle_text:
            p_sub = tf.add_paragraph()
            p_sub.text = subtitle_text
            p_sub.font.size = Pt(12)
            p_sub.font.color.rgb = RGBColor(148, 163, 184) if is_dark else COLOR_TEXT_MUTED
            p_sub.space_before = Pt(4)

    # =========================================================================
    # SLIDE 1: COVER SLIDE (MODERN DARK THEME)
    # =========================================================================
    s1 = prs.slides.add_slide(blank_layout)
    set_slide_background(s1, COLOR_NAVY_DARK)

    # Accent decorative glow bar
    bar = s1.shapes.add_shape(MSO_SHAPE.RECTANGLE, Inches(0.8), Inches(1.2), Inches(0.12), Inches(4.8))
    bar.fill.solid()
    bar.fill.fore_color.rgb = COLOR_BLUE_MAIN
    bar.line.fill.background()

    # Cover Title Box
    title_box = s1.shapes.add_textbox(Inches(1.2), Inches(1.1), Inches(11.2), Inches(4.5))
    tf1 = title_box.text_frame
    tf1.word_wrap = True

    p = tf1.paragraphs[0]
    p.text = "PLATFORM DIGITALISASI KLINIK FISIOTERAPI & REHABILITASI"
    p.font.size = Pt(12)
    p.font.bold = True
    p.font.color.rgb = RGBColor(96, 165, 250)

    p2 = tf1.add_paragraph()
    p2.text = "SportClinic.io"
    p2.font.size = Pt(44)
    p2.font.bold = True
    p2.font.color.rgb = COLOR_WHITE
    p2.space_before = Pt(10)

    p3 = tf1.add_paragraph()
    p3.text = "Solusi Manajemen Terpadu: Dari Reservasi WhatsApp, Rekam Terapi Berjenjang (Tahap A-D), Kasir Terintegrasi, hingga Rekap Medis Otomatis"
    p3.font.size = Pt(16)
    p3.font.color.rgb = RGBColor(203, 213, 225)
    p3.space_before = Pt(12)

    # Key highlights pill box
    p4 = tf1.add_paragraph()
    p4.text = "🚀 Siap Pakai di Cloud  •  🎁 Gratis 14 Hari Sistem  •  🌐 BONUS Gratis 2 Bulan Website di CorpShow"
    p4.font.size = Pt(13)
    p4.font.bold = True
    p4.font.color.rgb = COLOR_GREEN_MAIN
    p4.space_before = Pt(28)

    # Bottom branding footer
    footer = s1.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(1.2), Inches(5.9), Inches(10.8), Inches(0.8))
    footer.fill.solid()
    footer.fill.fore_color.rgb = COLOR_NAVY_LIGHT
    footer.line.color.rgb = RGBColor(51, 65, 85)
    ftf = footer.text_frame
    ftf.vertical_anchor = MSO_ANCHOR.MIDDLE
    fp = ftf.paragraphs[0]
    fp.text = "RS Terpadu & Mitra Klinik Sport Therapist  |  https://sport-therapist.corpshow.id  |  Presentasi Solusi 2026"
    fp.font.size = Pt(11)
    fp.font.color.rgb = RGBColor(148, 163, 184)
    fp.alignment = PP_ALIGN.CENTER

    # =========================================================================
    # SLIDE 2: PERMASALAHAN METODE LAMA (KENAPA EXCEL MERUGIKAN?)
    # =========================================================================
    s2 = prs.slides.add_slide(blank_layout)
    set_slide_background(s2, COLOR_BG_LIGHT)
    add_header(s2, "Tantangan Operasional", "Mengapa Menggunakan Microsoft Excel Menjadi Beban bagi Klinik?", 
               "Pengelolaan konvensional dengan spreadsheet dan buku catatan manual memicu kebocoran waktu, finansial, dan reputasi klinik.")

    pain_points = [
        ("❌ Jadwal Bentrok & No-Show Tinggi", 
         "Excel tidak memiliki validasi waktu nyata (real-time). Pasien sering datang bersamaan di jam yang sama, dan staf harus menyimpan nomor satu per satu secara manual untuk mengingatkan.",
         COLOR_ROSE_MAIN, COLOR_ROSE_LIGHT),
        ("❌ Data Tersebar & Rentan Korup", 
         "File Excel mudah tertimpa (overwrite), rusak, atau terkena virus. Ketika beberapa staf membuka file di saat bersamaan, data sesi terakhir kerap hilang tanpa jejak cadangan.",
         COLOR_AMBER_MAIN, RGBColor(254, 243, 199)),
        ("❌ Evaluasi Cedera Tidak Terstandarisasi", 
         "Catatan perkembangan atlet tidak memiliki tahapan klinis yang terukur (A s/d D). Jika terapis berhalangan hadir, terapis pengganti kebingungan melanjutkan riwayat tindakan sebelumnya.",
         RGBColor(124, 58, 237), RGBColor(245, 243, 255)),
        ("❌ Kebocoran Kasir & Rekap Rumit", 
         "Perhitungan tarif sesi terapi, sewa alat, dan tindakan tambahan dihitung manual dengan rumus Excel yang rentan typo. Butuh waktu 2-3 hari di akhir bulan hanya untuk mencocokkan setoran kasir.",
         COLOR_TEXT_DARK, RGBColor(241, 245, 249))
    ]

    card_w = Inches(5.6)
    card_h = Inches(2.3)
    coords = [
        (Inches(0.8), Inches(2.1)),
        (Inches(6.8), Inches(2.1)),
        (Inches(0.8), Inches(4.7)),
        (Inches(6.8), Inches(4.7)),
    ]

    for idx, (title, desc, border_c, bg_c) in enumerate(pain_points):
        x, y = coords[idx]
        card = s2.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, x, y, card_w, card_h)
        card.fill.solid()
        card.fill.fore_color.rgb = bg_c
        card.line.color.rgb = border_c
        card.line.width = Pt(1.5)
        
        ctf = card.text_frame
        ctf.word_wrap = True
        ctf.margin_left = ctf.margin_right = Inches(0.25)
        ctf.margin_top = Inches(0.2)
        
        cp = ctf.paragraphs[0]
        cp.text = title
        cp.font.size = Pt(14)
        cp.font.bold = True
        cp.font.color.rgb = border_c
        
        cp2 = ctf.add_paragraph()
        cp2.text = desc
        cp2.font.size = Pt(11.5)
        cp2.font.color.rgb = COLOR_TEXT_DARK
        cp2.space_before = Pt(8)

    # =========================================================================
    # SLIDE 3: KELEBIHAN WEBSITE VS MICROSOFT EXCEL (TABEL KOMPARASI)
    # =========================================================================
    s3 = prs.slides.add_slide(blank_layout)
    set_slide_background(s3, COLOR_BG_LIGHT)
    add_header(s3, "Studi Komparasi Nyata", "Perbandingan Head-to-Head: Website Khusus vs Microsoft Excel",
               "Melihat langsung perbedaan fundamental antara penggunaan spreadsheet konvensional dengan sistem klinik modern.")

    # Create Comparison Table
    rows = 7
    cols = 3
    t_left = Inches(0.8)
    t_top = Inches(2.05)
    t_width = Inches(11.7)
    t_height = Inches(4.9)

    table_shape = s3.shapes.add_table(rows, cols, t_left, t_top, t_width, t_height)
    tbl = table_shape.table
    tbl.columns[0].width = Inches(2.8)   # Aspek
    tbl.columns[1].width = Inches(4.45)  # Excel
    tbl.columns[2].width = Inches(4.45)  # Website SportClinic

    comparisons = [
        ("ASPEK OPERASIONAL", "PENGGUNAAN MICROSOFT EXCEL", "WEBSITE SPORTCLINIC.IO"),
        ("Akses & Kolaborasi", "Satu file dibuka bergantian; rentan file lock & bentrok versi", "Multi-user realtime (Admisi, Terapis, Kasir) dengan hak akses ketat (RBAC)"),
        ("Penjadwalan & Slot Jam", "Manual; risiko tinggi jadwal terapis bertabrakan", "Otomatis mengunci kuota & jam terapis, no double-booking"),
        ("Notifikasi Pasien", "Harus simpan nomor WhatsApp manual di ponsel staf", "1-Klik kirim konfirmasi jadwal & tiket WhatsApp langsung dari sistem"),
        ("Protokol Fisioterapi", "Catatan acak; tidak terstruktur; sulit dievaluasi", "Alur berjenjang terstandarisasi (Tahap A Akut s/d D Return to Sport)"),
        ("Billing & Kasir", "Rumus kalkulasi manual; rentan salah hitung tarif", "Tagihan otomatis per reservasi, cetak struk resmi kasir instan"),
        ("Keamanan & Backup Data", "File tersimpan lokal di 1 PC; risiko hilang sangat tinggi", "Penyimpanan Cloud terenkripsi, backup otomatis, multi-tenant aman")
    ]

    for r_idx, row_data in enumerate(comparisons):
        for c_idx, val in enumerate(row_data):
            cell = tbl.cell(r_idx, c_idx)
            cell.text = val
            p = cell.text_frame.paragraphs[0]
            p.word_wrap = True
            
            if r_idx == 0:
                cell.fill.solid()
                if c_idx == 0:
                    cell.fill.fore_color.rgb = COLOR_NAVY_DARK
                elif c_idx == 1:
                    cell.fill.fore_color.rgb = RGBColor(185, 28, 28) # Red
                else:
                    cell.fill.fore_color.rgb = COLOR_BLUE_MAIN
                p.font.bold = True
                p.font.size = Pt(11.5)
                p.font.color.rgb = COLOR_WHITE
                p.alignment = PP_ALIGN.CENTER
            else:
                cell.fill.solid()
                cell.fill.fore_color.rgb = COLOR_WHITE if r_idx % 2 == 1 else RGBColor(241, 245, 249)
                p.font.size = Pt(10.5)
                if c_idx == 0:
                    p.font.bold = True
                    p.font.color.rgb = COLOR_TEXT_DARK
                elif c_idx == 1:
                    p.font.color.rgb = RGBColor(185, 28, 28)
                else:
                    p.font.color.rgb = RGBColor(4, 120, 87) # Dark Green
                    p.font.bold = True

    # =========================================================================
    # SLIDE 4: SEBERAPA EFISIEN WEBSITE INI? (METRIK & DAMPAK NYATA)
    # =========================================================================
    s4 = prs.slides.add_slide(blank_layout)
    set_slide_background(s4, COLOR_BG_LIGHT)
    add_header(s4, "Dampak Efisiensi Terukur", "Seberapa Efisien Website Ini bagi Manajemen Klinik?",
               "Hasil implementasi nyata membuktikan akselerasi kinerja operasional klinik hingga berkali-kali lipat.")

    metrics = [
        ("⚡ 85%", "Pangkas Waktu Pendaftaran", 
         "Dari 10-15 menit (tulis buku/buka excel) menjadi < 2 menit per pasien. Pasien cukup menyebutkan nomor HP untuk memanggil riwayat.",
         COLOR_BLUE_MAIN, COLOR_BLUE_LIGHT),
        ("📅 98.5%", "Kehadiran Tepat Waktu (On-Time)", 
         "No-show rate (pasien mangkir) turun drastis berkat sistem 1-klik kirim pengingat jadwal via WhatsApp dengan template ramah & informatif.",
         COLOR_TEAL_MAIN, COLOR_TEAL_LIGHT),
        ("💰 100%", "Akurasi Kasir & Billing", 
         "Menghilangkan risiko selisih kas dan kelupaan menagih tindakan tambahan. Satu tagihan merangkum seluruh sesi dan langsung mencetak kuitansi resmi.",
         COLOR_GREEN_MAIN, RGBColor(236, 253, 245)),
        ("⏱️ Realtime", "Rekapitulasi Laporan Medis", 
         "Dari 2-3 hari proses rekap manual di akhir bulan menjadi 1 detik. Seluruh laporan kunjungan, diagnosis, dan evaluasi terapis dapat diunduh instan.",
         RGBColor(99, 102, 241), RGBColor(238, 242, 255))
    ]

    m_w = Inches(2.75)
    m_h = Inches(4.6)
    m_lefts = [Inches(0.8), Inches(3.8), Inches(6.8), Inches(9.8)]

    for idx, (stat, title, desc, accent_c, bg_c) in enumerate(metrics):
        card = s4.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, m_lefts[idx], Inches(2.1), m_w, m_h)
        card.fill.solid()
        card.fill.fore_color.rgb = bg_c
        card.line.color.rgb = accent_c
        card.line.width = Pt(1.5)

        ctf = card.text_frame
        ctf.word_wrap = True
        ctf.margin_left = ctf.margin_right = Inches(0.2)
        ctf.margin_top = Inches(0.3)

        sp = ctf.paragraphs[0]
        sp.text = stat
        sp.font.size = Pt(28)
        sp.font.bold = True
        sp.font.color.rgb = accent_c
        sp.alignment = PP_ALIGN.CENTER

        tp = ctf.add_paragraph()
        tp.text = title
        tp.font.size = Pt(13)
        tp.font.bold = True
        tp.font.color.rgb = COLOR_TEXT_DARK
        tp.space_before = Pt(14)
        tp.alignment = PP_ALIGN.CENTER

        dp = ctf.add_paragraph()
        dp.text = desc
        dp.font.size = Pt(10.5)
        dp.font.color.rgb = COLOR_TEXT_MUTED
        dp.space_before = Pt(14)
        dp.alignment = PP_ALIGN.LEFT

    # =========================================================================
    # SLIDE 5: STRUKTUR & FUNGSI MENU (BAGIAN 1: OPERASIONAL & KLINIS)
    # =========================================================================
    s5 = prs.slides.add_slide(blank_layout)
    set_slide_background(s5, COLOR_BG_LIGHT)
    add_header(s5, "Daftar Menu & Fitur Lengkap", "Modul Operasional & Penanganan Klinis Pasien",
               "Sistem dibagi menjadi modul kerja yang terstruktur rapi sesuai tugas masing-masing staf klinik.")

    menus_part1 = [
        ("1. Dashboard Klinik", 
         "Pusat kendali visual harian pimpinan klinik.\n• Ringkasan pasien hari ini, sesi aktif, dan antrean kasir\n• Statistik utilisasi fisioterapis & kapasitas ruangan\n• Grafik tren pemulihan pasien dan omzet berjalan",
         COLOR_BLUE_MAIN),
        ("2. Buat Reservasi & Antrian", 
         "Pendaftaran pasien baru maupun pasien lama (auto-search by HP).\n• Pemilihan terapis penanggung jawab & slot waktu akurat\n• Penentuan nomor antrean klinik dan cetak tiket admisi\n• Tombol 1-klik kirim jadwal langsung ke WhatsApp pasien",
         COLOR_TEAL_MAIN),
        ("3. Manajemen Reservasi Terapi", 
         "Monitoring jadwal harian dan status kedatangan pasien.\n• Status: Menunggu Konfirmasi, Dikonfirmasi, Selesai, Dibatalkan\n• Filter berdasarkan tanggal kunjungan dan nama fisioterapis\n• Fitur pembatalan dengan pencatatan alasan resmi",
         COLOR_AMBER_MAIN),
        ("4. Sesi Terapi Pasien (Clinical Engine)", 
         "Lembar kerja digital fisioterapis saat menangani pasien.\n• Protokol 4 Jenjang (Tahap A Akut s/d Tahap D Return to Sport)\n• Mendukung >1 sesi terapi dalam 1 hari untuk atlet intensif\n• Evaluasi klinis per sesi & tombol 'Advance Stage' (Naik Tahap)",
         RGBColor(124, 58, 237))
    ]

    card_w = Inches(5.6)
    card_h = Inches(2.25)
    coords5 = [
        (Inches(0.8), Inches(2.1)),
        (Inches(6.8), Inches(2.1)),
        (Inches(0.8), Inches(4.65)),
        (Inches(6.8), Inches(4.65)),
    ]

    for idx, (title, desc, color) in enumerate(menus_part1):
        x, y = coords5[idx]
        card = s5.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, x, y, card_w, card_h)
        card.fill.solid()
        card.fill.fore_color.rgb = COLOR_WHITE
        card.line.color.rgb = color
        card.line.width = Pt(1.5)

        ctf = card.text_frame
        ctf.word_wrap = True
        ctf.margin_left = ctf.margin_right = Inches(0.25)
        ctf.margin_top = Inches(0.18)

        p = ctf.paragraphs[0]
        p.text = title
        p.font.size = Pt(13)
        p.font.bold = True
        p.font.color.rgb = color

        p2 = ctf.add_paragraph()
        p2.text = desc
        p2.font.size = Pt(10.5)
        p2.font.color.rgb = COLOR_TEXT_DARK
        p2.space_before = Pt(6)

    # =========================================================================
    # SLIDE 6: STRUKTUR & FUNGSI MENU (BAGIAN 2: KASIR, LOGISTIK & MASTER DATA)
    # =========================================================================
    s6 = prs.slides.add_slide(blank_layout)
    set_slide_background(s6, COLOR_BG_LIGHT)
    add_header(s6, "Daftar Menu & Fitur Lengkap", "Modul Kasir, Logistik Modalitas & Manajemen Sistem",
               "Memastikan akurasi finansial, ketersediaan modalitas terapi, serta pengelolaan data klinik yang aman.")

    menus_part2 = [
        ("5. Kasir & Pembayaran Terpadu", 
         "Pusat billing pembayaran kasir otomatis.\n• 1 Tagihan kasir merangkum seluruh sesi terapi per reservasi\n• Rincian tarif sesi terapi + obat / alat bantu tambahan\n• Status bayar (Lunas / Belum) & cetak struk kuitansi resmi",
         COLOR_GREEN_MAIN),
        ("6. Stok Peralatan Terapi", 
         "Monitoring inventaris peralatan dan modalitas fisioterapi.\n• Kinesio taping, TENS pad, resist band, dry needle, ice pack\n• Pencatatan keluar-masuk barang per gudang klinik\n• Peringatan dini (alert) ketika stok mencapai batas minimum",
         COLOR_TEAL_MAIN),
        ("7. Master Data Terintegrasi", 
         "Pusat basis data terstruktur klinik.\n• Master Pasien: Biodata lengkap, pekerjaan, kontak darurat\n• Master Terapis: Spesialisasi, kontak, dan status aktif\n• Master Jenjang Terapi: Katalog Tahap A-D, durasi & tarif",
         COLOR_BLUE_MAIN),
        ("8. Rekap Rekam Terapi & Settings", 
         "Laporan analitik eksekutif dan keamanan sistem.\n• Rekap riwayat rekam medis pasien jangka panjang\n• Audit kunjungan pasien & efektivitas protokol terapi\n• Manajemen akun staf klinik dengan Role-Based Access Control",
         COLOR_TEXT_DARK)
    ]

    for idx, (title, desc, color) in enumerate(menus_part2):
        x, y = coords5[idx]
        card = s6.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, x, y, card_w, card_h)
        card.fill.solid()
        card.fill.fore_color.rgb = COLOR_WHITE
        card.line.color.rgb = color
        card.line.width = Pt(1.5)

        ctf = card.text_frame
        ctf.word_wrap = True
        ctf.margin_left = ctf.margin_right = Inches(0.25)
        ctf.margin_top = Inches(0.18)

        p = ctf.paragraphs[0]
        p.text = title
        p.font.size = Pt(13)
        p.font.bold = True
        p.font.color.rgb = color

        p2 = ctf.add_paragraph()
        p2.text = desc
        p2.font.size = Pt(10.5)
        p2.font.color.rgb = COLOR_TEXT_DARK
        p2.space_before = Pt(6)

    # =========================================================================
    # SLIDE 7: ALUR PROSES PELAYANAN KLINIS (END-TO-END WORKFLOW)
    # =========================================================================
    s7 = prs.slides.add_slide(blank_layout)
    set_slide_background(s7, COLOR_BG_LIGHT)
    add_header(s7, "Alur Kerja Standar (SOP)", "Alur Proses Pelayanan Klinik dari Awal Hingga Sembuh",
               "Bagaimana sistem menghubungkan admisi pendaftaran, tindakan fisioterapis, kasir, dan arsip rekam medis secara mulus.")

    steps = [
        ("01", "Pendaftaran & Booking", 
         "Pasien booking online atau via staf admisi. Jadwal terapis dan jam terkunci rapi di sistem.",
         COLOR_BLUE_MAIN),
        ("02", "Otomasi WhatsApp", 
         "Staf klik tombol kirim tiket. Pasien menerima pesan WhatsApp pengingat jadwal tanpa simpan kontak.",
         COLOR_TEAL_MAIN),
        ("03", "Sesi Terapi Berjenjang", 
         "Terapis memanggil antrean, melakukan tindakan (Tahap A-D), dan menginput hasil evaluasi fisik.",
         RGBColor(124, 58, 237)),
        ("04", "Billing Kasir Instan", 
         "Kasir menerima data tagihan otomatis, menerima pembayaran, dan langsung mencetak struk resmi.",
         COLOR_AMBER_MAIN),
        ("05", "Arsip Rekam & Lanjutan", 
         "Data tersimpan di riwayat medis. Pasien dapat langsung dijadwalkan untuk tahap berikutnya.",
         COLOR_GREEN_MAIN)
    ]

    box_w = Inches(2.15)
    box_h = Inches(4.5)
    spacing = Inches(0.24)
    start_x = Inches(0.8)

    for idx, (num, title, desc, color) in enumerate(steps):
        bx = start_x + idx * (box_w + spacing)
        card = s7.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, bx, Inches(2.1), box_w, box_h)
        card.fill.solid()
        card.fill.fore_color.rgb = COLOR_WHITE
        card.line.color.rgb = color
        card.line.width = Pt(1.5)

        ctf = card.text_frame
        ctf.word_wrap = True
        ctf.margin_left = ctf.margin_right = Inches(0.18)
        ctf.margin_top = Inches(0.25)

        p1 = ctf.paragraphs[0]
        p1.text = num
        p1.font.size = Pt(26)
        p1.font.bold = True
        p1.font.color.rgb = color
        p1.alignment = PP_ALIGN.CENTER

        p2 = ctf.add_paragraph()
        p2.text = title
        p2.font.size = Pt(12)
        p2.font.bold = True
        p2.font.color.rgb = COLOR_TEXT_DARK
        p2.space_before = Pt(10)
        p2.alignment = PP_ALIGN.CENTER

        p3 = ctf.add_paragraph()
        p3.text = desc
        p3.font.size = Pt(10)
        p3.font.color.rgb = COLOR_TEXT_MUTED
        p3.space_before = Pt(12)
        p3.alignment = PP_ALIGN.LEFT

    # =========================================================================
    # SLIDE 8: KEUNGGULAN PROTOKOL TERAPI BERJENJANG (TAHAP A - D)
    # =========================================================================
    s8 = prs.slides.add_slide(blank_layout)
    set_slide_background(s8, COLOR_BG_LIGHT)
    add_header(s8, "Keunggulan Klinis Khusus", "Protokol Terapi Berjenjang: Dirancang Khusus Cedera Fisik & Olahraga",
               "Berbeda dari software rumah sakit biasa yang kaku, sistem ini memiliki alur tahapan pemulihan terukur sesuai kaidah fisioterapi.")

    stages = [
        ("Tahap A (Akut & Anti-Nyeri)", 
         "Fokus: Penanganan Cedera Dini", 
         "• Reduksi inflamasi akut & peredaan nyeri\n• Modalitas: Cryotherapy (kompres es), resting, kompresi\n• Evaluasi skor nyeri (VAS) dan batas toleransi gerakan",
         RGBColor(239, 68, 68), RGBColor(254, 242, 242)),
        ("Tahap B (Mobilisasi & ROM)", 
         "Fokus: Pemulihan Gerak Sendi", 
         "• Mengembalikan Range of Motion (ROM)\n• Stimulasi jaringan lunak, peregangan pasif, TENS\n• Pencegahan kekakuan sendi dan atrofi otot pasca cedera",
         RGBColor(245, 158, 11), RGBColor(254, 243, 199)),
        ("Tahap C (Penguatan & Fungsional)", 
         "Fokus: Rekonstruksi Kekuatan", 
         "• Latihan beban bertahap (resistance band, beban ringan)\n• Penguatan otot penopang & stabilitas inti (core)\n• Latihan fungsional untuk menunjang aktivitas harian",
         COLOR_BLUE_MAIN, COLOR_BLUE_LIGHT),
        ("Tahap D (Return to Sport)", 
         "Fokus: Kesiapan Berolahraga Kembali", 
         "• Latihan kelincahan, daya ledak, dan ketahanan spesifik\n• Uji kesiapan fungsional atlet sebelum turun ke lapangan\n• Sertifikasi kesembuhan dan panduan pencegahan cedera ulang",
         COLOR_GREEN_MAIN, RGBColor(236, 253, 245))
    ]

    sw = Inches(5.6)
    sh = Inches(2.25)
    for idx, (title, sub, desc, border_c, bg_c) in enumerate(stages):
        x, y = coords5[idx]
        card = s8.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, x, y, sw, sh)
        card.fill.solid()
        card.fill.fore_color.rgb = bg_c
        card.line.color.rgb = border_c
        card.line.width = Pt(1.5)

        ctf = card.text_frame
        ctf.word_wrap = True
        ctf.margin_left = ctf.margin_right = Inches(0.25)
        ctf.margin_top = Inches(0.18)

        p = ctf.paragraphs[0]
        p.text = title
        p.font.size = Pt(13)
        p.font.bold = True
        p.font.color.rgb = border_c

        p_sub = ctf.add_paragraph()
        p_sub.text = sub
        p_sub.font.size = Pt(10.5)
        p_sub.font.bold = True
        p_sub.font.color.rgb = COLOR_TEXT_DARK
        p_sub.space_before = Pt(2)

        p2 = ctf.add_paragraph()
        p2.text = desc
        p2.font.size = Pt(10)
        p2.font.color.rgb = COLOR_TEXT_DARK
        p2.space_before = Pt(6)

    # =========================================================================
    # SLIDE 9: GRATIS 14 HARI & BONUS 2 BULAN WEBSITE CORPSHOW
    # =========================================================================
    s9 = prs.slides.add_slide(blank_layout)
    set_slide_background(s9, COLOR_BG_LIGHT)
    add_header(s9, "Penawaran Spesial & Bonus Eksklusif", "Program Uji Coba Gratis 14 Hari + BONUS 2 Bulan Website di CorpShow",
               "Dapatkan solusi terlengkap: operasional klinik rapi di dalam, dan website profil profesional di luar untuk memikat pasien baru.")

    trial_cards = [
        ("🎁 14 Hari SportClinic.io", 
         "Akses penuh tanpa batasan ke modul Reservasi WA, Antrean, Lembar Terapi Berjenjang (A-D), Kasir, dan Rekam Medis.",
         COLOR_BLUE_MAIN, COLOR_BLUE_LIGHT),
        ("🌐 BONUS: 2 Bulan CorpShow", 
         "Website Company Profile klinik profesional di platform CorpShow (corpshow.id). Kredibel di Google & terhubung langsung ke booking online!",
         COLOR_GREEN_MAIN, RGBColor(236, 253, 245)),
        ("💳 Tanpa Kartu Kredit", 
         "Registrasi instan dalam 2 menit. Anda tidak perlu memasukkan informasi kartu kredit maupun deposit biaya apa pun.",
         COLOR_TEAL_MAIN, COLOR_TEAL_LIGHT),
        ("📂 Gratis Migrasi Excel", 
         "Punya data master pasien di Excel? Tim teknis kami siap mendampingi proses import database ke sistem secara gratis.",
         RGBColor(124, 58, 237), RGBColor(245, 243, 255))
    ]

    c4_w = Inches(2.75)
    c4_h = Inches(2.6)
    c4_lefts = [Inches(0.8), Inches(3.8), Inches(6.8), Inches(9.8)]

    for idx, (title, desc, border_c, bg_c) in enumerate(trial_cards):
        card = s9.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, c4_lefts[idx], Inches(2.1), c4_w, c4_h)
        card.fill.solid()
        card.fill.fore_color.rgb = bg_c
        card.line.color.rgb = border_c
        card.line.width = Pt(1.5)

        ctf = card.text_frame
        ctf.word_wrap = True
        ctf.margin_left = ctf.margin_right = Inches(0.2)
        ctf.margin_top = Inches(0.22)

        p = ctf.paragraphs[0]
        p.text = title
        p.font.size = Pt(13)
        p.font.bold = True
        p.font.color.rgb = border_c

        p2 = ctf.add_paragraph()
        p2.text = desc
        p2.font.size = Pt(10.5)
        p2.font.color.rgb = COLOR_TEXT_DARK
        p2.space_before = Pt(8)

    # Big Banner Callout below
    banner = s9.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(5.0), Inches(11.7), Inches(1.8))
    banner.fill.solid()
    banner.fill.fore_color.rgb = COLOR_NAVY_DARK
    banner.line.color.rgb = COLOR_BLUE_MAIN
    banner.line.width = Pt(2)

    btf = banner.text_frame
    btf.word_wrap = True
    btf.margin_left = btf.margin_right = Inches(0.4)
    btf.margin_top = Inches(0.22)

    bp1 = btf.paragraphs[0]
    bp1.text = "Kombinasi Sempurna: Operasional Rapi di Dalam + Branding Profesional di Luar!"
    bp1.font.size = Pt(16)
    bp1.font.bold = True
    bp1.font.color.rgb = COLOR_WHITE
    bp1.alignment = PP_ALIGN.CENTER

    bp2 = btf.add_paragraph()
    bp2.text = "Sistem SportClinic (Trial 14 Hari)  +  Website Company Profile CorpShow (Gratis 2 Bulan)"
    bp2.font.size = Pt(13.5)
    bp2.font.bold = True
    bp2.font.color.rgb = COLOR_GREEN_MAIN
    bp2.space_before = Pt(6)
    bp2.alignment = PP_ALIGN.CENTER

    bp3 = btf.add_paragraph()
    bp3.text = "Akses Website & Klaim Penawaran: https://sport-therapist.corpshow.id  •  Tim Siap Membantu Setup Lengkap"
    bp3.font.size = Pt(11)
    bp3.font.color.rgb = RGBColor(148, 163, 184)
    bp3.space_before = Pt(6)
    bp3.alignment = PP_ALIGN.CENTER

    # =========================================================================
    # SLIDE 10: PENUTUP & KESIMPULAN
    # =========================================================================
    s10 = prs.slides.add_slide(blank_layout)
    set_slide_background(s10, COLOR_NAVY_DARK)

    cbox = s10.shapes.add_textbox(Inches(1.5), Inches(1.3), Inches(10.3), Inches(5.0))
    ctf10 = cbox.text_frame
    ctf10.word_wrap = True

    p = ctf10.paragraphs[0]
    p.text = "KESIMPULAN & LANGKAH SELANJUTNYA"
    p.font.size = Pt(12)
    p.font.bold = True
    p.font.color.rgb = RGBColor(96, 165, 250)
    p.alignment = PP_ALIGN.CENTER

    p2 = ctf10.add_paragraph()
    p2.text = "Tinggalkan Cara Lama yang Melelahkan.\nBeralihlah ke Solusi Cerdas SportClinic.io"
    p2.font.size = Pt(28)
    p2.font.bold = True
    p2.font.color.rgb = COLOR_WHITE
    p2.space_before = Pt(12)
    p2.alignment = PP_ALIGN.CENTER

    p3 = ctf10.add_paragraph()
    p3.text = "Klinik Lebih Rapi  •  Terapis Lebih Produktif  •  Pasien Lebih Puas  •  Pendapatan Lebih Terukur"
    p3.font.size = Pt(14)
    p3.font.bold = True
    p3.font.color.rgb = COLOR_TEAL_MAIN
    p3.space_before = Pt(16)
    p3.alignment = PP_ALIGN.CENTER

    p4 = ctf10.add_paragraph()
    p4.text = "🎁 Paket Penawaran Spesial:\nGratis Uji Coba 14 Hari Sistem + Gratis 2 Bulan Website Company Profile di CorpShow!"
    p4.font.size = Pt(14.5)
    p4.font.bold = True
    p4.font.color.rgb = COLOR_GREEN_MAIN
    p4.space_before = Pt(18)
    p4.alignment = PP_ALIGN.CENTER

    p5 = ctf10.add_paragraph()
    p5.text = "Daftar & Hubungi Kami Sekarang di:\n👉 https://sport-therapist.corpshow.id 👈"
    p5.font.size = Pt(16)
    p5.font.bold = True
    p5.font.color.rgb = COLOR_AMBER_MAIN
    p5.space_before = Pt(14)
    p5.alignment = PP_ALIGN.CENTER

    # Save presentation
    prs.save(filename)
    print(f"Presentation successfully created at: {filename}")

if __name__ == "__main__":
    out_file = sys.argv[1] if len(sys.argv) > 1 else "SportClinic_Presentation.pptx"
    create_deck(out_file)

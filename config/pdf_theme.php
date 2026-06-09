<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PDF Theme — Dashboard Export
    |--------------------------------------------------------------------------
    | Ubah nilai di sini untuk mengubah tampilan hasil export PDF dashboard.
    | Tidak perlu menyentuh view atau controller.
    |--------------------------------------------------------------------------
    */

    /* ── Identitas Laporan ─────────────────────────────────────────── */
    'farm_name' => 'Gumolong Farm',
    'report_title' => 'Laporan Statistik Dashboard Peternakan',
    'badge_text' => 'LAPORAN RESMI',

    /* ── Kertas ────────────────────────────────────────────────────── */
    'paper_size' => 'a4',        // a4 | letter | legal
    'paper_orientation' => 'portrait',  // portrait | landscape

    /* ── Warna Utama ───────────────────────────────────────────────── */
    'color_primary' => '#2E7D32',  // hijau utama — header, aksen, badge
    'color_primary_dark' => '#166534',  // hijau gelap — teks catatan interpretasi
    'color_primary_light' => '#F0FDF4',  // hijau sangat terang — background catatan
    'color_primary_border' => '#BBF7D0',  // border catatan interpretasi

    /* ── Warna Status ──────────────────────────────────────────────── */
    'color_danger' => '#B14B6F',  // merah — kematian, FCR buruk, kandang >90%
    'color_warning' => '#D97706',  // kuning — FCR sedang, kandang 70–89%
    'color_info' => '#3B82F6',  // biru — jumlah pejantan
    'color_earthy' => '#607F5B',  // hijau earthy — jumlah betina

    /* ── Warna Teks ────────────────────────────────────────────────── */
    'color_text' => '#1F2937',  // teks utama
    'color_text_muted' => '#6B7280',  // teks sekunder / meta
    'color_text_light' => '#9CA3AF',  // teks sangat ringan / footer

    /* ── Warna Tabel & Background ──────────────────────────────────── */
    'color_border' => '#E5E7EB',  // border umum
    'color_bg_alt' => '#F9FAFB',  // baris tabel genap (zebra)
    'color_bg_row_hover' => '#F3F4F6',  // baris total / hover
    'color_table_text' => '#374151',  // teks isi tabel

    /* ── Warna Kategori Domba (Progress Bar Distribusi) ────────────── */
    'kategori_colors' => [
        'cempe' => '#F59E0B',
        'dara' => '#607F5B',
        'indukan' => '#2E7D32',
        'pejantan' => '#3B82F6',
    ],

    /* ── Tipografi ─────────────────────────────────────────────────── */
    'font_family' => 'sans-serif',  // sans-serif | serif (DejaVu via DomPDF)
    'font_size_base' => '10px',
    'font_size_small' => '9px',
    'font_size_label' => '8.5px',
    'font_size_badge' => '8px',
    'font_size_footer' => '8px',
    'font_size_table' => '9.5px',
    'font_size_farmname' => '17px',
    'font_size_section' => '10px',
    'font_size_card_val' => '22px',

    /* ── Jarak & Padding ───────────────────────────────────────────── */
    'body_padding' => '30px 36px',
    'section_spacing' => '18px',       // jarak atas setiap section title

    /* ── Threshold Otomatis ────────────────────────────────────────── */
    // FCR: di bawah fcr_good = hijau, fcr_good–fcr_bad = kuning, di atas = merah
    'fcr_good' => 6,
    'fcr_bad' => 9,

    // Kandang: di bawah warning = hijau, warning–danger = kuning, di atas = merah
    'occupancy_warning' => 70,
    'occupancy_danger' => 90,

    /* ── Konten Opsional ───────────────────────────────────────────── */
    'show_interpretation_notes' => true,   // tampilkan / sembunyikan catatan interpretasi
    'show_farm_logo' => true,   // tampilkan logo di header PDF

];

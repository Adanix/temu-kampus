<?php

return [
    'meta_title' => 'Panduan Fitur & Dokumentasi Produk — TemuKampus',
    'title' => 'Panduan Produk & Alur Kerja TemuKampus',
    'subtitle' => 'Dokumentasi lengkap seluruh modul terimplementasi, pembagian peran, formula pencocokan cerdas, dan cakupan simulasi pada lingkungan demo.',
    'toc_title' => 'Daftar Isi',
    'sections' => [
        'overview' => [
            'title' => '1. Gambaran Umum Produk & Tujuan Demo',
            'body' => 'TemuKampus adalah sistem terpusat manajemen barang hilang dan ditemukan untuk lingkungan perguruan tinggi. Aplikasi ini menggantikan pengumuman tersebar di grup percakapan dengan katalog berbasis lokasi gedung terstruktur, pelindung privasi otomatis untuk kartu identitas, mekanisme anti-penipuan Blind Challenge, pencocokan cerdas berbasis bobot, dan serah terima fisik terverifikasi PIN 6-digit.',
        ],
        'personas' => [
            'title' => '2. Memulai Cepat & Panduan 4 Peran Kampus',
            'intro' => 'Anda dapat masuk ke seluruh peran secara instan melalui halaman Demo Login tanpa kata sandi. Setiap peran dilindungi otorisasi sisi server (Laravel Policy dan Gate).',
        ],
        'modules' => [
            'title' => '3. Panduan Lengkap Modul Terimplementasi',
            'items' => [
                ['name' => 'Katalog Pencarian Utama (/items)', 'desc' => 'Menyaring laporan berdasarkan kata kunci, tipe (Hilang/Temuan), kategori, gedung kampus, dan status dengan paginasi 12 item per halaman.'],
                ['name' => 'Pelaporan, Ubah, Batal & Arsip (/items/create)', 'desc' => 'Mendukung unggah foto (maks. 3MB), pencatatan ciri rahasia khusus temuan, serta pemilihan posisi penitipan barang.'],
                ['name' => 'Perlindungan Privasi Dokumen Sensitif', 'desc' => 'Setiap laporan berkategori Kartu Identitas & Dokumen otomatis disamarkan fotonya di katalog publik. Hanya pelapor dan petugas pos yang memiliki tombol buka pratinjau.'],
                ['name' => 'Direktori Pos Penitipan (/custody-posts)', 'desc' => 'Menampilkan lokasi pos satpam atau tata usaha resmi, jam operasional, ekstensi kontak, dan jumlah barang aktif yang dititipkan.'],
                ['name' => 'Smart Matching Otomatis (/matches)', 'desc' => 'Menghitung skor kemiripan secara sinkron tiap kali laporan baru atau perubahan disimpan, lalu menampilkan perbandingan berdampingan untuk skor >= 60%.'],
                ['name' => 'Klaim Blind Challenge & PIN 6-Digit (/claims & /custodian/desk)', 'desc' => 'Pengklaim menjawab ciri rahasia tanpa melihat petunjuk penemu. Petugas pos membandingkan secara berdampingan, menyetujui klaim secara atomik (menolak klaim pesaing otomatis), dan menerbitkan PIN 6-digit.'],
                ['name' => 'Analitik Kampus, Ekspor CSV & Master Lokasi (/admin/analytics & /admin/master)', 'desc' => 'Menghitung Tingkat Pemulihan (%), rata-rata waktu kembali (jam), 5 gedung teratas rawan kehilangan, ekspor CSV aman tanpa membocorkan ciri rahasia, dan CRUD zona/pos.'],
            ],
        ],
        'workflows' => [
            'title' => '4. Alur Kerja Utama & Data PIN Uji Siap Pakai',
            'steps' => [
                'Masuk sebagai Mahasiswa (Raka Pratama) dan lihat Dasbor: Anda langsung melihat dua klaim yang sudah disetujui dengan PIN aktif.',
                'Untuk uji serah terima di Meja Petugas Pos: masuk sebagai Petugas Pos (Bapak Hendra Kusuma), buka /custodian/desk, lalu masukkan PIN 482910 untuk klaim CLM-2026-0003 (Headphone Sony di Perpustakaan).',
                'Untuk uji serah terima oleh Penemu Langsung (with_finder): masuk sebagai Dosen & Staf (Dr. Ratna Wijaya), buka detail tiket TK-2026-0018 (Buku Mikroekonomi), lalu masukkan PIN 739204.',
                'Untuk uji peninjauan klaim Blind Challenge baru: di Meja Petugas Pos, tinjau CLM-2026-0001 atau CLM-2026-0002 untuk menyetujui dan menghasilkan PIN 6-digit baru.',
            ],
            'pin_callout_title' => 'PIN Pengujian Cepat yang Sudah Tersedia (Data Sampel)',
            'pin_callout_body' => 'Gunakan PIN 482910 (untuk Petugas Pos pada CLM-2026-0003 / TK-2026-0017) atau PIN 739204 (untuk Dr. Ratna Wijaya pada CLM-2026-0004 / TK-2026-0018). Format dengan strip (482-910) maupun angka polos (482910) sama-sama diterima.',
        ],
        'matching' => [
            'title' => '5. Siklus Status & Formula Smart Matching (35 + 25 + 25 + 15)',
            'formula_intro' => 'Skor maksimum adalah 100 poin. Pasangan dengan skor minimal 60 otomatis disimpan ke item_matches:',
            'weights' => [
                'Kategori Sama Persis: 35 poin (jika kategori berbeda, pasangan langsung dilewati).',
                'Kedekatan Zona Kampus: 25 poin jika gedung sama persis, 12 poin jika berada dalam klaster zona yang sama (misalnya sesama Umum), 0 poin jika berbeda.',
                'Kemiripan Kata Kunci, Warna & Merek: hingga 25 poin menggunakan Koefisien Overlap pada token judul, deskripsi, warna, dan merek setelah menghapus kata umum Indonesia & Inggris.',
                'Kedekatan Tanggal Kejadian: 15 poin (selisih <= 2 hari), 10 poin (<= 7 hari), 5 poin (<= 14 hari), 0 poin (> 14 hari).',
            ],
        ],
        'simulations' => [
            'title' => '6. Mode Demo, Layanan Tersimulasi & Batasan Saat Ini',
            'items' => [
                'Notifikasi Email & Pengingat Kampus: Dikirim secara tersimulasi ke tabel lokal notification_logs (terlihat di /notifications) tanpa server SMTP/WhatsApp eksternal.',
                'Otentikasi SSO Kampus: Digantikan oleh pemilihan 4 peran terotorisasi di /demo/login (membuat sesi Laravel asli dengan proteksi CSRF dan rate limit). Mode demo dapat dimatikan via APP_DEMO_MODE=false (rute demo otomatis mengembalikan HTTP 404).',
                'Peta Gedung Kampus: Menggunakan taksonomi gedung dan klaster zona terstruktur tanpa pemanggilan API peta berbayar.',
                'Sensor Foto Sensitif & Arsip 90 Hari: Diterapkan pada lapisan tampilan & Policy di demo ini; versi produksi dapat menambahkan redaksi gambar sisi server serta penjadwal otomatis harian.',
            ],
        ],
        'customization' => [
            'title' => '7. Butuh sistem yang disesuaikan dengan kampus Anda?',
            'body' => 'Demo ini menampilkan fitur dan alur kerja TemuKampus saat ini. Aplikasi web ini dapat dikembangkan lebih lanjut, diperluas, dan dikustomisasi sesuai proses bisnis serta kebutuhan spesifik institusi Anda (integrasi SSO kampus, notifikasi WhatsApp/Email asli, hingga penyesuaian antarmuka dan laporan).',
            'contact_label' => 'Kontak Kustomisasi:',
        ],
    ],
];

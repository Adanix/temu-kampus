<?php

return [
    'meta_title' => 'TemuKampus — Sistem Manajemen Barang Hilang & Ditemukan Kampus',
    'hero' => [
        'title' => 'Temukan barang hilang di kampus tanpa panik dan tanpa bocor privasi.',
        'subtitle' => 'Satu direktori terpusat untuk melaporkan barang hilang atau temuan di gedung kampus, mencocokkan laporan secara otomatis, dan menyerahkan barang dengan PIN 6-digit serta sensor otomatis dokumen sensitif.',
        'cta_primary' => 'Masuk ke Demo',
        'cta_secondary' => 'Baca Panduan Fitur',
        'trust_items' => [
            '4 Peran Kampus',
            'PIN Serah Terima 6-Digit',
            'Sensor KTM Otomatis',
        ],
    ],
    'preview' => [
        'ticket_label' => 'Tiket Terverifikasi',
        'match_badge' => '88% COCOK',
        'sample_title' => 'Dompet Kulit Cokelat + KTM Fakultas Teknik',
        'zone_label' => 'PERPUS-01 · Perpustakaan Pusat Lt. 3',
        'privacy_shield' => 'Dokumen Sensitif Disamarkan di Katalog Publik',
        'custody_label' => 'Pos Satpam Gerbang Utama · 24 Jam',
        'challenge_badge' => 'Blind Challenge Aktif',
    ],
    'capabilities' => [
        'heading' => 'Dirancang untuk alur kerja kampus yang nyata',
        'subheading' => 'Menggantikan pesan berantai yang tenggelam dengan verifikasi kepemilikan yang terstruktur dan aman.',
        'items' => [
            [
                'number' => '01',
                'title' => 'Verifikasi Blind Challenge',
                'description' => 'Pelapor temuan mencatat ciri rahasia yang tidak tampil ke publik. Pengklaim wajib menjawab tantangan ciri tersebut sebelum petugas pos menyetujui klaim.',
            ],
            [
                'number' => '02',
                'title' => 'Sensor Otomatis Dokumen Sensitif',
                'description' => 'Laporan kategori KTM, KTP, atau SIM otomatis diburamkan di katalog publik. Pratinjau jelas hanya dapat dibuka oleh pelapor dan petugas berwenang.',
            ],
            [
                'number' => '03',
                'title' => 'Smart Matching Berbasis Skor Transparan',
                'description' => 'Mesin pencocokan menghitung bobot Kategori (35%), Gedung/Zona (25%), Kata Kunci & Merek (25%), serta Kedekatan Tanggal (15%) tanpa kotak hitam.',
            ],
            [
                'number' => '04',
                'title' => 'Serah Terima Terverifikasi PIN 6-Digit',
                'description' => 'Setiap klaim yang disetujui menghasilkan PIN 6-digit unik bagi pemilik. Petugas memvalidasi PIN saat barang diambil agar status tertutup secara atomik.',
            ],
        ],
    ],
    'workflow' => [
        'heading' => 'Alur pemulihan barang dalam 4 langkah terukur',
        'steps' => [
            [
                'step' => '01',
                'title' => 'Lapor Terstruktur',
                'description' => 'Catat barang hilang atau temuan dengan gedung kampus, tanggal, dan ciri rahasia jika temuan.',
            ],
            [
                'step' => '02',
                'title' => 'Cocokkan Otomatis',
                'description' => 'Sistem menghitung skor kecocokan terhadap laporan lawan yang masih terbuka.',
            ],
            [
                'step' => '03',
                'title' => 'Verifikasi Blind Challenge',
                'description' => 'Petugas membandingkan jawaban pengklaim dengan ciri rahasia penemu secara berdampingan.',
            ],
            [
                'step' => '04',
                'title' => 'Serah Terima PIN',
                'description' => 'Pemilik menunjukkan PIN 6-digit di pos penitipan untuk menutup laporan menjadi Dikembalikan.',
            ],
        ],
    ],
    'roles' => [
        'heading' => 'Empat peran terpisah sesuai otoritas kampus',
        'subheading' => 'Coba seluruh peran secara langsung melalui halaman masuk demo tanpa mengetik kata sandi.',
        'cta' => 'Coba Peran di Demo',
        'items' => [
            [
                'role' => 'Mahasiswa',
                'persona' => 'Raka Pratama · Fakultas Teknik',
                'scope' => 'Melaporkan barang hilang, memantau kecocokan otomatis, mengajukan klaim Blind Challenge, dan melihat PIN pengambilan.',
            ],
            [
                'role' => 'Dosen & Staf',
                'persona' => 'Dr. Ratna Wijaya · Fakultas Ekonomi & Bisnis',
                'scope' => 'Melaporkan temuan di ruang kuliah dengan ciri rahasia, memilih pos titipan atau pegang penemu, dan verifikasi PIN.',
            ],
            [
                'role' => 'Petugas Pos',
                'persona' => 'Bapak Hendra Kusuma · Pos Satpam Gerbang Utama',
                'scope' => 'Meninjau antrean klaim Blind Challenge, membuka foto dokumen sensitif, menerbitkan PIN, dan menyelesaikan serah terima.',
            ],
            [
                'role' => 'Administrator Kampus',
                'persona' => 'Nadia Larasati · Biro Kemahasiswaan & Sarpras',
                'scope' => 'Melihat tingkat pemulihan, rata-rata durasi kembali, zona rawan kehilangan, mengelola gedung/pos, dan mengekspor CSV.',
            ],
        ],
    ],
    'final_cta' => [
        'heading' => 'Jelajahi seluruh siklus pemulihan barang secara langsung',
        'description' => 'Pilih peran kampus untuk menguji laporan baru, pencocokan skor otomatis, peninjauan klaim, hingga verifikasi PIN 6-digit.',
        'primary' => 'Buka Demo Interaktif',
        'secondary' => 'Lihat Dokumentasi Produk',
    ],
    'footer' => [
        'navigation_title' => 'Navigasi',
        'contact_title' => 'Kontak & Kustomisasi',
        'contact_note' => 'Ingin penyesuaian alur kerja atau integrasi SSO untuk institusi Anda?',
    ],
];

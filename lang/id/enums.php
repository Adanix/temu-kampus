<?php

return [
    'role' => [
        'student' => 'Mahasiswa',
        'staff' => 'Dosen & Staf',
        'custodian' => 'Petugas Pos',
        'admin' => 'Administrator Kampus',
    ],
    'item_type' => [
        'lost' => 'Hilang',
        'found' => 'Ditemukan',
    ],
    'category' => [
        'electronics' => 'Elektronik',
        'id_document' => 'Kartu Identitas & Dokumen',
        'wallet_bag' => 'Dompet & Tas',
        'keys' => 'Kunci',
        'books_stationery' => 'Buku & Alat Tulis',
        'accessories' => 'Aksesoris Pribadi',
        'other' => 'Lainnya',
    ],
    'item_status' => [
        'open' => 'Terbuka',
        'claim_approved' => 'Klaim Disetujui',
        'returned' => 'Dikembalikan',
        'unclaimed_archived' => 'Diarsipkan',
        'cancelled' => 'Dibatalkan',
    ],
    'custody_type' => [
        'with_finder' => 'Dipegang Penemu',
        'at_custody_post' => 'Dititipkan di Pos Kampus',
    ],
    'claim_status' => [
        'pending' => 'Menunggu Verifikasi',
        'approved' => 'Disetujui — Siap Diambil',
        'rejected' => 'Ditolak',
        'completed' => 'Serah Terima Selesai',
    ],
    'notification_event' => [
        'match_found' => 'Kecocokan Ditemukan',
        'claim_submitted' => 'Klaim Baru Diajukan',
        'claim_approved' => 'Klaim Disetujui & PIN Siap',
        'claim_rejected' => 'Klaim Ditolak',
        'handover_completed' => 'Serah Terima Selesai',
    ],
];

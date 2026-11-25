<?php

return [
    'enabled' => filter_var(env('APP_DEMO_MODE', true), FILTER_VALIDATE_BOOLEAN),
    'contact_email' => env('DEMO_CONTACT_EMAIL', 'nixresponding@gmail.com'),

    'personas' => [
        'student' => [
            'email' => 'raka.pratama@kampustemu.ac.id',
            'name' => 'Raka Pratama',
            'initials' => 'RP',
            'role' => 'student',
            'campus_unit' => 'Fakultas Teknik — Informatika',
            'student_staff_code' => 'NIM-2023010142',
            'landing_route' => 'dashboard',
        ],
        'staff' => [
            'email' => 'ratna.wijaya@kampustemu.ac.id',
            'name' => 'Dr. Ratna Wijaya',
            'initials' => 'RW',
            'role' => 'staff',
            'campus_unit' => 'Fakultas Ekonomi & Bisnis',
            'student_staff_code' => 'NIDN-0412088501',
            'landing_route' => 'dashboard',
        ],
        'custodian' => [
            'email' => 'hendra.kusuma@kampustemu.ac.id',
            'name' => 'Bapak Hendra Kusuma',
            'initials' => 'HK',
            'role' => 'custodian',
            'campus_unit' => 'Pos Satpam Gerbang Utama',
            'student_staff_code' => 'STF-POS-001',
            'landing_route' => 'custodian.desk',
        ],
        'admin' => [
            'email' => 'nadia.larasati@kampustemu.ac.id',
            'name' => 'Nadia Larasati',
            'initials' => 'NL',
            'role' => 'admin',
            'campus_unit' => 'Biro Kemahasiswaan & Sarpras',
            'student_staff_code' => 'ADM-SARPRAS-01',
            'landing_route' => 'admin.analytics',
        ],
    ],
];

<?php

return [
    'meta_title' => 'Product Documentation & Feature Guide — TemuKampus',
    'title' => 'TemuKampus Product Guide & Workflows',
    'subtitle' => 'Complete documentation of all implemented modules, campus role boundaries, weighted matching scoring, and demo simulation disclosures.',
    'toc_title' => 'Table of Contents',
    'sections' => [
        'overview' => [
            'title' => '1. Product Overview & Demo Purpose',
            'body' => 'TemuKampus is a centralized campus lost and found management platform built for universities and colleges. It replaces fragmented chat announcements with structured building taxonomy, automatic privacy blur for identity cards, Blind Challenge ownership verification, deterministic weighted smart matching, and 6-digit PIN physical handover closure.',
        ],
        'personas' => [
            'title' => '2. Quick Start & 4 Campus Personas',
            'intro' => 'You can explore every campus role immediately from the Demo Login screen without entering passwords. Every boundary is strictly enforced on the server via Laravel Policies and Gates.',
        ],
        'modules' => [
            'title' => '3. Complete Implemented Module Guide',
            'items' => [
                ['name' => 'Search-First Catalog (/items)', 'desc' => 'Filter reports by keyword, type (Lost/Found), category, campus building, and status with 12-item pagination.'],
                ['name' => 'Report, Edit, Cancel & Archive (/items/create)', 'desc' => 'Supports optional photo uploads (max 3MB), hidden traits for found items, and physical custody selection.'],
                ['name' => 'Sensitive Document Privacy Shield', 'desc' => 'Reports categorized as ID Cards & Documents automatically blur previews in public views. Only the reporter, custodians, and admins receive an explicit reveal toggle.'],
                ['name' => 'Custody Posts Directory (/custody-posts)', 'desc' => 'Displays official security or faculty desks, operating hours, extensions, and live active stored item counts.'],
                ['name' => 'Weighted Smart Matching (/matches)', 'desc' => 'Synchronously scores opposite open reports whenever an item is created or updated, surfacing side-by-side comparison cards for scores >= 60%.'],
                ['name' => 'Blind Challenge Claims & 6-Digit PIN (/claims & /custodian/desk)', 'desc' => 'Claimants answer hidden traits blindly. Officers compare hidden traits vs answers side-by-side, atomically approve one claim while auto-rejecting competing claims, and issue a 6-digit pickup PIN.'],
                ['name' => 'Campus Analytics, CSV Export & Campus Master (/admin/analytics & /admin/master)', 'desc' => 'Computes Recovery Rate (%), average recovery hours, top 5 building loss hotspots, safe UTF-8 CSV exports excluding hidden traits, and Zone/Custody Post CRUD.'],
            ],
        ],
        'workflows' => [
            'title' => '4. Main Workflows & Ready-to-Test Sample PINs',
            'steps' => [
                'Sign in as Student (Raka Pratama) and inspect Dashboard: two approved claims with active handover PINs are ready for pickup.',
                'To test Custody Post handover: sign in as Custody Officer (Bapak Hendra Kusuma), open /custodian/desk, and enter PIN 482910 on claim CLM-2026-0003 (Sony Headphones at Central Library).',
                'To test Finder-Held (with_finder) handover: sign in as Faculty & Staff (Dr. Ratna Wijaya), open ticket TK-2026-0018 (Microeconomics Textbook), and enter PIN 739204.',
                'To test reviewing new Blind Challenge claims: on the Custodian Desk, review pending claims CLM-2026-0001 or CLM-2026-0002 to approve and generate a fresh 6-digit PIN.',
            ],
            'pin_callout_title' => 'Pre-Seeded Test Handover PINs',
            'pin_callout_body' => 'Use PIN 482910 (for Custody Officer on CLM-2026-0003 / TK-2026-0017) or PIN 739204 (for Dr. Ratna Wijaya on CLM-2026-0004 / TK-2026-0018). Dashed (482-910) and plain (482910) formats are both accepted.',
        ],
        'matching' => [
            'title' => '5. Status Lifecycle & Weighted Matching Formula (35 + 25 + 25 + 15)',
            'formula_intro' => 'Maximum score is 100 points. Opposite open pairs scoring at least 60 are saved to item_matches:',
            'weights' => [
                'Exact Category Match: 35 points (different categories are immediately skipped).',
                'Campus Zone Proximity: 25 points for exact building match, 12 points for same zone cluster, 0 otherwise.',
                'Keyword, Color & Brand Similarity: up to 25 points using Overlap Coefficient across normalized tokens from title, description, color, and brand after stripping Indonesian and English stopwords.',
                'Date Proximity: 15 points (<= 2 days apart), 10 points (<= 7 days), 5 points (<= 14 days), 0 points (> 14 days).',
            ],
        ],
        'simulations' => [
            'title' => '6. Demo Mode, Simulated Services & Limitations',
            'items' => [
                'Email & Campus Alerts: Written locally to the notification_logs table (viewable at /notifications) with delivery_mode set to simulated instead of live SMTP or WhatsApp.',
                'Campus SSO Authentication: Replaced by an allowlisted 4-role quick-login at /demo/login creating real CSRF-protected Laravel sessions. Setting APP_DEMO_MODE=false returns HTTP 404 on demo auth endpoints.',
                'Campus Building Coordinates: Represented via structured campus zones and clusters without external mapping API billing.',
                'Presentation Blur & Manual Archiving: Enforced at UI + Policy layer for demo simplicity; production deployments can pair private object storage URLs and scheduled 90-day archiving jobs.',
            ],
        ],
        'customization' => [
            'title' => '7. Need something more tailored?',
            'body' => 'This demo showcases the current TemuKampus features and workflows. The web application can be further developed, extended, and customized to fit your campus processes and specific requirements (including campus SSO integration, live Email/WhatsApp notifications, custom branding, and institutional reporting).',
            'contact_label' => 'Contact:',
        ],
    ],
];

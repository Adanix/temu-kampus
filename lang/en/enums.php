<?php

return [
    'role' => [
        'student' => 'Student',
        'staff' => 'Faculty & Staff',
        'custodian' => 'Custody Officer',
        'admin' => 'Campus Administrator',
    ],
    'item_type' => [
        'lost' => 'Lost',
        'found' => 'Found',
    ],
    'category' => [
        'electronics' => 'Electronics',
        'id_document' => 'ID Cards & Documents',
        'wallet_bag' => 'Wallets & Bags',
        'keys' => 'Keys',
        'books_stationery' => 'Books & Stationery',
        'accessories' => 'Personal Accessories',
        'other' => 'Other',
    ],
    'item_status' => [
        'open' => 'Open',
        'claim_approved' => 'Claim Approved',
        'returned' => 'Returned',
        'unclaimed_archived' => 'Archived',
        'cancelled' => 'Cancelled',
    ],
    'custody_type' => [
        'with_finder' => 'Held by Finder',
        'at_custody_post' => 'Deposited at Custody Post',
    ],
    'claim_status' => [
        'pending' => 'Pending Review',
        'approved' => 'Approved — Ready for Pickup',
        'rejected' => 'Rejected',
        'completed' => 'Handover Completed',
    ],
    'notification_event' => [
        'match_found' => 'Match Found',
        'claim_submitted' => 'New Claim Submitted',
        'claim_approved' => 'Claim Approved & PIN Ready',
        'claim_rejected' => 'Claim Rejected',
        'handover_completed' => 'Handover Completed',
    ],
];

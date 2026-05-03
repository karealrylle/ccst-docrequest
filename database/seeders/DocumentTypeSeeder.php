<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        // ── Printable Documents (Same-day issuance, 0 processing days) ──
        $documents = [
            ['code' => 'COE',   'name' => 'Certificate of Enrollment',              'fee' => 100.00, 'processing_days' => 0, 'has_school_year' => false, 'is_printable' => true,  'is_active' => true],
            ['code' => 'TC',    'name' => 'Transfer Credentials',                    'fee' => 100.00, 'processing_days' => 0, 'has_school_year' => false, 'is_printable' => true,  'is_active' => true],
            ['code' => 'CGMC',  'name' => 'Certificate of Good Moral Character',     'fee' => 100.00, 'processing_days' => 0, 'has_school_year' => false, 'is_printable' => true,  'is_active' => true],
            ['code' => 'REG',   'name' => 'Registration Form',                       'fee' => 100.00, 'processing_days' => 0, 'has_school_year' => true,  'is_printable' => true,  'is_active' => true],
            ['code' => 'COG',   'name' => 'Certificate of Grades',                   'fee' => 100.00, 'processing_days' => 0, 'has_school_year' => true,  'is_printable' => true,  'is_active' => true],
            ['code' => 'CCOMP', 'name' => 'Certificate of Completion',               'fee' => 100.00, 'processing_days' => 0, 'has_school_year' => false, 'is_printable' => true,  'is_active' => true],
            ['code' => 'CGRAD', 'name' => 'Certificate of Graduation',               'fee' => 100.00, 'processing_days' => 0, 'has_school_year' => false, 'is_printable' => true,  'is_active' => true],
            ['code' => 'CRANK', 'name' => 'Certificate of Ranking',                  'fee' => 100.00, 'processing_days' => 0, 'has_school_year' => false, 'is_printable' => true,  'is_active' => true],
            ['code' => 'CLID',  'name' => 'Certificate of Lost ID',                  'fee' => 100.00, 'processing_days' => 0, 'has_school_year' => false, 'is_printable' => true,  'is_active' => true],
            ['code' => 'CGWA',  'name' => 'Certificate of GWA (General Weighted Average)', 'fee' => 100.00, 'processing_days' => 0, 'has_school_year' => false, 'is_printable' => true, 'is_active' => true],

            // ── Non-Printable Document (Requires processing) ──
            ['code' => 'F138',  'name' => 'Form 138 (Report Card)',                  'fee' => 100.00, 'processing_days' => 2, 'has_school_year' => true,  'is_printable' => false, 'is_active' => true],
        ];

        foreach ($documents as $doc) {
            DocumentType::updateOrCreate(
                ['code' => $doc['code']],
                $doc
            );
        }

        // Deactivate TOR (Transcript of Records) — not in the new document list
        DocumentType::where('code', 'TOR')->update(['is_active' => false]);
    }
}
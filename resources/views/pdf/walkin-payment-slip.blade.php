<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Slip - {{ $request->reference_number }}</title>
    <style>
        @page { size: letter; margin: 0.5in; }
        body { font-family: 'Times New Roman', serif; font-size: 11pt; margin: 0; padding: 0; }
        .page { width: 100%; position: relative; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        .header-text-cell { text-align: center; vertical-align: middle; }
        .school-name { font-size: 16pt; font-weight: bold; color: #1565C0; font-family: 'Helvetica', Arial, sans-serif; line-height: 1.2; }
        .office { font-size: 11pt; font-weight: bold; color: #1565C0; font-family: 'Helvetica', Arial, sans-serif; margin-top: 2px; }
        .header-divider { border-top: 2px solid #1565C0; margin: 6px 0 20px 0; }
        .slip-title { text-align: center; font-size: 16pt; font-weight: bold; letter-spacing: 4px; font-family: 'Helvetica', Arial, sans-serif; margin-bottom: 20px; color: #1565C0; }
        .info-section { margin-bottom: 20px; border: 1px solid #ddd; padding: 15px; border-radius: 4px; }
        .info-table { width: 100%; font-size: 10pt; }
        .info-label { font-weight: bold; width: 120px; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .items-table th { background-color: #1565C0; color: white; padding: 8px; text-align: left; font-size: 10pt; }
        .items-table td { padding: 8px; border-bottom: 1px solid #eee; font-size: 10pt; }
        .total-row td { font-weight: bold; font-size: 12pt; border-top: 2px solid #1565C0; }
        .cashier-section { border: 2px dashed #666; padding: 15px; margin-top: 30px; }
        .footer-note { text-align: center; margin-top: 20px; font-size: 10pt; font-weight: bold; color: #d32f2f; }
        .footer-seal { position: absolute; bottom: 0.5in; left: 0in; font-style: italic; font-size: 9pt; }
    </style>
</head>
<body>
<div class="page">
    <table class="header-table">
        <tr>
            <td style="width: 64px; vertical-align: middle;">
                <img src="{{ public_path('images/ccst-logo.png') }}" alt="CCST Logo" style="width: 64px; height: 64px;">
            </td>
            <td class="header-text-cell">
                <div class="school-name">Clark College of Science and Technology</div>
                <div class="office">OFFICE OF THE CASHIER</div>
            </td>
            <td style="width: 64px;"></td>
        </tr>
    </table>
    <div class="header-divider"></div>

    <div class="slip-title">PAYMENT SLIP</div>

    <div class="info-section">
        <table class="info-table">
            <tr>
                <td class="info-label">Reference No:</td>
                <td style="font-size: 14pt; font-weight: bold; color: #1565C0;">{{ $request->reference_number }}</td>
                <td class="info-label">Date:</td>
                <td>{{ date('F d, Y') }}</td>
            </tr>
            <tr>
                <td class="info-label">Student Name:</td>
                <td>{{ strtoupper($request->full_name) }}</td>
                <td class="info-label">Student No:</td>
                <td>{{ $request->student_number ?? 'WALK-IN' }}</td>
            </tr>
            <tr>
                <td class="info-label">Course/Strand:</td>
                <td>{{ $request->course_program }}</td>
                <td class="info-label">Year Level:</td>
                <td>{{ $request->year_level }}</td>
            </tr>
        </table>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Document Requested</th>
                <th style="text-align:center; width: 15%;">Qty</th>
                <th style="text-align:right; width: 25%;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($request->items as $item)
            <tr>
                <td>{{ $item->documentType->name }}</td>
                <td style="text-align:center;">{{ $item->copies }}</td>
                <td style="text-align:right;">PHP {{ number_format($item->fee * $item->copies, 2) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2" style="text-align:right;">TOTAL AMOUNT DUE:</td>
                <td style="text-align:right; color: #1565C0;">PHP {{ number_format($request->total_fee, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="cashier-section">
        <div style="font-weight: bold; font-size: 9pt; margin-bottom: 15px; color: #666;">FOR CASHIER USE ONLY</div>
        <table style="width: 100%;">
            <tr>
                <td width="33%" style="text-align: center;">
                    <div style="border-bottom: 1px solid #000; height: 30px; margin-bottom: 5px;"></div>
                    <div style="font-size: 8pt;">OR Number</div>
                </td>
                <td width="33%" style="text-align: center;">
                    <div style="border-bottom: 1px solid #000; height: 30px; margin-bottom: 5px;"></div>
                    <div style="font-size: 8pt;">Date Paid</div>
                </td>
                <td width="33%" style="text-align: center;">
                    <div style="border-bottom: 1px solid #000; height: 30px; margin-bottom: 5px;"></div>
                    <div style="font-size: 8pt;">Cashier Signature</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer-note">
        * PLEASE PRESENT THIS SLIP TO THE CASHIER FOR PAYMENT. *
    </div>

    <div class="footer-seal">
        CCST DocRequest System | Generated: {{ date('h:i A') }}
    </div>
</div>
</body>
</html>

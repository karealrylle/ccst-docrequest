<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - {{ $request->reference_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #1B6B3A;
            padding-bottom: 10px;
        }
        .school-name {
            font-size: 20px;
            font-weight: bold;
            color: #1B6B3A;
            margin-bottom: 5px;
        }
        .school-subtitle {
            font-size: 12px;
            color: #666;
            margin-bottom: 15px;
        }
        .receipt-title {
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 1px;
            margin-top: 20px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 30px;
        }
        .info-table td {
            padding: 5px 0;
            vertical-align: top;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            background-color: #f5f5f5;
            padding: 10px;
            text-align: left;
            border-bottom: 2px solid #ddd;
        }
        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .total-row td {
            font-weight: bold;
            font-size: 16px;
            border-top: 2px solid #333;
            border-bottom: none;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="school-name">Clark College of Science and Technology</div>
        <div class="school-subtitle">Official Document Request Receipt</div>
        <div class="receipt-title">PAYMENT RECEIPT</div>
    </div>

    <table class="info-table">
        <tr>
            <td class="info-label">Reference No:</td>
            <td>{{ $request->reference_number }}</td>
            <td class="info-label">Date:</td>
            <td>{{ date('M d, Y', strtotime($request->paid_at ?? now())) }}</td>
        </tr>
        <tr>
            <td class="info-label">Student Name:</td>
            <td>{{ $request->full_name }}</td>
            <td class="info-label">Student No:</td>
            <td>{{ $request->student_number }}</td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Document</th>
                <th style="text-align:center;">Copies</th>
                <th style="text-align:right;">Fee</th>
            </tr>
        </thead>
        <tbody>
            @foreach($request->items as $item)
            <tr>
                <td>{{ $item->documentType->name }}</td>
                <td style="text-align:center;">{{ $item->copies }}</td>
                <td style="text-align:right;">Php {{ number_format($item->fee * $item->copies, 2) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2" style="text-align:right; padding-right:15px;">TOTAL PAID</td>
                <td style="text-align:right; color:#1B6B3A;">Php {{ number_format($request->total_fee, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer" style="margin-top: 100px; border-top: 1px solid #eee; padding-top: 20px;">
        This electronic receipt serves as the system's official record of payment and is issued for audit and future verification purposes. No signature is required.
    </div>
</body>
</html>

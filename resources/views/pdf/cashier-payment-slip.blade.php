<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cashier Payment Slip - {{ $reference_number }}</title>
    <style>
        @page { 
            size: legal; 
            margin: 0.3in; 
        }
        body { 
            font-family: 'Helvetica', Arial, sans-serif; 
            font-size: 8.5pt; 
            margin: 0; 
            padding: 0; 
            color: #333; 
            line-height: 1.1;
        }
        
        .container { width: 100%; }

        .half { 
            width: 100%;
            box-sizing: border-box;
        }

        .perforation {
            border-top: 1px dashed #999;
            width: 100%;
            text-align: center;
            margin: 10px 0;
            padding: 5px 0;
        }
        .perforation-text {
            background: white;
            padding: 0 10px;
            font-size: 7pt;
            color: #888;
            position: relative;
            top: -10px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
        .logo { width: 40px; height: 40px; }
        .school-name { font-size: 12pt; font-weight: bold; color: #1565C0; margin-bottom: 1px; }
        .school-address { font-size: 7pt; color: #666; }
        
        .slip-title { 
            text-align: center; 
            font-weight: bold; 
            font-size: 9.5pt; 
            background-color: #f4f4f4; 
            padding: 4px; 
            margin-bottom: 8px;
            border: 1px solid #ddd;
            text-transform: uppercase;
        }

        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 5px; table-layout: fixed; }
        .details-table td { padding: 2px 0; vertical-align: top; }
        .label { font-weight: bold; width: 100px; font-size: 8pt; }
        .value { border-bottom: 1px solid #eee; font-size: 8.5pt; }

        .docs-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            font-size: 8pt;
        }
        .docs-table th {
            text-align: left;
            background-color: #f8f9fa;
            border-bottom: 1px solid #ddd;
            padding: 3px 6px;
            font-weight: bold;
            color: #1565C0;
        }
        .docs-table td {
            padding: 3px 6px;
            border-bottom: 1px dotted #eee;
        }

        .summary-sig-table { width: 100%; border-collapse: collapse; margin-top: 10px; }

        .amount-box {
            border: 2px solid #1565C0;
            padding: 4px 12px;
            text-align: center;
            display: inline-block;
        }
        .amount-label { font-size: 7pt; color: #666; text-transform: uppercase; }
        .amount-value { font-size: 13pt; font-weight: bold; color: #1565C0; }

        .signature-line { 
            border-top: 1px solid #000; 
            width: 160px; 
            text-align: center; 
            font-size: 7.5pt; 
            margin-top: 40px; 
            padding-top: 3px; 
            margin-left: auto;
        }
        
        .instructions { font-size: 7.5pt; font-style: italic; color: #666; line-height: 1.2; padding-right: 15px; }
        
    </style>
</head>
<body>

    <div class="container">
        <!-- TOP HALF -->
        <div class="half">
            <table class="header-table">
                <tr>
                    <td style="width: 45px;"><img src="{{ public_path('images/ccst-logo.png') }}" alt="CCST Logo" class="logo"></td>
                    <td style="text-align: center;">
                        <div class="school-name">Clark College of Science and Technology</div>
                        <div class="school-address">SNS Bldg., Aurea St., Samsonville Subdivision, Dau, Mabalacat City, Pampanga</div>
                    </td>
                    <td style="width: 45px;"></td>
                </tr>
            </table>

            <div class="slip-title">STUDENT PAYMENT RECEIPT – PRESENT TO REGISTRAR AFTER PAYMENT</div>

            <table class="details-table">
                <tr>
                    <td class="label">Reference Number:</td>
                    <td class="value" style="font-weight: bold; color: #1565C0;">{{ $reference_number }}</td>
                    <td class="label" style="padding-left: 15px; width: 90px;">Request Date:</td>
                    <td class="value">{{ $request_date }}</td>
                </tr>
                <tr>
                    <td class="label">Student Name:</td>
                    <td class="value">{{ strtoupper($student_name) }}</td>
                    <td class="label" style="padding-left: 15px; width: 90px;">Student Number:</td>
                    <td class="value">{{ $student_number }}</td>
                </tr>
                <tr>
                    <td class="label">Appointment Date:</td>
                    <td class="value">{{ $appointment_date ?? 'WALK-IN' }}</td>
                    <td class="label" style="padding-left: 15px; width: 90px;">Request Type:</td>
                    <td class="value"><strong>{{ $request_type }}</strong></td>
                </tr>
            </table>

            <table class="docs-table">
                <thead>
                    <tr>
                        <th>Document Description</th>
                        <th style="text-align: center; width: 40px;">Qty</th>
                        <th style="text-align: right; width: 80px;">Fee</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requested_documents as $doc)
                    <tr>
                        <td>{{ $doc->documentType->name }}</td>
                        <td style="text-align: center;">{{ $doc->copies }}</td>
                        <td style="text-align: right;">PHP {{ number_format($doc->fee, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <table class="summary-sig-table">
                <tr>
                    <td style="vertical-align: top; padding-top: 5px;">
                        <div class="instructions">
                            <strong>Note:</strong> Present this receipt to the Registrar after payment. Ensure both halves are stamped.
                        </div>
                    </td>
                    <td style="text-align: right; width: 200px;">
                        <div class="amount-box">
                            <div class="amount-label">Total to Pay</div>
                            <div class="amount-value">PHP {{ number_format($total_fee, 2) }}</div>
                        </div>
                        <div class="signature-line">CASHIER SIGNATURE & STAMP</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- PERFORATION -->
        <div class="perforation">
            <span class="perforation-text">✂ CUT HERE – PERFORATION LINE ✂</span>
        </div>

        <!-- BOTTOM HALF -->
        <div class="half">
            <table class="header-table">
                <tr>
                    <td style="width: 45px;"><img src="{{ public_path('images/ccst-logo.png') }}" alt="CCST Logo" class="logo"></td>
                    <td style="text-align: center;">
                        <div class="school-name">Clark College of Science and Technology</div>
                        <div class="school-address">OFFICE OF THE CASHIER</div>
                    </td>
                    <td style="width: 45px;"></td>
                </tr>
            </table>

            <div class="slip-title">CASHIER’S RECORD COPY – RETAIN BY CASHIER</div>

            <table class="details-table">
                <tr>
                    <td class="label">Reference Number:</td>
                    <td class="value" style="font-weight: bold;">{{ $reference_number }}</td>
                    <td class="label" style="padding-left: 15px; width: 90px;">Payment Date:</td>
                    <td class="value">{{ date('F d, Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Student Name:</td>
                    <td class="value">{{ strtoupper($student_name) }}</td>
                    <td class="label" style="padding-left: 15px; width: 90px;">Payment Time:</td>
                    <td class="value">{{ $current_time }}</td>
                </tr>
                <tr>
                    <td class="label">Student Number:</td>
                    <td class="value">{{ $student_number }}</td>
                    <td class="label" style="padding-left: 15px; width: 90px;">Request Type:</td>
                    <td class="value"><strong>{{ $request_type }}</strong></td>
                </tr>
            </table>

            <table class="docs-table">
                <thead>
                    <tr>
                        <th>Document Description</th>
                        <th style="text-align: center; width: 40px;">Qty</th>
                        <th style="text-align: right; width: 80px;">Fee</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requested_documents as $doc)
                    <tr>
                        <td>{{ $doc->documentType->name }}</td>
                        <td style="text-align: center;">{{ $doc->copies }}</td>
                        <td style="text-align: right;">PHP {{ number_format($doc->fee, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <table class="summary-sig-table">
                <tr>
                    <td style="vertical-align: top; padding-top: 5px;">
                        <div style="font-size: 8pt; font-weight: bold;">OR Number:</div>
                        <div style="border-bottom: 1px solid #ccc; height: 20px; width: 250px;"></div>
                        <div class="instructions" style="margin-top: 8px;">* Internal audit copy. Record OR Number.</div>
                    </td>
                    <td style="text-align: right; width: 200px;">
                        <div class="amount-box" style="border-color: #333;">
                            <div class="amount-label">Amount Collected</div>
                            <div class="amount-value">PHP {{ number_format($total_fee, 2) }}</div>
                        </div>
                        <div class="signature-line">CASHIER SIGNATURE & STAMP</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</body>
</html>

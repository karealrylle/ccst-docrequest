<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Walk-in Document Request Form</title>
    <style>
        @page { size: letter; margin: 0.5in; }
        body { font-family: 'Times New Roman', serif; font-size: 10pt; margin: 0; padding: 0; }
        .page { width: 100%; position: relative; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        .header-text-cell { text-align: center; vertical-align: middle; }
        .school-name { font-size: 16pt; font-weight: bold; color: #1565C0; font-family: 'Helvetica', Arial, sans-serif; line-height: 1.2; }
        .office { font-size: 11pt; font-weight: bold; color: #1565C0; font-family: 'Helvetica', Arial, sans-serif; margin-top: 2px; }
        .header-divider { border-top: 2px solid #1565C0; margin: 6px 0 20px 0; }
        .form-title { text-align: center; font-size: 14pt; font-weight: bold; letter-spacing: 2px; font-family: 'Helvetica', Arial, sans-serif; margin-bottom: 20px; background-color: #1565C0; color: white; padding: 5px; }
        .section-title { font-weight: bold; font-size: 11pt; border-bottom: 1px solid #1565C0; margin-bottom: 15px; color: #1565C0; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-label { font-weight: bold; width: 120px; padding: 8px 0; }
        .info-field { border-bottom: 1px solid #000; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table th, .items-table td { border: 1px solid #000; padding: 6px; text-align: left; font-size: 9pt; }
        .items-table th { background-color: #F0F7F0; }
        .checkbox-box { display: inline-block; width: 12px; height: 12px; border: 1px solid #000; margin-right: 5px; vertical-align: middle; }
        .footer-note { font-size: 8pt; text-align: center; margin-top: 20px; color: #666; }
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
                <div class="office">OFFICE OF THE REGISTRAR</div>
            </td>
            <td style="width: 64px;"></td>
        </tr>
    </table>
    <div class="header-divider"></div>

    <div class="form-title">DOCUMENT REQUEST FORM (WALK-IN)</div>

    <div class="section-title">I. STUDENT INFORMATION</div>
    <table class="info-table">
        <tr>
            <td class="info-label">Full Name:</td>
            <td class="info-field" colspan="3"></td>
        </tr>
        <tr>
            <td class="info-label">Student No:</td>
            <td class="info-field" width="35%"></td>
            <td class="info-label" style="padding-left: 20px; width: 15%;">Contact No:</td>
            <td class="info-field"></td>
        </tr>
        <tr>
            <td class="info-label">Email:</td>
            <td class="info-field" colspan="3"></td>
        </tr>
        <tr>
            <td class="info-label">Course/Strand:</td>
            <td class="info-field"></td>
            <td class="info-label" style="padding-left: 20px;">Grade Level:</td>
            <td class="info-field"></td>
        </tr>
    </table>

    <div class="section-title">II. DOCUMENT SELECTION</div>
    <table class="items-table">
        <thead>
            <tr>
                <th width="10%" style="text-align: center;">Select</th>
                <th>Document Type</th>
                <th width="15%" style="text-align: center;">Fee</th>
                <th width="15%" style="text-align: center;">Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach($documentTypes as $doc)
            <tr>
                <td style="text-align: center;"><div class="checkbox-box"></div></td>
                <td>{{ $doc->name }}</td>
                <td style="text-align: center;">PHP {{ number_format($doc->fee, 2) }}</td>
                <td style="text-align: center;"></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table style="width: 100%; margin-top: 30px;">
        <tr>
            <td width="45%" style="text-align: center;">
                <div style="border-bottom: 1px solid #000; height: 30px;"></div>
                <div style="font-size: 8pt; margin-top: 5px;">Student Signature</div>
            </td>
            <td width="10%"></td>
            <td width="45%" style="text-align: center;">
                <div style="border-bottom: 1px solid #000; height: 30px;"></div>
                <div style="font-size: 8pt; margin-top: 5px;">Date</div>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        Please submit this form to the Registrar's Office. Documents are processed only upon payment.
    </div>
</div>
</body>
</html>

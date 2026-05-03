<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document - {{ $student_name }}</title>
    <style>
        @page { size: letter; margin: 0.5in; }
        body { font-family: 'Times New Roman', serif; font-size: 12pt; margin: 0; padding: 0; }
        .page { width: 100%; position: relative; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        .header-text-cell { text-align: center; vertical-align: middle; }
        .school-name { font-size: 16pt; font-weight: bold; color: #1565C0; font-family: 'Helvetica', Arial, sans-serif; line-height: 1.2; }
        .formerly { font-style: italic; font-size: 9pt; color: #333; font-family: 'Helvetica', Arial, sans-serif; }
        .address { font-size: 9pt; color: #333; font-family: 'Helvetica', Arial, sans-serif; }
        .office { font-size: 11pt; font-weight: bold; color: #1565C0; font-family: 'Helvetica', Arial, sans-serif; margin-top: 2px; }
        .header-divider { border-top: 2px solid #1565C0; margin: 6px 0 24px 0; }
        .cert-title { text-align: center; font-size: 16pt; font-weight: bold; letter-spacing: 2px; font-family: 'Helvetica', Arial, sans-serif; margin-bottom: 28px; }
        .body-text { text-indent: 0.5in; text-align: justify; line-height: 1.8; margin-bottom: 16px; }
        .footer-seal { position: absolute; bottom: 0.5in; left: 0in; font-style: italic; font-size: 10pt; }
        .form-number { position: absolute; bottom: 0in; left: 0in; font-size: 10pt; }
    </style>
</head>
<body>
<div class="page">
    <table class="header-table">
        <tr>
            <td style="width: 72px; vertical-align: middle;">
                <img src="{{ public_path('images/ccst-logo.png') }}" alt="CCST Logo" style="width: 72px; height: 72px;">
            </td>
            <td class="header-text-cell">
                <div class="school-name">Clark College of Science and Technology</div>
                <div class="formerly">Formerly: Clark International College of Science and Technology</div>
                <div class="address">SNS Bldg., Aurea St., Samsonville Subdivision, Dau, Mabalacat City, Pampanga.</div>
                <div class="office">Office of the Registrar</div>
            </td>
            <td style="width: 72px;"></td>
        </tr>
    </table>
    <div class="header-divider"></div>

    <div class="cert-title">CERTIFICATION</div>

    <p class="body-text">
        This is to certify that <strong>{{ $student_name }}</strong> is a student of this institution.
        This certification is issued for whatever legal purpose it may serve.
    </p>

    <div style="margin-top: 48px;">
        <div style="font-style: italic; margin-bottom: 24px;">Certified True and Correct:</div>
        <div style="font-weight: bold;">Ms. Chriscel Ivy A. Caranza</div>
        <div>Senior High School Registrar</div>
    </div>

    <div class="footer-seal"><em>Not valid without the<br>school's official seal</em></div>
    <div class="form-number">CCST-GENERIC</div>
</div>
</body>
</html>

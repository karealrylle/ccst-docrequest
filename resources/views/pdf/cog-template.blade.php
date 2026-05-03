<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate of Grades - {{ $student_name }}</title>
    <style>
        @page { size: letter; margin: 0.5in; }
        body { font-family: 'Times New Roman', serif; font-size: 11pt; margin: 0; padding: 0; }
        .page { width: 100%; position: relative; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        .header-text-cell { text-align: center; vertical-align: middle; }
        .school-name { font-size: 16pt; font-weight: bold; color: #1565C0; font-family: 'Helvetica', Arial, sans-serif; line-height: 1.2; }
        .formerly { font-style: italic; font-size: 9pt; color: #333; font-family: 'Helvetica', Arial, sans-serif; }
        .address { font-size: 9pt; color: #333; font-family: 'Helvetica', Arial, sans-serif; }
        .office { font-size: 11pt; font-weight: bold; color: #1565C0; font-family: 'Helvetica', Arial, sans-serif; margin-top: 2px; }
        .header-divider { border-top: 2px solid #1565C0; margin: 6px 0 24px 0; }
        .cert-title { text-align: center; font-size: 14pt; font-weight: bold; letter-spacing: 2px; font-family: 'Helvetica', Arial, sans-serif; margin-bottom: 15px; }
        .info-grid { width: 100%; margin-bottom: 15px; font-size: 10pt; }
        .grades-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .grades-table th, .grades-table td { border: 1px solid #333; padding: 6px 8px; text-align: left; }
        .grades-table th { background-color: #F0F7F0; font-weight: bold; }
        .signature-section { margin-top: 30px; }
        .signatory-name { font-weight: bold; font-size: 11pt; }
        .footer-seal { position: absolute; bottom: 0.5in; left: 0in; font-style: italic; font-size: 9pt; }
        .form-number { position: absolute; bottom: 0in; left: 0in; font-size: 9pt; }
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
                <div class="address">SNS Bldg., Aurea St., Samsonville Subdivision, Dau, Mabalacat City, Pampanga. Tel. 624 - 0215</div>
                <div class="office">Office of the Registrar</div>
            </td>
            <td style="width: 72px;"></td>
        </tr>
    </table>
    <div class="header-divider"></div>

    <div class="cert-title">CERTIFICATE OF GRADES</div>

    <table class="info-grid">
        <tr>
            <td width="15%"><strong>Name:</strong></td><td width="35%">{{ $student_name }}</td>
            <td width="15%"><strong>Student No:</strong></td><td width="35%">{{ $student_number }}</td>
        </tr>
        <tr>
            <td><strong>Strand:</strong></td><td>{{ $strand }}</td>
            <td><strong>Section:</strong></td><td>{{ $section }}</td>
        </tr>
        <tr>
            <td><strong>Grade Level:</strong></td><td>{{ $grade_level }}</td>
            <td><strong>School Year:</strong></td><td>{{ $school_year }}</td>
        </tr>
    </table>

    <table class="grades-table">
        <thead>
            <tr>
                <th>Subject Description</th>
                <th width="15%" style="text-align: center;">Final Grade</th>
                <th width="15%" style="text-align: center;">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @forelse($grades['subjects'] as $subject)
            <tr>
                <td>{{ $subject['name'] }}</td>
                <td style="text-align: center;">{{ $subject['grade'] }}</td>
                <td style="text-align: center;">{{ $subject['remarks'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="text-align: center;">No grades recorded yet.</td>
            </tr>
            @endforelse
            <tr style="font-weight: bold; background-color: #F9F9F9;">
                <td style="text-align: right;">General Average:</td>
                <td style="text-align: center;">{{ $grades['average'] }}</td>
                <td style="text-align: center;">{{ $grades['average'] >= 75 ? 'PASSED' : 'FAILED' }}</td>
            </tr>
        </tbody>
    </table>

    <table style="width: 100%;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <div style="font-style: italic; margin-bottom: 24px;">Certified True and Correct:</div>
                <div class="signatory-name">Ms. Chriscel Ivy A. Caranza</div>
                <div>Senior High School Registrar</div>
            </td>
            <td style="width: 50%; vertical-align: top; text-align: right;">
                <div style="margin-top: 40px;">Date Issued: {{ $current_date }}</div>
            </td>
        </tr>
    </table>

    <div class="footer-seal"><em>Not valid without the<br>school's official seal</em></div>
    <div class="form-number">CCSTSHS-004</div>
</div>
</body>
</html>

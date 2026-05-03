<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transcript of Records - {{ $student_name }}</title>
    <style>
        @page { size: letter; margin: 0.5in; }
        body { font-family: 'Times New Roman', serif; font-size: 9pt; margin: 0; padding: 0; }
        .page { width: 100%; position: relative; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        .header-text-cell { text-align: center; vertical-align: middle; }
        .school-name { font-size: 14pt; font-weight: bold; color: #1565C0; font-family: 'Helvetica', Arial, sans-serif; line-height: 1.2; }
        .formerly { font-style: italic; font-size: 9pt; color: #333; font-family: 'Helvetica', Arial, sans-serif; }
        .address { font-size: 8pt; color: #333; font-family: 'Helvetica', Arial, sans-serif; }
        .office { font-size: 10pt; font-weight: bold; color: #1565C0; font-family: 'Helvetica', Arial, sans-serif; margin-top: 2px; }
        .header-divider { border-top: 2px solid #1565C0; margin: 6px 0 15px 0; }
        .cert-title { text-align: center; font-size: 12pt; font-weight: bold; font-family: 'Helvetica', Arial, sans-serif; margin-bottom: 10px; }
        .info-grid { width: 100%; margin-bottom: 15px; border-collapse: collapse; }
        .info-grid td { padding: 2px 5px; border-bottom: 1px solid #EEE; }
        .grades-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .grades-table th, .grades-table td { border: 1px solid #333; padding: 4px 6px; text-align: left; }
        .grades-table th { background-color: #F0F7F0; font-weight: bold; text-align: center; text-transform: uppercase; font-size: 8pt; }
        .footer-seal { position: absolute; bottom: 0.5in; left: 0in; font-style: italic; font-size: 8pt; }
        .form-number { position: absolute; bottom: 0in; left: 0in; font-size: 8pt; }
        .signatory-name { font-weight: bold; font-size: 10pt; text-decoration: underline; }
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
                <div class="formerly">Formerly: Clark International College of Science and Technology</div>
                <div class="address">SNS Bldg., Aurea St., Samsonville Subdivision, Dau, Mabalacat City, Pampanga.</div>
                <div class="office">OFFICIAL TRANSCRIPT OF RECORDS (SHS)</div>
            </td>
            <td style="width: 64px;"></td>
        </tr>
    </table>
    <div class="header-divider"></div>

    <table class="info-grid">
        <tr>
            <td width="15%"><strong>NAME:</strong></td><td width="35%">{{ strtoupper($student_name) }}</td>
            <td width="15%"><strong>LRN:</strong></td><td width="35%">{{ $student_number }}</td>
        </tr>
        <tr>
            <td><strong>STRAND:</strong></td><td>{{ strtoupper($strand) }}</td>
            <td><strong>GENDER:</strong></td><td>__________</td>
        </tr>
        <tr>
            <td><strong>GRADUATED:</strong></td><td>{{ $school_year }}</td>
            <td><strong>DATE ISSUED:</strong></td><td>{{ $current_date }}</td>
        </tr>
    </table>

    <table class="grades-table">
        <thead>
            <tr>
                <th width="15%">Subject Code</th>
                <th>Subject Description</th>
                <th width="10%">Grade</th>
                <th width="10%">Units</th>
                <th width="15%">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($grades['subjects'] as $subject)
            <tr>
                <td style="text-align: center;">SHS-{{ substr($subject['name'], 0, 3) }}</td>
                <td>{{ $subject['name'] }}</td>
                <td style="text-align: center;">{{ $subject['grade'] }}</td>
                <td style="text-align: center;">1.0</td>
                <td style="text-align: center;">PASSED</td>
            </tr>
            @endforeach
            <tr style="font-weight: bold;">
                <td colspan="2" style="text-align: right;">GENERAL WEIGHTED AVERAGE:</td>
                <td style="text-align: center;">{{ $grades['average'] }}</td>
                <td></td>
                <td style="text-align: center;">PROMOTED</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 40px;">
        <table style="width: 100%; text-align: center;">
            <tr>
                <td width="50%">
                    <div style="font-size: 8pt; margin-bottom: 30px;">Prepared by:</div>
                    <div class="signatory-name">____________________</div>
                    <div style="font-size: 8pt;">Registrar Clerk</div>
                </td>
                <td width="50%">
                    <div style="font-size: 8pt; margin-bottom: 30px;">Certified Correct:</div>
                    <div class="signatory-name">Ms. Chriscel Ivy A. Caranza</div>
                    <div style="font-size: 8pt;">SHS Registrar</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer-seal"><em>Not valid without the<br>school's official seal</em></div>
    <div class="form-number">CCST-TOR-SHS</div>
</div>
</body>
</html>

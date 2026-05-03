<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report Card (Form 138) - {{ $student_name }}</title>
    <style>
        @page { size: letter; margin: 0.5in; }
        body { font-family: 'Times New Roman', serif; font-size: 10pt; margin: 0; padding: 0; }
        .page { width: 100%; position: relative; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        .header-text-cell { text-align: center; vertical-align: middle; }
        .school-name { font-size: 14pt; font-weight: bold; color: #1565C0; font-family: 'Helvetica', Arial, sans-serif; line-height: 1.2; }
        .formerly { font-style: italic; font-size: 9pt; color: #333; font-family: 'Helvetica', Arial, sans-serif; }
        .address { font-size: 8pt; color: #333; font-family: 'Helvetica', Arial, sans-serif; }
        .office { font-size: 10pt; font-weight: bold; color: #1565C0; font-family: 'Helvetica', Arial, sans-serif; margin-top: 2px; }
        .header-divider { border-top: 2px solid #1565C0; margin: 6px 0 15px 0; }
        .cert-title { text-align: center; font-size: 12pt; font-weight: bold; font-family: 'Helvetica', Arial, sans-serif; margin-bottom: 10px; }
        .info-grid { width: 100%; margin-bottom: 10px; font-size: 9pt; }
        .grades-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 9pt; }
        .grades-table th, .grades-table td { border: 1px solid #333; padding: 4px 6px; text-align: left; }
        .grades-table th { background-color: #F0F7F0; font-weight: bold; text-align: center; }
        .signatory-name { font-weight: bold; font-size: 10pt; border-bottom: 1px solid #000; display: inline-block; padding: 0 15px; }
        .footer-seal { position: absolute; bottom: 0.5in; left: 0in; font-style: italic; font-size: 8pt; }
        .form-number { position: absolute; bottom: 0in; left: 0in; font-size: 8pt; }
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
                <div class="office">Student Progress Report Card (Form 138)</div>
            </td>
            <td style="width: 64px;"></td>
        </tr>
    </table>
    <div class="header-divider"></div>

    <table class="info-grid">
        <tr>
            <td width="12%"><strong>Name:</strong></td><td width="38%">{{ $student_name }}</td>
            <td width="12%"><strong>Student No:</strong></td><td width="38%">{{ $student_number }}</td>
        </tr>
        <tr>
            <td><strong>Strand:</strong></td><td>{{ $strand }}</td>
            <td><strong>Grade/Section:</strong></td><td>{{ $grade_level }} - {{ $section }}</td>
        </tr>
        <tr>
            <td><strong>School Year:</strong></td><td>{{ $school_year }}</td>
            <td><strong>Adviser:</strong></td><td>____________________</td>
        </tr>
    </table>

    <table class="grades-table">
        <thead>
            <tr>
                <th rowspan="2">Learning Areas</th>
                <th colspan="4">Quarterly Rating</th>
                <th rowspan="2" width="10%">Final Rating</th>
                <th rowspan="2" width="10%">Remarks</th>
            </tr>
            <tr>
                <th width="8%">1</th><th width="8%">2</th><th width="8%">3</th><th width="8%">4</th>
            </tr>
        </thead>
        <tbody>
            @forelse($grades['subjects'] as $subject)
            <tr>
                <td>{{ $subject['name'] }}</td>
                <td style="text-align: center;">{{ $subject['grade'] - 2 }}</td>
                <td style="text-align: center;">{{ $subject['grade'] - 1 }}</td>
                <td style="text-align: center;">{{ $subject['grade'] }}</td>
                <td style="text-align: center;">{{ $subject['grade'] }}</td>
                <td style="text-align: center; font-weight: bold;">{{ $subject['grade'] }}</td>
                <td style="text-align: center;">PASSED</td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align: center;">No grades recorded.</td></tr>
            @endforelse
            <tr style="font-weight: bold;">
                <td colspan="5" style="text-align: right;">General Average:</td>
                <td style="text-align: center; background-color: #EEE;">{{ $grades['average'] }}</td>
                <td style="text-align: center;">PASSED</td>
            </tr>
        </tbody>
    </table>

    <table style="width: 100%; margin-top: 30px; text-align: center;">
        <tr>
            <td width="33%">
                <div class="signatory-name">____________________</div>
                <div style="font-size: 8pt;">Class Adviser</div>
            </td>
            <td width="33%">
                <div class="signatory-name">Ms. Chriscel Ivy A. Caranza</div>
                <div style="font-size: 8pt;">SHS Registrar</div>
            </td>
            <td width="33%">
                <div class="signatory-name">Mr. Arvin Mark D. Serrano</div>
                <div style="font-size: 8pt;">SHS Principal</div>
            </td>
        </tr>
    </table>

    <div class="footer-seal"><em>Not valid without the school's official seal</em></div>
    <div class="form-number">SF9-SHS</div>
</div>
</body>
</html>

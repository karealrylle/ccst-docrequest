<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate of Ranking</title>
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
        .header-divider { border-top: 2px solid #1565C0; margin: 6px 0 30px 0; }
        .body-text { text-indent: 0.5in; text-align: justify; line-height: 1.8; margin-bottom: 14px; }
        .blank { border-bottom: 1px solid #000; font-weight: bold; padding: 0 5px; }
        .signatory-name { font-weight: bold; font-size: 12pt; margin-bottom: 2px; }
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
                <div class="address">SNS Bldg., Aurea St., Samsonville Subdivision, Dau, Mabalacat City, Pampanga. Tel. 624 - 0215</div>
                <div class="office">Office of the Registrar</div>
            </td>
            <td style="width: 72px;"></td>
        </tr>
    </table>
    <div class="header-divider"></div>

    <p class="body-text">
        This is to certify that <span class="blank">&nbsp;&nbsp;{{ $student_name }}&nbsp;&nbsp;</span>
        was a bona fide student of this institution under 
        <span class="blank">&nbsp;&nbsp;{{ $strand }}&nbsp;&nbsp;</span>.
    </p>

    <p class="body-text">
        He/She was recognized as one of the <strong>WITH HONORS</strong> awardee with a general weighted average of 
        <strong><span class="blank">&nbsp;&nbsp;{{ $general_average }}%&nbsp;&nbsp;</span></strong> during grade 11 and 12.
    </p>

    <p class="body-text">
        Based on his/her academic performance, he achieved 
        <strong><span class="blank">&nbsp;&nbsp;RANK __________&nbsp;&nbsp;</span></strong> 
        out of <span class="blank">&nbsp;&nbsp;__________&nbsp;&nbsp;</span> students for Senior High School Batch {{ $school_year }}.
    </p>

    <p class="body-text">
        Issued this {{ $current_date }} at Dau, Mabalacat City, Pampanga upon the request 
        of the abovementioned name for <em>scholarship purposes only.</em>
    </p>

    <table style="width: 100%; margin-top: 48px;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <div style="font-style: italic; margin-bottom: 32px;">Certified True and Correct by:</div>
                <div class="signatory-name">Ms. Chriscel Ivy A. Caranza</div>
                <div>Senior High School Registrar</div>
            </td>
            <td style="width: 50%; vertical-align: top;">
                <div style="margin-top: 32px; margin-bottom: 28px;">Noted by:</div>
                <div class="signatory-name">Mr. Arvin Mark D. Serrano</div>
                <div>Senior High School Principal</div>
            </td>
        </tr>
    </table>

    <div class="footer-seal"><em>Not valid without the<br>school's official seal</em></div>
    <div class="form-number">CCSTSHS FORM-005</div>
</div>
</body>
</html>

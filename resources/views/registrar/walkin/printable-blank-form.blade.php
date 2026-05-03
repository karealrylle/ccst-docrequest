<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Volkhov:wght@700&display=swap" rel="stylesheet">
    <title>Blank Document Request Form - CCST</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            font-size: 12px;
            color: #1A1A1A;
            background-color: #fff;
            margin: 0;
            padding: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-family: 'Volkhov', serif;
            font-size: 20px;
            margin: 0;
            text-transform: uppercase;
            font-weight: bold;
            color: #1B6B3A;
        }

        .header p {
            margin: 5px 0;
            font-size: 12px;
            color: #333;
        }

        .form-instructions {
            background-color: #f0f0f0;
            padding: 10px;
            border: 1px solid #ccc;
            margin-bottom: 25px;
            text-align: center;
            font-style: italic;
        }

        .section-title {
            background-color: #1B6B3A;
            color: #fff;
            padding: 6px 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 15px;
            font-size: 13px;
            border-radius: 4px;
        }

        .form-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .form-table td {
            padding: 8px 5px;
            vertical-align: bottom;
        }

        .label {
            font-weight: 700;
            width: 140px;
            color: #444;
            font-size: 11px;
            text-transform: uppercase;
        }

        .input-line {
            border-bottom: 2px solid #D0DDD0;
            flex-grow: 1;
            min-height: 20px;
        }

        .placeholder {
            color: #777;
            font-size: 10px;
            display: block;
            margin-top: 2px;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .request-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .request-table th, .request-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        .request-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }

        .text-left {
            text-align: left !important;
        }

        .checkbox-square {
            display: inline-block;
            width: 15px;
            height: 15px;
            border: 1px solid #000;
            margin-right: 5px;
            vertical-align: middle;
        }

        .footer {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .signature-block {
            width: 250px;
            text-align: center;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            margin-bottom: 5px;
            height: 30px;
        }

        @media print {
            body {
                padding: 0;
                margin: 1cm;
            }
            .no-print {
                display: none;
            }
            .form-instructions {
                background-color: transparent !important;
                border: 1px solid #000;
            }
            .section-title {
                background-color: #000 !important;
                -webkit-print-color-adjust: exact;
            }
            .request-table th {
                background-color: #f2f2f2 !important;
                -webkit-print-color-adjust: exact;
            }
        }

        /* Print Button for Browser View */
        .print-fab {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #1B6B3A;
            color: white;
            padding: 15px 25px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            z-index: 1000;
        }
    </style>
</head>
<body>

    <a href="javascript:window.print()" class="print-fab no-print">PRINT THIS FORM</a>

    <div class="header">
        <h1>Clark College of Science and Technology</h1>
        <p>Arayat Blvd., San Francisco, Mabalacat City, Pampanga</p>
        <p style="font-weight: bold; font-size: 14px; margin-top: 10px;">DOCUMENT REQUEST FORM (WALK-IN)</p>
    </div>

    <div class="form-instructions">
        Please fill out this form completely and submit to the registrar. Use CAPITAL LETTERS where applicable.
    </div>

    <div class="section-title">I. Personal Information</div>
    
    <div class="grid-3">
        <div>
            <span class="label">First Name:</span>
            <div class="input-line"></div>
            <span class="placeholder">e.g. Maria</span>
        </div>
        <div>
            <span class="label">Middle Name:</span>
            <div class="input-line"></div>
            <span class="placeholder">e.g. Santos (if none, leave blank)</span>
        </div>
        <div>
            <span class="label">Last Name:</span>
            <div class="input-line"></div>
            <span class="placeholder">e.g. Dela Cruz</span>
        </div>
    </div>

    <div class="grid-2" style="margin-top: 15px;">
        <div>
            <span class="label">Student Number:</span>
            <div class="input-line"></div>
            <span class="placeholder">e.g. 05-8959</span>
        </div>
        <div>
            <span class="label">Contact Number:</span>
            <div class="input-line"></div>
            <span class="placeholder">e.g. 09123456789</span>
        </div>
    </div>

    <div style="margin-top: 15px;">
        <span class="label">Email Address:</span>
        <div class="input-line"></div>
        <span class="placeholder">e.g. mariadelacruz@gmail.com</span>
    </div>

    <div style="margin-top: 15px;">
        <span class="label">Current Address:</span>
        <div class="input-line"></div>
        <span class="placeholder">e.g. Brgy. Dolores, Mabalacat City, Pampanga</span>
    </div>

    <div class="section-title" style="margin-top: 25px;">II. Academic Information</div>

    <div class="grid-3">
        <div>
            <span class="label">Strand:</span>
            <div class="input-line"></div>
            <span class="placeholder">e.g. ICT</span>
        </div>
        <div>
            <span class="label">Grade:</span>
            <div class="input-line"></div>
            <span class="placeholder">e.g. Grade 11 / Grade 12</span>
        </div>
        <div>
            <span class="label">Section:</span>
            <div class="input-line"></div>
            <span class="placeholder">e.g. ICT-12A</span>
        </div>
    </div>

    <div class="section-title" style="margin-top: 25px;">III. Document Request Details</div>

    <table class="request-table">
        <thead>
            <tr style="background-color: #ffffff; color: #777; font-size: 10px; font-style: italic;">
                <td>Example:</td>
                <td class="text-left">Good Moral Certificate</td>
                <td>2</td>
                <td>2023-2024</td>
                <td>2nd Sem</td>
            </tr>
            <tr>
                <th style="width: 5%;">Select</th>
                <th style="width: 45%;" class="text-left">Document Type</th>
                <th style="width: 10%;">Quantity</th>
                <th style="width: 20%;">Assessment Year</th>
                <th style="width: 20%;">Grading Period</th>
            </tr>
        </thead>
        <tbody>
            @foreach($documentTypes as $doc)
            <tr>
                <td><div class="checkbox-square"></div></td>
                <td class="text-left">{{ $doc->name }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @endforeach
            {{-- Extra empty rows for other requests --}}
            @for($i=0; $i<2; $i++)
            <tr>
                <td><div class="checkbox-square"></div></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @endfor
        </tbody>
    </table>

    <div style="margin-top: 20px; font-size: 11px;">
        <strong>Note:</strong> Processing time depends on the type of document. Please allow 3-5 working days for non-auto printable documents.
    </div>

    <div class="footer">
        <div class="signature-block">
            <div class="signature-line"></div>
            <p>Student's Signature Over Printed Name</p>
        </div>
        <div class="signature-block">
            <div class="signature-line"></div>
            <p>Date Filed</p>
        </div>
    </div>

</body>
</html>

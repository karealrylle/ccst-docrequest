@extends('pdf.report-layout')

@section('report_content')
    <table>
        <thead>
            <tr>
                <th>Student No.</th>
                <th>Full Name</th>
                <th>Grade/Year</th>
                <th>Strand/Course</th>
                <th>Registered</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
                <tr>
                    <td>{{ $student->student_number ?? 'N/A' }}</td>
                    <td>{{ $student->full_name }}</td>
                    <td>{{ $student->grade_level }}</td>
                    <td>{{ $student->strand }}</td>
                    <td>{{ $student->created_at->format('M d, Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

@section('summary_content')
    <div class="summary-item">
        <span class="left">Total Students Registered:</span>
        <span class="right font-bold">{{ $totalStudents }}</span>
    </div>
    
    <table style="width: 100%; margin-top: 10px; border: none;">
        <tr>
            <td style="width: 45%; vertical-align: top; border: none; padding: 0 20px 0 0;">
                <div class="font-bold" style="font-size: 11px; margin-bottom: 5px;">By Grade Level:</div>
                @foreach($gradeCounts as $grade => $count)
                    <div class="summary-item">
                        <span class="left">{{ $grade }}:</span>
                        <span class="right">{{ $count }}</span>
                    </div>
                @endforeach
            </td>
            <td style="width: 45%; vertical-align: top; border: none; padding: 0;">
                <div class="font-bold" style="font-size: 11px; margin-bottom: 5px;">By Strand:</div>
                @foreach($strandCounts as $strand => $count)
                    <div class="summary-item">
                        <span class="left">{{ $strand }}:</span>
                        <span class="right">{{ $count }}</span>
                    </div>
                @endforeach
            </td>
        </tr>
    </table>
@endsection

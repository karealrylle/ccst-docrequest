@extends('pdf.report-layout')

@section('report_content')
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Time Slot</th>
                <th>Student Name</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($appointments as $appointment)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</td>
                    <td>{{ $appointment->timeSlot->start_time }} - {{ $appointment->timeSlot->end_time }}</td>
                    <td>{{ $appointment->student->full_name ?? ($appointment->documentRequest->full_name ?? 'Unknown Student') }}</td>
                    <td style="text-transform: capitalize;">{{ $appointment->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

@section('summary_content')
    <div class="summary-item">
        <span class="left">Total Appointments:</span>
        <span class="right font-bold">{{ $totalAppointments }}</span>
    </div>
    <div class="summary-item">
        <span class="left">Overall Attendance Rate:</span>
        <span class="right font-bold">{{ $attendanceRate }}%</span>
    </div>
    <div style="margin-top: 10px;">
        <div class="font-bold" style="font-size: 11px; margin-bottom: 5px;">Breakdown by Status:</div>
        @foreach($statusCounts as $status => $count)
            <div class="summary-item">
                <span class="left" style="text-transform: capitalize;">{{ $status }}:</span>
                <span class="right">{{ $count }}</span>
            </div>
        @endforeach
    </div>
@endsection

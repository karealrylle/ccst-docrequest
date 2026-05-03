@extends('pdf.report-layout')

@section('report_content')
    <table>
        <thead>
            <tr>
                <th>Ref No.</th>
                <th>Student Name</th>
                <th>Date</th>
                <th>Total Fee</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $request)
                <tr>
                    <td>{{ $request->reference_number }}</td>
                    <td>{{ $request->full_name ?? ($request->appointment->student->full_name ?? ($request->user->full_name ?? 'Unknown Student')) }}</td>
                    <td>{{ $request->created_at->format('M d, Y') }}</td>
                    <td class="text-right">PHP {{ number_format($request->total_fee, 2) }}</td>
                    <td>
                        <span class="status-badge status-{{ $request->status == 'ready_for_pickup' ? 'ready' : $request->status }}">
                            {{ str_replace('_', ' ', $request->status) }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">Total Amount:</th>
                <th class="text-right">PHP {{ number_format($totalAmount, 2) }}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>
@endsection

@section('summary_content')
    <div class="summary-item">
        <span class="left">Total Requests:</span>
        <span class="right font-bold">{{ $totalRequests }}</span>
    </div>
    <div class="summary-item">
        <span class="left">Total Revenue:</span>
        <span class="right font-bold">PHP {{ number_format($totalAmount, 2) }}</span>
    </div>
    <div style="margin-top: 10px;">
        <div class="font-bold" style="font-size: 11px; margin-bottom: 5px;">Breakdown by Status:</div>
        @foreach($statusCounts as $status => $count)
            <div class="summary-item">
                <span class="left" style="text-transform: capitalize;">{{ str_replace('_', ' ', $status) }}:</span>
                <span class="right">{{ $count }}</span>
            </div>
        @endforeach
    </div>
@endsection

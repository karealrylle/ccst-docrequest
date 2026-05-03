@extends('pdf.report-layout')

@section('report_content')
    <table>
        <thead>
            <tr>
                <th>Ref No.</th>
                <th>Student Name</th>
                <th>Type</th>
                <th>Status</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
                <tr>
                    <td>{{ $payment->reference_number }}</td>
                    <td>{{ $payment->full_name ?? ($payment->user->full_name ?? 'Unknown Student') }}</td>
                    <td>{{ $payment->is_walk_in ? 'WALK-IN' : 'ONLINE' }}</td>
                    <td>{{ strtoupper($payment->payment_status) }}</td>
                    <td class="text-right">PHP {{ number_format($payment->total_fee, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-right">Total Collected (Paid Only):</th>
                <th class="text-right">PHP {{ number_format($totalCollected, 2) }}</th>
            </tr>
        </tfoot>
    </table>
@endsection

@section('summary_content')
    <div class="summary-item">
        <span class="left">Total Transactions:</span>
        <span class="right font-bold">{{ $totalPayments }}</span>
    </div>
    <div class="summary-item">
        <span class="left">Total Collected:</span>
        <span class="right font-bold">PHP {{ number_format($totalCollected, 2) }}</span>
    </div>
    <div style="margin-top: 10px;">
        <div class="font-bold" style="font-size: 11px; margin-bottom: 5px;">Breakdown by Status:</div>
        @foreach($statusCounts as $status => $count)
            <div class="summary-item">
                <span class="left" style="text-transform: uppercase;">{{ $status }}:</span>
                <span class="right">{{ $count }}</span>
            </div>
        @endforeach
    </div>
@endsection

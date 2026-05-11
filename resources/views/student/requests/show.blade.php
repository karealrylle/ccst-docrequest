@extends('layouts.student')

@section('title', 'Request Summary — ' . $docRequest->reference_number)

@section('content')

{{-- ── STICKY HEADER ── --}}
<div class="req-sticky-header">DOCUMENT REQUEST SUMMARY</div>

{{-- ── SCROLLABLE CONTAINER ── --}}
<div class="req-scroll">

    <div class="req-card">
        <div class="req-card-body">

            {{-- ════════════════════════════════════════════════════
                 SECTION 1: STUDENT INFORMATION
            ════════════════════════════════════════════════════ --}}
            <div class="section-heading-row">
                <span class="section-heading">Student Information</span>
                <div class="ref-meta">
                    <strong>Request No.</strong> {{ $docRequest->reference_number }}<br>
                    <strong>Date:</strong> {{ $docRequest->created_at->format('m/d/Y') }}
                </div>
            </div>

            <div class="form-row-3">
                <div class="form-field">
                    <label>Student Number</label>
                    <div class="field-readonly">{{ $docRequest->student_number ?? '—' }}</div>
                </div>
                <div class="form-field">
                    <label>Email</label>
                    <div class="field-readonly field-ellipsis">{{ auth()->user()->email }}</div>
                </div>
                <div class="form-field">
                    <label>Contact No.</label>
                    <div class="field-readonly">{{ $docRequest->contact_number }}</div>
                </div>
            </div>

            <div class="form-row-1">
                <div class="form-field">
                    <label>Full Name</label>
                    <div class="field-readonly">{{ $docRequest->full_name }}</div>
                </div>
            </div>

            <div class="form-row-2">
                <div class="form-field">
                    <label>Course / Program</label>
                    <div class="field-readonly">{{ $docRequest->course_program }}</div>
                </div>
                <div class="form-field">
                    <label>Year &amp; Section</label>
                    <div class="field-readonly">{{ $docRequest->year_level }} — {{ $docRequest->section }}</div>
                </div>
            </div>

            <div class="section-divider"></div>

            {{-- ════════════════════════════════════════════════════
                 SECTION 2: REQUESTED DOCUMENTS
            ════════════════════════════════════════════════════ --}}
            <div class="section-heading" style="margin-bottom:10px;">Requested Documents</div>

            <table class="docs-table">
                <thead>
                    <tr class="docs-table-header">
                        <th>Document</th>
                        <th style="width:130px;" class="text-center">Assessment Year</th>
                        <th style="width:110px;" class="text-center">Grading Period</th>
                        <th style="width:80px;"  class="text-center">Qty</th>
                        <th style="width:80px;"  class="text-end">Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($docRequest->items as $index => $item)
                    <tr style="background:{{ $index % 2 === 0 ? '#f8fafb' : 'white' }};">
                        <td class="doc-name-cell">{{ $item->documentType->name }}</td>
                        <td class="text-center doc-meta">{{ $item->assessment_year ?? 'n/a' }}</td>
                        <td class="text-center doc-meta">{{ $item->semester ?? 'n/a' }}</td>
                        <td class="text-center doc-meta">{{ $item->copies }}</td>
                        <td class="text-end doc-price">₱{{ number_format($item->fee * $item->copies, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="total-row">
                <span class="total-label">Total:</span>
                <div class="total-display">₱{{ number_format($docRequest->total_fee, 2) }}</div>
            </div>

            <div class="section-divider"></div>

            {{-- ════════════════════════════════════════════════════
                 SECTION 3: PAYMENT & APPOINTMENT DETAILS
            ════════════════════════════════════════════════════ --}}
            <div class="appointment-card">
                <div class="appointment-header">
                    <i class="bi bi-calendar-check" style="font-size: 1.2rem;"></i>
                    <span>Appointment Details</span>
                </div>
                
                @if($docRequest->appointment)
                    <div class="appointment-body">
                        <div class="apt-item">
                            <span class="apt-label">Date</span>
                            <span class="apt-value">{{ \Carbon\Carbon::parse($docRequest->appointment->appointment_date)->format('F d, Y') }}</span>
                        </div>
                        <div class="apt-item">
                            <span class="apt-label">Time</span>
                            <span class="apt-value">{{ $docRequest->appointment->timeSlot->label ?? 'N/A' }}</span>
                        </div>
                    </div>
                    
                    <div class="payment-instructions">
                        <i class="bi bi-cash-stack me-2" style="font-size: 1.1rem;"></i>
                        <span>
                            Please pay <strong>₱{{ number_format($docRequest->total_fee, 2) }}</strong> at the cashier office on 
                            <strong>{{ \Carbon\Carbon::parse($docRequest->appointment->appointment_date)->format('F d, Y') }}</strong> between 
                            <strong>{{ $docRequest->appointment->timeSlot->label ?? 'your scheduled time' }}</strong>. 
                            Bring your school ID and reference number (<strong>{{ $docRequest->reference_number }}</strong>).
                        </span>
                    </div>
                @elseif($docRequest->is_walk_in)
                    <div class="appointment-body">
                        <div class="apt-item">
                            <span class="apt-label">Type</span>
                            <span class="apt-value">Walk-in Request</span>
                        </div>
                        <div class="apt-item">
                            <span class="apt-label">Schedule</span>
                            <span class="apt-value">Immediate Processing</span>
                        </div>
                    </div>
                    <div class="payment-instructions" style="background: #e7f3ff; color: #0d47a1; border-color: #bbdefb;">
                        <i class="bi bi-info-circle me-2" style="font-size: 1.1rem;"></i>
                        <span>
                            This is a <strong>Walk-in</strong> request. Please proceed to the cashier to pay the total fee of 
                            <strong>₱{{ number_format($docRequest->total_fee, 2) }}</strong> and present your reference number 
                            (<strong>{{ $docRequest->reference_number }}</strong>).
                        </span>
                    </div>
                @else
                    <div class="appointment-body">
                        @if(!$docRequest->is_printable)
                            <div style="color: #666; font-style: italic; font-size: 0.85rem; line-height: 1.6;">
                                <i class="bi bi-info-circle-fill me-1" style="color: #1A9FE0;"></i> 
                                This request contains documents that require <strong>manual verification</strong> (e.g., Form 138). 
                                Appointment booking is disabled for now. You will receive an email notification once your request is ready for pickup.
                            </div>
                        @else
                            <span style="color: #666; font-style: italic;">No appointment details found for this request.</span>
                        @endif
                    </div>
                @endif
            </div>

            <div class="section-divider"></div>

            <p class="note-text">
                <strong>Note:</strong> Updates on your request status can be viewed in the
                <a href="{{ route('student.requests.history') }}" class="note-link">Request History</a> section.
            </p>

        </div>{{-- end req-card-body --}}
    </div>{{-- end req-card --}}

    {{-- ── BOTTOM ACTION BUTTONS ── --}}
    <div class="submit-row">
        <a href="{{ route('student.dashboard') }}" class="btn-cancel">Back to Home</a>

        @if($docRequest->status === 'pending')
            <button type="button" class="btn-danger" id="cancel-request-btn" onclick="document.getElementById('cancel-request-form').submit();">
                <i class="bi bi-x-circle me-1"></i> Cancel Request
            </button>
            <form id="cancel-request-form"
                  method="POST"
                  action="{{ route('student.requests.cancel', $docRequest->id) }}"
                  style="display:none;">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>

</div>{{-- end req-scroll --}}

@endsection

@push('styles')
<style>
    .req-sticky-header {
        background: #1B6B3A;
        color: white;
        font-size: 0.9rem;
        font-weight: 700;
        text-align: center;
        padding: 10px 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        max-width: 900px;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .req-scroll {
        height: calc(100vh - var(--header-h) - var(--footer-h) - 120px);
        overflow-y: auto;
        overflow-x: hidden;
        scrollbar-width: none;
    }
    .req-scroll::-webkit-scrollbar { display: none; }

    .req-card {
        background: #ffffff;
        border: 1px solid #D0DDD0;
        border-radius: 0 0 12px 12px;
        width: 100%;
        max-width: 900px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.10);
    }
    .req-card-body { padding: 20px 24px; }

    .section-heading {
        font-size: 0.85rem;
        font-weight: 700;
        color: #1A1A1A;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .section-heading-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
    }
    .section-divider { border-top: 1px solid #D0DDD0; margin: 16px 0; }

    .ref-meta { font-size: 0.78rem; color: #666; line-height: 1.6; text-align: right; }

    .form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-bottom: 10px; }
    .form-row-2 { display: grid; grid-template-columns: 1fr 1fr;     gap: 12px; margin-bottom: 10px; }
    .form-row-1 { display: grid; grid-template-columns: 1fr;         gap: 12px; margin-bottom: 10px; }
    .form-field  { display: flex; flex-direction: column; }
    .form-field label {
        font-size: 0.73rem;
        font-weight: 700;
        color: #555;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 3px;
    }
    .field-readonly {
        padding: 6px 10px;
        border: 1px solid #D0DDD0;
        border-radius: 4px;
        background: #f8f9fa;
        font-size: 0.82rem;
        color: #1A1A1A;
        font-family: 'Poppins', sans-serif;
        min-height: 32px;
    }
    .field-ellipsis { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

    .docs-table { width: 100%; border-collapse: collapse; font-size: 0.8rem; margin-bottom: 4px; }
    .docs-table-header { background: #1B6B3A; }
    .docs-table-header th { padding: 8px; font-size: 0.75rem; font-weight: 600; color: white; text-align: left; }
    .docs-table td { padding: 7px 8px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
    .doc-name-cell { font-weight: 600; color: #1A1A1A; font-size: 0.82rem; }
    .doc-meta      { color: #555; font-size: 0.8rem; }
    .doc-price     { font-weight: 700; color: #1B6B3A; font-size: 0.82rem; }

    .total-row    { display: flex; align-items: center; justify-content: flex-end; gap: 10px; padding-top: 8px; }
    .total-label  { font-size: 0.82rem; font-weight: 700; color: #1A1A1A; }
    .total-display {
        padding: 5px 16px;
        border: 2px solid #1B6B3A;
        border-radius: 4px;
        font-size: 0.9rem;
        font-weight: 700;
        color: #1B6B3A;
        background: white;
        min-width: 100px;
        text-align: right;
        font-family: 'Poppins', sans-serif;
    }

    .appointment-card {
        border: 1px solid #C3DEC9;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 16px;
    }
    .appointment-header {
        background: #1B6B3A;
        color: white;
        padding: 12px 16px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .appointment-body {
        padding: 16px;
        background: #f8fafb;
        display: flex;
        gap: 24px;
        border-bottom: 1px dashed #C3DEC9;
    }
    .apt-item {
        display: flex;
        flex-direction: column;
    }
    .apt-label {
        font-size: 0.75rem;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
    }
    .apt-value {
        font-size: 0.95rem;
        color: #1A1A1A;
        font-weight: 600;
    }
    .payment-instructions {
        background: #D4EDDA;
        padding: 14px 16px;
        font-size: 0.85rem;
        color: #155724;
        display: flex;
        align-items: flex-start;
        line-height: 1.5;
    }

    .note-text { font-size: 0.78rem; color: #888; margin: 0; }
    .note-link  { color: #1A9FE0; font-weight: 600; }

    .submit-row {
        margin-top: 20px;
        display: flex;
        justify-content: space-between;
        gap: 12px;
        max-width: 900px;
    }
    
    .btn-danger {
        display: inline-block;
        background: white;
        color: #DC3545;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 10px 24px;
        border: 1px solid #DC3545;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-danger:hover {
        background: #DC3545;
        color: white;
    }

    .btn-cancel:hover { background: #e2e6ea; }

    /* ── MOBILE SHOW ── */
    @media (max-width: 768px) {
        .req-scroll {
            height: calc(100vh - 60px - 35px - 40px);
        }
        .form-row-3, .form-row-2 {
            grid-template-columns: 1fr;
            gap: 10px;
        }
        .section-heading-row {
            flex-direction: column;
            gap: 10px;
        }
        .ref-meta {
            text-align: left;
        }
        .docs-table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }
        .appointment-body {
            flex-direction: column;
            gap: 12px;
        }
        .submit-row {
            flex-direction: column;
            gap: 10px;
            padding-bottom: 20px;
        }
        .btn-cancel, .btn-danger {
            width: 100%;
            text-align: center;
        }
        .total-row {
            flex-direction: column;
            align-items: center;
        }
    }
</style>
@endpush
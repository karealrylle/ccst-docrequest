@extends('layouts.student')

@section('title', 'Request History')

@section('content')

@php
    $flashText = session('success') ?? session('error') ?? null;
    $flashType = $flashText && session()->has('error') ? 'error' : 'success';
@endphp
@if($flashText)
<div id="flash-data"
     data-text="{{ e($flashText) }}"
     data-type="{{ $flashType }}"
     style="display:none;" aria-hidden="true"></div>
@endif

{{-- ══════════════════════════════════════════════════════════════════
     STICKY HEADER
══════════════════════════════════════════════════════════════════ --}}
<div class="req-sticky-header">REQUEST HISTORY</div>

{{-- ══════════════════════════════════════════════════════════════════
     SCROLLABLE CONTAINER
══════════════════════════════════════════════════════════════════ --}}
<div class="req-scroll">

    {{-- ── MAIN CARD ── --}}
    <div class="req-card">
        <div class="req-card-body">

            <div class="section-heading-row">
                <span class="section-heading">My Document Requests</span>
                <div style="font-size:0.78rem; color:#666;">
                    {{ $requests->total() }} request{{ $requests->total() !== 1 ? 's' : '' }} total
                </div>
            </div>

            {{-- ─────────────────────────────────────────────────────
                 EMPTY STATE
            ───────────────────────────────────────────────────── --}}
            @if($requests->isEmpty())
            <div style="text-align:center; padding:40px 20px;">
                <div style="width:60px; height:60px; background:#E8F5E9; border-radius:50%;
                            display:flex; align-items:center; justify-content:center;
                            margin:0 auto 14px;">
                    <i class="bi bi-folder2-open"
                       style="font-size:1.6rem; color:#1B6B3A;"></i>
                </div>
                <div class="fw-bold" style="font-size:0.95rem; color:#1A1A1A; margin-bottom:6px;">
                    No requests yet
                </div>
                <div style="font-size:0.82rem; color:#666; margin-bottom:16px;">
                    You haven't submitted any document requests yet.
                </div>
                <a href="{{ route('student.requests.create') }}" class="btn-submit">
                    <i class="bi bi-plus-circle me-1"></i> Request a Document
                </a>
            </div>

            @else

            {{-- ─────────────────────────────────────────────────────
                 REQUESTS TABLE
            ───────────────────────────────────────────────────── --}}
            <div class="hist-table-wrap">
                <table class="hist-table">
                    <thead>
                        <tr class="hist-table-header">
                            <th style="width:140px;">Reference No.</th>
                            <th>Documents</th>
                            <th style="width:80px;" class="text-center">Total</th>
                            <th style="width:120px;" class="text-center">Request Status</th>
                            <th style="width:120px;" class="text-center">Appointment</th>
                            <th style="width:160px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $index => $req)
                        @php
                            $status      = $req->status;
                            $appointment = $req->appointment;
                            
                            // Check if appointment exists and is active (scheduled)
                            $hasActiveAppointment = $appointment && $appointment->status === 'scheduled';
                            
                            // Determine which cancel button to show
                            $showCancelAppointment = $hasActiveAppointment;
                            $showCancelRequest = $status === 'pending' && !$hasActiveAppointment;

                            $reqBadge = match($status) {
                                'pending'            => ['label' => 'Pending',          'class' => 'badge-pending'],
                                'payment_method_set' => ['label' => 'Pending',          'class' => 'badge-pending'],
                                'payment_uploaded'   => ['label' => 'Pending',          'class' => 'badge-pending'],
                                'payment_rejected'   => ['label' => 'Pending',          'class' => 'badge-pending'],
                                'payment_verified'   => ['label' => 'Verified',         'class' => 'badge-verified'],
                                'processing'         => ['label' => 'Processing',       'class' => 'badge-processing'],
                                'ready_for_pickup'   => ['label' => 'Ready for Pickup', 'class' => 'badge-ready'],
                                'received'           => ['label' => 'Received',         'class' => 'badge-received'],
                                'completed'          => ['label' => 'Completed',        'class' => 'badge-completed'],
                                'cancelled'          => ['label' => 'Cancelled',        'class' => 'badge-cancelled'],
                                default              => ['label' => ucfirst($status),   'class' => 'badge-not-set'],
                            };

                            // Check if appointment was missed
                            $isMissed = $appointment && $appointment->status === 'missed';

                            // Determine if the request is finished
                            $isFinished = in_array($status, ['received', 'completed', 'cancelled']);

                            // Check if appointment exists and is active (scheduled)
                            // Buttons should only show if the request is NOT finished
                            $hasActiveAppointment = $appointment && $appointment->status === 'scheduled' && !$isFinished;
                            
                            // Determine which cancel button to show
                            $showCancelAppointment = $hasActiveAppointment;
                            $showCancelRequest = $status === 'pending' && !$hasActiveAppointment && !$isFinished;

                            // Show Book button if it's ready OR if it's pending (like for Form 138 or Missed)
                            $showBook     = ($status === 'ready_for_pickup' || $status === 'pending')
                                            && !$hasActiveAppointment
                                            && !$isFinished;
                        @endphp
                        <tr style="background:{{ $index % 2 === 0 ? '#f8fafb' : 'white' }};">

                            <td style="padding:10px 8px;">
                                <div class="fw-bold" style="font-size:0.8rem; color:#1B6B3A;">
                                    {{ $req->reference_number }}
                                </div>
                                <div style="font-size:0.72rem; color:#888; margin-top:2px;">
                                    {{ $req->created_at->format('m/d/Y') }}
                                </div>
                            </td>

                            <td style="padding:10px 8px;">
                                @foreach($req->items as $item)
                                <div style="font-size:0.78rem; color:#1A1A1A; line-height:1.5;">
                                    <span class="fw-bold" title="{{ $item->documentType->name }}">{{ $item->documentType->code }}</span>
                                    <span style="color:#888;">× {{ $item->copies }}</span>
                                </div>
                                @endforeach
                            </td>

                            <td class="text-end" style="padding:10px 8px;">
                                <div class="fw-bold" style="font-size:0.82rem; color:#1B6B3A;">
                                    ₱{{ number_format($req->total_fee, 2) }}
                                </div>
                            </td>

                            <td class="text-center" style="padding:10px 8px;">
                                <span class="hist-badge {{ $reqBadge['class'] }}">
                                    {{ $reqBadge['label'] }}
                                </span>
                            </td>
                             <td class="text-center" style="padding:10px 8px;">
                                @if($appointment && $appointment->status === 'scheduled')
                                    <div style="font-size:0.67rem; font-weight:600; color:#1B6B3A;">
                                        {{ date('M d', strtotime($appointment->appointment_date)) }}<br>
                                        {{ $appointment->timeSlot->label ?? '-' }}
                                    </div>
                                @elseif($appointment && $appointment->status === 'missed')
                                    <span class="hist-badge" style="background:#DC3545; color:white;">Missed</span>
                                @else
                                    <span class="hist-badge" style="background:#F0F0F0; color:#888;">Not booked</span>
                                @endif
                            </td>

                            <td class="text-center" style="padding:10px 8px;">
                                <div style="display:flex; flex-direction:column; gap:5px; align-items:center;">

                                    <a href="{{ route('student.requests.show', $req->id) }}"
                                    class="hist-btn hist-btn--view">
                                        <i class="bi bi-eye me-1"></i>View
                                    </a>

                                    @if($hasActiveAppointment)
                                    <button type="button"
                                            class="hist-btn hist-btn--book"
                                            onclick="openRescheduleModal({{ $appointment->id }}, '{{ $appointment->appointment_date }}', {{ $appointment->time_slot_id }})">
                                        <i class="bi bi-pencil-square me-1"></i>Edit Appt
                                    </button>
                                    @elseif($showBook)
                                    <button type="button"
                                            class="hist-btn hist-btn--book"
                                            onclick="openBookModal({{ $req->id }})">
                                        <i class="bi bi-calendar-check me-1"></i>Book Slot
                                    </button>
                                    @endif


                                    {{-- SINGLE CANCEL BUTTON - Dynamic based on appointment --}}
                                    @if($showCancelAppointment)
                                    <button type="button"
                                            class="hist-btn hist-btn--cancel"
                                            onclick="confirmCancelAppointment({{ $appointment->id }})">
                                            Cancel Appt
                                    </button>
                                    <form id="cancel-appointment-form-{{ $appointment->id }}"
                                        method="POST"
                                        action="{{ route('student.appointments.cancel', $appointment->id) }}"
                                        style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    @elseif($showCancelRequest)
                                    <button type="button"
                                            class="hist-btn hist-btn--cancel"
                                            onclick="confirmCancel({{ $req->id }}, '{{ $req->reference_number }}')">
                                            Cancel Request
                                    </button>
                                    <form id="cancel-form-{{ $req->id }}"
                                        method="POST"
                                        action="{{ route('student.requests.cancel', $req->id) }}"
                                        style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    @endif

                                </div>
                            </td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ─────────────────────────────────────────────────────
                 PAGINATION
            ───────────────────────────────────────────────────── --}}
            @if($requests->hasPages())
            <div class="hist-pagination">
                @if($requests->onFirstPage())
                    <span class="page-btn page-btn--disabled"><i class="bi bi-chevron-left"></i></span>
                @else
                    <a href="{{ $requests->previousPageUrl() }}" class="page-btn">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                @endif

                @foreach($requests->getUrlRange(1, $requests->lastPage()) as $page => $url)
                    @if($page == $requests->currentPage())
                        <span class="page-btn page-btn--active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                    @endif
                @endforeach

                @if($requests->hasMorePages())
                    <a href="{{ $requests->nextPageUrl() }}" class="page-btn">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                @else
                    <span class="page-btn page-btn--disabled"><i class="bi bi-chevron-right"></i></span>
                @endif
            </div>
            @endif

            @endif {{-- end @if not empty --}}

        </div>{{-- end req-card-body --}}
    </div>{{-- end req-card --}}

    <div style="padding-bottom:20px;"></div>

</div>{{-- end req-scroll --}}


{{-- ══════════════════════════════════════════════════════════════════
     BOOK APPOINTMENT MODAL
══════════════════════════════════════════════════════════════════ --}}
<div id="book-modal-backdrop" class="modal-backdrop-custom" style="display:none;">
    <div class="modal-box">

        <div class="modal-header-custom" id="modal-header-title">
            <i class="bi bi-calendar-check me-2"></i>Book Pickup Appointment
            <button type="button"
                    onclick="closeBookModal()"
                    style="margin-left:auto; background:none; border:none;
                           color:white; font-size:1.1rem; cursor:pointer; line-height:1;">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="modal-body-custom">

            <div style="font-size:0.82rem; color:#555; margin-bottom:14px; line-height:1.6;" id="modal-subtitle">
                Choose a date and time slot to pick up your documents.
            </div>

            <form id="book-appointment-form" method="POST"
                  action="{{ route('student.appointments.store') }}">
                @csrf
                <input type="hidden" name="_method" id="modal-method" value="POST">
                <input type="hidden" name="document_request_id" id="modal-request-id" value="">

                <div class="form-field mb-3">
                    <label>Pickup Date</label>
                    <input type="text"
                           name="appointment_date"
                           id="appointment-date-picker"
                           class="field-input"
                           placeholder="Select a date"
                           readonly>
                </div>

                <div class="form-field mb-4">
                    <label>Time Slot</label>
                    <select name="time_slot_id" id="time-slot-select" class="field-input">
                        <option value="">— Select a time slot —</option>
                        @foreach($timeSlots as $slot)
                        <option value="{{ $slot->id }}">{{ $slot->label }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:10px;">
                    <button type="button" onclick="closeBookModal()" class="btn-cancel-sm">
                        Cancel
                    </button>
                    <button type="submit" class="btn-submit-sm" id="modal-submit-btn">
                        <i class="bi bi-check-circle me-1"></i>Confirm Booking
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>


@endsection


@push('styles')
<style>
    .layout-alert-hide { display: none !important; }

    .req-sticky-header {
        background: #1B6B3A; color: white;
        font-size: 0.9rem; font-weight: 700;
        text-align: center; padding: 10px 20px;
        text-transform: uppercase; letter-spacing: 0.5px;
        max-width: 900px; position: sticky; top: 0; z-index: 10;
    }

    .req-scroll {
        height: calc(100vh - var(--header-h) - var(--footer-h) - 120px);
        overflow-y: auto; overflow-x: hidden;
    }

    .req-card {
        background: #ffffff; border: 1px solid #D0DDD0;
        border-radius: 0 0 12px 12px; width: 100%; max-width: 900px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.10);
    }
    .req-card-body { padding: 20px 12px; }

    /* ── MOBILE HISTORY ── */
    @media (max-width: 768px) {
        .req-scroll {
            height: calc(100vh - 60px - 35px - 40px);
        }
        .hist-table th, .hist-table td {
            padding: 8px 4px;
            font-size: 0.72rem;
        }
        .hist-btn {
            width: 100%;
            padding: 6px 8px;
        }
    }


    .section-heading { font-size:0.85rem; font-weight:700; color:#1A1A1A; text-transform:uppercase; letter-spacing:0.3px; }
    .section-heading-row { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:14px; }

    .form-field { display: flex; flex-direction: column; }
    .form-field label { font-size:0.73rem; font-weight:700; color:#555; text-transform:uppercase; letter-spacing:0.4px; margin-bottom:3px; }
    .field-input { padding:6px 10px; border:1px solid #D0DDD0; border-radius:4px; background:white; font-size:0.82rem; color:#1A1A1A; font-family:'Poppins',sans-serif; min-height:32px; width:100%; outline:none; }
    .field-input:focus { border-color:#1B6B3A; box-shadow:0 0 0 2px rgba(27,107,58,0.12); }

    .hist-table-wrap { width:100%; overflow-x:auto; }
    .hist-table { width:100%; border-collapse:collapse; font-size:0.8rem; }
    .hist-table-header { background:#1B6B3A; }
    .hist-table-header th { padding:9px 8px; font-size:0.74rem; font-weight:600; color:white; text-align:left; white-space:nowrap; }
    .hist-table td { border-bottom:1px solid #f0f0f0; vertical-align: middle; }
    .hist-table tbody tr:last-child td { border-bottom:none; }
    .hist-table tbody tr:hover { background:#F5FBF5 !important; }

    .hist-badge { display:inline-block; font-size:0.68rem; font-weight:700; padding:3px 8px; border-radius:20px; white-space:nowrap; letter-spacing:0.2px; }
    .badge-pending      { background:#FFF3CD; color:#856404; }
    .badge-processing   { background:#E8F4FD; color:#0969A2; font-style:italic; }
    .badge-ready        { background:#F5A623; color:white; font-weight:800; }
    .badge-received     { background:#1B6B3A; color:white; font-weight:800; }
    .badge-completed    { background:#E2E2E2; color:#777; }
    .badge-cancelled    { background:#DC3545; color:white; }
    .badge-verified     { background:#1B6B3A; color:white; }
    .badge-rejected     { background:#F8D7DA; color:#721C24; }
    .badge-uploaded     { background:#FFF3CD; color:#856404; }
    .badge-not-uploaded { background:#F0F0F0; color:#888; }
    .badge-not-set      { background:#F0F0F0; color:#aaa; }
    .badge-pay-office   { background:#E8F4FD; color:#0969A2; }

    .hist-btn { display:inline-flex; align-items:center; justify-content:center; font-size:0.70rem; font-weight:700; padding:4px 10px; border-radius:4px; border:none; cursor:pointer; text-decoration:none; white-space:nowrap; font-family:'Poppins',sans-serif; transition:opacity 0.15s; width:100px; }
    .hist-btn:hover { opacity:0.82; }
    .hist-btn--view     { background:#E8F4FD; color:#0969A2; }
    .hist-btn--upload   { background:#1A9FE0; color:white; }
    .hist-btn--reupload { background:#F5C518; color:#1A1A1A; }
    .hist-btn--receipt  { background:#D4EDDA; color:#155724; }
    .hist-btn--book     { background:#1B6B3A; color:white; }
    .hist-btn--cancel   { background:#F8D7DA; color:#721C24; }
    .hist-btn--claiming {background: #f5c5186d;color: #1A1A1A;}
    .hist-btn--claiming:hover {background: #e6b800;color: #1A1A1A;}

    .hist-pagination { display:flex; justify-content:center; align-items:center; gap:4px; padding:16px 0 4px; }
    .page-btn { display:inline-flex; align-items:center; justify-content:center; width:30px; height:30px; border-radius:4px; font-size:0.78rem; font-weight:600; color:#1B6B3A; text-decoration:none; border:1px solid #D0DDD0; background:white; transition:all 0.15s; }
    .page-btn:hover         { background:#F0F7F0; color:#1B6B3A; }
    .page-btn--active       { background:#1B6B3A; color:white; border-color:#1B6B3A; }
    .page-btn--active:hover { background:#1B6B3A; color:white; }
    .page-btn--disabled     { color:#CCC; border-color:#EEE; cursor:not-allowed; pointer-events:none; }

    .modal-backdrop-custom { position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:1050; display:flex; align-items:center; justify-content:center; }
    .modal-box { background:white; border-radius:10px; width:100%; max-width:420px; box-shadow:0 12px 40px rgba(0,0,0,0.20); overflow:hidden; }
    .modal-header-custom { background:#1B6B3A; color:white; font-size:0.88rem; font-weight:700; padding:12px 16px; display:flex; align-items:center; }
    .modal-body-custom { padding:18px 20px; }
    .btn-submit-sm { background:#1A9FE0; color:white; font-weight:700; font-size:0.82rem; padding:8px 20px; border:none; border-radius:6px; cursor:pointer; font-family:'Poppins',sans-serif; transition:background 0.2s; }
    .btn-submit-sm:hover { background:#0D7FBF; }
    .btn-cancel-sm { background:#F5C518; color:#1A1A1A; font-weight:700; font-size:0.82rem; padding:8px 20px; border:none; border-radius:6px; cursor:pointer; font-family:'Poppins',sans-serif; transition: background 0.2s; }
    .btn-cancel-sm:hover { background:#E0B000; }

    .btn-submit { display:inline-flex; align-items:center; background:#1A9FE0; color:white; font-weight:700; font-size:0.85rem; padding:10px 24px; border:none; border-radius:6px; cursor:pointer; text-decoration:none; font-family:'Poppins',sans-serif; transition:background 0.2s; }
    .btn-submit:hover { background:#0D7FBF; color:white; }

    #bell-btn-wrapper { position:relative; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; }
    #bell-badge { position:absolute; top:-5px; right:-5px; background:#DC3545; color:white; font-size:0.58rem; font-weight:700; min-width:16px; height:16px; border-radius:8px; padding:0 3px; display:none; align-items:center; justify-content:center; pointer-events:none; line-height:1; }
    #bell-dropdown { position:absolute; top:calc(100% + 10px); right:0; width:310px; background:white; border:1px solid #D0DDD0; border-radius:10px; box-shadow:0 8px 28px rgba(0,0,0,0.14); z-index:9999; overflow:hidden; opacity:0; visibility:hidden; transform:translateY(-6px); transition:opacity 0.18s ease,transform 0.18s ease,visibility 0s linear 0.18s; }
    #bell-dropdown.open { opacity:1; visibility:visible; transform:translateY(0); transition:opacity 0.18s ease,transform 0.18s ease,visibility 0s linear 0s; }
    #bell-dropdown-header { background:#1B6B3A; color:white; font-size:0.78rem; font-weight:700; padding:9px 14px; text-transform:uppercase; letter-spacing:0.4px; display:flex; align-items:center; gap:6px; }
    #bell-dropdown-body { max-height:260px; overflow-y:auto; }
    .bell-notif-empty { padding:18px 14px; font-size:0.82rem; color:#999; text-align:center; }
    .bell-notif-item { display:flex; align-items:flex-start; gap:9px; padding:10px 14px; border-bottom:1px solid #f0f0f0; font-size:0.8rem; color:#333; line-height:1.4; }
    .bell-notif-item:last-child { border-bottom:none; }
    .bell-notif-item.unread { background:#f5fdf7; }
    .bell-notif-icon { flex-shrink:0; margin-top:2px; font-size:0.9rem; }
    .bell-notif-icon.success { color:#2E8B57; }
    .bell-notif-icon.error   { color:#DC3545; }
    .bell-notif-time { font-size:0.7rem; color:#bbb; margin-top:3px; }

</style>
@endpush

@push('scripts')
<script>
function confirmCancel(requestId, refNumber) {
    Swal.fire({
        title: 'Cancel Request?',
        text: 'Are you sure you want to cancel ' + refNumber + '? This cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, cancel it',
        cancelButtonText: 'No, keep it',
        confirmButtonColor: '#DC3545',
        cancelButtonColor: '#F5C518',
        reverseButtons: true
    }).then(function (result) {
        if (result.isConfirmed) {
            document.getElementById('cancel-form-' + requestId).submit();
        }
    });
}

function closeBookModal() {
    document.getElementById('book-modal-backdrop').style.display = 'none';
}

function openBookModal(requestId) {
    document.getElementById('modal-header-title').innerHTML = '<i class="bi bi-calendar-check me-2"></i>Book Pickup Appointment';
    document.getElementById('modal-subtitle').textContent = 'Choose a date and time slot to pick up your documents.';
    document.getElementById('modal-submit-btn').innerHTML = '<i class="bi bi-check-circle me-1"></i>Confirm Booking';
    document.getElementById('modal-method').value = 'POST';
    document.getElementById('book-appointment-form').action = "{{ route('student.appointments.store') }}";
    
    document.getElementById('modal-request-id').value = requestId;
    document.getElementById('appointment-date-picker').value = '';
    document.getElementById('time-slot-select').value = '';
    
    document.getElementById('book-modal-backdrop').style.display = 'flex';
}

function openRescheduleModal(appointmentId, date, timeSlotId) {
    document.getElementById('modal-header-title').innerHTML = '<i class="bi bi-pencil-square me-2"></i>Edit Appointment';
    document.getElementById('modal-subtitle').textContent = 'Choose a new date and time slot to pick up your documents.';
    document.getElementById('modal-submit-btn').innerHTML = '<i class="bi bi-check-circle me-1"></i>Save Changes';
    document.getElementById('modal-method').value = 'PATCH';
    const rescheduleUrl = "{{ route('student.appointments.reschedule', ':id') }}";
    document.getElementById('book-appointment-form').action = rescheduleUrl.replace(':id', appointmentId);
    
    document.getElementById('modal-request-id').value = ''; // Not needed for reschedule
    
    const datePicker = document.getElementById('appointment-date-picker');
    datePicker.value = date;
    
    // If flatpickr is active, update its internal date
    if (datePicker._flatpickr) {
        datePicker._flatpickr.setDate(date);
    }
    
    document.getElementById('time-slot-select').value = timeSlotId;
    
    document.getElementById('book-modal-backdrop').style.display = 'flex';
}

document.addEventListener('DOMContentLoaded', function () {
    const backdrop = document.getElementById('book-modal-backdrop');
    if (backdrop) {
        backdrop.addEventListener('click', function (e) { if (e.target === backdrop) closeBookModal(); });
    }
    const datePicker = document.getElementById('appointment-date-picker');
    if (datePicker && typeof flatpickr !== 'undefined') {
        flatpickr(datePicker, {
            minDate: "today",
            disable: [function (date) { return date.getDay() === 0 || date.getDay() === 6; }],
            dateFormat: 'Y-m-d',
            disableMobile: true
        });
    }
});
</script>
@endpush
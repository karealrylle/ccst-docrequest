@extends('layouts.student')

@section('title', 'Dashboard')

@section('content')

{{-- Hero Section --}}
<div class="welcome-section">
    <h1 class="welcome-title">SKIP THE LINE.<br>REQUEST ONLINE.</h1>
    <p class="welcome-subtitle">No more waiting in line. Request your school documents anytime, anywhere.</p>
    <div class="hero-actions">
        <a href="{{ route('student.requests.create') }}" class="btn-hero-request">
            REQUEST NOW
        </a>
    </div>
</div>

{{-- Upcoming Appointment Card --}}
@if($upcomingAppointment)
<div class="appointment-card">
    <div class="appointment-card-header">
        <i class="bi bi-calendar-check-fill me-2"></i> Upcoming Appointment
    </div>
    <div class="appointment-card-body">
        <div class="appointment-details">
            <div class="appointment-date">
                <i class="bi bi-calendar3"></i>
                <span>{{ \Carbon\Carbon::parse($upcomingAppointment->appointment_date)->format('F d, Y') }}</span>
            </div>
            <div class="appointment-time">
                <i class="bi bi-clock"></i>
                <span>{{ $upcomingAppointment->timeSlot->label ?? '—' }}</span>
            </div>
            <div class="appointment-reference">
                <i class="bi bi-receipt"></i>
                <span>Reference: {{ $upcomingAppointment->documentRequest->reference_number }}</span>
            </div>
            <div class="appointment-amount">
                <i class="bi bi-cash-stack"></i>
                <span>Amount Due: ₱{{ number_format($upcomingAppointment->documentRequest->total_fee, 2) }}</span>
            </div>
        </div>
        <div class="appointment-actions">
            <a href="{{ route('student.requests.show', $upcomingAppointment->document_request_id) }}" class="btn-view-details">
                View Details
            </a>
            <button type="button" class="btn-cancel-appointment" onclick="cancelAppointment({{ $upcomingAppointment->id }})">
                Cancel Appointment
            </button>
        </div>
    </div>
</div>
@else
<div class="no-appointment-card">
    <div class="no-appointment-icon">
        <i class="bi bi-calendar-x"></i>
    </div>
    <div class="no-appointment-text">No upcoming appointments</div>
    <a href="{{ route('student.requests.create') }}" class="btn-create-request">Create a Request</a>
</div>
@endif

{{-- Announcement Cards --}}
<div class="announcements-section">
    <div class="announcements-header">
        <i class="bi bi-megaphone-fill me-2"></i> Announcements
    </div>
    <div class="announcements-grid">
        <div class="announce-card">
            <div class="announce-card-header">Announcement Board</div>
            <div class="announce-card-body">
                @if($announcement && $announcement->is_published)
                    {!! nl2br(e($announcement->content)) !!}
                @else
                    <span class="text-muted fst-italic">No announcement currently published.</span>
                @endif
            </div>
        </div>
        <div class="announce-card">
            <div class="announce-card-header">Transaction Days</div>
            <div class="announce-card-body">
                @if($transactionDays && $transactionDays->is_published)
                    {!! nl2br(e($transactionDays->content)) !!}
                @else
                    <span class="text-muted fst-italic">No transaction day changes at this time.</span>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('right-panel')
    <div class="rp-date-card">
        <div class="rp-date-day">{{ now()->format('d') }}</div>
        <div class="rp-date-month">{{ now()->format('F Y') }}</div>
        <div class="rp-date-time" id="live-time">--:-- --</div>
    </div>

    <div class="ccst-card mb-0">
        <div class="ccst-card-header blue">Quick Stats</div>
        <div class="ccst-card-body p-0">
            <div class="rp-stat-row">
                <span><i class="bi bi-hourglass-split me-2"></i> Pending Requests</span>
                <strong>{{ $pendingCount }}</strong>
            </div>
            <div class="rp-stat-row" style="border-bottom:none;">
                <span><i class="bi bi-check-circle me-2"></i> Completed Requests</span>
                <strong>{{ $completedCount }}</strong>
            </div>
        </div>
    </div>

@endsection

@push('styles')
<style>
    .welcome-section {
        margin-bottom: 40px;
        padding-top: 10px;
    }

    .welcome-title {
        font-family: 'Volkhov', serif;
        font-size: 2.5rem;
        font-weight: 800;
        color: #1A1A1A;
        margin-bottom: 16px;
        line-height: 1.1;
        letter-spacing: 1px;
    }

    .welcome-subtitle {
        font-size: 1rem;
        color: #444;
        max-width: 650px;
        line-height: 2.5;
        margin-bottom: 30px;
    }

    .btn-hero-request {
        display: inline-block;
        background: #2575C0; /* Branded blue from screenshot */
        color: white;
        font-weight: 800;
        font-size: 1rem;
        padding: 16px 40px;
        border-radius: 6px;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 4px 15px rgba(37, 117, 192, 0.3);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-hero-request:hover {
        background: #1D5E9B;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(37, 117, 192, 0.4);
    }

    .quick-request {
        margin-bottom: 28px;
    }

    .btn-request-now {
        display: inline-flex;
        align-items: center;
        background: #1A9FE0;
        color: white;
        font-weight: 700;
        font-size: 1rem;
        padding: 14px 32px;
        border-radius: 8px;
        text-decoration: none;
        transition: background 0.2s;
        border: none;
        cursor: pointer;
    }

    .btn-request-now:hover {
        background: #0D7FBF;
        color: white;
    }

    .appointment-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 28px;
        border-left: 4px solid #1B6B3A;
    }

    .appointment-card-header {
        background: #1B6B3A;
        color: white;
        font-size: 0.9rem;
        font-weight: 700;
        padding: 12px 20px;
    }

    .appointment-card-body {
        padding: 20px;
    }

    .appointment-details {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        margin-bottom: 20px;
    }

    .appointment-date, .appointment-time, .appointment-reference, .appointment-amount {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        color: #333;
    }

    .appointment-date i, .appointment-time i, .appointment-reference i, .appointment-amount i {
        color: #1B6B3A;
        width: 20px;
    }

    .appointment-actions {
        display: flex;
        gap: 12px;
        padding-top: 12px;
        border-top: 1px solid #f0f0f0;
    }

    .btn-view-details {
        background: #1A9FE0;
        color: white;
        padding: 8px 20px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        text-decoration: none;
    }

    .btn-cancel-appointment {
        background: #f0f0f0;
        color: #DC3545;
        border: none;
        padding: 8px 20px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
    }

    .no-appointment-card {
        background: white;
        border-radius: 12px;
        padding: 32px;
        text-align: center;
        margin-bottom: 28px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .no-appointment-icon {
        font-size: 3rem;
        color: #ccc;
        margin-bottom: 12px;
    }

    .no-appointment-text {
        font-size: 0.9rem;
        color: #888;
        margin-bottom: 16px;
    }

    .btn-create-request {
        background: #1B6B3A;
        color: white;
        padding: 8px 24px;
        border-radius: 6px;
        font-size: 0.8rem;
        text-decoration: none;
        display: inline-block;
    }

    .announcements-section {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .announcements-header {
        background: #F5C518;
        color: #1A1A1A;
        font-size: 0.9rem;
        font-weight: 700;
        padding: 12px 20px;
    }

    .announcements-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0;
    }

    .announce-card {
        padding: 16px;
        border-right: 1px solid #f0f0f0;
    }

    .announce-card:last-child {
        border-right: none;
    }

    .announce-card-header {
        font-size: 0.8rem;
        font-weight: 700;
        color: #1B6B3A;
        margin-bottom: 10px;
        text-transform: uppercase;
    }

    .announce-card-body {
        font-size: 0.85rem;
        line-height: 1.5;
        color: #444;
    }

    .rp-date-card {
        background: rgba(255,255,255,0.18);
        border-radius: 10px;
        padding: 16px;
        text-align: center;
        color: white;
        backdrop-filter: blur(8px);
        margin-bottom: 18px;
    }

    .rp-date-day { font-size: 2.8rem; font-weight: 700; line-height: 1; }
    .rp-date-month { font-size: 0.85rem; opacity: 0.85; margin-top: 2px; }
    .rp-date-time { font-size: 1rem; font-weight: 600; margin-top: 6px; }

    .rp-stat-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 9px 14px;
        border-bottom: 1px solid rgba(255,255,255,0.2);
        font-size: 0.82rem;
        color: white;
    }

    .rp-guide-step {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 9px 14px;
        border-bottom: 1px solid rgba(255,255,255,0.2);
        font-size: 0.78rem;
        color: rgba(255,255,255,0.92);
    }

    .rp-step-num {
        width: 20px;
        height: 20px;
        min-width: 20px;
        border-radius: 50%;
        background: #F5C518;
        color: #1A1A1A;
        font-size: 0.68rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* ── MOBILE DASHBOARD ── */
    @media (max-width: 768px) {
        .welcome-title {
            font-size: 1.8rem;
        }
        .welcome-subtitle {
            font-size: 0.85rem;
            line-height: 1.8;
        }
        .btn-hero-request {
            padding: 12px 30px;
            font-size: 0.9rem;
            width: 100%;
            text-align: center;
        }
        .appointment-details {
            grid-template-columns: 1fr;
        }
        .announcements-grid {
            grid-template-columns: 1fr;
        }
        .announce-card {
            border-right: none;
            border-bottom: 1px solid #f0f0f0;
        }
        .announce-card:last-child {
            border-bottom: none;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function cancelAppointment(appointmentId) {
        Swal.fire({
            title: 'Cancel Appointment?',
            text: 'Are you sure you want to cancel your appointment? You can book a new one.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DC3545',
            cancelButtonColor: '#1B6B3A',
            confirmButtonText: 'Yes, Cancel',
            cancelButtonText: 'No, Keep'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                const cancelUrl = "{{ route('student.appointments.cancel', ':id') }}";
                form.action = cancelUrl.replace(':id', appointmentId);
                form.innerHTML = '@csrf @method("DELETE")';
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function updateTime() {
        const now = new Date();
        let h = now.getHours();
        const m = String(now.getMinutes()).padStart(2,'0');
        const s = String(now.getSeconds()).padStart(2,'0');
        const ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        const el = document.getElementById('live-time');
        if (el) el.textContent = `${h}:${m}:${s} ${ampm}`;
    }
    updateTime();
    setInterval(updateTime, 1000);
</script>
@endpush
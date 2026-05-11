@extends('layouts.registrar')

@section('title', 'Appointment Calendar')

@section('content')

<div class="registrar-sticky-header">APPOINTMENT CALENDAR</div>

<div class="calendar-layout-wrapper">
    <div class="calendar-filters" style="margin-bottom: 20px;">
        <div class="calendar-filter-tabs" style="margin-bottom: 16px;">
            <div class="stats-overview-card active" data-status="all">
                <div class="stats-overview-icon"><i class="bi bi-grid"></i></div>
                <div class="stats-overview-info">
                    <div class="stats-overview-value">{{ $totalRequests }}</div>
                    <div class="stats-overview-label">All</div>
                </div>
            </div>
            <div class="stats-overview-card" data-status="pending">
                <div class="stats-overview-icon"><i class="bi bi-hourglass-split"></i></div>
                <div class="stats-overview-info">
                    <div class="stats-overview-value">{{ $pendingCount }}</div>
                    <div class="stats-overview-label">Pending</div>
                </div>
            </div>
            <div class="stats-overview-card" data-status="missed">
                <div class="stats-overview-icon"><i class="bi bi-calendar-x" style="color: #6c757d;"></i></div>
                <div class="stats-overview-info">
                    <div class="stats-overview-value">{{ $missedCount }}</div>
                    <div class="stats-overview-label">Missed</div>
                </div>
            </div>
            <div class="stats-overview-card" data-status="ready_for_pickup">
                <div class="stats-overview-icon"><i class="bi bi-box-seam"></i></div>
                <div class="stats-overview-info">
                    <div class="stats-overview-value">{{ $readyCount }}</div>
                    <div class="stats-overview-label">Ready</div>
                </div>
            </div>
            <div class="stats-overview-card" data-status="completed">
                <div class="stats-overview-icon"><i class="bi bi-check2-all"></i></div>
                <div class="stats-overview-info">
                    <div class="stats-overview-value">{{ $completedCount }}</div>
                    <div class="stats-overview-label">Completed</div>
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 16px; align-items: center; flex-wrap: wrap; width: 100%;">
            {{-- Search Bar --}}
            <div class="search-box-wrapper" style="flex: 1; min-width: 250px;">
                <div class="search-box" style="background: white; padding: 12px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); position: relative; display: flex; align-items: center;">
                    <i class="bi bi-search" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #6c757d;"></i>
                    <input type="text" id="calendarSearchInput" placeholder="Search by reference number or student name..." style="width: 100%; border: none; padding-left: 36px; background: transparent; box-shadow: none; outline: none; font-size: 0.95rem; color: #1A1A1A;">
                </div>
            </div>
        </div>
    </div>

    <div style="display: flex; gap: 20px; align-items: stretch; min-height: 600px;">
        {{-- Left Side: Calendar --}}
        <div style="flex: 5; min-width: 0;">
            <div class="calendar-container" style="height: 100%; display: flex; flex-direction: column;">
                <div id="calendar" style="flex: 1;"></div>
                <div class="time-slot-legend-horizontal" style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #E5E7EB;">
                    <div class="legend-item"><span class="legend-dot legend-dot-red"></span> <span class="legend-text">Pending</span></div>
                    <div class="legend-item"><span class="legend-dot legend-dot-yellow"></span> <span class="legend-text">Processing</span></div>
                    <div class="legend-item"><span class="legend-dot legend-dot-orange"></span> <span class="legend-text">Ready for Pickup</span></div>
                    <div class="legend-item"><span class="legend-dot legend-dot-green"></span> <span class="legend-text">Received</span></div>
                    <div class="legend-item"><span class="legend-dot" style="background:#3b82f6;"></span> <span class="legend-text">Completed</span></div>
                    <div class="legend-item"><span class="legend-dot legend-dot-missed" style="background:#6c757d;"></span> <span class="legend-text">Missed</span></div>
                    <div class="legend-item"><span class="legend-dot" style="background:#6B7280;"></span> <span class="legend-text">Declined</span></div>
                </div>
            </div>
        </div>

        {{-- Right Side: Time Slots --}}
        <div style="flex: 3; min-width: 0;">
            <div class="ccst-card" style="border: 1px solid #D0DDD0; height: 100%; display: flex; flex-direction: column; margin: 0;">
                <div class="ccst-card-header blue" style="display: flex; justify-content: space-between; align-items: center; background: #1A9FE0;">
                    <span><i class="bi bi-clock-history me-2"></i> Time Slots</span>
                    <button class="btn-add-timeslot" onclick="openTimeSlotModal()" style="background: none; border: none; color: white; cursor: pointer; font-size: 1.1rem; padding: 0 4px;">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>
                <div class="ccst-card-body p-0" id="timeSlotsList" style="flex: 1; overflow-y: auto;">
                    <div class="rp-stat-row text-muted" style="padding: 14px 16px; color: #666 !important;">Loading time slots...</div>
                </div>
            </div>
        </div>
    </div>
</div>



{{-- Appointment Details Modal --}}
<div id="appointmentModal" class="modal-overlay" style="display:none;">
    <div class="modal-container" style="max-width: 450px;">
        <div class="modal-header-custom">
            <h4><i class="bi bi-calendar-check me-2"></i>Appointment Details</h4>
            <button type="button" class="modal-close" onclick="closeAppointmentModal()">&times;</button>
        </div>
        <div class="modal-body-custom">
            <div class="detail-row">
                <label>Student Name:</label>
                <span id="modalStudentName"></span>
            </div>
            <div class="detail-row">
                <label>Student Number:</label>
                <span id="modalStudentNumber"></span>
            </div>
            <div class="detail-row">
                <label>Reference No.:</label>
                <span id="modalReferenceNo"></span>
            </div>
            <div class="detail-row">
                <label>Amount:</label>
                <span id="modalAmount"></span>
            </div>
            <div class="detail-row">
                <label>Time Slot:</label>
                <span id="modalTimeSlot"></span>
            </div>
            <div class="detail-row">
                <label>Status:</label>
                <span id="modalStatus" class="status-badge"></span>
            </div>
        </div>
        <div class="modal-footer-custom" id="modalActions">
            <button type="button" class="btn-complete" onclick="markCompleted()">Mark Completed</button>
            <button type="button" class="btn-missed" onclick="markMissed()">Mark Missed</button>
        </div>
    </div>
</div>

{{-- Hidden forms for actions --}}
<form id="completeForm" method="POST" style="display:none;">
    @csrf
    @method('PATCH')
</form>

<form id="missedForm" method="POST" style="display:none;">
    @csrf
    @method('PATCH')
</form>

{{-- Day Detail Modal --}}
<div id="dayModal" class="modal-overlay" style="display:none;">
    <div class="modal-container" style="max-width:520px;">
        <div class="modal-header-custom" style="background:#1B6B3A;">
            <h4><i class="bi bi-calendar3 me-2"></i><span id="dayModalTitle">Requests</span></h4>
            <button type="button" class="modal-close" onclick="closeDayModal()">&times;</button>
        </div>
        <div class="modal-body-custom" style="max-height:420px;overflow-y:auto;" id="dayModalBody">
            <p class="text-muted">Loading...</p>
        </div>
    </div>
</div>

{{-- Time Slot Modal --}}
<div id="timeSlotModal" class="modal-overlay" style="display:none;">
    <div class="modal-container" style="max-width: 450px;">
        <div class="modal-header-custom">
            <h4 id="timeSlotModalTitle"><i class="bi bi-plus-circle me-2"></i>Add Time Slot</h4>
            <button type="button" class="modal-close" onclick="closeTimeSlotModal()">&times;</button>
        </div>
        <form id="timeSlotForm">
            @csrf
            <input type="hidden" id="timeSlotId" name="id" value="">
            <div class="modal-body-custom">
                <div class="form-group">
                    <label>Slot Label <span class="required">*</span></label>
                    <input type="text" name="label" id="slotLabel" class="form-input" placeholder="e.g., 8:00 AM - 9:00 AM" required>
                </div>
                <div class="form-row-2">
                    <div class="form-group">
                        <label>Start Time <span class="required">*</span></label>
                        <input type="time" name="start_time" id="slotStartTime" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label>End Time <span class="required">*</span></label>
                        <input type="time" name="end_time" id="slotEndTime" class="form-input" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Max Capacity <span class="required">*</span></label>
                    <input type="number" name="max_capacity" id="slotMaxCapacity" class="form-input" value="5" min="1" max="20" required>
                    <small class="form-hint">Maximum number of appointments per time slot per day</small>
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn-cancel-modal" onclick="closeTimeSlotModal()">Cancel</button>
                <button type="submit" class="btn-save-modal">Save Time Slot</button>
            </div>
        </form>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="deleteSlotModal" class="modal-overlay" style="display:none;">
    <div class="modal-container" style="max-width: 400px;">
        <div class="modal-header-custom" style="background:#DC3545;">
            <h4><i class="bi bi-exclamation-triangle me-2"></i>Delete Time Slot</h4>
            <button type="button" class="modal-close" onclick="closeDeleteSlotModal()">&times;</button>
        </div>
        <div class="modal-body-custom">
            <p>Are you sure you want to delete this time slot?</p>
            <p class="text-muted" style="font-size:0.75rem;">This action cannot be undone.</p>
        </div>
        <div class="modal-footer-custom">
            <button type="button" class="btn-cancel-modal" onclick="closeDeleteSlotModal()">Cancel</button>
            <button type="button" class="btn-delete-confirm" id="confirmDeleteSlotBtn">Delete Permanently</button>
        </div>
    </div>
</div>

@endsection

@section('right-panel')
   

    


@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<style>
    .registrar-sticky-header {
        background: #1B6B3A;
        color: white;
        font-size: 0.9rem;
        font-weight: 700;
        text-align: center;
        padding: 10px 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        position: relative; /* Changed to relative so it scrolls with content */
        margin-bottom: 20px;
        margin-left: -24px;
        margin-right: -24px;
        margin-top: -28px;
        width: calc(100% + 48px);
    }

    .calendar-layout-wrapper {
        width: 100%;
        max-width: 100%;
        overflow-x: hidden;
    }

    .calendar-title {
        font-family: 'Volkhov', serif;
        font-size: 1.5rem;
        font-weight: 700;
        color: #1A1A1A;
        margin: 0;
    }

    .calendar-user-info {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 14px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }

    .user-avatar-small {
        display: none;
    }

    .user-avatar-small img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .user-display-name {
        font-size: 0.9rem;
        font-weight: 600;
        color: #1A1A1A;
    }

    .calendar-filters {
        display: flex;
        flex-direction: column;
        gap: 18px;
        margin-bottom: 18px;
    }

    .filter-search-row {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        justify-content: space-between;
        align-items: center;
    }

    .filter-search-row .filter-search {
        flex: 1 1 320px;
        min-width: 240px;
    }

    .filter-search-row .year-month-selector {
        flex: 0 0 auto;
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .calendar-filter-tabs {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 14px;
        width: 100%;
    }

    .stats-overview-card {
        background: white;
        border: 1px solid transparent;
        border-radius: 14px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 1px 6px rgba(0,0,0,0.08);
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }

    .stats-overview-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.12);
    }

    .stats-overview-card.active {
        background: #1B6B3A;
        color: white;
        border-color: #166534;
        box-shadow: 0 6px 16px rgba(0,0,0,0.14);
    }

    .stats-overview-card.active .stats-overview-icon,
    .stats-overview-card.active .stats-overview-value,
    .stats-overview-card.active .stats-overview-label {
        color: white;
    }

    .stats-overview-card.active .stats-overview-icon i {
        color: white !important;
    }

    .stats-overview-icon {
        font-size: 1.35rem;
        color: #1B6B3A;
    }

    .stats-overview-value {
        font-size: 1.4rem;
        font-weight: 800;
        color: #1A1A1A;
        line-height: 1.2;
    }

    .stats-overview-label {
        font-size: 0.72rem;
        color: #6B7280;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .stats-overview-card.active .stats-overview-label {
        color: rgba(255,255,255,0.85);
    }

    .filter-chip.pending { background: #FEE2E2; border-color: #FECACA; color: #991B1B; }
    .filter-chip.approved { background: #FFF7ED; border-color: #FED7AA; color: #C2410C; }
    .filter-chip.processing { background: #FEF3C7; border-color: #FDE68A; color: #92400E; }
    .filter-chip.ready { background: #DCFCE7; border-color: #BBF7D0; color: #166534; }
    .filter-chip.completed { background: #DBEAFE; border-color: #BFDBFE; color: #1D4ED8; }
    .filter-chip.declined { background: #E5E7EB; border-color: #D1D5DB; color: #374151; }

    .filter-search {
        min-width: 320px;
        flex: 1 1 320px;
        display: flex;
        justify-content: flex-end;
    }

    .filter-search input {
        width: 100%;
        max-width: 360px;
        padding: 10px 14px;
        border-radius: 999px;
        border: 1px solid #D1D5DB;
        font-size: 0.9rem;
        outline: none;
    }

    .filter-search input:focus {
        border-color: #1A9FE0;
        box-shadow: 0 0 0 4px rgba(26, 159, 224, 0.12);
    }

    .filter-controls {
        display: flex;
        gap: 12px;
        align-items: center;
        flex: 1;
        justify-content: flex-end;
    }

    .year-month-selector {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .year-select, .month-select {
        padding: 8px 12px;
        border: 1px solid #D1D5DB;
        border-radius: 6px;
        font-size: 0.85rem;
        font-family: 'Poppins', sans-serif;
        background: white;
        min-width: 100px;
    }

    .year-select {
        min-width: 80px;
    }

    .btn-print-list {
        background: #1B6B3A;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .calendar-container {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        min-height: 600px;
    }

    #calendar {
        min-height: 560px;
    }

    /* FullCalendar Customization */
    .fc {
        font-family: 'Poppins', sans-serif;
    }

    .fc-toolbar {
        display: flex;
        justify-content: center;
    }

    .fc-toolbar-chunk {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .fc-toolbar-chunk:first-child,
    .fc-toolbar-chunk:last-child {
        display: none;
    }

    .fc-toolbar-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1B6B3A;
        margin: 0 20px !important;
        text-transform: uppercase;
        letter-spacing: 1px;
        display: inline-block;
        vertical-align: middle;
    }

    .fc-toolbar {
        display: flex;
        justify-content: center !important;
        margin-bottom: 25px !important;
    }

    .fc-toolbar-chunk {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .fc-button-primary, 
    .fc-button-primary:hover, 
    .fc-button-primary:active, 
    .fc-button-primary:focus,
    .fc-button {
        background: transparent !important;
        border: none !important;
        color: #1B6B3A !important;
        box-shadow: none !important;
        padding: 0 10px !important;
        font-size: 1.5rem !important;
        font-weight: 700 !important;
        outline: none !important;
    }

    .fc-button-primary:hover {
        color: #166534 !important;
        transform: scale(1.1);
        transition: transform 0.2s;
    }

    .fc-day-today {
        background-color: #F0F7F0 !important;
    }

    /* Legend */
    .legend-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 14px;
        border-bottom: 1px solid rgba(255,255,255,0.2);
    }

    .legend-color {
        width: 16px;
        height: 16px;
        border-radius: 4px;
    }

    /* Modal */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1050;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-container {
        background: white;
        border-radius: 12px;
        width: 90%;
        max-width: 450px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    }

    .modal-header-custom {
        background: #1B6B3A;
        color: white;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-body-custom {
        padding: 20px;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        font-size: 0.85rem;
    }

    .detail-row label {
        font-weight: 700;
        color: #555;
    }

    .modal-footer-custom {
        padding: 15px 20px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        border-top: 1px solid #f0f0f0;
    }

    .btn-complete {
        background: #1B6B3A;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
    }

    .btn-missed {
        background: #DC3545;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
    }

    .status-badge {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .status-scheduled { background: #FFF3CD; color: #856404; }
    .status-completed { background: #D4EDDA; color: #155724; }
    .status-missed { background: #F8D7DA; color: #721C24; }
    .status-cancelled { background: #F0F0F0; color: #888; }

   

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

    /* Time Slot Items */
    .time-slot-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 16px;
        border-bottom: 1px solid #f0f0f0;
        gap: 10px;
    }

    .time-slot-info {
        flex: 1;
    }

    .time-slot-label {
        font-size: 0.85rem;
        color: #1A1A1A;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .time-slot-meta {
        font-size: 0.75rem;
        color: #666;
    }

    .status-dots-container {
        display: inline-flex;
        gap: 4px;
        margin-left: 8px;
        align-items: center;
    }

    .status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
    }

    .status-dot-red {
        background-color: #DC3545; /* Red for pending/cancelled */
    }

    .status-dot-yellow {
        background-color: #FFC107; /* Yellow for processing */
    }

    .status-dot-orange {
        background-color: #FD7E14; /* Orange for ready for pickup */
    }

    .status-dot-green {
        background-color: #28A745; /* Green for received */
    }

    .time-slot-legend-horizontal {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
        background: white;
        border-radius: 8px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .legend-text {
        font-size: 0.75rem;
        font-weight: 600;
        color: #4B5563;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
    }

    .legend-dot-red { background: #DC3545; }
    .legend-dot-yellow { background: #FFC107; }
    .legend-dot-orange { background: #FD7E14; }
    .legend-dot-green { background: #28A745; }

    .time-slot-actions {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .slot-status {
        font-size: 0.6rem;
        padding: 2px 8px;
        border-radius: 20px;
        font-weight: 600;
    }

    .status-active {
        background: #D4EDDA;
        color: #155724;
    }

    .status-inactive {
        background: #F8D7DA;
        color: #721C24;
    }

    .slot-action-btn {
        background: #F0F0F0;
        border: 1px solid #DDD;
        color: #555;
        width: 28px;
        height: 28px;
        border-radius: 6px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .slot-action-btn:hover {
        background: #E0E0E0;
    }

    .slot-action-btn.edit-slot:hover {
        background: #1A9FE0;
        color: white;
        border-color: #0D7FBF;
    }

    .slot-action-btn.delete-slot:hover {
        background: #DC3545;
        color: white;
        border-color: #BD2130;
    }

    .slot-action-btn.toggle-slot:hover {
        background: #F5C518;
        color: #1A1A1A;
        border-color: #D4A710;
    }

    .btn-add-timeslot {
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        width: 28px;
        height: 28px;
        border-radius: 6px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .btn-add-timeslot:hover {
        background: rgba(255,255,255,0.4);
    }

    .btn-delete-confirm {
        background: #DC3545;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
    }

    .form-row-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 15px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        font-size: 0.7rem;
        font-weight: 700;
        color: #555;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 5px;
    }

    .form-input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #D0DDD0;
        border-radius: 6px;
        font-size: 0.8rem;
        font-family: 'Poppins', sans-serif;
    }

    .required {
        color: #DC3545;
    }

    .form-hint {
        font-size: 0.65rem;
        color: #888;
        margin-top: 4px;
        display: block;
    }

    .btn-cancel-modal {
        background: #f0f0f0;
        border: none;
        padding: 8px 20px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
    }

    .btn-save-modal {
        background: #1B6B3A;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
    }

    /* Request dot indicators on calendar cells */
    .req-dot-bar {
        display: flex;
        gap: 3px;
        flex-wrap: wrap;
        padding: 4px 5px 2px;
        justify-content: flex-start;
        margin-top: 2px;
    }
    .req-dot {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 0.65rem;
        font-weight: 700;
        color: white;
    }
    .request-summary-wrapper {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        margin-top: 4px;
    }
    .request-summary-badge {
        display: inline-block;
        font-size: 0.62rem;
        padding: 2px 5px;
        border-radius: 999px;
        color: white;
        font-weight: 700;
        line-height: 1.1;
    }
    .request-summary-pending { background: #DC3545; }
    .request-summary-processing { background: #F97316; }
    .request-summary-ready   { background: #22c55e; }
    .request-summary-completed { background: #3B82F6; }
    .request-summary-declined { background: #6B7280; }
    .calendar-error {
        padding: 30px;
        text-align: center;
        color: #4B5563;
        font-size: 0.95rem;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        background: #FAFAFA;
        margin: 10px 0;
    }
    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
    }

    /* Day modal student list */
    .day-status-section { margin-bottom: 16px; }
    .day-status-title {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 4px 10px;
        border-radius: 20px;
        display: inline-block;
        margin-bottom: 8px;
    }
    .day-status-title.pending    { background:#FEE2E2; color:#991B1B; }
    .day-status-title.processing { background:#FEF3C7; color:#92400E; }
    .day-status-title.ready      { background:#DCFCE7; color:#166534; }
    .day-status-title.done       { background:#DBEAFE; color:#1D4ED8; }
    .day-status-title.declined   { background:#E5E7EB; color:#374151; }
    .day-student-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 7px 12px;
        border-radius: 8px;
        margin-bottom: 5px;
        background: #f8f9fa;
        font-size: 0.8rem;
    }
    .day-student-name { font-weight: 600; color: #1A1A1A; }
    .day-student-ref  { font-size: 0.7rem; color: #888; }
    .no-requests-msg  { text-align:center; color:#aaa; padding: 24px 0; font-size:0.85rem; }

</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script>
    let calendar = null;
    let currentAppointmentId = null;
    let currentDeleteSlotId = null;
    let currentEditSlot = null;

    // ── Request-by-date data cache ────────────────────────────────────────────
    let requestsByDate = {}; // keyed by 'YYYY-MM-DD'
    let calendarStatusFilter = 'all';
    let calendarSearchTerm = '';

    function buildRequestUrl(start, end) {
        const params = new URLSearchParams({
            start,
            end,
        });
        if (calendarStatusFilter && calendarStatusFilter !== 'all') {
            params.set('status', calendarStatusFilter);
        }
        if (calendarSearchTerm.trim()) {
            params.set('search', calendarSearchTerm.trim());
        }
        return '{{ route("registrar.calendar.requests-by-date") }}' + '?' + params.toString();
    }

    function fetchRequestsByDate(start, end) {
        const url = buildRequestUrl(start, end);
        fetch(url, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
            .then(r => r.json())
            .then(data => {
                requestsByDate = {};
                data.forEach(d => { requestsByDate[d.date] = d; });
                renderRequestDots();
            })
            .catch(err => {
                console.error('Failed to load requests by date:', err);
                renderCalendarError('Unable to load document request summaries for this month.');
            });
    }

    function setCalendarStatusFilter(status) {
        calendarStatusFilter = status;
        document.querySelectorAll('.stats-overview-card').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.status === status);
        });
        if (calendar) {
            fetchRequestsByDate(calendar.view.activeStart.toISOString().slice(0,10), calendar.view.activeEnd.toISOString().slice(0,10));
        }
    }

    function setupCalendarFilters() {
        document.querySelectorAll('.stats-overview-card').forEach(btn => {
            btn.addEventListener('click', function() {
                setCalendarStatusFilter(this.dataset.status);
            });
        });

        const searchInput = document.getElementById('calendarSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', debounce(function(event) {
                calendarSearchTerm = event.target.value;
                if (calendar) {
                    fetchRequestsByDate(calendar.view.activeStart.toISOString().slice(0,10), calendar.view.activeEnd.toISOString().slice(0,10));
                }
            }, 250));
        }
    }



    function debounce(fn, delay) {
        let timer = null;
        return function(...args) {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), delay);
        };
    }

    function renderCalendarError(message) {
        const calEl = document.getElementById('calendar');
        if (!calEl) return;
        calEl.innerHTML = '<div class="calendar-error">' + escapeHtml(message) + '</div>';
    }

    function renderRequestDots() {
        // Remove old dot bars and badges
        document.querySelectorAll('.req-dot-bar, .request-summary-badge').forEach(el => el.remove());

        Object.entries(requestsByDate).forEach(([date, info]) => {
            const dayCell = document.querySelector('.fc-daygrid-day[data-date="' + date + '"]');
            if (!dayCell) return;

            const eventsContainer = dayCell.querySelector('.fc-daygrid-day-events') || dayCell.querySelector('.fc-daygrid-day-frame');
            if (!eventsContainer) return;

            const dots = [];
            if (info.pending.length) dots.push('<span class="req-dot" style="background:#DC3545;" title="Pending">' + info.pending.length + '</span>');
            if (info.approved && info.approved.length) dots.push('<span class="req-dot" style="background:#F97316;" title="Approved">' + info.approved.length + '</span>');
            if (info.processing.length) dots.push('<span class="req-dot" style="background:#F97316;" title="Processing">' + info.processing.length + '</span>');
            if (info.ready.length)   dots.push('<span class="req-dot" style="background:#22c55e;" title="Ready for Pickup">' + info.ready.length + '</span>');
            if (info.completed.length) dots.push('<span class="req-dot" style="background:#3b82f6;" title="Completed">' + info.completed.length + '</span>');
            if (info.missed && info.missed.length) dots.push('<span class="req-dot" style="background:#6c757d;" title="Missed">' + info.missed.length + '</span>');
            if (info.declined.length) dots.push('<span class="req-dot" style="background:#6B7280;" title="Declined">' + info.declined.length + '</span>');

            let dotsHtml = '<div class="req-dot-bar">' + dots.join('') + '</div>';

            if (dots.length) {
                eventsContainer.insertAdjacentHTML('beforeend', dotsHtml);
            }
        });
    }

    function openDayModal(dateStr) {
        const info = requestsByDate[dateStr];
        const label = new Date(dateStr + 'T00:00:00').toLocaleDateString('en-PH', { month:'long', day:'numeric', year:'numeric' });
        document.getElementById('dayModalTitle').textContent = label;

        let html = '';

        const hasRequests = info && (
            info.pending.length ||
            (info.approved && info.approved.length) ||
            info.processing.length ||
            info.ready.length ||
            info.completed.length ||
            (info.missed && info.missed.length) ||
            info.declined.length
        );

        if (!hasRequests) {
            html = '<div class="no-requests-msg"><i class="bi bi-inbox" style="font-size:2rem;"></i><br>No document requests on this date.</div>';
        } else {
            const renderSection = (title, cls, items) => {
                if (!items.length) return '';
                let s = `<div class="day-status-section"><span class="day-status-title ${cls}">${title} (${items.length})</span>`;
                items.forEach(st => {
                    s += `<div class="day-student-row">
                        <div>
                            <div class="day-student-name">${escapeHtml(st.name)}</div>
                            <div class="day-student-ref">${escapeHtml(st.sn)} &bull; ${escapeHtml(st.ref)}</div>
                        </div>
                    </div>`;
                });
                s += '</div>';
                return s;
            };
            html += renderSection('Pending', 'pending', info.pending);
            if (info.approved && info.approved.length) {
                html += renderSection('Approved', 'processing', info.approved);
            }
            html += renderSection('Processing', 'processing', info.processing);
            html += renderSection('Ready for Pickup', 'ready', info.ready);
            html += renderSection('Completed', 'done', info.completed);
            if (info.missed && info.missed.length) {
                html += renderSection('Missed', 'declined', info.missed);
            }
            html += renderSection('Declined', 'declined', info.declined);
        }

        document.getElementById('dayModalBody').innerHTML = html;
        document.getElementById('dayModal').style.display = 'flex';
    }

    function closeDayModal() {
        document.getElementById('dayModal').style.display = 'none';
    }

    // Load time slots with action buttons
    function loadTimeSlots() {
        fetch('{{ route("registrar.calendar.time-slots") }}')
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('timeSlotsList');
                if (data.length === 0) {
                    container.innerHTML = '<div class="rp-stat-row text-muted">No time slots configured. Click + to add.</div>';
                    return;
                }
                
                let html = '';
                data.forEach(slot => {
                    const statusClass = slot.is_active ? 'status-active' : 'status-inactive';
                    const statusText = slot.is_active ? 'Active' : 'Inactive';
                    
                    html += `
                        <div class="time-slot-item" data-id="${slot.id}">
                            <div class="time-slot-info">
                                <div class="time-slot-label">
                                    <strong>${escapeHtml(slot.label)}</strong>
                                </div>
                                <div class="time-slot-meta">Max: ${slot.max_capacity} students</div>
                            </div>
                            <div class="time-slot-actions">
                                <span class="slot-status ${statusClass}">${statusText}</span>
                                <button class="slot-action-btn edit-slot" onclick="editTimeSlot(${slot.id})" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="slot-action-btn toggle-slot" onclick="toggleTimeSlot(${slot.id})" title="Toggle Active">
                                    <i class="bi bi-power"></i>
                                </button>
                                <button class="slot-action-btn delete-slot" onclick="confirmDeleteSlot(${slot.id})" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    `;
                });
                container.innerHTML = html;
            });
    }

    // Helper function to escape HTML
    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    // Open Add Time Slot Modal
    function openTimeSlotModal() {
        currentEditSlot = null;
        document.getElementById('timeSlotModalTitle').innerHTML = '<i class="bi bi-plus-circle me-2"></i>Add Time Slot';
        document.getElementById('timeSlotId').value = '';
        document.getElementById('slotLabel').value = '';
        document.getElementById('slotStartTime').value = '';
        document.getElementById('slotEndTime').value = '';
        document.getElementById('slotMaxCapacity').value = '5';
        document.getElementById('timeSlotModal').style.display = 'flex';
    }

    // Edit Time Slot
    function editTimeSlot(id) {
        fetch(`/registrar/time-slots/${id}/data`)
            .then(res => res.json())
            .then(data => {
                currentEditSlot = data;
                document.getElementById('timeSlotModalTitle').innerHTML = '<i class="bi bi-pencil me-2"></i>Edit Time Slot';
                document.getElementById('timeSlotId').value = data.id;
                document.getElementById('slotLabel').value = data.label;
                document.getElementById('slotStartTime').value = data.start_time;
                document.getElementById('slotEndTime').value = data.end_time;
                document.getElementById('slotMaxCapacity').value = data.max_capacity;
                document.getElementById('timeSlotModal').style.display = 'flex';
            });
    }

    // Close Time Slot Modal
    function closeTimeSlotModal() {
        document.getElementById('timeSlotModal').style.display = 'none';
        document.getElementById('timeSlotForm').reset();
    }

    // Save Time Slot (Create or Update)
    const timeSlotForm = document.getElementById('timeSlotForm');
    if (timeSlotForm) {
        timeSlotForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const id = document.getElementById('timeSlotId').value;
            const formData = {
                label: document.getElementById('slotLabel').value,
                start_time: document.getElementById('slotStartTime').value,
                end_time: document.getElementById('slotEndTime').value,
                max_capacity: document.getElementById('slotMaxCapacity').value,
            };
        
        const url = id ? `/registrar/time-slots/${id}` : '{{ route("registrar.timeslots.store") }}';
        const method = id ? 'PUT' : 'POST';
        
        fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Success', data.message, 'success');
                closeTimeSlotModal();
                loadTimeSlots();
                // Refresh calendar events if view changed
                if (calendar) calendar.refetchEvents();
            } else {
                Swal.fire('Error', data.message || 'Failed to save time slot.', 'error');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            Swal.fire('Error', 'Failed to save time slot.', 'error');
        });
        });
    }

    // Toggle Time Slot Active Status
    function toggleTimeSlot(id) {
        fetch(`/registrar/time-slots/${id}/toggle`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Success', data.message, 'success');
                loadTimeSlots();
                if (calendar) calendar.refetchEvents();
            } else {
                Swal.fire('Error', data.message || 'Failed to toggle time slot.', 'error');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            Swal.fire('Error', 'Failed to toggle time slot.', 'error');
        });
    }

    // Confirm Delete Time Slot
    function confirmDeleteSlot(id) {
        currentDeleteSlotId = id;
        document.getElementById('deleteSlotModal').style.display = 'flex';
    }

    function closeDeleteSlotModal() {
        document.getElementById('deleteSlotModal').style.display = 'none';
        currentDeleteSlotId = null;
    }

    // Delete Time Slot
    const confirmDeleteSlotBtn = document.getElementById('confirmDeleteSlotBtn');
    if (confirmDeleteSlotBtn) {
        confirmDeleteSlotBtn.addEventListener('click', function() {
            if (!currentDeleteSlotId) return;
            
            fetch(`/registrar/time-slots/${currentDeleteSlotId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Deleted!', data.message, 'success');
                closeDeleteSlotModal();
                loadTimeSlots();
                if (calendar) calendar.refetchEvents();
            } else {
                Swal.fire('Error', data.message || 'Cannot delete this time slot.', 'error');
                closeDeleteSlotModal();
            }
        })
        .catch(err => {
            console.error('Error:', err);
            Swal.fire('Error', 'Failed to delete time slot.', 'error');
            closeDeleteSlotModal();
        });
        });
    }

    function closeAppointmentModal() {
        document.getElementById('appointmentModal').style.display = 'none';
        currentAppointmentId = null;
    }

    function markCompleted() {
        if (!currentAppointmentId) return;
        
        Swal.fire({
            title: 'Mark as Completed?',
            text: 'This appointment will be marked as completed.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1B6B3A',
            cancelButtonColor: '#DC3545',
            confirmButtonText: 'Yes, Complete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('completeForm');
                form.action = `/registrar/appointments/${currentAppointmentId}/complete`;
                form.submit();
            }
        });
    }

    function markMissed() {
        if (!currentAppointmentId) return;
        
        Swal.fire({
            title: 'Mark as Missed?',
            text: 'This appointment will be marked as missed (student no-show).',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DC3545',
            cancelButtonColor: '#1B6B3A',
            confirmButtonText: 'Yes, Mark as Missed',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('missedForm');
                form.action = `/registrar/appointments/${currentAppointmentId}/missed`;
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

    // ── FullCalendar init ─────────────────────────────────────────────────────
    function initCalendar() {
        try {
        const calEl = document.getElementById('calendar');
        if (!calEl) { console.error('Calendar element #calendar not found'); return; }
        if (typeof FullCalendar === 'undefined') {
            console.error('FullCalendar not loaded');
            renderCalendarError('Calendar script failed to load. Please refresh the page.');
            return;
        }
        calendar = new FullCalendar.Calendar(calEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: '',
                center: 'prev,title,next',
                right: ''
            },
            editable: true,
            eventSources: [
                {
                    url: '{{ route("registrar.calendar.appointments") }}',
                    method: 'GET',
                    failure: function() { console.error('Failed to load appointments'); }
                }
            ],
            datesSet: function(info) {
                fetchRequestsByDate(info.startStr.slice(0,10), info.endStr.slice(0,10));
            },
            eventDidMount: function(info) {
                // Re-render dots after events render
                setTimeout(renderRequestDots, 50);
            },
            dateClick: function(info) {
                openDayModal(info.dateStr);
            },
            eventClick: function(info) {
                info.jsEvent.preventDefault();
                const props = info.event.extendedProps;
                currentAppointmentId = info.event.id;

                document.getElementById('modalStudentName').textContent  = info.event.title;
                document.getElementById('modalStudentNumber').textContent = props.student_number;
                document.getElementById('modalReferenceNo').textContent  = props.reference_number;
                document.getElementById('modalAmount').textContent       = '₱' + parseFloat(props.amount).toFixed(2);
                document.getElementById('modalTimeSlot').textContent     = props.time_slot_label;

                const badge = document.getElementById('modalStatus');
                badge.textContent = props.status.charAt(0).toUpperCase() + props.status.slice(1).replace(/_/g,' ');
                badge.className = 'status-badge status-' + props.status;

                const actions = document.getElementById('modalActions');
                const completeBtn = actions.querySelector('.btn-complete');
                const missedBtn   = actions.querySelector('.btn-missed');
                if (props.status === 'scheduled') {
                    completeBtn.style.display = '';
                    missedBtn.style.display   = '';
                } else {
                    completeBtn.style.display = 'none';
                    missedBtn.style.display   = 'none';
                }

                document.getElementById('appointmentModal').style.display = 'flex';
            },
            eventDrop: function(info) {
                fetch('{{ route("registrar.calendar.reschedule", ["id" => "__ID__"]) }}'.replace('__ID__', info.event.id), {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ new_date: info.event.startStr.slice(0,10) })
                })
                .then(r => r.json())
                .then(d => {
                    if (!d.success) { info.revert(); Swal.fire('Error', d.message, 'error'); }
                    else { fetchRequestsByDate(calendar.view.activeStart.toISOString().slice(0,10), calendar.view.activeEnd.toISOString().slice(0,10)); }
                })
                .catch(() => { info.revert(); });
            }
        });
        calendar.render();
        setupCalendarFilters();
        loadTimeSlots();
        
        loadTimeSlots();
        } catch(e) {
            console.error('Calendar init error:', e);
            const calEl = document.getElementById('calendar');
            if (calEl) calEl.innerHTML = '<div class="calendar-error">Unable to load calendar. See browser console for details.</div>';
        }
    }
    // Hide print dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('printDropdown');
        const btn = document.querySelector('.btn-print-list');
        if (dropdown && dropdown.style.display === 'block' && !dropdown.contains(e.target) && !btn.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    // Print with range
    function printRange(range) {
        document.getElementById('printDropdown').style.display = 'none';
        window.open('{{ route("registrar.appointments.print-cashier-list") }}?range=' + range, '_blank');
    }
    
    // Run immediately — @stack('scripts') is at bottom of <body> so DOM is ready
    initCalendar();
</script>
@endpush
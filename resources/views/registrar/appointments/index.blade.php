@extends('layouts.registrar')

@section('title', 'Appointments Management')

@section('content')

<div class="registrar-sticky-header">APPOINTMENTS MANAGEMENT</div>

{{-- Filter Tabs - Full width row with uniform buttons --}}
<div class="filter-tabs-wrapper">
    <div class="filter-tabs">
        <button class="filter-tab active" data-tab="upcoming">Upcoming Appointments</button>
        <button class="filter-tab" data-tab="today">Today's Appointments</button>
        <button class="filter-tab" data-tab="all">All Appointments</button>
        <button class="filter-tab" data-tab="time-slots">Manage Time Slots</button>
    </div>
</div>

{{-- Upcoming Appointments View --}}
<div id="tab-upcoming" class="tab-content active">
    <div class="appointments-card">
        <div class="table-scroll-wrapper">
            <div class="table-scroll-body">
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <th style="width: 20%">Student Name</th>
                            <th style="width: 15%">Reference No.</th>
                            <th style="width: 20%">Appointment Date</th>
                            <th style="width: 20%">Time Slot</th>
                            <th style="width: 15%">Status</th>
                            <th style="width: 10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($upcomingAppointments as $appointment)
                        <tr>
                            <td style="width: 20%">{{ $appointment->student->name ?? 'N/A' }}</td>
                            <td style="width: 15%">
                                <a href="{{ route('registrar.requests.show', $appointment->document_request_id) }}" class="ref-link">
                                    {{ $appointment->documentRequest->reference_number ?? 'N/A' }}
                                </a>
                            </td>
                            <td style="width: 20%">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</td>
                            <td style="width: 20%">{{ $appointment->timeSlot->label ?? '—' }}</td>
                            <td style="width: 10%">
                                @php
                                    $statusClass = match($appointment->status) {
                                        'scheduled' => 'status-scheduled',
                                        'completed' => 'status-completed',
                                        'missed' => 'status-missed',
                                        'canceled' => 'status-canceled',
                                        default => 'status-scheduled',
                                    };
                                    $statusLabel = ucfirst($appointment->status);
                                @endphp
                                <span class="appointment-status {{ $statusClass }}">{{ $statusLabel }}</span>
                            </td>
                            <td style="width: 10%">
                                @if($appointment->status === 'scheduled')
                                    <div class="action-buttons">
                                        <button type="button" class="action-btn-complete" onclick="markCompleted({{ $appointment->id }})">
                                            <i class="bi bi-check-lg"></i> Complete
                                        </button>
                                        <button type="button" class="action-btn-missed" onclick="markMissed({{ $appointment->id }})">
                                            <i class="bi bi-clock"></i> Missed
                                        </button>
                                    </div>
                                @else
                                    <span class="badge-final">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No upcoming appointments found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Today's Appointments View --}}
<div id="tab-today" class="tab-content">
    <div style="margin-bottom: 15px; display: flex; gap: 12px;">
        <a href="{{ route('registrar.appointments.print-cashier-list', ['range' => 'today']) }}" target="_blank" class="btn-action" style="background: #1A9FE0; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 0.8rem; font-weight: 600;">
            <i class="bi bi-list-check"></i> Print Cashier List (Today)
        </a>
        <a href="{{ route('registrar.appointments.bulk-print-receipts', ['date' => today()->format('Y-m-d')]) }}" target="_blank" class="btn-action" style="background: #1B6B3A; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 0.8rem; font-weight: 600;">
            <i class="bi bi-printer-fill"></i> Print Today's Receipts
        </a>
    </div>
    <div class="appointments-card">
        <div class="table-scroll-wrapper">
            <div class="table-scroll-body">
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <th style="width: 30%">Student Name</th>
                            <th style="width: 25%">Reference No.</th>
                            <th style="width: 25%">Time Slot</th>
                            <th style="width: 10%">Status</th>
                            <th style="width: 10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($todayAppointments as $appointment)
                        <tr>
                            <td style="width: 30%">{{ $appointment->student->name ?? 'N/A' }}</td>
                            <td style="width: 25%">
                                <a href="{{ route('registrar.requests.show', $appointment->document_request_id) }}" class="ref-link">
                                    {{ $appointment->documentRequest->reference_number ?? 'N/A' }}
                                </a>
                            </td>
                            <td style="width: 25%">{{ $appointment->timeSlot->label ?? '—' }}</td>
                            <td style="width: 10%">
                                @php
                                    $statusClass = match($appointment->status) {
                                        'scheduled' => 'status-scheduled',
                                        'completed' => 'status-completed',
                                        'missed' => 'status-missed',
                                        'canceled' => 'status-canceled',
                                        default => 'status-scheduled',
                                    };
                                @endphp
                                <span class="appointment-status {{ $statusClass }}">{{ ucfirst($appointment->status) }}</span>
                            </td>
                            <td style="width: 10%">
                                @if($appointment->status === 'scheduled')
                                    <div class="action-buttons">
                                        <button type="button" class="action-btn-complete" onclick="markCompleted({{ $appointment->id }})">
                                            <i class="bi bi-check-lg"></i> Complete
                                        </button>
                                        <button type="button" class="action-btn-missed" onclick="markMissed({{ $appointment->id }})">
                                            <i class="bi bi-clock"></i> Missed
                                        </button>
                                    </div>
                                @else
                                    <span class="badge-final">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No appointments scheduled for today.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- All Appointments View --}}
<div id="tab-all" class="tab-content">
    <div class="appointments-card">
        <div class="table-scroll-wrapper">
            <div class="table-scroll-body">
                <table class="appointments-table" id="allAppointmentsTable">
                    <thead>
                        <tr>
                            <th style="width: 20%">Student Name</th>
                            <th style="width: 15%">Reference No.</th>
                            <th style="width: 20%">Appointment Date</th>
                            <th style="width: 20%">Time Slot</th>
                            <th style="width: 15%">Status</th>
                            <th style="width: 10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allAppointments as $appointment)
                        <tr>
                            <td style="width: 20%">{{ $appointment->student->name ?? 'N/A' }}</td>
                            <td style="width: 15%">
                                <a href="{{ route('registrar.requests.show', $appointment->document_request_id) }}" class="ref-link">
                                    {{ $appointment->documentRequest->reference_number ?? 'N/A' }}
                                </a>
                            </td>
                            <td style="width: 20%">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</td>
                            <td style="width: 20%">{{ $appointment->timeSlot->label ?? '—' }}</td>
                            <td style="width: 10%">
                                @php
                                    $statusClass = match($appointment->status) {
                                        'scheduled' => 'status-scheduled',
                                        'completed' => 'status-completed',
                                        'missed' => 'status-missed',
                                        'canceled' => 'status-canceled',
                                        default => 'status-scheduled',
                                    };
                                @endphp
                                <span class="appointment-status {{ $statusClass }}">{{ ucfirst($appointment->status) }}</span>
                            </td>
                            <td style="width: 10%">
                                @if($appointment->status === 'scheduled')
                                    <div class="action-buttons">
                                        <button type="button" class="action-btn-complete" onclick="markCompleted({{ $appointment->id }})">
                                            <i class="bi bi-check-lg"></i> Complete
                                        </button>
                                        <button type="button" class="action-btn-missed" onclick="markMissed({{ $appointment->id }})">
                                            <i class="bi bi-clock"></i> Missed
                                        </button>
                                    </div>
                                @else
                                    <span class="badge-final">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No appointments found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Manage Time Slots View --}}
<div id="tab-time-slots" class="tab-content">
    <div class="time-slots-card">
        <div class="card-header-custom">
            <span><i class="bi bi-clock-history me-2"></i>Time Slots Management</span>
            <button class="btn-add-slot" onclick="openAddSlotModal()">
                <i class="bi bi-plus-lg"></i> Add Time Slot
            </button>
        </div>
        <div class="table-scroll-wrapper">
            <div class="table-scroll-body">
                <table class="time-slots-table">
                    <thead>
                        <tr>
                            <th style="width: 25%">Label</th>
                            <th style="width: 20%">Start Time</th>
                            <th style="width: 20%">End Time</th>
                            <th style="width: 15%">Max Capacity</th>
                            <th style="width: 10%">Status</th>
                            <th style="width: 10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($timeSlots as $slot)
                        <tr>
                            <td style="width: 25%"><strong>{{ $slot->label }}</strong></td>
                            <td style="width: 20%">{{ \Carbon\Carbon::parse($slot->start_time)->format('g:i A') }}</td>
                            <td style="width: 20%">{{ \Carbon\Carbon::parse($slot->end_time)->format('g:i A') }}</td>
                            <td style="width: 15%">{{ $slot->max_capacity }}</td>
                            <td style="width: 10%">
                                <span class="slot-status {{ $slot->is_active ? 'status-active' : 'status-inactive' }}">
                                    {{ $slot->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td style="width: 10%">
                                <div class="action-buttons">
                                    <button type="button" class="action-btn-edit" onclick="openEditSlotModal({{ $slot->id }})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="action-btn-toggle" onclick="toggleSlotStatus({{ $slot->id }})">
                                        <i class="bi bi-power"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No time slots configured. Click "Add Time Slot" to create one.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Add/Edit Time Slot Modal --}}
<div id="slotModal" class="modal-overlay" style="display:none;">
    <div class="modal-container" style="max-width: 500px;">
        <div class="modal-header-custom">
            <h4 id="modalTitle"><i class="bi bi-plus-circle me-2"></i>Add Time Slot</h4>
            <button type="button" class="modal-close" onclick="closeSlotModal()">&times;</button>
        </div>
        <form id="slotForm" method="POST">
            @csrf
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
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_active" id="slotIsActive" value="1" checked>
                        <span>Active (visible to students)</span>
                    </label>
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn-cancel-modal" onclick="closeSlotModal()">Cancel</button>
                <button type="submit" class="btn-save-modal">Save Time Slot</button>
            </div>
        </form>
    </div>
</div>

<form id="completeForm" method="POST" style="display:none;">
    @csrf
    @method('PATCH')
</form>

<form id="missedForm" method="POST" style="display:none;">
    @csrf
    @method('PATCH')
</form>

<form id="toggleSlotForm" method="POST" style="display:none;">
    @csrf
    @method('PATCH')
</form>

@endsection

@section('right-panel')


    <div class="ccst-card mb-0">
        <div class="ccst-card-header blue">Appointment Stats</div>
        <div class="ccst-card-body p-0">
            <div class="rp-stat-row">
                <span><i class="bi bi-calendar-week me-2"></i> Upcoming</span>
                <strong>{{ $upcomingCount ?? 0 }}</strong>
            </div>
            <div class="rp-stat-row">
                <span><i class="bi bi-calendar-day me-2"></i> Today</span>
                <strong>{{ $todayCount ?? 0 }}</strong>
            </div>
            <div class="rp-stat-row" style="border-bottom:none;">
                <span><i class="bi bi-clock-history me-2"></i> Total Slots</span>
                <strong>{{ $timeSlots->count() ?? 0 }}</strong>
            </div>
        </div>
    </div>

    <div class="ccst-card mb-0">
        <div class="ccst-card-header yellow">Quick Tips</div>
        <div class="ccst-card-body p-0">
            <div class="rp-guide-step">
                <span class="rp-step-num">1</span>
                <span>Mark appointments as Completed when student picks up</span>
            </div>
            <div class="rp-guide-step">
                <span class="rp-step-num">2</span>
                <span>Mark as Missed if student doesn't show up</span>
            </div>
            <div class="rp-guide-step" style="border-bottom:none;">
                <span class="rp-step-num">3</span>
                <span>Deactivate time slots during holidays</span>
            </div>
        </div>
    </div>
@endsection

@push('styles')
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
        position: sticky;
        top: 0;
        z-index: 10;
        margin-bottom: 15px;
    }

    /* Filter Tabs - Full width wrapper */
    .filter-tabs-wrapper {
        margin-bottom: 15px;
    }

    .filter-tabs {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        background: white;
        padding: 8px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .filter-tab {
        flex: 1;
        min-width: 0;
        background: #f0f0f0;
        border: none;
        padding: 10px 16px;
        border-radius: 30px;
        font-size: 0.8rem;
        font-weight: 600;
        color: #666;
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
        white-space: nowrap;
    }

    .filter-tab:hover {
        background: #e0e0e0;
    }

    .filter-tab.active {
        background: #1B6B3A;
        color: white;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    /* Appointments Card */
    .appointments-card, .time-slots-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        display: flex;
        flex-direction: column;
        flex: 1;
        min-height: 0;
    }

    .card-header-custom {
        background: #1B6B3A;
        color: white;
        padding: 12px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 700;
    }

    .btn-add-slot {
        background: #F5C518;
        color: #1A1A1A;
        border: none;
        padding: 6px 16px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.2s;
    }

    .btn-add-slot:hover {
        opacity: 0.85;
    }

    /* Scrollable Table */
    .table-scroll-wrapper {
        flex: 1;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        min-height: 0;
    }

    .table-scroll-body {
        flex: 1;
        overflow-y: auto;
        max-height: calc(100vh - 280px);
    }

    .appointments-table, .time-slots-table {
        width: 100%;
        border-collapse: collapse;
    }

    .appointments-table thead, .time-slots-table thead {
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .appointments-table th, .time-slots-table th {
        background: #F0F7F0;
        padding: 12px 16px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #1B6B3A;
        text-align: center;
        border-bottom: 2px solid #D0DDD0;
    }

    .appointments-table td, .time-slots-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.8rem;
        vertical-align: middle;
        text-align: center;
    }

    .appointments-table tr:hover, .time-slots-table tr:hover {
        background: #f8fafb;
    }

    /* Make first column text left-aligned for better readability */
    .appointments-table td:first-child,
    .appointments-table th:first-child,
    .time-slots-table td:first-child,
    .time-slots-table th:first-child {
        text-align: left;
    }

    .ref-link {
        color: #1B6B3A;
        text-decoration: none;
        font-weight: 600;
    }

    .ref-link:hover {
        text-decoration: underline;
    }

    .claiming-badge {
        background: #F0F7F0;
        color: #1B6B3A;
        padding: 2px 8px;
        border-radius: 4px;
        font-family: monospace;
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .appointment-status {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .status-scheduled { background: #FFF3CD; color: #856404; }
    .status-completed { background: #D4EDDA; color: #155724; }
    .status-missed { background: #F8D7DA; color: #721C24; }
    .status-canceled { background: #F0F0F0; color: #888; }

    .slot-status {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .status-active { background: #D4EDDA; color: #155724; }
    .status-inactive { background: #F8D7DA; color: #721C24; }

    .action-buttons {
        display: flex;
        gap: 6px;
        justify-content: center;
    }

    .action-btn-complete {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        background: #1B6B3A;
        color: white;
        border: none;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }

    .action-btn-complete:hover {
        background: #0C5A2E;
    }

    .action-btn-missed {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        background: #DC3545;
        color: white;
        border: none;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }

    .action-btn-missed:hover {
        background: #b02a37;
    }

    .action-btn-edit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        background: #F5C518;
        color: #1A1A1A;
        border: none;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
        min-width: 38px;
    }

    .action-btn-edit:hover {
        background: #e6b800;
    }

    .action-btn-toggle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        background: #1A9FE0;
        color: white;
        border: none;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
        min-width: 38px;
    }

    .action-btn-toggle:hover {
        background: #0D7FBF;
    }

    .badge-final {
        color: #999;
        font-size: 0.7rem;
    }

    /* Modal Styles */
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
        max-width: 500px;
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

    .modal-header-custom h4 {
        margin: 0;
        font-size: 1rem;
    }

    .modal-close {
        background: none;
        border: none;
        color: white;
        font-size: 1.3rem;
        cursor: pointer;
    }

    .modal-body-custom {
        padding: 20px;
    }

    .modal-footer-custom {
        padding: 15px 20px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        border-top: 1px solid #f0f0f0;
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

    .required {
        color: #DC3545;
    }

    .form-input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #D0DDD0;
        border-radius: 6px;
        font-size: 0.8rem;
        font-family: 'Poppins', sans-serif;
    }

    .form-input:focus {
        outline: none;
        border-color: #1B6B3A;
    }

    .form-row-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .form-hint {
        font-size: 0.7rem;
        color: #888;
        margin-top: 4px;
        display: block;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }

    .checkbox-label input {
        width: 16px;
        height: 16px;
        cursor: pointer;
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

    /* Right Panel Styles */
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

    .text-center { text-align: center; }
    .py-4 { padding-top: 24px; padding-bottom: 24px; }
    .text-muted { color: #888; }

    /* Ensure no page scroll, only table scrolls */
    .main-content {
        overflow-y: hidden !important;
        display: flex;
        flex-direction: column;
        height: calc(100vh - var(--header-h) - var(--footer-h));
        padding-bottom: 20px;
    }

    .main-content > .registrar-sticky-header {
        flex-shrink: 0;
    }

    .main-content > .filter-tabs-wrapper {
        flex-shrink: 0;
    }

    .main-content > .tab-content {
        flex: 1;
        min-height: 0;
        display: none;
    }

    .main-content > .tab-content.active {
        display: flex;
        flex-direction: column;
    }

    .main-content > .tab-content > .appointments-card,
    .main-content > .tab-content > .time-slots-card {
        flex: 1;
        min-height: 0;
        display: flex;
        flex-direction: column;
    }
</style>
@endpush

@push('scripts')
<script>
    // Tab switching
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            const tabId = this.dataset.tab;
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.getElementById(`tab-${tabId}`).classList.add('active');
        });
    });

    // Mark appointment as completed
    function markCompleted(appointmentId) {
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
                form.action = `/registrar/appointments/${appointmentId}/complete`;
                form.submit();
            }
        });
    }

    // Mark appointment as missed
    function markMissed(appointmentId) {
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
                form.action = `/registrar/appointments/${appointmentId}/missed`;
                form.submit();
            }
        });
    }

    // Time Slot Modal functions
    let currentSlotId = null;

    function openAddSlotModal() {
        currentSlotId = null;
        document.getElementById('modalTitle').innerHTML = '<i class="bi bi-plus-circle me-2"></i>Add Time Slot';
        document.getElementById('slotForm').action = '{{ route("registrar.timeslots.store") }}';
        document.getElementById('slotLabel').value = '';
        document.getElementById('slotStartTime').value = '';
        document.getElementById('slotEndTime').value = '';
        document.getElementById('slotMaxCapacity').value = '5';
        document.getElementById('slotIsActive').checked = true;
        document.getElementById('slotModal').style.display = 'flex';
    }

    function openEditSlotModal(id) {
        currentSlotId = id;
        document.getElementById('modalTitle').innerHTML = '<i class="bi bi-pencil me-2"></i>Edit Time Slot';
        document.getElementById('slotForm').action = `/registrar/time-slots/${id}`;
        
        fetch(`/registrar/time-slots/${id}/data`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('slotLabel').value = data.label;
                document.getElementById('slotStartTime').value = data.start_time;
                document.getElementById('slotEndTime').value = data.end_time;
                document.getElementById('slotMaxCapacity').value = data.max_capacity;
                document.getElementById('slotIsActive').checked = data.is_active;
                document.getElementById('slotModal').style.display = 'flex';
            });
    }

    function closeSlotModal() {
        document.getElementById('slotModal').style.display = 'none';
    }

    function toggleSlotStatus(id) {
        Swal.fire({
            title: 'Toggle Status?',
            text: 'This will activate/deactivate the time slot.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1B6B3A',
            cancelButtonColor: '#DC3545',
            confirmButtonText: 'Yes, Toggle',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('toggleSlotForm');
                form.action = `/registrar/time-slots/${id}/toggle`;
                form.submit();
            }
        });
    }

    // Live clock

</script>
@endpush
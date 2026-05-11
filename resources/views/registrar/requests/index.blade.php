@extends('layouts.registrar')

@section('title', 'Manage Requests')

@section('content')

<div class="registrar-sticky-header">DOCUMENT REQUESTS</div>

<div class="search-box-wrapper" style="margin-bottom: 16px; width: 100%;">
    <div class="search-box" style="background: white; padding: 12px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); width: 100%;">
        <i class="bi bi-search"></i>
        <input type="text" id="searchInput" placeholder="Search by reference number or student name..." style="width: 100%; border: none; padding-left: 36px; background: transparent; box-shadow: none;">
    </div>
</div>

{{-- Stats Overview Cards acting as Filter Tabs --}}
<div class="stats-overview-row mb-3">
    <div class="stats-overview-card active" data-filter="all">
        <div class="stats-overview-icon"><i class="bi bi-folder2"></i></div>
        <div class="stats-overview-info">
            <div class="stats-overview-value">{{ $totalRequests ?? 0 }}</div>
            <div class="stats-overview-label">All Requests</div>
        </div>
    </div>
    <div class="stats-overview-card" data-filter="pending">
        <div class="stats-overview-icon"><i class="bi bi-hourglass-split"></i></div>
        <div class="stats-overview-info">
            <div class="stats-overview-value">{{ $pendingCount ?? 0 }}</div>
            <div class="stats-overview-label">Pending</div>
        </div>
    </div>
    <div class="stats-overview-card" data-filter="ready_for_pickup">
        <div class="stats-overview-icon"><i class="bi bi-box-seam"></i></div>
        <div class="stats-overview-info">
            <div class="stats-overview-value">{{ $readyCount ?? 0 }}</div>
            <div class="stats-overview-label">Ready for Pickup</div>
        </div>
    </div>
    <div class="stats-overview-card" data-filter="completed">
        <div class="stats-overview-icon"><i class="bi bi-check2-all"></i></div>
        <div class="stats-overview-info">
            <div class="stats-overview-value">{{ $completedCount ?? 0 }}</div>
            <div class="stats-overview-label">Completed</div>
        </div>
    </div>
    <div class="stats-overview-card" data-filter="missed">
        <div class="stats-overview-icon"><i class="bi bi-calendar-x" style="color: #6c757d;"></i></div>
        <div class="stats-overview-info">
            <div class="stats-overview-value">{{ $missedCount ?? 0 }}</div>
            <div class="stats-overview-label">Missed</div>
        </div>
    </div>
    <div class="stats-overview-card" data-filter="cancelled">
        <div class="stats-overview-icon"><i class="bi bi-x-circle" style="color: #DC3545;"></i></div>
        <div class="stats-overview-info">
            <div class="stats-overview-value">{{ $cancelledCount ?? 0 }}</div>
            <div class="stats-overview-label">Cancelled</div>
        </div>
    </div>
</div>

{{-- Requests Table with Scrollable Body --}}
<div class="requests-card">
    <div class="table-scroll-wrapper">
        <div class="table-scroll-body">
            <table class="requests-table" id="requestsTable">
                <thead>
                    <tr>
                        <th style="width: 10%">Reference No.</th>
                        <th style="width: 14%">Student Name</th>
                        <th style="width: 11%">Appointment Date</th>
                        <th style="width: 12%">Document Type(s)</th>
                        <th style="width: 9%">Total Amount</th>
                        <th style="width: 12%">Request Status</th>
                        <th style="width: 20%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                    @php
                        $isMissed = $request->appointment && $request->appointment->status === 'missed' && $request->status === 'pending';
                    @endphp
                    <tr data-status="{{ $request->status }}" data-missed="{{ $isMissed ? '1' : '0' }}">
                        <td style="width: 10%">
                            <strong>{{ $request->reference_number }}</strong>
                            @if($request->is_walk_in)
                                <br><span class="badge" style="background:#FFAA00; color:black; font-size: 0.65rem; margin-top:2px;">Walk-in</span>
                            @endif
                        </td>
                        <td style="width: 14%">{{ $request->full_name }}</td>
                        <td style="width: 11%">
                            @if($request->is_walk_in)
                                {{ $request->created_at->format('M d, Y') }}
                            @elseif($request->appointment)
                                {{ \Carbon\Carbon::parse($request->appointment->appointment_date)->format('M d, Y') }}
                            @else
                                <span class="text-muted">No Appointment</span>
                            @endif
                        </td>
                        <td style="width: 12%">
                            <div class="document-types">
                                @foreach($request->items as $item)
                                    <span class="doc-type-badge" title="{{ $item->documentType->name }}">{{ $item->documentType->code }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td style="width: 9%">₱{{ number_format($request->total_fee, 2) }}</td>
                        <td style="width: 12%">
                            @php
                                $statusLabel = $request->status;
                                $statusClass = 'req-pending';
                                
                                if ($request->appointment && $request->appointment->status === 'missed' && $request->status === 'pending') {
                                    $statusLabel = 'Missed Appointment';
                                    $statusClass = 'req-missed';
                                } else {
                                    $statusInfo = match($request->status) {
                                        'pending' => ['label' => 'Pending', 'class' => 'req-pending'],
                                        'ready_for_pickup' => ['label' => 'Ready for Pickup', 'class' => 'req-ready'],
                                        'completed' => ['label' => 'Completed', 'class' => 'req-completed'],
                                        'cancelled' => ['label' => 'Cancelled', 'class' => 'req-cancelled'],
                                        default => ['label' => $request->status, 'class' => 'req-pending'],
                                    };
                                    $statusLabel = $statusInfo['label'];
                                    $statusClass = $statusInfo['class'];
                                }
                            @endphp
                            <span class="req-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                        </td>
                        <td style="width: 20%">
                            <div class="action-buttons">
                                {{-- Completed -> Details --}}
                                @if($request->status === 'completed')
                                    <a href="{{ route('registrar.requests.show', $request->id) }}" class="action-btn-details" title="View Details">
                                        <i class="bi bi-info-circle"></i> Details
                                    </a>
                                {{-- Ready for Pickup -> Print Documents --}}
                                @elseif($request->status === 'ready_for_pickup')
                                    <a href="{{ route('registrar.requests.show', $request->id) }}" class="action-btn-print">
                                        <i class="bi bi-printer"></i> Print Documents
                                    </a>
                                {{-- Pending & Non-Printable -> Mark as Ready --}}
                                @elseif($request->status === 'pending' && !$request->is_printable)
                                    <button type="button" class="action-btn-ready" onclick="markAsReady({{ $request->id }}, '{{ $request->reference_number }}')">
                                        <i class="bi bi-check-circle"></i> Mark as Ready
                                    </button>
                                {{-- Default fallback (e.g. Received/Cancelled/Other Pending) --}}
                                @else
                                    <a href="{{ route('registrar.requests.show', $request->id) }}" class="action-btn-details">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No document requests found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>
</div>



{{-- Hidden forms for status updates --}}
<form id="statusForm" method="POST" style="display:none;">
    @csrf
    @method('PATCH')
    <input type="hidden" name="status" id="statusValue">
</form>

<form id="receivedForm" method="POST" style="display:none;">
    @csrf
    @method('PATCH')
</form>

@endsection

@section('right-panel')

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

    .search-box {
        position: relative;
        display: flex;
        align-items: center;
        flex: 1;
        min-width: 250px;
    }

    .search-box i {
        position: absolute;
        left: 12px;
        color: #999;
    }

    .search-box input {
        width: 100%;
        padding: 8px 12px 8px 36px;
        border: 1px solid #D0DDD0;
        border-radius: 8px;
        font-size: 0.8rem;
        font-family: 'Poppins', sans-serif;
    }

    .search-box input:focus {
        outline: none;
        border-color: #1B6B3A;
    }

    /* Requests Card */
    .requests-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        display: flex;
        flex-direction: column;
        flex: 1;
        min-height: 0;
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

    .requests-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 900px;
    }

    .requests-table thead {
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .requests-table th {
        background: #F0F7F0;
        padding: 8px 6px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #1B6B3A;
        text-align: center;
        border-bottom: 2px solid #D0DDD0;
    }

    .requests-table td {
        padding: 8px 6px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.8rem;
        vertical-align: middle;
        text-align: center;
    }

    .requests-table tr:hover {
        background: #f8fafb;
    }



    /* Method Badges */
    .method-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .method-gcash { background: #E8F4FD; color: #0969A2; }
    .method-bank { background: #FFF3CD; color: #856404; }
    .method-cash { background: #D4EDDA; color: #155724; }
    .method-na { background: #F0F0F0; color: #888; }

    /* Status Badges */
    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.68rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .status-waiting { background: #F0F0F0; color: #888; }
    .status-pending { background: #FFF3CD; color: #856404; }
    .status-rejected { background: #F8D7DA; color: #721C24; }
    .status-verified { background: #D4EDDA; color: #155724; }
    .status-cancelled { background: #F0F0F0; color: #888; text-decoration: line-through; }

    /* Request Status Badges */
    .req-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.68rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .req-pending { background: #FFF3CD; color: #856404; }
    .req-verified { background: #CCE5FF; color: #004085; }
    .req-processing { background: #E8F4FD; color: #0969A2; }
    .req-ready { background: #D4EDDA; color: #155724; font-weight: 800; }
    .req-received { background: #F0F0F0; color: #1A1A1A; font-weight: 800; }
    .req-cancelled { background: #F0F0F0; color: #888; text-decoration: line-through; }

    .claiming-hint {
        font-size: 0.6rem;
        color: #1B6B3A;
        margin-top: 4px;
        font-family: monospace;
        letter-spacing: 0.5px;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 6px;
        align-items: center;
    }

    .action-btn-view {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        background: #1A9FE0;
        color: white;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 600;
        text-decoration: none;
        width: 90%;
        min-width: 100px;
    }

    .action-btn-view:hover {
        background: #0D7FBF;
        color: white;
    }

    .action-btn-generate {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        background: #1B6B3A;
        color: white;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 600;
        text-decoration: none;
        width: 90%;
        min-width: 100px;
    }

    .action-btn-generate:hover {
        background: #0C5A2E;
        color: white;
    }

    .action-btn-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        background: #F5C518;
        color: #1A1A1A;
        border: none;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 600;
        cursor: pointer;
        width: 90%;
        min-width: 100px;
        transition: all 0.2s;
    }

    .action-btn-status:hover {
        background: #e6b800;
    }

    .status-dropdown {
        position: relative;
    }

    .status-dropdown-menu {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        border: 1px solid #D0DDD0;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 100;
        min-width: 170px;
        margin-top: 4px;
    }

    .status-dropdown-menu.show {
        display: block;
    }

    .status-option {
        display: flex;
        align-items: center;
        gap: 8px;
        width: 100%;
        padding: 8px 12px;
        border: none;
        background: white;
        font-size: 0.75rem;
        font-weight: 500;
        color: #333;
        cursor: pointer;
        text-align: left;
        transition: background 0.2s;
    }

    .status-option:hover {
        background: #f0f7f0;
    }

    .status-option i {
        font-size: 0.8rem;
    }

    .status-cancel {
        color: #DC3545;
        border-top: 1px solid #f0f0f0;
    }

    .status-cancel:hover {
        background: #F8D7DA;
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

    .main-content > .requests-card {
        flex: 1;
        min-height: 0;
        margin-bottom: 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .filter-tabs {
            flex-wrap: wrap;
        }
        
        .search-box {
            width: 100%;
        }
    }

    .stats-overview-row {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 24px;
        justify-content: space-between;
    }

    .stats-overview-card {
        flex: 1;
        min-width: 120px;
        background: white;
        border-radius: 12px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }

    .stats-overview-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }

    .stats-overview-card.active {
        background: #1B6B3A;
        color: white;
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
        font-size: 0.65rem;
        color: #666;
        text-transform: uppercase;
    }

    .action-btn-details {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        background: #6c757d;
        color: white;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 600;
        text-decoration: none;
        width: 90%;
        min-width: 100px;
    }
    .action-btn-details:hover { background: #5a6268; color: white; }

    .action-btn-ready {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        background: #17a2b8;
        color: white;
        border: none;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 600;
        width: 90%;
        min-width: 100px;
        cursor: pointer;
    }
    .action-btn-ready:hover { background: #138496; }

    .action-btn-print {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        background: #1A9FE0;
        color: white;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 600;
        text-decoration: none;
        width: 90%;
        min-width: 100px;
    }
    .action-btn-print:hover { background: #0D7FBF; color: white; }

    .action-btn-view {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        background: #6c757d;
        color: white;
        border: none;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 600;
        width: 90%;
        min-width: 100px;
        cursor: pointer;
    }
    .action-btn-view:hover { background: #5a6268; color: white; }

    .action-btn-generate {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        background: #ffc107;
        color: #212529;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 0.65rem;
        font-weight: 700;
        text-decoration: none;
        width: 90%;
        min-width: 100px;
    }
    .action-btn-generate:hover { background: #e0a800; color: #212529; }

    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 4px;
        width: 100%;
        align-items: center;
    }

    .document-types {
        display: flex;
        flex-direction: column;
        gap: 4px;
        align-items: center;
    }

    .doc-type-badge {
        display: inline-block;
        background: #F0F7F0;
        color: #1B6B3A;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.65rem;
        font-weight: 600;
    }

    .req-pending { background: #FFF3CD; color: #856404; }
    .req-ready { background: #F5A623; color: white; font-weight: 800; }
    .req-completed { background: #1B6B3A; color: white; font-weight: 800; }
    .req-cancelled { background: #DC3545; color: white; }
    .req-missed { background: #6c757d; color: white; }
</style>
@endpush

@push('scripts')
<script>
    // Filter tabs functionality using stats cards
    document.querySelectorAll('.stats-overview-card').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.stats-overview-card').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.dataset.filter;
            const rows = document.querySelectorAll('#requestsTable tbody tr');
            
            rows.forEach(row => {
                const status = row.getAttribute('data-status');
                const isMissed = row.getAttribute('data-missed') === '1';

                if (filter === 'all') {
                    row.style.display = '';
                } else if (filter === 'missed') {
                    row.style.display = isMissed ? '' : 'none';
                } else if (filter === 'pending') {
                    // Match the controller's definition of pending, excluding missed
                    const pendingStatuses = ['pending', 'payment_method_set', 'payment_uploaded', 'payment_rejected'];
                    row.style.display = (pendingStatuses.includes(status) && !isMissed) ? '' : 'none';
                } else {
                    row.style.display = (status === filter && !isMissed) ? '' : 'none';
                }
            });
        });
    });

    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#requestsTable tbody tr');
        
        rows.forEach(row => {
            const refNumber = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
            const studentName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            
            if (refNumber.includes(searchTerm) || studentName.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Toggle status dropdown
    function toggleStatusDropdown(requestId) {
        const dropdown = document.getElementById(`status-dropdown-${requestId}`);
        document.querySelectorAll('.status-dropdown-menu').forEach(menu => {
            if (menu.id !== `status-dropdown-${requestId}`) {
                menu.classList.remove('show');
            }
        });
        dropdown.classList.toggle('show');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.status-dropdown')) {
            document.querySelectorAll('.status-dropdown-menu').forEach(menu => {
                menu.classList.remove('show');
            });
        }
    });

    // Update request status
    function updateStatus(requestId, newStatus) {
        let statusText = '';
        switch(newStatus) {
            case 'completed': 
                statusText = 'mark this request as completed'; 
                break;
            case 'ready_for_pickup': 
                statusText = 'mark this request as ready for pickup'; 
                break;
            case 'cancelled': 
                statusText = 'cancel this request'; 
                break;
            default: 
                statusText = 'update this request';
        }
        
        Swal.fire({
            title: 'Confirm Status Update',
            text: `Are you sure you want to ${statusText}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1B6B3A',
            cancelButtonColor: '#DC3545',
            confirmButtonText: 'Yes, Update',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('statusForm');
                form.action = `/registrar/requests/${requestId}/status`;
                document.getElementById('statusValue').value = newStatus;
                form.submit();
            }
        });
    }

    // Print request details
    function printRequest(requestId) {
        // Open the request show page in a new window and trigger print
        const printWindow = window.open(`/registrar/requests/${requestId}`, '_blank');
        printWindow.onload = function() {
            printWindow.print();
        };
    }

    // Mark as completed (auto-marks payment as paid)
    function markAsReady(requestId, refNo) {
        Swal.fire({
            title: 'Mark as Ready?',
            text: `Are you sure you want to mark request ${refNo} as ready for pickup? This will notify the student to claim their documents.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1B6B3A',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Mark as Ready',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Use fetch for AJAX request
                const url = `{{ route('registrar.requests.mark-ready', ':id') }}`.replace(':id', requestId);
                fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Success', data.message, 'success').then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Error', data.message || 'Something went wrong', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'An unexpected error occurred.', 'error');
                });
            }
        });
    }

    function viewDocumentSummary(requestId) {
        window.location.href = `/registrar/requests/${requestId}`;
    }

    // Live clock
    function updateTime() {
        const now = new Date();
        let h = now.getHours();
        const m = String(now.getMinutes()).padStart(2, '0');
        const s = String(now.getSeconds()).padStart(2, '0');
        const ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        const el = document.getElementById('live-time');
        if (el) el.textContent = `${h}:${m}:${s} ${ampm}`;
    }
    updateTime();
    setInterval(updateTime, 1000);
</script>
@endpush
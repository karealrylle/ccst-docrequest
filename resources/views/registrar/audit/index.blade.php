@extends('layouts.registrar')

@section('title', 'Audit Log')

@section('content')

<div class="audit-sticky-header">AUDIT LOG</div>

{{-- Filter Section --}}
<div class="filter-section">
    <form method="GET" action="{{ route('registrar.audit.index') }}" class="filter-form">
        <div class="filter-row">
            <div class="filter-group">
                <label>Date From</label>
                <input type="date" name="date_from" class="filter-input" value="{{ request('date_from') }}">
            </div>
            <div class="filter-group">
                <label>Date To</label>
                <input type="date" name="date_to" class="filter-input" value="{{ request('date_to') }}">
            </div>
            <div class="filter-group">
                <label>User</label>
                <select name="user_id" class="filter-input">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ ucfirst($user->role) }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label>Action Type</label>
                <select name="action" class="filter-input">
                    <option value="">All Actions</option>
                    <option value="status_change" {{ request('action') == 'status_change' ? 'selected' : '' }}>Status Changes</option>
                    <option value="creation" {{ request('action') == 'creation' ? 'selected' : '' }}>Request Creations</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Request Reference</label>
                <select name="request_id" class="filter-input">
                    <option value="">All Requests</option>
                    @foreach($requests as $req)
                        <option value="{{ $req->id }}" {{ request('request_id') == $req->id ? 'selected' : '' }}>
                            {{ $req->reference_number }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn-filter"><i class="bi bi-search"></i> Apply Filters</button>
                <a href="{{ route('registrar.audit.index') }}" class="btn-reset">Reset</a>
                <a href="{{ route('registrar.audit.export', request()->all()) }}" class="btn-export" target="_blank">
                    <i class="bi bi-file-pdf"></i> Export PDF
                </a>
            </div>
        </div>
    </form>
</div>

{{-- Summary Cards --}}
<div class="summary-cards">
    <div class="summary-card">
        <div class="summary-icon"><i class="bi bi-clock-history"></i></div>
        <div class="summary-value">{{ number_format($totalActions) }}</div>
        <div class="summary-label">Total Actions</div>
    </div>
    <div class="summary-card">
        <div class="summary-icon"><i class="bi bi-calendar-day"></i></div>
        <div class="summary-value">{{ number_format($todayActions) }}</div>
        <div class="summary-label">Today's Actions</div>
    </div>
    <div class="summary-card">
        <div class="summary-icon"><i class="bi bi-people"></i></div>
        <div class="summary-value">{{ number_format($topUsers->sum('count')) }}</div>
        <div class="summary-label">Actions by Staff</div>
    </div>
</div>

{{-- Top Users Section --}}
<div class="top-users-section">
    <div class="section-header">Most Active Users</div>
    <div class="top-users-list">
        @foreach($topUsers as $user)
            <div class="top-user-item">
                <div class="top-user-name">{{ $user->changedBy->name ?? 'Unknown' }}</div>
                <div class="top-user-count">{{ number_format($user->count) }} actions</div>
            </div>
        @endforeach
        @if($topUsers->isEmpty())
            <div class="text-muted text-center py-3">No activity recorded yet.</div>
        @endif
    </div>
</div>

{{-- Audit Log Table --}}
<div class="audit-card">
    <div class="table-scroll-wrapper">
        <div class="table-scroll-body">
            <table class="audit-table">
                <thead>
                    <tr>
                        <th style="width: 12%">Date & Time</th>
                        <th style="width: 15%">User</th>
                        <th style="width: 12%">Role</th>
                        <th style="width: 12%">Reference No.</th>
                        <th style="width: 20%">Action</th>
                        <th style="width: 29%">Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->created_at->format('M d, Y h:i A') }}</td>
                        <td>
                            <strong>{{ $log->changedBy->name ?? 'System' }}</strong>
                        </td>
                        <td>
                            <span class="role-badge role-{{ $log->changedBy->role ?? 'system' }}">
                                {{ ucfirst($log->changedBy->role ?? 'System') }}
                            </span>
                        </td>
                        <td>
                            @if($log->documentRequest)
                                <a href="{{ route('registrar.requests.show', $log->document_request_id) }}" class="ref-link">
                                    {{ $log->documentRequest->reference_number }}
                                </a>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if(is_null($log->old_status))
                                <span class="action-badge action-create">Request Created</span>
                            @else
                                <span class="action-badge action-update">Status Updated</span>
                            @endif
                        </td>
                        <td>
                            @if(!is_null($log->old_status))
                                <div class="status-change">
                                    <span class="old-status">{{ ucfirst(str_replace('_', ' ', $log->old_status)) }}</span>
                                    <i class="bi bi-arrow-right-short"></i>
                                    <span class="new-status">{{ ucfirst(str_replace('_', ' ', $log->new_status)) }}</span>
                                </div>
                                @if($log->notes)
                                    <div class="log-notes">{{ $log->notes }}</div>
                                @endif
                            @else
                                <div class="log-notes">{{ $log->notes ?? 'Request created' }}</div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No audit logs found.</td>
                    </tr>
                    @endforelse
                </tbody>
            <tr>
        </div>
    </div>
</div>

{{-- Pagination --}}
@if($logs->hasPages())
<div class="pagination-wrapper">
    {{ $logs->links() }}
</div>
@endif

@endsection

@section('right-panel')


    <div class="ccst-card mb-0">
        <div class="ccst-card-header blue">Audit Information</div>
        <div class="ccst-card-body p-0">
            <div class="rp-stat-row">
                <span><i class="bi bi-info-circle me-2"></i> All status changes are logged</span>
            </div>
            <div class="rp-stat-row">
                <span><i class="bi bi-person-badge me-2"></i> User actions are tracked</span>
            </div>
            <div class="rp-stat-row" style="border-bottom:none;">
                <span><i class="bi bi-shield-check me-2"></i> Complete audit trail</span>
            </div>
        </div>
    </div>

    <div class="ccst-card mb-0">
        <div class="ccst-card-header yellow">Action Types</div>
        <div class="ccst-card-body p-0">
            <div class="action-legend">
                <div class="legend-item">
                    <span class="legend-badge create"></span>
                    <span>Request Created</span>
                </div>
                <div class="legend-item">
                    <span class="legend-badge update"></span>
                    <span>Status Updated</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .audit-sticky-header {
        background: #1B6B3A;
        color: white;
        font-size: 0.9rem;
        font-weight: 700;
        text-align: center;
        padding: 10px 20px;
        text-transform: uppercase;
        margin-bottom: 20px;
    }

    .filter-section {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .filter-form {
        width: 100%;
    }

    .filter-row {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: flex-end;
    }

    .filter-group {
        flex: 1;
        min-width: 140px;
    }

    .filter-group label {
        display: block;
        font-size: 0.7rem;
        font-weight: 700;
        color: #555;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .filter-input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #D0DDD0;
        border-radius: 6px;
        font-size: 0.8rem;
        font-family: 'Poppins', sans-serif;
    }

    .filter-actions {
        display: flex;
        gap: 8px;
    }

    .btn-filter {
        background: #1A9FE0;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
    }

    .btn-reset {
        background: #f0f0f0;
        color: #666;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
    }

    .btn-export {
        background: #1B6B3A;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* Summary Cards */
    .summary-cards {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }

    .summary-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .summary-icon {
        font-size: 2rem;
        color: #1B6B3A;
        margin-bottom: 10px;
    }

    .summary-value {
        font-size: 1.8rem;
        font-weight: 800;
        color: #1A1A1A;
    }

    .summary-label {
        font-size: 0.7rem;
        color: #666;
        text-transform: uppercase;
    }

    /* Top Users Section */
    .top-users-section {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .section-header {
        background: #F5C518;
        color: #1A1A1A;
        font-size: 0.85rem;
        font-weight: 700;
        padding: 12px 20px;
        text-transform: uppercase;
    }

    .top-users-list {
        padding: 10px 0;
    }

    .top-user-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 20px;
        border-bottom: 1px solid #f0f0f0;
    }

    .top-user-name {
        font-weight: 600;
        color: #333;
    }

    .top-user-count {
        font-size: 0.8rem;
        color: #1B6B3A;
        font-weight: 600;
    }

    /* Audit Table */
    .audit-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .table-scroll-wrapper {
        overflow-x: auto;
    }

    .audit-table {
        width: 100%;
        border-collapse: collapse;
    }

    .audit-table th {
        background: #F0F7F0;
        padding: 12px 16px;
        font-size: 0.7rem;
        font-weight: 700;
        color: #1B6B3A;
        text-align: left;
    }

    .audit-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.8rem;
        vertical-align: middle;
    }

    .role-badge {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 20px;
        font-size: 0.65rem;
        font-weight: 600;
    }

    .role-student { background: #E8F4FD; color: #0969A2; }
    .role-registrar { background: #FFF3CD; color: #856404; }
    .role-system { background: #F0F0F0; color: #888; }

    .action-badge {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 20px;
        font-size: 0.65rem;
        font-weight: 600;
    }

    .action-create { background: #D4EDDA; color: #155724; }
    .action-update { background: #FFF3CD; color: #856404; }

    .status-change {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .old-status {
        background: #F8D7DA;
        color: #721C24;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.7rem;
    }

    .new-status {
        background: #D4EDDA;
        color: #155724;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.7rem;
    }

    .log-notes {
        font-size: 0.7rem;
        color: #888;
        margin-top: 4px;
    }

    .ref-link {
        color: #1B6B3A;
        text-decoration: none;
        font-weight: 600;
    }

    .action-legend {
        padding: 12px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 6px 0;
    }

    .legend-badge {
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }

    .legend-badge.create { background: #D4EDDA; }
    .legend-badge.update { background: #FFF3CD; }

    .pagination-wrapper {
        margin-top: 20px;
        display: flex;
        justify-content: center;
    }

        border-bottom: 1px solid rgba(255,255,255,0.2);
        color: white;
    }

    @media (max-width: 1000px) {
        .summary-cards {
            grid-template-columns: 1fr;
        }
        .filter-row {
            flex-direction: column;
        }
        .filter-actions {
            justify-content: flex-end;
        }
    }
</style>
@endpush

@push('scripts')
<script>

</script>
@endpush
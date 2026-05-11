@extends('layouts.registrar')

@section('title', 'Reports & Analytics')

@section('content')

<div class="registrar-sticky-header">REPORTS & ANALYTICS</div>

{{-- Analytics Dashboard --}}
<div class="analytics-section">
    <div class="section-header">
        <i class="bi bi-graph-up me-2"></i> Analytics Dashboard
    </div>
    
    <div class="analytics-grid">
        {{-- Monthly Requests Chart --}}
        <div class="analytics-card">
            <div class="analytics-card-header">Monthly Document Requests</div>
            <div class="analytics-card-body">
                <canvas id="requestsChart" height="140"></canvas>
            </div>
        </div>
        
        {{-- Monthly Appointments Chart --}}
        <div class="analytics-card">
            <div class="analytics-card-header">Monthly Appointments</div>
            <div class="analytics-card-body">
                <canvas id="appointmentsChart" height="140"></canvas>
            </div>
        </div>
    </div>
    
    {{-- Most Requested Documents - Full Width --}}
    <div class="analytics-full-card">
        <div class="analytics-card-header">Most Requested Documents</div>
        <div class="analytics-card-body">
            <canvas id="topDocumentsChart" height="100"></canvas>
        </div>
    </div>
    
    <div class="analytics-grid">
        {{-- Status Distribution --}}
        <div class="analytics-card" style="border-bottom: none;">
            <div class="analytics-card-header">Request Status Distribution</div>
            <div class="analytics-card-body">
                <canvas id="statusChart" height="140"></canvas>
            </div>
        </div>
        
        {{-- Empty or additional card can go here --}}
        <div class="analytics-card" style="border-bottom: none; display: flex; align-items: center; justify-content: center; background: #fcfcfc;">
            <div class="text-center">
                <i class="bi bi-info-circle text-muted mb-2" style="font-size: 1.5rem;"></i>
                <p class="text-muted small">Data updates in real-time as requests are processed.</p>
            </div>
        </div>
    </div>
</div>

{{-- Report Generation Section --}}
<div class="reports-section">
    <div class="section-header">
        <i class="bi bi-file-text me-2"></i> Generate Reports
    </div>
    
    <div class="reports-grid">
        {{-- Document Requests Report --}}
        <div class="report-card">
            <div class="report-card-header">Document Requests Report</div>
            <div class="report-card-body">
                <form method="GET" action="{{ route('registrar.reports.export') }}" target="_blank">
                    <input type="hidden" name="report_type" value="requests">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Date From</label>
                            <input type="date" name="date_from" class="form-input" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                        </div>
                        <div class="form-group">
                            <label>Date To</label>
                            <input type="date" name="date_to" class="form-input" value="{{ now()->format('Y-m-d') }}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="include_summary" value="1" checked>
                            <span>Include Summary (Totals by Status)</span>
                        </label>
                    </div>
                    
                    <button type="submit" class="btn-generate">
                        <i class="bi bi-file-pdf"></i> Generate PDF
                    </button>
                </form>
            </div>
        </div>
        
        {{-- Payments Report --}}
        <div class="report-card">
            <div class="report-card-header">Payments Report</div>
            <div class="report-card-body">
                <form method="GET" action="{{ route('registrar.reports.export') }}" target="_blank">
                    <input type="hidden" name="report_type" value="payments">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Date From</label>
                            <input type="date" name="date_from" class="form-input" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                        </div>
                        <div class="form-group">
                            <label>Date To</label>
                            <input type="date" name="date_to" class="form-input" value="{{ now()->format('Y-m-d') }}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="include_summary" value="1" checked>
                            <span>Include Summary (Total Collected by Status)</span>
                        </label>
                    </div>
                    
                    <button type="submit" class="btn-generate">
                        <i class="bi bi-file-pdf"></i> Generate PDF
                    </button>
                </form>
            </div>
        </div>
        
        {{-- Appointments Report --}}
        <div class="report-card">
            <div class="report-card-header">Appointments Report</div>
            <div class="report-card-body">
                <form method="GET" action="{{ route('registrar.reports.export') }}" target="_blank">
                    <input type="hidden" name="report_type" value="appointments">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Date From</label>
                            <input type="date" name="date_from" class="form-input" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                        </div>
                        <div class="form-group">
                            <label>Date To</label>
                            <input type="date" name="date_to" class="form-input" value="{{ now()->format('Y-m-d') }}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="include_summary" value="1" checked>
                            <span>Include Summary (Attendance Statistics)</span>
                        </label>
                    </div>
                    
                    <button type="submit" class="btn-generate">
                        <i class="bi bi-file-pdf"></i> Generate PDF
                    </button>
                </form>
            </div>
        </div>
        
        {{-- Students Report --}}
        <div class="report-card">
            <div class="report-card-header">Students Report</div>
            <div class="report-card-body">
                <form method="GET" action="{{ route('registrar.reports.export') }}" target="_blank">
                    <input type="hidden" name="report_type" value="students">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Date From</label>
                            <input type="date" name="date_from" class="form-input" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                        </div>
                        <div class="form-group">
                            <label>Date To</label>
                            <input type="date" name="date_to" class="form-input" value="{{ now()->format('Y-m-d') }}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="include_summary" value="1" checked>
                            <span>Include Summary (Student Count by Strand/Grade)</span>
                        </label>
                    </div>
                    
                    <button type="submit" class="btn-generate">
                        <i class="bi bi-file-pdf"></i> Generate PDF
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

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
        position: relative; /* Changed to relative so it scrolls with content */
        margin-bottom: 20px;
        margin-left: -24px;
        margin-right: -24px;
        margin-top: -28px;
        width: calc(100% + 48px);
    }

    .section-header {
        background: #F5C518;
        color: #1A1A1A;
        font-size: 1rem;
        font-weight: 700;
        padding: 12px 20px;
        border-radius: 12px 12px 0 0;
    }

    .analytics-section, .reports-section {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 24px;
    }

    .analytics-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0;
    }

    .analytics-card {
        padding: 15px; /* Reduced padding */
        border-right: 1px solid #f0f0f0;
        border-bottom: 1px solid #f0f0f0;
    }

    .analytics-full-card {
        padding: 15px 20px;
        border-bottom: 1px solid #f0f0f0;
        background: #fafafa;
    }

    .analytics-card:nth-child(2n) {
        border-right: none;
    }

    .analytics-card:nth-last-child(-n+2) {
        border-bottom: none;
    }

    .analytics-card-header {
        font-size: 0.85rem;
        font-weight: 700;
        color: #1B6B3A;
        margin-bottom: 15px;
        text-transform: uppercase;
    }

    .reports-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        padding: 20px;
    }

    .report-card {
        background: #f8f9fa;
        border-radius: 12px;
        overflow: hidden;
    }

    .report-card-header {
        background: #1B6B3A;
        color: white;
        font-size: 0.85rem;
        font-weight: 700;
        padding: 12px 16px;
    }

    .report-card-body {
        padding: 16px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 12px;
    }

    .form-group {
        margin-bottom: 12px;
    }

    .form-group label {
        display: block;
        font-size: 0.7rem;
        font-weight: 700;
        color: #555;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .form-input {
        width: 100%;
        padding: 8px 10px;
        border: 1px solid #D0DDD0;
        border-radius: 6px;
        font-size: 0.8rem;
        font-family: 'Poppins', sans-serif;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }

    .checkbox-label span {
        font-size: 0.75rem;
        font-weight: normal;
        text-transform: none;
    }

    .btn-generate {
        width: 100%;
        background: #1A9FE0;
        color: white;
        border: none;
        padding: 10px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: background 0.2s;
    }

    .btn-generate:hover {
        background: #0D7FBF;
    }



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

    @media (max-width: 1000px) {
        .analytics-grid, .reports-grid {
            grid-template-columns: 1fr;
        }
        .analytics-card {
            border-right: none;
        }
        .analytics-card:nth-last-child(-n+2) {
            border-bottom: 1px solid #f0f0f0;
        }
        .analytics-card:last-child {
            border-bottom: none;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Monthly Requests Chart
    const requestsCtx = document.getElementById('requestsChart').getContext('2d');
    new Chart(requestsCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthlyRequests['labels']) !!},
            datasets: [{
                label: 'Document Requests',
                data: {!! json_encode($monthlyRequests['data']) !!},
                borderColor: '#1A9FE0',
                backgroundColor: 'rgba(26, 159, 224, 0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'top' }
            }
        }
    });

    // Monthly Appointments Chart
    const appointmentsCtx = document.getElementById('appointmentsChart').getContext('2d');
    new Chart(appointmentsCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthlyAppointments['labels']) !!},
            datasets: [{
                label: 'Appointments',
                data: {!! json_encode($monthlyAppointments['data']) !!},
                borderColor: '#1B6B3A',
                backgroundColor: 'rgba(27, 107, 58, 0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'top' }
            }
        }
    });

    // Top Documents Chart (Bar)
    const topDocsCtx = document.getElementById('topDocumentsChart').getContext('2d');
    new Chart(topDocsCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($topDocuments->pluck('name')) !!},
            datasets: [{
                label: 'Number of Requests',
                data: {!! json_encode($topDocuments->pluck('count')) !!},
                backgroundColor: [
                    '#1B6B3A', '#1A9FE0', '#F5C518', '#DC3545', '#6c757d'
                ],
                borderRadius: 4,
                barThickness: 20
            }]
        },
        options: {
            indexAxis: 'y', // Horizontal Bar Chart
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { display: false }
                },
                y: {
                    grid: { display: false }
                }
            }
        }
    });

    // Status Distribution Pie Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: ['Pending', 'Missed', 'Ready for Pickup', 'Completed', 'Cancelled'],
            datasets: [{
                data: [
                    {{ $statusDistribution['pending'] }},
                    {{ $statusDistribution['missed'] }},
                    {{ $statusDistribution['ready_for_pickup'] }},
                    {{ $statusDistribution['completed'] }},
                    {{ $statusDistribution['cancelled'] }}
                ],
                backgroundColor: ['#FFF3CD', '#6c757d', '#E8F4FD', '#D4EDDA', '#F0F0F0'],
                borderColor: ['#856404', '#495057', '#0969A2', '#155724', '#888'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });


</script>
@endpush
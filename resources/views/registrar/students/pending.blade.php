@extends('layouts.registrar')

@section('title', 'Pending Student Verifications')

@section('content')

<div class="registrar-sticky-header">PENDING STUDENT VERIFICATIONS</div>

<div class="pending-card">
    <form id="bulkVerifyForm" method="POST" action="{{ route('registrar.students.verify-bulk') }}">
        @csrf
        <div class="table-scroll-body">
            <table class="pending-table">
                <thead>
                    <tr>
                        <th style="width: 5%"><input type="checkbox" id="selectAll"></th>
                        <th style="width: 20%">Student Name</th>
                        <th style="width: 15%">Student Number</th>
                        <th style="width: 20%">Email</th>
                        <th style="width: 10%">Strand</th>
                        <th style="width: 10%">Grade</th>
                        <th style="width: 10%">Registered</th>
                        <th style="width: 10%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingStudents as $student)
                    <tr>
                        <td><input type="checkbox" name="student_ids[]" value="{{ $student->id }}"></td>
                        <td>{{ $student->full_name }}</td>
                        <td>{{ $student->student_number }}</td>
                        <td>{{ $student->email }}</td>
                        <td>{{ $student->strand }}</td>
                        <td>{{ $student->grade_level }}</td>
                        <td>{{ $student->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="action-buttons">
                                <button type="button" class="btn-view-id" onclick="viewId({{ $student->id }})">
                                    <i class="bi bi-card-image"></i> ID
                                </button>
                                <button type="button" class="btn-verify" onclick="confirmVerify({{ $student->id }})">
                                    <i class="bi bi-check-circle"></i> Verify
                                </button>
                                <button type="button" class="btn-reject" onclick="confirmReject({{ $student->id }})">
                                    <i class="bi bi-x-circle"></i> Reject
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No pending student verifications.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($pendingStudents->count() > 0)
        <div class="bulk-actions">
            <button type="submit" class="btn-bulk-verify">
                <i class="bi bi-check2-all"></i> Verify Selected
            </button>
        </div>
        @endif
    </form>
    
    {{ $pendingStudents->links() }}
</div>

<form id="rejectForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<form id="verifyForm" method="POST" style="display:none;">
    @csrf
    @method('PATCH')
</form>

@endsection

@section('right-panel')


    <div class="ccst-card mb-0">
        <div class="ccst-card-header blue">Verification Guide</div>
        <div class="ccst-card-body p-0">
            <div class="rp-guide-step">
                <span class="rp-step-num">1</span>
                <span>Click ID to view uploaded student ID</span>
            </div>
            <div class="rp-guide-step">
                <span class="rp-step-num">2</span>
                <span>Verify ID matches student information</span>
            </div>
            <div class="rp-guide-step">
                <span class="rp-step-num">3</span>
                <span>Click Verify to approve account</span>
            </div>
            <div class="rp-guide-step" style="border-bottom:none;">
                <span class="rp-step-num">4</span>
                <span>Click Reject to remove invalid registration</span>
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
        margin-bottom: 20px;
    }

    .pending-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        padding: 20px;
    }

    .pending-table {
        width: 100%;
        border-collapse: collapse;
    }

    .pending-table th {
        background: #F0F7F0;
        padding: 12px;
        font-size: 0.7rem;
        font-weight: 700;
        color: #1B6B3A;
        text-align: left;
    }

    .pending-table td {
        padding: 12px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.8rem;
        vertical-align: middle;
    }

    .action-buttons {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }

    .btn-view-id {
        background: #F5C518;
        color: #1A1A1A;
        border: none;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.7rem;
        cursor: pointer;
    }

    .btn-verify {
        background: #1B6B3A;
        color: white;
        border: none;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.7rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .btn-reject {
        background: #DC3545;
        color: white;
        border: none;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.7rem;
        cursor: pointer;
    }

    .bulk-actions {
        margin-top: 20px;
        padding-top: 15px;
        border-top: 1px solid #f0f0f0;
        display: flex;
        justify-content: flex-end;
    }

    .btn-bulk-verify {
        background: #1B6B3A;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 6px;
        font-size: 0.8rem;
        cursor: pointer;
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
</style>
@endpush

@push('scripts')
<script>
    // Select all checkbox
    document.getElementById('selectAll')?.addEventListener('change', function() {
        document.querySelectorAll('input[name="student_ids[]"]').forEach(cb => cb.checked = this.checked);
    });

    // View ID modal
    function viewId(id) {
        window.open(`/registrar/students/${id}/id`, '_blank', 'width=600,height=500');
    }

    // Confirm reject
    function confirmReject(id) {
        Swal.fire({
            title: 'Reject Student Registration?',
            text: 'This will permanently delete the student account. They can re-register.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DC3545',
            cancelButtonColor: '#1B6B3A',
            confirmButtonText: 'Yes, Reject',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('rejectForm');
                form.action = `/registrar/students/${id}/reject`;
                form.submit();
            }
        });
    }

    // Confirm verify
    function confirmVerify(id) {
        Swal.fire({
            title: 'Verify Student Registration?',
            text: 'This will approve the student account and allow them to request documents.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1B6B3A',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Verify',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('verifyForm');
                form.action = `/registrar/students/${id}/verify`;
                form.submit();
            }
        });
    }

    // Live clock

</script>
@endpush
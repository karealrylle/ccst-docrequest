@extends('layouts.registrar')

@section('title', 'Document Types & Pricing')

@section('content')

<div class="registrar-sticky-header">DOCUMENT TYPES & PRICING</div>

<div class="pending-card">
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h5 class="mb-0" style="color: #1B6B3A; font-weight: 700;">Manage Documents</h5>
        </div>
        <div>
            <a href="{{ route('registrar.document-types.create') }}" class="btn btn-sm" style="background-color: #1B6B3A; color: white;">
                <i class="bi bi-plus-circle"></i> Add New Document Type
            </a>
        </div>
    </div>

    <div class="table-scroll-body">
        <table class="pending-table">
            <thead>
                <tr>
                    <th style="width: 10%">Code</th>
                    <th style="width: 30%">Document Name</th>
                    <th style="width: 15%">Fee (₱)</th>
                    <th style="width: 15%">Printable</th>
                    <th style="width: 10%">Status</th>
                    <th style="width: 20%">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documentTypes as $doc)
                <tr>
                    <td class="fw-bold">{{ $doc->code }}</td>
                    <td>{{ $doc->name }}</td>
                    <td>₱{{ number_format($doc->fee, 2) }}</td>
                    <td>
                        @if($doc->is_printable)
                            <span class="badge bg-info text-dark"><i class="bi bi-printer"></i> System Printed</span>
                        @else
                            <span class="badge bg-secondary"><i class="bi bi-file-earmark"></i> Physical Only</span>
                        @endif
                    </td>
                    <td>
                        @if($doc->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Disabled</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('registrar.document-types.edit', $doc->id) }}" class="btn-view-id text-decoration-none">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form action="{{ route('registrar.document-types.destroy', $doc->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-reject" onclick="return confirm('Are you sure you want to delete this document type?')">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No document types configured.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
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
        padding: 8px 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
    }

    .pending-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        padding: 15px 20px;
    }

    .pending-table {
        width: 100%;
        border-collapse: collapse;
    }

    .pending-table th {
        background: #F0F7F0;
        padding: 8px 12px;
        font-size: 0.7rem;
        font-weight: 700;
        color: #1B6B3A;
        text-align: left;
    }

    .pending-table td {
        padding: 6px 12px;
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
        font-size: 0.8rem;
        cursor: pointer;
        display: inline-block;
    }

    .btn-reject {
        background: #DC3545;
        color: white;
        border: none;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.8rem;
        cursor: pointer;
    }

    /* ── TABLE HEIGHT ADJUSTMENT ── */
    /* Change 500px to a larger value (e.g., 700px) to show more rows */
    .table-scroll-body {
        max-height: 700px;
        overflow-y: auto;
    }
</style>
@endpush

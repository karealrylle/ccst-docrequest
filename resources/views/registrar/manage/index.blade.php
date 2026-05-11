@extends('layouts.registrar')

@section('title', 'Manage Registrars')

@section('content')

<div class="registrar-sticky-header">MANAGE REGISTRARS</div>

<div class="pending-card">
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h5 class="mb-0" style="color: #1B6B3A; font-weight: 700;">Registrar Accounts</h5>
        </div>
        <div>
            <a href="{{ route('registrar.manage.create') }}" class="btn btn-sm" style="background-color: #1B6B3A; color: white;">
                <i class="bi bi-person-plus"></i> Create New Account
            </a>
        </div>
    </div>

    <div class="table-scroll-body">
        <table class="pending-table">
            <thead>
                <tr>
                    <th style="width: 25%">Name</th>
                    <th style="width: 30%">Email</th>
                    <th style="width: 15%">Role</th>
                    <th style="width: 15%">Status</th>
                    <th style="width: 15%">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($registrars as $registrar)
                <tr @if(!$registrar->is_active) style="background-color: #fcfcfc;" @endif>
                    <td class="fw-bold @if(!$registrar->is_active) text-muted @endif">
                        {{ $registrar->full_name ?? $registrar->name }}
                    </td>
                    <td class="@if(!$registrar->is_active) text-muted @endif">{{ $registrar->email }}</td>
                    <td>
                        @if($registrar->is_admin)
                            <span class="badge bg-primary">Admin</span>
                        @else
                            <span class="badge bg-secondary">Staff</span>
                        @endif
                    </td>
                    <td>
                        @if($registrar->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Deactivated</span>
                        @endif
                    </td>
                    <td>
                        @if($registrar->id !== auth()->id())
                        <div class="action-buttons">
                            <form action="{{ route('registrar.manage.toggle-active', $registrar->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="{{ $registrar->is_active ? 'btn-deactivate' : 'btn-activate' }}" 
                                        onclick="return confirm('Are you sure you want to {{ $registrar->is_active ? 'deactivate' : 'activate' }} this account?')">
                                    @if($registrar->is_active)
                                        <i class="bi bi-person-x"></i> Deactivate
                                    @else
                                        <i class="bi bi-person-check"></i> Activate
                                    @endif
                                </button>
                            </form>
                        </div>
                        @else
                            <span class="text-muted fst-italic">You</span>
                        @endif
                    </td>
                </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No registrar accounts found.</td>
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
        font-size: 0.8rem;
        font-weight: 700;
        color: #1B6B3A;
        text-align: left;
    }

    .pending-table td {
        padding: 12px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.85rem;
        vertical-align: middle;
    }

    .btn-deactivate {
        background: #DC3545;
        color: white;
        border: none;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.75rem;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-deactivate:hover {
        background: #c82333;
    }

    .btn-activate {
        background: #1B6B3A;
        color: white;
        border: none;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.75rem;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-activate:hover {
        background: #14522c;
    }
</style>
@endpush

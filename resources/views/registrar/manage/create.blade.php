@extends('layouts.registrar')

@section('title', 'Create Registrar Account')

@section('content')

<div class="registrar-sticky-header">CREATE NEW REGISTRAR ACCOUNT</div>

<div class="pending-card" style="max-width: 600px; margin: 0 auto;">
    <div class="mb-4">
        <h5 style="color: #1B6B3A; font-weight: 700;">Account Details</h5>
        <p class="text-muted small">Fill out the form below to create a new registrar or staff account.</p>
    </div>

    <form action="{{ route('registrar.manage.store') }}" method="POST">
        @csrf
        
        <div class="mb-3">
            <label for="name" class="form-label fw-bold small text-uppercase" style="color: #555;">Full Name</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. John Doe" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label fw-bold small text-uppercase" style="color: #555;">Email Address</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="e.g. john@ccst.edu.ph" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="password" class="form-label fw-bold small text-uppercase" style="color: #555;">Password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="password_confirmation" class="form-label fw-bold small text-uppercase" style="color: #555;">Confirm Password</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
            <a href="{{ route('registrar.manage.index') }}" class="btn btn-light" style="font-weight: 600;">
                Cancel
            </a>
            <button type="submit" class="btn px-4" style="background-color: #1B6B3A; color: white; font-weight: 600;">
                <i class="bi bi-check-circle me-1"></i> Create Account
            </button>
        </div>
    </form>
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
        padding: 30px;
    }

    .form-control:focus {
        border-color: #1B6B3A;
        box-shadow: 0 0 0 0.25rem rgba(27, 107, 58, 0.1);
    }
</style>
@endpush

@extends('layouts.student')

@section('title', 'My Account')

@section('content')

@php
    $flashText = session('success') ?? session('error') ?? null;
    $flashType = $flashText && session()->has('error') ? 'error' : 'success';
@endphp
@if($flashText)
<div id="flash-data"
     data-text="{{ e($flashText) }}"
     data-type="{{ $flashType }}"
     style="display:none;" aria-hidden="true"></div>
@endif

{{-- ══════════════════════════════════════════════════════════════════
     STICKY HEADER
══════════════════════════════════════════════════════════════════ --}}
<div class="req-sticky-header">MY ACCOUNT</div>

{{-- ══════════════════════════════════════════════════════════════════
     SCROLLABLE CONTAINER
══════════════════════════════════════════════════════════════════ --}}
<div class="req-scroll">

    {{-- ══════════════════════════════════════════════════════════════
         CARD 1 — PROFILE IDENTITY
    ══════════════════════════════════════════════════════════════════ --}}
    <div class="req-card">
        <div class="req-card-body">

            <div class="section-heading-row">
                <span class="section-heading">Profile</span>
                <span style="font-size:0.72rem; color:#888;">
                    Member since {{ $user->created_at->format('F Y') }}
                </span>
            </div>

            {{-- ── Avatar + name block ── --}}
            <div class="profile-identity-row" style="display:flex; align-items:center; gap:20px; margin-bottom:20px;">

                <div style="position:relative; flex-shrink:0;">
                    <div id="avatar-wrap"
                         onclick="document.getElementById('photo-input').click()"
                         style="width:80px; height:80px; border-radius:50%;
                                overflow:hidden; cursor:pointer; position:relative;
                                border:3px solid #D0DDD0; flex-shrink:0;">

                        @if($user->profile_photo)
                            <img id="avatar-img"
                                 src="{{ route('student.account.photo') }}"
                                 alt="Profile Photo"
                                 style="width:100%; height:100%; object-fit:cover;">
                        @else
                            <div id="avatar-initials"
                                 style="width:100%; height:100%; background:#1B6B3A;
                                        display:flex; align-items:center; justify-content:center;
                                        font-size:1.6rem; font-weight:700; color:white;
                                        font-family:'Poppins',sans-serif;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif

                        <div id="avatar-overlay"
                             style="position:absolute; inset:0; background:rgba(0,0,0,0.45);
                                    display:flex; align-items:center; justify-content:center;
                                    opacity:0; transition:opacity 0.2s; border-radius:50%;">
                            <i class="bi bi-camera-fill" style="color:white; font-size:1.1rem;"></i>
                        </div>
                    </div>

                    <div style="position:absolute; bottom:2px; right:2px;
                                width:22px; height:22px; background:#1A9FE0;
                                border-radius:50%; display:flex; align-items:center;
                                justify-content:center; border:2px solid white;
                                pointer-events:none;">
                        <i class="bi bi-camera-fill" style="color:white; font-size:0.55rem;"></i>
                    </div>
                </div>

                {{-- Hidden photo upload form --}}
                <form id="photo-form"
                      method="POST"
                      action="{{ route('student.account.updatePhoto') }}"
                      enctype="multipart/form-data"
                      style="display:none;">
                    @csrf
                    <input type="file" id="photo-input" name="profile_photo"
                           accept=".jpg,.jpeg,.png" style="display:none;">
                </form>

                <div>
                    <div style="font-size:1.1rem; font-weight:700; color:#1A1A1A; line-height:1.3;">
                        {{ $user->name }}
                    </div>
                    <div style="margin-top:4px;">
                        <span style="background:#F5C518; color:#1A1A1A; font-size:0.68rem;
                                     font-weight:700; padding:2px 10px; border-radius:20px;
                                     text-transform:uppercase; letter-spacing:0.5px;">
                            Student
                        </span>
                    </div>
                    <div style="font-size:0.78rem; color:#888; margin-top:5px;">
                        {{ $user->email }}
                    </div>
                    <div style="font-size:0.72rem; color:#aaa; margin-top:2px;">
                        Click the avatar to change your photo
                    </div>
                </div>
            </div>

            <div class="section-divider"></div>

            {{-- ── Read-only school record fields ── --}}
            <div style="font-size:0.72rem; font-weight:700; color:#888;
                        text-transform:uppercase; letter-spacing:0.4px; margin-bottom:10px;">
                School Information
                <span class="school-info-note" style="font-weight:400; text-transform:none; color:#aaa; margin-left:6px;">
                    — contact the registrar to update these fields
                </span>
            </div>

            <div class="form-row-3 mb-2">
                <div class="form-field">
                    <label>Student Number</label>
                    <div class="field-readonly">{{ $user->student_number ?? '—' }}</div>
                </div>
                <div class="form-field">
                    <label>Grade Level</label>
                    <div class="field-readonly">{{ $user->grade_level ?? '—' }}</div>
                </div>
                <div class="form-field">
                    <label>Section</label>
                    <div class="field-readonly">{{ $user->section ?? '—' }}</div>
                </div>
            </div>

            <div class="form-row-2">
                <div class="form-field">
                    <label>Strand / Program</label>
                    <div class="field-readonly">{{ $user->strand ?? '—' }}</div>
                </div>
                <div class="form-field">
                    <label>Email Address</label>
                    <div class="field-readonly"
                         style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                        {{ $user->email }}
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         CARD 2 — EDIT CONTACT + ADDRESS
    ══════════════════════════════════════════════════════════════════ --}}
    <div class="req-card" style="margin-top:12px;">
        <div class="req-card-body">

            <div class="section-heading-row">
                <span class="section-heading">Contact Information</span>
                <span style="font-size:0.72rem; color:#888;">You can edit these</span>
            </div>

            @if($errors->has('contact_number') || $errors->has('address'))
            <div style="background:#FFF0F0; border:1px solid #F5C6CB;
                        border-left:4px solid #DC3545; border-radius:6px;
                        padding:10px 14px; margin-bottom:14px;">
                <div class="fw-bold" style="color:#721C24; font-size:0.82rem; margin-bottom:4px;">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>Please fix the following:
                </div>
                <ul class="mb-0 ps-3" style="font-size:0.8rem; color:#721C24;">
                    @if($errors->has('contact_number'))
                        <li>{{ $errors->first('contact_number') }}</li>
                    @endif
                    @if($errors->has('address'))
                        <li>{{ $errors->first('address') }}</li>
                    @endif
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('student.account.updateProfile') }}" id="profile-form">
                @csrf
                @method('PATCH')

                <div class="form-row-2 mb-3">
                    <div class="form-field">
                        <label>Contact Number <span style="color:#DC3545;">*</span></label>
                        <input type="text"
                               name="contact_number"
                               class="field-input @error('contact_number') input-error @enderror"
                               value="{{ old('contact_number', $user->contact_number) }}"
                               placeholder="e.g. 09XX-XXX-XXXX"
                               maxlength="20">
                        @error('contact_number')
                            <div style="font-size:0.76rem; color:#DC3545; margin-top:3px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-field">
                        <label>Address</label>
                        <input type="text"
                               name="address"
                               class="field-input @error('address') input-error @enderror"
                               value="{{ old('address', $user->address) }}"
                               placeholder="Home address">
                        @error('address')
                            <div style="font-size:0.76rem; color:#DC3545; margin-top:3px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="section-divider"></div>

                <div style="display:flex; justify-content:flex-end; padding-top:4px;">
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-check-circle me-1"></i>Save Changes
                    </button>
                </div>
            </form>

        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         CARD 3 — CHANGE PASSWORD
    ══════════════════════════════════════════════════════════════════ --}}
    <div class="req-card" style="margin-top:12px;">
        <div class="req-card-body">

            <div class="section-heading-row">
                <span class="section-heading">Change Password</span>
            </div>

            @if($errors->has('current_password') || $errors->has('new_password') || $errors->has('new_password_confirmation'))
            <div style="background:#FFF0F0; border:1px solid #F5C6CB;
                        border-left:4px solid #DC3545; border-radius:6px;
                        padding:10px 14px; margin-bottom:14px;">
                <div class="fw-bold" style="color:#721C24; font-size:0.82rem; margin-bottom:4px;">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>Please fix the following:
                </div>
                <ul class="mb-0 ps-3" style="font-size:0.8rem; color:#721C24;">
                    @if($errors->has('current_password'))
                        <li>{{ $errors->first('current_password') }}</li>
                    @endif
                    @if($errors->has('new_password'))
                        <li>{{ $errors->first('new_password') }}</li>
                    @endif
                    @if($errors->has('new_password_confirmation'))
                        <li>{{ $errors->first('new_password_confirmation') }}</li>
                    @endif
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('student.account.updatePassword') }}" id="password-form">
                @csrf
                @method('PATCH')

                <div class="form-row-3 mb-3">

                    <div class="form-field">
                        <label>Current Password <span style="color:#DC3545;">*</span></label>
                        <div style="position:relative;">
                            <input type="password" name="current_password" id="current_password"
                                   class="field-input @error('current_password') input-error @enderror"
                                   placeholder="Enter current password" style="padding-right:36px;">
                            <button type="button" class="pwd-toggle" data-target="current_password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <div style="font-size:0.76rem; color:#DC3545; margin-top:3px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label>New Password <span style="color:#DC3545;">*</span></label>
                        <div style="position:relative;">
                            <input type="password" name="new_password" id="new_password"
                                   class="field-input @error('new_password') input-error @enderror"
                                   placeholder="Min. 8 characters" style="padding-right:36px;">
                            <button type="button" class="pwd-toggle" data-target="new_password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        @error('new_password')
                            <div style="font-size:0.76rem; color:#DC3545; margin-top:3px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label>Confirm New Password <span style="color:#DC3545;">*</span></label>
                        <div style="position:relative;">
                            <input type="password" name="new_password_confirmation"
                                   id="new_password_confirmation"
                                   class="field-input"
                                   placeholder="Repeat new password" style="padding-right:36px;">
                            <button type="button" class="pwd-toggle" data-target="new_password_confirmation">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div id="pwd-match-msg" style="font-size:0.74rem; margin-top:3px; display:none;"></div>
                    </div>

                </div>

                {{-- Password strength bar --}}
                <div style="margin-bottom:14px;">
                    <div style="font-size:0.72rem; color:#888; margin-bottom:4px;">Password strength</div>
                    <div style="height:5px; background:#EEE; border-radius:4px; overflow:hidden;">
                        <div id="strength-bar" style="height:100%; width:0; border-radius:4px; transition:all 0.3s ease;"></div>
                    </div>
                    <div id="strength-label" style="font-size:0.72rem; color:#888; margin-top:3px;"></div>
                </div>

                <div class="section-divider"></div>

                <div style="display:flex; justify-content:flex-end; padding-top:4px;">
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-lock-fill me-1"></i>Update Password
                    </button>
                </div>
            </form>

        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         CARD 4 — DANGER ZONE
    ══════════════════════════════════════════════════════════════════ --}}
    <div class="req-card" style="margin-top:12px; border:1px solid #FFDada; background:#FFF5F5;">
        <div class="req-card-body">
            <div class="section-heading-row">
                <span class="section-heading" style="color:#DC3545;">Danger Zone</span>
            </div>
            
            <p style="font-size:0.78rem; color:#666; margin-bottom:15px;">
                Deleting your account will remove all your data and document request history. This action is not immediate; your account will be soft-deleted for 30 days before permanent removal.
            </p>

            <div style="display:flex; justify-content:flex-start;">
                <button type="button" class="btn-danger-outline" onclick="confirmAccountDeletion()">
                    <i class="bi bi-trash3-fill me-1"></i>Delete My Account
                </button>
            </div>
        </div>
    </div>

    <div style="padding-bottom:20px;"></div>

</div>{{-- end req-scroll --}}

@endsection


@push('styles')
<style>
    .layout-alert-hide { display: none !important; }

    .req-sticky-header {
        background: #1B6B3A; color: white; font-size: 0.9rem; font-weight: 700;
        text-align: center; padding: 10px 20px; text-transform: uppercase;
        letter-spacing: 0.5px; max-width: 900px; position: sticky; top: 0; z-index: 10;
    }

    .req-scroll {
        height: calc(100vh - var(--header-h) - var(--footer-h) - 120px);
        overflow-y: auto; overflow-x: hidden; scrollbar-width: none;
    }
    .req-scroll::-webkit-scrollbar { display: none; }

    /* ── MOBILE ACCOUNT ── */
    @media (max-width: 768px) {
        .req-scroll {
            height: calc(100vh - 60px - 35px - 40px);
        }
        .form-row-3, .form-row-2 {
            grid-template-columns: 1fr;
            gap: 10px;
        }
        .req-card-body {
            padding: 15px 12px;
        }
        .profile-identity-row {
            flex-direction: column;
            text-align: center;
        }
        .btn-submit, .btn-danger-outline {
            width: 100%;
            justify-content: center;
            padding: 12px;
        }
        .section-heading-row {
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 5px;
        }
        .school-info-note {
            display: block;
            margin-left: 0 !important;
            margin-top: 4px;
        }
    }


    .req-card {
        background: #ffffff; border: 1px solid #D0DDD0;
        border-radius: 0 0 12px 12px; width: 100%; max-width: 900px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.10);
    }
    .req-card-body { padding: 20px 24px; }

    .section-heading { font-size:0.85rem; font-weight:700; color:#1A1A1A; text-transform:uppercase; letter-spacing:0.3px; }
    .section-heading-row { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:14px; }
    .section-divider { border-top:1px solid #D0DDD0; margin:14px 0; }

    .form-row-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; margin-bottom:10px; }
    .form-row-2 { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:10px; }
    .form-field { display:flex; flex-direction:column; }
    .form-field label { font-size:0.73rem; font-weight:700; color:#555; text-transform:uppercase; letter-spacing:0.4px; margin-bottom:3px; }
    .field-readonly { padding:6px 10px; border:1px solid #D0DDD0; border-radius:4px; background:#f8f9fa; font-size:0.82rem; color:#1A1A1A; font-family:'Poppins',sans-serif; min-height:32px; }
    .field-input { padding:6px 10px; border:1px solid #D0DDD0; border-radius:4px; background:white; font-size:0.82rem; color:#1A1A1A; font-family:'Poppins',sans-serif; min-height:32px; width:100%; outline:none; }
    .field-input:focus { border-color:#1B6B3A; box-shadow:0 0 0 2px rgba(27,107,58,0.12); }
    .field-input.input-error { border-color:#DC3545; }

    #avatar-wrap:hover #avatar-overlay { opacity: 1 !important; }

    .pwd-toggle { position:absolute; right:8px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:#888; padding:0; font-size:0.85rem; line-height:1; }
    .pwd-toggle:hover { color:#1B6B3A; }

    .btn-submit:hover { background:#0D7FBF; color:white; }

    .btn-danger-outline {
        display: inline-flex;
        align-items: center;
        background: transparent;
        color: #DC3545;
        font-weight: 700;
        font-size: 0.82rem;
        padding: 8px 18px;
        border: 2px solid #DC3545;
        border-radius: 6px;
        cursor: pointer;
        text-decoration: none;
        font-family: 'Poppins', sans-serif;
        transition: all 0.2s;
    }
    .btn-danger-outline:hover {
        background: #DC3545;
        color: white;
    }

    #bell-btn-wrapper { position:relative; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; }
    #bell-badge { position:absolute; top:-5px; right:-5px; background:#DC3545; color:white; font-size:0.58rem; font-weight:700; min-width:16px; height:16px; border-radius:8px; padding:0 3px; display:none; align-items:center; justify-content:center; pointer-events:none; line-height:1; }
    #bell-dropdown { position:absolute; top:calc(100% + 10px); right:0; width:310px; background:white; border:1px solid #D0DDD0; border-radius:10px; box-shadow:0 8px 28px rgba(0,0,0,0.14); z-index:9999; overflow:hidden; opacity:0; visibility:hidden; transform:translateY(-6px); transition:opacity 0.18s ease,transform 0.18s ease,visibility 0s linear 0.18s; }
    #bell-dropdown.open { opacity:1; visibility:visible; transform:translateY(0); transition:opacity 0.18s ease,transform 0.18s ease,visibility 0s linear 0s; }
    #bell-dropdown-header { background:#1B6B3A; color:white; font-size:0.78rem; font-weight:700; padding:9px 14px; text-transform:uppercase; letter-spacing:0.4px; display:flex; align-items:center; gap:6px; }
    #bell-dropdown-body { max-height:260px; overflow-y:auto; }
    .bell-notif-empty { padding:18px 14px; font-size:0.82rem; color:#999; text-align:center; }
    .bell-notif-item { display:flex; align-items:flex-start; gap:9px; padding:10px 14px; border-bottom:1px solid #f0f0f0; font-size:0.8rem; color:#333; line-height:1.4; }
    .bell-notif-item:last-child { border-bottom:none; }
    .bell-notif-item.unread { background:#f5fdf7; }
    .bell-notif-icon { flex-shrink:0; margin-top:2px; font-size:0.9rem; }
    .bell-notif-icon.success { color:#2E8B57; }
    .bell-notif-icon.error   { color:#DC3545; }
    .bell-notif-time { font-size:0.7rem; color:#bbb; margin-top:3px; }
</style>
@endpush


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ── Profile photo: preview then auto-submit ──
    const photoInput = document.getElementById('photo-input');
    const photoForm  = document.getElementById('photo-form');
    const avatarImg  = document.getElementById('avatar-img');
    const avatarInit = document.getElementById('avatar-initials');

    if (photoInput) {
        photoInput.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;
            const allowed = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!allowed.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File',
                    text: 'Please upload a JPG or PNG image.',
                });
                photoInput.value = '';
                return;
            }
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: 'Maximum size is 2 MB.',
                });
                photoInput.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function (e) {
                if (avatarInit) avatarInit.style.display = 'none';
                if (avatarImg) {
                    avatarImg.src = e.target.result;
                    avatarImg.style.display = 'block';
                } else {
                    const wrap = document.getElementById('avatar-wrap');
                    const img  = document.createElement('img');
                    img.id = 'avatar-img';
                    img.src = e.target.result;
                    img.style.cssText = 'width:100%;height:100%;object-fit:cover;';
                    wrap.insertBefore(img, wrap.firstChild);
                    if (avatarInit) avatarInit.style.display = 'none';
                }
            };
            reader.readAsDataURL(file);
            photoForm.submit();
        });
    }

    // ── Password toggle ──
    document.querySelectorAll('.pwd-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const input = document.getElementById(this.dataset.target);
            const icon  = this.querySelector('i');
            if (!input) return;
            if (input.type === 'password') { input.type = 'text'; icon.className = 'bi bi-eye-slash'; }
            else { input.type = 'password'; icon.className = 'bi bi-eye'; }
        });
    });

    // ── Password strength + match ──
    const newPwdInput  = document.getElementById('new_password');
    const confirmInput = document.getElementById('new_password_confirmation');
    const strengthBar  = document.getElementById('strength-bar');
    const strengthLabel= document.getElementById('strength-label');
    const matchMsg     = document.getElementById('pwd-match-msg');

    function getStrength(pwd) {
        let score = 0;
        if (pwd.length >= 8)           score++;
        if (pwd.length >= 12)          score++;
        if (/[A-Z]/.test(pwd))         score++;
        if (/[0-9]/.test(pwd))         score++;
        if (/[^A-Za-z0-9]/.test(pwd))  score++;
        return score;
    }

    if (newPwdInput && strengthBar) {
        newPwdInput.addEventListener('input', function () {
            const val   = this.value;
            const score = getStrength(val);
            const pct   = val.length === 0 ? 0 : Math.min(100, (score / 5) * 100);
            let color = '#DC3545', label = 'Weak';
            if (score >= 4) { color = '#1B6B3A'; label = 'Strong'; }
            else if (score >= 3) { color = '#F59E0B'; label = 'Fair'; }
            else if (score >= 2) { color = '#F5C518'; label = 'Weak'; }
            strengthBar.style.width      = pct + '%';
            strengthBar.style.background = color;
            strengthLabel.textContent    = val.length === 0 ? '' : label;
            strengthLabel.style.color    = color;
            checkMatch();
        });
    }

    function checkMatch() {
        if (!confirmInput || !newPwdInput || !matchMsg) return;
        const confirmVal = confirmInput.value;
        if (confirmVal.length === 0) { matchMsg.style.display = 'none'; return; }
        matchMsg.style.display = 'block';
        if (newPwdInput.value === confirmVal) {
            matchMsg.textContent = '✓ Passwords match';
            matchMsg.style.color = '#1B6B3A';
        } else {
            matchMsg.textContent = '✗ Passwords do not match';
            matchMsg.style.color = '#DC3545';
        }
    }

    if (confirmInput) confirmInput.addEventListener('input', checkMatch);
});

function confirmAccountDeletion() {
    Swal.fire({
        title: 'Delete Your Account?',
        text: 'This will schedule your account for permanent deletion in 30 days. You will be logged out immediately. Are you sure?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC3545',
        cancelButtonColor: '#1B6B3A',
        confirmButtonText: 'Yes, Delete Account',
        cancelButtonText: 'Cancel',
        input: 'password',
        inputPlaceholder: 'Enter your password to confirm',
        inputAttributes: {
            autocapitalize: 'off',
            autocorrect: 'off'
        },
        showLoaderOnConfirm: true,
        preConfirm: (password) => {
            if (!password) {
                Swal.showValidationMessage('Password is required');
                return false;
            }
            return fetch('{{ route("student.account.destroy") }}', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ password: password })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(json => { throw new Error(json.message || 'Verification failed') });
                }
                return response.json();
            })
            .catch(error => {
                Swal.showValidationMessage(`Request failed: ${error}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Scheduled!',
                text: 'Your account has been scheduled for deletion.',
                icon: 'success'
            }).then(() => {
                window.location.href = '{{ route("login") }}';
            });
        }
    });
}
</script>
@endpush
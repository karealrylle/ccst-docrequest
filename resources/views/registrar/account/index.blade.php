@extends('layouts.registrar')

@section('title', 'My Account')

@section('content')

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
                    Member since {{ auth()->user()->created_at->format('F Y') }}
                </span>
            </div>

            {{-- ── Avatar + name block ── --}}
            <div style="display:flex; align-items:center; gap:20px; margin-bottom:20px;">

                <div style="position:relative; flex-shrink:0;">
                    <div id="avatar-wrap"
                         onclick="document.getElementById('photo-input').click()"
                         style="width:80px; height:80px; border-radius:50%;
                                overflow:hidden; cursor:pointer; position:relative;
                                border:3px solid #D0DDD0; flex-shrink:0;">

                        @if(auth()->user()->profile_photo)
                            <img id="avatar-img"
                                 src="{{ route('registrar.account.photo') }}"
                                 alt="Profile Photo"
                                 style="width:100%; height:100%; object-fit:cover;">
                        @else
                            <div id="avatar-initials"
                                 style="width:100%; height:100%; background:#1B6B3A;
                                        display:flex; align-items:center; justify-content:center;
                                        font-size:1.6rem; font-weight:700; color:white;
                                        font-family:'Poppins',sans-serif;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
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
                      action="{{ route('registrar.account.updatePhoto') }}"
                      enctype="multipart/form-data"
                      style="display:none;">
                    @csrf
                    <input type="file" id="photo-input" name="profile_photo"
                           accept=".jpg,.jpeg,.png" style="display:none;">
                </form>

                <div>
                    <div style="font-size:1.1rem; font-weight:700; color:#1A1A1A; line-height:1.3;">
                        {{ auth()->user()->name }}
                    </div>
                    <div style="margin-top:4px;">
                        <span style="background:#F5C518; color:#1A1A1A; font-size:0.68rem;
                                     font-weight:700; padding:2px 10px; border-radius:20px;
                                     text-transform:uppercase; letter-spacing:0.5px;">
                            Registrar
                        </span>
                    </div>
                    <div style="font-size:0.78rem; color:#888; margin-top:5px;">
                        {{ auth()->user()->email }}
                    </div>
                    <div style="font-size:0.72rem; color:#aaa; margin-top:2px;">
                        Click the avatar to change your photo
                    </div>
                </div>
            </div>

            <div class="section-divider"></div>

            {{-- ── Read-only account information ── --}}
            <div style="font-size:0.72rem; font-weight:700; color:#888;
                        text-transform:uppercase; letter-spacing:0.4px; margin-bottom:10px;">
                Account Information
                <span style="font-weight:400; text-transform:none; color:#aaa; margin-left:6px;">
                    — contact administrator to update these fields
                </span>
            </div>

            <div class="form-row-2 mb-2">
                <div class="form-field">
                    <label>Full Name</label>
                    <div class="field-readonly">{{ auth()->user()->name }}</div>
                </div>
                <div class="form-field">
                    <label>Role</label>
                    <div class="field-readonly">Registrar</div>
                </div>
            </div>

            <div class="form-row-2">
                <div class="form-field">
                    <label>Email Address</label>
                    <div class="field-readonly"
                         style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                        {{ auth()->user()->email }}
                    </div>
                </div>
                <div class="form-field">
                    <label>Member Since</label>
                    <div class="field-readonly">{{ auth()->user()->created_at->format('F d, Y') }}</div>
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

            <form method="POST" action="{{ route('registrar.account.updateProfile') }}" id="profile-form">
                @csrf
                @method('PATCH')

                <div class="form-row-2 mb-3">
                    <div class="form-field">
                        <label>Contact Number <span style="color:#DC3545;">*</span></label>
                        <input type="text"
                               name="contact_number"
                               class="field-input @error('contact_number') input-error @enderror"
                               value="{{ old('contact_number', auth()->user()->contact_number) }}"
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
                               value="{{ old('address', auth()->user()->address) }}"
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

            <form method="POST" action="{{ route('registrar.account.updatePassword') }}" id="password-form">
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

    <div style="padding-bottom:20px;"></div>

</div>{{-- end req-scroll --}}

@endsection

@section('right-panel')


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
        overflow-y: auto; overflow-x: hidden;
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

    .btn-submit { display:inline-flex; align-items:center; background:#1A9FE0; color:white; font-weight:700; font-size:0.85rem; padding:10px 24px; border:none; border-radius:6px; cursor:pointer; text-decoration:none; font-family:'Poppins',sans-serif; transition:background 0.2s; }
    .btn-submit:hover { background:#0D7FBF; color:white; }


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

    // Live clock

});
</script>
@endpush
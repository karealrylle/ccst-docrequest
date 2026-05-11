<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — CCST DocRequest</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Volkhov:wght@700&display=swap" rel="stylesheet">

    <style>
        :root {
            --blue-main:   #1A9FE0;
            --blue-dark:   #0D7FBF;
            --green-dark:  #1B6B3A;
            --panel-bg:    #F6FFF1;
            --text-dark:   #1A1A2E;
            --text-gray:   #6B7280;
            --white:       #FFFFFF;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            overflow: hidden;
            display: flex;
            background: transparent;
            position: relative;
            z-index: 1;
        }

        /* Full-page background */
        .page-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
        }

        .page-bg .bg-img {
            position: absolute;
            inset: 0;
            background-size: 54.5%;
            background-position: bottom left;
            background-repeat: no-repeat;
        }

        .page-bg .bg-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                to right,
                rgba(1, 58, 29, 0.703)    0%,
                rgba(104, 163, 131, 0.568) 71%,
                rgba(42, 93, 66, 0.215)    100%
            );
        }

        /* Left panel */
        .left-panel {
            width: 50%;
            flex-shrink: 0;
            background: transparent;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 48px;
            overflow: hidden;
            animation: fadeUp 0.5s ease both;
        }

        .left-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .left-content .ccst-logo {
            width: 280px;
            height: 280px;
            object-fit: contain;
            margin-bottom: 34px;
            filter: drop-shadow(0 4px 12px rgba(0,0,0,0.35));
        }

        .left-content h1 {
            font-family: 'Volkhov', serif;
            font-size: 2.22rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 16px;
            text-shadow: 0 2px 8px rgba(0,0,0,0.3);
            text-align: center;
            width: 100%;
        }

        .left-content p {
            font-size: 0.98rem;
            line-height: 1.6;
            color: rgba(255,255,255,0.88);
            max-width: 380px;
        }

        /* Right panel */
        .right-panel {
            position: fixed;
            top: 0;
            right: 0;
            width: 50%;
            height: 100vh;
            background: var(--panel-bg);
            border-radius: 75px 0 0 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 48px;
            box-shadow: -8px 0 40px rgba(0,0,0,0.18);
            overflow-y: auto;
            position: relative;
            z-index: 1;
        }

        /* Curve image as background */
        .curve-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            overflow: hidden;
            z-index: 0;
            opacity: 0.15;
            pointer-events: none;
        }

        .curve-bg img {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: auto;
        }

        /* Login card */
        .login-card {
            width: 100%;
            max-width: 420px;
            background: var(--white);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0px 7px 20px 5px rgba(0,0,0,0.25);
            animation: fadeUp 0.5s ease both;
            position: relative;
            z-index: 1;
        }

        .card-header-strip {
            background: var(--blue-main);
            padding: 20px 32px;
            text-align: center;
        }

        .card-header-strip h2 {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--white);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .card-body-inner {
            padding: 24px 32px 28px;
        }

        .card-subtitle {
            text-align: center;
            color: var(--text-gray);
            font-size: 0.82rem;
            margin-bottom: 22px;
        }

        /* Fields */
        .field-group { margin-bottom: 16px; }

        .field-group label {
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .input-wrap { position: relative; }

        .input-wrap input {
            width: 100%;
            padding: 11px 16px 11px 40px;
            border: 2px solid #DBEAFE;
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.875rem;
            color: var(--text-dark);
            background: #F8FAFF;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }

        .input-wrap input::placeholder { color: #B0BAC8; }

        .input-wrap input:focus {
            border-color: var(--blue-main);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(26,159,224,0.12);
        }

        .input-wrap input.is-invalid {
            border-color: #EF4444;
            background: #FFF5F5;
        }

        .field-icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--blue-main);
            font-size: 0.95rem;
            pointer-events: none;
        }

        .toggle-pw {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #B0BAC8;
            cursor: pointer;
            padding: 0;
            font-size: 0.9rem;
            line-height: 1;
        }

        .toggle-pw:hover { color: var(--blue-main); }

        .field-error {
            color: #EF4444;
            font-size: 0.72rem;
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 4px;
            font-weight: 500;
        }

        /* Login button */
        .btn-login {
            width: 100%;
            padding: 12px;
            background: var(--blue-main);
            color: white;
            border: none;
            border-radius: 50px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 8px;
            transition: background 0.2s, transform 0.1s;
            box-shadow: 0 4px 14px rgba(26,159,224,0.35);
        }

        .btn-login:hover {
            background: var(--blue-dark);
            transform: translateY(-1px);
        }

        /* Below-card text */
        .below-card {
            margin-top: 20px;
            text-align: center;
            font-size: 0.78rem;
            color: var(--text-gray);
        }

        .below-card a {
            color: var(--blue-main);
            font-weight: 700;
            text-decoration: none;
        }

        /* Footer */
        .right-footer {
            position: absolute;
            bottom: 20px;
            text-align: center;
            font-size: 0.72rem;
            color: var(--text-gray);
            line-height: 1.6;
            left: 0;
            right: 0;
        }

        .right-footer a {
            color: var(--blue-main);
            font-weight: 600;
            text-decoration: none;
        }

        /* Session status */
        .session-status {
            background: #ECFDF5;
            color: #065F46;
            border: 1px solid #A7F3D0;
            border-radius: 8px;
            padding: 9px 13px;
            font-size: 0.78rem;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 7px;
            font-weight: 500;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            body { flex-direction: column; height: auto; overflow: auto; }
            .page-bg .bg-img { background-size: cover; background-position: center; }
            .left-panel { width: 100%; min-height: 260px; padding: 32px 24px; }
            .left-content .ccst-logo { width: 180px; height: 180px; margin-bottom: 20px; }
            .left-content h1 { font-size: 1.6rem; }
            .right-panel { width: 100%; border-radius: 0; padding: 32px 24px 0; position: relative; background: transparent; box-shadow: none; display: flex; flex-direction: column; min-height: calc(100vh - 260px); }
            .right-footer { position: relative; margin-top: 40px; background: var(--panel-bg); padding: 25px 20px 30px; border-radius: 20px 20px 0 0; margin-left: -24px; margin-right: -24px; margin-bottom: 0; z-index: 10; }
            .below-card { color: white; margin-bottom: 30px; }
            .below-card a { color: #F5C518; }
            .curve-bg { display: none; }
        }
    </style>
</head>
<body>

    {{-- Full-page background --}}
    <div class="page-bg">
        <div class="bg-img" style="background-image:url({{ json_encode(asset('images/ccst-building.jpeg')) }})"></div>
        <div class="bg-overlay"></div>
    </div>

    {{-- LEFT PANEL --}}
    <div class="left-panel">
        <div class="left-content">
            <img class="ccst-logo" src="{{ asset('images/ccst-logo.png') }}" alt="CCST Logo" onerror="this.style.display='none'">
            <h1>Online Document Request<br>and Tracking System</h1>
            <p>Quick, easy and secure: Clark College of Science and Technology's Online Document Request and Tracking System for SHS Registrar</p>
        </div>
    </div>

    {{-- RIGHT PANEL --}}
    <div class="right-panel">

        {{-- Curve image as background --}}
        <div class="curve-bg">
            <img src="{{ asset('images/right-panel-login.png') }}" alt="">
        </div>

        {{-- Login card --}}
        <div class="login-card">

            <div class="card-header-strip">
                <h2>Log In Your Account</h2>
            </div>

            <div class="card-body-inner">

                <p class="card-subtitle">Effortlessly request documents online</p>

                @if (session('status'))
                    <div class="session-status">
                        <i class="bi bi-check-circle-fill"></i>
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    {{-- Email --}}
                    <div class="field-group">
                        <label for="email">Email</label>
                        <div class="input-wrap">
                            <i class="bi bi-envelope field-icon"></i>
                            <input type="email" id="email" name="email"
                                value="{{ old('email') }}"
                                placeholder="Enter email address"
                                autocomplete="email" autofocus
                                class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                                required>
                        </div>
                        @error('email')
                            <div class="field-error">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="field-group">
                        <label for="password">Password</label>
                        <div class="input-wrap">
                            <i class="bi bi-lock field-icon"></i>
                            <input type="password" id="password" name="password"
                                placeholder="Enter your password"
                                autocomplete="current-password"
                                class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                                required>
                            <button type="button" class="toggle-pw" onclick="togglePassword()" tabindex="-1">
                                <i class="bi bi-eye" id="pw-icon"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="field-error">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <button type="submit" class="btn-login">Login</button>

                </form>

            </div>
        </div>

        {{-- Below card text --}}
        <div class="below-card">
            Don't have an account? <a href="{{ route('register') }}">Register here</a>
        </div>

        {{-- Footer --}}
        <div class="right-footer">
            <a href="#">CCST Website</a><br>
            &copy; Copyright 2026 Clark College of Science and Technology<br>
            Document Request System
        </div>

    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('pw-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }
    </script>

</body>
</html>
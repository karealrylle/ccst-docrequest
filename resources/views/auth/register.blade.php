<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — CCST DocRequest</title>

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
            /* Removed justify-content: center to prevent clipping when content is long */
            padding: 60px 48px;
            box-shadow: -8px 0 40px rgba(0,0,0,0.18);
            overflow-y: auto;
            position: relative;
            z-index: 1;
        }

        /* Curve image as background inside right panel */
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

        /* Register card */
        .register-card {
            width: 100%;
            max-width: 550px;
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
            padding: 15px 27px;
            text-align: center;
        }

        .card-header-strip h2 {
            font-size: 0.98rem;
            font-weight: 800;
            color: var(--white);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .card-body-inner {
            padding: 19px 24px 22px;
            max-height: 72vh;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--blue-main) transparent;
        }

        .card-body-inner::-webkit-scrollbar {
            width: 6px;
        }

        .card-body-inner::-webkit-scrollbar-track {
            background: transparent;
        }

        .card-body-inner::-webkit-scrollbar-thumb {
            background-color: var(--blue-main);
            border-radius: 10px;
        }

        .card-subtitle {
            text-align: center;
            color: var(--text-gray);
            font-size: 0.68rem;
            margin-bottom: 15px;
        }

        /* Name fields - 3 columns */
        .name-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 0 12px;
        }

        .fields-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0 14px;
        }

        .field-full { grid-column: 1 / -1; }

        .field-group { margin-bottom: 12px; }

        .field-group label {
            display: block;
            font-size: 0.65rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 4px;
        }

        .input-wrap { position: relative; }

        .input-wrap input,
        .input-wrap select {
            width: 100%;
            padding: 9px 12px 9px 32px;
            border: 2px solid #DBEAFE;
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.7rem;
            color: var(--text-dark);
            background: #F8FAFF;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
            appearance: none;
        }

        .input-wrap input:focus,
        .input-wrap select:focus {
            border-color: var(--blue-main);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(26,159,224,0.12);
        }

        .input-wrap input.is-invalid,
        .input-wrap select.is-invalid {
            border-color: #EF4444;
            background: #FFF5F5;
        }

        .field-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--blue-main);
            font-size: 0.77rem;
            pointer-events: none;
            z-index: 1;
        }

        .toggle-pw {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #B0BAC8;
            cursor: pointer;
            padding: 0;
            font-size: 0.85rem;
            line-height: 1;
        }

        .toggle-pw:hover { color: var(--blue-main); }

        .field-error {
            color: #EF4444;
            font-size: 0.68rem;
            margin-top: 3px;
            display: flex;
            align-items: center;
            gap: 3px;
            font-weight: 500;
        }

        .btn-register {
            width: 100%;
            padding: 9px;
            background: var(--blue-main);
            color: white;
            border: none;
            border-radius: 50px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.77rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 6px;
            margin-bottom: 10px;
            transition: background 0.2s, transform 0.1s;
            box-shadow: 0 4px 14px rgba(26,159,224,0.35);
        }

        .btn-register:hover {
            background: var(--blue-dark);
            transform: translateY(-1px);
        }

        .below-card {
            margin-top: 14px;
            text-align: center;
            font-size: 0.78rem;
            color: var(--text-gray);
        }

        .below-card a {
            color: var(--blue-main);
            font-weight: 700;
            text-decoration: none;
        }

        .right-footer {
            position: absolute;
            bottom: 16px;
            text-align: center;
            font-size: 0.7rem;
            color: var(--text-gray);
            line-height: 1.6;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            body { flex-direction: column; height: auto; overflow: auto; }
            .page-bg .bg-img { background-size: cover; background-position: center; }
            .left-panel { width: 100%; min-height: 220px; padding: 32px 24px; }
            .left-content .ccst-logo { width: 150px; height: 150px; margin-bottom: 15px; }
            .left-content h1 { font-size: 1.4rem; }
            .right-panel { width: 100%; border-radius: 0; margin-left: 0; padding: 32px 20px 0; position: relative; background: transparent; box-shadow: none; display: flex; flex-direction: column; min-height: calc(100vh - 220px); }
            .card-body-inner { padding-bottom: 40px; }
            .right-footer { position: relative; margin-top: 40px; background: var(--panel-bg); padding: 25px 20px 30px; border-radius: 20px 20px 0 0; margin-left: -20px; margin-right: -20px; margin-bottom: 0; z-index: 10; }
            .below-card { color: white; margin-bottom: 30px; }
            .below-card a { color: #F5C518; }
            .curve-bg { display: none; }
            .fields-grid { grid-template-columns: 1fr; }
            .field-full { grid-column: 1; }
            .name-grid { grid-template-columns: 1fr; gap: 10px; }
        }
    </style>
</head>
<body>

    <div class="page-bg">
        <div class="bg-img" style="background-image:url({{ json_encode(asset('images/ccst-building.jpeg')) }})"></div>
        <div class="bg-overlay"></div>
    </div>

    <div class="left-panel">
        <div class="left-content">
            <img class="ccst-logo" src="{{ asset('images/ccst-logo.png') }}" alt="CCST Logo" onerror="this.style.display='none'">
            <h1>Online Document Request<br>and Tracking System</h1>
            <p>Quick, easy and secure: Clark College of Science and Technology's Online Document Request and Tracking System for SHS Registrar</p>
        </div>
    </div>

    <div class="right-panel">
        {{-- Curve image as background (behind card) --}}
        <div class="curve-bg">
            <img src="{{ asset('images/right-panel-login.png') }}" alt="">
        </div>

        <div class="register-card">
            <div class="card-header-strip">
                <h2>Create an Account</h2>
            </div>
            <div class="card-body-inner">
                <p class="card-subtitle">Fill in your details to register as a student</p>

                <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                    @csrf

                    {{-- Name Fields - 3 columns --}}
                    <div class="name-grid">
                        <div class="field-group">
                            <label for="first_name">First Name</label>
                            <div class="input-wrap">
                                <i class="bi bi-person field-icon"></i>
                                <input type="text" id="first_name" name="first_name"
                                    value="{{ old('first_name') }}"
                                    placeholder="e.g. Maria"
                                    class="{{ $errors->has('first_name') ? 'is-invalid' : '' }}"
                                    required>
                            </div>
                            @error('first_name')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-group">
                            <label for="middle_name">Middle Name</label>
                            <div class="input-wrap">
                                <i class="bi bi-person-badge field-icon"></i>
                                <input type="text" id="middle_name" name="middle_name"
                                    value="{{ old('middle_name') }}"
                                    placeholder="e.g. Santos (if none, leave blank)">
                            </div>
                            @error('middle_name')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-group">
                            <label for="last_name">Last Name</label>
                            <div class="input-wrap">
                                <i class="bi bi-person-circle field-icon"></i>
                                <input type="text" id="last_name" name="last_name"
                                    value="{{ old('last_name') }}"
                                    placeholder="e.g. Dela Cruz"
                                    class="{{ $errors->has('last_name') ? 'is-invalid' : '' }}"
                                    required>
                            </div>
                            @error('last_name')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="fields-grid">
                        {{-- Student Number --}}
                        <div class="field-group">
                            <label for="student_number">Student Number</label>
                            <div class="input-wrap">
                                <i class="bi bi-hash field-icon"></i>
                                <input type="text" id="student_number" name="student_number"
                                    value="{{ old('student_number') }}"
                                    placeholder="e.g. 05-8959"
                                    class="{{ $errors->has('student_number') ? 'is-invalid' : '' }}"
                                    required>
                            </div>
                            @error('student_number')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Contact Number --}}
                        <div class="field-group">
                            <label for="contact_number">Contact Number</label>
                            <div class="input-wrap">
                                <i class="bi bi-telephone field-icon"></i>
                                <input type="text" id="contact_number" name="contact_number"
                                    value="{{ old('contact_number') }}"
                                    placeholder="09XXXXXXXXX"
                                    class="{{ $errors->has('contact_number') ? 'is-invalid' : '' }}"
                                    required>
                            </div>
                            @error('contact_number')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="field-group field-full">
                            <label for="email">Email Address</label>
                            <div class="input-wrap">
                                <i class="bi bi-envelope field-icon"></i>
                                <input type="email" id="email" name="email"
                                    value="{{ old('email') }}"
                                    placeholder="Enter email address"
                                    autocomplete="email"
                                    class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                                    required>
                            </div>
                            @error('email')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Strand --}}
                        <div class="field-group">
                            <label for="strand">Strand</label>
                            <div class="input-wrap">
                                <i class="bi bi-book field-icon"></i>
                                <select id="strand" name="strand"
                                    class="{{ $errors->has('strand') ? 'is-invalid' : '' }}"
                                    required>
                                    <option value="" disabled {{ old('strand') ? '' : 'selected' }}>Select strand</option>
                                    <option value="ABM"  {{ old('strand') == 'ABM'  ? 'selected' : '' }}>ABM</option>
                                    <option value="ICT"  {{ old('strand') == 'ICT'  ? 'selected' : '' }}>ICT</option>
                                    <option value="HUMSS"{{ old('strand') == 'HUMSS'? 'selected' : '' }}>HUMSS</option>
                                    <option value="STEM" {{ old('strand') == 'STEM' ? 'selected' : '' }}>STEM</option>
                                    <option value="GAS"  {{ old('strand') == 'GAS'  ? 'selected' : '' }}>GAS</option>
                                    <option value="HE"   {{ old('strand') == 'HE'   ? 'selected' : '' }}>HE</option>
                                </select>
                            </div>
                            @error('strand')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Grade Level --}}
                        <div class="field-group">
                            <label for="grade_level">Grade Level</label>
                            <div class="input-wrap">
                                <i class="bi bi-mortarboard field-icon"></i>
                                <select id="grade_level" name="grade_level"
                                    class="{{ $errors->has('grade_level') ? 'is-invalid' : '' }}"
                                    required>
                                    <option value="" disabled {{ old('grade_level') ? '' : 'selected' }}>Select grade</option>
                                    <option value="Grade 11" {{ old('grade_level') == 'Grade 11' ? 'selected' : '' }}>Grade 11</option>
                                    <option value="Grade 12" {{ old('grade_level') == 'Grade 12' ? 'selected' : '' }}>Grade 12</option>
                                </select>
                            </div>
                            @error('grade_level')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Section - Dynamic Dropdown --}}
                        <div class="field-group field-full">
                            <label for="section">Section</label>
                            <div class="input-wrap">
                                <i class="bi bi-people field-icon"></i>
                                <select id="section" name="section"
                                    class="{{ $errors->has('section') ? 'is-invalid' : '' }}"
                                    required>
                                    <option value="">Select strand and grade level first</option>
                                </select>
                            </div>
                            @error('section')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Student ID Upload --}}
                        <div class="field-group field-full">
                            <label for="student_id_photo">Student ID <span style="color:#DC3545;">*</span></label>
                            <div class="input-wrap">
                                <i class="bi bi-card-image field-icon"></i>
                                <input type="file" id="student_id_photo" name="student_id_photo"
                                    accept=".jpg,.jpeg,.png,.pdf"
                                    class="field-input @error('student_id_photo') is-invalid @enderror"
                                    style="padding-left: 32px;"
                                    required>
                            </div>
                            
                            {{-- ID Preview --}}
                            <div id="id-preview-container" style="display: none; margin-top: 10px; text-align: center; border: 2px dashed #DBEAFE; padding: 10px; border-radius: 12px; background: #F8FAFF;">
                                <p style="font-size: 0.6rem; color: #1B6B3A; font-weight: 700; margin-bottom: 5px;">ID PREVIEW</p>
                                <img id="id-preview-img" src="#" alt="ID Preview" style="max-width: 100%; max-height: 150px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                <div id="pdf-preview-icon" style="display: none;">
                                    <i class="bi bi-file-pdf-fill" style="font-size: 3rem; color: #DC3545;"></i>
                                    <p id="pdf-name" style="font-size: 0.7rem; color: #1A1A2E; margin-top: 5px;"></p>
                                </div>
                            </div>

                            <small style="font-size:0.65rem; color:#666;">Upload a clear photo of your school ID (JPG, PNG, or PDF, max 2MB)</small>
                            @error('student_id_photo')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="field-group">
                            <label for="password">Password</label>
                            <div class="input-wrap">
                                <i class="bi bi-lock field-icon"></i>
                                <input type="password" id="password" name="password"
                                    placeholder="Create password"
                                    autocomplete="new-password"
                                    class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                                    required>
                                <button type="button" class="toggle-pw" onclick="togglePw('password','pw-icon1')" tabindex="-1">
                                    <i class="bi bi-eye" id="pw-icon1"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Confirm Password --}}
                        <div class="field-group">
                            <label for="password_confirmation">Confirm Password</label>
                            <div class="input-wrap">
                                <i class="bi bi-lock-fill field-icon"></i>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    placeholder="Repeat password"
                                    autocomplete="new-password"
                                    required>
                                <button type="button" class="toggle-pw" onclick="togglePw('password_confirmation','pw-icon2')" tabindex="-1">
                                    <i class="bi bi-eye" id="pw-icon2"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-register">Create Account</button>
                </form>
            </div>
        </div>

        <div class="below-card">
            Already have an account? <a href="{{ route('login') }}">Sign in here</a>
        </div>

        <div class="right-footer">
            {{-- <a href="#">CCST Website</a><br> --}}
            &copy; Copyright 2026 Clark College of Science and Technology<br>
            Document Request System
        </div>
    </div>

    <script>
        function togglePw(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }

        // Dynamic section dropdown
        const strandSelect = document.getElementById('strand');
        const gradeLevelSelect = document.getElementById('grade_level');
        const sectionSelect = document.getElementById('section');

        function generateSections() {
            const strand = strandSelect.value;
            const gradeLevel = gradeLevelSelect.value;
            
            if (!strand || !gradeLevel) {
                sectionSelect.innerHTML = '<option value="">Select strand and grade level first</option>';
                return;
            }
            
            const gradeNum = gradeLevel === 'Grade 11' ? '11' : '12';
            const sections = ['A', 'B', 'C', 'D', 'E'];
            let options = '<option value="" disabled selected>Select section</option>';
            
            sections.forEach(letter => {
                const sectionValue = `${strand}-${gradeNum}${letter}`;
                const oldValue = "{{ old('section') }}";
                const selected = oldValue === sectionValue ? 'selected' : '';
                options += `<option value="${sectionValue}" ${selected}>${sectionValue}</option>`;
            });
            
            sectionSelect.innerHTML = options;
        }

        strandSelect.addEventListener('change', generateSections);
        gradeLevelSelect.addEventListener('change', generateSections);
        
        if (strandSelect.value && gradeLevelSelect.value) {
            generateSections();
        }

        // ID Photo Preview Logic
        const idInput = document.getElementById('student_id_photo');
        const previewContainer = document.getElementById('id-preview-container');
        const previewImg = document.getElementById('id-preview-img');
        const pdfPreviewIcon = document.getElementById('pdf-preview-icon');
        const pdfName = document.getElementById('pdf-name');

        idInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                previewContainer.style.display = 'block';

                if (file.type === 'application/pdf') {
                    previewImg.style.display = 'none';
                    pdfPreviewIcon.style.display = 'block';
                    pdfName.textContent = file.name;
                } else {
                    pdfPreviewIcon.style.display = 'none';
                    previewImg.style.display = 'inline-block';
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                    }
                    reader.readAsDataURL(file);
                }
            } else {
                previewContainer.style.display = 'none';
            }
        });
    </script>
</body>
</html>
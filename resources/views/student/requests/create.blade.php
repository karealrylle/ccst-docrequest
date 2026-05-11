@extends('layouts.student')

@section('title', 'Request Document')

@section('content')

{{-- Sticky card header — stays visible when scrolling --}}
<div class="req-sticky-header">DOCUMENT REQUEST FORM</div>

<div class="req-scroll">
<form method="POST" action="{{ route('student.requests.store') }}" id="requestForm">
    @csrf

    <div class="req-card">

        <div class="req-card-body">

            {{-- ── STUDENT INFORMATION ── --}}
            <div class="section-heading-row">
                <span class="section-heading">STUDENT INFORMATION</span>
                <span class="req-date">Date: {{ now()->format('m/d/Y') }}</span>
            </div>

            {{-- Row 1: Student Number, Email, Contact --}}
            <div class="form-row-3">
                <div class="form-field">
                    <label>Student Number *</label>
                    <input type="text" value="{{ auth()->user()->student_number }}" readonly
                           class="field-readonly">
                    {{-- Hidden field for submission --}}
                    <input type="hidden" name="student_number" value="{{ auth()->user()->student_number }}">
                </div>
                <div class="form-field">
                    <label>Email *</label>
                    <input type="text" value="{{ auth()->user()->email }}" readonly class="field-readonly">
                    <input type="hidden" name="email" value="{{ auth()->user()->email }}">
                </div>
                <div class="form-field">
                    <label>Contact No. *</label>
                    <input type="text" value="{{ auth()->user()->contact_number }}" readonly class="field-readonly">
                    <input type="hidden" name="contact_number" value="{{ auth()->user()->contact_number }}">
                </div>
            </div>

            {{-- Row 2: First Name, Middle Name, Last Name (auto-filled from user profile) --}}
            <div class="form-row-3">
                <div class="form-field">
                    <label>First Name *</label>
                    <input type="text" value="{{ auth()->user()->first_name }}" readonly class="field-readonly">
                    <input type="hidden" name="first_name" value="{{ auth()->user()->first_name }}">
                </div>
                <div class="form-field">
                    <label>Middle Name</label>
                    <input type="text" value="{{ auth()->user()->middle_name ?? '—' }}" readonly class="field-readonly">
                    <input type="hidden" name="middle_name" value="{{ auth()->user()->middle_name }}">
                </div>
                <div class="form-field">
                    <label>Last Name *</label>
                    <input type="text" value="{{ auth()->user()->last_name }}" readonly class="field-readonly">
                    <input type="hidden" name="last_name" value="{{ auth()->user()->last_name }}">
                </div>
            </div>

            {{-- Row 3: Course/Program read-only --}}
            <div class="form-row-1">
                <div class="form-field">
                    <label>Course / Program *</label>
                    <input type="text" value="{{ auth()->user()->strand }}" readonly class="field-readonly">
                    <input type="hidden" name="course_program" value="{{ auth()->user()->strand }}">
                </div>
            </div>

            {{-- Row 4: Year & Section (auto-filled, read-only) --}}
            <div class="form-row-short">
                <div class="form-field">
                    <label>Year *</label>
                    <input type="text" value="{{ auth()->user()->grade_level }}" readonly class="field-readonly">
                    <input type="hidden" name="year_level" value="{{ auth()->user()->grade_level }}">
                </div>
                <div class="form-field">
                    <label>Section *</label>
                    <input type="text" value="{{ auth()->user()->section }}" readonly class="field-readonly">
                    <input type="hidden" name="section" value="{{ auth()->user()->section }}">
                </div>
            </div>

            {{-- ── DOCUMENTS AVAILABLE ── --}}
            <div class="section-heading" style="margin-top:20px; margin-bottom:10px;">
                DOCUMENTS AVAILABLE
            </div>

            <table class="docs-table">
                <thead>
                    <tr class="docs-table-header">
                        <th style="width:40px;"></th>
                        <th>Document</th>
                        <th style="width:80px;">Price</th>
                        <th style="width:90px;">Est. Days</th>
                        <th style="width:100px;">Quantity</th>
                        <th style="width:130px;">Assessment Year</th>
                        <th style="width:120px;">Grading Period</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($documentTypes as $doc)
                    <tr class="doc-row" data-fee="{{ $doc->fee }}" data-has-year="{{ $doc->has_school_year ? '1' : '0' }}" data-is-printable="{{ $doc->is_printable ? '1' : '0' }}">
                        <td class="text-center">
                            <input type="checkbox"
                                   class="doc-checkbox"
                                   value="1"
                                   onchange="toggleDocRow(this)">
                        </td>
                        <td class="doc-name-cell">{{ $doc->name }}</td>
                        <td class="doc-price">₱{{ number_format($doc->fee, 2) }}</td>
                        <td class="text-center doc-days">{{ $doc->processing_days }} day{{ $doc->processing_days > 1 ? "s" : "" }}</td>
                        <td class="text-center">
                            {{--
                                FIX 1: Added a hidden document_type_id input.
                                The controller needs documents[n][document_type_id] to know
                                which document type this row refers to. Without this, the
                                controller gets no document_type_id and throws validation errors.
                                These inputs (along with copies/ay/semester) are DISABLED for
                                unchecked rows at submit time — see validateForm() in the JS.
                            --}}
                            <input type="hidden"
                                   class="doc-type-id-input"
                                   name="documents[{{ $loop->index }}][document_type_id]"
                                   value="{{ $doc->id }}"
                                   disabled>

                            <div class="qty-wrap">
                                <button type="button" class="qty-btn" onclick="changeQty(this, -1)">
                                    <i class="bi bi-dash-circle"></i>
                                </button>
                                <input type="number"
                                       name="documents[{{ $loop->index }}][copies]"
                                       value="0" min="0" max="10"
                                       class="qty-input"
                                       disabled
                                       onchange="recalcTotal()">
                                <button type="button" class="qty-btn" onclick="changeQty(this, 1)">
                                    <i class="bi bi-plus-circle"></i>
                                </button>
                            </div>
                        </td>
                        <td class="text-center ay-cell">
                            @if($doc->has_school_year)
                                <select name="documents[{{ $loop->index }}][assessment_year]" class="mini-select ay-select" disabled>
                                    <option value="">—select—</option>
                                    <option value="A.Y. 2023-2024">A.Y. 2023-2024</option>
                                    <option value="A.Y. 2024-2025">A.Y. 2024-2025</option>
                                    <option value="A.Y. 2025-2026" selected>A.Y. 2025-2026</option>
                                </select>
                            @else
                                <span class="na-dash">—</span>
                            @endif
                        </td>
                        <td class="text-center sem-cell">
                            @if($doc->has_school_year)
                                <select name="documents[{{ $loop->index }}][semester]" class="mini-select sem-select" disabled>
                                    <option value="">—select—</option>
                                    <option value="1st Grading">1st Grading</option>
                                    <option value="2nd Grading">2nd Grading</option>
                                    <option value="3rd Grading">3rd Grading</option>
                                    <option value="4th Grading">4th Grading</option>
                                </select>
                            @else
                                <span class="na-dash">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Total --}}
            <div class="total-row">
                <span class="total-label">Total:</span>
                <input type="text" id="totalDisplay" value="₱0.00" readonly class="total-input">
                <input type="hidden" name="total_fee" id="totalFee" value="0">
            </div>

            @if($errors->any())
                <div class="alert alert-danger mt-3 py-2" style="font-size:0.82rem;">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ── APPOINTMENT INFORMATION ── --}}
            <div id="appointmentSection" style="display:none; margin-top:30px; border-top: 2px dashed #D0DDD0; padding-top:20px;">
                <div class="section-heading" style="margin-bottom:15px;">
                    APPOINTMENT INFORMATION
                    <small class="text-muted d-block" style="text-transform:none; font-weight:400; margin-top:4px;">
                        Please select your preferred date and time for document pickup.
                    </small>
                </div>

                <div class="form-row-2">
                    <div class="form-field">
                        <label>Preferred Date *</label>
                        <input type="date" name="appointment_date" id="appointmentDate" 
                               class="field-input"
                               min="{{ date('Y-m-d') }}"
                               onchange="fetchAvailableSlots()">
                    </div>
                    <div class="form-field">
                        <label>Available Time Slots *</label>
                        <select name="time_slot_id" id="timeSlotSelect" class="field-select" disabled>
                            <option value="">— select date first —</option>
                        </select>
                        <div id="slotLoading" class="small text-primary mt-1" style="display:none;">
                            <span class="spinner-border spinner-border-sm me-1"></span> Loading slots...
                        </div>
                    </div>
                </div>

                <div class="alert alert-info mt-3 py-2" style="font-size:0.78rem; border-left: 4px solid #1A9FE0;">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <strong>Note:</strong> Appointments are subject to daily capacity limits. If a slot is not visible, it may be fully booked.
                </div>
            </div>

            {{-- ── FORM 138 INSTRUCTIONS (Hidden Initially) ── --}}
            <div id="form138Instructions" style="display:none; margin-top:30px; border-top: 2px dashed #DC3545; padding-top:20px;">
                <div class="section-heading" style="margin-bottom:15px; color: #DC3545;">
                    FORM 138 REQUEST INSTRUCTIONS
                </div>
                <div class="alert alert-warning py-3" style="font-size:0.85rem; border-left: 4px solid #DC3545; background-color: #fff9f9;">
                    <i class="bi bi-exclamation-triangle-fill me-2" style="font-size: 1.1rem;"></i>
                    <span>
                        You have selected a document that requires <strong>manual verification</strong> (e.g., Form 138). Appointment booking is disabled for this request because it requires the Registrar's Office to verify your records before the document can be prepared.
                        <br><br>
                        <strong>How to proceed:</strong>
                        <ul class="mt-2 mb-0">
                            <li>Submit this request now (no appointment needed).</li>
                            <li>Our staff will verify your records and process the document.</li>
                            <li>You will receive an <strong>email notification</strong> once your Form 138 is ready for pickup.</li>
                        </ul>
                    </span>
                </div>
            </div>

            {{-- Submit + Cancel INSIDE the card --}}
            <div class="submit-row">
                <a href="{{ route('student.dashboard') }}" class="btn-cancel">Cancel</a>
                
                {{-- Initial Button --}}
                <button type="button" class="btn-book-step" id="btnBookStep" onclick="showAppointmentStep()">
                    BOOK APPOINTMENT
                </button>

                {{-- Final Submit Button (Hidden Initially) --}}
                <button type="button" class="btn-submit" id="btnFinalSubmit" style="display:none;" onclick="validateFinalForm()">
                    SUBMIT REQUEST
                </button>
            </div>

        </div>
        {{-- end req-card-body --}}
    </div>
    {{-- end req-card --}}

</form>

</div>{{-- req-scroll --}}

@endsection

@push('styles')
<style>
    /* Sticky form header — stays on top when scrolling */
    .req-sticky-header {
        background: #1B6B3A;
        color: white;
        font-size: 0.9rem;
        font-weight: 700;
        text-align: center;
        padding: 10px 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        max-width: 9z0px;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    /* Scrollable container — explicit height so form scrolls inside it */
    .req-scroll {
        background: rgba(255,255,255,0.85);
        height: calc(100vh - var(--header-h) - var(--footer-h) - 120px);
        overflow-y: auto;
        overflow-x: hidden;
    }

    /* ── Main card ── */
    .req-card {
        background: #ffffff;
        border: 1px solid #D0DDD0;
        border-radius: 0 0 12px 12px;
        overflow: hidden;
        width: 100%;
        max-width: 900px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.10);
    }

    .req-card-body {
        padding: 20px 24px;
    }

    /* ── Section headings ── */
    .section-heading-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .section-heading {
        font-size: 0.85rem;
        font-weight: 700;
        color: #1A1A1A;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .req-date {
        font-size: 0.78rem;
        color: #666;
    }

    /* ── Form rows ── */
    .form-row-3 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 12px;
        margin-bottom: 10px;
    }

    .form-row-1 {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
        margin-bottom: 10px;
    }

    .form-row-short {
        display: grid;
        grid-template-columns: 100px 1fr;
        gap: 12px;
        margin-bottom: 10px;
    }

    .form-field label {
        display: block;
        font-size: 0.74rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 4px;
    }

    .field-readonly {
        width: 100%;
        padding: 6px 10px;
        border: 1px solid #D0DDD0;
        border-radius: 4px;
        background: #f8f9fa;
        font-size: 0.8rem;
        color: #444;
        font-family: 'Poppins', sans-serif;
        cursor: default;
    }

    .field-input {
        width: 100%;
        padding: 6px 10px;
        border: 1px solid #D0DDD0;
        border-radius: 4px;
        background: white;
        font-size: 0.8rem;
        color: #1A1A1A;
        font-family: 'Poppins', sans-serif;
        outline: none;
        transition: border-color 0.2s;
    }

    .field-input:focus { border-color: #1A9FE0; }

    .field-select {
        width: 100%;
        padding: 6px 10px;
        border: 1px solid #D0DDD0;
        border-radius: 4px;
        background: white;
        font-size: 0.8rem;
        color: #1A1A1A;
        font-family: 'Poppins', sans-serif;
        outline: none;
        cursor: pointer;
    }

    /* ── Documents table ── */
    .docs-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.8rem;
        margin-bottom: 12px;
    }

    .docs-table-header {
        background: #1B6B3A;
        border-bottom: 1px solid #1B6B3A;
    }

    .docs-table-header th {
        padding: 8px 8px;
        font-size: 0.75rem;
        font-weight: 600;
        color: white;
        text-align: left;
    }

    .doc-days {
        font-size: 0.76rem;
        color: #666;
    }

    .na-dash {
        color: #bbb;
        font-size: 0.85rem;
    }

    .docs-table td {
        padding: 7px 8px;
        border-bottom: 1px solid #f0f0f0;
        vertical-align: middle;
    }

    .doc-row { transition: background 0.15s; }
    .doc-row:hover { background: #fafffe; }
    .doc-row.selected { background: #f0faf5; }

    .doc-name-cell { font-weight: 500; color: #1A1A1A; }
    .doc-price { font-weight: 600; color: #1B6B3A; }

    /* Checkbox style */
    .doc-checkbox {
        width: 15px;
        height: 15px;
        accent-color: #1B6B3A;
        cursor: pointer;
    }

    /* ── Quantity controls ── */
    .qty-wrap {
        display: flex;
        align-items: center;
        gap: 4px;
        justify-content: center;
    }

    .qty-btn {
        background: none;
        border: none;
        color: #1B6B3A;
        font-size: 1rem;
        cursor: pointer;
        padding: 0;
        line-height: 1;
        transition: color 0.15s;
    }

    .qty-btn:hover { color: #0C6637; }

    .qty-input {
        width: 36px;
        text-align: center;
        border: 1px solid #D0DDD0;
        border-radius: 4px;
        padding: 3px 4px;
        font-size: 0.8rem;
        font-family: 'Poppins', sans-serif;
        color: #1A1A1A;
    }

    .qty-input::-webkit-inner-spin-button { -webkit-appearance: none; }

    /* Dimmed look for unchecked rows */
    .doc-row:not(.selected) .qty-input,
    .doc-row:not(.selected) .mini-select {
        opacity: 0.45;
        pointer-events: none;
    }

    /* ── Mini selects for AY + Semester ── */
    .mini-select {
        font-size: 0.72rem;
        padding: 3px 4px;
        border: 1px solid #D0DDD0;
        border-radius: 4px;
        font-family: 'Poppins', sans-serif;
        width: 100%;
        color: #444;
        background: white;
    }

    /* ── Total row ── */
    .total-row {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        padding-top: 8px;
        border-top: 1px solid #D0DDD0;
    }

    .total-label {
        font-size: 0.82rem;
        font-weight: 700;
        color: #1A1A1A;
    }

    .total-input {
        width: 100px;
        text-align: right;
        padding: 5px 10px;
        border: 1px solid #D0DDD0;
        border-radius: 4px;
        font-size: 0.85rem;
        font-weight: 700;
        color: #1B6B3A;
        background: #f8f9fa;
        font-family: 'Poppins', sans-serif;
    }

    /* ── Submit row (inside card) ── */
    .submit-row {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 12px;
        margin-top: 16px;
        padding-top: 14px;
        border-top: 1px solid #D0DDD0;
    }

    .btn-cancel {
        background: #F5C518;
        color: #1A1A1A;
        font-weight: 700;
        font-size: 0.88rem;
        padding: 10px 28px;
        border-radius: 6px;
        text-decoration: none;
        transition: opacity 0.2s;
        font-family: 'Poppins', sans-serif;
    }

    .btn-cancel:hover { opacity: 0.85; color: #1A1A1A; }

    .btn-submit {
        background: #1A9FE0;
        color: white;
        font-weight: 700;
        font-size: 0.88rem;
        padding: 10px 36px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        letter-spacing: 0.5px;
        transition: background 0.2s;
        font-family: 'Poppins', sans-serif;
    }

    .btn-submit:hover { background: #0D7FBF; }

    .btn-book-step {
        background: #1B6B3A;
        color: white;
        font-weight: 700;
        font-size: 0.88rem;
        padding: 10px 36px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        letter-spacing: 0.5px;
        transition: background 0.2s;
        font-family: 'Poppins', sans-serif;
    }

    .btn-book-step:hover { background: #134D29; }

    .form-row-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 10px;
    }

    /* ── MOBILE REQUEST FORM ── */
    @media (max-width: 768px) {
        .req-sticky-header {
            font-size: 0.8rem;
            padding: 8px 15px;
        }
        .req-scroll {
            height: calc(100vh - 60px - 35px - 40px); /* Adjust for mobile header/footer */
        }
        .req-card-body {
            padding: 15px 12px;
        }
        .form-row-3, .form-row-2, .form-row-short {
            grid-template-columns: 1fr;
            gap: 10px;
        }
        .docs-table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }
        .submit-row {
            flex-direction: column;
            gap: 10px;
            padding-bottom: 20px;
        }
        .btn-cancel, .btn-submit, .btn-book-step {
            width: 100%;
            text-align: center;
            padding: 12px;
        }
        .total-row {
            justify-content: center;
            flex-direction: column;
            align-items: center;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // ─────────────────────────────────────────────────────────────────────────
    // toggleDocRow()
    // Called when student checks/unchecks a document checkbox.
    // Enables or disables the row's inputs, sets qty to 1 when first checked.
    // ─────────────────────────────────────────────────────────────────────────
    function toggleDocRow(checkbox) {
        const row      = checkbox.closest('.doc-row');
        const qtyInput = row.querySelector('.qty-input');
        const typeId   = row.querySelector('.doc-type-id-input');
        const aySelect = row.querySelector('.ay-select');
        const semSelect= row.querySelector('.sem-select');

        if (checkbox.checked) {
            // Enable the row's submittable inputs
            qtyInput.disabled  = false;
            if (typeId)    typeId.disabled   = false;
            if (aySelect)  aySelect.disabled  = false;
            if (semSelect) semSelect.disabled = false;

            row.classList.add('selected');
            if (parseInt(qtyInput.value) === 0) qtyInput.value = 1;
        } else {
            // Disable so they are excluded from the POST body entirely
            qtyInput.disabled  = true;
            if (typeId)    typeId.disabled   = true;
            if (aySelect)  aySelect.disabled  = true;
            if (semSelect) semSelect.disabled = true;

            row.classList.remove('selected');
            qtyInput.value = 0;
        }
        recalcTotal();
        updateStepButton();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // changeQty()
    // +/- buttons. Also auto-checks the row when qty goes above 0.
    // ─────────────────────────────────────────────────────────────────────────
    function changeQty(btn, delta) {
        const row      = btn.closest('.doc-row');
        const input    = row.querySelector('.qty-input');
        const checkbox = row.querySelector('.doc-checkbox');
        let val = parseInt(input.value) + delta;
        if (val < 0)  val = 0;
        if (val > 10) val = 10;
        input.value = val;

        if (val > 0 && !checkbox.checked) {
            checkbox.checked = true;
            toggleDocRow(checkbox);    // enables the row inputs
            input.value = val;         // restore value after toggleDocRow sets it to 1
        } else if (val === 0 && checkbox.checked) {
            checkbox.checked = false;
            toggleDocRow(checkbox);    // disables the row inputs
        }
        recalcTotal();
        updateStepButton();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // recalcTotal()
    // Adds up fees only for checked rows with qty > 0.
    // ─────────────────────────────────────────────────────────────────────────
    function recalcTotal() {
        let total = 0;
        document.querySelectorAll('.doc-row').forEach(row => {
            const fee     = parseFloat(row.dataset.fee) || 0;
            const qty     = parseInt(row.querySelector('.qty-input').value) || 0;
            const checked = row.querySelector('.doc-checkbox').checked;
            if (checked && qty > 0) total += fee * qty;
        });
        document.getElementById('totalDisplay').value = '₱' + total.toFixed(2);
        document.getElementById('totalFee').value = total.toFixed(2);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // validateForm()
    // Called by the SUBMIT button (type="button" — NOT type="submit").
    // 1. Checks at least one document is selected.
    // 2. Checks all selected rows have qty >= 1.
    // 3. Shows SweetAlert2 confirmation before submitting.
    //
    // FIX 2: Because the row inputs are already disabled for unchecked rows
    // (see toggleDocRow), the browser automatically excludes them from the
    // POST body. We never need to manually strip anything in the JS here —
    // it just works.
    // ─────────────────────────────────────────────────────────────────────────
    function validateForm() {
        const checkedRows = [...document.querySelectorAll('.doc-row')]
            .filter(row => row.querySelector('.doc-checkbox').checked);

        // Guard: nothing selected
        if (checkedRows.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Document Selected',
                text: 'Please select at least one document before submitting.',
                confirmButtonColor: '#1A9FE0'
            });
            return;
        }

        // Guard: selected row has qty 0
        const zeroQtyRow = checkedRows.find(
            row => parseInt(row.querySelector('.qty-input').value) < 1
        );
        if (zeroQtyRow) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Quantity',
                text: 'Please enter a quantity of at least 1 for each selected document.',
                confirmButtonColor: '#1A9FE0'
            });
            return;
        }

        // Guard: selected row requires Assessment Year / Grading Period but one or both are blank.
        // data-has-year="1" marks the rows that have these dropdowns (REG and COG).
        const missingPeriodRow = checkedRows.find(row => {
            if (row.dataset.hasYear !== '1') return false; // row does not need these fields
            const ay  = row.querySelector('.ay-select');
            const sem = row.querySelector('.sem-select');
            return !ay || !ay.value || !sem || !sem.value;
        });
        if (missingPeriodRow) {
            const docName = missingPeriodRow.querySelector('.doc-name-cell').textContent.trim();
            Swal.fire({
                icon: 'warning',
                title: 'Missing Grading Period',
                text: `Please select the Assessment Year and Grading Period for "${docName}".`,
                confirmButtonColor: '#1A9FE0'
            });
            return;
        }

        // All good — show confirmation dialog
        Swal.fire({
            title: 'Confirmation',
            text: 'Are you sure you want to submit this document request?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1A9FE0',
            cancelButtonColor: '#F5C518',
            cancelButtonText: '<span style="color:#1A1A1A">Cancel</span>',
            confirmButtonText: 'Submit'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('requestForm').submit();
            }
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Section dropdown population
    // ─────────────────────────────────────────────────────────────────────────
    const sectionsByStrandGrade = {
        'ABM':  { 'Grade 11': ['ABM-1A','ABM-1B','ABM-1C'],   'Grade 12': ['ABM-2A','ABM-2B','ABM-2C'] },
        'ICT':  { 'Grade 11': ['ICT-1A','ICT-1B','ICT-1C'],   'Grade 12': ['ICT-2A','ICT-2B','ICT-2C'] },
        'HUMSS':{ 'Grade 11': ['HUMSS-1A','HUMSS-1B','HUMSS-1C'], 'Grade 12': ['HUMSS-2A','HUMSS-2B','HUMSS-2C'] },
        'STEM': { 'Grade 11': ['STEM-1A','STEM-1B','STEM-1C'], 'Grade 12': ['STEM-2A','STEM-2B','STEM-2C'] },
        'GAS':  { 'Grade 11': ['GAS-1A','GAS-1B','GAS-1C'],   'Grade 12': ['GAS-2A','GAS-2B','GAS-2C'] },
        'HE':   { 'Grade 11': ['HE-1A','HE-1B','HE-1C'],      'Grade 12': ['HE-2A','HE-2B','HE-2C'] },
    };

    const userStrand  = "{{ auth()->user()->strand }}";
    const userGrade   = "{{ auth()->user()->grade_level }}";
    const userSection = "{{ auth()->user()->section }}";

    function updateSection() {
        const grade   = document.getElementById('yearLevel').value;
        const sel     = document.getElementById('sectionField');
        sel.innerHTML = '<option value="">— select section —</option>';
        const sections = (sectionsByStrandGrade[userStrand] || {})[grade] || [];
        sections.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s;
            opt.textContent = s;
            if (s === userSection) opt.selected = true;
            sel.appendChild(opt);
        });
    }

    // Populate section dropdown on page load
    updateSection();

    // ─────────────────────────────────────────────────────────────────────────
    // Sync qty input typed directly by user (not via +/- buttons)
    // ─────────────────────────────────────────────────────────────────────────
    document.querySelectorAll('.qty-input').forEach(input => {
        input.addEventListener('change', function () {
            const row      = this.closest('.doc-row');
            const checkbox = row.querySelector('.doc-checkbox');
            const val      = parseInt(this.value) || 0;
            if (val > 0 && !checkbox.checked) {
                checkbox.checked = true;
                toggleDocRow(checkbox);
                this.value = val;
            } else if (val === 0 && checkbox.checked) {
                checkbox.checked = false;
                toggleDocRow(checkbox);
            }
            recalcTotal();
        });
    });

    // ─────────────────────────────────────────────────────────────────────────
    // Multi-Step Logic
    // ─────────────────────────────────────────────────────────────────────────
    function showAppointmentStep() {
        const checkedRows = [...document.querySelectorAll('.doc-row')]
            .filter(row => row.querySelector('.doc-checkbox').checked);

        // Basic validation before showing appointment section
        if (checkedRows.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Document Selected',
                text: 'Please select at least one document before proceeding.',
                confirmButtonColor: '#1B6B3A'
            });
            return;
        }

        // Check if any selected document is non-printable (like Form 138)
        const hasNonPrintable = checkedRows.some(row => {
            return row.dataset.isPrintable === '0';
        });

        const appointmentSection = document.getElementById('appointmentSection');
        const form138Instructions = document.getElementById('form138Instructions');
        const btnBookStep = document.getElementById('btnBookStep');
        const btnFinalSubmit = document.getElementById('btnFinalSubmit');

        if (hasNonPrintable) {
            // Hide appointment booking, show instructions
            appointmentSection.style.display = 'none';
            form138Instructions.style.display = 'block';
            
            // Clear any selected appointment data
            document.getElementById('appointmentDate').value = '';
            document.getElementById('timeSlotSelect').value = '';
            document.getElementById('timeSlotSelect').disabled = true;
        } else {
            // Show standard appointment booking
            appointmentSection.style.display = 'block';
            form138Instructions.style.display = 'none';
        }

        // Transition buttons
        btnBookStep.style.display = 'none';
        btnFinalSubmit.style.display = 'block';

        // Scroll to the new section
        setTimeout(() => {
            const target = hasNonPrintable ? form138Instructions : appointmentSection;
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 100);
    }

    function updateStepButton() {
        const checkedRows = [...document.querySelectorAll('.doc-row')]
            .filter(row => row.querySelector('.doc-checkbox').checked);
        
        const btnBookStep = document.getElementById('btnBookStep');
        if (!btnBookStep) return;

        const hasNonPrintable = checkedRows.some(row => row.dataset.isPrintable === '0');
        
        if (hasNonPrintable) {
            btnBookStep.textContent = 'PROCEED TO SUBMIT';
        } else {
            btnBookStep.textContent = 'BOOK APPOINTMENT';
        }
    }

    function fetchAvailableSlots() {
        const date = document.getElementById('appointmentDate').value;
        const slotSelect = document.getElementById('timeSlotSelect');
        const loading = document.getElementById('slotLoading');

        if (!date) return;

        // Reset and show loading
        slotSelect.innerHTML = '<option value="">Loading slots...</option>';
        slotSelect.disabled = true;
        loading.style.display = 'block';

        fetch(`{{ route('student.appointments.available-slots') }}?date=${date}`)
            .then(response => response.json())
            .then(data => {
                loading.style.display = 'none';
                slotSelect.innerHTML = '<option value="">— select time slot —</option>';
                
                if (data.length === 0) {
                    slotSelect.innerHTML = '<option value="">No slots available for this date</option>';
                } else {
                    data.forEach(slot => {
                        const option = document.createElement('option');
                        option.value = slot.id;
                        option.textContent = slot.label;
                        if (slot.is_full) {
                            option.disabled = true;
                            option.textContent += ' [FULL]';
                        }
                        slotSelect.appendChild(option);
                    });
                    slotSelect.disabled = false;
                }
            })
            .catch(error => {
                loading.style.display = 'none';
                console.error('Error fetching slots:', error);
                slotSelect.innerHTML = '<option value="">Error loading slots. Try again.</option>';
            });
    }

    function validateFinalForm() {
        const appointmentSection = document.getElementById('appointmentSection');
        const isAppointmentVisible = appointmentSection.style.display !== 'none';

        if (isAppointmentVisible) {
            const date = document.getElementById('appointmentDate').value;
            const slot = document.getElementById('timeSlotSelect').value;

            if (!date || !slot) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Appointment',
                    text: 'Please select both an appointment date and a time slot.',
                    confirmButtonColor: '#1A9FE0'
                });
                return;
            }
        }

        // Final confirmation
        Swal.fire({
            title: 'Final Confirmation',
            text: isAppointmentVisible 
                ? 'Ready to submit your request and book this appointment?' 
                : 'Ready to submit your request for manual verification?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1A9FE0',
            cancelButtonColor: '#F5C518',
            cancelButtonText: '<span style="color:#1A1A1A">Wait, let me check</span>',
            confirmButtonText: 'Yes, Submit Request'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('requestForm').submit();
            }
        });
    }
    // ─────────────────────────────────────────────────────────────────────────
    // Initial selection handling (Auto-select from Documents page)
    // ─────────────────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const selectId = urlParams.get('select');
        
        if (selectId) {
            // Give a tiny delay to ensure all table elements are fully processed
            setTimeout(() => {
                const typeInput = document.querySelector(`.doc-type-id-input[value="${selectId}"]`);
                if (typeInput) {
                    const row = typeInput.closest('.doc-row');
                    const checkbox = row.querySelector('.doc-checkbox');
                    
                    if (checkbox && !checkbox.checked) {
                        checkbox.checked = true;
                        toggleDocRow(checkbox);
                        
                        // Scroll the document table into view for the student
                        row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        
                        // Optional: Highlight the row briefly
                        row.style.transition = 'background 0.5s';
                        row.style.background = '#e8f5e9';
                        setTimeout(() => row.style.background = '', 2000);
                    }
                }
            }, 100);
        }
    });
</script>
@endpush
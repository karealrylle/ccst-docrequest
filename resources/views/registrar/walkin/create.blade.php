@extends('layouts.registrar')

@section('title', 'Walk-in Document Request')

@section('content')
<div class="registrar-sticky-header">CREATE WALK-IN REQUEST</div>

<div class="d-flex align-items-stretch mb-3 px-2">
    <div class="ccst-card mb-0" style="flex: 1; border-left: 4px solid #1A9FE0;">
        <div class="ccst-card-body p-3">
            <h6 class="fw-bold mb-2 text-primary" style="color: #1A9FE0 !important;"><i class="bi bi-info-circle me-1"></i> Walk-in Guide</h6>
            <ol class="mb-0 ps-3" style="font-size: 0.85rem;">
                <li>Student walks-in and fills up physical document request.</li>
                <li>The registrar will ask for the student’s school ID to confirm they are a real student.</li>
                <li>Registrar copies the details into this form.</li>
                <li>If documents are ready to print: print payment slip, student pays, documents are released.</li>
                <li>If not ready: student receives email when ready, pays at cashier, and picks up documents.</li>
            </ol>
        </div>
    </div>
    <div class="ms-3 d-flex">
        <a href="{{ route('registrar.walkin.blank-form') }}" class="print-btn" target="_blank" style="width: 160px; height: 100%; border-radius: 20px;">
            <div class="print-icon" style="width: 50px; height: 50px;">
                <img src="{{ asset('images/print.png') }}" alt="Print">
            </div>
            <div class="print-label" style="width: 130px; height: 24px;">
                <span>PRINT BLANK FORM</span>
            </div>
        </a>
    </div>
</div>

<div class="request-detail-scroll">
    <div class="request-detail-card p-4">

        <form action="{{ route('registrar.walkin.store') }}" method="POST" id="walkinForm">
            @csrf

            <h5 class="border-bottom pb-2 mb-3" style="color: #1B6B3A; font-weight: 700;">Student Information</h5>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold" style="font-size: 0.85rem;">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="full_name" class="form-control" value="{{ old('full_name') }}" required placeholder="e.g. Dela Cruz, Maria S.">
                    @error('full_name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold" style="font-size: 0.85rem;">Student Number</label>
                    <input type="text" name="student_number" class="form-control" value="{{ old('student_number') }}" placeholder="e.g. 05-8959">
                    @error('student_number') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold" style="font-size: 0.85rem;">Email Address</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="e.g. mariadelacruz@gmail.com">
                    @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold" style="font-size: 0.85rem;">Contact Number</label>
                    <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number') }}" placeholder="e.g. 09XXXXXXXXX">
                </div>
                <div class="col-md-8 mb-3">
                    <label class="form-label fw-bold" style="font-size: 0.85rem;">Address</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address') }}" placeholder="e.g. Brgy. Dolores, Mabalacat City, Pampanga">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold" style="font-size: 0.85rem;">Strand <span class="text-danger">*</span></label>
                    <select name="course_program" id="course_program" class="form-select" required>
                        <option value="" disabled selected>Select Strand</option>
                        <option value="STEM" {{ old('course_program') == 'STEM' ? 'selected' : '' }}>STEM</option>
                        <option value="ABM" {{ old('course_program') == 'ABM' ? 'selected' : '' }}>ABM</option>
                        <option value="HUMSS" {{ old('course_program') == 'HUMSS' ? 'selected' : '' }}>HUMSS</option>
                        <option value="GAS" {{ old('course_program') == 'GAS' ? 'selected' : '' }}>GAS</option>
                        <option value="TVL-ICT" {{ old('course_program') == 'TVL-ICT' ? 'selected' : '' }}>TVL-ICT</option>
                        <option value="TVL-HE" {{ old('course_program') == 'TVL-HE' ? 'selected' : '' }}>TVL-HE</option>
                    </select>
                    @error('course_program') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold" style="font-size: 0.85rem;">Grade <span class="text-danger">*</span></label>
                    <select name="year_level" id="year_level" class="form-select" required>
                        <option value="" disabled selected>Select Grade</option>
                        <option value="Grade 11" {{ old('year_level') == 'Grade 11' ? 'selected' : '' }}>Grade 11</option>
                        <option value="Grade 12" {{ old('year_level') == 'Grade 12' ? 'selected' : '' }}>Grade 12</option>
                    </select>
                    @error('year_level') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold" style="font-size: 0.85rem;">Section <span class="text-danger">*</span></label>
                    <select name="section" id="section" class="form-select" required>
                        <option value="" disabled selected>Select Strand & Grade first</option>
                    </select>
                    @error('section') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <h5 class="border-bottom pb-2 mb-3 mt-4" style="color: #1B6B3A; font-weight: 700;">Document Selection <span class="text-danger">*</span></h5>
            @error('documents') <div class="alert alert-danger py-2">{{ $message }}</div> @enderror

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead style="background-color: #F0F7F0; color: #1B6B3A;">
                        <tr>
                            <th style="width: 5%; text-align: center;">Select</th>
                            <th style="width: 50%;">Document Type</th>
                            <th style="width: 15%; text-align: right;">Fee</th>
                            <th style="width: 30%;">Quantity / Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($documentTypes as $doc)
                        <tr>
                            <td style="text-align: center;">
                                <input class="form-check-input doc-checkbox" type="checkbox" name="documents[]" value="{{ $doc->id }}" id="doc_{{ $doc->id }}" data-fee="{{ $doc->fee }}" {{ (is_array(old('documents')) && in_array($doc->id, old('documents'))) ? 'checked' : '' }}>
                            </td>
                            <td>
                                <label for="doc_{{ $doc->id }}" class="fw-bold d-block" style="cursor:pointer;">{{ $doc->name }}</label>
                                @if($doc->processing_days > 0)
                                    <small class="text-muted">{{ $doc->processing_days }} day(s) processing</small>
                                @endif
                            </td>
                            <td style="text-align: right;">₱{{ number_format($doc->fee, 2) }}</td>
                            <td>
                                <div class="input-group input-group-sm" style="width: 120px;">
                                    <span class="input-group-text">Qty</span>
                                    <input type="number" name="copies[{{ $doc->id }}]" id="qty_{{ $doc->id }}" class="form-control doc-qty" value="{{ old('copies.'.$doc->id, 1) }}" min="1" max="10" {{ (is_array(old('documents')) && in_array($doc->id, old('documents'))) ? '' : 'disabled' }}>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card mt-4 mb-4" style="background-color: #f8f9fa; border: 1px dashed #ccc;">
                <div class="card-body text-end">
                    <h5 class="mb-0">Total Amount: <span id="totalFeeDisplay" style="color: #1B6B3A; font-weight: 700; font-size: 1.5rem;">₱0.00</span></h5>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 border-top pt-3">
                <a href="{{ route('registrar.dashboard') }}" class="btn btn-secondary px-4">Cancel</a>
                <button type="submit" class="btn text-white px-4" style="background-color: #1B6B3A;" id="submitBtn" disabled>
                    <i class="bi bi-file-earmark-check"></i> Generate Request & Payment Slip
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('right-panel')
    {{-- Right panel empty to give more space for the form --}}
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
    .request-detail-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border: 1px solid #e0e0e0;
    }
    .form-control, .form-select {
        border: 1px solid #D0DDD0;
        border-radius: 8px;
        padding: 10px 12px;
        background-color: #fcfdfc !important;
        font-family: 'Poppins', sans-serif;
        font-size: 0.85rem;
    }
    .form-control:focus, .form-select:focus {
        box-shadow: 0 0 0 3px rgba(27, 107, 58, 0.1);
        border-color: #1B6B3A;
        background-color: #fff !important;
    }
    .form-control::placeholder {
        font-size: 0.75rem;
        color: #adb5bd;
        opacity: 0.8;
    }
    .form-label {
        color: #444;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }
    .btn-primary:hover {
        background-color: #0D7FBF !important;
    }

    /* Copied Dashboard Print Button Styles */
    .print-btn {
        position: relative;
        background: linear-gradient(179.89deg, #FFFFFF -21.69%, #CDECFF 51.86%, #029CFE 146.82%);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.3s ease;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    .print-btn:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(2,156,254,0.3); }
    .print-icon { margin-bottom: 10px; display: flex; align-items: center; justify-content: center; }
    .print-icon img { max-width: 100%; max-height: 100%; object-fit: contain; }
    .print-label { background: #01025F; border-radius: 25px; display: flex; align-items: center; justify-content: center; }
    .print-label span { font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 10px; color: #FFFFFF; letter-spacing: 0.5px; }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.doc-checkbox');
        const totalDisplay = document.getElementById('totalFeeDisplay');
        const submitBtn = document.getElementById('submitBtn');

        function calculateTotal() {
            let total = 0;
            let hasChecked = false;

            checkboxes.forEach(cb => {
                const docId = cb.value;
                const qtyInput = document.getElementById('qty_' + docId);
                
                if (cb.checked) {
                    hasChecked = true;
                    qtyInput.disabled = false;
                    const fee = parseFloat(cb.dataset.fee);
                    const qty = parseInt(qtyInput.value) || 1;
                    total += fee * qty;
                } else {
                    qtyInput.disabled = true;
                }
            });

            totalDisplay.textContent = '₱' + total.toFixed(2);
            submitBtn.disabled = !hasChecked;
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', calculateTotal);
        });

        document.querySelectorAll('.doc-qty').forEach(qty => {
            qty.addEventListener('input', calculateTotal);
        });

        // Initialize on load (for old input repopulation)
        calculateTotal();

        // Dynamic Section Logic
        const strandSelect = document.getElementById('course_program');
        const gradeSelect = document.getElementById('year_level');
        const sectionSelect = document.getElementById('section');

        function updateSections() {
            const strand = strandSelect.value;
            const grade = gradeSelect.value;
            
            if (!strand || !grade) {
                sectionSelect.innerHTML = '<option value="" disabled selected>Select Strand & Grade first</option>';
                return;
            }

            const gradeNum = grade === 'Grade 11' ? '11' : '12';
            const sections = ['A', 'B', 'C', 'D', 'E'];
            let options = '<option value="" disabled selected>Select Section</option>';
            
            sections.forEach(letter => {
                const sectionValue = `${strand}-${gradeNum}${letter}`;
                const selected = "{{ old('section') }}" === sectionValue ? 'selected' : '';
                options += `<option value="${sectionValue}" ${selected}>${sectionValue}</option>`;
            });
            
            sectionSelect.innerHTML = options;
        }

        strandSelect.addEventListener('change', updateSections);
        gradeSelect.addEventListener('change', updateSections);

        // Run once on load if values exist
        if (strandSelect.value && gradeSelect.value) {
            updateSections();
        }
    });
</script>
@endpush

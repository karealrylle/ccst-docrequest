@extends('layouts.registrar')

@section('title', 'Request Details - ' . $request->reference_number)

@section('content')

<div class="registrar-sticky-header">REQUEST DETAILS</div>

<div class="request-detail-scroll">
    <div class="request-detail-card">
        {{-- Request Information --}}
        <div class="detail-section">
            <h3><i class="bi bi-person-badge me-2"></i>Student Information</h3>
            <div class="detail-grid">
                <div><label>Reference Number:</label> <strong>{{ $request->reference_number }}</strong></div>
                <div><label>Student Name:</label> <strong>{{ $request->full_name }}</strong></div>
                <div><label>Student Number:</label> <strong>{{ $request->student_number }}</strong></div>
                <div><label>Contact Number:</label> <strong>{{ $request->contact_number }}</strong></div>
                <div><label>Course/Program:</label> <strong>{{ $request->course_program }}</strong></div>
                <div><label>Year & Section:</label> <strong>{{ $request->year_level }} - {{ $request->section }}</strong></div>
                <div><label>Appointment Date:</label> 
                    <strong>
                        @if($request->is_walk_in)
                            {{ $request->created_at->format('M d, Y') }} (Walk-in)
                        @elseif($request->appointment)
                            {{ \Carbon\Carbon::parse($request->appointment->appointment_date)->format('M d, Y') }}
                        @else
                            No Appointment
                        @endif
                    </strong>
                </div>
                <div><label>Status:</label> 
                    <strong>
                        @php
                            $statusConfig = [
                                'pending' => ['label' => 'Pending', 'class' => 'status-pending'],
                                'ready_for_pickup' => ['label' => 'Ready for Pickup', 'class' => 'status-ready'],
                                'completed' => ['label' => 'Completed', 'class' => 'status-verified'],
                                'cancelled' => ['label' => 'Cancelled', 'class' => 'status-cancelled'],
                            ];
                            $reqStatus = $statusConfig[$request->status] ?? ['label' => ucfirst($request->status), 'class' => ''];
                        @endphp
                        <span class="status-badge {{ $reqStatus['class'] }}">{{ $reqStatus['label'] }}</span>
                    </strong>
                </div>
                <div><label>Total Fee:</label> <strong style="color:#1B6B3A;">₱{{ number_format($request->total_fee, 2) }}</strong></div>
            </div>
        </div>

        {{-- Requested Documents (Print Selection) --}}
        <div class="detail-section">
            <h3><i class="bi bi-file-earmark-text me-2"></i>Requested Documents</h3>
            <form id="printSelectedForm">
                @csrf
                <input type="hidden" name="request_id" value="{{ $request->id }}">
                <table class="docs-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;"><input type="checkbox" id="selectAllDocs"></th>
                            <th>Document Name</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($request->items as $item)
                        @php
                            $isPrinted = $request->generatedDocuments->where('document_type_id', $item->document_type_id)->isNotEmpty();
                            $generatedDoc = $request->generatedDocuments->where('document_type_id', $item->document_type_id)->first();
                        @endphp
                        <tr>
                            <td>
                                @if($item->documentType->is_printable)
                                    <input type="checkbox" name="document_item_ids[]" value="{{ $item->id }}" class="doc-checkbox">
                                @else
                                    <i class="bi bi-dash text-muted"></i>
                                @endif
                            </td>
                            <td><strong>{{ $item->documentType->name }}</strong></td>
                            <td class="text-center">{{ $item->copies }}</td>
                            <td class="text-center">
                                @if($isPrinted)
                                    <span class="status-badge status-verified">Printed</span>
                                @else
                                    <span class="status-badge status-pending">Not Printed</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($item->documentType->is_printable)
                                    <div style="display: flex; gap: 5px; justify-content: flex-end;">
                                        <button type="button" 
                                                onclick="previewDocument({{ $request->id }}, {{ $item->document_type_id }}, '{{ $item->documentType->name }}')" 
                                                class="btn-tiny {{ $isPrinted ? 'btn-view-file' : 'btn-generate-file' }}">
                                            <i class="bi {{ $isPrinted ? 'bi-eye' : 'bi-file-pdf' }}"></i> 
                                            {{ $isPrinted ? 'View' : 'Preview' }}
                                        </button>

                                        @if($request->status === 'ready_for_pickup')
                                            <button type="button" 
                                                    onclick="printSingleDocument({{ $item->id }})" 
                                                    class="btn-tiny" style="background: #1B6B3A; color: white; border: none;">
                                                <i class="bi bi-printer"></i> Print
                                            </button>
                                        @endif
                                    </div>
                                @else
                                    <button type="button" 
                                            onclick="markAsCompleted({{ $request->id }})" 
                                            class="btn-tiny" style="background: #1B6B3A; color: white;">
                                        <i class="bi bi-check2-circle"></i> Mark as Completed
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                
                @if($request->status === 'ready_for_pickup')
                <div class="print-actions mt-4" style="display: none; justify-content: center;">
                    <button type="button" onclick="printSelectedDocuments()" class="btn-action btn-received w-64 py-3 shadow-sm" id="printSelectedBtn" disabled>
                        <i class="bi bi-printer-fill me-2"></i> PRINT SELECTED DOCUMENTS
                    </button>
                </div>
                @endif
            </form>
        </div>

        {{-- Action Buttons --}}
        <div class="action-buttons">
            {{-- Mark as Ready (for non-printable) --}}
            @if($request->status === 'pending' && !$request->is_printable)
                <button type="button" onclick="markAsReady({{ $request->id }})" class="btn-action btn-processing">
                    <i class="bi bi-check-circle"></i> Mark as Ready for Pickup
                </button>
            @endif

            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                {{-- Print Receipt (Cashier Payment Slip) --}}
                <a href="{{ route('registrar.requests.print-cashier-receipt', $request->id) }}" target="_blank" class="btn-action" style="background:#0D7FBF;">
                    <i class="bi bi-printer"></i> Print Receipt
                </a>

                {{-- E-receipt Logic --}}
                @if($request->payment_status !== 'paid')
                    <form action="{{ route('registrar.requests.collect-payment', $request->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn-action" style="background:#198754;" onclick="return confirm('Process payment and view e-receipt?')">
                            <i class="bi bi-file-earmark-check"></i> View E-receipt
                        </button>
                    </form>
                @else
                    <a href="{{ route('registrar.requests.collect-payment', $request->id) }}" target="_blank" class="btn-action" style="background:#198754;">
                        <i class="bi bi-file-earmark-check"></i> View E-receipt
                    </a>
                @endif
            </div>

            @if($request->status === 'ready_for_pickup' && !$request->is_printable)
                <button type="button" onclick="markAsCompleted({{ $request->id }})" class="btn-action" style="background:#055160;">
                    <i class="bi bi-check-all"></i> Mark as Completed
                </button>
            @endif

            <a href="{{ route('registrar.requests.index') }}" class="btn-back mt-0">
                <i class="bi bi-arrow-left me-1"></i> Back to Requests List
            </a>
        </div>
    </div>
</div>

{{-- PDF Preview Modal --}}
<div id="previewModal" class="modal-overlay" style="display:none;">
    <div class="modal-container" style="max-width: 800px; width: 95%;">
        <div class="modal-header" style="background: #1B6B3A;">
            <h4 id="previewModalTitle">Document Preview</h4>
            <button class="modal-close" onclick="closePreviewModal()">&times;</button>
        </div>
        <div class="modal-body p-0" style="height: 600px;">
            <iframe id="previewIframe" src="" style="width: 100%; height: 100%; border: none;"></iframe>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel-modal" onclick="closePreviewModal()">Close</button>
            <button class="btn-action btn-received" onclick="printFromPreview()" id="modalPrintBtn">
                <i class="bi bi-printer"></i> Print
            </button>
        </div>
    </div>
</div>

<!-- Non-Printable Walk-in Modal -->
<div class="modal fade" id="nonPrintableModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background:#F5C518; color:#1A1A1A; border:none;">
                <h6 class="modal-title fw-700 mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i> Documents Not Yet Ready
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding:20px;">
                <p>These documents require manual processing and are not auto-printable.</p>
                <p><strong>Instructions for Student:</strong> Please inform the student that they will receive an email when their request is ready to be picked up. They can go to the cashier directly to pay at that time.</p>
                <p><strong>Registrar Action:</strong> Once you have finished processing the documents, mark this request as <strong>Ready for Pickup</strong>. The Payment Slip can then be generated and given to the cashier.</p>
            </div>
            <div class="modal-footer" style="border-top:1px solid #e0e0e0; padding:15px 20px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('right-panel')



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
        position: sticky;
        top: 0;
        z-index: 10;
        margin-bottom: 24px;
    }

    .request-detail-scroll {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: 4px;
        margin-bottom: 20px;
    }

    .request-detail-scroll::-webkit-scrollbar { width: 6px; }
    .request-detail-scroll::-webkit-scrollbar-track { background: #f0f0f0; border-radius: 3px; }
    .request-detail-scroll::-webkit-scrollbar-thumb { background: #1B6B3A; border-radius: 3px; }

    .request-detail-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        padding: 24px;
    }

    .detail-section {
        margin-bottom: 28px;
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 20px;
    }
    .detail-section:last-of-type { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }

    .detail-section h3 {
        font-size: 1rem;
        font-weight: 700;
        color: #1B6B3A;
        margin-bottom: 16px;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px 24px;
    }

    .detail-grid div { font-size: 0.85rem; }
    .detail-grid label { color: #888; font-weight: 500; margin-right: 8px; }

    .docs-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
    }
    .docs-table th {
        background: #F0F7F0;
        padding: 10px;
        color: #1B6B3A;
        font-weight: 600;
        text-align: left;
    }
    .docs-table td {
        padding: 10px;
        border-bottom: 1px solid #f0f0f0;
        vertical-align: middle;
    }
    .text-center { text-align: center; }
    .text-end { text-align: right; }

    .action-buttons {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid #f0f0f0;
        align-items: center;
    }

    .btn-action {
        color: white;
        border: none;
        padding: 10px 13px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        transition: opacity 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .btn-action:hover { opacity: 0.85; }
    .btn-action:disabled { opacity: 0.5; cursor: not-allowed; }

    .btn-processing { background: #17a2b8; }
    .btn-received { background: #1B6B3A; }

    .btn-tiny {
        padding: 4px 12px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        border: none;
        cursor: pointer;
    }
    .btn-view-file { background: #E8F4FD; color: #0969A2; }
    .btn-generate-file { background: #F0F7F0; color: #1B6B3A; }
    
    .mt-4 { margin-top: 1.5rem; }
    .w-100 { width: 100%; }
    .w-75 { width: 75%; }
    .w-64 { width: 64%; }
    .mx-auto { margin-left: auto; margin-right: auto; }
    .d-block { display: block; }

    /* Modal Overlay */
    .modal-overlay {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.7); z-index: 2000;
        display: flex; align-items: center; justify-content: center;
        backdrop-filter: blur(4px);
    }
    .modal-container {
        background: white; border-radius: 12px; 
        overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        animation: modalFadeIn 0.3s ease-out;
    }
    @keyframes modalFadeIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .modal-header {
        color: white; padding: 15px 20px;
        display: flex; justify-content: space-between; align-items: center;
    }
    .modal-header h4 { margin: 0; font-size: 1.1rem; font-weight: 600; }
    .modal-close { background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer; }
    .modal-footer {
        padding: 15px 20px; display: flex; justify-content: flex-end;
        gap: 12px; border-top: 1px solid #f0f0f0;
    }
    .btn-cancel-modal {
        background: #f8f9fa; border: 1px solid #ddd; padding: 8px 20px;
        border-radius: 6px; cursor: pointer; font-family: 'Poppins', sans-serif;
        font-weight: 500;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        background: #f0f0f0;
        color: #666;
        padding: 10px 20px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 600;
    }
    .btn-back:hover { background: #e0e0e0; color: #1A1A1A; }

    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    .status-pending { background: #FFF3CD; color: #856404; }
    .status-ready { background: #F5A623; color: white; }
    .status-verified { background: #1B6B3A; color: white; }
    .status-cancelled { background: #DC3545; color: white; }

    .rp-guide-step {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 12px 14px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        font-size: 0.8rem;
        color: rgba(255,255,255,0.9);
    }
    .rp-step-num {
        width: 22px; height: 22px; min-width: 22px;
        border-radius: 50%; background: #F5C518; color: #1A1A1A;
        font-size: 0.7rem; font-weight: 800;
        display: flex; align-items: center; justify-content: center;
    }

</style>
@endpush

@push('scripts')
<script>
    let currentPreviewUrl = '';

    function previewDocument(requestId, documentTypeId, docName) {
        let url = "{{ route('registrar.documents.preview', [':requestId', ':documentTypeId']) }}";
        url = url.replace(':requestId', requestId).replace(':documentTypeId', documentTypeId);
        
        document.getElementById('previewModalTitle').textContent = `Preview: ${docName}`;
        document.getElementById('previewIframe').src = url;
        document.getElementById('previewModal').style.display = 'flex';
    }

    function closePreviewModal() {
        document.getElementById('previewModal').style.display = 'none';
        document.getElementById('previewIframe').src = '';
    }

    function printSelectedDocuments() {
        const form = document.getElementById('printSelectedForm');
        const formData = new FormData(form);
        
        Swal.fire({
            title: 'Generating Documents...',
            text: 'Please wait while we prepare the PDFs.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('{{ route("registrar.documents.print-selected") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    if (data.download_url) {
                        window.open(data.download_url, '_blank');
                    }
                    window.location.reload();
                });
            } else {
                Swal.fire('Error', data.message || 'Generation failed', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'An unexpected error occurred.', 'error');
        });
    }

    // Mark as ready for pickup (non-printable)
    function markAsReady(id) {
        Swal.fire({
            title: 'Mark as Ready?',
            text: 'This will notify the student that their documents are prepared and ready for pickup.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#17a2b8',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Mark as Ready',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const url = `{{ route('registrar.requests.mark-ready', ':id') }}`.replace(':id', id);
                fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Success', data.message, 'success').then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Error', data.message || 'Failed to mark as ready', 'error');
                    }
                });
            }
        });
    }

    function markAsCompleted(id) {
        Swal.fire({
            title: 'Mark as Completed?',
            text: 'This will mark the request as completed and notify the student.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Mark as Completed',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ route('registrar.requests.completed', ':id') }}`.replace(':id', id);
                
                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                
                const method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                method.value = 'PATCH';
                
                form.appendChild(csrf);
                form.appendChild(method);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Checkbox logic
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('selectAllDocs');
        const checkboxes = document.querySelectorAll('.doc-checkbox');
        const printBtn = document.getElementById('printSelectedBtn');

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updatePrintButton();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updatePrintButton);
        });

        function updatePrintButton() {
            const checkedCount = document.querySelectorAll('.doc-checkbox:checked').length;
            if (printBtn) {
                printBtn.disabled = checkedCount === 0;
                const container = printBtn.closest('.print-actions');
                if (container) {
                    container.style.display = checkedCount > 0 ? 'flex' : 'none';
                }
            }
        }
    });

    function openPaymentSlipPreview(url) {
        document.getElementById('previewModalTitle').innerText = 'Payment Slip Preview';
        document.getElementById('previewIframe').src = url;
        document.getElementById('previewModal').style.display = 'flex';
    }

    function printFromPreview() {
        const iframe = document.getElementById('previewIframe');
        if (iframe.src) {
            window.open(iframe.src, '_blank');
        }
    }

    function closePreviewModal() {
        document.getElementById('previewModal').style.display = 'none';
        document.getElementById('previewIframe').src = '';
    }
    function printSingleDocument(itemId) {
        // Uncheck all first
        document.querySelectorAll('.doc-checkbox').forEach(cb => cb.checked = false);
        // Check only this item
        const checkbox = document.querySelector(`.doc-checkbox[value="${itemId}"]`);
        if (checkbox) {
            checkbox.checked = true;
            printSelectedDocuments();
        }
    }
</script>
@endpush

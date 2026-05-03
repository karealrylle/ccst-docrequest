@extends('layouts.registrar')

@section('title', 'Email Templates')

@section('content')

<div class="templates-sticky-header">EMAIL TEMPLATES</div>

<div class="templates-grid">
    @foreach($templates as $template)
    <div class="template-card">
        <div class="template-header">
            <div class="template-type">
                @php
                    $typeLabels = [
                        'account_verified' => 'Account Verified',
                        'registration_pending' => 'Registration Pending',
                        'appointment_confirmed' => 'Appointment Confirmed',
                        'appointment_reminder' => 'Appointment Reminder',
                        'document_ready' => 'Document Ready',
                    ];
                @endphp
                <i class="bi bi-envelope-fill me-2"></i>
                {{ $typeLabels[$template->type] ?? ucfirst(str_replace('_', ' ', $template->type)) }}
            </div>
            <div class="template-actions">
                <a href="{{ route('registrar.email-templates.preview', $template->id) }}" class="btn-preview" target="_blank">
                    <i class="bi bi-eye"></i> Preview
                </a>
                <button class="btn-edit-template" onclick="openEditModal({{ $template->id }})">
                    <i class="bi bi-pencil"></i> Edit
                </button>
            </div>
        </div>
        <div class="template-body">
            <div class="template-subject">
                <strong>Subject:</strong> {{ $template->subject }}
            </div>
            <div class="template-subject">
                <strong>Last Updated:</strong> {{ $template->updated_at ? $template->updated_at->diffForHumans() : 'Not updated' }}
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Edit Modal --}}
<div id="editTemplateModal" class="modal-overlay" style="display:none;">
    <div class="modal-container" style="max-width: 700px;">
        <div class="modal-header-custom">
            <h4><i class="bi bi-pencil-square me-2"></i>Edit Email Template</h4>
            <button type="button" class="modal-close" onclick="closeEditModal()">&times;</button>
        </div>
        <form id="editTemplateForm">
            @csrf
            @method('PATCH')
            <div class="modal-body-custom">
                <div class="form-group">
                    <label>Subject <span class="required">*</span></label>
                    <input type="text" name="subject" id="templateSubject" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Body <span class="required">*</span></label>
                    <textarea name="body" id="templateBody" class="form-textarea" rows="12" required></textarea>
                    <small class="form-hint">
                        Available variables: {{ student_name }}, {{ student_number }}, {{ reference_number }}, 
                        {{ appointment_date }}, {{ time_slot }}, {{ amount }}, {{ registrar_name }}
                    </small>
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn-reset-template" id="resetTemplateBtn">Reset to Default</button>
                <button type="button" class="btn-cancel-modal" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn-save-modal">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<form id="resetForm" method="POST" style="display:none;">
    @csrf
</form>

@endsection

@section('right-panel')


    <div class="ccst-card mb-0">
        <div class="ccst-card-header blue">Available Variables</div>
        <div class="ccst-card-body p-0">
            <div class="rp-stat-row"><span>{{ student_name }}</span><span>Student's full name</span></div>
            <div class="rp-stat-row"><span>{{ student_number }}</span><span>Student number</span></div>
            <div class="rp-stat-row"><span>{{ reference_number }}</span><span>Request reference</span></div>
            <div class="rp-stat-row"><span>{{ appointment_date }}</span><span>Appointment date</span></div>
            <div class="rp-stat-row"><span>{{ time_slot }}</span><span>Time slot label</span></div>

            <div class="rp-stat-row"><span>{{ amount }}</span><span>Payment amount</span></div>
            <div class="rp-stat-row" style="border-bottom:none;"><span>{{ registrar_name }}</span><span>Registrar name</span></div>
        </div>
    </div>

    <div class="ccst-card mb-0">
        <div class="ccst-card-header yellow">Tips</div>
        <div class="ccst-card-body p-0">
            <div class="rp-guide-step"><span class="rp-step-num">1</span><span>Use variables for personalization</span></div>
            <div class="rp-guide-step"><span class="rp-step-num">2</span><span>Preview before saving</span></div>
            <div class="rp-guide-step" style="border-bottom:none;"><span class="rp-step-num">3</span><span>Reset to default if needed</span></div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .templates-sticky-header {
        background: #1B6B3A;
        color: white;
        font-size: 0.9rem;
        font-weight: 700;
        text-align: center;
        padding: 10px 20px;
        text-transform: uppercase;
        margin-bottom: 20px;
    }

    .templates-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .template-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .template-header {
        background: #1B6B3A;
        color: white;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .template-type {
        font-size: 0.95rem;
        font-weight: 700;
    }

    .template-actions {
        display: flex;
        gap: 10px;
    }

    .btn-preview, .btn-edit-template {
        padding: 5px 15px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
    }

    .btn-preview {
        background: #F5C518;
        color: #1A1A1A;
        border: none;
    }

    .btn-edit-template {
        background: #1A9FE0;
        color: white;
        border: none;
    }

    .template-body {
        padding: 15px 20px;
    }

    .template-subject {
        font-size: 0.85rem;
        margin-bottom: 8px;
    }



    .rp-stat-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 14px;
        border-bottom: 1px solid rgba(255,255,255,0.2);
        font-size: 0.75rem;
        color: white;
    }

    .rp-guide-step {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 9px 14px;
        border-bottom: 1px solid rgba(255,255,255,0.2);
        font-size: 0.78rem;
        color: rgba(255,255,255,0.92);
    }

    .rp-step-num {
        width: 20px;
        height: 20px;
        min-width: 20px;
        border-radius: 50%;
        background: #F5C518;
        color: #1A1A1A;
        font-size: 0.68rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-reset-template {
        background: #DC3545;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
    }

    .form-textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #D0DDD0;
        border-radius: 6px;
        font-family: monospace;
        font-size: 0.8rem;
        resize: vertical;
    }
</style>
@endpush

@push('scripts')
<script>
    let currentTemplateId = null;

    function openEditModal(id) {
        currentTemplateId = id;
        fetch(`/registrar/email-templates/${id}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('templateSubject').value = data.subject;
                document.getElementById('templateBody').value = data.body;
                document.getElementById('editTemplateModal').style.display = 'flex';
            });
    }

    function closeEditModal() {
        document.getElementById('editTemplateModal').style.display = 'none';
        currentTemplateId = null;
    }

    document.getElementById('editTemplateForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        fetch(`/registrar/email-templates/${currentTemplateId}`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                subject: document.getElementById('templateSubject').value,
                body: document.getElementById('templateBody').value
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Success', data.message, 'success').then(() => {
                    location.reload();
                });
                closeEditModal();
            } else {
                Swal.fire('Error', data.message || 'Failed to update template.', 'error');
            }
        });
    });

    document.getElementById('resetTemplateBtn').addEventListener('click', function() {
        Swal.fire({
            title: 'Reset Template?',
            text: 'This will restore the default template. Any custom changes will be lost.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DC3545',
            cancelButtonColor: '#1B6B3A',
            confirmButtonText: 'Yes, Reset',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('resetForm');
                form.action = `/registrar/email-templates/${currentTemplateId}/reset`;
                form.submit();
            }
        });
    });
</script>
@endpush
<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Models\DocumentRequestItem;
use App\Models\DocumentType;
use App\Models\StatusLog;
use App\Notifications\RequestSubmittedNotification;
use App\Traits\SendsDatabaseNotifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DocumentRequestController extends Controller
{
    use SendsDatabaseNotifications;

    // ─────────────────────────────────────────────────────────────────────────
    // SHOW REQUEST FORM
    // GET /student/requests/create
    // ─────────────────────────────────────────────────────────────────────────
    public function create()
    {
        // Load only active document types so deactivated ones don't appear
        $documentTypes = DocumentType::where('is_active', true)->get();

        // Get all active time slots for the booking section
        $timeSlots = \App\Models\TimeSlot::where('is_active', true)
            ->orderBy('start_time')
            ->get();

        return view('student.requests.create', compact('documentTypes', 'timeSlots'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STORE NEW REQUEST
    // POST /student/requests
    // ─────────────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        // Check if any non-printable document (like Form 138) is in the requested documents
        $hasNonPrintable = false;
        if ($request->has('documents')) {
            foreach ($request->input('documents') as $doc) {
                $docType = DocumentType::find($doc['document_type_id']);
                if ($docType && !$docType->is_printable) {
                    $hasNonPrintable = true;
                    break;
                }
            }
        }

        $validated = $request->validate([
            'contact_number'   => 'required|string',
            'course_program'   => 'required|string',
            'year_level'       => 'required|string',
            'section'          => 'required|string',
            'documents'        => 'required|array',
            'documents.*.document_type_id' => 'required|exists:document_types,id',
            'documents.*.copies'           => 'required|integer|min:1',
            'documents.*.assessment_year'  => 'nullable|string',
            'documents.*.semester'         => 'nullable|string',
            'appointment_date' => $hasNonPrintable ? 'nullable|date|after_or_equal:today' : 'required|date|after_or_equal:today',
            'time_slot_id'     => $hasNonPrintable ? 'nullable|exists:time_slots,id' : 'required|exists:time_slots,id',
        ]);

        $user = Auth::user();

        // 1. Check Appointment Slot Capacity (if appointment is provided)
        if (!empty($validated['appointment_date']) && !empty($validated['time_slot_id'])) {
            $timeSlot = \App\Models\TimeSlot::findOrFail($validated['time_slot_id']);
            $appointmentsCount = \App\Models\Appointment::where('time_slot_id', $timeSlot->id)
                ->where('appointment_date', $validated['appointment_date'])
                ->whereNotIn('status', ['canceled', 'missed'])
                ->count();

            if ($appointmentsCount >= $timeSlot->max_capacity) {
                return back()->with('error', 'The selected time slot is already full. Please choose another one.')->withInput();
            }
        }

        try {
            DB::beginTransaction();

            // Collect document types and printability
            $documentTypeCodes = [];
            $allPrintable = true;

            foreach ($validated['documents'] as $doc) {
                $docType = DocumentType::findOrFail($doc['document_type_id']);
                $documentTypeCodes[] = $docType->code;
                
                if (!$docType->is_printable) {
                    $allPrintable = false;
                }
            }

            // Determine initial status
            $initialStatus = $allPrintable ? 'ready_for_pickup' : 'pending';

            // 2. Create the DocumentRequest
            $docRequest = DocumentRequest::create([
                'reference_number' => 'TEMP',
                'user_id'          => $user->id,
                'student_number'   => $user->student_number ?? 'N/A',
                'full_name'        => $user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name,
                'contact_number'   => $validated['contact_number'],
                'course_program'   => $validated['course_program'],
                'year_level'       => $validated['year_level'],
                'section'          => $validated['section'],
                'total_fee'        => 0,
                'document_types'   => implode(', ', $documentTypeCodes),
                'status'           => $initialStatus,
                'is_printable'     => $allPrintable,
            ]);

            // Generate reference number
            $docRequest->update([
                'reference_number' => 'DQST-' . date('Y') . '-' . str_pad($docRequest->id, 5, '0', STR_PAD_LEFT),
            ]);

            $totalFee = 0;

            foreach ($validated['documents'] as $doc) {
                $docType = DocumentType::findOrFail($doc['document_type_id']);

                DocumentRequestItem::create([
                    'document_request_id' => $docRequest->id,
                    'document_type_id'    => $doc['document_type_id'],
                    'copies'              => $doc['copies'],
                    'assessment_year'     => $doc['assessment_year'] ?? null,
                    'semester'            => $doc['semester'] ?? null,
                    'fee'                 => $docType->fee,
                ]);

                $totalFee += $docType->fee * $doc['copies'];
            }

            $docRequest->update(['total_fee' => $totalFee]);

            // 3. Create the Appointment (if provided)
            if (!empty($validated['appointment_date']) && !empty($validated['time_slot_id'])) {
                \App\Models\Appointment::create([
                    'document_request_id' => $docRequest->id,
                    'user_id'             => $user->id,
                    'time_slot_id'        => $validated['time_slot_id'],
                    'appointment_date'    => $validated['appointment_date'],
                    'status'              => 'scheduled',
                ]);
            }

            // 4. Log status change
            StatusLog::create([
                'document_request_id' => $docRequest->id,
                'changed_by'          => $user->id,
                'old_status'          => null,
                'new_status'          => $initialStatus,
                'notes'               => 'Request submitted by student with appointment booked.',
            ]);

            // Send confirmation email and database notification
            $user->notify(new \App\Notifications\RequestSubmittedNotification($docRequest));

            DB::commit();

            return redirect()->route('student.requests.show', $docRequest->id)
                ->with('success', 'Your request and appointment have been successfully submitted!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong: ' . $e->getMessage())->withInput();
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SHOW REQUEST SUMMARY
    // GET /student/requests/{id}
    // ─────────────────────────────────────────────────────────────────────────
    public function show($id)
    {
        $docRequest = DocumentRequest::with(['items.documentType'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('student.requests.show', compact('docRequest'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CANCEL REQUEST
    // DELETE /student/requests/{id}
    // ─────────────────────────────────────────────────────────────────────────
    public function cancel($id)
    {
        $docRequest = DocumentRequest::where('user_id', Auth::id())->findOrFail($id);

        // Only pending requests can be cancelled
        if ($docRequest->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be cancelled.');
        }

        // Check if there's an active appointment
        $activeAppointment = \App\Models\Appointment::where('document_request_id', $docRequest->id)
            ->where('status', 'scheduled')
            ->first();

        if ($activeAppointment) {
            return back()->with('error', 'Please cancel your appointment first before cancelling the request.');
        }

        $oldStatus = $docRequest->status;
        $docRequest->update(['status' => 'cancelled']);

        StatusLog::create([
            'document_request_id' => $docRequest->id,
            'changed_by'          => Auth::id(),
            'old_status'          => $oldStatus,
            'new_status'          => 'cancelled',
            'notes'               => 'Request cancelled by student.',
        ]);

        // Send notification
        $message = 'Request ' . $docRequest->reference_number . ' has been cancelled.';
        $url = route('student.requests.history');
        $this->sendNotificationToCurrentUser($message, $url);
        session()->flash('check_notifications', true);

        return redirect()->route('student.requests.history');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // REQUEST HISTORY
    // GET /student/history
    // ─────────────────────────────────────────────────────────────────────────
    public function history()
    {
        $requests = DocumentRequest::with(['items.documentType', 'appointment.timeSlot'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        // Get all active time slots for the booking modal
        $timeSlots = \App\Models\TimeSlot::where('is_active', true)
            ->orderBy('start_time')
            ->get();

        return view('student.history.index', compact('requests', 'timeSlots'));
    }
}
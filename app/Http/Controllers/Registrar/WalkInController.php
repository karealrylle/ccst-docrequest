<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use App\Models\DocumentRequest;
use App\Models\DocumentRequestItem;
use App\Models\StatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class WalkInController extends Controller
{
    /**
     * Show the walk-in document request form.
     */
    public function create()
    {
        $documentTypes = DocumentType::where('is_active', true)->get();
        return view('registrar.walkin.create', compact('documentTypes'));
    }

    /**
     * Store the walk-in document request without creating a user account.
     */
    public function store(Request $request)
    {
        // 1. Validate the form input
        $request->validate([
            'full_name' => 'required|string|max:150',
            'student_number' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:150',
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'course_program' => 'required|string|max:100',
            'year_level' => 'required|string|max:50',
            'section' => 'nullable|string|max:50',
            'documents' => 'required|array|min:1',
            'documents.*' => 'exists:document_types,id',
            'copies' => 'required|array',
        ]);

        // 2. Calculate the total fee and determine if all requested docs are printable
        $totalFee = 0;
        $allPrintable = true;

        foreach ($request->documents as $docId) {
            $docType = DocumentType::find($docId);
            $copies = $request->copies[$docId] ?? 1;
            $totalFee += $docType->fee * $copies;
            
            if (!$docType->is_printable) {
                $allPrintable = false;
            }
        }

        // 3. Generate Reference Number (DQST-YYYY-XXXXX)
        $year = date('Y');
        $lastRequest = DocumentRequest::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $sequence = $lastRequest ? intval(substr($lastRequest->reference_number, -5)) + 1 : 1;
        $referenceNumber = 'DQST-' . $year . '-' . str_pad($sequence, 5, '0', STR_PAD_LEFT);

        // Determine initial status based on printability
        // If all documents are printable, it's ready for pickup immediately
        $initialStatus = $allPrintable ? 'ready_for_pickup' : 'pending';

        // 4. Create the Document Request (user_id is null for walk-ins)
        $docRequest = DocumentRequest::create([
            'reference_number' => $referenceNumber,
            'user_id' => null, // No user account
            'student_number' => $request->student_number ?? 'WALK-IN',
            'full_name' => $request->full_name,
            'email' => $request->email,
            'contact_number' => $request->contact_number ?? 'N/A',
            'course_program' => $request->course_program,
            'year_level' => $request->year_level,
            'section' => $request->section ?? 'N/A',
            'total_fee' => $totalFee,
            'status' => $initialStatus,
            'payment_status' => 'unpaid',
            'is_walk_in' => true,
            'is_printable' => $allPrintable,
            'walk_in_handled_by' => Auth::id(),
            'remarks' => $request->address ? 'Address: ' . $request->address : null,
        ]);

        // 5. Create Document Request Items
        foreach ($request->documents as $docId) {
            $docType = DocumentType::find($docId);
            $copies = $request->copies[$docId] ?? 1;

            DocumentRequestItem::create([
                'document_request_id' => $docRequest->id,
                'document_type_id' => $docType->id,
                'copies' => $copies,
                'fee' => $docType->fee,
            ]);
        }

        // 6. Log the status
        StatusLog::create([
            'document_request_id' => $docRequest->id,
            'changed_by' => Auth::id(),
            'old_status' => null,
            'new_status' => $initialStatus,
            'notes' => $allPrintable ? 'Walk-in request created. All documents are printable, marked as ready for pickup immediately.' : 'Walk-in document request created. Awaiting payment and processing.',
        ]);

        // 7. Redirect to the request details page
        return redirect()->route('registrar.requests.show', $docRequest->id)
            ->with('success', 'Walk-in request created successfully. ' . ($allPrintable ? 'The request is ready for printing.' : 'Please generate the Payment Slip for the student.'));
    }

    public function generatePaymentDocument($id)
    {
        $request = DocumentRequest::with(['items.documentType', 'appointment'])->findOrFail($id);
        
        if (!$request->is_walk_in) {
            abort(403, 'This request is not a walk-in request.');
        }

        // Prepare data for the new cashier-payment-slip template
        $data = [
            'student_name' => $request->full_name,
            'student_number' => $request->student_number ?? 'WALK-IN',
            'reference_number' => $request->reference_number,
            'request_date' => $request->created_at->format('F d, Y'),
            'total_fee' => $request->total_fee,
            'requested_documents' => $request->items,
            'request_type' => 'WALK-IN',
            'appointment_date' => $request->appointment ? $request->appointment->appointment_date->format('F d, Y') : 'WALK-IN',
            'current_time' => now()->format('h:i A'),
        ];

        $pdf = Pdf::loadView('pdf.cashier-payment-slip', $data);
        
        // Return inline stream so the registrar can immediately print it
        return $pdf->stream('payment-slip-'.$request->reference_number.'.pdf');
    }

    /**
     * Generate a blank physical document request form for students to fill up.
     */
    public function blankForm()
    {
        $documentTypes = DocumentType::where('is_active', true)->get();
        return view('registrar.walkin.printable-blank-form', compact('documentTypes'));
    }
}

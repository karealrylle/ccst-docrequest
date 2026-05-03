<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Models\StatusLog;
use App\Notifications\DocumentReadyNotification;
use App\Notifications\RequestCompletedNotification;
use App\Traits\SendsDatabaseNotifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class RequestManagementController extends Controller
{
    use SendsDatabaseNotifications;

    public function index()
    {
        $requests = DocumentRequest::with(['items.documentType', 'appointment.timeSlot', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(100);

        $totalRequests = DocumentRequest::count();
        $pendingCount = DocumentRequest::whereIn('status', ['pending', 'payment_method_set', 'payment_uploaded', 'payment_rejected'])->count();
        $readyCount = DocumentRequest::where('status', 'ready_for_pickup')->count();
        $completedCount = DocumentRequest::where('status', 'completed')->count();
        $cancelledCount = DocumentRequest::where('status', 'cancelled')->count();

        return view('registrar.requests.index', compact(
            'requests', 'totalRequests', 'pendingCount', 'readyCount', 'completedCount', 'cancelledCount'
        ));
    }

    public function show($id)
    {
        $request = DocumentRequest::with(['items.documentType', 'appointment.timeSlot', 'user'])
            ->findOrFail($id);

        return view('registrar.requests.show', compact('request'));
    }

    public function updateStatus(Request $request, $id)
    {
        $docRequest = DocumentRequest::findOrFail($id);
        $oldStatus = $docRequest->status;
        $newStatus = $request->input('status');

        // Define allowed status transitions
        $allowedTransitions = [
            'pending' => ['ready_for_pickup', 'completed', 'cancelled'],
            'ready_for_pickup' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => [],
        ];

        // Check if transition is allowed
        if (!in_array($newStatus, $allowedTransitions[$oldStatus] ?? [])) {
            // Send notification to registrar instead of error banner
            $message = "❌ Cannot change status from '" . ucfirst(str_replace('_', ' ', $oldStatus)) . "' to '" . ucfirst(str_replace('_', ' ', $newStatus)) . "'. Invalid status transition.";
            $this->sendNotificationToCurrentUser($message, route('registrar.requests.index'));
            session()->flash('check_notifications', true);
            
            return redirect()->route('registrar.requests.index');
        }

        // Auto-mark payment if completed
        if ($newStatus === 'completed') {
            // payment is now handled manually/by completing the request
        }

        $docRequest->status = $newStatus;
        $docRequest->save();

        // Log status change
        StatusLog::create([
            'document_request_id' => $docRequest->id,
            'changed_by' => Auth::id(),
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'notes' => "Status updated by registrar.",
        ]);

        // Send notification to STUDENT
        $student = $docRequest->user;
        if ($student) {
            $message = $this->getStudentStatusMessage($newStatus, $docRequest->reference_number);
            $url = route('student.requests.history');
            $this->sendNotification($student, $message, $url);
        }

        // Send notification to REGISTRAR (self)
        $registrarMessage = "✅ Request {$docRequest->reference_number} status updated from '" . ucfirst(str_replace('_', ' ', $oldStatus)) . "' to '" . ucfirst(str_replace('_', ' ', $newStatus)) . "'.";
        $this->sendNotificationToCurrentUser($registrarMessage, route('registrar.requests.show', $docRequest->id));
        
        // Set session flag for auto-open notification
        session()->flash('check_notifications', true);

        return redirect()->route('registrar.requests.index');
    }

    public function markAsCompleted($id)
    {
        $docRequest = DocumentRequest::findOrFail($id);

        if ($docRequest->status !== 'ready_for_pickup') {
            $message = "❌ Cannot mark as completed. Request must be 'Ready for Pickup' first.";
            $this->sendNotificationToCurrentUser($message, route('registrar.requests.index'));
            return redirect()->route('registrar.requests.index');
        }

        $oldStatus = $docRequest->status;
        $docRequest->status = 'completed';
        $docRequest->completed_at = now();
        $docRequest->save();

        // Update appointment if exists
        if ($docRequest->appointment) {
            $docRequest->appointment->update([
                'status' => 'completed',
                'claimed_at' => now(),
            ]);
        }

        // Log status change
        StatusLog::create([
            'document_request_id' => $docRequest->id,
            'changed_by' => Auth::id(),
            'old_status' => $oldStatus,
            'new_status' => 'completed',
            'notes' => "Request marked as completed. Student picked up their documents.",
        ]);

        // Send notification to STUDENT (Email + Database)
        $student = $docRequest->user;
        if ($student) {
            $student->notify(new RequestCompletedNotification($docRequest));
        }

        // Send notification to REGISTRAR (Database)
        $registrarMessage = "✅ Request {$docRequest->reference_number} marked as COMPLETED.";
        $this->sendNotificationToCurrentUser($registrarMessage, route('registrar.requests.show', $docRequest->id));
        
        session()->flash('check_notifications', true);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Request marked as completed.'
            ]);
        }

        return redirect()->route('registrar.requests.index');
    }

    /**
     * Mark request as ready for pickup
     */
    public function markAsReady($id)
    {
        $docRequest = DocumentRequest::findOrFail($id);
        
        if ($docRequest->status !== 'pending') {
             if (request()->ajax()) {
                 return response()->json(['success' => false, 'message' => 'Only pending requests can be marked as ready.'], 400);
             }
             return back()->with('error', 'Only pending requests can be marked as ready.');
        }
        
        $oldStatus = $docRequest->status;
        $docRequest->status = 'ready_for_pickup';
        $docRequest->save(); // CRITICAL: Save the change
        
        // Log status change
        \App\Models\StatusLog::create([
            'document_request_id' => $docRequest->id,
            'changed_by' => Auth::id(),
            'old_status' => $oldStatus,
            'new_status' => 'ready_for_pickup',
            'notes' => "Marked as ready for pickup.",
        ]);
        
        // Send database + email notification to student
        $student = $docRequest->user;
        if ($student) {
            $student->notify(new DocumentReadyNotification($docRequest));
        } elseif ($docRequest->is_walk_in && $docRequest->email) {
            // Send email to walk-in student
            \Illuminate\Support\Facades\Notification::route('mail', $docRequest->email)
                ->notify(new \App\Notifications\WalkInDocumentReadyNotification($docRequest));
        }

        session()->flash('check_notifications', true);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Request marked as ready for pickup.'
            ]);
        }
        
        return back()->with('success', 'Request marked as ready for pickup.');
    }



    private function getStudentStatusMessage($status, $referenceNumber)
    {
        return match($status) {
            'ready_for_pickup' => "📦 Your request {$referenceNumber} is ready for pickup! Please proceed to the registrar's office to claim your documents.",
            'received' => "✅ Thank you for picking up your documents for request {$referenceNumber}. Have a great day!",
            'cancelled' => "❌ Your request {$referenceNumber} has been cancelled. Please contact the registrar for more information.",
            'completed' => "✅ Your documents for request {$referenceNumber} have been completed. Thank you for using CCST DocRequest!",
            default => "Your request {$referenceNumber} status has been updated to: " . ucfirst(str_replace('_', ' ', $status)),
        };
    }

    /**
     * Collect payment for an online request (Over-the-Counter Cash)
     */
    public function collectPayment($id)
    {
        $request = DocumentRequest::with(['items.documentType', 'user'])->findOrFail($id);
        
        if ($request->payment_status === 'paid') {
            // Just return the PDF if already paid
            $pdf = Pdf::loadView('registrar.walkin.receipt-pdf', ['request' => $request]);
            return $pdf->stream('receipt-'.$request->reference_number.'.pdf');
        }

        // Update payment status and set status to ready_for_pickup if it's pending and not printable
        $updateData = [
            'payment_status' => 'paid',
            'paid_at' => now(),
        ];
        
        // If it's a physical document request waiting for payment, mark it as ready for pickup 
        // (assuming registrar is printing it or handing it over now)
        if ($request->status === 'pending' && !$request->is_printable) {
            $updateData['status'] = 'ready_for_pickup';
            
            // Log the status change too
            StatusLog::create([
                'document_request_id' => $request->id,
                'changed_by' => Auth::id(),
                'old_status' => 'pending',
                'new_status' => 'ready_for_pickup',
                'notes' => 'Status updated to ready after payment collection.',
            ]);
        }

        $request->update($updateData);

        // Generate receipt PDF
        $pdf = Pdf::loadView('registrar.walkin.receipt-pdf', ['request' => $request]);
        return $pdf->download('receipt-'.$request->reference_number.'.pdf');
    }

    /**
     * Print the cashier payment slip / receipt for any request
     */
    public function printCashierReceipt($id)
    {
        $request = DocumentRequest::with(['items.documentType', 'appointment.timeSlot'])->findOrFail($id);
        
        // Prepare data for the cashier-payment-slip template
        $data = [
            'student_name' => $request->full_name,
            'student_number' => $request->student_number ?? ($request->is_walk_in ? 'WALK-IN' : 'N/A'),
            'reference_number' => $request->reference_number,
            'request_date' => $request->created_at->format('F d, Y'),
            'total_fee' => $request->total_fee,
            'requested_documents' => $request->items,
            'request_type' => $request->is_walk_in ? 'WALK-IN' : 'ONLINE',
            'appointment_date' => $request->appointment ? $request->appointment->appointment_date->format('F d, Y') : ($request->is_walk_in ? $request->created_at->format('F d, Y') : 'N/A'),
            'appointment_time' => $request->appointment ? ($request->appointment->timeSlot->label ?? 'N/A') : ($request->is_walk_in ? $request->created_at->format('h:i A') : 'N/A'),
            'current_time' => now()->format('h:i A'),
        ];

        $pdf = Pdf::loadView('pdf.cashier-payment-slip', $data);
        return $pdf->stream('payment-slip-'.$request->reference_number.'.pdf');
    }
}
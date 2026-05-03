<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\TimeSlot;
use App\Traits\SendsDatabaseNotifications;
use Illuminate\Http\Request;
use App\Models\DocumentRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    use SendsDatabaseNotifications;

    public function index()
    {
        $upcomingAppointments = Appointment::with(['student', 'documentRequest', 'timeSlot'])
            ->where('status', 'scheduled')
            ->whereDate('appointment_date', '>=', today())
            ->orderBy('appointment_date', 'asc')
            ->orderBy('time_slot_id', 'asc')
            ->get();

        $todayAppointments = Appointment::with(['student', 'documentRequest', 'timeSlot'])
            ->whereDate('appointment_date', today())
            ->orderBy('time_slot_id', 'asc')
            ->get();

        $allAppointments = Appointment::with(['student', 'documentRequest', 'timeSlot'])
            ->orderBy('created_at', 'desc')
            ->get();

        $timeSlots = TimeSlot::orderBy('start_time', 'asc')->get();

        $upcomingCount = $upcomingAppointments->count();
        $todayCount = $todayAppointments->count();

        return view('registrar.appointments.index', compact(
            'upcomingAppointments',
            'todayAppointments',
            'allAppointments',
            'timeSlots',
            'upcomingCount',
            'todayCount'
        ));
    }

    public function complete($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->update(['status' => 'completed']);

        return redirect()
            ->route('registrar.appointments.index')
            ->with('success', 'Appointment marked as completed.');
    }

    public function missed($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->update(['status' => 'missed']);

        return redirect()
            ->route('registrar.appointments.index')
            ->with('success', 'Appointment marked as missed.');
    }

    public function storeSlot(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:100',
            'start_time' => 'required',
            'end_time' => 'required',
            'max_capacity' => 'required|integer|min:1|max:20',
            'is_active' => 'nullable|boolean',
        ]);

        TimeSlot::create([
            'label' => $validated['label'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'max_capacity' => $validated['max_capacity'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()
            ->route('registrar.appointments.index')
            ->with('success', 'Time slot added successfully.');
    }

    public function updateSlot(Request $request, $id)
    {
        $slot = TimeSlot::findOrFail($id);

        $validated = $request->validate([
            'label' => 'required|string|max:100',
            'start_time' => 'required',
            'end_time' => 'required',
            'max_capacity' => 'required|integer|min:1|max:20',
            'is_active' => 'nullable|boolean',
        ]);

        $slot->update([
            'label' => $validated['label'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'max_capacity' => $validated['max_capacity'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()
            ->route('registrar.appointments.index')
            ->with('success', 'Time slot updated successfully.');
    }

    public function toggleSlot($id)
    {
        $slot = TimeSlot::findOrFail($id);
        $slot->update(['is_active' => !$slot->is_active]);

        return redirect()
            ->route('registrar.appointments.index')
            ->with('success', 'Time slot status toggled.');
    }

    public function getSlotData($id)
    {
        $slot = TimeSlot::findOrFail($id);
        return response()->json([
            'label' => $slot->label,
            'start_time' => $slot->start_time,
            'end_time' => $slot->end_time,
            'max_capacity' => $slot->max_capacity,
            'is_active' => $slot->is_active,
        ]);
    }
    

    public function printCashierList()
    {
        $range = request('range', 'today');
        $today = today();
        
        $query = DocumentRequest::with(['appointment.timeSlot', 'items.documentType', 'user'])
            ->orderBy('created_at', 'desc');

        switch ($range) {
            case 'today':
                $query->where(function($q) use ($today) {
                    $q->whereHas('appointment', function($sub) use ($today) {
                        $sub->whereDate('appointment_date', $today);
                    })->orWhere(function($sub) use ($today) {
                        $sub->where('is_walk_in', true)
                            ->whereDate('created_at', $today);
                    });
                });
                $dateLabel = $today->format('F d, Y');
                break;
            case 'all':
                $dateLabel = 'All Requests';
                break;
            default:
                $query->where(function($q) use ($today) {
                    $q->whereHas('appointment', function($sub) use ($today) {
                        $sub->whereDate('appointment_date', $today);
                    })->orWhere(function($sub) use ($today) {
                        $sub->where('is_walk_in', true)
                            ->whereDate('created_at', $today);
                    });
                });
                $dateLabel = $today->format('F d, Y');
                break;
        }

        $appointments = $query->get()
            ->map(function ($docRequest) {
                return (object) [
                    'date' => $docRequest->is_walk_in 
                        ? $docRequest->created_at->format('M d, Y')
                        : ($docRequest->appointment ? $docRequest->appointment->appointment_date->format('M d, Y') : $docRequest->created_at->format('M d, Y')),
                    'time_slot' => $docRequest->appointment->timeSlot->label ?? 'WALK-IN',
                    'student_name' => $docRequest->full_name,
                    'student_number' => $docRequest->student_number,
                    'reference_number' => $docRequest->reference_number,
                    'amount' => $docRequest->total_fee,
                    'strand' => $docRequest->course_program, // Using course_program as strand/program
                    'grade_section' => $docRequest->year_level . ' - ' . $docRequest->section,
                    'request_type' => $docRequest->is_walk_in ? 'WALK-IN' : 'ONLINE',
                    'requested_documents' => $docRequest->items,
                ];
            });

        $date = $dateLabel;
        $printed_by = auth()->user()->name;
        $totalAmount = $appointments->sum('amount');
        $totalStudents = $appointments->count();

        $pdf = Pdf::loadView('pdf.cashier-list', compact('appointments', 'date', 'printed_by', 'totalAmount', 'totalStudents'));
        
        return $pdf->stream('cashier-list-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Print all cashier receipts for a specific date (Bulk Print)
     */
    public function bulkPrintCashierReceipts()
    {
        $date = request('date', today()->format('Y-m-d'));
        $targetDate = \Carbon\Carbon::parse($date);
        
        // Get both scheduled appointments and walk-ins for this date
        $requests = DocumentRequest::with(['items.documentType', 'user', 'appointment.timeSlot'])
            ->where(function($query) use ($targetDate) {
                $query->whereHas('appointment', function($sub) use ($targetDate) {
                    $sub->whereDate('appointment_date', $targetDate)
                        ->where('status', 'scheduled');
                })->orWhere(function($sub) use ($targetDate) {
                    $sub->where('is_walk_in', true)
                        ->whereDate('created_at', $targetDate);
                });
            })
            ->get();

        if ($requests->isEmpty()) {
            return back()->with('error', 'No scheduled appointments or walk-ins found for this date.');
        }

        $receiptsData = $requests->map(function ($req) {
            return [
                'reference_number' => $req->reference_number,
                'student_name' => $req->full_name,
                'student_number' => $req->student_number ?? 'N/A',
                'appointment_date' => $req->appointment ? $req->appointment->appointment_date->format('F d, Y') : $req->created_at->format('F d, Y'),
                'appointment_time' => $req->appointment ? ($req->appointment->timeSlot->label ?? 'N/A') : $req->created_at->format('h:i A'),
                'request_type' => $req->is_walk_in ? 'WALK-IN' : 'ONLINE',
                'total_fee' => $req->total_fee,
                'requested_documents' => $req->items,
                'current_time' => now()->format('h:i A'),
            ];
        });

        $pdf = Pdf::loadView('pdf.bulk-cashier-receipts', [
            'receipts' => $receiptsData,
            'dateLabel' => \Carbon\Carbon::parse($date)->format('F d, Y')
        ]);
        
        return $pdf->stream('bulk-receipts-' . $date . '.pdf');
    }

}
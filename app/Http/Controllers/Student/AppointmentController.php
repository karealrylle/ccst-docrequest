<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\DocumentRequest;
use App\Models\TimeSlot;
use App\Notifications\AppointmentConfirmedNotification;
use App\Traits\SendsDatabaseNotifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    use SendsDatabaseNotifications;

    /**
     * Store a new appointment
     * POST /student/appointments
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_request_id' => 'required|exists:document_requests,id',
            'appointment_date'    => 'required|date|after_or_equal:today',
            'time_slot_id'        => 'required|exists:time_slots,id',
        ]);

        $user = Auth::user();
        
        // Get the document request and verify ownership
        $docRequest = DocumentRequest::where('user_id', $user->id)
            ->where('id', $validated['document_request_id'])
            ->firstOrFail();

        // Verify request is ready for pickup OR is printable and pending
        $canBookAppointment = $docRequest->status === 'ready_for_pickup' || 
                             ($docRequest->status === 'pending' && $docRequest->is_printable);
        
        if (!$canBookAppointment) {
            return back()->with('error', 'You can only book appointments for requests that are ready for pickup or printable documents awaiting processing.');
        }

        // Check if appointment already exists for this request
        $existingAppointment = Appointment::where('document_request_id', $docRequest->id)->first();
        if ($existingAppointment) {
            return back()->with('error', 'An appointment already exists for this request.');
        }

        // Get the time slot and check capacity
        $timeSlot = TimeSlot::findOrFail($validated['time_slot_id']);
        
        // Check if slot is at capacity
        $appointmentsCount = Appointment::where('time_slot_id', $timeSlot->id)
            ->where('appointment_date', $validated['appointment_date'])
            ->count();
        
        if ($appointmentsCount >= $timeSlot->max_capacity) {
            return back()->with('error', 'This time slot is fully booked. Please choose another slot.');
        }

        // Create the appointment
        $appointment = Appointment::create([
            'document_request_id' => $docRequest->id,
            'user_id'             => $user->id,
            'time_slot_id'        => $timeSlot->id,
            'appointment_date'    => $validated['appointment_date'],
            'status'              => 'scheduled',
        ]);

        // Send confirmation email and database notification
        $user->notify(new AppointmentConfirmedNotification($appointment));

        return redirect()
            ->route('student.requests.history')
            ->with('success', 'Appointment booked successfully! Your pickup appointment is scheduled.');
    }

    /**
     * Reschedule an existing appointment
     * PATCH /student/appointments/{id}
     */
    public function reschedule(Request $request, $id)
    {
        $validated = $request->validate([
            'appointment_date' => 'required|date|after_or_equal:today',
            'time_slot_id'     => 'required|exists:time_slots,id',
        ]);

        $user = Auth::user();
        
        // Get the appointment and verify ownership
        $appointment = Appointment::where('user_id', $user->id)
            ->with('documentRequest')
            ->findOrFail($id);

        // Check if appointment can be rescheduled (not completed or missed)
        if (in_array($appointment->status, ['completed', 'missed', 'canceled'])) {
            return back()->with('error', 'This appointment cannot be rescheduled.');
        }

        // Check if request is pending or ready for pickup
        if (!in_array($appointment->documentRequest->status, ['pending', 'ready_for_pickup'])) {
            return back()->with('error', 'You can only reschedule pending or ready-for-pickup requests.');
        }

        // Check capacity for new time slot
        $timeSlot = TimeSlot::findOrFail($validated['time_slot_id']);
        $appointmentsCount = Appointment::where('time_slot_id', $timeSlot->id)
            ->where('appointment_date', $validated['appointment_date'])
            ->where('id', '!=', $id)
            ->count();
        
        if ($appointmentsCount >= $timeSlot->max_capacity) {
            return back()->with('error', 'This time slot is fully booked. Please choose another slot.');
        }

        // Store old slot info for notification
        $oldSlot = $appointment->timeSlot;
        $oldDate = $appointment->appointment_date;

        // Update the appointment
        $appointment->update([
            'time_slot_id'     => $timeSlot->id,
            'appointment_date' => $validated['appointment_date'],
            'status'           => 'scheduled',
        ]);

        // Send confirmation email and database notification for reschedule
        $user->notify(new AppointmentConfirmedNotification($appointment));

        return redirect()
            ->route('student.requests.history')
            ->with('success', 'Appointment rescheduled successfully!');
    }

    /**
     * Cancel an appointment
     * DELETE /student/appointments/{id}
     */
    public function cancel($id)
    {
        $user = Auth::user();
        
        // Get the appointment and verify ownership
        $appointment = Appointment::where('user_id', $user->id)
            ->with(['timeSlot', 'documentRequest'])
            ->findOrFail($id);

        // Check if appointment can be cancelled
        if (in_array($appointment->status, ['completed', 'missed', 'canceled'])) {
            return back()->with('error', 'This appointment cannot be cancelled.');
        }

        // Store info for notification
        $appointmentDate = $appointment->appointment_date;
        $timeSlotLabel = $appointment->timeSlot->label;
        $docRequest = $appointment->documentRequest;
        
        // Delete the appointment (or set status to cancelled)
        $appointment->delete(); // This completely removes it so a new one can be created
        
        // Send notification to student
        $message = 'Your appointment scheduled for ' . 
                date('F j, Y', strtotime($appointmentDate)) . 
                ' at ' . $timeSlotLabel . 
                ' has been cancelled. You can book a new appointment anytime.';
        $url = route('student.requests.history');
        $this->sendNotificationToCurrentUser($message, $url);

        return redirect()
            ->route('student.requests.history')
            ->with('success', 'Appointment cancelled successfully. You can book a new appointment.');
    }

    /**
     * Get available slots for a specific date (AJAX)
     * GET /student/appointments/available-slots
     */
    public function getAvailableSlots(Request $request)
    {
        $date = $request->query('date');
        if (!$date) {
            return response()->json([]);
        }

        $timeSlots = TimeSlot::where('is_active', true)->orderBy('start_time', 'asc')->get();
        $availableSlots = [];

        foreach ($timeSlots as $slot) {
            $appointmentsCount = Appointment::where('time_slot_id', $slot->id)
                ->where('appointment_date', $date)
                ->whereNotIn('status', ['canceled', 'missed']) // active appointments
                ->count();

            $available = max(0, $slot->max_capacity - $appointmentsCount);

            $availableSlots[] = [
                'id' => $slot->id,
                'label' => $slot->label,
                'max_capacity' => $slot->max_capacity,
                'available' => $available,
                'is_full' => $available <= 0
            ];
        }

        return response()->json($availableSlots);
    }
}
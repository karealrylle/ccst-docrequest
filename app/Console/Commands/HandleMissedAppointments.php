<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\DocumentRequest;
use App\Notifications\AppointmentMissedNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HandleMissedAppointments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:handle-missed-appointments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates scheduled appointments that were missed (past date) and notifies students.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to process missed appointments...');

        // Find appointments that are 'scheduled' and have a date in the past
        $missedAppointments = Appointment::where('status', 'scheduled')
            ->where('appointment_date', '<', now()->toDateString())
            ->with(['documentRequest', 'student'])
            ->get();

        if ($missedAppointments->isEmpty()) {
            $this->info('No missed appointments found.');
            return;
        }

        $count = 0;

        foreach ($missedAppointments as $appointment) {
            $docRequest = $appointment->documentRequest;

            // Skip if the request is already completed or cancelled
            if (!$docRequest || in_array($docRequest->status, ['completed', 'cancelled', 'received'])) {
                continue;
            }

            DB::transaction(function () use ($appointment, $docRequest) {
                $oldStatus = $docRequest->status;

                // 1. Update appointment status to 'missed'
                $appointment->update(['status' => 'missed']);

                // 2. Update document request status to 'pending'
                $docRequest->update(['status' => 'pending']);

                // 3. Log the status change
                DB::table('status_logs')->insert([
                    'document_request_id' => $docRequest->id,
                    'changed_by' => 1, // System/Admin user ID (assumed 1) or a dedicated system user
                    'old_status' => $oldStatus,
                    'new_status' => 'pending',
                    'notes' => 'No-show: appointment missed on ' . $appointment->appointment_date->format('Y-m-d'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // 4. Notify the student
                if ($appointment->student) {
                    $appointment->student->notify(new AppointmentMissedNotification($appointment));
                }
            });

            $count++;
        }

        $this->info("Successfully processed {$count} missed appointments.");
        Log::info("Missed appointment handler: {$count} records updated.");
    }
}

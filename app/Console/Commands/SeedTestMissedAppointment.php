<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\DocumentRequest;
use App\Models\Appointment;

class SeedTestMissedAppointment extends Command
{
    protected $signature = 'test:seed-missed';
    protected $description = 'Seed a missed appointment for student@ccst.edu.ph';

    public function handle()
    {
        $user = User::where('email', 'student@ccst.edu.ph')->first();
        if (!$user) {
            $this->error('User student@ccst.edu.ph not found.');
            return;
        }

        for ($i = 1; $i <= 5; $i++) {
            $docRequest = DocumentRequest::create([
                'user_id' => $user->id,
                'reference_number' => 'DQST-TEST-' . now()->timestamp . '-' . $i,
                'status' => 'pending',
                'full_name' => $user->name,
                'email' => $user->email,
                'student_number' => $user->student_number ?? '2024-000' . $i,
                'contact_number' => $user->contact_number ?? '0912345678' . $i,
                'course_program' => $user->strand ?? 'General Academics',
                'year_level' => $user->grade_level ?? '12',
                'section' => $user->section ?? 'A',
                'total_fee' => 0.00,
            ]);

            Appointment::create([
                'document_request_id' => $docRequest->id,
                'user_id' => $user->id,
                'appointment_date' => now()->subDays(rand(1, 10))->toDateString(),
                'status' => 'scheduled',
                'time_slot_id' => rand(1, 5)
            ]);
        }

        $this->info('5 Test appointments created with past dates.');
    }
}

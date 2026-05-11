<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schedule;
use App\Models\Appointment;
use App\Notifications\AppointmentReminderNotification;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $tomorrow = \Carbon\Carbon::tomorrow()->toDateString();
    
    $appointments = Appointment::where('appointment_date', $tomorrow)
        ->where('status', 'scheduled')
        ->with('user')
        ->get();
        
    foreach ($appointments as $appointment) {
        if ($appointment->user) {
            $appointment->user->notify(new AppointmentReminderNotification($appointment));
        }
    }
})->daily();

Schedule::command('app:purge-soft-deleted-students')->daily();
Schedule::command('app:handle-missed-appointments')->dailyAt('23:59');

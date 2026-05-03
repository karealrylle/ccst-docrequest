<?php

use Illuminate\Support\Facades\Route;

// Auth Controllers
use App\Http\Controllers\ProfileController;

// Student Controllers
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use App\Http\Controllers\Student\DocumentRequestController;
use App\Http\Controllers\Student\PaymentController;
use App\Http\Controllers\Student\AppointmentController as StudentAppointment;
use App\Http\Controllers\Student\NotificationController as StudentNotification;

// Registrar Controllers
use App\Http\Controllers\Registrar\DashboardController as RegistrarDashboard;
use App\Http\Controllers\Registrar\RequestManagementController;
use App\Http\Controllers\Registrar\AppointmentController as RegistrarAppointment;
use App\Http\Controllers\Registrar\ReportController;
use App\Http\Controllers\Registrar\NotificationController as RegistrarNotification;
use App\Http\Controllers\Registrar\StudentVerificationController;
use App\Http\Controllers\Registrar\CalendarController;
use App\Http\Controllers\Registrar\WalkInController;
use App\Http\Controllers\Registrar\DocumentGeneratorController;
use App\Http\Controllers\Registrar\EmailTemplateController;
use App\Http\Controllers\Registrar\StudentManagementController;
use App\Http\Controllers\Registrar\DocumentTypeController;
use App\Http\Controllers\Registrar\RegistrarManagementController;


// Cashier Controllers
use App\Http\Controllers\Cashier\DashboardController as CashierDashboard;
use App\Http\Controllers\Cashier\PaymentVerificationController;
use App\Http\Controllers\Cashier\ReceiptController;
use App\Http\Controllers\Cashier\PaymentSettingsController;
use App\Http\Controllers\Cashier\NotificationController as CashierNotification;

// ═══════════════════════════════════════════════════════════════════
// PUBLIC ROUTES
// ═══════════════════════════════════════════════════════════════════

Route::get('/', function () {
    return redirect()->route('login');
});

require __DIR__.'/auth.php';

// ─────────────────────────────────────────────────────────────────────────────
// POST-LOGIN REDIRECT
// ─────────────────────────────────────────────────────────────────────────────

Route::get('/dashboard', function () {
    $role = auth()->user()->role;

    return match($role) {
        'student'   => redirect()->route('student.dashboard'),
        'registrar' => redirect()->route('registrar.dashboard'),
        'cashier'   => redirect()->route('cashier.dashboard'),
        default     => abort(403, 'Unknown role.'),
    };
})->middleware('auth')->name('dashboard');

Route::get('/test-notifications', function() {
    $user = auth()->user();
    if (!$user) return 'Please login first';
    
    // 1. Database System Notification
    $user->notify(new \App\Notifications\SystemNotification(
        "🛠️ Test Notification: System is working correctly!",
        route('dashboard')
    ));
    
    // 2. Document Ready (Email + DB)
    $docReq = \App\Models\DocumentRequest::first();
    if ($docReq) {
        $user->notify(new \App\Notifications\DocumentReadyNotification($docReq));
    }
    
    // 3. Account Verified (Email + DB)
    $user->notify(new \App\Notifications\AccountVerifiedNotification());
    
    // 4. Request Submitted (Email + DB)
    if ($docReq) {
        $user->notify(new \App\Notifications\RequestSubmittedNotification($docReq));
    }

    return "✅ 4 Test notifications (Database + Email) have been sent to your account!";
})->middleware(['auth']);

// ─────────────────────────────────────────────────────────────────────────────
// STUDENT ROUTES
// ─────────────────────────────────────────────────────────────────────────────

Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {

    // ── Dashboard & Info ─────────────────────────────────────────────────────
    Route::get('/dashboard', [StudentDashboard::class, 'index'])->name('dashboard');
    Route::get('/documents', [StudentDashboard::class, 'documents'])->name('documents.available');
    
    // ── Account ──────────────────────────────────────────────────────────────
    Route::get('/account', [StudentDashboard::class, 'account'])->name('account.index');
    Route::get('/account/photo', [StudentDashboard::class, 'servePhoto'])->name('account.photo');
    Route::post('/account/photo', [StudentDashboard::class, 'updatePhoto'])->name('account.updatePhoto');
    Route::patch('/account/profile', [StudentDashboard::class, 'updateProfile'])->name('account.updateProfile');
    Route::patch('/account/password', [StudentDashboard::class, 'updatePassword'])->name('account.updatePassword');

    // ── Document Requests ────────────────────────────────────────────────────
    Route::controller(DocumentRequestController::class)->group(function() {
        Route::get('/requests/create', 'create')->name('requests.create');
        Route::post('/requests', 'store')->name('requests.store');
        Route::get('/requests/{id}', 'show')->name('requests.show');
        Route::delete('/requests/{id}', 'cancel')->name('requests.cancel');
        Route::get('/history', 'history')->name('requests.history');
    });

    // ── Payments ─────────────────────────────────────────────────────────────
    Route::controller(PaymentController::class)->group(function() {
        Route::patch('/requests/{id}/payment-method', 'setMethod')->name('payments.setMethod');
        Route::get('/requests/{id}/upload', 'showUpload')->name('payments.showUpload');
        Route::post('/requests/{id}/upload', 'store')->name('payments.store');
        Route::post('/requests/{id}/reupload', 'reupload')->name('payments.reupload');
        Route::get('/receipts/{id}/download', 'downloadReceipt')->name('receipts.download');
    });

    // ── Appointments ─────────────────────────────────────────────────────────
    Route::controller(StudentAppointment::class)->group(function() {
        Route::post('/appointments', 'store')->name('appointments.store');
        Route::patch('/appointments/{id}', 'reschedule')->name('appointments.reschedule');
        Route::delete('/appointments/{id}', 'cancel')->name('appointments.cancel');
    });

    // ── Notifications (AJAX) ─────────────────────────────────────────────────
    Route::controller(StudentNotification::class)->group(function() {
        Route::get('/notifications', 'index')->name('notifications.index');
        Route::patch('/notifications/{id}/read', 'markOneRead')->name('notifications.markOneRead');
        Route::post('/notifications/mark-all-read', 'markAllRead')->name('notifications.markAllRead');
    });
    
    // ── Appointments ─────────────────────────────────────────────────────────
    Route::controller(StudentAppointment::class)->group(function() {
        Route::get('/appointments/available-slots', 'getAvailableSlots')->name('appointments.available-slots');
        Route::get('/appointments/create/{requestId}', 'create')->name('appointments.create');
        Route::post('/appointments', 'store')->name('appointments.store');
        Route::patch('/appointments/{id}', 'reschedule')->name('appointments.reschedule');
        Route::delete('/appointments/{id}', 'cancel')->name('appointments.cancel');
    });

});

// ─────────────────────────────────────────────────────────────────────────────
// REGISTRAR ROUTES
// ─────────────────────────────────────────────────────────────────────────────

Route::middleware(['auth', 'role:registrar'])->prefix('registrar')->name('registrar.')->group(function () {

    // ── Dashboard & Account ──────────────────────────────────────────────────
    Route::get('/dashboard', [RegistrarDashboard::class, 'index'])->name('dashboard');
    Route::get('/account', [RegistrarDashboard::class, 'account'])->name('account');
    Route::patch('/announcements/{id}', [RegistrarDashboard::class, 'update'])->name('announcements.update');
    Route::patch('/announcements/{id}/publish', [RegistrarDashboard::class, 'publish'])->name('announcements.publish');

    // ── Request Management ───────────────────────────────────────────────────
    Route::controller(RequestManagementController::class)->group(function() {
        Route::get('/requests', 'index')->name('requests.index');
        Route::get('/requests/{id}', 'show')->name('requests.show');
        Route::patch('/requests/{id}/status', 'updateStatus')->name('requests.updateStatus');
        Route::patch('/requests/{id}/completed', 'markAsCompleted')->name('requests.completed');
        Route::patch('/requests/{id}/mark-ready', 'markAsReady')->name('requests.mark-ready');
        Route::match(['patch', 'get'], '/requests/{id}/collect-payment', 'collectPayment')->name('requests.collect-payment');
        Route::get('/requests/{id}/print-cashier-receipt', 'printCashierReceipt')->name('requests.print-cashier-receipt');
    });

    // ── Appointments Management ─────────────────────────────────────────
    Route::controller(RegistrarAppointment::class)->group(function() {
        Route::get('/appointments', 'index')->name('appointments.index');
        // Route::get('/appointments/print-cashier-list', 'printCashierList')->name('appointments.print-cashier-list');
        Route::get('/appointments/print-cashier-list', [RegistrarAppointment::class, 'printCashierList'])->name('appointments.print-cashier-list');
        Route::get('/appointments/bulk-print-receipts', [RegistrarAppointment::class, 'bulkPrintCashierReceipts'])->name('appointments.bulk-print-receipts');
        Route::patch('/appointments/{id}/complete', 'complete')->name('appointments.complete');
        Route::patch('/appointments/{id}/missed', 'missed')->name('appointments.missed');
        Route::get('/time-slots/{id}/data', 'getSlotData')->name('timeslots.data');
        Route::post('/time-slots', 'storeSlot')->name('timeslots.store');
        Route::patch('/time-slots/{id}', 'updateSlot')->name('timeslots.update');
        Route::patch('/time-slots/{id}/toggle', 'toggleSlot')->name('timeslots.toggle');
    });

    // ── Reports ──────────────────────────────────────────────────────────────
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    

    // ── Notifications (AJAX) ─────────────────────────────────────────────────
    Route::controller(RegistrarNotification::class)->group(function() {
        Route::get('/notifications', 'index')->name('notifications.index');
        Route::patch('/notifications/{id}/read', 'markOneRead')->name('notifications.markOneRead');
        Route::post('/notifications/mark-all-read', 'markAllRead')->name('notifications.markAllRead');
    });


    // ── Account ──────────────────────────────────────────────────────────
    Route::get('/account', [RegistrarDashboard::class, 'account'])->name('account');
    Route::get('/account/photo', [RegistrarDashboard::class, 'servePhoto'])->name('account.photo');
    Route::post('/account/photo', [RegistrarDashboard::class, 'updatePhoto'])->name('account.updatePhoto');
    Route::patch('/account/profile', [RegistrarDashboard::class, 'updateProfile'])->name('account.updateProfile');
    Route::patch('/account/password', [RegistrarDashboard::class, 'updatePassword'])->name('account.updatePassword');



    // ── Student Verification & Management ──────────────────────────────────────────────
    Route::prefix('students')->name('students.')->group(function () {
        Route::get('/', [StudentManagementController::class, 'index'])->name('index');
        Route::get('/pending', [StudentVerificationController::class, 'pending'])->name('pending');
        Route::patch('/{id}/verify', [StudentVerificationController::class, 'verify'])->name('verify');
        Route::post('/verify-bulk', [StudentVerificationController::class, 'verifyBulk'])->name('verify-bulk');
        Route::get('/{id}/id', [StudentVerificationController::class, 'showId'])->name('show-id');
        Route::delete('/{id}/reject', [StudentVerificationController::class, 'reject'])->name('reject');
        
        Route::get('/{id}', [StudentManagementController::class, 'show'])->name('show');
        Route::patch('/{id}/toggle-active', [StudentManagementController::class, 'toggleActive'])->name('toggle-active');
        Route::post('/{id}/send-reset', [StudentManagementController::class, 'sendPasswordReset'])->name('send-reset');
    });

    // ── Calendar ──────────────────────────────────────────────────────────
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');

    // ── Walk-in Mode ──────────────────────────────────────────────────────
    Route::prefix('walkin')->name('walkin.')->group(function () {
        Route::get('/blank-form', [WalkInController::class, 'blankForm'])->name('blank-form');
        Route::get('/create', [WalkInController::class, 'create'])->name('create');
        Route::post('/store', [WalkInController::class, 'store'])->name('store');
        Route::get('/payment/{id}', [WalkInController::class, 'generatePaymentDocument'])->name('payment');
    });

    // ── Document Generation ──────────────────────────────────────────────
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/prepare/{requestId}/{documentTypeId}', [DocumentGeneratorController::class, 'prepare'])->name('prepare');
        Route::post('/generate/{requestId}/{documentTypeId}', [DocumentGeneratorController::class, 'generate'])->name('generate');
        Route::get('/generate/{requestId}/{documentTypeId}', [DocumentGeneratorController::class, 'generate'])->name('generate-get');
        Route::get('/generate-all/{requestId}', [DocumentGeneratorController::class, 'generateAll'])->name('generate-all');
        Route::post('/print-selected', [DocumentGeneratorController::class, 'printSelected'])->name('print-selected');
        Route::get('/preview/{requestId}/{documentTypeId}', [DocumentGeneratorController::class, 'preview'])->name('preview');
        Route::get('/download', [DocumentGeneratorController::class, 'download'])->name('download');
    });

    // ── Calendar ──────────────────────────────────────────────────────────
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
    Route::get('/calendar/appointments', [CalendarController::class, 'getAppointments'])->name('calendar.appointments');
    Route::get('/calendar/time-slots', [CalendarController::class, 'getTimeSlots'])->name('calendar.time-slots');
    Route::get('/calendar/requests-by-date', [CalendarController::class, 'getRequestsByDate'])->name('calendar.requests-by-date');
    Route::patch('/calendar/appointments/{id}/reschedule', [CalendarController::class, 'reschedule'])->name('calendar.reschedule');

    
    // ── Time Slot Management (AJAX) ──────────────────────────────────────
    Route::post('/time-slots', [CalendarController::class, 'storeTimeSlot'])->name('timeslots.store');
    Route::put('/time-slots/{id}', [CalendarController::class, 'updateTimeSlot'])->name('timeslots.update');
    Route::delete('/time-slots/{id}', [CalendarController::class, 'deleteTimeSlot'])->name('timeslots.delete');
    Route::patch('/time-slots/{id}/toggle', [CalendarController::class, 'toggleTimeSlot'])->name('timeslots.toggle');
    Route::get('/time-slots/{id}/data', [CalendarController::class, 'getSlotData'])->name('timeslots.data');

    // ── Email Templates ──────────────────────────────────────────────────
    Route::prefix('email-templates')->name('email-templates.')->group(function () {
        Route::get('/', [EmailTemplateController::class, 'index'])->name('index');
        Route::patch('/{id}', [EmailTemplateController::class, 'update'])->name('update');
        Route::post('/{id}/reset', [EmailTemplateController::class, 'reset'])->name('reset');
        Route::get('/{id}/preview', [EmailTemplateController::class, 'preview'])->name('preview');
        Route::get('/email-templates/{id}', [EmailTemplateController::class, 'show'])->name('email-templates.show');
    });

    // ── Document Types Management ──────────────────────────────────────────────
    Route::resource('document-types', DocumentTypeController::class)->except(['show']);

    // ── Manage Registrars ──────────────────────────────────────────────────
    Route::prefix('manage')->name('manage.')->group(function () {
        Route::get('/', [RegistrarManagementController::class, 'index'])->name('index');
        Route::get('/create', [RegistrarManagementController::class, 'create'])->name('create');
        Route::post('/', [RegistrarManagementController::class, 'store'])->name('store');
        Route::delete('/{id}', [RegistrarManagementController::class, 'destroy'])->name('destroy');
    });

});

// ─────────────────────────────────────────────────────────────────────────────
// CASHIER ROUTES
// ─────────────────────────────────────────────────────────────────────────────

Route::middleware(['auth', 'role:cashier'])->prefix('cashier')->name('cashier.')->group(function () {

    // ── Dashboard & Account ──────────────────────────────────────────────────
    Route::get('/dashboard', [CashierDashboard::class, 'index'])->name('dashboard');
    Route::get('/account', [CashierDashboard::class, 'account'])->name('account');
    Route::patch('/account/profile', [CashierDashboard::class, 'updateProfile'])->name('account.updateProfile');
    Route::patch('/account/password', [CashierDashboard::class, 'updatePassword'])->name('account.updatePassword');
    Route::get('/account/photo', [CashierDashboard::class, 'servePhoto'])->name('account.photo');
    Route::post('/account/photo', [CashierDashboard::class, 'updatePhoto'])->name('account.updatePhoto');

    // ── Payment Verification ─────────────────────────────────────────────────
    Route::controller(PaymentVerificationController::class)->group(function() {
        Route::get('/payments', 'index')->name('payments.index');
        Route::get('/payments/{id}', 'show')->name('payments.show');
        Route::patch('/payments/{id}/verify', 'verify')->name('payments.verify');
        Route::patch('/payments/{id}/reject', 'reject')->name('payments.reject');
        Route::patch('/payments/{id}/mark-cash-paid', 'markCashPaid')->name('payments.markCashPaid');
        Route::get('/payments/{id}/proof', [PaymentVerificationController::class, 'serveProof'])->name('payments.proof');
    });

    // ── Receipts ─────────────────────────────────────────────────────────────
    Route::get('/receipts/{id}/download', [ReceiptController::class, 'download'])->name('receipts.download');

    // ── Payment Settings ─────────────────────────────────────────────────────
    Route::controller(PaymentSettingsController::class)->group(function() {
        Route::get('/settings', 'index')->name('settings.index');
        Route::patch('/settings/{id}', 'update')->name('settings.update');
        Route::patch('/settings/{id}/toggle', 'toggle')->name('settings.toggle');
    });

    // ── Notifications (AJAX) ─────────────────────────────────────────────────
    Route::controller(CashierNotification::class)->group(function() {
        Route::get('/notifications', 'index')->name('notifications.index');
        Route::patch('/notifications/{id}/read', 'markOneRead')->name('notifications.markOneRead');
        Route::post('/notifications/mark-all-read', 'markAllRead')->name('notifications.markAllRead');
    });

    Route::post('/settings', [PaymentSettingsController::class, 'store'])->name('settings.store');
    Route::delete('/settings/{id}', [PaymentSettingsController::class, 'destroy'])->name('settings.destroy');

});

// Debug route - Remove after testing
Route::get('/debug-notifications', function () {
    $user = App\Models\User::find(10); // Change to your student ID
    $notifications = $user->notifications()->latest()->take(10)->get();
    
    $result = [];
    foreach ($notifications as $n) {
        $result[] = [
            'id' => $n->id,
            'data' => $n->data,
            'created_at' => $n->created_at->toDateTimeString(),
        ];
    }
    
    return response()->json($result);
})->middleware('auth');

// ═══ TEMPORARY: Test email sending ═══════════════════════════════════════
Route::get('/test-email', function () {
    $user = auth()->user();
    
    // Test 1: Raw email
    try {
        \Illuminate\Support\Facades\Mail::raw(
            'This is a test email from CCST DocRequest. If you received this, email sending works!',
            function ($message) use ($user) {
                $message->to($user->email)->subject('CCST DocRequest - Email Test');
            }
        );
        $rawResult = '✅ Raw email sent successfully to ' . $user->email;
    } catch (\Exception $e) {
        $rawResult = '❌ Raw email failed: ' . $e->getMessage();
    }
    
    // Test 2: Notification email (AccountVerified as test)
    try {
        $user->notify(new \App\Notifications\AccountVerifiedNotification());
        $notifResult = '✅ AccountVerifiedNotification sent successfully';
    } catch (\Exception $e) {
        $notifResult = '❌ Notification failed: ' . $e->getMessage();
    }
    
    return response()->json([
        'raw_email' => $rawResult,
        'notification_email' => $notifResult,
        'mail_config' => [
            'mailer' => config('mail.default'),
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'from' => config('mail.from.address'),
            'queue' => config('queue.default'),
        ],
    ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
})->middleware('auth');

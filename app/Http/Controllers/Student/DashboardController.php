<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Traits\SendsDatabaseNotifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    use SendsDatabaseNotifications;

    // ─────────────────────────────────────────────────────────────────────
    // DASHBOARD
    // GET /student/dashboard
    // ─────────────────────────────────────────────────────────────────────
    public function index()
    {
        $user = Auth::user();

        // Get upcoming appointment
        $upcomingAppointment = \App\Models\Appointment::with(['documentRequest', 'timeSlot'])
            ->where('user_id', $user->id)
            ->where('status', 'scheduled')
            ->whereDate('appointment_date', '>=', today())
            ->whereHas('documentRequest', function ($query) {
                $query->whereNotIn('status', ['received', 'completed', 'cancelled']);
            })
            ->orderBy('appointment_date', 'asc')
            ->first();

        // Get counts for stats
        $pendingCount = DocumentRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'processing', 'ready_for_pickup', 'payment_uploaded', 'payment_verified'])
            ->count();

        $completedCount = DocumentRequest::where('user_id', $user->id)
            ->whereIn('status', ['completed', 'received'])
            ->count();

        // Announcements
        $announcement = \App\Models\Announcement::where('type', 'announcement')
            ->where('is_published', true)
            ->latest()
            ->first();

        $transactionDays = \App\Models\Announcement::where('type', 'transaction_days')
            ->where('is_published', true)
            ->latest()
            ->first();

        return view('student.dashboard', compact(
            'user',
            'announcement',
            'transactionDays',
            'upcomingAppointment',
            'pendingCount',
            'completedCount'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────
    // AVAILABLE DOCUMENTS
    // GET /student/documents
    // ─────────────────────────────────────────────────────────────────────
    public function documents()
    {
        $documentTypes = \App\Models\DocumentType::where('is_active', true)->get();

        return view('student.documents.index', compact('documentTypes'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // ACCOUNT PAGE
    // GET /student/account
    // ─────────────────────────────────────────────────────────────────────
    public function account()
    {
        $user = Auth::user();

        return view('student.account.index', compact('user'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // SERVE PROFILE PHOTO (private storage)
    // GET /student/account/photo
    // ─────────────────────────────────────────────────────────────────────
    public function servePhoto()
    {
        $user = Auth::user();

        if (! $user->profile_photo) {
            abort(404);
        }

        $path = storage_path('app/private/' . $user->profile_photo);

        if (! file_exists($path)) {
            abort(404);
        }

        $mime = mime_content_type($path);

        return response()->file($path, ['Content-Type' => $mime]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // UPDATE PROFILE (contact number + address only)
    // PATCH /student/account/profile
    // ─────────────────────────────────────────────────────────────────────
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'contact_number' => ['required', 'string', 'max:20'],
            'address'        => ['nullable', 'string', 'max:500'],
        ]);

        $user = Auth::user();
        $user->update($validated);

        // Send notification for profile update
        $message = 'Your profile information has been updated successfully.';
        $url = route('student.account.index');
        $this->sendNotificationToCurrentUser($message, $url);
        session()->flash('check_notifications', true);

        return redirect()->route('student.account.index');
    }

    // ─────────────────────────────────────────────────────────────────────
    // UPDATE PROFILE PHOTO
    // POST /student/account/photo
    // ─────────────────────────────────────────────────────────────────────
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => ['required', 'file', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
        ]);

        $user = Auth::user();

        // Delete the old photo from storage if one exists
        if ($user->profile_photo) {
            Storage::disk('local')->delete($user->profile_photo);
        }

        // Store new photo in storage/app/private/photos/
        $file = $request->file('profile_photo');
        $filename = 'user_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('photos', $filename, 'local');

        $user->update(['profile_photo' => $path]);

        // Send notification for photo update
        $message = 'Your profile photo has been updated successfully.';
        $url = route('student.account.index');
        $this->sendNotificationToCurrentUser($message, $url);
        session()->flash('check_notifications', true);

        return redirect()->route('student.account.index');
    }

    // ─────────────────────────────────────────────────────────────────────
    // UPDATE PASSWORD
    // PATCH /student/account/password
    // ─────────────────────────────────────────────────────────────────────
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password'          => ['required', 'string'],
            'new_password'              => ['required', 'string', 'min:8', 'confirmed'],
            'new_password_confirmation' => ['required', 'string'],
        ]);

        $user = Auth::user();

        // Verify the current password is correct before allowing change
        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Your current password is incorrect.'])
                ->withInput();
        }

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        // Send notification for password change
        $message = 'Your password has been changed successfully. Please use your new password next time you log in.';
        $url = route('student.account.index');
        $this->sendNotificationToCurrentUser($message, $url);
        session()->flash('check_notifications', true);

        return redirect()->route('student.account.index');
    }
}
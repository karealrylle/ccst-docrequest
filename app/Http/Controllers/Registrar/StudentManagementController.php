<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class StudentManagementController extends Controller
{
    public function index(Request $request)
    {
        $students = User::where('role', 'student')
            ->where('is_verified', true)
            ->when($request->search, function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->search}%")
                      ->orWhere('student_number', 'like', "%{$request->search}%")
                      ->orWhere('email', 'like', "%{$request->search}%");
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('registrar.students.index', compact('students'));
    }

    public function show($id)
    {
        $student = User::where('role', 'student')->findOrFail($id);
        return view('registrar.students.show', compact('student'));
    }

    public function toggleActive($id)
    {
        $student = User::where('role', 'student')->findOrFail($id);
        // Toggle the is_active property
        $student->update(['is_active' => !$student->is_active]);
        $status = $student->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Student $status successfully.");
    }

    public function sendPasswordReset($id)
    {
        $student = User::where('role', 'student')->findOrFail($id);
        // Use Laravel's built-in password reset
        $broker = app('auth.password.broker');
        $token = $broker->createToken($student);
        $student->sendPasswordResetNotification($token);
        return back()->with('success', 'Password reset link sent to student email.');
    }

    public function destroy($id)
    {
        $student = User::where('role', 'student')->findOrFail($id);
        
        // Soft delete the student
        $student->delete();
        
        return back()->with('success', 'Student account has been scheduled for deletion.');
    }
}

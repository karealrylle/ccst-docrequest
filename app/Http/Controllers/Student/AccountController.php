<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AccountDeletionNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    /**
     * Soft delete the student account.
     */
    public function destroy(Request $request)
    {
        $user = $request->user();

        // Validate password
        $request->validate([
            'password' => 'required',
        ]);

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'The provided password does not match our records.'], 422);
        }

        DB::transaction(function () use ($user) {
            // Notify all registrars
            $registrars = User::where('role', 'registrar')->get();
            foreach ($registrars as $registrar) {
                $registrar->notify(new AccountDeletionNotification($user));
            }

            // Soft delete the user
            $user->delete();

            // Log out
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        });

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Account scheduled for deletion.']);
        }

        return redirect()->route('login')->with('success', 'Your account has been scheduled for deletion. You have 30 days to restore it by contacting the registrar.');
    }
}

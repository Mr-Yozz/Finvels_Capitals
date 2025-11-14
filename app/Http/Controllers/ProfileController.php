<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    // Show profile
    public function edit(Request $request): View
    {
        return view('admin.user', [
            'user' => $request->user(),
        ]);
    }

    // Update profile info
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email',
            'role'  => 'nullable|string'
        ]);

        $user = $request->user();
        $user->fill($request->only('name', 'email', 'role'));

        // Reset email verification if changed
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully');
    }

    // Update Password
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Manager cannot change password
        if ($user->role !== 'admin') {
            return back()->with('error', 'Only admin can change the password.');
        }

        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', 'min:6'],
        ]);

        // Check current password
        if (!Hash::check($request->current_password, $request->user()->password)) {
            return back()->withErrors(['current_password' => 'Incorrect current password']);
        }

        // Update password
        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully');
    }

    // Delete user
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password']
        ]);

        $user = $request->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Account deleted successfully');
    }
}

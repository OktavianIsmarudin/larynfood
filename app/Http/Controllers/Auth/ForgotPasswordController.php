<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    /**
     * Display forgot password form
     */
    public function showForgotPasswordForm(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle reset password request (custom, no email link)
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Email tidak ditemukan.',
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('login')->with('success', 'Password berhasil direset. Silakan login.');
    }
}

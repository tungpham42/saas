<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class VerificationController extends Controller
{
    /**
     * Show verification notice
     */
    public function show()
    {
        return view('auth.verify');
    }

    /**
     * Verify email
     */
    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        // Check if hash matches
        if (sha1($user->email) !== $hash) {
            return redirect()->route('verification.notice')
                ->with('error', 'Invalid verification link.');
        }

        // Check if already verified
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard')
                ->with('info', 'Email already verified.');
        }

        // Mark as verified
        $user->markEmailAsVerified();

        return redirect()->route('dashboard')
            ->with('success', 'Email verified successfully! You can now create chatbots.');
    }

    /**
     * Resend verification email
     */
    public function resend(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard')
                ->with('info', 'Email already verified.');
        }

        $this->sendVerificationEmail($user);

        return back()->with('success', 'Verification email sent. Please check your inbox.');
    }

    /**
     * Send verification email
     */
    private function sendVerificationEmail(User $user)
    {
        $verificationUrl = route('verification.verify', [
            'id' => $user->id,
            'hash' => sha1($user->email)
        ]);

        Mail::send('emails.verify', [
            'user' => $user,
            'verificationUrl' => $verificationUrl
        ], function($mail) use ($user) {
            $mail->to($user->email)
                 ->subject('Verify Your Email Address - SaaS AI Chatbot');
        });
    }
}

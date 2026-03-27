<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    /**
     * Show registration form
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'terms' => 'required|accepted',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'user_role' => 'user',
            'bot_limit' => 1, // Default 1 bot for free plan
            'email_verified_at' => null,
            'remember_token' => Str::random(60),
        ]);

        // Send email verification
        $this->sendVerificationEmail($user);

        // Log the user in
        auth()->login($user);

        // Trigger registered event
        event(new Registered($user));

        return redirect()->route('verification.notice')
            ->with('success', 'Account created successfully! Please verify your email address.');
    }

    /**
     * Send email verification
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

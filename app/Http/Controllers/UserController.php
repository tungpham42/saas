<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'user')
            ->withCount('bots')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'bot_limit' => 'nullable|integer|min:1',
            'paypal_sub_id' => 'nullable|string',
            'paypal_sub_status' => 'nullable|string|in:None,Active,Suspended,Cancelled',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
            'bot_limit' => $validated['bot_limit'] ?? 1,
            'paypal_sub_id' => $validated['paypal_sub_id'] ?? null,
            'paypal_sub_status' => $validated['paypal_sub_status'] ?? 'None',
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'bot_limit' => 'nullable|integer|min:1',
            'paypal_sub_id' => 'nullable|string',
            'paypal_sub_status' => 'nullable|string|in:None,Active,Suspended,Cancelled',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'bot_limit' => $validated['bot_limit'] ?? $user->bot_limit,
            'paypal_sub_id' => $validated['paypal_sub_id'] ?? $user->paypal_sub_id,
            'paypal_sub_status' => $validated['paypal_sub_status'] ?? $user->paypal_sub_status,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        // Delete user's bots and related data
        foreach ($user->bots as $bot) {
            $bot->channels()->delete();
            $bot->chatLogs()->delete();
            $bot->ragDocuments()->delete();
            $bot->sessionStats()->delete();
            $bot->leads()->delete();
            $bot->delete();
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function profile()
    {
        $user = auth()->user();
        return view('users.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'current_password' => 'nullable|required_with:password|current_password',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}

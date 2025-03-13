<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthController extends Controller
{
    // ✅ Register a new user
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'profile_picture' => 'nullable|string',
            'currency' => 'nullable|string|max:3',
            'language' => 'nullable|string|max:5',
            'timezone' => 'nullable|string|max:50',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'profile_picture' => $request->profile_picture ?? null,
            'currency' => $request->currency ?? 'USD',
            'language' => $request->language ?? 'en',
            'timezone' => $request->timezone ?? 'UTC',
            'default_account_id' => null,
        ]);

        return response()->json([
            'message' => 'User registered successfully.',
            'user' => $user,
        ], 201);
    }

    // ✅ Login a user
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean', // Supports "Remember Me"
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        // ✅ Generate token
        $token = $user->createToken('auth_token')->plainTextToken;

        // ✅ Save remember_token if "Remember Me" is checked
        if ($request->remember_me) {
            $user->remember_token = $token;
            $user->save();
        }

        return response()->json([
            'message' => 'Login successful.',
            'token' => $token,
            'user' => $user,
        ]);
    }

    // ✅ Fetch the authenticated user profile
    public function userProfile(Request $request)
    {
        return response()->json([
            'user' => $request->user()->load('accounts', 'transactions', 'budgets', 'goals', 'notifications'),
        ]);
    }

    // ✅ Update user profile
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|string',
            'currency' => 'nullable|string|max:3',
            'language' => 'nullable|string|max:5',
            'timezone' => 'nullable|string|max:50',
        ]);

        $user->update($request->only(['name', 'profile_picture', 'currency', 'language', 'timezone']));

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => $user,
        ]);
    }

    // ✅ Update user password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect.'], 400);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json(['message' => 'Password updated successfully.']);
    }

    // ✅ Logout the user
    public function logout(Request $request)
    {
        $user = $request->user();

        // Clear remember_token and revoke all tokens
        $user->remember_token = null;
        $user->save();
        $user->tokens()->delete();

        return response()->json(['message' => 'Successfully logged out.']);
    }
}

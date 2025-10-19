<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // Register: returns user + token
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'nullable|in:collector,trusted_collector,moderator,admin'
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'] ?? 'collector',
            'password' => bcrypt($data['password']),
        ]);

        // create token for mobile: note token abilities can be specified
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token], 201);
    }
    
    // Login: returns user + token
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $data['email'])->first();

        if ( !$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // revoke previous tokens optionally for mobile single-session behavior:
        // $user->tokens()->delete();

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token], 200);
    }

    // Logout: deletes current token
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    // Request password reset email
    public function forgotPassword(Request $request)
    {
        $data = $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(['email' => $data['email']]);

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'Password reset link sent.']);
        }

        return response()->json(['message' => 'Unable to send reset link.'], 500);
    }

    // Reset password (expects token, email, password, password_confirmation)
    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $data,
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password has been reset.']);
        }

        return response()->json(['message' => 'Password reset failed.'], 500);
    }

    // Optional: get current user info
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }
}